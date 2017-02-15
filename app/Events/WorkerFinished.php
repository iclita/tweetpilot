<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Worker;

class WorkerFinished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The App\Worker instance.
     *
     * @var App\Worker
     */
    public $worker;

    /**
     * Create a new event instance.
     *
     * @param Worker $worker
     * @return void
     */
    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
