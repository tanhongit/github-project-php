<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\GithubService;
use CSlant\GitHubProject\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Throwable;

class GenerateCommentAction
{
    protected WebhookService $webhookService;

    protected GithubService $githubService;

    public function __construct(WebhookService $webhookService, GithubService $githubService)
    {
        $this->webhookService = $webhookService;
        $this->githubService = $githubService;
    }

    /**
     * Generate a comment message from the webhook payload
     *
     * @param  array<string, mixed>|object  $payload
     * @param  bool  $validate  Whether to validate the payload (default: true)
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function __invoke(array|object $payload, bool $validate = true): JsonResponse
    {
        try {
            $payload = is_object($payload) ? (array) $payload : $payload;

            if ($validate) {
                $validationResponse = $this->webhookService->validatePayload($payload);
                if ($validationResponse !== null) {
                    return $validationResponse;
                }
            }

            $comment = $this->githubService->generateCommentMessage($payload);

            return response()->json([
                'success' => true,
                'message' => __('github-project::github-project.success.message'),
                'comment' => $comment,
                'payload' => $payload,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('github-project::github-project.error.message', ['error' => $e->getMessage()]),
                'comment' => '',
                'payload' => $payload,
            ], 500);
        }
    }
}
