<?php

namespace App\Http\Controllers;

use App\Models\Backdrop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BackdropController extends Controller
{
    /**
     * Display a listing of the backdrops.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Backdrop::query();

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        $backdrops = $query->orderBy('name')->paginate(10);
        
        return view('studio.backdrops.index', compact('backdrops'));
    }

    /**
     * Show the form for creating a new backdrop.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('studio.backdrops.create');
    }

    /**
     * Store a newly created backdrop in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:backdrops',
            'color_code' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1,true,false,on,off',
        ]);

        Backdrop::create([
            'name' => $request->name,
            'color_code' => $request->color_code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('backdrops.index')
            ->with('success', 'Backdrop added successfully');
    }

    /**
     * Display the specified backdrop.
     *
     * @param  \App\Models\Backdrop  $backdrop
     * @return \Illuminate\Http\Response
     */
    public function show(Backdrop $backdrop)
    {
        // Get bookings that use this backdrop
        $bookings = $backdrop->bookings()->orderBy('booking_date', 'desc')->paginate(5);
        
        return view('studio.backdrops.show', compact('backdrop', 'bookings'));
    }

    /**
     * Show the form for editing the specified backdrop.
     *
     * @param  \App\Models\Backdrop  $backdrop
     * @return \Illuminate\Http\Response
     */
    public function edit(Backdrop $backdrop)
    {
        // Count active bookings that use this backdrop
        $activeBookings = $backdrop->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
            
        return view('studio.backdrops.edit', compact('backdrop', 'activeBookings'));
    }

    /**
     * Update the specified backdrop in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Backdrop  $backdrop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Backdrop $backdrop)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('backdrops')->ignore($backdrop->backdrop_id, 'backdrop_id'),
            ],
            'color_code' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active' => 'nullable|in:0,1,true,false,on,off',
        ]);

        $backdrop->update([
            'name' => $request->name,
            'color_code' => $request->color_code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('backdrops.index')
            ->with('success', 'Backdrop updated successfully');
    }

    /**
     * Remove the specified backdrop from storage.
     *
     * @param  \App\Models\Backdrop  $backdrop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Backdrop $backdrop)
    {
        // Check if backdrop is used in any bookings
        if ($backdrop->bookings()->count() > 0) {
            return redirect()->route('backdrops.index')
                ->with('error', 'Cannot delete backdrop. It is used in bookings.');
        }

        $backdrop->delete();

        return redirect()->route('backdrops.index')
            ->with('success', 'Backdrop deleted successfully');
    }
    
    /**
     * Get backdrops list for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBackdropsList()
    {
        $backdrops = Backdrop::where('is_active', 1)
            ->select('backdrop_id', 'name', 'color_code')
            ->orderBy('name')
            ->get();
            
        return response()->json($backdrops);
    }
}
