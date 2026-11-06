<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
    <title>Impostor House</title>
</head>

<body>
    <div class="container bg-game vh-100">

        <!-- PROGRESO GLOBAL DE LA SALA -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Progreso de la partida</strong>
                    <strong id="textoProgreso">{{ $progreso }}%</strong>
                </div>

                <div class="progress" style="height: 12px;">
                    <div id="barraProgreso" class="progress-bar bg-success" role="progressbar"
                        style="width: {{ $progreso }}%;" aria-valuenow="{{ $progreso }}" aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>

                <small class="text-muted mt-2 d-block" id="detalleProgreso">
                    {{ $completadas }} de {{ $total }} misiones completadas
                </small>

            </div>
        </div>

        <!-- PANEL DE REUNIONES Y ACCIONES -->
        <div class="panel-acciones mb-4">

            <div class="panel-acciones-titulo">
                <span>ACCIONES DE PARTIDA</span>
                <small>Interactúa con los demás jugadores</small>
            </div>

            <div class="acciones-grid">

              
                @if($estado === 'vivo')
                <!-- HE MUERTO -->
                <button type="button" class="accion-btn accion-muerte" id="btnHeMuerto">
                    <span class="accion-icono">
                        <i class="fas fa-skull"></i>
                    </span>
                    <span class="accion-info">
                        <strong>He muerto</strong>
                        <small>Informar que has muerto</small>
                    </span>
                </button>

                <!-- ENCONTRE UN MUERTO -->
                <button type="button" class="accion-btn accion-reporte" id="btnEncontrado">
                    <span class="accion-icono">
                        <i class="fas fa-bullhorn"></i>
                    </span>
                    <span class="accion-info">
                        <strong>Encontré un muerto</strong>
                        <small>Reportar y convocar votación</small>
                    </span>
                </button>
                @else
                <p> Estas muerto completa misiones y no chismoses no sea 🐸😊 😄 </p>
                @endif
              

            </div>
        </div>
        <div class="row pt-4">
            <div class="col-md-12">
                <p class="titulo">MISIONES </p>
                <p class="subtitulo">Explora la casa, toma fotos de los lugares indicados y completa la misión </p>
            </div>
        </div>

        <div class="row pt-3 justify-content-center align-items-center mx-3 py-2 rounded">
            <div class="col-md-12 text-center">

                <div class="mapa-contenedor">

                    <img src="{{ asset('storage/'.$lugar->imagen) }}" class="imagen-mapa" alt="{{ $lugar->nombre }}">

                    @foreach($puntos as $punto)

                    <img id="marcador-{{ $punto->id }}" src="{{ asset('img/iconografia/ubicacion.png') }}"
                        class="marcador-mapa {{ $punto->completado ? 'marcador-completado' : '' }}" style="
            left: {{ $punto->x + ($punto->ancho / 2) }}%;
            top: {{ $punto->y + ($punto->alto / 2) }}%;
        " alt="Punto de interés">

                    @endforeach

                </div>

            </div>
        </div>


        @foreach($puntos as $index => $punto)

        <div class="row mb-3" id="tarea-{{ $punto->id }}">

            <div class="col-12">

                <div class="row bg-formularios align-items-center mx-3 py-2 rounded tarea-card
            {{ $punto->completado ? 'tarea-completada' : '' }}">

                    <div class="col-2">
                        <img src="{{ asset('img/iconografia/camara.png') }}" alt="Cámara"
                            style="width:50px;height:50px;">
                    </div>

                    <div class="col-6">
                        <div class="row align-items-center">

                            <div class="col-2 titulo">
                                {{ $index + 1 }}
                            </div>

                            <div class="col-10 text-light nombre-tarea">
                                Tomar foto de {{ $punto->nombre }}
                            </div>

                        </div>
                    </div>

                    <div class="col-4 text-end">

                        @if($punto->completado)

                        <span class="badge bg-success estado-tarea">
                            COMPLETADA
                        </span>

                        @else

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#exampleModal" data-id="{{ $punto->id }}" data-punto="{{ $punto->nombre }}">
                            Completar
                        </button>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        @endforeach

    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered bg-game">
            <div class="modal-content bg-game">

                <div class="modal-header">
                    <h5 class="modal-title titulo" id="exampleModalLabel">
                        TOMAR FOTO
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="subtitulo">
                        Coloca la cámara en el lugar indicado.
                    </p>

                    <div class="text-center">

                        <!-- Cámara -->
                        <div class="contenedor-camara">

                            <video id="videoCamara" autoplay playsinline class="w-100"></video>

                            <!-- Cuenta regresiva -->
                            <div id="cuentaRegresiva" class="cuenta-regresiva d-none">
                                3
                            </div>

                            <!-- Fotografía capturada -->
                            <canvas id="canvasFoto" class="w-100 d-none"></canvas>

                        </div>

                        <p id="mensajeCamara" class="texto mt-3">
                            Esperando cámara...
                        </p>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Cerrar
                    </button>

                    <button type="button" class="btn btn-game" id="btnCapturar" disabled>
                        Capturar
                    </button>
                    <button type="button" class="btn btn-success d-none" id="btnEnviarFoto">
                        📷 Enviar fotografía
                    </button>

                </div>

            </div>
        </div>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        let flujoCamara = null;
