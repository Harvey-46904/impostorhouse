<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajePrueba implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function broadcastOn(): array
    {
        return [new Channel('pruebas')];
    }

    public function broadcastAs(): string
    {
        return 'mensaje.prueba';
    }

    public function broadcastWith(): array
    {
        return [
            'mensaje' => 'WebSocket funcionando correctamente!',
            'hora' => now()->format('H:i:s'),
        ];
    }
}