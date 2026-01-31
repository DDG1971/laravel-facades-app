<?php

namespace App\Http\Controllers;

use App\Models\FacadeType;
use Illuminate\Http\Request;

class FacadeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facadeTypes = FacadeType::all();

        return view('facade-Types.index', compact('facadeTypes'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('facade-Types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:255|unique:facade_types,name_en',
            'name_ru' => 'required|string|max:255|unique:facade_types,name_ru',
            'pricing_mode' => 'required|in:inherit,set_base,percent_add,none',
            'pricing_value' => 'numeric|min:0',
            'unit_mode' => 'required|in:inherit,piece,m2,lm',
        ]);

        FacadeType::create($validatedData);

        return redirect()->route('facade-types.index')
            ->with('success', 'Фасад добавлен');
    }

    /**
     * Display the specified resource.
     */
    public function show(FacadeType $facadeType)
    {
        return view('facade-Types.show', compact('facadeType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FacadeType $facadeType)
    {
        return view('facade-Types.edit', compact('facadeType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FacadeType $facadeType)
    {
        $validated  = $request->validate([
            'name_en' => 'required|string|max:255|unique:facade_types,name_en,' . $facadeType->id,
            'name_ru' => 'required|string|max:255|unique:facade_types,name_ru,' . $facadeType->id,
            'pricing_mode' => 'required|in:inherit,set_base,percent_add,none',
            'pricing_value' => 'numeric|min:0',
            'unit_mode' => 'required|in:inherit,piece,m2,lm',
        ]);

        $facadeType->update($validated);

        return redirect()->route('facade-types.index')
            ->with('success', 'Тип фасада обновлён');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FacadeType $facadeType)
    {
        return redirect()->route('facade-types.index')
            ->with('success', 'Тип фасада удалён');
    }
}
