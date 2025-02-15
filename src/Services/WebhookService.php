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

    public function eventApproved(string $event): bool
    {
        return str_contains($event, 'project');
    }

    public function eventRequestApproved(Request $request): bool
    {
        $event = $request->server->get('HTTP_X_GITHUB_EVENT');

        return $this->eventApproved((string) $event);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return null|JsonResponse
     */
    public function validatePayload(array $payload): ?JsonResponse
    {
        if (!isset($payload['action'])) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.action_not_found')],
                404
            );
        }

        if (!$this->isStatusCommentEnabled((string) $payload['changes']['field_value']['field_name'])) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.status_comment_disabled')],
                400
            );
        }

        $nodeId = $payload['projects_v2_item']['content_node_id'] ?? null;
        $fieldData = $payload['changes']['field_value'] ?? null;

        if (!$nodeId || !$fieldData) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.missing_fields')],
                400
            );
        }

        if (view()->exists('github-project::md.fields.'.$fieldData['field_type'])) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.missing_field_template')],
                400
            );
        }

        return null;
    }

    /**
     * Check if the field name is "Status" and if status comments are enabled.
     *
     * @param  string  $fieldName
     *
     * @return bool
     */
    public function isStatusCommentEnabled(string $fieldName): bool
    {
        if ($fieldName === 'Status' && !config('github-project.enable_status_comment')) {
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
