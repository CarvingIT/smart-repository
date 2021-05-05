<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pop\Mail\Client;
use Illuminate\Support\Facades\Storage;
use App\CollectionMailbox;
use App\MailMessage;

class ImportFromMailbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportFromMailbox
			{--since= : Fetch emails from yyyy-mm-dd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports emails from configured mailbox for each collection.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		// get the configured mailboxes
		$mailboxes = CollectionMailbox::all();
		foreach($mailboxes as $mbox){
			$creds = json_decode($mbox->credentials);
			$files_fetched = false;
			// get a date that was 2 days ago
			$default_since = date('Y-m-d', strtotime('-2 days'));
			$since = empty($this->option('since')) ? 'SINCE '.$default_since : 'SINCE '.$this->option('since');
			echo "Importing emails from ".$mbox->address." that came since $since.\n";

			$imap = new Client\Imap($creds->server_address, $creds->server_port);
			$imap->setUsername($creds->username)
     			->setPassword($creds->password);

			$imap->setFolder('INBOX');
			$imap->open('/'.$creds->security);

			// Sorted by date, reverse order (newest first)
			$ids     = $imap->getMessageIdsBy(SORTDATE, true, SE_UID, $since);
		
			echo "There are ".count($ids)." emails.\n";
		
			// continue if there are no emails
			if(count($ids) == 0) continue;
			$temp_dir = \Str::uuid();

			foreach($ids as $id){
				// check if the id is already processed
				$message = MailMessage::where('message_id', $id)
					->where('mailbox_id', $mbox->id)->first();
				if($message){
					// message is already processed
					echo "Message ".$id." already processed.\n";
					continue;
				}
				$headers = $imap->getMessageHeadersById($id);
				$parts   = $imap->getMessageParts($id);
				$attachments = $imap->getMessageAttachments($id);

				echo "parts: ".count($parts)."\n";
				echo "attachments: ".count($attachments)."\n";
				//print_r($headers);
				foreach($attachments as $a){
					echo $a->type."\n";
					echo $a->basename."\n";
					Storage::put('mailbox_imports/'.$temp_dir.'/'.$a->basename, $a->content);
					$files_fetched = true;
				}
				// body of the email
				//echo $parts[0]->content."\n";
				// save the message in order to avoid processing again	
				$message = new MailMessage;
				$message->mailbox_id = $mbox->id;
				$message->message_id = $id;
				$message->save();
			}
			if($files_fetched){
				try{
					// import all files stored under mailbox_imports/$temp_dir
					$this->call('SR:ImportDocs', 
						['collection_id'=> 1, 
						'--dir'=> storage_path('app').'/mailbox_imports/'.$temp_dir]);
					// clean contents of mailbox_imports
					Storage::deleteDirectory('/mailbox_imports/');
				}
				catch(\Exception $e){
					echo $e->getMessage()."\n";
				}
			}
		}
    }
}
