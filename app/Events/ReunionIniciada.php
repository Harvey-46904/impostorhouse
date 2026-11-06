<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReunionIniciada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $idsala;
    public $reunion_id;
    public $tipo;
    public $votacion_inicia_at;
    public $votacion_finaliza_at;

    public function __construct($idsala, $reunion)
    {
        $this->idsala = $idsala;
        $this->reunion_id = $reunion->id;
        $this->tipo = $reunion->tipo;
        $this->votacion_inicia_at = $reunion->votacion_inicia_at;
        $this->votacion_finaliza_at = $reunion->votacion_finaliza_at;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->idsala),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reunion.iniciada';
    }
}