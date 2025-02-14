<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class WebhookAction
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): JsonResponse
    {
        $request = Request::createFromGlobals();

        if (!$this->webhookService->eventRequestApproved($request)) {
            return response()->json(['message' => __('github-project::github-project.error.event.denied')], 403);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode($request->getContent(), true);

        $validationResponse = $this->webhookService->validatePayload($payload);
        if ($validationResponse !== null) {
            return $validationResponse;
        }

        $message = view('github-project::md.comment', compact('payload'))->render();

        $this->webhookService->commentOnNode((string) $payload['projects_v2_item']['content_node_id'], $message);

        return response()->json(['message' => __('github-project::github-project.success.message')]);
    }
}
