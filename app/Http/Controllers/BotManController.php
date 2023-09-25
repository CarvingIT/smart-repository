<?php
namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;

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
			else if($message == 1){
				$this->themeInfo($botman);
			}
			else if($message == 2){
				$this->search($botman, $req);
			}
			else{
                $botman->reply("I understand these instructions <br/> 1. Information about themes and sub-themes <br /> 2. Search the repository with keywords. <br/> (Type in just the number and press enter.)");
            }
        });
        $botman->listen();
    }
    /**

     * Place your BotMan logic here.

     */

    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
            $name = $answer->getText();
            $this->say('Nice to meet you '.$name.'.');
        });
    }

    public function search($botman, $req)
    {
        $botman->ask('Enter search keywords', function(Answer $answer, $req) {
            $keywords = $answer->getText();
			$client = new \GuzzleHttp\Client();
			$http_host = request()->getHttpHost();
			$protocol = request()->getScheme();
			$endpoint = $protocol.'://'.$http_host.'/api/collection/1/search?search[value]='.urlencode($keywords);
			//$this->say($endpoint);
			// /api/collection/1/search?search[value]=
			$res = $client->get($endpoint);
			//$res = $http_req->send();
			$status_code = $res->getStatusCode();
			if($status_code == 200){
				$body = $res->getBody();
				$documents_array = json_decode($body);
				$botman_results = '';
				$botman_results .= 'Found '.$documents_array->recordsFiltered.' documents from '.$documents_array->recordsTotal.'.';
				$botman_results .= '<br/>Listing 10 most relevant here.<br/>';
				
				foreach($documents_array->data as $d){
					$botman_results .= '<a href="/collection/1/document/'.$d->id.'">'.$d->title.'</a><br />';
				}
				$this->say($botman_results.'');
				//$this->say($body.'');
			}else{
				$this->say('There was some error. Please try again.');
			}
            //$this->say('You entered '.$keywords.'.');
        });
    }

	public function themeInfo($botman){
		$botman->reply("You want information on themes.");
	}	

}
