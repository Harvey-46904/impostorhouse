<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jugador;
use DB;
use Illuminate\Http\Request;

class ReporteSalaController extends Controller
{
    public function index()
    {
        $lugares = "hola";

        return view('admin.reportes.index', compact('lugares'));
    }

    public function tareasSala($id)
    {
        $sala = DB::table('salas')
            ->where('id', $id)
            ->first();

        if (! $sala) {
            abort(404, 'Sala no encontrada');
        }

        $tareas = DB::table('jugador_sala_puntos')
            ->join('jugador_sala', 'jugador_sala_puntos.jugador_sala_id', '=', 'jugador_sala.id')
            ->join('jugadors', 'jugador_sala.idjugador', '=', 'jugadors.id')
            ->join('punto_interes', 'jugador_sala_puntos.punto_interes_id', '=', 'punto_interes.id')
            ->where('jugador_sala.idsala', $id)
            ->select(
                'jugador_sala_puntos.id as tarea_id',
                'jugador_sala_puntos.completado',
                'jugador_sala_puntos.foto',
                'jugador_sala_puntos.completado_at',
                'jugadors.id as jugador_id',
                'jugadors.nombre as jugador',
                'punto_interes.id as punto_id',
                'punto_interes.nombre as tarea'
            )
            ->orderBy('jugadors.nombre')
            ->get();

        return view('admin.reportes.detalles', compact('sala', 'tareas'));
    }
    public function jugadormuerto(Request $request)
    {
        $request->validate([
            'tagid' => 'required|string|max:255',
        ]);

        $jugador = Jugador::where('tagid', $request->tagid)->first();

        if (! $jugador) {
            return response()->json([
                'success' => false,
                'message' => 'No existe ningún jugador asociado a este TAG.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'jugador' => [
                'id'     => $jugador->id,
                'nombre' => $jugador->nombre,
                'tagid'  => $jugador->tagid,
            ],
        ]);
    }
}
