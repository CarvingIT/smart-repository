<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pop\Mail\Client;

class ImportFromMailbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:ImportFromMailbox';

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
		echo "Importing emails!\n";

		$imap = new Client\Imap('imap.gmail.com', 993);
		$imap->setUsername('carvingtesting@gmail.com')
     		->setPassword('carvingtesting123');

		$imap->setFolder('INBOX');
		$imap->open('/ssl');

		// Sorted by date, reverse order (newest first)
		$ids     = $imap->getMessageIdsBy(SORTDATE, true);
		$headers = $imap->getMessageHeadersById($ids[0]);
		$parts   = $imap->getMessageParts($ids[0]);

		print_r($headers);

		// Assuming the first part is an image attachment, display image
		//header('Content-Type: image/jpeg');
		//header('Content-Length: ' . strlen($parts[0]->content));
		echo $parts[0]->content;
    }
}
