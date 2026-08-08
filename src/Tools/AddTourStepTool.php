<?php

namespace Platform\Tour\Tools;

use Platform\Core\Contracts\ToolContract;
use Platform\Core\Contracts\ToolContext;
use Platform\Core\Contracts\ToolMetadataContract;
use Platform\Core\Contracts\ToolResult;
use Platform\Tour\Models\Tour;
use Platform\Tour\Models\TourStep;

class AddTourStepTool implements ToolContract, ToolMetadataContract
{
    public function getName(): string
    {
        return 'tour.steps.POST';
    }

    public function getDescription(): string
    {
        return 'POST /tour/steps - Fügt einer Tour einen Regie-Schritt hinzu. REQUIRED: tour_id, message. '
            . 'Optional: navigate (Pfad, z.B. "/encounter/appointments/9" — beamt den Zuschauer dorthin), '
            . 'title, position (Default: ans Ende). Reihenfolge = position aufsteigend.';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'tour_id'  => ['type' => 'integer', 'description' => 'ID der Tour (REQUIRED).'],
                'message'  => ['type' => 'string', 'description' => 'Kommentar-Text des Schritts (REQUIRED).'],
                'navigate' => ['type' => 'string', 'description' => 'Optionaler Zielpfad für diesen Schritt.'],
                'title'    => ['type' => 'string', 'description' => 'Optionale Überschrift.'],
                'position' => ['type' => 'integer', 'description' => 'Optionale Reihenfolge (Default: ans Ende).'],
                'action_tool'      => ['type' => 'string', 'description' => 'Optional: Tool, das beim "Weiter" von diesem Schritt im Kontext des Zuschauers ausgeführt wird (z.B. "planner.tasks.POST"). Nur additive/sichere Aktionen.'],
                'action_arguments' => ['type' => 'object', 'description' => 'Optional: Argumente für action_tool (JSON-Objekt).'],
            ],
            'required' => ['tour_id', 'message'],
        ];
    }

    public function execute(array $arguments, ToolContext $context): ToolResult
    {
        $teamId = $context->team?->id ?? $context->user?->currentTeam?->id;
        if (!$teamId) {
            return ToolResult::error('NO_TEAM', 'Kein Team im Kontext.');
        }

        $tourId = (int) ($arguments['tour_id'] ?? 0);
        $tour = Tour::forTeam((int) $teamId)->find($tourId);
        if (!$tour) {
            return ToolResult::error('NOT_FOUND', 'Tour nicht gefunden.');
        }

        $message = trim((string) ($arguments['message'] ?? ''));
        if ($message === '') {
            return ToolResult::error('VALIDATION_ERROR', 'message ist erforderlich.');
        }

        $position = isset($arguments['position']) && (int) $arguments['position'] > 0
            ? (int) $arguments['position']
            : ((int) TourStep::where('tour_id', $tour->id)->max('position') + 1);

        $step = TourStep::create([
            'tour_id'            => $tour->id,
            'position'           => $position,
            'navigate_url'       => isset($arguments['navigate']) && $arguments['navigate'] !== '' ? (string) $arguments['navigate'] : null,
            'title'              => isset($arguments['title']) && $arguments['title'] !== '' ? (string) $arguments['title'] : null,
            'message'            => $message,
            'action_tool'        => isset($arguments['action_tool']) && $arguments['action_tool'] !== '' ? (string) $arguments['action_tool'] : null,
            'action_arguments'   => isset($arguments['action_arguments']) && is_array($arguments['action_arguments']) ? $arguments['action_arguments'] : null,
        ]);

        return ToolResult::success([
            'id'       => $step->id,
            'tour_id'  => $tour->id,
            'position' => $step->position,
        ]);
    }

    public function getMetadata(): array
    {
        return [
            'read_only' => false, 'category' => 'action', 'tags' => ['tour', 'regie', 'step', 'create'],
            'risk_level' => 'write', 'requires_auth' => true, 'requires_team' => true, 'idempotent' => false,
        ];
    }
}
