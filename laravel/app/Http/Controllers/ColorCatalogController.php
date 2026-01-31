<?php

namespace App\Http\Controllers;

use App\Models\ColorCatalog;
use Illuminate\Http\Request;

class ColorCatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $catalogs = ColorCatalog::withCount('colorCodes')->get();

        return view('color_catalogs.index', compact('catalogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('color_catalogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255|unique:color_catalogs,name_en',
        ]);

        ColorCatalog::create($validated); return redirect()->route('color_catalogs.index')
        ->with('success', 'Каталог добавлен');
    }

    /**
     * Display the specified resource.
     */
    public function show(ColorCatalog $colorCatalog)
    {
        $colorCatalog->load('colorCodes');

        return view('color_catalogs.show', compact('colorCatalog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ColorCatalog $colorCatalog)
    {
        return view('color_catalogs.edit', compact('colorCatalog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ColorCatalog $colorCatalog)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255|unique:color_catalogs,name_en,' . $colorCatalog->id,
            ]);

        $colorCatalog->update($validated);

        return redirect()->route('color_catalogs.index')
                ->with('success', 'Каталог обновлён');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ColorCatalog $colorCatalog)
    {
        $colorCatalog->delete();

        return redirect()->route('color_catalogs.index')
            ->with('success', 'Каталог удалён');
    }
}
