<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DocumentComment;
use App\Collection;
use Session;
use Auth;

class CommentController extends Controller
{
    /**
     * Save a new comment on a document.
     * Checks that comments are enabled for the collection first.
     */
    public function save(Request $request)
    {
        $collection = Collection::find($request->collection_id);

        // Check if comments are enabled for this collection
        $col_config = json_decode($collection->column_config ?? '{}');
        if (empty($col_config->comments_enabled) || $col_config->comments_enabled != 1) {
            Session::flash('alert-danger', 'Comments are disabled for this collection.');
            return redirect("/collection/{$request->collection_id}/document/{$request->document_id}/details");
        }

        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $comment = new DocumentComment;
        $comment->document_id  = $request->document_id;
        $comment->collection_id = $request->collection_id;
        $comment->created_by   = Auth::id();
        $comment->comment      = $request->comment;

        try {
            $comment->save();
            Session::flash('alert-success', 'Comment posted successfully!');
        } catch (\Exception $e) {
            Session::flash('alert-danger', 'Error: ' . $e->getMessage());
        }

        return redirect("/collection/{$request->collection_id}/document/{$request->document_id}/details#comments");
    }

    /**
     * Delete a comment. Only the author or an admin may do this.
     */
    public function delete(Request $request, $comment_id)
    {
        $comment = DocumentComment::findOrFail($comment_id);

        if (!$comment->canBeDeletedBy(Auth::user())) {
            Session::flash('alert-danger', 'You do not have permission to delete this comment.');
            return redirect("/collection/{$comment->collection_id}/document/{$comment->document_id}/details#comments");
        }

        $collection_id = $comment->collection_id ?? ($comment->document ? $comment->document->collection_id : null);
        $document_id   = $comment->document_id;

        $comment->delete();
        Session::flash('alert-success', 'Comment deleted.');

        return redirect("/collection/{$collection_id}/document/{$document_id}/details#comments");
    }
}
