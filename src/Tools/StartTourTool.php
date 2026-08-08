<?php

namespace Platform\Tour\Tools;

use Platform\Core\Contracts\ToolContract;
use Platform\Core\Contracts\ToolContext;
use Platform\Core\Contracts\ToolMetadataContract;
use Platform\Core\Contracts\ToolResult;
use Platform\Tour\Models\Tour;
use Platform\Tour\Models\TourRun;
use Platform\Tour\Models\TourStep;

class StartTourTool implements ToolContract, ToolMetadataContract
{
    public function getName(): string
    {
        return 'tour.start';
    }

    public function getDescription(): string
    {
        return 'POST /tour/start - Startet eine Tour für einen Zuschauer: dessen Browser-Overlay zeigt ab '
            . 'sofort Schritt 1 und "Weiter" schaltet vor. REQUIRED: tour_id. Optional: user_id '
            . '(Default: aktueller User). Beendet vorherige laufende Touren des Zuschauers.';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'tour_id' => ['type' => 'integer', 'description' => 'ID der zu startenden Tour (REQUIRED).'],
                'user_id' => ['type' => 'integer', 'description' => 'Optional: Zuschauer-User-ID (Default: aktueller User).'],
            ],
            'required' => ['tour_id'],
        ];
    }

    public function execute(array $arguments, ToolContext $context): ToolResult
    {
        $teamId = $context->team?->id ?? $context->user?->currentTeam?->id;
        if (!$teamId) {
            return ToolResult::error('NO_TEAM', 'Kein Team im Kontext.');
        }

        $tour = Tour::forTeam((int) $teamId)->find((int) ($arguments['tour_id'] ?? 0));
        if (!$tour) {
            return ToolResult::error('NOT_FOUND', 'Tour nicht gefunden.');
        }

        $total = TourStep::where('tour_id', $tour->id)->count();
        if ($total === 0) {
            return ToolResult::error('EMPTY_TOUR', 'Diese Tour hat noch keine Schritte.');
        }

        $userId = (int) ($arguments['user_id'] ?? $context->user?->id ?? 0);
        if (!$userId) {
            return ToolResult::error('NO_USER', 'Kein Zuschauer-User bestimmbar.');
        }

        // Vorherige laufende Ablaeufe des Zuschauers beenden.
        TourRun::where('user_id', $userId)->where('team_id', (int) $teamId)
            ->where('status', 'running')->update(['status' => 'done']);

        $run = TourRun::create([
            'tour_id'          => $tour->id,
            'user_id'          => $userId,
            'team_id'          => (int) $teamId,
            'current_position' => 1,
            'status'           => 'running',
        ]);

        return ToolResult::success([
            'run_id'  => $run->id,
            'tour'    => $tour->name,
            'steps'   => $total,
            'user_id' => $userId,
            'message' => "Tour '{$tour->name}' gestartet ({$total} Schritte).",
        ]);
    }

    public function getMetadata(): array
    {
        return [
            'read_only' => false, 'category' => 'action', 'tags' => ['tour', 'regie', 'start', 'play'],
            'risk_level' => 'write', 'requires_auth' => true, 'requires_team' => true, 'idempotent' => false,
        ];
    }
}
