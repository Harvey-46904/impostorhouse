<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProgresoSalaActualizado implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $idsala;
    public $progreso;
    public $completadas;
    public $total;
    public $finalizada;

    public function __construct(
        $idsala,
        $progreso,
        $completadas,
        $total,
        $finalizada
    ) {
        $this->idsala = $idsala;
        $this->progreso = $progreso;
        $this->completadas = $completadas;
        $this->total = $total;
        $this->finalizada = $finalizada;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->idsala),
        ];
    }

    public function broadcastAs(): string
    {
        return 'progreso.actualizado';
    }
}