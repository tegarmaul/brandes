<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        $users        = User::where('role', 'user')->get();
        $totalUser    = $users->count();
        $userAktif    = $users->where('aktif', true)->count();
        $userNonaktif = $users->where('aktif', false)->count();

        return view('user.list', compact('users', 'totalUser', 'userAktif', 'userNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'pin'          => 'required|numeric|digits:6',
            // fingerprint_id: slot ID integer dari sensor biometrik ESP32 (1–127)
            'fingerprint_id' => 'nullable|integer|min:1|max:127|unique:users,fingerprint_id',
            'role'         => 'required|in:user',
        ]);

        User::create([
            'nama'           => $request->nama,
            'username'       => $request->username,
            'pin'            => Hash::make($request->pin),
            'fingerprint_id' => $request->fingerprint_id ?: null,
            'role'           => $request->role,
            'aktif'          => true,
        ]);

        return redirect()->route('user.list')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'           => 'required|string|max:255',
            'username'       => 'required|string|max:255|unique:users,username,' . $id,
            'pin'            => 'nullable|numeric|digits:6',
            'fingerprint_id' => 'nullable|integer|min:1|max:127|unique:users,fingerprint_id,' . $id,
            'aktif'          => 'boolean',
        ]);

        $data = [
            'nama'           => $request->nama,
            'username'       => $request->username,
            'fingerprint_id' => $request->fingerprint_id ?: null,
            'aktif'          => $request->boolean('aktif', true),
        ];

        if ($request->filled('pin')) {
            $data['pin'] = Hash::make($request->pin);
        }

        $user->update($data);

        return redirect()->route('user.list')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('user.list')->with('success', 'User berhasil dihapus.');
    }
}
