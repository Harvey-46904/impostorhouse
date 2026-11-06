
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reunión de emergencia</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #111629;
            color: white;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .reunion-container {
            width: 100%;
            max-width: 500px;
            text-align: center;
            background: #171b2e;
            border: 1px solid #303653;
            border-radius: 20px;
            padding: 30px 25px;
        }

        .reunion-titulo {
            color: #f1bd64;
            font-weight: 900;
            margin: 20px 0 10px;
        }

        .estado-reunion {
            color: #aab7d2;
        }

        .contador {
            background: #252f49;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
        }

        .contador-numero {
            font-size: 65px;
            font-weight: 900;
            color: white;
            font-variant-numeric: tabular-nums;
        }

        .jugador-voto {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 15px;
            margin-bottom: 10px;
            background: #252f49;
            color: white;
            border: 2px solid #405b91;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            transition: .2s;
        }

        .jugador-voto:hover:not(:disabled) {
            background: #405b91;
        }

        .jugador-voto.seleccionado {
            background: #315e45;
            border-color: #48c77a;
        }

        .jugador-voto:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .jugador-voto small {
            color: #F18A9F;
        }

        #listaJugadores {
            margin-top: 20px;
        }

        .resultado-jugador {
            background: #252f49;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 10px;
            text-align: left;
        }

        .oculto {
            display: none !important;
        }

        #mensajeRevelacion {
            margin-top: 25px;
            font-size: 22px;
            color: #f1bd64;
        }

        #contadorRevelacion {
            color: #aab7d2;
        }
    </style>
</head>

<body>

