<?php

namespace App\Http\Controllers;

use App\Models\Thickness;
use Illuminate\Http\Request;

class ThicknessController extends Controller
{
    public function index()
    {
        $thicknesses = Thickness::orderByRaw("
         CASE value
          WHEN 19 THEN 1
           WHEN 22 THEN 2
            WHEN 16 THEN 3
             WHEN 38 THEN 4
              ELSE 5
               END
                ")
            ->orderBy('value')->get();

        return view('thicknesses.index', compact('thicknesses'));
    }
    //  форма создания
     public function create()
     {
         return view('thicknesses.create');
     }
     //  сохранение новой записи
     public function store(Request $request)
     {
         $request->validate([
             'value' => 'required|integer|min:1',
             'label' => 'nullable|string|max:50',
             'price' => 'nullable|numeric|min:0',
         ]);

         Thickness::create($request->only('value', 'label', 'price'));

         return redirect()->route('thicknesses.index')
             ->with('success', 'Толщина добавлена!');
     }
     //  форма редактирования
     public function edit(Thickness $thickness)
     {
         return view('thicknesses.edit', compact('thickness'));
     }
     //  обновление записи
    public function update(Request $request, Thickness $thickness)
    {
        $request->validate([
            'value' => 'required|integer|min:1',
            'label' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            ]);
        $thickness->update($request->only('value', 'label', 'price'));

        return redirect()->route('thicknesses.index')
            ->with('success', 'Толщина обновлена!');
    }
    //  удаление
     public function destroy(Thickness $thickness)
     {
         $thickness->delete();

         return redirect()->route('thicknesses.index')
             ->with('success', 'Толщина удалена!');
     }
}
