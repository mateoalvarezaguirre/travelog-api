<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TripWaypointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripWaypoint extends Model
{
    /** @use HasFactory<TripWaypointFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'trip_id',
        'sequence',
        'display_name',
        'lat',
        'lng',
        'provider',
        'place_id',
        'country_code',
        'address_json',
        'date_from',
        'date_to',
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
            'lat'          => 'float',
            'lng'          => 'float',
            'sequence'     => 'integer',
            'address_json' => 'array',
            'date_from'    => 'date',
            'date_to'      => 'date',
        ];
    }
}
