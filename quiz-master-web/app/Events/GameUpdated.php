<?php

namespace App\Events;

use App\Models\GameRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameRoom;

    public function __construct(GameRoom $gameRoom)
    {
        $this->gameRoom = $gameRoom;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('game.' . $this->gameRoom->room_code),
        ];
    }

    public function broadcastAs(): string
    {
        return 'GameUpdated';
    }

    public function broadcastWith(): array
    {
        // LOGIKA PENENTUAN PEMENANG (SERVER SIDE)
        $winnerId = null;
        if ($this->gameRoom->status === 'finished') {
            if ($this->gameRoom->host_score > $this->gameRoom->challenger_score) {
                $winnerId = $this->gameRoom->host_id;
            } elseif ($this->gameRoom->challenger_score > $this->gameRoom->host_score) {
                $winnerId = $this->gameRoom->challenger_id;
            }
            // Jika seri, winnerId tetap null
        }

        return [
            'id' => $this->gameRoom->id,
            'host_score' => $this->gameRoom->host_score,
            'challenger_id' => $this->gameRoom->challenger_id,
            'challenger_score' => $this->gameRoom->challenger_score,
            'status' => $this->gameRoom->status,
            'winner_id' => $winnerId, // <-- KIRIM ID PEMENANG
        ];
    }
}