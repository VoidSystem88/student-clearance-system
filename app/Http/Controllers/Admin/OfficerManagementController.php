<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OfficerManagementController extends Controller
{
    public function index()
    {
        $officers = User::where('role', 'officer')
            ->with('department')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $departments = Department::where('is_active', true)->get();
        
        return view('admin.officers', compact('officers', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'officer',
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Officer',
            'description' => 'Added new officer: ' . $user->email,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.officers')->with('success', 'Officer added successfully!');
    }

    public function getData($id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        return response()->json($officer);
    }

    public function update(Request $request, $id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'department_id' => 'required|exists:departments,id',
        ]);

        $officer->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $officer->update(['password' => Hash::make($request->password)]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Officer',
            'description' => 'Updated officer: ' . $officer->email,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
    }

    public function destroy($id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        
        if ($officer->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $officerEmail = $officer->email;
        $officer->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Officer',
            'description' => 'Deleted officer: ' . $officerEmail,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        $officer->update(['is_active' => !$officer->is_active]);
        
        $status = $officer->is_active ? 'activated' : 'deactivated';
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Officer',
            'description' => $status . ' officer: ' . $officer->email,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.officers')->with('success', "Officer {$status} successfully!");
    }
}