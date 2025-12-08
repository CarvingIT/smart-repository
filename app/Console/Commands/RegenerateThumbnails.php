<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\Collection;
use App\Services\ThumbnailService;
use Illuminate\Support\Facades\Log;

class RegenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SR:RegenerateThumbnails {collection_id : ID of the collection or "all" for all collections}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerates PDF thumbnails for documents in a collection';

    /**
     * Thumbnail service instance
     *
     * @var ThumbnailService
     */
    protected $thumbnailService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->thumbnailService = new ThumbnailService();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(!env('ENABLE_PDF_THUMBNAILS')){
            echo "The feature of PDF thumbnails is not active.\n";
            exit;
        }
        $collection_id = $this->argument('collection_id');
        
        if ($collection_id == 'all') {
            $collections = Collection::where('content_type', 'Uploaded documents')->get();
            
            $this->info('Regenerating thumbnails for all collections...');
            
            foreach ($collections as $collection) {
                $this->regenerateCollectionThumbnails($collection);
            }
            
            $this->info('Completed thumbnail regeneration for all collections.');
        } else {
            $collection = Collection::find($collection_id);
            
            if (!$collection) {
                $this->error("Collection with ID {$collection_id} not found.");
                return 1;
            }
            
            if ($collection->content_type !== 'Uploaded documents') {
                $this->error("Collection must be of type 'Uploaded documents'.");
                return 1;
            }
            
            $this->regenerateCollectionThumbnails($collection);
        }
        
        return 0;
    }

    /**
     * Regenerate thumbnails for a specific collection
     *
     * @param Collection $collection
     * @return void
     */
    protected function regenerateCollectionThumbnails($collection)
    {
        $this->info("Processing collection: {$collection->name} (ID: {$collection->id})");
        
        // Get all PDF documents in the collection
        $documents = Document::where('collection_id', $collection->id)
            ->where(function($query) {
                $query->where('type', 'application/pdf')
                      ->orWhere('type', 'like', '%application/pdf%');
            })
            ->get();
        
        if ($documents->isEmpty()) {
            $this->info("No PDF documents found in this collection.");
            return;
        }
        
        $this->info("Found {$documents->count()} PDF document(s). Generating thumbnails...");
        
        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();
        
        $success = 0;
        $failed = 0;
        
        foreach ($documents as $document) {
            try {
                $result = $this->thumbnailService->regenerateThumbnail($document);
                
                if ($result) {
                    $success++;
                } else {
                    $failed++;
                    Log::warning("Failed to generate thumbnail for document ID: {$document->id}");
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Error generating thumbnail for document ID {$document->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Thumbnail generation completed for collection '{$collection->name}':");
        $this->info("  - Success: {$success}");
        $this->info("  - Failed: {$failed}");
        $this->newLine();
    }
}
