<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserPresenceChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $status;

    public function __construct($userId, $status)
    {
        $this->userId = $userId;
        $this->status = $status;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user.status.' . $this->userId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'status' => $this->status,
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.status.changed';
    }
}
