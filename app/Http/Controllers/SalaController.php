<?php
namespace App\Http\Controllers;

use App\Events\PartidaIniciada;
use App\Events\ProgresoSalaActualizado;
use App\Events\ReunionIniciada;
use App\Models\Jugador;
use App\Models\Lugar;
use App\Models\PuntoInteres;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaController extends Controller
{
    public function VisualizarSala()
    {
        $sala = Sala::where('estado', 'activa')->get();
        return view('impostor.sala', compact('sala'));
    }
    public function entrar(Request $request)
    {
        // Obtener el jugador desde la cookie
        $idjugador = $request->cookie('jugador_id');

        $jugador = Jugador::find($idjugador);

        // Comprobar que existe el jugador
        if (! $jugador) {
            return redirect()->route('perfil');
        }

        // Obtener la sala
        $sala = Sala::find($request->idsala);

        if (! $sala || $sala->estado != 'activa') {
            return redirect()->back()->with('error', 'La sala no está disponible.');
        }
        session(['sala_id' => $sala->id]);
        // Registrar jugador en la sala
        DB::table('jugador_sala')->updateOrInsert(
            [
                'idjugador' => $jugador->id,
                'idsala'    => $sala->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return redirect()->route('sala.espera');

    }

    public function mostrarRol(Request $request)
    {
        $idjugador = $request->cookie('jugador_id');
        $idsala    = session('sala_id');

        $sala = Sala::find($idsala);

        if (! $sala || $sala->estado != 'en_curso') {
            return redirect()->route('sala.espera');
        }

        $participante = DB::table('jugador_sala')
            ->where('idjugador', $idjugador)
            ->where('idsala', $idsala)
            ->first();

        if (! $participante || ! $participante->rol) {
            return redirect()->route('sala.espera');
        }

        return view('impostor.rol', [
            'rol' => $participante->rol,
        ]);
    }

    public function iniciar(Request $request)
    {
        $idjugador = $request->cookie('jugador_id');
        $idsala    = session('sala_id');

        if (! $idjugador || ! $idsala) {
            return redirect()->route('sala');
        }

        $resultado = DB::transaction(function () use ($idjugador, $idsala) {

            $sala = Sala::where('id', $idsala)
                ->lockForUpdate()
                ->first();

            if (! $sala) {
                return 'no_existe';
            }

            // Verificar administrador
            $admin = Jugador::find($idjugador);

            if (! $admin || ! $admin->admin) {
                return 'sin_permiso';
            }

            // Verificar pertenencia a la sala
            $pertenece = DB::table('jugador_sala')
                ->where('idjugador', $admin->id)
                ->where('idsala', $sala->id)
                ->exists();

            if (! $pertenece) {
                return 'sin_permiso';
            }

            if ($sala->estado != 'activa') {
                return 'ya_iniciada';
            }

            // Obtener jugadores
            $jugadores = DB::table('jugador_sala')
                ->where('idsala', $sala->id)
                ->get();

            $cantidad         = $jugadores->count();
            $numeroImpostores = (int) $sala->numero_impostores;

            if ($numeroImpostores < 1 || $numeroImpostores >= $cantidad) {
                return 'jugadores_insuficientes';
            }

            // Obtener puntos del lugar seleccionado
            $puntosDisponibles = PuntoInteres::where('lugar_id', $sala->lugar)
                ->get();

            if ($puntosDisponibles->count() < 3) {
                return 'puntos_insuficientes';
            }

            // Elegir impostores aleatoriamente
            $impostores = $jugadores
                ->pluck('idjugador')
                ->shuffle()
                ->take($numeroImpostores);

            // Asignar roles y puntos
            foreach ($jugadores as $jugador) {

                $rol = $impostores->contains($jugador->idjugador)
                    ? 'impostor'
                    : 'tripulante';

                DB::table('jugador_sala')
                    ->where('idsala', $sala->id)
                    ->where('idjugador', $jugador->idjugador)
                    ->update([
                        'rol'        => $rol,
                        'updated_at' => now(),
                    ]);

                // Identificador de la participación
                $jugadorSala = DB::table('jugador_sala')
                    ->where('idsala', $sala->id)
                    ->where('idjugador', $jugador->idjugador)
                    ->first();

                // Seleccionar 3 puntos aleatorios para este jugador
                $puntosJugador = $puntosDisponibles
                    ->shuffle()
                    ->take(3);

                foreach ($puntosJugador as $punto) {

                    DB::table('jugador_sala_puntos')->insert([
                        'jugador_sala_id'  => $jugadorSala->id,
                        'punto_interes_id' => $punto->id,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }
            }

            // Cambiar estado
            $sala->estado = 'en_curso';
            $sala->save();

            return 'iniciada';
        });

        if ($resultado == 'sin_permiso') {
            abort(403, 'No tienes permiso para iniciar esta partida.');
        }

        if ($resultado == 'jugadores_insuficientes') {
            return redirect()->back()
                ->with('error', 'No hay suficientes jugadores para iniciar la partida.');
        }

        if ($resultado == 'puntos_insuficientes') {
            return redirect()->back()
                ->with('error', 'El lugar seleccionado debe tener al menos 3 puntos de interés.');
        }

        if ($resultado == 'ya_iniciada') {
            return redirect()->route('rol');
        }

        if ($resultado != 'iniciada') {
            return redirect()->route('sala');
        }

        event(new PartidaIniciada($idsala));

        return redirect()->route('rol');
    }

    public function espera(Request $request)
    {
        $idjugador = $request->cookie('jugador_id');
        $idsala    = session('sala_id');

        $jugador = Jugador::find($idjugador);
        $sala    = Sala::find($idsala);

        if (! $jugador || ! $sala) {
            return redirect()->route('sala');
        }

        // Consultar los jugadores que están en la sala
        $jugadores = DB::table('jugador_sala')
            ->join('jugadors', 'jugador_sala.idjugador', '=', 'jugadors.id')
            ->where('jugador_sala.idsala', $sala->id)
            ->select('jugadors.nombre', 'jugadors.admin')
            ->get();

        return view('impostor.espera', compact('jugador', 'sala', 'jugadores'));
    }
    public function tareas(Request $request)
    {
        $idjugador = $request->cookie('jugador_id');
        $idsala    = session('sala_id');

        // Buscar la participación del jugador
        $jugadorSala = DB::table('jugador_sala')
            ->where('idjugador', $idjugador)
            ->where('idsala', $idsala)
            ->first();

        if (! $jugadorSala) {
            return redirect()->route('sala');
        }

        // Obtener la sala y su lugar
        $sala = Sala::find($idsala);

        if (! $sala || $sala->estado != 'en_curso') {
            return redirect()->route('sala');
        }

        $lugar = Lugar::find($sala->lugar);

        if (! $lugar) {
            return redirect()->route('sala');
        }

        // Obtener únicamente los puntos asignados a este jugador
        $puntos = DB::table('jugador_sala_puntos')
            ->join(
                'punto_interes',
                'jugador_sala_puntos.punto_interes_id',
                '=',
                'punto_interes.id'
            )
            ->where('jugador_sala_puntos.jugador_sala_id', $jugadorSala->id)
            ->select(
                'punto_interes.*',
                'jugador_sala_puntos.completado',
                'jugador_sala_puntos.foto'
            )
            ->get();
        $total = DB::table('jugador_sala_puntos')
            ->join('jugador_sala', 'jugador_sala_puntos.jugador_sala_id', '=', 'jugador_sala.id')
            ->where('jugador_sala.idsala', $idsala)
            ->count();

        $completadas = DB::table('jugador_sala_puntos')
            ->join('jugador_sala', 'jugador_sala_puntos.jugador_sala_id', '=', 'jugador_sala.id')
            ->where('jugador_sala.idsala', $idsala)
            ->where('jugador_sala_puntos.completado', true)
            ->count();

        $progreso = $total > 0
            ? round(($completadas / $total) * 100)
            : 0;
        $estado = $jugadorSala->estado;
        return view('impostor.tareas', compact(
            'lugar',
            'puntos',
            'progreso',
            'completadas',
            'total',
            'estado'
        ));
    }

    public function completarMision(Request $request)
    {
        $request->validate([
            'punto_interes_id' => 'required|integer',
            'foto'             => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $idjugador = $request->cookie('jugador_id');
        $idsala    = session('sala_id');

        if (! $idjugador || ! $idsala) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Sesión no válida.',
            ], 401);
        }

        // Buscar la participación del jugador
        $jugadorSala = DB::table('jugador_sala')
            ->where('idjugador', $idjugador)
            ->where('idsala', $idsala)
            ->first();

        if (! $jugadorSala) {
            return response()->json([
                'success' => false,
                'mensaje' => 'No perteneces a esta sala.',
            ], 403);
        }

        // Comprobar que la sala esté en curso
        $sala = Sala::find($idsala);

        if (! $sala || $sala->estado !== 'en_curso') {
            return response()->json([
                'success' => false,
                'mensaje' => 'La partida no está activa.',
            ], 403);
        }

        // Comprobar que el punto esté asignado a este jugador
        $mision = DB::table('jugador_sala_puntos')
            ->where('jugador_sala_id', $jugadorSala->id)
            ->where('punto_interes_id', $request->punto_interes_id)
            ->first();

        if (! $mision) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Esta misión no está asignada a tu jugador.',
            ], 403);
        }

        // Evitar completar dos veces la misma misión
        if ($mision->completado) {
            return response()->json([
                'success' => true,
                'id'      => (int) $request->punto_interes_id,
                'mensaje' => 'Esta misión ya estaba completada.',
            ]);
        }

        // Guardar fotografía
        $rutaFoto = $request->file('foto')->store('evidencias', 'public');

        // Actualizar misión
        DB::table('jugador_sala_puntos')
            ->where('id', $mision->id)
            ->where('completado', false)
            ->update([
                'completado'    => true,
                'foto'          => $rutaFoto,
                'completado_at' => now(),
                'updated_at'    => now(),
            ]);

        // Calcular progreso global
        $total = DB::table('jugador_sala_puntos')
            ->join('jugador_sala', 'jugador_sala_puntos.jugador_sala_id', '=', 'jugador_sala.id')
            ->where('jugador_sala.idsala', $idsala)
            ->count();

        $completadas = DB::table('jugador_sala_puntos')
            ->join('jugador_sala', 'jugador_sala_puntos.jugador_sala_id', '=', 'jugador_sala.id')
            ->where('jugador_sala.idsala', $idsala)
            ->where('jugador_sala_puntos.completado', true)
            ->count();

        $progreso = $total > 0
            ? round(($completadas / $total) * 100)
            : 0;

        // Finalizar partida si todas las tareas están completadas
        $finalizada = $total > 0 && $completadas >= $total;

        if ($finalizada) {
            $sala->estado = 'finalizada';
            $sala->save();
        }

        // Notificar a todos los jugadores mediante Reverb
        event(new ProgresoSalaActualizado(
            $idsala,
            $progreso,
            $completadas,
            $total,
            $finalizada
        ));

        return response()->json([
            'success'     => true,
            'id'          => (int) $request->punto_interes_id,
            'progreso'    => $progreso,
            'completadas' => $completadas,
            'total'       => $total,
            'finalizada'  => $finalizada,
            'mensaje'     => '¡Misión completada!',
        ]);
    }
   
public function heMuerto(Request $request)
{
    $idjugador = $request->cookie('jugador_id');
    $idsala = session('sala_id');

    if (!$idjugador || !$idsala) {
        return response()->json([
            'success' => false,
            'mensaje' => 'Sesión no válida.',
        ], 401);
    }

    $resultado = DB::transaction(function () use ($idjugador, $idsala) {

        // Bloquear la sala para evitar cálculos simultáneos
        $sala = DB::table('salas')
            ->where('id', $idsala)
            ->lockForUpdate()
            ->first();

        if (!$sala || $sala->estado !== 'en_curso') {
            return [
                'error' => 'La partida no está activa.',
                'status' => 403,
            ];
        }

        // Buscar y bloquear la participación del jugador
        $jugadorSala = DB::table('jugador_sala')
            ->where('idjugador', $idjugador)
            ->where('idsala', $idsala)
            ->lockForUpdate()
            ->first();

        if (!$jugadorSala) {
            return [
                'error' => 'No perteneces a esta sala.',
                'status' => 403,
            ];
        }

        if ($jugadorSala->estado === 'muerto') {
            return [
                'success' => true,
                'mensaje' => 'Ya estás muerto.',
                'ganador' => null,
            ];
        }

        // Marcar al jugador como muerto
        DB::table('jugador_sala')
            ->where('id', $jugadorSala->id)
            ->update([
                'estado' => 'muerto',
                'updated_at' => now(),
            ]);

        // Contar impostores vivos
        $impostoresVivos = DB::table('jugador_sala')
            ->where('idsala', $idsala)
            ->where('estado', 'vivo')
            ->where('rol', 'impostor')
            ->count();

        // Contar tripulantes vivos
        $tripulantesVivos = DB::table('jugador_sala')
            ->where('idsala', $idsala)
            ->where('estado', 'vivo')
            ->where('rol', 'tripulante')
            ->count();

        // Determinar ganador
        $ganador = null;

        if ($impostoresVivos === 0) {
            $ganador = 'tripulantes';
        } elseif ($impostoresVivos >= $tripulantesVivos) {
            $ganador = 'impostores';
        }

        // Si alguien ganó, finalizar la sala
        if ($ganador !== null) {
            DB::table('salas')
                ->where('id', $idsala)
                ->update([
                    'estado' => 'finalizada',
                    'updated_at' => now(),
                ]);
        }

        return [
            'success' => true,
            'mensaje' => 'Tu estado ahora es muerto.',
            'ganador' => $ganador,
            'impostores_vivos' => $impostoresVivos,
            'tripulantes_vivos' => $tripulantesVivos,
        ];
    });

    if (isset($resultado['error'])) {
        return response()->json([
            'success' => false,
            'mensaje' => $resultado['error'],
        ], $resultado['status']);
    }

    // Avisar a todos los jugadores si la partida terminó
    if (!empty($resultado['ganador'])) {
        event(new \App\Events\PartidaFinalizada(
            $idsala,
            $resultado['ganador'],
            $resultado['impostores_vivos'],
            $resultado['tripulantes_vivos']
        ));
    }

    return response()->json($resultado);
}

    
public function encontrarMuerto(Request $request)
{
    $idjugador = $request->cookie('jugador_id');
    $idsala = session('sala_id');

    if (!$idjugador || !$idsala) {
        return response()->json([
            'success' => false,
            'mensaje' => 'Sesión no válida.',
        ], 401);
    }

    $resultado = DB::transaction(function () use ($idjugador, $idsala) {

        // Bloquear la sala para evitar reuniones simultáneas
        $sala = DB::table('salas')
            ->where('id', $idsala)
            ->lockForUpdate()
            ->first();

        if (!$sala || $sala->estado !== 'en_curso') {
            return [
                'error' => 'La partida no está activa.',
                'status' => 403,
            ];
        }

        // Buscar al jugador que reporta
        $jugador = DB::table('jugador_sala')
            ->where('idjugador', $idjugador)
            ->where('idsala', $idsala)
            ->first();

        if (!$jugador) {
            return [
                'error' => 'No perteneces a esta sala.',
                'status' => 403,
            ];
        }

        if ($jugador->estado !== 'vivo') {
            return [
                'error' => 'Los jugadores muertos no pueden reportar.',
                'status' => 403,
            ];
        }

        // Comprobar que exista al menos un jugador muerto
        $hayMuertos = DB::table('jugador_sala')
            ->where('idsala', $idsala)
            ->where('estado', 'muerto')
            ->exists();

        if (!$hayMuertos) {
            return [
                'error' => 'No hay ningún jugador muerto para reportar.',
                'status' => 422,
            ];
        }

        // Comprobar que no exista otra reunión activa
        $reunionActiva = DB::table('reuniones')
            ->where('idsala', $idsala)
            ->whereIn('estado', [
                'cuenta_regresiva',
                'votacion',
                'hablando',
            ])
            ->exists();

        if ($reunionActiva) {
            return [
                'error' => 'Ya existe una reunión en curso.',
                'status' => 409,
            ];
        }

        // Tiempos de la reunión
        $ahora = now();

        $reunionId = DB::table('reuniones')->insertGetId([
            'idsala' => $idsala,
            'convocada_por' => $jugador->id,
            'tipo' => 'emergencia',
            'estado' => 'cuenta_regresiva',

            'iniciada_at' => $ahora,
            'votacion_inicia_at' => $ahora->copy()->addSeconds(30),
            'votacion_finaliza_at' => $ahora->copy()->addSeconds(90),

            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        return [
            'reunion_id' => $reunionId,
        ];
    });

    if (isset($resultado['error'])) {
        return response()->json([
            'success' => false,
            'mensaje' => $resultado['error'],
        ], $resultado['status']);
    }

    $reunion = DB::table('reuniones')
        ->where('id', $resultado['reunion_id'])
        ->first();

    // Avisar a todos los jugadores mediante Reverb
    event(new ReunionIniciada($idsala, $reunion));

    return response()->json([
        'success' => true,
        'reunion_id' => $reunion->id,
        'mensaje' => 'Reunión de emergencia iniciada.',
        'inicio' => $reunion->iniciada_at,
        'votacion_inicia' => $reunion->votacion_inicia_at,
        'votacion_finaliza' => $reunion->votacion_finaliza_at,
    ]);
}


    public function verReunion($id)
    {
        $idsala    = session('sala_id');
        $idjugador = request()->cookie('jugador_id');

        if (! $idsala || ! $idjugador) {
            abort(403, 'Sesión no válida.');
        }

        $jugador = DB::table('jugador_sala')
            ->where('idjugador', $idjugador)
            ->where('idsala', $idsala)
            ->first();

        if (! $jugador) {
            abort(403, 'No perteneces a esta sala.');
        }

        $reunion = DB::table('reuniones')
            ->where('id', $id)
            ->where('idsala', $idsala)
            ->first();

        if (! $reunion) {
            abort(404, 'Reunión no encontrada.');
        }
        $jugadores = DB::table('jugador_sala')
            ->join('jugadors', 'jugador_sala.idjugador', '=', 'jugadors.id')
            ->where('jugador_sala.idsala', $idsala)
            ->select(
                'jugador_sala.id',
                'jugador_sala.estado',
                'jugadors.nombre'
            )
            ->orderBy('jugador_sala.id')
            ->get();

        return view('impostor.reunion', compact(
            'reunion',
            'jugador',
            'jugadores'
        ));
        // return view('impostor.reunion', compact('reunion', 'jugador'));
    }

    public function votar(Request $request, $id)
    {
        $request->validate([
            'votado_jugador_sala_id' => 'required|integer',
        ]);

        $idjugador = $request->cookie('jugador_id');
        $idsala    = session('sala_id');

        if (! $idjugador || ! $idsala) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Sesión no válida.',
            ], 401);
        }

        $resultado = DB::transaction(function () use (
            $id,
            $idjugador,
            $idsala,
            $request
        ) {
            $reunion = DB::table('reuniones')
                ->where('id', $id)
                ->where('idsala', $idsala)
                ->lockForUpdate()
                ->first();

            if (! $reunion) {
                return ['error' => 'Reunión no encontrada.', 'status' => 404];
            }

            if ($reunion->tipo !== 'emergencia') {
                return ['error' => 'Esta reunión no tiene votación.', 'status' => 403];
            }

            if (in_array($reunion->estado, ['finalizada'])) {
                return ['error' => 'La votación ya terminó.', 'status' => 409];
            }

            $ahora = now();

            if ($ahora->lt(\Carbon\Carbon::parse($reunion->votacion_inicia_at))) {
                return ['error' => 'La votación todavía no ha comenzado.', 'status' => 403];
            }

            if ($ahora->gte(\Carbon\Carbon::parse($reunion->votacion_finaliza_at))) {
                return ['error' => 'El tiempo para votar terminó.', 'status' => 409];
            }

            // Buscar al jugador que está votando
            $votante = DB::table('jugador_sala')
                ->where('idjugador', $idjugador)
                ->where('idsala', $idsala)
                ->first();

            if (! $votante) {
                return ['error' => 'No perteneces a esta sala.', 'status' => 403];
            }

            if ($votante->estado !== 'vivo') {
                return ['error' => 'Los jugadores muertos no pueden votar.', 'status' => 403];
            }

            // Comprobar que el jugador elegido esté vivo y pertenezca a la sala
            $elegido = DB::table('jugador_sala')
                ->where('id', $request->votado_jugador_sala_id)
                ->where('idsala', $idsala)
                ->first();

            if (! $elegido || $elegido->estado !== 'vivo') {
                return ['error' => 'El jugador seleccionado no está disponible.', 'status' => 422];
            }

            // Comprobar que no haya votado anteriormente
            $yaVoto = DB::table('votos')
                ->where('reunion_id', $id)
                ->where('jugador_sala_id', $votante->id)
                ->exists();

            if ($yaVoto) {
                return ['error' => 'Ya registraste tu voto.', 'status' => 409];
            }

            // Cambiar el estado a votación si todavía estaba en cuenta regresiva
            DB::table('reuniones')
                ->where('id', $id)
                ->update([
                    'estado'     => 'votacion',
                    'updated_at' => now(),
                ]);

            // Guardar voto
            DB::table('votos')->insert([
                'reunion_id'             => $id,
                'jugador_sala_id'        => $votante->id,
                'votado_jugador_sala_id' => $elegido->id,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            return ['success' => true];
        });

        if (isset($resultado['error'])) {
            return response()->json([
                'success' => false,
                'mensaje' => $resultado['error'],
            ], $resultado['status']);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Tu voto ha sido registrado.',
        ]);
    }

    public function cerrarVotacion($id)
    {
        $idjugador = request()->cookie('jugador_id');
        $idsala    = session('sala_id');

        if (! $idjugador || ! $idsala) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Sesión no válida.',
            ], 401);
        }

        $resultado = DB::transaction(function () use ($id, $idsala) {

            $reunion = DB::table('reuniones')
                ->where('id', $id)
                ->where('idsala', $idsala)
                ->lockForUpdate()
                ->first();

            if (! $reunion) {
                return ['error' => 'Reunión no encontrada.', 'status' => 404];
            }

            // Evitar cerrar dos veces la misma votación
            if ($reunion->estado === 'finalizada') {
                return [
                    'ya_finalizada'             => true,
                    'resultado'                 => $reunion->resultado,
                    'expulsado_jugador_sala_id' => $reunion->expulsado_jugador_sala_id,
                ];
            }

            if (now()->lt(\Carbon\Carbon::parse($reunion->votacion_finaliza_at))) {
                return [
                    'error'  => 'La votación todavía está abierta.',
                    'status' => 403,
                ];
            }

            // Contar votos
            $conteo = DB::table('votos')
                ->select('votado_jugador_sala_id', DB::raw('COUNT(*) as cantidad'))
                ->where('reunion_id', $id)
                ->groupBy('votado_jugador_sala_id')
                ->orderByDesc('cantidad')
                ->get();

            $expulsado       = null;
            $estadoResultado = 'sin_votos';

            if ($conteo->isNotEmpty()) {
                $mayor = $conteo->first()->cantidad;

                $empatados = $conteo->where('cantidad', $mayor);

                if ($empatados->count() === 1) {

                    $expulsado = $empatados->first()->votado_jugador_sala_id;

                    $jugadorExpulsado = DB::table('jugador_sala')
                        ->where('id', $expulsado)
                        ->where('idsala', $idsala)
                        ->first();

                    if ($jugadorExpulsado && $jugadorExpulsado->estado === 'vivo') {

                        DB::table('jugador_sala')
                            ->where('id', $expulsado)
                            ->update([
                                'estado'     => 'muerto',
                                'updated_at' => now(),
                            ]);

                        $estadoResultado = $jugadorExpulsado->rol === 'impostor'
                            ? 'impostor'
                            : 'no_impostor';

                    } else {
                        $expulsado       = null;
                        $estadoResultado = 'empate';
                    }

                } else {
                    $estadoResultado = 'empate';
                }
            }

            DB::table('reuniones')
                ->where('id', $id)
                ->update([
                    'estado'                    => 'finalizada',
                    'expulsado_jugador_sala_id' => $expulsado,
                    'resultado'                 => $estadoResultado,
                    'finalizada_at'             => now(),
                    'updated_at'                => now(),
                ]);

            return [
                'ya_finalizada'             => false,
                'resultado'                 => $estadoResultado,
                'expulsado_jugador_sala_id' => $expulsado,
            ];
        });

        if (isset($resultado['error'])) {
            return response()->json([
                'success' => false,
                'mensaje' => $resultado['error'],
            ], $resultado['status']);
        }

        return response()->json([
            'success'                   => true,
            'mensaje'                   => 'Votación finalizada.',
            'ya_finalizada'             => $resultado['ya_finalizada'],
            'resultado'                 => $resultado['resultado'],
            'expulsado_jugador_sala_id' => $resultado['expulsado_jugador_sala_id'],
        ]);
    }

}
