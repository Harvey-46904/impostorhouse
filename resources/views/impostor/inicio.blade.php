<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/mystyle.css') }}">
    <title>Impostor House</title>
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 vh-100 d-flex flex-column justify-content-end"  style="background-image: url('img/inicio-fondo.jpg'); background-size: cover; background-position: center;">

  <button type="button" class="btn btn-game mb-4"
        onclick="solicitarCamara()">
    COMENZAR
</button>

</div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>

    async function solicitarCamara() {

        try {

            // Solicitar permiso para utilizar la cámara
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            });

            // Apagar la cámara después de conceder el permiso
            stream.getTracks().forEach(track => track.stop());

            // Ir al perfil
            window.location.href = "{{ route('perfil') }}";

        } catch (error) {

            alert('Debes permitir el acceso a la cámara para jugar.');

            console.error(error);

        }

    }

</script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>