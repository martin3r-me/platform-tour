<?php

namespace Platform\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Ein Regie-Schritt einer Tour: navigiert (optional) und zeigt einen Kommentar.
 */
class TourStep extends Model
{
    protected $fillable = [
        'tour_id', 'position', 'navigate_url', 'title', 'message', 'highlight_selector',
        'action_tool', 'action_arguments',
    ];

    protected $casts = [
        'position'         => 'integer',
        'action_arguments' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (TourStep $step) {
            if (empty($step->uuid)) {
                $step->uuid = (string) Str::uuid();
            }
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}
