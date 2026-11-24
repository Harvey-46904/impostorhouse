<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SignalReceivedAviator implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $signal;

    public function __construct(array $signal)
    {
        $this->signal = $signal;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('signals'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'signal.received';
    }

    public function broadcastWith(): array
    {
        return [
            'signal' => $this->signal,
        ];
    }
}