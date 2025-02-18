<?php

namespace CSlant\GitHubProject\Services;

use Github\Client;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WebhookService
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function eventRequestApproved(Request $request): bool
    {
        $event = $request->server->get('HTTP_X_GITHUB_EVENT');

        return $this->eventApproved((string) $event);
    }

    protected function eventApproved(string $event): bool
    {
        return str_contains($event, 'project');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function isActionPresent(array $payload): bool
    {
        return isset($payload['action']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function hasValidNodeAndFieldData(array $payload): bool
    {
        return isset($payload['projects_v2_item']['content_node_id'], $payload['changes']['field_value']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function hasFieldTemplate(array $payload): bool
    {
        $fieldType = $payload['changes']['field_value']['field_type'] ?? '';

        return view()->exists('github-project::md.field_types.'.$fieldType);
    }

    protected function createErrorResponse(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json(['message' => __($message)], $statusCode);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return null|JsonResponse
     */
    public function validatePayload(array $payload): ?JsonResponse
    {
        if (!$this->isActionPresent($payload)) {
            return $this->createErrorResponse('github-project::github-project.error.event.action_not_found', 404);
        }

        if (!$this->isStatusCommentEnabled($payload)) {
            return $this->createErrorResponse('github-project::github-project.error.event.status_comment_disabled');
        }

        if (!$this->hasValidNodeAndFieldData($payload)) {
            return $this->createErrorResponse('github-project::github-project.error.event.missing_fields');
        }

        if (!$this->hasFieldTemplate($payload)) {
            return $this->createErrorResponse('github-project::github-project.error.event.missing_field_template');
        }

        return null;
    }

    /**
     * Check if the field name is "Status" and if status comments are enabled.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return bool
     */
    protected function isStatusCommentEnabled(array $payload): bool
    {
        $fieldType = $payload['changes']['field_value']['field_type'] ?? '';

        if ((string) $fieldType !== 'Status'
            && !config('github-project.enable_status_comment')
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param  string  $contentNodeId
     * @param  string  $message
     *
     * @return array<string, mixed>
     */
    public function commentOnNode(string $contentNodeId, string $message): array
    {
        $query = <<<'GRAPHQL'
            mutation($input: AddCommentInput!) {
                addComment(input: $input) {
                    commentEdge {
                        node {
                            id
                            body
                        }
                    }
                }
            }
            GRAPHQL;

        $variables = [
            'input' => [
                'subjectId' => $contentNodeId,
                'body' => $message,
            ],
        ];

        return $this->client->graphql()->execute($query, $variables);
    }
}
