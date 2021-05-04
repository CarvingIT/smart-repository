<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pop\Mail\Client;
use Illuminate\Support\Facades\Storage;

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
		// get a date that was 2 days ago
		$default_since = date('Y-m-d', strtotime('-2 days'));
		$since = empty($this->option('since')) ? 'SINCE '.$default_since : 'SINCE '.$this->option('since');
		echo "Importing emails that came since $since.\n";

		$imap = new Client\Imap('imap.gmail.com', 993);
		$imap->setUsername('carvingtesting@gmail.com')
     		->setPassword('carvingtesting123');

		$imap->setFolder('INBOX');
		$imap->open('/ssl');

		// Sorted by date, reverse order (newest first)
		$ids     = $imap->getMessageIdsBy(SORTDATE, true, SE_UID, $since);

		echo "There are ".count($ids)." emails\n";
		
		// exit if there are no emails
		if(count($ids) == 0) exit;
		$temp_dir = \Str::uuid();

		foreach($ids as $id){
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
			}
			// body of the email
			//echo $parts[0]->content."\n";
		}
		// import all files stored under mailbox_imports/$temp_dir
		$this->call('SR:ImportDocs', 
			['collection_id'=> 1, 
			'--dir'=> storage_path('app').'/mailbox_imports/'.$temp_dir]);
		// clean contents of mailbox_imports
		Storage::deleteDirectory('/mailbox_imports/');
    }
}
