<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class ConsumoEntregado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * ID del casino donde se registró la entrega.
     */
    public int $idCasino;

    /**
     * Crear una nueva instancia del evento.
     */
    public function __construct(int $idCasino)
    {
        $this->idCasino = $idCasino;
    }

    /**
     * Canal de broadcast.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('casino.' . $this->idCasino);
    }
}

