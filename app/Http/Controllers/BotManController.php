<?php
namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
use DonatelloZa\RakePlus\RakePlus;
use App\Document;
use App\Util;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle(Request $req)
    {
        $botman = app('botman');
        $botman->hears('{message}', function($botman, $req, $message) {
            if (strtolower($message) == 'hi' || strtolower($message) == 'hello') {
                $this->askName($botman);
            }
			else if($message == 'q'){
				$this->search($botman, $req);
            }
			else{
				$this->unknownCommand($botman);
			}
        });
        $botman->listen();
    }

    /**

     * Place your BotMan logic here.

     */
	public function unknownCommand($botman){
		$botman->reply('I don\'t understand that command. Type "h" to get a list of commands.');
	}

    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
            $name = $answer->getText();
            $this->say('Nice to meet you '.$name.'.');
        });
    }

    public function search($botman, $req)
    {
        $botSearch = function(Answer $answer, $req){
            $keywords = RakePlus::create($answer->getText())->get();
			//$this->say($keywords);
			$client = new \GuzzleHttp\Client();
			$http_host = request()->getHttpHost();
			$protocol = request()->getScheme();
			$endpoint = $protocol.'://'.$http_host.'/api/collection/1/search?search[value]='.urlencode(implode(" ",$keywords));

			$res = $client->get($endpoint);

			$status_code = $res->getStatusCode();
			if($status_code == 200){
				$body = $res->getBody();
				$documents_array = json_decode($body);
				$botman_results = '';
				if(count($documents_array->data) == 0){
					$botman_results .= "I don't know.";
				}
				
				$info_from_docs = '';
				foreach($documents_array->data as $d){
					$doc = Document::find($d->id);
					$info_from_docs .= $doc->text_content;
				}

				// remove Page \d\d from the string
				$info_from_docs = preg_replace('/Page \d\d*/',' ', $info_from_docs);
				$chunks = Util::createTextChunks($info_from_docs, 4000, 1000);
				$matches = Util::findMatches($chunks, $keywords);
				$matches_details = '';
				$matches_cnt = 0;
				foreach($matches as $chunk_id => $score){
					if($matches_cnt < 10){
						// consider only 10 most relevant matches for answering questions
						$matches_details .= $chunk_id . ' - '. $score ."\n";	
					}
					$matches_cnt++;
				}
				$this->say($matches_details);
			}else{
				$this->say('There was some error. Please try again.');
			}
        };

		$botman->ask('Type in your question', $botSearch);
    }

}
