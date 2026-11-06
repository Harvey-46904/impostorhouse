
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PartidaFinalizada implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $idsala;
    public $ganador;
    public $impostoresVivos;
    public $tripulantesVivos;

    public function __construct(
        $idsala,
        $ganador,
        $impostoresVivos,
        $tripulantesVivos
    ) {
        $this->idsala = $idsala;
        $this->ganador = $ganador;
        $this->impostoresVivos = $impostoresVivos;
        $this->tripulantesVivos = $tripulantesVivos;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sala.' . $this->idsala),
        ];
    }

    public function broadcastAs(): string
    {
        return 'partida.finalizada';
    }

    public function broadcastWith(): array
    {
        return [
            'idsala' => $this->idsala,
            'ganador' => $this->ganador,
            'impostores_vivos' => $this->impostoresVivos,
            'tripulantes_vivos' => $this->tripulantesVivos,
        ];
    }
}