let puntoActual = null;

const video = document.getElementById('videoCamara');
const canvas = document.getElementById('canvasFoto');
const cuenta = document.getElementById('cuentaRegresiva');
const boton = document.getElementById('btnCapturar');
const btnEnviarFoto = document.getElementById('btnEnviarFoto');
const mensaje = document.getElementById('mensajeCamara');
const modal = document.getElementById('exampleModal');

// SELECCIONAR LA MISIÓN
modal.addEventListener('show.bs.modal', function (event) {

    const botonMision = event.relatedTarget;

    puntoActual = botonMision.dataset.id;

    console.log('Misión seleccionada:', puntoActual);

});

// ACTIVAR CÁMARA
modal.addEventListener('shown.bs.modal', async function () {

    console.log('Modal abierto');

    try {

        mensaje.textContent = 'Solicitando acceso a la cámara...';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('El navegador no permite acceder a la cámara. Verifica HTTPS.');
        }

        flujoCamara = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment'
            },
            audio: false
        });

        video.srcObject = flujoCamara;

        await video.play();

        video.classList.remove('d-none');
        canvas.classList.add('d-none');

        boton.classList.remove('d-none');
        btnEnviarFoto.classList.add('d-none');

        boton.disabled = false;

        mensaje.textContent = 'Cámara lista para tomar la foto';

        console.log('Cámara activada correctamente');

    } catch (error) {

        mensaje.textContent = 'Error: ' + error.message;

        console.error('Error de cámara:', error);

    }

});

// TOMAR FOTOGRAFÍA
boton.addEventListener('click', function () {

    boton.disabled = true;

    let segundos = 3;

    cuenta.textContent = segundos;
    cuenta.classList.remove('d-none');

    let temporizador = setInterval(function () {

        segundos--;

        if (segundos > 0) {

            cuenta.textContent = segundos;

        } else {

            clearInterval(temporizador);

            cuenta.classList.add('d-none');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            let contexto = canvas.getContext('2d');

            contexto.drawImage(
                video,
                0,
                0,
                canvas.width,
                canvas.height
            );

            video.classList.add('d-none');
            canvas.classList.remove('d-none');

            mensaje.textContent = '¡Fotografía capturada correctamente!';

            boton.classList.add('d-none');
            btnEnviarFoto.classList.remove('d-none');

        }

    }, 1000);

});

// ENVIAR FOTOGRAFÍA AL SERVIDOR
btnEnviarFoto.addEventListener('click', function () {

    if (!puntoActual) {
        alert('No se ha seleccionado ninguna misión.');
        return;
    }

    btnEnviarFoto.disabled = true;
    btnEnviarFoto.textContent = 'Enviando...';

    canvas.toBlob(async function (blob) {

        if (!blob) {
            alert('No se pudo procesar la fotografía.');
            btnEnviarFoto.disabled = false;
            btnEnviarFoto.textContent = 'Enviar fotografía';
            return;
        }

        const datos = new FormData();

        datos.append('punto_interes_id', puntoActual);
        datos.append('foto', blob, 'evidencia.jpg');

        try {

            const respuesta = await fetch(
                @json(route('tareas.completar')),
                {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        'Accept': 'application/json'
                    },
                    body: datos
                }
            );

            const resultado = await respuesta.json();
if (resultado.ganador) {
    window.location.href =
        @json(route('resultados')) + '?ganador=' + resultado.ganador;
    return;
}
            if (!respuesta.ok || !resultado.success) {
                throw new Error(
                    resultado.message ||
                    resultado.mensaje ||
                    'No se pudo completar la misión.'
                );
            }

            // ACTUALIZAR LA TAREA
            const tarea = document.getElementById(
                'tarea-' + resultado.id
            );
            // Cambiar también el marcador del mapa a verde
            const marcador = document.getElementById(
                'marcador-' + resultado.id
            );

            if (marcador) {
                marcador.classList.add('marcador-completado');
            }

            if (tarea) {

                const tarjeta = tarea.querySelector('.tarea-card');

                tarjeta.classList.add('tarea-completada');

                const columna = tarea.querySelector('.col-4');

                columna.innerHTML = `
                    <span class="badge bg-success">
                        COMPLETADA
                    </span>
                `;

            }

            // CERRAR MODAL
            bootstrap.Modal.getInstance(modal).hide();

            console.log('Misión completada:', resultado.id);

        } catch (error) {

            console.error(error);
            alert(error.message);

        } finally {

            btnEnviarFoto.disabled = false;
            btnEnviarFoto.textContent = 'Enviar fotografía';

        }

    }, 'image/jpeg', 0.85);

});

