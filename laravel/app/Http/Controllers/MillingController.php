<?php

namespace App\Http\Controllers;

use App\Models\Milling;
use Illuminate\Http\Request;

class MillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $millings = Milling::all();

        return view('millings.index', compact('millings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('millings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255|unique:millings,name_en',
            'code' => 'required|string|unique:millings,code',
            'price_retail' => 'nullable|numeric',
            'price_dealer' => 'nullable|numeric',
            'price_private' => 'nullable|numeric',
        ]);

        Milling::create($validated);

        return redirect()->route('millings.index')
            ->with('success', 'Фрезеровка добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(Milling $milling)
    {
        return view('millings.show', compact('milling'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Milling $milling)
    {
        return view('millings.edit', compact('milling'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  Milling $milling)
    {
        $validated = $request->validate([
            'price_retail' => 'nullable|numeric',
            'price_dealer' => 'nullable|numeric',
            'price_private' => 'nullable|numeric',
            ]);
        $milling->update($validated);

        return redirect()->route('millings.index')
            ->with('success', 'Фрезеровка обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
