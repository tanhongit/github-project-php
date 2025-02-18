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

    public function handleComment(array $payload): void
    {
        $contentNodeId = (string) $payload['projects_v2_item']['content_node_id'];

        if (config('github-project.is_queue_enabled')) {
            ProcessWebhookEvent::dispatch(
                $contentNodeId,
                view('github-project::md.shared.content', compact('payload'))->render()
            );
        }

        $this->commentOnNode($contentNodeId, view('github-project::md.comment', compact('payload'))->render());
    }
}
