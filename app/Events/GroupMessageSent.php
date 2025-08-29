<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $groupId;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->groupId = $message->group_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('group.' . $this->groupId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'user_id' => $this->message->user_id,
            'group_id' => $this->groupId,
            'message' => $this->message->message,
            'is_seen' => $this->message->is_seen,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'user' => [
                'name' => $this->message->user->name,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
