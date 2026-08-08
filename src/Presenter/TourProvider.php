<?php

namespace Platform\Tour\Presenter;

use Platform\Core\Contracts\PresenterTourProvider;
use Platform\Tour\Models\TourRun;
use Platform\Tour\Models\TourStep;

/**
 * Liefert dem Core-Overlay den aktiven Tour-Schritt eines Zuschauers aus der DB und
 * schaltet den Ablauf vor. Registriert in der Core-PresenterTourRegistry.
 */
class TourProvider implements PresenterTourProvider
{
    public function activeStep(int $userId, int $teamId): ?array
    {
        $run = $this->runningRun($userId, $teamId);
        if (!$run) {
            return null;
        }

        $steps = TourStep::where('tour_id', $run->tour_id)->orderBy('position')->get();
        if ($steps->isEmpty()) {
            return null;
        }

        $total = $steps->count();
        $idx   = max(1, (int) $run->current_position);

        if ($idx > $total) {
            $run->update(['status' => 'done']);
            return null;
        }

        $step = $steps[$idx - 1];

        return [
            'title'    => $step->title,
            'message'  => (string) $step->message,
            'navigate' => $step->navigate_url,
            'speaker'  => 'Claude',
            'position' => $idx,
            'total'    => $total,
            'is_last'  => $idx >= $total,
        ];
    }

    public function advance(int $userId, int $teamId): void
    {
        $run = $this->runningRun($userId, $teamId);
        if (!$run) {
            return;
        }

        $total = TourStep::where('tour_id', $run->tour_id)->count();
        $next  = (int) $run->current_position + 1;

        if ($next > $total) {
            $run->update(['status' => 'done']);
        } else {
            $run->update(['current_position' => $next]);
        }
    }

    public function stop(int $userId, int $teamId): void
    {
        TourRun::where('user_id', $userId)->where('team_id', $teamId)
            ->where('status', 'running')->update(['status' => 'done']);
    }

    protected function runningRun(int $userId, int $teamId): ?TourRun
    {
        return TourRun::where('user_id', $userId)->where('team_id', $teamId)
            ->where('status', 'running')->latest('id')->first();
    }
}
