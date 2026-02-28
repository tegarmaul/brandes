<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        $users       = User::where('role', 'user')->get();
        $totalUser   = $users->count();
        $userAktif   = $users->count();
        $userNonaktif = 0;

        return view('user.list', compact('users', 'totalUser', 'userAktif', 'userNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string',
            'pin'         => 'required|numeric|digits:6',
            'fingerprint' => 'nullable|string',
            'role'        => 'required|in:user',
        ]);

        User::create([
            'nama'        => $request->nama,
            'pin'         => $request->pin,
            'fingerprint' => $request->fingerprint,
            'role'        => $request->role,
        ]);

        return redirect()->route('user.list')->with('success', 'User berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('user.list')->with('success', 'User berhasil dihapus.');
    }
}