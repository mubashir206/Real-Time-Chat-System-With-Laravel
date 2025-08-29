<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessagesSeen implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $groupId;
    public $userId;
    public $messageIds;

    public function __construct($groupId, $userId, $messageIds)
    {
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->messageIds = $messageIds;
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
            'group_id' => $this->groupId,
            'user_id' => $this->userId,
            'message_ids' => $this->messageIds,
        ];
    }

    public function broadcastAs(): string
    {
        return 'messages.seen';
    }
}
