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


    public function isMyFavorite(){
        return $this->user_id === auth()->id();
    }


    public static function isFavorited($user_id, $document_id){
        return static::where('user_id', $user_id)
                     ->where('document_id', $document_id)
                     ->exists();
    }


    public static function toggle($user_id, $document_id){
        $favorite = static::where('user_id', $user_id)
                         ->where('document_id', $document_id)
                         ->first();

        if ($favorite) {
            // Unfavorite if it exists
            $favorite->delete();
            return false; 
        } else {
            // Favorite if it doesn't exist
            static::create([
                'user_id' => $user_id,
                'document_id' => $document_id,
            ]);
            return true;
        }
    }
}
