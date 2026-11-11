<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de sala {{ $sala->id }}</title>
</head>
<body>

    <h1>Reporte de sala: {{ $sala->nombre }}</h1>

    @php
        $jugadores = $tareas->groupBy('jugador_id');
    @endphp

    @foreach($jugadores as $jugadorId => $tareasJugador)

        <h2>Jugador: {{ $tareasJugador->first()->jugador }}</h2>

        <table border="1" cellpadding="10" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Tarea</th>
                    <th>Fotografía</th>
                </tr>
            </thead>

            <tbody>
                @foreach($tareasJugador as $tarea)
                    <tr>
                        <td>
                            {{ $tarea->tarea }}

                            <br>

                            <strong>
                                {{ $tarea->completado ? 'Completada' : 'Pendiente' }}
                            </strong>

                            @if($tarea->completado_at)
                                <br>
                                <small>{{ $tarea->completado_at }}</small>
                            @endif
                        </td>

                        <td align="center">
                            @if($tarea->foto)
                                <a href="{{ asset('storage/' . $tarea->foto) }}" target="_blank">
                                    <img
                                        src="{{ asset('storage/' . $tarea->foto) }}"
                                        alt="Evidencia"
                                        width="250"
                                    >
                                </a>
                            @else
                                Sin fotografía
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <br><br>

    @endforeach

</body>
</html>
