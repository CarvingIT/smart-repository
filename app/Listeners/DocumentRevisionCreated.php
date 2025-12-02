<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DocumentRevisionCreated as DocumentRevisionCreatedNotification;

class DocumentRevisionCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // if a slack URL is provided, notifiable = collection else users of the collection
        $collection_config = json_decode($event->document_revision->document->collection->column_config);
        $notifiable = $event->document_revision->document->collection;
        //Log::debug($collection_users);
        try{
	    	if($event->document_revision->document->revisions->count() > 1){
               	Notification::send($notifiable, new DocumentRevisionCreatedNotification($event->document_revision));
			}
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
        }

        $revision = $event->document_revision;
        $document = $revision->document;
        $collection = $document->collection;

        if (!$collection || $document->deleted_at !== null ) {
            return;
        }

        $revision_size = (int)$revision->size;

        $prev = \App\DocumentRevision::where('document_id', $document->id)
            ->where('id', '<', $revision->id)
            ->orderBy('id', 'desc')
            ->first();

        $collection->increment("size_revisions", $revision_size);

        if ($prev) {
            $prev_size = (int)$prev->size;
            $delta = $revision_size - $prev_size;
            if ($delta > 0) {
                $collection->increment("size_active", $delta);
            } elseif ($delta < 0) {
                $collection->decrement("size_active", abs($delta));
            }
        } else {
            $collection->increment("document_count");
            $collection->increment("size_active", $revision_size);
        }
    }
}
