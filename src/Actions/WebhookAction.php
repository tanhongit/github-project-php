<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WebhookAction
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function __invoke(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $event = $request->server->get('HTTP_X_GITHUB_EVENT');

        if (!$this->webhookService->eventApproved($event)) {
            return response()->json(['message' => __('github-project::github-project.error.event.denied')], 403);
        }

        /** @var array<string, list<string, mixed>> $payload */
        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['action'])) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.action_not_found')],
                400
            );
        }

        $nodeId = $payload['projects_v2_item']['content_node_id'] ?? null;
        $fieldData = $payload['changes']['field_value'] ?? null;
        $editor = $payload['sender']['login'] ?? 'Unknown';

        if (!$nodeId || !$fieldData) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.missing_fields')],
                400
            );
        }

        $fieldName = $fieldData['field_name'] ?? 'Unknown Field';
        $fromValue = $fieldData['from']['name'] ?? 'Unknown';
        $toValue = $fieldData['to']['name'] ?? 'Unknown';

        $message = view(
            'github-project::md.comment',
            compact('editor', 'fieldName', 'fromValue', 'toValue')
        )->render();

        $this->webhookService->commentOnNode((string) $nodeId, $message);

        return response()->json(['message' => __('github-project::github-project.success.message')]);
    }
}
