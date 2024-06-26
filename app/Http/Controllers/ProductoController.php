<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductoController extends Controller
{
    
    public function index()
    {
        return view('productos.index');
    }

    public function inventario()
    {
        return view('productos.inventario');
    }

    public function reportes()
    {
        return view('productos.reportes');
    }

    public function departamentos()
    {
        return view('productos.departamentos');
    }
}
