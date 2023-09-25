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
    public function handle()
    {
        $botman = app('botman');
        $botman->hears('{message}', function($botman, $message) {

            if (strtolower($message) == 'hi' || strtolower($message) == 'hello') {
                $this->askName($botman);
            }
			else if($message == 1){
				$this->themeInfo($botman);
			}
			else if($message == 2){
				$this->search($botman);
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

    public function search($botman)
    {
        $botman->ask('Enter search keywords', function(Answer $answer) {
            $keywords = $answer->getText();
            $this->say('You entered '.$keywords.'.');
        });
    }

	public function themeInfo($botman){
		$botman->reply("You want information on themes.");
	}	

}
