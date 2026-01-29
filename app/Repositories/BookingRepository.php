<?php

namespace App\Repositories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingRepository
{
    /**
     * Get bookings by filter criteria with enhanced search and filtering
     *
     * @param string $filter
     * @param string $status
     * @param string $search
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredBookings($filter = 'all', $status = null, $search = null)
    {
        $query = Booking::with(['primaryStaff', 'bookingAddons', 'backdrop', 'package'])
            ->select('bookings.*'); // Explicit select to avoid conflicts
        
        // Apply date filter
        switch ($filter) {
            case 'upcoming':
                $query->where('booking_date', '>=', now()->format('Y-m-d'))
                      ->whereNotIn('status', ['canceled', 'completed']);
                break;
            case 'today':
                $query->whereDate('booking_date', now()->format('Y-m-d'));
                break;
            case 'this_week':
                $query->whereBetween('booking_date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
                break;
            case 'this_month':
                $query->whereYear('booking_date', now()->year)
                      ->whereMonth('booking_date', now()->month);
                break;
            case 'past':
                $query->where('booking_date', '<', now()->format('Y-m-d'));
                break;
            case 'all':
            default:
                // Show all bookings
                break;
        }
        
        // Apply status filter if provided
        if ($status && $status != 'all') {
            $query->where('status', $status);
        }
        
        // Apply search if provided
        if ($search && strlen(trim($search)) >= 2) {
            $searchTerms = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
            
            $query->where(function($q) use ($search, $searchTerms) {
                // Search by booking ID or booking reference
                $q->where('bookings.booking_id', 'like', "%{$search}%")
                  ->orWhere('bookings.booking_reference', 'like', "%{$search}%");
                
                // Search by client name
                $q->orWhere(function($clientQ) use ($search, $searchTerms) {
                    $clientQ->where(DB::raw("CONCAT(bookings.client_first_name, ' ', bookings.client_last_name)"), 'like', "%{$search}%");
                    foreach ($searchTerms as $term) {
                        if (strlen($term) >= 2) {
                            $clientQ->orWhere('bookings.client_first_name', 'like', "%{$term}%")
                                   ->orWhere('bookings.client_last_name', 'like', "%{$term}%");
                        }
                    }
                });

                // Search by client email and phone
                $q->orWhere('bookings.client_email', 'like', "%{$search}%")
                  ->orWhere('bookings.client_phone', 'like', "%{$search}%");
                
                // Search by booking date - enhanced date parsing
                $this->addDateSearchConditions($q, $search);
                
                // Search by status
                $q->orWhere('bookings.status', 'like', "%{$search}%");
                
                // Search by staff name
                $q->orWhereHas('primaryStaff', function($subq) use ($search, $searchTerms) {
                    $subq->where(function($nameq) use ($search, $searchTerms) {
                        $nameq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                        foreach ($searchTerms as $term) {
                            if (strlen($term) >= 2) {
                                $nameq->orWhere('first_name', 'like', "%{$term}%")
                                      ->orWhere('last_name', 'like', "%{$term}%");
                            }
                        }
                    });
                });
                
                // Search by backdrop name
                $q->orWhereHas('backdrop', function($subq) use ($search) {
                    $subq->where('name', 'like', "%{$search}%");
                });
                
                // Search by package name
                $q->orWhereHas('package', function($subq) use ($search) {
                    $subq->where('title', 'like', "%{$search}%");
                });

                // Search in notes
                $q->orWhere('bookings.notes', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('booking_date', 'desc')
                     ->orderBy('start_time')
                     ->paginate(10);
    }

    /**
     * Get upcoming dashboard bookings
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingDashboardBookings($limit = 5)
    {
        return Booking::with(['package', 'primaryStaff'])
            ->upcoming()
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->take($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->booking_id,
                    'client' => $booking->client_first_name . ' ' . $booking->client_last_name,
                    'date' => $booking->booking_date->format('M d, Y'),
                    'time' => Carbon::parse($booking->start_time)->format('h:i A') . ' - ' . 
                              Carbon::parse($booking->end_time)->format('h:i A'),
                    'status' => $booking->status,
                    'package' => $booking->package ? $booking->package->title : 'No package',
                    'amount' => $booking->total_amount,
                    'primary_staff' => $booking->primaryStaff ? $booking->primaryStaff->first_name . ' ' . $booking->primaryStaff->last_name : null
                ];
            });
    }
    
    /**
     * Find booking by reference code
     *
     * @param string $reference
     * @return \App\Models\Booking|null
     */
    public function findByReference(string $reference)
    {
        return Booking::where('booking_reference', $reference)
            ->with(['package', 'addons', 'primaryStaff', 'backdrop'])
            ->first();
    }

    /**
     * Add date search conditions to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return void
     */
    private function addDateSearchConditions($query, $search)
    {
        try {
            // Try to parse as a full date
            if (strtotime($search) !== false) {
                $dateSearch = date('Y-m-d', strtotime($search));
                $query->orWhere('bookings.booking_date', $dateSearch);
                
                // Also try partial date matches
                $query->orWhere('bookings.booking_date', 'like', "%{$dateSearch}%");
            }
            
            // Try month name search (January, Feb, etc.)
            $monthNames = [
                'january' => '01', 'february' => '02', 'march' => '03',
                'april' => '04', 'may' => '05', 'june' => '06',
                'july' => '07', 'august' => '08', 'september' => '09',
                'october' => '10', 'november' => '11', 'december' => '12',
                'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
                'jun' => '06', 'jul' => '07', 'aug' => '08', 'sep' => '09',
                'oct' => '10', 'nov' => '11', 'dec' => '12'
            ];
            
            $searchLower = strtolower($search);
            if (isset($monthNames[$searchLower])) {
                $query->orWhereMonth('bookings.booking_date', $monthNames[$searchLower]);
            }
            
            // Try day search (1-31)
            if (is_numeric($search) && $search >= 1 && $search <= 31) {
                $query->orWhereDay('bookings.booking_date', (int)$search);
            }
            
            // Try year search
            if (is_numeric($search) && $search >= 2020 && $search <= 2030) {
                $query->orWhereYear('bookings.booking_date', (int)$search);
            }
        } catch (\Exception $e) {
            // Silently continue if date parsing fails
        }
    }

    /**
     * Get bookings for a specific staff member
     *
     * @param int $staffId
     * @param string|null $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsByStaff($staffId, $status = null)
    {
        $query = Booking::where('primary_staff_id', $staffId)
            ->with(['package', 'backdrop', 'addons'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');
            
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->get();
    }

    /**
     * Get revenue statistics for bookings
     *
     * @param string $period
     * @return array
     */
    public function getRevenueStats($period = 'month')
    {
        $query = Booking::where('status', '!=', 'cancelled');
        
        switch ($period) {
            case 'today':
                $query->whereDate('booking_date', today());
                break;
            case 'week':
                $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('booking_date', now()->month)
                      ->whereYear('booking_date', now()->year);
                break;
            case 'year':
                $query->whereYear('booking_date', now()->year);
                break;
        }
        
        return [
            'total_bookings' => $query->count(),
            'total_revenue' => $query->sum('total_amount'),
            'average_booking_value' => $query->avg('total_amount'),
            'confirmed_bookings' => $query->where('status', 'confirmed')->count(),
            'completed_bookings' => $query->where('status', 'completed')->count(),
        ];
    }
}
