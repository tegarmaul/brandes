<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\LokasiBrankas;
use App\Models\LokasiHistory;

class LokasiController extends Controller
{
    public function index()
    {
        if (!session('user')) {
            return redirect()->route('login');
        }

        // Ambil semua brankas aktif
        $brankases = collect();
        $brankas   = null;

        try {
            $brankases = LokasiBrankas::where('aktif', true)->get();
            $brankas   = $brankases->first();
        } catch (\Exception $e) {
            // Tabel lokasi_brankas belum ada — jalankan: bun database/setup.mjs
        }

        // Ambil 50 history GPS terbaru
        // Jika tabel lokasi_history belum ada (migration belum dijalankan), kembalikan koleksi kosong
        $histories = collect();

        if (Schema::hasTable('lokasi_history')) {
            try {
                $histories = LokasiHistory::with('brankas')
                    ->when($brankas, fn($q) => $q->where('lokasi_brankas_id', $brankas->id))
                    ->orderByDesc('recorded_at')
                    ->limit(50)
                    ->get();
            } catch (\Exception $e) {
                // Fallback ke koleksi kosong
            }
        }

        return view('lokasi.brankas', compact('brankases', 'brankas', 'histories'));
    }
}
