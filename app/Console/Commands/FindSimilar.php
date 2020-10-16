<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
//use NlpTools\Tokenizers\WhitespaceAndPunctuationTokenizer;
//use \NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Similarity\CosineSimilarity;
use App\Curation;
use App\SimilarDocument;

class FindSimilar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:FindSimilar {document_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finds similar documents';

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
	   $document_id = $this->argument('document_id');
	   $document = Document::find($document_id);
	   // source tokens 
	   $s_token_counts = Curation::getWordWeights($document->text_content);

	   $collection_id = $document->collection->id;
	   //echo $collection_id."\n";
	   $docs = Document::where('collection_id', $collection_id)->get();
	   foreach($docs as $d){
		$token_counts = Curation::getWordWeights($d->text_content);
		$cos_sim = new CosineSimilarity();
		try{
			$cosine = $cos_sim->similarity($s_token_counts, $token_counts);
			$similar_doc = SimilarDocument::where('document_id', $document_id)
				->where('target_document_id', $d->id)->first();
			if(!$similar_doc){
				$similar_doc = new SimilarDocument;
			}
			$similar_doc->document_id = $document->id;
			$similar_doc->target_document_id = $d->id;
			$similar_doc->cosine_similarity = $cosine;
			$similar_doc->source_updated_at = $document->updated_at;
			$similar_doc->target_updated_at = $d->updated_at;
			$similar_doc->save();
			//echo $cosine."\n";
		}
		catch(\Exception $e){
			echo $d->id."\t".$e->getMessage()."\n";
		}
	  }
    }
}
