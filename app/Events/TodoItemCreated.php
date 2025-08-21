<?php

namespace App\Events;

use App\Models\TodoItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TodoItemCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;

    public function __construct(TodoItem $item)
    {
        $this->item = $item;
    }

    public function broadcastOn()
    {
        return new Channel('todo.' . $this->item->todo_id);
    }
}
