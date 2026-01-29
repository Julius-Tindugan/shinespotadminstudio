<?php
// Studio Management Routes - Add to routes/web.php inside the admin.auth middleware group

// Studio Management
Route::prefix('studio')->group(function () {
    // Main Dashboard
    Route::get('/', [App\Http\Controllers\StudioManagementController::class, 'index'])->name('studio.index');
    
    // Equipment Management
    Route::resource('equipment', App\Http\Controllers\EquipmentController::class);
    
    // Backdrop Management
    Route::resource('backdrops', App\Http\Controllers\BackdropController::class);
});
