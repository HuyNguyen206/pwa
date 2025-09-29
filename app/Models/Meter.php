<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    /** @use HasFactory<\Database\Factories\MeterFactory> */
    use HasFactory;

    protected $fillable = [
        'code','name',
        'location_lat',
        'location_lng',
        'unit',
    ];

    public function readings(): HasMany {
        return $this->hasMany(Reading::class);
    }
}
