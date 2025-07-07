<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\GithubService;
use CSlant\GitHubProject\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param  Request  $request
     * @param  bool  $validate  Whether to validate the payload (default: true)
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function __invoke(Request $request, bool $validate = true): JsonResponse
    {
        $startTime = microtime(true);

        try {
            if ($request->isJson()) {
                $jsonContent = $request->json();
                $payload = is_object($jsonContent) && method_exists($jsonContent, 'all')
                    ? $jsonContent->all()
                    : (array) $jsonContent;
            } else {
                $payload = json_decode($request->getContent(), true);
            }

            if (!empty($payload['payload'])) {
                $payload = $payload['payload'];
            }

            if (is_string($payload)) {
                $payload = json_decode($payload, true);
            }

            if ($validate) {
                $validationResponse = $this->webhookService->validatePayloadForComment($payload);
                if ($validationResponse !== null) {
                    return $validationResponse;
                }
            }

            $comment = $this->githubService->generateCommentMessage($payload);

            return response()->json([
                'success' => true,
                'message' => __('github-project::github-project.success.message'),
                'comment' => $comment,
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing request',
                'error' => [
                    'message' => $e->getMessage(),
                    'type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString()),
                ],
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            ], 500);
        }
    }
}
