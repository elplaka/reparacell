<?php

namespace App\Http\Controllers;

use App\Models\MarcaEquipo;
use Illuminate\Http\Request;

class MarcaEquipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('equipos.marcas');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MarcaEquipo $marcaEquipo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarcaEquipo $marcaEquipo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarcaEquipo $marcaEquipo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarcaEquipo $marcaEquipo)
    {
        //
    }
}
