<?php

namespace App\Http\Controllers;

use App\Models\Drilling;
use Illuminate\Http\Request;

class DrillingController extends Controller
{
    /**
     * Список всех сверловок
     */
    public function index()
    {
        $drillings = Drilling::orderBy('name_ru')->get();
        return view('drillings.index', compact('drillings'));
    }

    /**
     * Форма создания
     */
    public function create()
    {
        return view('drillings.create');
    }

    /**
     * Сохранение новой сверловки
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_en' => 'required|string|max:255|unique:drillings,name_en',
            'price'   => 'nullable|numeric|min:0',
        ]);

        Drilling::create($validated);

        return redirect()->route('drillings.index')
            ->with('success', 'Тип сверления успешно добавлен.');
    }

    /**
     * Просмотр одной сверловки
     */
    public function show(Drilling $drilling)
    {
        return view('drillings.show', compact('drilling'));
    }

    /**
     * Форма редактирования
     */
    public function edit(Drilling $drilling)
    {
        return view('drillings.edit', compact('drilling'));
    }

    /**
     * Обновление данных
     */
    public function update(Request $request, Drilling $drilling)
    {
        $validated = $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_en' => 'required|string|max:255|unique:drillings,name_en,' . $drilling->id,
            'price'   => 'nullable|numeric|min:0',
        ]);

        $drilling->update($validated);

        return redirect()->route('drillings.index')
            ->with('success', 'Данные сверления обновлены.');
    }

    /**
     * Удаление
     */
    public function destroy(Drilling $drilling)
    {
        // Можно добавить проверку: если сверление используется в заказах - не удалять
        $drilling->delete();

        return redirect()->route('drillings.index')
            ->with('success', 'Тип сверления удален.');
    }
}
