
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">

    <title>Impostor House</title>
</head>

<body>

    <div class="container bg-game">

        <!-- LOGO -->
        <div class="row">
            <div class="col-md-12">
                <img src="{{ url('img/iconografia', 'logo.png') }}" class="img-fluid" alt="Logo">
            </div>
        </div>

        <!-- TITULO -->
        <div class="row">
            <div class="col-md-12">
                <p class="titulo">¿CÓMO TE</p>
                <p class="titulo">LLAMAS?</p>
            </div>
        </div>

        <!-- FORMULARIO -->
        <form action="{{ route('perfil.guardar') }}" method="POST">
            @csrf

            @if(!$jugador)

                <!-- NOMBRE PARA JUGADOR NUEVO -->
                <div class="row bg-formularios justify-content-center align-items-center mx-3 py-2 rounded">

                    <div class="col-2">
                        <img src="{{ url('img/iconografia', 'user.png') }}"
                            alt="Usuario" style="width: 50px; height: 50px;">
                    </div>

                    <div class="col-9">
                        <input type="text"
                            name="nombre"
                            class="form-control bg-formularios text-light"
                            placeholder="Nombre"
                            maxlength="255"
                            required>
                    </div>

                </div>

            @else

                <!-- SALUDO PARA JUGADOR EXISTENTE -->
                <p class="subtitulo">
                    ¡Hola, {{ $jugador->nombre }}! Escoge tu color.
                </p>

            @endif

            <!-- TITULO COLORES -->
            <div class="row py-3 align-items-center">

                <div class="col-2">
                    <img src="{{ url('img/iconografia', 'colores.png') }}"
                        alt="Colores" style="width: 50px; height: 50px;">
                </div>

                <div class="col-9">
                    <p class="subtitulo text-start">Escoger Color</p>
                </div>

            </div>

            <!-- COLORES -->
            @php
                $colores = [
                    'amarillo',
                    'azul',
                    'morado',
                    'naranja',
                    'negro',
                    'rojo',
                    'rosa',
                    'verde'
                ];
            @endphp

            <div class="row g-3">

                @foreach($colores as $color)

                    <div class="col-4">

                        <div class="personaje"
                            onclick="seleccionarColor(this, '{{ $color }}')">

                            <img src="{{ asset('img/colores/'.$color.'.png') }}"
                                alt="{{ $color }}"
                                class="img-fluid">

                        </div>

                    </div>

                @endforeach

            </div>

            <!-- COLOR SELECCIONADO -->
            <input type="hidden" name="color" id="colorSeleccionado">

            <!-- BOTON COMENZAR -->
            <div class="row mt-4">
                <div class="col-12">

                    <button type="submit" class="btn btn-game mb-4">
                        COMENZAR
                    </button>

                </div>
            </div>

        </form>

    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
    </script>

    <!-- SELECCIONAR COLOR -->
    <script>

        function seleccionarColor(elemento, color) {

            // Quitar selección de todos
            document.querySelectorAll('.personaje').forEach(function(item) {
                item.classList.remove('seleccionado');
            });

            // Iluminar el seleccionado
            elemento.classList.add('seleccionado');

            // Guardar el color seleccionado
            document.getElementById('colorSeleccionado').value = color;

            console.log('Color seleccionado:', color);

        }

    </script>

</body>
</html>
