<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'role', 'suspended_at', 'updated_at', 'created_at', 'is_admin');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Role filter
        if ($request->role && $request->role !== 'all') {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } else {
                $query->where('role', $request->role)->where('is_admin', false);
            }
        }

        // Status filter - based on suspended_at
        if ($request->status && $request->status !== 'all') {
            if ($request->status === 'suspended') {
                $query->whereNotNull('suspended_at');
            } elseif ($request->status === 'active') {
                $query->whereNull('suspended_at');
            }
        }

        $users = $query
            ->orderBy($request->get('sort', 'name'), $request->get('direction', 'asc'))
            ->paginate(8)
            ->appends($request->query());

        // Real counts (across entire table, not just current page)
        $totalCount     = User::count();
        $activeCount    = User::whereNull('suspended_at')->count();
        $adminCount     = User::where('is_admin', true)->count();
        $suspendedCount = User::whereNotNull('suspended_at')->count();

        return view('users', compact('users', 'totalCount', 'activeCount', 'adminCount', 'suspendedCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role'  => ['required', Rule::in(['viewer', 'admin'])],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make(Str::random(12)),
            'role'     => $request->role,
            'is_admin' => $request->role === 'admin',
            'suspended_at' => null,
        ]);

        // TODO: dispatch invite mail here

        return redirect()->route('users.index')->with('success', 'User invited successfully!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'  => ['required', Rule::in(['viewer', 'admin'])],
        ]);

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'is_admin' => $request->role === 'admin',
        ]);

        return response()->json(['success' => true]);
    }

    public function suspend(User $user)
    {
        // Prevent self-suspension
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot suspend yourself.'], 403);
        }

        // Toggle suspend status
        if ($user->suspended_at) {
            $user->suspended_at = null;
            $message = 'User unsuspended successfully!';
        } else {
            $user->suspended_at = now();
            $message = 'User suspended successfully!';
        }
        
        $user->save();

        return response()->json(['success' => true, 'message' => $message, 'suspended' => (bool) $user->suspended_at]);
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}