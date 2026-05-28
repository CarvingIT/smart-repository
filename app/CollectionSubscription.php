<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectionSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'collection_id',
        'user_email',
        'user_name',
        'challan',
        'amount',
        'payment_status',
        'payment_reference',
        'till_date',
        'payment_payload',
        'reconciliation_payload',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'till_date' => 'date',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}