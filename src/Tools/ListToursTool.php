<?php

namespace Platform\Tour\Tools;

use Platform\Core\Contracts\ToolContract;
use Platform\Core\Contracts\ToolContext;
use Platform\Core\Contracts\ToolMetadataContract;
use Platform\Core\Contracts\ToolResult;
use Platform\Tour\Models\Tour;

class ListToursTool implements ToolContract, ToolMetadataContract
{
    public function getName(): string
    {
        return 'tour.tours.GET';
    }

    public function getDescription(): string
    {
        return 'GET /tour/tours - Listet die Touren des Teams mit Schrittzahl und Status.';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];
    }

    public function execute(array $arguments, ToolContext $context): ToolResult
    {
        $teamId = $context->team?->id ?? $context->user?->currentTeam?->id;
        if (!$teamId) {
            return ToolResult::error('NO_TEAM', 'Kein Team im Kontext.');
        }

        $tours = Tour::forTeam((int) $teamId)->withCount('steps')->orderByDesc('id')->get()
            ->map(fn ($t) => [
                'id'          => $t->id,
                'name'        => $t->name,
                'status'      => $t->status,
                'steps'       => $t->steps_count,
                'share_token' => $t->share_token,
            ])->all();

        return ToolResult::success(['data' => $tours, 'total' => count($tours)]);
    }

    public function getMetadata(): array
    {
        return [
            'read_only' => true, 'category' => 'query', 'tags' => ['tour', 'regie', 'list'],
            'risk_level' => 'read', 'requires_auth' => true, 'requires_team' => true, 'idempotent' => true,
        ];
    }
}