<div class="reunion-container">

    <!-- ETAPA 1: PREPARACIÓN -->
    <div id="panelPreparacion">

        <div style="font-size: 45px;">🚨</div>

        <h1 class="reunion-titulo">¡CUERPO REPORTADO!</h1>

        <p class="estado-reunion">
            Los jugadores se están reuniendo...
        </p>

        <div class="contador">

            <small>TIEMPO PARA REUNIRSE</small>

            <div class="contador-numero" id="contadorPreparacion">
                30
            </div>

            <small>Prepárate para votar</small>

        </div>

    </div>


    <!-- ETAPA 2: VOTACIÓN -->
    <div id="panelVotacion" class="oculto">

        <div style="font-size: 40px;">🗳️</div>

        <h1 class="reunion-titulo">
            ¿QUIÉN ES EL IMPOSTOR?
        </h1>

        <div class="contador">

            <small>TIEMPO PARA VOTAR</small>

            <div class="contador-numero" id="contadorVotacion">
                60
            </div>

        </div>

        <p id="mensajeVoto">
            Selecciona al jugador que sospechas.
        </p>

        <div id="listaJugadores">

            @foreach($jugadores as $j)

                <button
                    type="button"
                    class="jugador-voto"
                    data-id="{{ $j->id }}"
                    @disabled($j->estado === 'muerto' || $jugador->estado === 'muerto')>

                    <span>
                        {{ $j->estado === 'muerto' ? 'XXXX' : $j->nombre }}
                    </span>

                    @if($j->estado === 'muerto')
                        <small>MUERTO</small>
                    @endif

                </button>

            @endforeach

        </div>

    </div>


    <!-- ETAPA 3: RESULTADOS -->
    <div id="panelResultados" class="oculto">

        <div style="font-size: 40px;">⚖️</div>

        <h1 class="reunion-titulo">
            RESULTADO DE VOTACIÓN
        </h1>

        <div id="listaResultados"></div>

        <h3 id="mensajeRevelacion"></h3>

        <p id="contadorRevelacion"></p>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // =====================================
    // DATOS DE LA REUNIÓN
    // =====================================

    const reunionId = @json($reunion->id);

    const inicioVotacion = new Date(
        @json(\Carbon\Carbon::parse($reunion->votacion_inicia_at)->toIso8601String())
    ).getTime();

    const finVotacion = new Date(
        @json(\Carbon\Carbon::parse($reunion->votacion_finaliza_at)->toIso8601String())
    ).getTime();

    const esMuerto = @json($jugador->estado === 'muerto');

    // =====================================
    // ELEMENTOS HTML
    // =====================================

    const panelPreparacion = document.getElementById('panelPreparacion');
    const panelVotacion = document.getElementById('panelVotacion');
    const panelResultados = document.getElementById('panelResultados');

    const contadorPreparacion = document.getElementById('contadorPreparacion');
    const contadorVotacion = document.getElementById('contadorVotacion');

    const mensajeVoto = document.getElementById('mensajeVoto');

    const mensajeRevelacion = document.getElementById('mensajeRevelacion');
    const contadorRevelacion = document.getElementById('contadorRevelacion');

    // =====================================
    // VARIABLES
    // =====================================

    let votoRegistrado = false;
    let cierreEnviado = false;
    let etapaActual = 'preparacion';

    // =====================================
    // ENVIAR VOTO
    // =====================================

    async function enviarVoto(idJugador) {

        if (votoRegistrado || esMuerto || Date.now() >= finVotacion) {
            return;
        }

        if (!confirm('¿Confirmas tu voto? No podrás cambiarlo.')) {
            return;
        }

        try {

            const respuesta = await fetch(
                @json(route('reunion.votar', $reunion->id)),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        votado_jugador_sala_id: idJugador
                    })
                }
            );

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.success) {
                throw new Error(resultado.mensaje || 'No se pudo registrar el voto.');
            }

            votoRegistrado = true;

            document.querySelectorAll('.jugador-voto').forEach(boton => {
                boton.disabled = true;

                if (boton.dataset.id == idJugador) {
                    boton.classList.add('seleccionado');
                }
            });

            mensajeVoto.textContent =
                '¡Voto registrado! Esperando el resultado.';

        } catch (error) {

            alert(error.message);

        }
    }


    // =====================================
    // EVENTOS DE LOS JUGADORES
    // =====================================

    document.querySelectorAll('.jugador-voto').forEach(boton => {

        boton.addEventListener('click', function () {

            enviarVoto(this.dataset.id);

        });

    });


    // =====================================
    // MOSTRAR RESULTADO
    // =====================================

    function mostrarResultado(resultado) {

        panelPreparacion.classList.add('oculto');
        panelVotacion.classList.add('oculto');
        panelResultados.classList.remove('oculto');

        if (resultado === 'sin_votos') {

            mensajeRevelacion.textContent = 'Nadie ha votado.';

        } else if (resultado === 'empate') {

            mensajeRevelacion.textContent = '¡Ha ocurrido un empate!';

        } else if (resultado === 'impostor') {

            mensajeRevelacion.textContent = '¡Han expulsado al impostor!';

        } else if (resultado === 'no_impostor') {

            mensajeRevelacion.textContent = 'El jugador expulsado no era el impostor.';

        } else {

            mensajeRevelacion.textContent = 'La votación ha finalizado.';

        }

        contadorRevelacion.textContent = 'Regresando a la partida...';

        // Esperar 3 segundos antes de redirigir
        setTimeout(function () {

            // Por ahora todos regresan a tareas.
            // Después conectaremos la pantalla de victoria del impostor.
            window.location.href = @json(route('tareas'));

        }, 3000);

    }


    // =====================================
    // CERRAR VOTACIÓN
    // =====================================

    async function cerrarVotacion() {

        if (cierreEnviado) {
            return;
        }

        cierreEnviado = true;

        contadorVotacion.textContent = '00';

        try {

            const respuesta = await fetch(
                @json(route('reunion.cerrarVotacion', $reunion->id)),
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }
            );

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.success) {
                throw new Error(resultado.mensaje || 'No se pudo cerrar la votación.');
            }

            console.log('Votación finalizada:', resultado);

            mostrarResultado(resultado.resultado);

        } catch (error) {

            console.error('Error al cerrar votación:', error);

            cierreEnviado = false;

            // Reintentar si el servidor todavía no permite cerrar
            setTimeout(cerrarVotacion, 1500);

        }
    }


    // =====================================
    // ACTUALIZAR PANTALLA Y CONTADORES
    // =====================================

    function actualizarPantalla() {

        const ahora = Date.now();

        // =================================
        // ETAPA 1: PREPARACIÓN
        // =================================

        if (ahora < inicioVotacion) {

            etapaActual = 'preparacion';

            panelPreparacion.classList.remove('oculto');
            panelVotacion.classList.add('oculto');
            panelResultados.classList.add('oculto');

            const restante = Math.max(
                0,
                Math.ceil((inicioVotacion - ahora) / 1000)
            );

            contadorPreparacion.textContent =
                String(restante).padStart(2, '0');

            return;
        }


        // =================================
        // ETAPA 2: VOTACIÓN
        // =================================

        if (ahora < finVotacion) {

            if (etapaActual !== 'votacion') {

                etapaActual = 'votacion';

                panelPreparacion.classList.add('oculto');
                panelVotacion.classList.remove('oculto');
                panelResultados.classList.add('oculto');

                if (esMuerto) {

                    mensajeVoto.textContent =
                        'Estás muerto. Puedes observar, pero no votar.';

                }

            }

            const restante = Math.max(
                0,
                Math.ceil((finVotacion - ahora) / 1000)
            );

            contadorVotacion.textContent =
                String(restante).padStart(2, '0');

            return;
        }


        // =================================
        // ETAPA 3: CIERRE DE VOTACIÓN
        // =================================

        if (etapaActual !== 'finalizando') {

            etapaActual = 'finalizando';

            contadorVotacion.textContent = '00';

            cerrarVotacion();

        }

    }


    // =====================================
    // INICIAR RELOJ
    // =====================================

    actualizarPantalla();

    setInterval(actualizarPantalla, 200);

});
</script>

</body>
</html>
