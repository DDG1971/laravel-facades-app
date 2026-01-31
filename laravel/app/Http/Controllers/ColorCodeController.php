<?php

namespace App\Http\Controllers;

use App\Models\ColorCatalog;
use App\Models\ColorCode;
use Illuminate\Http\Request;

class ColorCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $codes = ColorCode::with('colorCatalog')->get();
        return view('color_codes.index', compact('codes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $catalogs = ColorCatalog::all();

        return view('color_codes.create', compact('catalogs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:color_codes,code',
            'color_catalog_id' => 'required|exists:color_catalogs,id',
        ]);

        ColorCode::create($validated);

        return redirect()->route('color_codes.index')
            ->with('success', 'Код цвета добавлен');
    }

    /**
     * Display the specified resource.
     */
    public function show(ColorCode $colorCode)
    {
        $colorCode->load('colorCatalog');

        return view('color_codes.show', compact('colorCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ColorCode $colorCode)
    {
        $catalogs = ColorCatalog::all();

        return view('color_codes.edit', compact('colorCode','catalogs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ColorCode $colorCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:color_codes,code,' . $colorCode->id,
            'color_catalog_id' => 'required|exists:color_catalogs,id',
        ]);

        $colorCode->update($validated);

        return redirect()->route('color_codes.index')
            ->with('success', 'Код цвета обновлён');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ColorCode $colorCode)
    {
        $colorCode->delete();

        return redirect()->route('color_codes.index')
            ->with('success', 'Код цвета удалён');
    }
}
