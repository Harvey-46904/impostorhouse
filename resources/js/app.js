import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel('pruebas')
    .listen('.mensaje.prueba', (event) => {
        console.log('Evento recibido:', event);

        const mensajes = document.getElementById('mensajes');

        if (mensajes) {
            const elemento = document.createElement('p');

            elemento.textContent = `${event.hora} — ${event.mensaje}`;

            mensajes.prepend(elemento);
        }
    });

console.log('Laravel Echo inicializado');
// Escuchar eventos de las salas
window.escucharSala = function(idsala, urlRol) {
    window.Echo.channel('sala.' + idsala)
        .listen('.partida.iniciada', function(event) {
            console.log('¡La partida ha comenzado!', event);

            window.location.href = urlRol;
        });
};