<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageCategory;
use App\Models\PackageInclusion;
use App\Models\PackageFreeItem;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    /**
     * Display a listing of the packages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Package::with(['category', 'inclusions', 'freeItems']);
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
        
        // Sort options - always prioritize featured packages first
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Always order by is_featured DESC first (featured = 1 comes first)
        $query->orderBy('is_featured', 'desc');
        
        // Then apply secondary sorting
        if ($sortBy === 'category') {
            $query->join('package_categories', 'packages.category_id', '=', 'package_categories.category_id')
                  ->orderBy('package_categories.name', $sortOrder)
                  ->select('packages.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $packages = $query->paginate(12)->withQueryString();
        $categories = PackageCategory::active()->get();
        
        return view('packages.index', compact('packages', 'categories'));
    }

    /**
     * Show the form for creating a new package.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = PackageCategory::active()->orderBy('name')->get();
        $addons = Addon::where('is_active', true)->get();
        
        return view('packages.create', compact('categories', 'addons'));
    }

    /**
     * Store a newly created package in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:package_categories,category_id',
            'title' => 'required|string|max:100|unique:packages,title',
            'price' => 'required|numeric|min:0|max:999999.99',
            'duration_hours' => 'nullable|integer|min:0|max:48',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'max_capacity' => 'nullable|integer|min:1|max:1000',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|in:0,1,true,false,on,off',
            'is_featured' => 'nullable|in:0,1,true,false,on,off',
            'max_bookings_per_day' => 'nullable|integer|min:1|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'inclusions' => 'nullable|array',
            'inclusions.*' => 'nullable|string|max:255|distinct',
            'free_items' => 'nullable|array', 
            'free_items.*' => 'nullable|string|max:255|distinct',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,addon_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form and correct any errors.');
        }

        // Additional validation for featured packages limit
        if ($request->has('is_featured')) {
            $featuredValidation = Package::validateFeaturedStatus(true);
            if (!$featuredValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $featuredValidation['message']);
            }
        }

        try {
            DB::beginTransaction();
            
            $package = new Package();
            $package->category_id = $request->input('category_id');
            $package->title = $request->input('title');
            $package->price = $request->input('price');
            
            // Handle duration (hours and minutes)
            $hours = $request->input('duration_hours', 0);
            $minutes = $request->input('duration_minutes', 0);
            if ($hours > 0 || $minutes > 0) {
                $package->setDurationFromHoursMinutes($hours, $minutes);
            }
            
            $package->max_capacity = $request->input('max_capacity');
            $package->description = $request->input('description');
            $package->is_active = $request->has('is_active') ? 1 : 0;
            $package->is_featured = $request->has('is_featured') ? 1 : 0;
            $package->max_bookings_per_day = $request->input('max_bookings_per_day');
            
            // Set sort order to last
            $maxSortOrder = Package::max('sort_order') ?? 0;
            $package->sort_order = $maxSortOrder + 1;
            
            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                
                // Validate file size (5MB limit)
                if ($image->getSize() > 5242880) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Image file is too large. Maximum size is 5MB.');
                }
                
                // Store image as base64-encoded blob in database
                $imageData = file_get_contents($image->getRealPath());
                if ($imageData !== false) {
                    $package->image_data = base64_encode($imageData);
                    $package->image_mime_type = $image->getClientMimeType();
                    $package->image_name = $image->getClientOriginalName();
                    $package->image_size = $image->getSize();
                }
            }
            
            $package->save();
            
            // Save inclusions
            if ($request->has('inclusions') && is_array($request->input('inclusions'))) {
                foreach ($request->input('inclusions') as $inclusionText) {
                    $inclusionText = trim($inclusionText);
                    if (!empty($inclusionText)) {
                        $inclusion = new PackageInclusion();
                        $inclusion->package_id = $package->package_id;
                        $inclusion->inclusion_text = $inclusionText;
                        $inclusion->save();
                    }
                }
            }
            
            // Save free items
            if ($request->has('free_items') && is_array($request->input('free_items'))) {
                foreach ($request->input('free_items') as $freeItemText) {
                    $freeItemText = trim($freeItemText);
                    if (!empty($freeItemText)) {
                        $freeItem = new PackageFreeItem();
                        $freeItem->package_id = $package->package_id;
                        $freeItem->free_item_text = $freeItemText;
                        $freeItem->save();
                    }
                }
            }
            

            
            // Sync addons
            if ($request->has('addons') && is_array($request->input('addons'))) {
                $addonIds = array_filter($request->input('addons'), function($id) {
                    return is_numeric($id) && $id > 0;
                });
                if (!empty($addonIds)) {
                    $package->addons()->sync($addonIds);
                }
            }

            DB::commit();
            
            $message = 'Package created successfully.';
            if ($package->is_featured) {
                $message .= ' Your featured package appears at the top of the list.';
            } else {
                $message .= ' Your package appears in the list sorted by display order.';
            }
            
            return redirect()->route('packages.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Package creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create package. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified package.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $package = Package::with(['category', 'inclusions', 'freeItems', 'addons'])->findOrFail($id);
        return view('packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified package.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $package = Package::with(['category', 'inclusions', 'freeItems', 'addons'])->findOrFail($id);
        
        // Get all active categories
        $categories = PackageCategory::active()->orderBy('name')->get();
        
        // If the current package has an inactive category, include it in the list
        if ($package->category && !$package->category->is_active) {
            $categories->prepend($package->category);
        }
        
        $addons = Addon::where('is_active', true)->get();
        
        return view('packages.edit', compact('package', 'categories', 'addons'));
    }

    /**
     * Update the specified package in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Log::info('Package update started', [
            'package_id' => $id,
            'request_data' => $request->all()
        ]);

        // Get current package for validation context
        $currentPackage = Package::with('category')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => [
                'required',
                'exists:package_categories,category_id',
                function ($attribute, $value, $fail) use ($currentPackage) {
                    $selectedCategory = PackageCategory::find($value);
                    if ($selectedCategory && !$selectedCategory->is_active) {
                        // Allow inactive categories for existing packages but log a warning
                        \Log::warning('Package updated with inactive category', [
                            'package_id' => $currentPackage->package_id,
                            'category_id' => $value,
                            'category_name' => $selectedCategory->name,
                            'is_current_category' => $currentPackage->category_id == $value
                        ]);
                    }
                }
            ],
            'title' => 'required|string|max:100|unique:packages,title,' . $id . ',package_id',
            'price' => 'required|numeric|min:0|max:999999.99',
            'duration_hours' => 'nullable|integer|min:0|max:48',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'max_capacity' => 'nullable|integer|min:1|max:1000',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|in:0,1,true,false,on,off',
            'is_featured' => 'nullable|in:0,1,true,false,on,off',
            'max_bookings_per_day' => 'nullable|integer|min:1|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'remove_image' => 'nullable|in:0,1,true,false,on,off',
            'inclusions' => 'nullable|array',
            'inclusions.*' => 'nullable|string|max:255|distinct',
            'free_items' => 'nullable|array',
            'free_items.*' => 'nullable|string|max:255|distinct',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,addon_id',
        ], [
            'category_id.required' => 'A category must be selected for this package.',
            'category_id.exists' => 'The selected category is invalid or no longer exists.',
            'title.required' => 'The package title is required.',
            'title.unique' => 'A package with this title already exists.',
            'price.required' => 'The package price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'price.max' => 'The price is too high (maximum: ₱999,999.99).',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a JPEG, PNG, JPG, GIF, or WEBP file.',
            'image.max' => 'The image size cannot exceed 5MB.',
            'is_active.in' => 'The active status must be a valid value.',
            'is_featured.in' => 'The featured status must be a valid value.',
            'remove_image.in' => 'Invalid value for image removal option.',
        ]);

        if ($validator->fails()) {
            // Get current package for context
            $currentPackage = Package::with('category')->find($id);
            
            \Log::error('Package update validation failed', [
                'package_id' => $id,
                'current_category_id' => $currentPackage ? $currentPackage->category_id : 'unknown',
                'current_category_active' => $currentPackage && $currentPackage->category ? $currentPackage->category->is_active : 'unknown',
                'submitted_category_id' => $request->input('category_id'),
                'errors' => $validator->errors()->toArray(),
                'failed_rules' => $validator->failed(),
                'input_summary' => [
                    'title' => $request->input('title'),
                    'category_id' => $request->input('category_id'),
                    'price' => $request->input('price'),
                    'has_image' => $request->hasFile('image'),
                    'remove_image' => $request->input('remove_image')
                ]
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed. Please check the form and correct any errors.');
        }

        // Additional validation for featured packages limit
        if ($request->has('is_featured')) {
            $featuredValidation = Package::validateFeaturedStatus(true, $id);
            if (!$featuredValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $featuredValidation['message']);
            }
        }

        try {
            // Use the already loaded package from validation
            $package = $currentPackage;
            
            // Debug: Log the initial state and request parameters
            \Log::info('Package update started', [
                'package_id' => $id,
                'has_current_image' => !empty($package->image_data),
                'remove_image_flag' => $request->input('remove_image'),
                'has_new_image_upload' => $request->hasFile('image'),
                'current_image_name' => $package->image_name
            ]);
            
            $package->category_id = $request->input('category_id');
            $package->title = $request->input('title');
            $package->price = $request->input('price');
            
            // Handle duration (hours and minutes)
            $hours = $request->input('duration_hours', 0);
            $minutes = $request->input('duration_minutes', 0);
            if ($hours > 0 || $minutes > 0) {
                $package->setDurationFromHoursMinutes($hours, $minutes);
            } else {
                $package->duration_hours = null;
            }
            
            $package->max_capacity = $request->input('max_capacity');
            $package->description = $request->input('description');
            $package->is_active = $request->has('is_active') ? 1 : 0;
            $package->is_featured = $request->has('is_featured') ? 1 : 0;
            $package->max_bookings_per_day = $request->input('max_bookings_per_day');
            
            // Handle image operations - process removal first, then upload
            // Step 1: Handle image removal if requested (clear existing image data)
            if ($request->has('remove_image') && $request->input('remove_image') == '1') {
                $package->image_data = null;
                $package->image_mime_type = null;
                $package->image_name = null;
                $package->image_size = null;
            }
            
            // Step 2: Handle new image upload (will replace any existing image or add new one)
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image');
                
                // Validate file size (5MB limit)
                if ($image->getSize() > 5242880) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Image file is too large. Maximum size is 5MB.');
                }
                
                // Validate file type
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!in_array($image->getClientMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Invalid image format. Please upload JPEG, PNG, JPG, GIF, or WEBP images only.');
                }
                
                // Store new image as base64-encoded blob in database
                $imageData = file_get_contents($image->getRealPath());
                if ($imageData !== false) {
                    $encodedData = base64_encode($imageData);
                    $package->image_data = $encodedData;
                    $package->image_mime_type = $image->getClientMimeType();
                    $package->image_name = $image->getClientOriginalName();
                    $package->image_size = $image->getSize();
                }
            }
            
            // Debug: Log the final image state before saving
            \Log::info('Package update - final image state', [
                'package_id' => $id,
                'has_image_data' => !empty($package->image_data),
                'image_name' => $package->image_name,
                'image_size' => $package->image_size,
                'image_mime_type' => $package->image_mime_type
            ]);
            
            $package->save();
            
            // Update inclusions (delete old ones and add new ones)
            $package->inclusions()->delete();
            if ($request->has('inclusions') && is_array($request->input('inclusions'))) {
                foreach ($request->input('inclusions') as $inclusionText) {
                    $inclusionText = trim($inclusionText);
                    if (!empty($inclusionText)) {
                        $inclusion = new PackageInclusion();
                        $inclusion->package_id = $package->package_id;
                        $inclusion->inclusion_text = $inclusionText;
                        $inclusion->save();
                    }
                }
            }
            
            // Update free items (delete old ones and add new ones)
            $package->freeItems()->delete();
            if ($request->has('free_items') && is_array($request->input('free_items'))) {
                foreach ($request->input('free_items') as $freeItemText) {
                    $freeItemText = trim($freeItemText);
                    if (!empty($freeItemText)) {
                        $freeItem = new PackageFreeItem();
                        $freeItem->package_id = $package->package_id;
                        $freeItem->free_item_text = $freeItemText;
                        $freeItem->save();
                    }
                }
            }
            
            // Sync addons
            if ($request->has('addons') && is_array($request->input('addons'))) {
                $addonIds = array_filter($request->input('addons'), function($id) {
                    return is_numeric($id) && $id > 0;
                });
                $package->addons()->sync($addonIds);
            } else {
                $package->addons()->detach();
            }

            \Log::info('Package update completed successfully', [
                'package_id' => $id,
                'updated_title' => $package->title
            ]);

            $message = 'Package updated successfully.';
            if ($package->is_featured) {
                $message .= ' Featured packages appear at the top of the list.';
            }

            return redirect()->route('packages.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Package update failed: ' . $e->getMessage(), [
                'package_id' => $id,
                'exception' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update package. Please try again.');
        }
    }

    /**
     * Update package with image upload (using POST instead of PUT for better file upload support)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWithImage(Request $request, $id)
    {
        return $this->update($request, $id);
    }

    /**
     * Remove the specified package from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            // Delete related records (cascading should handle this, but being explicit)
            $package->inclusions()->delete();
            $package->freeItems()->delete(); 
            $package->addons()->detach();
            
            $package->delete();

            return redirect()->route('packages.index')
                ->with('success', 'Package deleted successfully.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Package deletion failed: ' . $e->getMessage());
            
            return redirect()->route('packages.index')
                ->with('error', 'Failed to delete package. It may be in use by existing bookings.');
        }
    }

    /**
     * Serve package image from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function serveImage($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            if (!$package->image_data || !$package->image_mime_type) {
                return response()->json(['error' => 'Image not found'], 404);
            }
            
            // Decode base64 image data
            $imageData = base64_decode($package->image_data);
            
            // Use ETag for caching based on last modified time and image size
            $etag = md5($package->updated_at . $package->image_size);
            $lastModified = $package->updated_at->format('D, d M Y H:i:s') . ' GMT';
            
            // Check if client has current version
            $ifNoneMatch = request()->header('If-None-Match');
            if ($ifNoneMatch && $ifNoneMatch === '"' . $etag . '"') {
                return response('', 304)
                    ->header('ETag', '"' . $etag . '"')
                    ->header('Last-Modified', $lastModified);
            }
            
            return response($imageData)
                ->header('Content-Type', $package->image_mime_type)
                ->header('Content-Length', strlen($imageData))
                ->header('ETag', '"' . $etag . '"')
                ->header('Last-Modified', $lastModified)
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour instead of 1 year
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image not found'], 404);
        }
    }

    /**
     * Remove image from package via AJAX.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeImage($id)
    {
        try {
            $package = Package::findOrFail($id);
            
            $package->image_data = null;
            $package->image_mime_type = null;
            $package->image_name = null;
            $package->image_size = null;
            $package->save();

            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully.'
            ]);
                
        } catch (\Exception $e) {
            \Log::error('Package image removal failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove image. Please try again.'
            ], 500);
        }
    }

    /**
     * Toggle the status of the specified package.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $package = Package::findOrFail($id);
        $package->is_active = !$package->is_active;
        $package->save();

        return redirect()->route('packages.index')
            ->with('success', 'Package status updated successfully.');
    }

    /**
     * Get package details including inclusions and free items.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackageDetails($id)
    {
        $package = Package::with(['inclusions', 'freeItems', 'addons'])->find($id);
        
        if (!$package) {
            return response()->json([
                'error' => 'Package not found'
            ], 404);
        }
        
        return response()->json([
            'package' => [
                'package_id' => $package->package_id,
                'title' => $package->title,
                'price' => $package->price,
                'description' => $package->description,
                'package_type' => $package->package_type,
            ],
            'inclusions' => $package->inclusions,
            'freeItems' => $package->freeItems,
            'addons' => $package->addons,
        ]);
    }
    
    /**
     * Get packages list for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackagesList()
    {
        $packages = Package::where('is_active', 1)
            ->select('package_id', 'title', 'price', 'package_type')
            ->orderBy('title')
            ->get();
            
        return response()->json($packages);
    }

    /**
     * Toggle featured status for a package.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function toggleFeatured(Request $request, $id)
    {
        try {
            $package = Package::findOrFail($id);
            $newFeaturedStatus = !$package->is_featured;

            // Validate featured status change
            if ($newFeaturedStatus) {
                $featuredValidation = Package::validateFeaturedStatus(true, $id);
                if (!$featuredValidation['valid']) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $featuredValidation['message']
                        ], 400);
                    }
                    
                    return redirect()->back()
                        ->with('error', $featuredValidation['message']);
                }
            }

            $package->is_featured = $newFeaturedStatus;
            $package->save();

            $message = $newFeaturedStatus 
                ? 'Package has been marked as featured and will appear at the top of the list.' 
                : 'Package has been removed from featured and will return to its regular position.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_featured' => $newFeaturedStatus,
                    'featured_count' => Package::getFeaturedCount(),
                    'max_featured_limit' => Package::getMaxFeaturedLimit()
                ]);
            }

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error toggling package featured status: ' . $e->getMessage(), [
                'package_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update featured status. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update featured status. Please try again.');
        }
    }

    /**
     * Get featured packages statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeaturedStats()
    {
        return response()->json([
            'featured_count' => Package::getFeaturedCount(),
            'max_featured_limit' => Package::getMaxFeaturedLimit(),
            'can_add_more' => Package::canBeFeatured()
        ]);
    }
}
