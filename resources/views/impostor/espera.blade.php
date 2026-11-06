<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
    @vite(['resources/js/app.js'])
    <title>Impostor House - Sala de espera</title>
</head>

<body>

    <div class="container bg-game vh-100">
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
        <div class="row">
            <div class="col-md-12">
                <img src="{{ url('img/iconografia', 'logo.png') }}" class="img-fluid" alt="Logo">
            </div>
        </div>

        <div class="row py-4 text-center">
            <div class="col-12">
                <p class="titulo">SALA DE ESPERA</p>
                <p class="subtitulo">{{ $sala->nombre }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <p class="subtitulo">JUGADORES CONECTADOS</p>
            </div>
        </div>

        @foreach($jugadores as $j)
        <div class="row bg-formularios align-items-center mx-3 py-2 rounded mb-2">
            <div class="col-9">
                <p class="texto mb-0">{{ $j->nombre }}</p>
            </div>

            <div class="col-3 text-end">
                @if($j->admin)
                <span class="texto-destacado">ADMIN</span>
                @else
                <span class="texto-secundario">JUGADOR</span>
                @endif
            </div>
        </div>
        @endforeach

        @if($jugador->admin)

        <div class="row mt-4">
            <div class="col-12">
                <form action="{{ route('sala.iniciar') }}" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-game mb-4">
                        COMENZAR PARTIDA
                    </button>
                </form>
            </div>
        </div>

        @else

        <div class="row mt-4">
            <div class="col-12 text-center">
                <p class="subtitulo">
                    Esperando que el administrador inicie la partida...
                </p>
            </div>
        </div>

        @endif

    </div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (window.Echo) {

            window.escucharSala(
    {{ $sala->id }},
    @json(route('rol'))
);

            console.log('Escuchando sala: {{ $sala->id }}');

        } else {

            console.error('Laravel Echo no está inicializado');

        }

    });
</script>
</html>