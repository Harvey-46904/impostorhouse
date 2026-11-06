<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PartidaIniciada implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $idsala;

    public function __construct($idsala)
    {
        $this->idsala = $idsala;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->idsala)
        ];
    }

    public function broadcastAs(): string
    {
        return 'partida.iniciada';
    }

    public function broadcastWith(): array
    {
        return [
            'idsala' => $this->idsala
        ];
    }
}