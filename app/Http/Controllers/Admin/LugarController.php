<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lugar;
use App\Models\PuntoInteres;

class LugarController extends Controller
{
    public function index()
    {
        $lugares = Lugar::with('puntos')->get();

        return view('admin.lugares.index', compact('lugares'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'puntos' => 'required|json',
        ]);

        $puntos = json_decode($request->puntos, true);

        if (!is_array($puntos) || count($puntos) === 0) {
            return back()->withErrors([
                'puntos' => 'Debes dibujar al menos un punto de interés.'
            ]);
        }

        foreach ($puntos as $punto) {
            if (
                !isset($punto['nombre'], $punto['x'], $punto['y'],
                    $punto['ancho'], $punto['alto']) ||
                !is_numeric($punto['x']) ||
                !is_numeric($punto['y']) ||
                !is_numeric($punto['ancho']) ||
                !is_numeric($punto['alto']) ||
                $punto['x'] < 0 || $punto['y'] < 0 ||
                $punto['ancho'] <= 0 || $punto['alto'] <= 0 ||
                $punto['x'] + $punto['ancho'] > 100 ||
                $punto['y'] + $punto['alto'] > 100
            ) {
                return back()->withErrors([
                    'puntos' => 'Hay coordenadas inválidas en los puntos.'
                ]);
            }
        }

        DB::transaction(function () use ($request, $puntos) {

            $ruta = $request->file('imagen')
                ->store('lugares', 'public');

            $lugar = Lugar::create([
                'nombre' => $request->nombre,
                'imagen' => $ruta,
            ]);

            foreach ($puntos as $punto) {
                $lugar->puntos()->create([
                    'nombre' => $punto['nombre'],
                    'x' => $punto['x'],
                    'y' => $punto['y'],
                    'ancho' => $punto['ancho'],
                    'alto' => $punto['alto'],
                ]);
            }
        });

        return redirect()
            ->route('voyager.lugares.index')
            ->with('success', 'Lugar guardado correctamente.');
    }
}