// CERRAR CÁMARA Y RESTABLECER MODAL
modal.addEventListener('hidden.bs.modal', function () {

    if (flujoCamara) {

        flujoCamara.getTracks().forEach(function (track) {
            track.stop();
        });

        flujoCamara = null;

    }

    video.srcObject = null;

    video.classList.remove('d-none');
    canvas.classList.add('d-none');

    cuenta.classList.add('d-none');

    boton.classList.remove('d-none');
    boton.disabled = true;
    boton.textContent = '📷 Capturar';

    btnEnviarFoto.classList.add('d-none');
    btnEnviarFoto.disabled = false;
    btnEnviarFoto.textContent = 'Enviar fotografía';

    mensaje.textContent = 'Esperando cámara...';

    puntoActual = null;

});
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

    const idsala = @json(session('sala_id'));

    function actualizarProgreso(event) {

        const progreso = event.progreso;

        document.getElementById('barraProgreso').style.width = progreso + '%';
        document.getElementById('barraProgreso').setAttribute('aria-valuenow', progreso);

        document.getElementById('textoProgreso').textContent = progreso + '%';

        document.getElementById('detalleProgreso').textContent =
            event.completadas + ' de ' + event.total + ' misiones completadas';

        // Si todas las misiones están completadas
        if (event.finalizada) {
            document.getElementById('textoProgreso').textContent = '100%';

            setTimeout(function () {
                window.location.href = @json(route('resultados'));
            }, 2000);
        }
    }

    if (window.Echo && idsala) {
        window.Echo.channel('sala.' + idsala)
            .listen('.progreso.actualizado', actualizarProgreso);
    }

});
    </script>

    <script>
document.getElementById('btnHeMuerto').addEventListener('click', async function () {

    if (!confirm('¿Estás seguro de que has muerto?')) {
        return;
    }

    const boton = this;

    boton.disabled = true;

    try {
        const respuesta = await fetch(@json(route('jugador.heMuerto')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok || !resultado.success) {
            throw new Error(resultado.mensaje || 'No se pudo actualizar el estado.');
        }

        boton.innerHTML = `
            <span class="accion-icono">
                <i class="fas fa-skull"></i>
            </span>
            <span class="accion-info">
                <strong>ESTÁS MUERTO</strong>
                <small>Ya no puedes participar</small>
            </span>
        `;

        boton.classList.add('disabled');

    } catch (error) {
        boton.disabled = false;
        alert(error.message);
    }

});
</script>
<script>
document.getElementById('btnEncontrado').addEventListener('click', async function () {

    if (!confirm('¿Deseas reportar un jugador muerto e iniciar una reunión?')) {
        return;
    }

    const boton = this;
    boton.disabled = true;

    try {
        const respuesta = await fetch(@json(route('reunion.emergencia')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok || !resultado.success) {
            throw new Error(resultado.mensaje || 'No se pudo iniciar la reunión.');
        }

        console.log('Reunión creada:', resultado.reunion_id);
window.location.href = @json(url('/reunion')) + '/' + resultado.reunion_id;
    } catch (error) {
        alert(error.message);
        boton.disabled = false;
    }

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const idsala = @json(session('sala_id'));
    const urlReunion = @json(url('/reunion'));

window.Echo.channel('sala.' + idsala)

    .listen('.reunion.iniciada', function(event) {

        console.log('REUNIÓN INICIADA:', event);

        window.location.href =
            urlReunion + '/' + event.reunion_id;

    })

    .listen('.partida.finalizada', function(event) {

        console.log('PARTIDA FINALIZADA:', event);

        if (event.ganador === 'impostores') {

            window.location.href =
                @json(route('resultados')) + '?ganador=impostores';

        } else if (event.ganador === 'tripulantes') {

            window.location.href =
                @json(route('resultados')) + '?ganador=tripulantes';

        }

    });

});
</script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>