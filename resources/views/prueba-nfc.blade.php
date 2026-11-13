
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prueba NFC - Impostor House</title>

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #10102b, #211044, #111827);
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .panel {
            width: 100%;
            max-width: 480px;
            background: #1c1c38;
            border: 1px solid #484078;
            border-radius: 20px;
            padding: 25px;
            box-sizing: border-box;
            text-align: center;
        }

        h1 { color: #b69cff; }

        .icon {
            font-size: 65px;
            margin: 20px;
        }

        button {
            width: 100%;
            padding: 15px;
            margin-top: 12px;
            border: 0;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        #start {
            background: #8155ee;
            color: white;
        }

        #stop {
            background: #373750;
            color: white;
        }

        #status {
            margin: 20px 0;
            padding: 13px;
            border-radius: 10px;
            background: #292945;
        }

        #result {
            text-align: left;
            background: #111827;
            padding: 15px;
            border-radius: 12px;
            margin-top: 20px;
            overflow-wrap: anywhere;
            font-size: 13px;
        }

        .success { color: #5cf2a5; }
        .error { color: #ff7777; }

        pre {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>
<div class="panel">

    <h1>IMPOSTOR HOUSE</h1>
    <p>Laboratorio de lectura NFC</p>

    <div class="icon">📡</div>

    <h2>Acerca tu celular al TAG</h2>
    <p>Acerca la parte trasera del teléfono al sticker NFC.</p>

    <div id="status">Esperando para iniciar...</div>

    <button id="start">📲 Iniciar lectura NFC</button>
    <button id="stop" disabled>Detener lectura</button>

    <div id="result">
        <strong>Resultado de lectura</strong>
        <p>Aún no se ha detectado ningún TAG.</p>
    </div>

</div>

<script>
    const statusBox = document.getElementById('status');
    const resultBox = document.getElementById('result');
    const startButton = document.getElementById('start');
    const stopButton = document.getElementById('stop');

    let controller = null;

    function showStatus(message, type = '') {
        statusBox.textContent = message;
        statusBox.className = type;
    }

    function bytesToHex(data) {
        return Array.from(data)
            .map(byte => byte.toString(16).padStart(2, '0'))
            .join(' ');
    }

    function readRecord(record) {
    if (!record.data) {
        return {
            tipo: record.recordType,
            contenido: 'Sin contenido'
        };
    }

    const bytes = new Uint8Array(
        record.data.buffer,
        record.data.byteOffset,
        record.data.byteLength
    );

    let content;

    if (record.recordType === 'text' && bytes.length > 0) {
        const languageLength = bytes[0] & 0x3f;
        const isUtf16 = (bytes[0] & 0x80) !== 0;

        const textBytes = bytes.slice(1 + languageLength);

        content = new TextDecoder(
            isUtf16 ? 'utf-16' : 'utf-8'
        ).decode(textBytes);
    } else {
        content = new TextDecoder().decode(bytes);
    }

    return {
        tipo: record.recordType,
        identificador: record.id || 'No disponible',
        contenido: content,
        hexadecimal: bytesToHex(bytes)
    };
}

    startButton.addEventListener('click', async () => {

        if (!('NDEFReader' in window)) {
            showStatus(
                'Este navegador no soporta Web NFC. Prueba con Chrome en Android.',
                'error'
            );
            return;
        }

        try {
            const ndef = new NDEFReader();

            controller = new AbortController();

            showStatus('Buscando TAG NFC...', '');

            await ndef.scan({
                signal: controller.signal
            });

            startButton.disabled = true;
            stopButton.disabled = false;

            showStatus('Lector activo. Acerca el celular al TAG.');

            ndef.onreading = (event) => {

                const records = event.message.records.map(readRecord);

                const result = {
                    resultado: 'TAG NFC DETECTADO',
                    serialNumber: event.serialNumber || 'No proporcionado',
                    registros: records
                };

                resultBox.innerHTML = `
                    <strong class="success">¡TAG DETECTADO!</strong>
                    <pre>${JSON.stringify(result, null, 2)}</pre>
                `;

                showStatus('¡Lectura NFC exitosa!', 'success');

                console.log('NFC detectado:', result);
            };

            ndef.onreadingerror = () => {
                showStatus(
                    'Se detectó un TAG, pero no se pudo leer su contenido.',
                    'error'
                );
            };

        } catch (error) {
            showStatus('Error: ' + error.message, 'error');
            console.error(error);
        }
    });

    stopButton.addEventListener('click', () => {
        if (controller) {
            controller.abort();
            controller = null;
        }

        startButton.disabled = false;
        stopButton.disabled = true;

        showStatus('Lectura detenida.');
    });
</script>

</body>
</html>
