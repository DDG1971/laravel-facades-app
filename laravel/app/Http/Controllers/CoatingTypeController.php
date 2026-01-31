<?php

namespace App\Http\Controllers;

use App\Models\CoatingType;
use Illuminate\Http\Request;

class CoatingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coatingTypes = CoatingType::all();

        return view('coating-types.index', compact('coatingTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coating-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:coating_types,name',
            'label' => 'required|string',
           'description' => 'nullable|string',
           'price' => 'required|numeric|min:0',
        ]);

        CoatingType::create($validated);

        return redirect()->route('coating-types.index')
         ->with('success', 'Покрытие добавлено');
    }

    /**
     * Display the specified resource.
     */
    public function show(CoatingType $coatingType)
    {
        return view('coating-types.show', compact('coatingType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CoatingType $coatingType)
    {
        return view('coating-types.edit', compact('coatingType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CoatingType $coatingType)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:coating_types,name,' . $coatingType->id,
            'label' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            ]);
        $coatingType->update($validated);

        return redirect()->route('coating-types.index')
            ->with('success', 'Покрытие обновлено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CoatingType $coatingType)
    {
        $coatingType->delete();

        return redirect()->route('coating-types.index')
            ->with('success', 'Покрытие удалено');
    }
}
