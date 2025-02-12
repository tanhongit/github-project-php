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
     * @return JsonResponse|null
     */
    public function validatePayload(array $payload): ?JsonResponse
    {
        if (!isset($payload['action'])) {
            return response()->json(
                ['message' => __('github-project::github-project.error.event.action_not_found')],
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

        return null;
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
