<?php

namespace Platform\Tour\Tools;

use Platform\Core\Contracts\ToolContract;
use Platform\Core\Contracts\ToolContext;
use Platform\Core\Contracts\ToolMetadataContract;
use Platform\Core\Contracts\ToolResult;
use Platform\Tour\Models\Tour;

class CreateTourTool implements ToolContract, ToolMetadataContract
{
    public function getName(): string
    {
        return 'tour.tours.POST';
    }

    public function getDescription(): string
    {
        return 'POST /tour/tours - Legt eine neue geführte Tour (Regie-Skript) an. REQUIRED: name. '
            . 'Optional: description, status (draft|active, default draft). Schritte danach mit tour.steps.POST.';
    }

    public function getSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name'        => ['type' => 'string', 'description' => 'Name der Tour (REQUIRED).'],
                'description' => ['type' => 'string', 'description' => 'Optionale Beschreibung.'],
                'status'      => ['type' => 'string', 'enum' => ['draft', 'active'], 'description' => 'Default draft.'],
            ],
            'required' => ['name'],
        ];
    }

    public function execute(array $arguments, ToolContext $context): ToolResult
    {
        $teamId = $context->team?->id ?? $context->user?->currentTeam?->id;
        if (!$teamId) {
            return ToolResult::error('NO_TEAM', 'Kein Team im Kontext.');
        }
        $name = trim((string) ($arguments['name'] ?? ''));
        if ($name === '') {
            return ToolResult::error('VALIDATION_ERROR', 'name ist erforderlich.');
        }

        $tour = Tour::create([
            'team_id'            => (int) $teamId,
            'name'               => $name,
            'description'        => isset($arguments['description']) && $arguments['description'] !== '' ? (string) $arguments['description'] : null,
            'status'             => ($arguments['status'] ?? 'draft') === 'active' ? 'active' : 'draft',
            'created_by_user_id' => $context->user?->id,
        ]);

        return ToolResult::success(['id' => $tour->id, 'name' => $tour->name, 'status' => $tour->status]);
    }

    public function getMetadata(): array
    {
        return [
            'read_only' => false, 'category' => 'action', 'tags' => ['tour', 'regie', 'create'],
            'risk_level' => 'write', 'requires_auth' => true, 'requires_team' => true, 'idempotent' => false,
        ];
    }
}
