<?php
namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use DonatelloZa\RakePlus\RakePlus;
use App\Document;
use App\Util;
use App\ChatGPT;
use Illuminate\Support\Facades\Log;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\BotmanAnswer;
use App\Traits\Search;

class BotManController extends Controller
{
	use Search;
	public $chatgpt;
    /**
     * Place your BotMan logic here.
     */
    public function handle(Request $req)
    {
        $botman = app('botman');
		
        $botman->hears('{message}', function($botman, $req, $message) {
			if($message == 'q'){
				$this->questionAnswerMode($botman, $req);
            }
			/*
            else if (strtolower($message) == 'hi' || strtolower($message) == 'hello') {
                $this->askName($botman);
            }
			else if($message == 'h'){
				$this->helpMenu($botman);
			}
			*/
			else{
				//$this->unknownCommand($botman);
				$this->questionAnswerMode($botman, $req);
			}
        });
        $botman->listen();
		/*
		$botman->startConversation(new QuestionAnswerMode);
		*/
    }

    /**

     * Place your BotMan logic here.

     */
	public function helpMenu($botman){
		$botman->reply('Following commands are available -<br /><strong>h</strong> - Show this help menu <br /><strong>q</strong> - Ask a question.');
	}

    public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
            $name = $answer->getText();
            $this->say('Nice to meet you '.$name.'.');
        });
    }

    public function questionAnswerMode($botman, $req)
    {
		$this_controller = $this;

        $botSearch = function(Answer $answer, $req) use ($this_controller, &$botSearch ,$botman) {
			// get keywords
			$question = $answer->getText();
			// $keywords = RakePlus::create($question)->get(); // this gives phrases
            $keywords = RakePlus::create($question)->keywords();
			Log::debug('Keywords: '.implode(' ',$keywords));
			// check if this question was asked earlier
			// saves time
			$question = ltrim(rtrim(preg_replace('!\s+!', ' ', $question)));
			$botman_answer = BotmanAnswer::where('question', $question)->first();
			$related_docs_link = 'Find more related documents <a target="_new" href="/collection/1?isa_search_parameter='.
                            urlencode(implode(' ',$keywords)).'">here</a>.';
			if($botman_answer){
				$this->say('This question was asked earlier and the answer was - <br />'. $botman_answer->answer);
				$this->say($related_docs_link);
				return $this->ask('Ask another question.', $botSearch);
			}

			//$this->say(implode(",",$keywords));
			$request = new \Illuminate\Http\Request;
			$request->merge(['search'=>['value'=>implode(",",$keywords)], 'length'=>10, 'return_format'=>'raw']);
			$search_results = $this_controller->search($request);
			$documents_array = json_decode($search_results);	

				Log::debug('Got '.count($documents_array->data). ' documents.');
				if(count($documents_array->data) == 0){
					$this->say('I did not get any documents to answer your question from.');
					return $this->ask('Try rephrasing your question.', $botSearch);
				}

				//$this->say(count($documents_array->data).' documents to be scanned.');
				
				$doc_list = '';
				$chunks = [];
				foreach($documents_array->data as $d){
					$info_from_doc = '';
					$doc = Document::find($d->id);
					$info_from_doc .= $doc->title."\n";
					$info_from_doc .= $doc->text_content."\n";
					$meta_info = '';
					foreach($doc->meta as $meta_value){
						if(empty($meta_value->meta_field) || empty($doc->meta_value($meta_value->meta_field_id, true))) continue;
						$meta_info .= $meta_value->meta_field->label.': '.strip_tags($doc->meta_value($meta_value->meta_field_id))."\n";
					}
					$info_from_doc .= $meta_info;

					$chunks_doc = Util::createTextChunks($info_from_doc, 4000, 1000);
					//$chunks_doc = Util::createTextChunks($info_from_doc, 1500, 300);
					foreach($chunks_doc as $c){
						$c = "====DOC-".$doc->id."-====\n".$c;
						$chunks[] = $c;
					}
				}

				//$this->say($chunks[0]);
				// remove Page \d\d from the string
				$matches = Util::findMatches($chunks, $keywords);
				//$this->say('Found '.count($matches). ' matches.');
				$matches_details = '';
				// take first 5 
				$matches = array_slice($matches, 0, 5);
				//$matches_details .= $chunks[0];
				$docs_containing_answer = [];
				if(count($matches) == 0){
					//$this->say('Found '.count($documents_array->data).' documents that look relevant but could not answer your question.');
					return $this->ask('Try rephrasing your question.', $botSearch);
				}
				else{
					// show answer here
					$answer_full = '';
					$open_ai_req_cnt = 0;
					foreach($matches as $chunk_id => $score){
						$open_ai_req_cnt++;
						try{
							$this_controller->chatgpt = new ChatGPT( env("OPENAI_API_KEY") );
							$answer = $this_controller->answerQuestion( $chunks[$chunk_id], $question );
							if( $answer !== false && !empty($answer->content)) {
								$answer_full .= $answer->content;
								// which chunk contains the answer ?
								$chunk_containing_answer = $chunk_id;
								$pattern = '/====DOC-(\d\d*)-====/';
								preg_match($pattern, $chunks[$chunk_id], $doc_matches);
								//$this->say(count($doc_matches). ' - '.serialize($doc_matches));
								array_shift($doc_matches);
								break;
        					}
							else{
								//$answer_full = 'Did not get any answer';
							}
						}
						catch(\Exception $e){
							$this->say($e->getMessage());
							break;
						}
						if($open_ai_req_cnt == 1){
							//$botman->reply('I am still looking for an answer to your question.');
						}
						else{
							//$botman->reply('Be patient.');
						}
						Log::debug('OpenAI request #'. $open_ai_req_cnt);
					}
					if(empty($answer_full)){
						$this->say('I did not get an answer to your query.');
						return $this->ask('Please see if you can find any documents 
							<a target="_new" href="/collection/1?isa_search_parameter='.
							urlencode(implode(' ',$keywords)).'">here</a>.', $botSearch);
						//$this->ask('Try making your question more specific.', $botSearch);
					}
					else{
						foreach($doc_matches as $dm){
							$m_doc = Document::find($dm);
							$doc_list .= '<a target="_new" href="/collection/'.$m_doc->collection->id.'/document/'.$m_doc->id.'">'.$m_doc->title.'</a><br/>';
						}
						$answer_full .= '<br/><br/>Reference - <br />'.$doc_list;
						$this->say($answer_full);
						$this->say($related_docs_link);
						// log q and a here
						$botman_answer = new BotmanAnswer;
						$botman_answer->question = ltrim(rtrim(preg_replace('!\s+!', ' ', $question)));
						$botman_answer->keywords = implode(' ',array_sort($keywords));	
						$botman_answer->answer = $answer_full;
						$botman_answer->save();
						return $this->ask('Type in another question.', $botSearch);
					}
				}
			//}else{
				//$this->say('There was some error. Please try again.');
			//}
        };

		$botman->ask('Type in your question.', $botSearch);
    }

	function answer_not_found( bool $not_found = true ) {

	}

	public function answerQuestion( string $chunk, string $question ) {
		try{
			$chatgpt = $this->chatgpt;
    		$chatgpt->smessage( "The user will give you an excerpt from a document. Answer the question based on the information in the excerpt." );
    		$chatgpt->umessage( "### EXCERPT FROM DOCUMENT:\n\n$chunk" );
    		$chatgpt->umessage( $question );
	
    		$response = $chatgpt->response( true );
	
    		if( isset( $response->function_call ) ) {
        		return false;
    		}
	
    		if( empty( $response->content ) ) {
        		return false;
    		}
	
    		if( $chatgpt->version() < 4 && ! $this->gpt3_check( $question, $response->content ) ) {
        		return false;
    		}
    		return $response;
		}
		catch(\Exception $e){
			Log::debug($e->getMessage());
			return false;
		}
	}

	public function gpt3_check( $question, $answer ) {
    	$chatgpt = new ChatGPT( getenv("OPENAI_API_KEY") );
    	$chatgpt->umessage( "Question: \"$question\"\nAnswer: \"$answer\"\n\nAnswer YES if the answer is similar to 'the answer to the question was not found in the information provided' or 'the excerpt does not mention that'. Answer only YES or NO" );
    	$response = $chatgpt->response();

    	return stripos( $response->content, "yes" ) === false;
	}
}
