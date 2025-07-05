<?php

namespace CSlant\GitHubProject\Services;

use CSlant\GitHubProject\Jobs\ProcessWebhookEvent;
use Github\AuthMethod;
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

        $this->client->authenticate((string) config('github-project.github.access_token'), null, AuthMethod::ACCESS_TOKEN);

        return $this->client->graphql()->execute($query, $variables);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws \Throwable
     */
    public function handleComment(array $payload): void
    {
        if (config('github-project.is_queue_enabled')) {
            ProcessWebhookEvent::dispatch($payload);

            return;
        }

        $this->commentOnNode(
            (string) $payload['projects_v2_item']['content_node_id'],
            $this->generateCommentMessage($payload)
        );
    }

    /**
     * Generate the comment message from payload without posting it
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws \Throwable
     */
    public function generateCommentMessage(array $payload): string
    {
        return view('github-project::md.comment', compact('payload'))->render();
    }
}
