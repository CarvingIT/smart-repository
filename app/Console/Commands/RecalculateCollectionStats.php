<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateCollectionStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:recalculate-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate collection statistics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $collections = \App\Collection::all();

        foreach ($collections as $collection) {

            $document_count = \App\Document::where("collection_id", $collection->id)
                ->whereNull("deleted_at")
                ->count();


            $size_active = \App\Document::where("collection_id", $collection->id)
                ->whereNull("deleted_at")
                ->sum("size");

            
            $size_revisions = \App\Document::where("collection_id", $collection->id)
                ->whereNull("documents.deleted_at")
                ->join("document_revisions", "documents.id", "=", "document_revisions.document_id")
                ->sum("document_revisions.size");

            
            $size_deleted = \App\Document::where("collection_id", $collection->id)
                ->whereNotNull("documents.deleted_at")
                ->join("document_revisions", "documents.id", "=", "document_revisions.document_id")
                ->sum("document_revisions.size");

            
            $collection->update([
                "document_count" => $document_count,
                "size_active" => $size_active,
                "size_revisions" => $size_revisions,
                "size_deleted" => $size_deleted,
            ]);

            $this->info("Updated collection ID {$collection->id}: document_count={$document_count}, size_active={$size_active}, size_revisions={$size_revisions}, size_deleted={$size_deleted}");

        }

        return Command::SUCCESS;
    }
}
