<?php

namespace CSlant\GitHubProject\Services;

use CSlant\GitHubProject\Jobs\ProcessWebhookEvent;
use Github\Client;

class GithubService
{
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

        $client = new Client;

        return $client->graphql()->execute($query, $variables);
    }

    public function handleComment(string $contentNodeId, string $message): void
    {
        if (config('github-project.is_queue_enabled')) {
            ProcessWebhookEvent::dispatch($contentNodeId, $message);
        }

        $this->commentOnNode($contentNodeId, $message);
    }
}
