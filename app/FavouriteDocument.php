<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id'
    ];

    /**
     * Get the user that owns the favourite document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document that is favourited.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
