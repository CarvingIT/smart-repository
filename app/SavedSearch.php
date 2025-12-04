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

    /**
     * Check if the current user owns this saved search.
     */
    public function isOwner()
    {
        return $this->user_id === auth()->id();
    }

    /**
     * Get saved searches for a specific user and collection.
     */
    public static function forUserAndCollection($userId, $collectionId)
    {
        return static::where('user_id', $userId)
                     ->where('collection_id', $collectionId)
                     ->orderBy('created_at', 'desc')
                     ->get();
    }

    /**
     * Get all saved searches for a user.
     */
    public static function forUser($userId)
    {
        return static::where('user_id', $userId)
                     ->with('collection')
                     ->orderBy('created_at', 'desc')
                     ->get();
    }

    /**
     * Check if user is admin or has highest role.
     */
    public static function isAdminUser($user)
    {
        // Check if user has admin role or is_admin flag
        return $user->is_admin == 1 || $user->hasRole('admin') || $user->hasRole('superadmin');
    }

    /**
     * Get all saved searches (for admin view).
     */
    public static function getAllSearches()
    {
        return static::with(['collection', 'user'])
                     ->orderBy('created_at', 'desc')
                     ->get();
    }
}
