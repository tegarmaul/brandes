<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        $admins       = User::where('role', 'admin')->get();
        $totalAdmin   = $admins->count();
        $adminAktif   = $admins->count(); // sesuaikan jika ada kolom status
        $adminNonaktif = 0;

        return view('admin.list', compact('admins', 'totalAdmin', 'adminAktif', 'adminNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'pin'  => 'required|numeric|digits:6',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'nama' => $request->nama,
            'pin'  => $request->pin,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.list')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.list')->with('success', 'Admin berhasil dihapus.');
    }
}