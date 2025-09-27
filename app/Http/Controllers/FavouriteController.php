<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FavouriteDocument;
use App\Document;
use Auth;

class FavouriteController extends Controller
{
    /**
     * Add document to favourites
     */
    public function addFavourite(Request $request)
    {
        try {
            $document_id = $request->document_id;
            $user_id = Auth::id();
            
            // Check if document exists
            $document = Document::findOrFail($document_id);
            
            // Check if already favourited
            $existing = FavouriteDocument::where('user_id', $user_id)
                                       ->where('document_id', $document_id)
                                       ->first();
            
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document already in favourites'
                ]);
            }
            
            // Add to favourites
            FavouriteDocument::create([
                'user_id' => $user_id,
                'document_id' => $document_id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Document added to favourites',
                'favourite_count' => $document->favourite_count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding to favourites'
            ]);
        }
    }
    
    /**
     * Remove document from favourites
     */
    public function removeFavourite(Request $request)
    {
        try {
            $document_id = $request->document_id;
            $user_id = Auth::id();
            
            $favourite = FavouriteDocument::where('user_id', $user_id)
                                        ->where('document_id', $document_id)
                                        ->first();
            
            if (!$favourite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not in favourites'
                ]);
            }
            
            $favourite->delete();
            
            $document = Document::find($document_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Document removed from favourites',
                'favourite_count' => $document ? $document->favourite_count : 0
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing from favourites'
            ]);
        }
    }
    
    /**
     * Get user's favourite documents
     */
    public function getFavourites()
    {
        $user_id = Auth::id();
        $favourites = FavouriteDocument::with(['document', 'document.collection'])
                                     ->where('user_id', $user_id)
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        
        return response()->json([
            'success' => true,
            'favourites' => $favourites
        ]);
    }
    
    /**
     * Show favourite documents page
     */
    public function index()
    {
        $user_id = Auth::id();
        $favourites = FavouriteDocument::with(['document', 'document.collection'])
                                     ->where('user_id', $user_id)
                                     ->whereHas('document') // Only include favourites with existing documents
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        
        return view('favourite-documents', [
            'favourites' => $favourites,
            'activePage' => 'favourite-documents',
            'titlePage' => 'Favourite Documents'
        ]);
    }
}
