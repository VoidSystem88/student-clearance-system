<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffManagementController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 'staff')->orderBy('created_at', 'desc')->get();
        $departments = Department::all();
        return view('admin.staffs', compact('staffs', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Staff',
            'description' => 'Added new staff: ' . $user->email,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff added successfully');
    }

    public function getData($id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        return response()->json($staff);
    }

    public function update(Request $request, $id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'department_id' => 'nullable|exists:departments,id',
        ]);
        
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $staff->update(['password' => Hash::make($request->password)]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Staff',
            'description' => 'Updated staff: ' . $staff->email,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff updated successfully');
    }

    public function destroy($id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        $staffEmail = $staff->email;
        $staff->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Staff',
            'description' => 'Deleted staff: ' . $staffEmail,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff deleted successfully');
    }
}