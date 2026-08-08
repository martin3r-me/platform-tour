<?php

namespace Platform\Tour\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Der aktive Ablauf einer Tour für einen Zuschauer: an welchem Schritt er gerade steht.
 */
class TourRun extends Model
{
    protected $fillable = [
        'tour_id', 'user_id', 'team_id', 'current_position', 'status',
    ];

    protected $casts = [
        'current_position' => 'integer',
    ];

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
