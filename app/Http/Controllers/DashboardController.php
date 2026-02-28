<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /* ── Dashboard Admin ── */
    public function admin()
    {
        return view('dashboard.admin');
    }

    /* ── Dashboard User ── */
    public function user()
    {
        return view('dashboard.user');
    }
}