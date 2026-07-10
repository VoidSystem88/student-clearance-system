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
        $staffs = User::where('role', 'staff')
            ->with('department') // ✅ Eager load department
            ->orderBy('created_at', 'desc')
            ->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.staffs', compact('staffs', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id', // ✅ Gawing REQUIRED
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);
        
        // ✅ Auto-update din ang department's staff_email kung wala pa
        $department = Department::find($request->department_id);
        if ($department && !$department->staff_email) {
            $department->update(['staff_email' => $request->email]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Staff',
            'description' => 'Added new staff: ' . $user->email . ' to ' . ($department->name ?? 'N/A'),
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff added successfully!');
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'department_id' => 'required|exists:departments,id', // ✅ Gawing REQUIRED
        ]);
        
        $oldDepartmentId = $staff->department_id;
        
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $staff->update(['password' => Hash::make($request->password)]);
        }
        
        // ✅ Update department staff_email if changed
        if ($oldDepartmentId != $request->department_id) {
            // Clear old department's staff_email
            $oldDept = Department::find($oldDepartmentId);
            if ($oldDept && $oldDept->staff_email == $staff->email) {
                $oldDept->update(['staff_email' => null]);
            }
            
            // Set new department's staff_email
            $newDept = Department::find($request->department_id);
            if ($newDept) {
                $newDept->update(['staff_email' => $request->email]);
            }
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Staff',
            'description' => 'Updated staff: ' . $staff->email,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff updated successfully!');
    }

    public function destroy($id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        $staffEmail = $staff->email;
        $departmentId = $staff->department_id;
        
        // ✅ Clear department staff_email before deleting
        if ($departmentId) {
            $dept = Department::find($departmentId);
            if ($dept && $dept->staff_email == $staffEmail) {
                $dept->update(['staff_email' => null]);
            }
        }
        
        $staff->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Staff',
            'description' => 'Deleted staff: ' . $staffEmail,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff deleted successfully!');
    }
}