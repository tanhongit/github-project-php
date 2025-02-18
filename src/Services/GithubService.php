<?php

namespace CSlant\GitHubProject\Services;

use CSlant\GitHubProject\Jobs\ProcessWebhookEvent;
use Github\Client;

class GithubService
{
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client;
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

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws \Throwable
     */
    public function handleComment(array $payload): void
    {
        $contentNodeId = (string) $payload['projects_v2_item']['content_node_id'] ?? '';

        if (config('github-project.is_queue_enabled')) {
            ProcessWebhookEvent::dispatch($payload);

            return;
        }

        $this->commentOnNode($contentNodeId, view('github-project::md.comment', compact('payload'))->render());
    }
}
