<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MetaFieldSeries extends Model
{
    protected $table = 'meta_field_series';
    protected $fillable = [
        'meta_field_id', 'prefix', 'date_format', 'next_sequence', 'pad_length', 'reset_period'
    ];

    public function meta_field(){
        return $this->belongsTo('App\MetaField');
    }

    /**
     * Generate next series value and increment counter safely.
     * Returns generated string.
     */
    public function generateNext(){
        return DB::transaction(function() {
            $series = static::where('id', $this->id)->lockForUpdate()->first();
            if(!$series) return null;
            $seq = $series->next_sequence;
            $parts = [];
            if(!empty($series->prefix)) $parts[] = $series->prefix;
            if(!empty($series->date_format)) $parts[] = date($series->date_format);
            $num = str_pad($seq, intval($series->pad_length ?: 0), '0', STR_PAD_LEFT);
            $parts[] = $num;
            $series->next_sequence = $seq + 1;
            $series->save();
            return implode('/', $parts);
        });
    }
}
