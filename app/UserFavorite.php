<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model{  
    protected $table = 'user_favourite';

    protected $fillable = [
        'user_id',
        'document_id',
    ];

    protected $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }


    public function document(){
        return $this->belongsTo(Document::class);
    }


    public static function isFavorited($userId, $documentId){
        return static::where('user_id', $userId)
                     ->where('document_id', $documentId)
                     ->exists();
    }


    public static function toggle($userId, $documentId){
        $favorite = static::where('user_id', $userId)
                         ->where('document_id', $documentId)
                         ->first();

        if ($favorite) {
            // Unfavorite if it exists
            $favorite->delete();
            return false; 
        } else {
            // Favorite if it doesn't exist
            static::create([
                'user_id' => $userId,
                'document_id' => $documentId,
            ]);
            return true;
        }
    }
}
