<?php

namespace Platform\Tour\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Eine geführte Tour: geordnete Regie-Schritte, die im Presenter-Overlay ablaufen.
 */
class Tour extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id', 'name', 'description', 'status', 'share_token', 'created_by_user_id', 'position',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tour $tour) {
            if (empty($tour->uuid)) {
                $tour->uuid = (string) Str::uuid();
            }
            if (empty($tour->share_token)) {
                $tour->share_token = Str::random(40);
            }
        });
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function steps()
    {
        return $this->hasMany(TourStep::class)->orderBy('position');
    }

    public function runs()
    {
        return $this->hasMany(TourRun::class);
    }
}
