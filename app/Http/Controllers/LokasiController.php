<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        if (!session('user') || session('user.role') !== 'admin') {
            return redirect()->route('login');
        }

        $histories = [
            [
                'waktu'   => '2026-01-31 14:30:45',
                'brankas' => 'Brankas Balai Desa Bengle',
                'lat'     => '6.9',
                'lng'     => '109.147994',
                'getaran' => '0.1 G',
                'status'  => 'Normal',
            ],
            [
                'waktu'   => '2026-01-30 09:15:20',
                'brankas' => 'Brankas Balai Desa Bengle',
                'lat'     => '6.9',
                'lng'     => '109.147994',
                'getaran' => '0.2 G',
                'status'  => 'Normal',
            ],
            [
                'waktu'   => '2026-01-29 16:45:10',
                'brankas' => 'Brankas Balai Desa Bengle',
                'lat'     => '6.9',
                'lng'     => '109.147994',
                'getaran' => '1.8 G',
                'status'  => 'Waspada',
            ],
            [
                'waktu'   => '2026-01-28 11:20:33',
                'brankas' => 'Brankas Balai Desa Bengle',
                'lat'     => '6.9',
                'lng'     => '109.147994',
                'getaran' => '0.1 G',
                'status'  => 'Normal',
            ],
        ];

        return view('lokasi.brankas', compact('histories'));
    }
}