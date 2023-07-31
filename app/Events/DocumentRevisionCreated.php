<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\DocumentRevision;

class DocumentRevisionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $document_revision;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DocumentRevision $document_revision)
    {
	 $this->document_revision = $document_revision;
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
