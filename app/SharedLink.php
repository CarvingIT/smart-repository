<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'token',
        'password',
        'expires_at',
        'permission_level',
        'description',
        'is_active',
        'downloadable',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'downloadable' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
