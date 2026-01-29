<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    /**
     * Display a listing of the addons.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addons = Addon::orderBy('addon_name')->get();
        return view('addons.index', compact('addons'));
    }

    /**
     * Show the form for creating a new addon.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('addons.create');
    }

    /**
     * Store a newly created addon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'addon_name' => 'required|string|max:100|unique:addons,addon_name',
            'addon_price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form and correct any errors.');
        }

        try {
            $addon = new Addon();
            $addon->addon_name = trim($request->input('addon_name'));
            $addon->addon_price = $request->input('addon_price');
            $addon->description = $request->input('description');
            $addon->is_active = $request->has('is_active') ? 1 : 0;
            $addon->save();

            return redirect()->route('addons.index')
                ->with('success', 'Addon created successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Addon creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create addon. Please try again.');
        }
    }

    /**
     * Display the specified addon.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $addon = Addon::findOrFail($id);
        return view('addons.show', compact('addon'));
    }

    /**
     * Show the form for editing the specified addon.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $addon = Addon::findOrFail($id);
        return view('addons.edit', compact('addon'));
    }

    /**
     * Update the specified addon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'addon_name' => 'required|string|max:100|unique:addons,addon_name,' . $id . ',addon_id',
            'addon_price' => 'required|numeric|min:0|max:999999.99',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form and correct any errors.');
        }

        try {
            $addon = Addon::findOrFail($id);
            $addon->addon_name = trim($request->input('addon_name'));
            $addon->addon_price = $request->input('addon_price');
            $addon->description = $request->input('description');
            $addon->is_active = $request->has('is_active') ? 1 : 0;
            $addon->save();

            return redirect()->route('addons.index')
                ->with('success', 'Addon updated successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Addon update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update addon. Please try again.');
        }
    }

    /**
     * Remove the specified addon from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $addon = Addon::findOrFail($id);
            
            // Check if addon is used in any packages
            $packagesCount = $addon->packages()->count();
            if ($packagesCount > 0) {
                return redirect()->route('addons.index')
                    ->with('error', 'Cannot delete addon. It is currently used in ' . $packagesCount . ' package(s).');
            }
            
            // Delete associations with packages
            $addon->packages()->detach();
            
            $addon->delete();

            return redirect()->route('addons.index')
                ->with('success', 'Addon deleted successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Addon deletion failed: ' . $e->getMessage());
            
            return redirect()->route('addons.index')
                ->with('error', 'Failed to delete addon. Please try again.');
        }
    }

    /**
     * Toggle the status of the addon.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $addon = Addon::findOrFail($id);
        $addon->is_active = !$addon->is_active;
        $addon->save();

        return redirect()->route('addons.index')
            ->with('success', 'Addon status updated successfully.');
    }

    /**
     * Get all addons in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddonsJson()
    {
        $addons = Addon::where('is_active', true)->get();
        return response()->json($addons);
    }
    
    /**
     * Get addons list for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddonsList()
    {
        $addons = Addon::where('is_active', 1)
            ->select('addon_id', 'addon_name', 'addon_price', 'description')
            ->orderBy('addon_name')
            ->get();
            
        return response()->json($addons);
    }
}
