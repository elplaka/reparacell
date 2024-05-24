<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VentaController extends Controller
{
    
    public function index()
    {
        return view('ventas.index');
    }

    public function creditos()
    {
        return view('ventas.creditos');
    }
}
