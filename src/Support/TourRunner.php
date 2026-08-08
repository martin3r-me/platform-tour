<?php

namespace Platform\Tour\Support;

use Platform\Tour\Models\Tour;
use Platform\Tour\Models\TourRun;
use Platform\Tour\Models\TourStep;

/**
 * Startet eine Tour für einen Zuschauer: beendet laufende Ablaeufe, legt einen neuen an
 * und liefert die Ziel-URL des ersten Schritts (für den Redirect).
 */
class TourRunner
{
    public static function start(Tour $tour, int $userId, int $teamId): ?string
    {
        $first = TourStep::where('tour_id', $tour->id)->orderBy('position')->first();
        if (!$first) {
            return null;
        }

        TourRun::where('user_id', $userId)->where('team_id', $teamId)
            ->where('status', 'running')->update(['status' => 'done']);

        TourRun::create([
            'tour_id'          => $tour->id,
            'user_id'          => $userId,
            'team_id'          => $teamId,
            'current_position' => 1,
            'status'           => 'running',
        ]);

        return $first->navigate_url ?: null;
    }
}
