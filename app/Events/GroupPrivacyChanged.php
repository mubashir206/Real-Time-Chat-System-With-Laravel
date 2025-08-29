<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Group;

class GroupPrivacyChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $oldType;
    public $newType;

    public function __construct(Group $group, $oldType, $newType)
    {
        $this->group = $group;
        $this->oldType = $oldType;
        $this->newType = $newType;
    }

    public function broadcastOn()
    {
        return new Channel('group.' . $this->group->id);
    }

    public function broadcastAs()
    {
        return 'group.privacy.changed';
    }

    public function broadcastWith()
    {
        return [
            'group_id' => $this->group->id,
            'old_type' => $this->oldType,
            'new_type' => $this->newType,
            'group' => $this->group
        ];
    }
}
