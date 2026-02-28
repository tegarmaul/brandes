<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        $admins        = User::where('role', 'admin')->get();
        $totalAdmin    = $admins->count();
        $adminAktif    = $admins->where('aktif', true)->count();
        $adminNonaktif = $admins->where('aktif', false)->count();

        return view('admin.list', compact('admins', 'totalAdmin', 'adminAktif', 'adminNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'pin'      => 'required|numeric|digits:6',
            'role'     => 'required|in:admin',
        ]);

        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'pin'      => Hash::make($request->pin),
            'role'     => $request->role,
            'aktif'    => true,
        ]);

        return redirect()->route('admin.list')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'pin'      => 'nullable|numeric|digits:6',
            'aktif'    => 'boolean',
        ]);

        $data = [
            'nama'     => $request->nama,
            'username' => $request->username,
            'aktif'    => $request->boolean('aktif', true),
        ];

        if ($request->filled('pin')) {
            $data['pin'] = Hash::make($request->pin);
        }

        $admin->update($data);

        return redirect()->route('admin.list')->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.list')->with('success', 'Admin berhasil dihapus.');
    }
}
