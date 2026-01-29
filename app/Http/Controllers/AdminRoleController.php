<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Role;

class AdminRoleController extends Controller
{
    /**
     * Display a listing of admin roles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }
    
    /**
     * Show the form for creating a new role
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.roles.create');
    }
    
    /**
     * Store a newly created role
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string|max:255'
        ]);
        
        $role = Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description
        ]);
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }
    
    /**
     * Show the form for editing the specified role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }
    
    /**
     * Update the specified role
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:50|unique:roles,role_name,'.$id.',role_id',
            'description' => 'nullable|string|max:255'
        ]);
        
        $role = Role::findOrFail($id);
        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description
        ]);
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }
    
    /**
     * Remove the specified role
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Check if this is a protected role
        if (in_array($role->role_name, ['Super Admin', 'Admin', 'Staff'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete a protected system role');
        }
        
        // Check if role is in use
        if ($role->admins()->count() > 0 || $role->staff()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete a role that is assigned to users');
        }
        
        $role->delete();
        
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }
    
    /**
     * Assign roles to a user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignRoles(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:admin,staff',
            'user_id' => 'required|integer',
            'roles' => 'required|array',
            'roles.*' => 'integer|exists:roles,role_id'
        ]);
        
        if ($request->user_type === 'admin') {
            $admin = Admin::findOrFail($request->user_id);
            $admin->roles()->sync($request->roles);
        } else {
            $staff = Staff::findOrFail($request->user_id);
            $staff->roles()->sync($request->roles);
        }
        
        return redirect()->back()->with('success', 'Roles assigned successfully');
    }
}
