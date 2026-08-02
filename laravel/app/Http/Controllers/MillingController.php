<?php

namespace App\Http\Controllers;

use App\Models\Milling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class MillingController extends Controller
{
    public function index()
    {
        $millings = Milling::orderBy('name')->get(); // ← сортировка по алфавиту
        return view('millings.index', compact('millings'));
    }

    public function create()
    {
        return view('millings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255|unique:millings,name_en',
            'code' => 'required|string|unique:millings,code',
            'price_retail' => 'nullable|numeric',
            'price_dealer' => 'nullable|numeric',
            'price_private' => 'nullable|numeric',
            'price_coloring' => 'nullable|numeric',
            'svg_file' => 'nullable|file|mimes:svg,png,jpg,jpeg|max:2048',
        ]);

        // Сохраняем SVG если загружен
        if ($request->hasFile('svg_file')) {
            $validated['svg_path'] = $request->file('svg_file')->store('millings', 'public');
        }

        Milling::create($validated);

        return redirect()->route('millings.index')
            ->with('success', 'Фрезеровка добавлена');
    }

    public function show(Milling $milling)
    {
        return view('millings.show', compact('milling'));
    }

    public function edit(Milling $milling)
    {
        return view('millings.edit', compact('milling'));
    }

    public function update(Request $request, Milling $milling)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255|unique:millings,name_en,' . $milling->id,
            'code' => 'required|string|unique:millings,code,' . $milling->id,
            'price_retail' => 'nullable|numeric',
            'price_dealer' => 'nullable|numeric',
            'price_private' => 'nullable|numeric',
            'price_coloring' => 'nullable|numeric',
            'svg_file' => 'nullable|file|mimes:svg,png,jpg,jpeg|max:2048',
        ]);

        // Если загружен новый SVG — удаляем старый и сохраняем новый
        if ($request->hasFile('svg_file')) {
            if ($milling->svg_path) {
                Storage::disk('public')->delete($milling->svg_path);
            }
            $validated['svg_path'] = $request->file('svg_file')->store('millings', 'public');
        }

        $milling->update($validated);

        return redirect()->route('millings.index')
            ->with('success', 'Фрезеровка полностью обновлена');
    }

    public function destroy(string $id)
    {
        $milling = Milling::findOrFail($id);

        // Удаляем файл чертежа
        if ($milling->svg_path) {
            Storage::disk('public')->delete($milling->svg_path);
        }

        $milling->delete();

        return redirect()->route('millings.index')
            ->with('success', 'Фрезеровка удалена');
    }
}
