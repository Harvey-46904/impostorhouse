<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jugador;
use Illuminate\Support\Facades\Cookie;

class JugadorController extends Controller
{
   public function perfil()
{
    $id = request()->cookie('jugador_id');

    $jugador = Jugador::find($id);

    return view('impostor.perfil', compact('jugador'));
}
   
public function guardar(Request $request)
{
    $request->validate([
        'color' => 'required|in:amarillo,azul,morado,naranja,negro,rojo,rosa,verde',
    ]);

    $id = $request->cookie('jugador_id');

    $jugador = Jugador::find($id);

    if (!$jugador) {

        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $jugador = Jugador::create([
            'nombre' => $request->nombre,
        ]);

        Cookie::queue(cookie('jugador_id', $jugador->id, 525600));
    }

    session(['color_jugador' => $request->color]);

    return redirect()->route('sala');
}

}