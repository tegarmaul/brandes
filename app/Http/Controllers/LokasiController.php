<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LokasiBrankas;
use App\Models\LokasiHistory;

class LokasiController extends Controller
{
    public function index()
    {
        if (!session('user')) {
            return redirect()->route('login');
        }

        // Ambil semua brankas aktif beserta data GPS terbaru
        $brankases = LokasiBrankas::where('aktif', true)->get();

        // Ambil brankas pertama sebagai default tampilan peta
        $brankas = $brankases->first();

        // Ambil 50 history GPS terbaru untuk tabel
        $histories = LokasiHistory::with('brankas')
            ->when($brankas, fn($q) => $q->where('lokasi_brankas_id', $brankas->id))
            ->orderByDesc('recorded_at')
            ->limit(50)
            ->get();

        return view('lokasi.brankas', compact('brankases', 'brankas', 'histories'));
    }
}
