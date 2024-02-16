<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf;


class CajaController extends Controller
{
    public function index()
    {
        return view('caja.index');   
    }
}
