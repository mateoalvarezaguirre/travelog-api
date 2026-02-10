<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TripRouteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripRoute extends Model
{
    /** @use HasFactory<TripRouteFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'trip_id';

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'trip_id',
        'polyline_encoded',
        'geojson',
        'distance_meters',
        'duration_seconds',
        'routing_provider',
        'checksum',
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
            'geojson'          => 'array',
            'distance_meters'  => 'integer',
            'duration_seconds' => 'integer',
        ];
    }
}
