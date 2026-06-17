<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DepartmentManagementController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name', 'asc')->paginate(10);
        return view('admin.departments', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:departments',
            'handler_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'staff_email' => 'required|email|unique:departments',
            'password' => 'required|string|min:8',
        ]);
        
        $department = Department::create([
            'name' => $request->name,
            'handler_name' => $request->handler_name,
            'description' => $request->description,
            'staff_email' => $request->staff_email,
            'staff_password' => Hash::make($request->password),
            'is_active' => true,
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Department',
            'description' => 'Added new department: ' . $department->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Department added successfully');
    }

    public function getData($id)
    {
        $department = Department::findOrFail($id);
        return response()->json($department);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|unique:departments,name,' . $id,
            'handler_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'staff_email' => 'required|email|unique:departments,staff_email,' . $id,
        ]);
        
        $department->update([
            'name' => $request->name,
            'handler_name' => $request->handler_name,
            'description' => $request->description,
            'staff_email' => $request->staff_email,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $department->update(['staff_password' => Hash::make($request->password)]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Department',
            'description' => 'Updated department: ' . $department->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Department updated successfully');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $departmentName = $department->name;
        $department->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Department',
            'description' => 'Deleted department: ' . $departmentName,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Department deleted successfully');
    }

    public function toggleStatus($id)
    {
        $department = Department::findOrFail($id);
        $department->update(['is_active' => !$department->is_active]);
        
        $status = $department->is_active ? 'activated' : 'deactivated';
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Department',
            'description' => $status . ' department: ' . $department->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', "Department {$status} successfully");
    }

    public function assignYearsToDepartment($id, Request $request)
    {
        $department = Department::findOrFail($id);
        
        $request->validate([
            'years' => 'required|array',
            'years.*' => 'in:1st Year,2nd Year,3rd Year,4th Year'
        ]);
        
        \App\Models\DepartmentYearAssignment::where('department_id', $id)->delete();
        
        foreach ($request->years as $year) {
            \App\Models\DepartmentYearAssignment::create([
                'department_id' => $id,
                'year_level' => $year
            ]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Department',
            'description' => 'Updated year assignments for department: ' . $department->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Year levels assigned successfully!');
    }
}