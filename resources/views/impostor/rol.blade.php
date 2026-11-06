<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
    <title>Impostor House</title>
</head>

<body>
    <div class="container bg-game vh-100">

        <div class="row">
            <div class="col-md-12"> <img src="{{ url('img/iconografia','logo.png') }}" class="img-fluid" alt="...">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 py-4">
                <p class="titulo">¿QUÉ ERES?</p>
                <p class="subtitulo">La casa tiene dos roles.. y tu rol es...</p>
            </div>
        </div>
        <div class="row pt-3 justify-content-center align-items-center mx-3 py-2 rounded">
            <div class="col-md-12 text-center">

                <!-- Imagen oculta inicialmente -->
                <img id="imagenRol" src="" class="img-fluid d-none" alt="Rol del jugador">

                <!-- Signo de interrogación -->
                <div id="rolOculto" class="rol-oculto mx-auto">
                    ?
                </div>

            </div>
        </div>
        <div class="row pt-3">

    @if(!$rol)

        <form action="{{ route('rol.iniciar') }}" method="POST">
            @csrf

            <button type="submit" class="btn btn-game mb-4">
                COMENZAR
            </button>
        </form>

    @else

        <a href="{{ route('tareas') }}" class="btn btn-game mb-4">
            CONTINUAR
        </a>

    @endif

</div>
    </div>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
     function revelarRol(rol) {

    let imagen = document.getElementById('imagenRol');
    let oculto = document.getElementById('rolOculto');

    if (rol == 'impostor') {
        imagen.src = "{{ asset('img/iconografia/impostor.jpg') }}";
    }
    else if (rol == 'tripulante') {
        imagen.src = "{{ asset('img/iconografia/tripulante.jpg') }}";
    }
    else {
        return;
    }

    oculto.classList.add('d-none');
    imagen.classList.remove('d-none');
}
@if($rol)
    revelarRol(@json($rol));
@endif
    </script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>