<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentComment extends Model
{
    protected $table = 'document_comments';

    protected $fillable = [
        'comment',
        'created_by',
        'document_id',
        'collection_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo('App\Document', 'document_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Check if the given user is allowed to delete this comment.
     * Admins and the comment author can delete.
     */
    public function canBeDeletedBy($user): bool
    {
        if (!$user) return false;
        return $user->hasRole('admin') || $this->created_by == $user->id;
    }
}

