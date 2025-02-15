<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\GithubService;
use CSlant\GitHubProject\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class WebhookAction
{
    protected WebhookService $webhookService;
    protected GithubService $githubService;

    public function __construct(WebhookService $webhookService, GithubService $githubService)
    {
        $this->webhookService = $webhookService;
        $this->githubService = $githubService;
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

        $response = $this->githubService->handleComment((string) $payload['projects_v2_item']['content_node_id'], $message);

        return response()->json([
            'message' => __('github-project::github-project.success.message'),
            'response' => $response,
        ]);
    }
}
