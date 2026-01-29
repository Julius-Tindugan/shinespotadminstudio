<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Staff;
use App\Models\Backdrop;
use App\Models\Addon;
use App\Models\Package;
use App\Services\BookingExpenditureService;
use App\Services\OTPService;
use App\Services\NotificationService;
use App\Services\BookingReferenceService;
use App\Services\PaymentMonitoringService;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * The booking repository instance.
     */
    protected $bookingRepository;

    /**
     * The booking expenditure service instance.
     */
    protected $expenditureService;

    /**
     * The OTP service instance.
     */
    protected $otpService;

    /**
     * The notification service instance.
     */
    protected $notificationService;
    
    /**
     * The payment monitoring service instance.
     */
    protected $paymentMonitoringService;
    
    /**
     * The booking conflict service instance.
     */
    protected $conflictService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\BookingRepository  $bookingRepository
     * @param  \App\Services\BookingExpenditureService  $expenditureService
     * @param  \App\Services\OTPService  $otpService
     * @param  \App\Services\NotificationService  $notificationService
     * @param  \App\Services\BookingReferenceService  $referenceService
     * @param  \App\Services\PaymentMonitoringService  $paymentMonitoringService
     * @param  \App\Services\BookingConflictService  $conflictService
     * @return void
     */
    public function __construct(
        BookingRepository $bookingRepository,
        BookingExpenditureService $expenditureService,
        OTPService $otpService,
        NotificationService $notificationService,
        BookingReferenceService $referenceService,
        PaymentMonitoringService $paymentMonitoringService,
        \App\Services\BookingConflictService $conflictService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->expenditureService = $expenditureService;
        $this->otpService = $otpService;
        $this->notificationService = $notificationService;
        $this->referenceService = $referenceService;
        $this->paymentMonitoringService = $paymentMonitoringService;
        $this->conflictService = $conflictService;
    }
    
    /**
     * Display a listing of all bookings with filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $status = $request->status;
        $search = $request->search;
        
        $bookings = $this->bookingRepository->getFilteredBookings($filter, $status, $search);
        
        // No need to process - client info is stored directly in booking fields
        
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        
        if ($request->ajax() || $request->has('ajax')) {
            // Format search terms for highlighting
            $searchHighlight = $search ?? '';
            
            return response()->json([
                'html' => view('bookings.partials.booking-list', compact('bookings', 'statuses'))->render(),
                'success' => true,
                'message' => 'Bookings fetched successfully',
                'current_filter' => $filter,
                'current_status' => $status ?? 'all',
                'count' => $bookings->total(),
                'search' => $searchHighlight
            ]);
        }
        
        return view('bookings.index', compact('bookings', 'statuses', 'filter'));
    }

    /**
     * Show the form for creating a new booking.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $staff = Staff::where('status', 'active')->orderBy('first_name')->get();
        $packages = Package::with(['inclusions', 'freeItems'])
            ->where('is_active', 1)
            ->orderBy('title')
            ->get();
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $addons = Addon::orderBy('addon_name')->get();
        $backdrops = Backdrop::where('is_active', 1)->orderBy('name')->get();
        
        return view('bookings.create', compact('staff', 'packages', 'statuses', 'addons', 'backdrops'));
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Add debug logging
        \Log::info('Booking store method called', [
            'user_id' => session('admin_id') ?? session('staff_id'),
            'session_admin' => session('admin_logged_in'),
            'session_staff' => session('staff_logged_in'),
            'request_data' => $request->except(['password', '_token'])
        ]);

        $validator = Validator::make($request->all(), [
            'client_first_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'client_last_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'client_email' => 'nullable|email:rfc,dns|max:100',
            'client_phone' => 'nullable|string|max:30|regex:/^[\d\s\-\+\(\)]+$/',
            'package_id' => 'required|exists:packages,package_id',
            'primary_staff_id' => 'nullable|exists:staff_users,staff_id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'backdrop_id' => 'nullable|exists:backdrops,backdrop_id',
            'backdrop_selections' => 'nullable|array',
            // On creation, only allow 'pending' status - will auto-update to 'confirmed' when paid
            'status' => 'required|in:pending',
            'notes' => 'nullable|string|max:1000',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,addon_id',
            'addon_qty' => 'nullable|array',
            'addon_qty.*' => 'integer|min:1|max:99',
        ], [
            'client_first_name.regex' => 'First name must contain only letters and spaces.',
            'client_last_name.regex' => 'Last name must contain only letters and spaces.',
            'client_email.email' => 'Please enter a valid email address.',
            'client_phone.regex' => 'Please enter a valid phone number.',
            'booking_date.after_or_equal' => 'Booking date cannot be in the past.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'end_time.date_format' => 'End time must be in HH:MM format.',
            'end_time.after' => 'End time must be after start time.',
        ]);

        if ($validator->fails()) {
            \Log::warning('Booking validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['password', '_token'])
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Additional business logic validation
        $additionalValidation = $this->validateBookingBusinessRules($request);
        if ($additionalValidation !== true) {
            return redirect()->back()
                ->with('error', $additionalValidation)
                ->withInput();
        }
        
        // Use serializable transaction to prevent race conditions
        DB::beginTransaction();
        
        try {
            // Double-check availability with database lock
            $conflictCheck = $this->conflictService->checkTimeSlotConflicts(
                $request->booking_date,
                $request->start_time,
                $request->end_time,
                $request->primary_staff_id,
                null
            );
            
            if ($conflictCheck['hasConflict']) {
                throw new \Exception($conflictCheck['message']);
            }
            
            // Calculate expenditures using the injected service
            $expenditures = $this->expenditureService->calculateExpendituresFromFormData($request->all());
            
            // Generate unique booking reference
            $bookingReference = $this->referenceService->generateUniqueReference();

            // Create booking
            $booking = new Booking();
            $booking->package_id = $request->package_id;
            $booking->booking_reference = $bookingReference;
            $booking->client_first_name = trim($request->client_first_name ?? '');
            $booking->client_last_name = trim($request->client_last_name ?? '');
            $booking->client_email = trim($request->client_email ?? '');
            $booking->client_phone = trim($request->client_phone ?? '');
            $booking->primary_staff_id = $request->primary_staff_id;
            $booking->booking_date = $request->booking_date;
            $booking->start_time = $request->start_time;
            $booking->end_time = $request->end_time;
            $booking->backdrop_id = $request->backdrop_id;
            $booking->backdrop_selections = $request->backdrop_selections ?? null;
            // Always set status to 'pending' on creation - will be auto-updated to 'confirmed' when paid
            $booking->status = 'pending';
            $booking->total_amount = $expenditures['total'];
            $booking->payment_status = 'unpaid';
            $booking->notes = trim($request->notes ?? '');
            $booking->created_by = session('admin_id') ?? session('staff_id');
            
            if (!$booking->save()) {
                throw new \Exception('Failed to save booking to database');
            }

            // Log booking creation
            \App\Models\BookingLog::log($booking, 'created', null, $booking->toArray(), $request->ip(), $request->userAgent(), $booking->created_by);
            
            // Attach addons to the booking if any
            if ($request->has('addons') && is_array($request->addons)) {
                foreach ($request->addons as $addonId) {
                    $addon = Addon::find($addonId);
                    if ($addon) {
                        $quantity = isset($request->addon_qty[$addonId]) ? intval($request->addon_qty[$addonId]) : 1;
                        $booking->bookingAddons()->attach($addonId, [
                            'quantity' => $quantity,
                            'price' => $addon->addon_price
                        ]);
                    }
                }
            }
            
            // Services have been removed from the system
            
            DB::commit();
            
            // Ensure session is properly saved before redirect
            session()->save();
            
            return redirect()->route('bookings.index')
                ->with('success', 'Booking created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', '_token'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Error creating booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified booking.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        $booking->load(['primaryStaff', 'payments', 'package', 'addons', 'backdrop', 'paymentTransactions']);
        
        // Ensure client info is available - for legacy entries
        if (empty($booking->client_first_name)) {
            // Set placeholder values if client info is missing
            $booking->client_first_name = 'Unknown';
            $booking->client_last_name = 'Client';
            $booking->client_email = '';
            $booking->client_phone = '';
        }
        
        // Get payment summary
        $paymentSummary = $this->paymentMonitoringService->getPaymentSummary($booking);
        
        return view('bookings.show', compact('booking', 'paymentSummary'));
    }

    /**
     * Show the form for editing the specified booking.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        $booking->load(['primaryStaff', 'package', 'backdrop']);
        
        $staff = Staff::where('status', 'active')->orderBy('first_name')->get();
        $packages = Package::where('is_active', 1)->orderBy('title')->get();
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $addons = Addon::orderBy('addon_name')->get();
        $backdrops = Backdrop::where('is_active', 1)->orderBy('name')->get();
        
        // Get currently selected addons and their quantities from the pivot table
        // Use bookingAddons() method to access the relationship
        $selectedAddons = [];
        $bookingAddons = $booking->bookingAddons()->get();
        foreach ($bookingAddons as $addon) {
            $selectedAddons[$addon->addon_id] = $addon->pivot->quantity;
        }
        
        return view('bookings.edit', compact(
            'booking', 
            'staff',
            'packages',
            'statuses', 
            'addons',
            'selectedAddons',
            'backdrops'
        ));
    }

    /**
     * Update the specified booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'client_first_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'client_last_name' => 'required|string|max:50|regex:/^[a-zA-Z\s]+$/',
            'client_email' => 'required|email:rfc,dns|max:100',
            'client_phone' => 'required|string|max:30|regex:/^[\d\s\-\+\(\)]+$/',
            'package_id' => 'required|exists:packages,package_id',
            'primary_staff_id' => 'nullable|exists:staff_users,staff_id',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'backdrop_id' => 'nullable|exists:backdrops,backdrop_id',
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'notes' => 'nullable|string|max:1000',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,addon_id',
            'addon_qty' => 'nullable|array',
            'addon_qty.*' => 'integer|min:1|max:99',
        ], [
            'client_first_name.required' => 'First name is required.',
            'client_first_name.regex' => 'First name must contain only letters and spaces.',
            'client_last_name.required' => 'Last name is required.',
            'client_last_name.regex' => 'Last name must contain only letters and spaces.',
            'client_email.required' => 'Email address is required.',
            'client_email.email' => 'Please enter a valid email address.',
            'client_phone.required' => 'Phone number is required.',
            'client_phone.regex' => 'Please enter a valid phone number.',
            'package_id.required' => 'Package selection is required.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'end_time.date_format' => 'End time must be in HH:MM format.',
            'end_time.after' => 'End time must be after start time.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Additional business logic validation (skip date validation for existing bookings)
        $additionalValidation = $this->validateBookingBusinessRules($request, $booking);
        if ($additionalValidation !== true) {
            return redirect()->back()
                ->with('error', $additionalValidation)
                ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Save original booking data for change detection and logging
            $originalData = $booking->toArray();
            $originalStatus = $booking->status;
            
            // Calculate expenditures using the service
            $expenditures = $this->expenditureService->calculateExpendituresFromFormData($request->all());
            
            $booking->client_first_name = trim($request->client_first_name);
            $booking->client_last_name = trim($request->client_last_name);
            $booking->client_email = trim($request->client_email);
            $booking->client_phone = trim($request->client_phone);
            $booking->package_id = $request->package_id;
            $booking->primary_staff_id = $request->primary_staff_id;
            $booking->booking_date = $request->booking_date;
            $booking->start_time = $request->start_time;
            $booking->end_time = $request->end_time;
            $booking->backdrop_id = $request->backdrop_id;
            // Convert backdrop_selections to JSON string if it's an array
            $booking->backdrop_selections = $request->backdrop_selections ? 
                (is_array($request->backdrop_selections) ? json_encode($request->backdrop_selections) : $request->backdrop_selections) : 
                null;
            $booking->status = $request->status;
            $booking->payment_status = $request->payment_status;
            $booking->total_amount = $expenditures['total']; // Use calculated total
            $booking->notes = trim($request->notes ?? '');

            if (!$booking->save()) {
                throw new \Exception('Failed to update booking in database');
            }

            // Log the update
            \App\Models\BookingLog::log($booking, 'updated', $booking->getOriginal(), $booking->toArray(), $request->ip(), $request->userAgent(), $booking->created_by);

            // Detach all existing addons
            $booking->bookingAddons()->detach();

            // Attach updated addons to the booking if any
            if ($request->has('addons') && is_array($request->addons)) {
                foreach ($request->addons as $addonId) {
                    $addon = Addon::find($addonId);
                    if ($addon) {
                        $quantity = isset($request->addon_qty[$addonId]) ? intval($request->addon_qty[$addonId]) : 1;
                        $booking->bookingAddons()->attach($addonId, [
                            'quantity' => $quantity,
                            'price' => $addon->addon_price
                        ]);
                    }
                }
            }

            // Services section removed

            // Send notifications based on status changes
            if ($originalStatus !== $booking->status) {
                if ($booking->status === 'confirmed') {
                    $this->notificationService->sendBookingConfirmation($booking);
                } elseif ($booking->status === 'cancelled') {
                    $this->notificationService->sendBookingCancellation($booking);
                }
            }

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error updating booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified booking from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Booking $booking)
    {
        try {
            // Check if booking can be deleted
            // Allow deletion if booking is completed and paid, or if there are no payments
            if ($booking->payments()->count() > 0) {
                // Allow deletion if status is completed and payment_status is paid
                if ($booking->status !== 'completed' || $booking->payment_status !== 'paid') {
                    $errorMessage = 'Cannot delete booking with associated payments unless status is completed and payment is fully paid.';
                    
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 400);
                    }
                    
                    return redirect()->back()
                        ->with('error', $errorMessage);
                }
            }
            
            // Delete the booking
            DB::beginTransaction();

            // Log the deletion before actually deleting
            \App\Models\BookingLog::log($booking, 'deleted', $booking->toArray(), null, $request->ip(), $request->userAgent(), $booking->created_by);

            // Detach all addons
            $booking->bookingAddons()->detach();

            // Delete booking
            $booking->delete();

            DB::commit();

            // Return JSON response for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking deleted successfully!'
                ]);
            }

            return redirect()->route('bookings.index')
                ->with('success', 'Booking deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = 'Error deleting booking: ' . $e->getMessage();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }
    
    /**
     * Quick update booking status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status provided.'
            ], 400);
        }
        
        try {
            // Track if status actually changed
            $statusChanged = $booking->status !== $request->status;
            $oldStatus = $booking->status;
            
            // Update the status
            $booking->status = $request->status;

            $booking->save();

            // Log the status update
            \App\Models\BookingLog::log($booking, 'status_updated', $booking->getOriginal(), $booking->toArray(), $request->ip(), $request->userAgent(), $booking->created_by);

            // Send notifications based on status changes
            if ($statusChanged) {
                if ($booking->status === 'confirmed') {
                    $this->notificationService->sendBookingConfirmation($booking);
                } elseif ($booking->status === 'cancelled') {
                    $this->notificationService->sendBookingCancellation($booking);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking status updated successfully!',
                'new_status' => $booking->status,
                'old_status' => $oldStatus,
                'status_changed' => $statusChanged,
                'booking_id' => $booking->booking_id,
                'updated_at' => $booking->updated_at->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error updating booking status: ' . $e->getMessage(), [
                'booking_id' => $booking->booking_id,
                'requested_status' => $request->status
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating booking status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get dashboard upcoming bookings.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcomingBookings()
    {
        return response()->json(
            $this->bookingRepository->getUpcomingDashboardBookings(5)
        );
    }
    
    /**
     * Get active staff for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveStaff()
    {
        $staff = Staff::where('status', 'active')
            ->select('staff_id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();
            
        return response()->json($staff);
    }

    /**
     * Validate booking business rules
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking|null $booking
     * @return true|string
     */
    private function validateBookingBusinessRules(Request $request, Booking $booking = null)
    {
        // Prepare booking data for validation
        $bookingData = [
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'primary_staff_id' => $request->primary_staff_id,
            'exclude_booking_id' => $booking ? $booking->booking_id : null
        ];
        
        // Use the conflict service for comprehensive validation
        $validationResult = $this->conflictService->validateBookingRequest($bookingData);
        
        if (!$validationResult['isValid']) {
            $errorMessage = implode(' ', $validationResult['errors']);
            
            // Log conflict details for debugging
            if (!empty($validationResult['conflicts'])) {
                \Log::warning('Booking validation failed due to conflicts', [
                    'booking_data' => $bookingData,
                    'conflicts' => $validationResult['conflicts']
                ]);
            }
            
            return $errorMessage;
        }

        // Check if the package is active
        $package = Package::find($request->package_id);
        if (!$package || !$package->is_active) {
            return 'The selected package is not available.';
        }

        // Check if backdrop is active (if selected)
        if ($request->backdrop_id) {
            $backdrop = Backdrop::find($request->backdrop_id);
            if (!$backdrop || !$backdrop->is_active) {
                return 'The selected backdrop is not available.';
            }
        }

        // Check if all addons are active (if selected)
        if ($request->has('addons') && is_array($request->addons)) {
            foreach ($request->addons as $addonId) {
                $addon = Addon::find($addonId);
                if (!$addon || !$addon->is_active) {
                    return 'One or more selected addons are not available.';
                }
            }
        }

        return true;
    }

    /**
     * Add payment to a booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function addPayment(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:gcash,onsite_cash,onsite_card',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $paymentMethod = $request->payment_method;
            $notes = $request->notes;

            // Only accept full payment of the total amount
            $amount = $booking->total_amount;

            // Create payment transaction
            if ($paymentMethod === 'gcash') {
                // For GCash, create Xendit payment link
                $xenditService = app(\App\Services\XenditService::class);
                
                Log::info('Creating GCash payment', [
                    'booking_id' => $booking->booking_id,
                    'amount' => $amount,
                    'reference' => $booking->booking_reference,
                ]);
                
                $paymentData = $xenditService->createEWalletPayment([
                    'reference_id' => $booking->booking_reference . '-' . time(),
                    'amount' => $amount,
                    'customer_email' => $booking->client_email,
                    'customer_name' => $booking->client_first_name . ' ' . $booking->client_last_name,
                    'description' => 'Payment for Booking ' . $booking->booking_reference,
                ]);

                if (!$paymentData['success']) {
                    Log::error('Failed to create GCash payment link', [
                        'booking_id' => $booking->booking_id,
                        'error' => $paymentData['message'] ?? 'Unknown error',
                        'data' => $paymentData,
                    ]);
                    throw new \Exception($paymentData['message'] ?? 'Failed to create payment link');
                }

                // Extract payment URL - now it's invoice_url
                $paymentUrl = $paymentData['data']['invoice_url'] ?? null;

                \App\Models\PaymentTransaction::create([
                    'booking_id' => $booking->booking_id,
                    'payment_method' => $paymentMethod,
                    'amount' => $amount,
                    'transaction_reference' => $paymentData['data']['reference_id'],
                    'xendit_payment_id' => $paymentData['data']['id'],
                    'xendit_status' => $paymentData['data']['status'] ?? 'PENDING',
                    'payment_date' => now(),
                    'processed_by' => session('admin_id') ?? session('staff_id'),
                    'notes' => $notes,
                    'metadata' => json_encode([
                        'payment_url' => $paymentUrl,
                        'invoice_url' => $paymentUrl,
                    ]),
                ]);

                DB::commit();

                // Don't show toast notification, just redirect with payment_url for modal
                return redirect()->back()
                    ->with('payment_url', $paymentUrl);

            } else {
                // For onsite payments, mark as paid immediately
                \App\Models\PaymentTransaction::create([
                    'booking_id' => $booking->booking_id,
                    'payment_method' => $paymentMethod,
                    'amount' => $amount,
                    'transaction_reference' => 'ONSITE-' . $booking->booking_reference . '-' . time(),
                    'xendit_status' => 'PAID',
                    'payment_date' => now(),
                    'processed_by' => session('admin_id') ?? session('staff_id'),
                    'notes' => $notes,
                ]);

                // Update booking payment status to paid (full payment)
                $booking->payment_status = 'paid';
                $booking->save();

                DB::commit();

                return redirect()->back()
                    ->with('success', 'Payment recorded successfully');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error adding payment', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->booking_id
            ]);

            return redirect()->back()
                ->with('error', 'Error adding payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate and send invoice via email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice(Request $request, Booking $booking)
    {
        try {
            // Validate email address exists
            if (empty($booking->client_email)) {
                throw new \Exception('No email address found for this booking');
            }

            // Load necessary relationships
            $booking->load(['package', 'addons', 'payments', 'primaryStaff']);
            
            // Ensure addons is a collection (not null)
            if (!$booking->relationLoaded('addons') || $booking->addons === null) {
                $booking->setRelation('addons', collect([]));
            }

            // Verify package exists
            if (!$booking->package) {
                throw new \Exception('Package information not found for this booking');
            }

            \Log::info('Attempting to send invoice', [
                'booking_id' => $booking->booking_id,
                'client_email' => $booking->client_email,
                'package_title' => $booking->package->title ?? 'N/A',
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson(),
                'has_xhr_header' => $request->header('X-Requested-With') === 'XMLHttpRequest',
            ]);

            // Send invoice email
            \Mail::to($booking->client_email)->send(new \App\Mail\BookingInvoiceMail($booking));

            \Log::info('Invoice email sent successfully', [
                'booking_id' => $booking->booking_id,
                'client_email' => $booking->client_email,
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice sent successfully to ' . $booking->client_email
                ]);
            }

            return redirect()->back()
                ->with('success', 'Invoice sent successfully to ' . $booking->client_email);

        } catch (\Swift_TransportException $e) {
            \Log::error('SMTP Error generating invoice', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->booking_id,
                'client_email' => $booking->client_email ?? 'N/A',
            ]);

            $errorMessage = 'SMTP Error: ' . $e->getMessage();
            
            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage);

        } catch (\Exception $e) {
            \Log::error('Error generating invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $booking->booking_id,
                'client_email' => $booking->client_email ?? 'N/A',
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error sending invoice: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error sending invoice: ' . $e->getMessage());
        }
    }
}
