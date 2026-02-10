<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TripLocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLocation extends Model
{
    /** @use HasFactory<TripLocationFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'trip_id',
        'provider',
        'place_id',
        'display_name',
        'lat',
        'lng',
        'country_code',
        'address_json',
        'bounds_json',
        'timezone',
        'utc_offset_minutes',
    ];

    /**
     * @return BelongsTo<Trip, $this>
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lat'                => 'float',
            'lng'                => 'float',
            'address_json'       => 'array',
            'bounds_json'        => 'array',
            'utc_offset_minutes' => 'integer',
        ];
    }
}
