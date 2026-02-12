<?php

namespace App\Events;

use App\Models\Challenge;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChallengeReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $challenge;

    public function __construct(Challenge $challenge)
    {
        $this->challenge = $challenge;
    }

    // Kirim ke Channel Pribadi milik Target (Pemain yang ditantang)
    public function broadcastOn(): array
    {
        // Channel: user.{id}
        return [
            new PrivateChannel('user.' . $this->challenge->target_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'sender_name' => $this->challenge->sender->name,
            'category' => $this->challenge->room->category->name,
            'challenge_id' => $this->challenge->id
        ];
    }
}