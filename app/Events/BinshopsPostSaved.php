<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\BinshopsPost;

class BinshopsPostSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public $binshops_post;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BinshopsPost $binshops_post)
    {
		$this->binshops_post = $binshops_post;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
