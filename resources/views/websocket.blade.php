<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Prueba WebSockets - Impostorhouse</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main style="max-width: 800px; margin: 50px auto; font-family: Arial, sans-serif;">
        <h1>Prueba de WebSockets Hache
            
        </h1>

        <p>
            Esta página utiliza Laravel Blade, Laravel Echo y Laravel Reverb.
        </p>

        <button id="enviar" type="button">
            Enviar evento de prueba
        </button>

        <h2>Mensajes recibidos</h2>

        <div id="mensajes">
            <p>Esperando mensajes de Reverb...</p>
        </div>

        <p id="estado"></p>
    </main>

    <script>
        document.getElementById('enviar').addEventListener('click', async () => {
            const estado = document.getElementById('estado');

            estado.textContent = 'Enviando evento...';

            try {
                const respuesta = await fetch('/emitir-prueba', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!respuesta.ok) {
                    throw new Error(`Error HTTP ${respuesta.status}`);
                }

                const resultado = await respuesta.json();

                estado.textContent = resultado.mensaje;
            } catch (error) {
                estado.textContent = `Error: ${error.message}`;
            }
        });
    </script>
</body>
</html>