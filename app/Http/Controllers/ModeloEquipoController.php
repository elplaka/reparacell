<?php

namespace App\Http\Controllers;

use App\Models\ModeloEquipo;
use Illuminate\Http\Request;

class ModeloEquipoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('equipos.modelos');
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
    public function show(ModeloEquipo $modeloEquipo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ModeloEquipo $modeloEquipo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModeloEquipo $modeloEquipo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModeloEquipo $modeloEquipo)
    {
        //
    }
}
