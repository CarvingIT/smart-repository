<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DocumentSaved as DocumentSavedNotification;
use App\Approval;
use App\Services\ThumbnailService;
use App\Services\UserAlertService;

class DocumentSaved
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
        //$was_changed = $event->document->wasChanged();

		$notifiable = $event->document->collection;
        $collection_config = json_decode($event->document->collection->column_config);
        if(!empty($collection_config->slack_webhook) || !empty($collection_config->notify_email)){
		    try{
		    	Notification::send($notifiable, new DocumentSavedNotification($event->document));
		    }
		    catch(\Exception $e){
		    	Log::error($e->getMessage());
		    }
        }

        try {
            $status = empty($event->document->approved_on) ? 'updated' : 'published';
            if ($event->document->wasRecentlyCreated) {
                $status = 'created';
            }

            $alertTitle = 'Document '.$status;
            $alertMessage = 'Document "'.$event->document->title.'" in collection "'.$event->document->collection->name.'" was '.$status.'.';
            $alertUrl = '/collection/'.$event->document->collection->id.'/document/'.$event->document->id.'/details';

            $alertService = new UserAlertService();
            $alertService->createForCollectionUsers(
                $event->document->collection,
                $alertTitle,
                $alertMessage,
                $alertUrl,
                [
                    'type' => 'document_saved',
                    'document_id' => $event->document->id,
                    'collection_id' => $event->document->collection->id,
                    'status' => $status,
                ]
            );
        }
        catch(\Exception $e){
            Log::warning('Unable to create document alert: '.$e->getMessage());
        }

        // add a record in the approvals table only on first save (not on every edit)
        if($event->document->collection->require_approval == 1
           && empty($event->document->approved_on)){
			// only create an approval record if no pending one already exists
			$already_has_pending = $event->document->approvals()
				->whereNull('approval_status')
				->exists();
			if (!$already_has_pending) {
				// get the first role id from approval workflow
				$collection_config = $event->document->collection->column_config;
				$col_conf = json_decode($collection_config);
				if (!empty($col_conf) && !empty($col_conf->approved_by)) {
					$approvers = $col_conf->approved_by;
					$approval_record = new Approval(['approved_by_role'=>$approvers[0]]);
					$event->document->approvals()->save($approval_record);
				}
			}
		}

	    // Update elasticsearch index 
        Log::info('Updating the elasticsearch index now');
       	$elastic_hosts = env('ELASTIC_SEARCH_HOSTS', 'localhost:9200');
       	$hosts = explode(",",$elastic_hosts);
        $client = ClientBuilder::create()->setHosts($hosts)
		        ->setBasicAuthentication('elastic', env('ELASTIC_PASSWORD','some-default-password'))
		        ->setCABundle('/etc/elasticsearch/certs/http_ca.crt')
                ->build();
            $body = $event->document->toArray();
            $body['text_content'] = $event->document->text_content;
            foreach($event->document->meta as $mv){
                if(!empty($mv->value)){
                    if(@$mv->meta_field->type == 'Numeric') {
                        $val = floatval($mv->value);
                    }
                    else {// for Date/Text/Textarea/Select/....
                        $val = $mv->value;
                    }
                    $body['meta_'.$mv->meta_field_id] = $val;
                }
            }

            $del_params = [
                'index' => 'sr_documents',
                'id'    => $event->document->id
            ];
            $params = [
                'index' => 'sr_documents',
                'id'    => $event->document->id,
                'body'  => $body
            ];

            // delete record if present
	    	try{
                $del_response = $client->delete($del_params);
	    	}
	    	catch(\Exception $e){
	    		//Log::warning($e->getMessage());
	    	}
            // index the new/updated document
            try{
           	    $response = $client->index($params);
    		    Log::info('Elastic index updated for document '. $event->document->id.'.');
            }
	    	catch(\Exception $e){
	    		Log::warning($e->getMessage());
	    	}

        // Generate PDF thumbnail only if a PDF file was uploaded
        // Check if document was recently created/updated with a file
        if (env('ENABLE_PDF_THUMBNAILS') && $event->document->type == 'application/pdf' && !empty($event->document->path)) {
            $thumbnailService = new ThumbnailService();
            
            // Get the PDF file path
            $collection = $event->document->collection;
            $storageDrive = empty($collection->storage_drive) ? 'local' : $collection->storage_drive;
            
            // Only generate thumbnail if file is on local storage and exists
            if ($storageDrive === 'local') {
                $pdfPath = storage_path('app/' . $event->document->path);
                
                // Check if file exists and was recently modified (uploaded/updated)
                if (file_exists($pdfPath)) {
                    $fileModifiedTime = filemtime($pdfPath);
                    $documentUpdatedTime = strtotime($event->document->updated_at);
                    
                    // Generate thumbnail only if file was modified within last 5 minutes
                    // This ensures we only regenerate when file is actually uploaded
                    if (($documentUpdatedTime - $fileModifiedTime) < 300) {
                        try {
                            $thumbnailService->generateThumbnail($pdfPath, $event->document->collection_id, $event->document->id);
                            Log::info('PDF thumbnail generated for document ' . $event->document->id);
                        } catch (\Exception $e) {
                            Log::warning('Failed to generate thumbnail for document ' . $event->document->id . ': ' . $e->getMessage());
                        }
                    }
                }
            }
        }
    }
}
