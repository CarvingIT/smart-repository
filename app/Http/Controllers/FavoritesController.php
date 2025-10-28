<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Document;
use App\UserFavorite;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $user = Auth::user();

        return view("favorites.index", [
            "user" => $user,
        ]);
    }

    public function data(){
        try {
            $user = Auth::user();
            $favorites = $user->favorites()->with("collection")->get();

            $data = [];
            foreach($favorites as $document){
                $data[] = [
                    "title" => '<a href="/collection/' . $document->collection_id . '/document/' . $document->id . '/details">'
                                . ($document->title ?: "Untitled ") . '</a>',
                    "collection_name" => $document->collection ? $document->collection->name : "N/A",
                    "created_at" => $document->created_at ? $document->created_at->format('Y-M-d') : "N/A",
                    "size" => $document->size ? \App\Util::human_filesize($document->size) : "0 B",
                    "actions" => $this->getActionButtons($document),
                ];
            }

            return response()->json([
                "data" => $data,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ], 500);
        }
    }

    private function getActionButtons($document){
        $buttons = '';
        
        // View details button
        if (env('ENABLE_INFO_PAGE') == 1) {
            $buttons .= '<a class="btn btn-primary btn-link" title="' . __('Information and more') . '" href="/collection/' . $document->collection_id . '/document/' . $document->id . '/details">';
            $buttons .= '<i class="material-icons">forward</i>';
            $buttons .= '</a>';
        }
        
        // Unfavorite button
        $buttons .= '<button type="button" class="btn btn-danger btn-link unfavorite-btn" data-document-id="' . $document->id . '" title="' . __('Remove from favorites') . '">';
        $buttons .= '<i class="material-icons">favorite</i>';
        $buttons .= '</button>';
        
        return $buttons;
    }

    public function toggle(Document $document){
        $user_id = Auth::id();

        $is_now_favorited = UserFavorite::toggle($user_id, $document->id);

        return response()->json([
            "status" => "success",
            "favorited" => $is_now_favorited,
        ]);
    }
}
