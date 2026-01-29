<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('is_available', 1);
            } elseif ($request->availability === 'unavailable') {
                $query->where('is_available', 0);
            }
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', 0);
            }
        }

        $equipment = $query->orderBy('name')->paginate(10);
        
        return view('studio.equipment.index', compact('equipment'));
    }

    /**
     * Show the form for creating new equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Define equipment types
        $equipmentTypes = [
            'Camera', 
            'Lens', 
            'Lighting', 
            'Lighting Modifier',
            'Prop', 
            'Accessory', 
            'Support', 
            'Background',
            'Other'
        ];

        return view('studio.equipment.create', compact('equipmentTypes'));
    }

    /**
     * Store a newly created equipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:equipment,name',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|string|in:Excellent,Good,Fair,Poor,Needs Repair',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('equipment', 'public');
        }

        // Create equipment
        Equipment::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'quantity' => $validated['quantity'],
            'cost' => $validated['cost'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'condition' => $validated['condition'],
            'is_available' => $request->has('is_available') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment added successfully');
    }

    /**
     * Display the specified equipment.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function show(Equipment $equipment)
    {
        // Get bookings that use this equipment
        $bookings = $equipment->bookings()->orderBy('booking_date', 'desc')->paginate(5);
        
        return view('studio.equipment.show', compact('equipment', 'bookings'));
    }

    /**
     * Show the form for editing the specified equipment.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipment $equipment)
    {
        $equipmentTypes = [
            'Camera', 
            'Lens', 
            'Lighting', 
            'Lighting Modifier',
            'Prop', 
            'Accessory', 
            'Support', 
            'Background',
            'Other'
        ];

        return view('studio.equipment.edit', compact('equipment', 'equipmentTypes'));
    }

    /**
     * Update the specified equipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipment', 'name')->ignore($equipment->equipment_id, 'equipment_id'),
            ],
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'condition' => 'required|string|in:Excellent,Good,Fair,Poor,Needs Repair',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($equipment->image) {
                Storage::disk('public')->delete($equipment->image);
            }
            $imagePath = $request->file('image')->store('equipment', 'public');
            $equipment->image = $imagePath;
        }

        // Update equipment
        $equipment->name = $validated['name'];
        $equipment->type = $validated['type'];
        $equipment->description = $validated['description'] ?? null;
        $equipment->quantity = $validated['quantity'];
        $equipment->cost = $validated['cost'] ?? null;
        $equipment->purchase_date = $validated['purchase_date'] ?? null;
        $equipment->condition = $validated['condition'];
        $equipment->is_available = $request->has('is_available') ? 1 : 0;
        $equipment->is_active = $request->has('is_active') ? 1 : 0;
        $equipment->save();

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment updated successfully');
    }

    /**
     * Remove the specified equipment from storage.
     *
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipment $equipment)
    {
        // Check if equipment is used in any bookings
        if ($equipment->bookings()->count() > 0) {
            return redirect()->route('equipment.index')
                ->with('error', 'Cannot delete equipment. It is used in bookings.');
        }

        // Delete image if it exists
        if ($equipment->image) {
            Storage::disk('public')->delete($equipment->image);
        }

        $equipment->delete();

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment deleted successfully');
    }
}
