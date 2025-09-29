<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reading extends Model
{
    /** @use HasFactory<\Database\Factories\ReadingFactory> */
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'value',
        'noted_at',
        'notes',
    ];

    protected $casts = [
        'noted_at' => 'datetime',
    ];

    public function meter(): BelongsTo {
        return $this->belongsTo(Meter::class);
    }
}
