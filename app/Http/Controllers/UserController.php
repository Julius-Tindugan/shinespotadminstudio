<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of admin users
     *
     * @return \Illuminate\Http\Response
     */
    public function adminIndex()
    {
        $admins = Admin::with('roles')->get();
        return view('admin.users.admin-index', compact('admins'));
    }
    
    /**
     * Display a listing of staff users
     *
     * @return \Illuminate\Http\Response
     */
    public function staffIndex()
    {
        $staff = Staff::with('roles')->get();
        return view('admin.users.staff-index', compact('staff'));
    }
    
    /**
     * Show the form for creating a new admin user
     *
     * @return \Illuminate\Http\Response
     */
    public function createAdmin()
    {
        $roles = Role::all();
        return view('admin.users.admin-create', compact('roles'));
    }
    
    /**
     * Show the form for creating a new staff user
     *
     * @return \Illuminate\Http\Response
     */
    public function createStaff()
    {
        $roles = Role::all();
        return view('admin.users.staff-create', compact('roles'));
    }
    
    /**
     * Store a newly created admin user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:admin_users',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:admin_users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,role_id'
        ]);
        
        try {
            // Create admin user
            $admin = new Admin();
            $admin->username = $request->username;
            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->email = $request->email;
            $admin->password = $request->password; // This uses the password setter in the model
            $admin->is_active = 1;
            $admin->save();
            
            // Assign roles
            $admin->roles()->attach($request->roles);
            
            Log::info('Admin user created', ['admin_id' => $admin->admin_id]);
            
            return redirect()->route('admin.users.admin-index')
                ->with('success', 'Admin user created successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to create admin user', ['error' => $e->getMessage()]);
            
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Failed to create admin user: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created staff user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:staff_users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,role_id'
        ]);
        
        try {
            // Create staff user
            $staff = Staff::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password, // The setter will hash this automatically
                'status' => 'active',
                'last_login' => null,
            ]);
            
            // Assign roles
            $staff->roles()->attach($request->roles);
            
            Log::info('Staff user created', ['staff_id' => $staff->staff_id]);
            
            return redirect()->route('admin.users.staff-index')
                ->with('success', 'Staff user created successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to create staff user', ['error' => $e->getMessage()]);
            
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Failed to create staff user: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified admin user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAdmin($id)
    {
        $admin = Admin::with('roles')->findOrFail($id);
        $roles = Role::all();
        $assignedRoles = $admin->roles->pluck('role_id')->toArray();
        
        return view('admin.users.admin-edit', compact('admin', 'roles', 'assignedRoles'));
    }
    
    /**
     * Show the form for editing the specified staff user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editStaff($id)
    {
        $staff = Staff::with('roles')->findOrFail($id);
        $roles = Role::all();
        $assignedRoles = $staff->roles->pluck('role_id')->toArray();
        
        return view('admin.users.staff-edit', compact('staff', 'roles', 'assignedRoles'));
    }
    
    /**
     * Update the specified admin user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $request->validate([
            'username' => 'required|string|max:50|unique:admin_users,username,'.$id.',admin_id',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100|unique:admin_users,email,'.$id.',admin_id',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,role_id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        try {
            // Update admin user
            $admin->username = $request->username;
            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->email = $request->email;
            
            if ($request->filled('password')) {
                $admin->password = $request->password; // Uses the mutator
            }
            
            $admin->save();
            
            // Update roles
            $admin->roles()->sync($request->roles);
            
            Log::info('Admin user updated', ['admin_id' => $admin->admin_id]);
            
            return redirect()->route('admin.users.admin-index')
                ->with('success', 'Admin user updated successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to update admin user', ['error' => $e->getMessage(), 'admin_id' => $id]);
            
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Failed to update admin user: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the specified staff user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStaff(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:staff_users,email,'.$id.',staff_id',
            'phone' => 'required|string|max:20',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,role_id',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
        ]);
        
        try {
            // Update staff data
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];
            
            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password_hash'] = Hash::make($request->password);
            }
            
            $staff->update($updateData);
            
            // Update roles
            $staff->roles()->sync($request->roles);
            
            Log::info('Staff user updated', ['staff_id' => $staff->staff_id]);
            
            return redirect()->route('admin.users.staff-index')
                ->with('success', 'Staff user updated successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to update staff user', ['error' => $e->getMessage(), 'staff_id' => $id]);
            
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Failed to update staff user: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified admin user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        
        try {
            // First remove role associations
            $admin->roles()->detach();
            
            // Then delete the admin (or mark inactive if Firebase deletion handled it)
            if (!$admin->firebase_uid || !isset($firebaseResult) || !$firebaseResult['success']) {
                $admin->delete();
            }
            
            Log::info('Admin user deleted', ['admin_id' => $id]);
            
            return redirect()->route('admin.users.admin-index')
                ->with('success', 'Admin user deleted successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete admin user', ['error' => $e->getMessage(), 'admin_id' => $id]);
            
            return back()->with('error', 'Failed to delete admin user: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified staff user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyStaff($id)
    {
        $staff = Staff::findOrFail($id);
        
        try {
            // First remove role associations
            $staff->roles()->detach();
            
            // Delete the staff user
            $staff->delete();
            
            Log::info('Staff user deleted', ['staff_id' => $id]);
            
            return redirect()->route('admin.users.staff-index')
                ->with('success', 'Staff user deleted successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete staff user', ['error' => $e->getMessage(), 'staff_id' => $id]);
            
            return back()->with('error', 'Failed to delete staff user: ' . $e->getMessage());
        }
    }
}
