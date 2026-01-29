<?php

namespace App\Http\Controllers;

use App\Models\Backdrop;
use App\Models\Equipment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudioManagementController extends Controller
{
    /**
     * Display the studio management dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get counts for dashboard
        $totalBackdrops = Backdrop::count();
        $activeBackdrops = Backdrop::where('is_active', true)->count();
        
        $totalEquipment = Equipment::count();
        $activeEquipment = Equipment::where('is_active', true)->count();
        $availableEquipment = Equipment::where('is_available', true)->count();
        
        // Equipment by type (for chart)
        $equipmentByType = Equipment::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
            
        // Recent bookings with studio items
        $recentBookings = Booking::with(['backdrop', 'equipment'])
            ->whereHas('backdrop')
            ->orWhereHas('equipment')
            ->orderBy('booking_date', 'desc')
            ->take(5)
            ->get();
            
        return view('studio.index', compact(
            'totalBackdrops', 
            'activeBackdrops', 
            'totalEquipment', 
            'activeEquipment', 
            'availableEquipment',
            'equipmentByType',
            'recentBookings'
        ));
    }
}
