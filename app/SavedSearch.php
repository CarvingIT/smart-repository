<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $table = 'saved_searches';

    protected $fillable = [
        'user_id',
        'collection_id',
        'name',
        'query',
    ];

    protected $casts = [
        'query' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this saved search.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the collection this saved search belongs to.
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
