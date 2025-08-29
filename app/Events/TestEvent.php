<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestEvent implements ShouldBroadcast
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // Kis channel par broadcast hoga
    public function broadcastOn()
    {
        return new Channel('my-channel'); // tumhare JS me jo channel hai
    }

    // Event ka naam
    public function broadcastAs()
    {
        return 'my-event'; // tumhare JS me jo event hai
    }

    public function broadcastWith()
{
    return [
        'message' => $this->message
    ];
}
}
