<?php

namespace App\Http\Controllers;

use App\Models\Kamar;

class LandingController extends Controller
{
    public function index()
    {
        $kamars = Kamar::where('status', 'kosong')->get();
        return view('landing', compact('kamars'));
    }
}