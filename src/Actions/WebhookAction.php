<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\WebhookService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;

class WebhookAction
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function __invoke()
    {
        $request = Request::createFromGlobals();
        $event = $request->server->get('HTTP_X_GITHUB_EVENT');

        if (!$this->webhookService->eventApproved($event)) {
            return response()->json(['message' => 'Event not approved'], 400);
        }

        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['action'])) {
            return response()->json(['message' => 'Not an action event'], 400);
        }

        $issueOrPrNodeId = $payload['projects_v2_item']['content_node_id'] ?? null;
        $fieldData = $payload['changes']['field_value'] ?? null;
        $editor = $payload['sender']['login'] ?? 'Unknown';

        if (!$issueOrPrNodeId || !$fieldData) {
            return response()->json(['message' => 'Missing required fields'], 400);
        }

        $fieldName = $fieldData['field_name'] ?? 'Unknown Field';
        $fromValue = $fieldData['from']['name'] ?? 'Unknown';
        $toValue = $fieldData['to']['name'] ?? 'Unknown';

        $message = "**$editor** updated **$fieldName** from **$fromValue** to **$toValue**.";
        Log::info('message', [
            'message' => $message,
        ]);
        $this->webhookService->commentOnIssueOrPR($issueOrPrNodeId, $message);

        return response()->json(['message' => 'Comment added'], 200);
    }
}
