<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('accounts', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'active',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('accounts')->with('success', 'User added successfully.');
    }

    public function archive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);
        return redirect()->route('accounts')->with('success', 'User archived successfully.');
    }

    public function unarchive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);
        return redirect()->route('accounts')->with('success', 'User unarchived successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'role' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('accounts')->with('success', 'User updated successfully.');
    }
}
