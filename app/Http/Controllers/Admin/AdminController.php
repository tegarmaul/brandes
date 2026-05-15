<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Admin.
     * Mengambil seluruh data user dengan role admin dan menghitung rekapitulasi statusnya.
     */
    public function index()
    {
        // 1. Verifikasi Autentikasi (Hanya sesi Admin yang diizinkan mengakses)
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        // 2. Pengambilan Data & Kalkulasi Statistik
        // Mengurutkan data agar akun Super Admin selalu berada di urutan No. 1
        $admins = User::where('role', 'admin')
            ->orderBy('is_super_admin', 'desc')
            ->orderBy('id', 'asc') // Urutan selanjutnya berdasarkan yang paling lama dibuat
            ->get();
        $totalAdmin    = $admins->count();
        $adminAktif    = $admins->where('aktif', true)->count();
        $adminNonaktif = $admins->where('aktif', false)->count();

        // 3. Render Tampilan dengan Data Pendukung
        return view('admin.list-admin', compact('admins', 'totalAdmin', 'adminAktif', 'adminNonaktif'));
    }

    /**
     * Menyimpan entri data Admin baru ke dalam database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Formulir
        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'pin'      => 'required|numeric|digits:6',
            'role'     => 'required|in:admin',
        ]);

        // 2. Eksekusi Penambahan Data
        User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'pin'      => Crypt::encryptString($request->pin),
            'role'     => $request->role,
            'aktif'    => true,                      // Akun yang baru dibuat selalu aktif secara default
        ]);

        return redirect()->route('admin.list')->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Memperbarui detail profil Admin yang sudah ada di database.
     */
    public function update(Request $request, $id)
    {
        // 1. Cari Target Admin
        $admin = User::findOrFail($id);

        // 2. Validasi Input (Aturan unique username diabaikan untuk ID milik dirinya sendiri)
        $request->validate([
            'nama'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'pin'      => 'nullable|numeric|digits:6', // PIN tidak wajib diisi (nullable) saat pembaruan profil
            'aktif'    => 'boolean',
        ]);

        // 3. Susun Objek Pembaruan Data
        $data = [
            'nama'     => $request->nama,
            'username' => $request->username,
            'aktif'    => $request->boolean('aktif', true),
        ];

        // 4. Proses PIN hanya jika pengguna sengaja mengisi input tersebut
        if ($request->filled('pin')) {
            $data['pin'] = Crypt::encryptString($request->pin);
        }

        // 5. Simpan Perubahan
        $admin->update($data);

        return redirect()->route('admin.list')->with('success', 'Admin berhasil diperbarui.');
    }

    /**
     * Menghapus data Admin secara permanen.
     */
    public function destroy($id)
    {
        $admin = User::findOrFail($id);

        // Proteksi: Super Admin tidak boleh dihapus
        if ($admin->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Super Admin tidak dapat dihapus.'
            ], 403);
        }

        $admin->delete();

        // Respons untuk antarmuka yang menggunakan request AJAX (contoh: Modal Fetch API)
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil dihapus.'
            ]);
        }

        // Respons Fallback jika terjadi navigasi konvensional (non-AJAX)
        return redirect()->route('admin.list')->with('success', 'Admin berhasil dihapus.');
    }

    /**
     * Mengubah status operasional (Aktif/Nonaktif) dari seorang Admin secara dinamis.
     */
    public function toggleStatus($id)
    {
        $admin = User::findOrFail($id);

        // Proteksi: Status Super Admin tidak boleh dinonaktifkan
        if ($admin->is_super_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Status Super Admin tidak dapat diubah.'
            ], 403);
        }
        
        // Membalikkan nilai boolean yang berlaku saat ini (Toggle Switch: true -> false, false -> true)
        $admin->aktif = !$admin->aktif;
        $admin->save();

        // Mengembalikan status dalam bentuk JSON agar UI/Toggleswitch ter-update tanpa me-reload halaman
        return response()->json([
            'success' => true,
            'aktif'   => (bool) $admin->aktif
        ]);
    }
}
