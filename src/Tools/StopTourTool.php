<?php

namespace Platform\Tour\Tools;

use Platform\Core\Contracts\ToolContract;
use Platform\Core\Contracts\ToolContext;
use Platform\Core\Contracts\ToolMetadataContract;
use Platform\Core\Contracts\ToolResult;
use Platform\Tour\Models\TourRun;

class StopTourTool implements ToolContract, ToolMetadataContract
{
    public function getName(): string
    {
        return 'tour.stop';
    }

    public function getDescription(): string
    {
        return 'POST /tour/stop - Beendet die laufende Tour eines Zuschauers. Optional: user_id (Default: aktueller User).';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'user_id' => ['type' => 'integer', 'description' => 'Optional: Zuschauer-User-ID (Default: aktueller User).'],
            ],
            'required' => [],
        ];
    }

    public function execute(array $arguments, ToolContext $context): ToolResult
    {
        $teamId = $context->team?->id ?? $context->user?->currentTeam?->id;
        if (!$teamId) {
            return ToolResult::error('NO_TEAM', 'Kein Team im Kontext.');
        }
        $userId = (int) ($arguments['user_id'] ?? $context->user?->id ?? 0);
        if (!$userId) {
            return ToolResult::error('NO_USER', 'Kein Zuschauer-User bestimmbar.');
        }

        $count = TourRun::where('user_id', $userId)->where('team_id', (int) $teamId)
            ->where('status', 'running')->update(['status' => 'done']);

        return ToolResult::success(['stopped' => $count, 'user_id' => $userId]);
    }

    public function getMetadata(): array
    {
        return [
            'read_only' => false, 'category' => 'action', 'tags' => ['tour', 'regie', 'stop'],
            'risk_level' => 'write', 'requires_auth' => true, 'requires_team' => true, 'idempotent' => true,
        ];
    }
}
