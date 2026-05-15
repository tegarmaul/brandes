<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LokasiBrankas;

class DashboardController extends Controller
{
    public function index()
    {
        $brankas = LokasiBrankas::first();
        return view('user.dashboard', compact('brankas'));
    }
}
