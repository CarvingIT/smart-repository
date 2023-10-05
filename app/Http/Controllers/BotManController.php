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
			else if(!empty($message)){
				
			}
			else{
                $botman->reply("Hello! \n Type in your question and let me see if I can answer that.");
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
        //$botman->ask('Enter search keywords', $botSearch = function(Answer $answer, $req) {
        $botSearch = function(Answer $answer, $req) use (&$botSearch){
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
				if(count($documents_array->data) == 0){
					$botman_results .= "I don't know.";
				}
				else{
					/*
					$botman_results .= 'Found '.$documents_array->recordsFiltered.' documents from '.$documents_array->recordsTotal.'.';
					$botman_results .= '<br/>Listing 10 most relevant here.<br/>';
					*/
				}
				
				foreach($documents_array->data as $d){
					$botman_results .= '<li><a href="/collection/1/document/'.$d->id.'">'.$d->title.'</a></li>';
				}
				$this->say($botman_results.'');
			}else{
				$this->say('There was some error. Please try again.');
			}
            //$this->say('You entered '.$keywords.'.');
        };
		$botman->ask('Enter search keywords', $botSearch);
    }

}
