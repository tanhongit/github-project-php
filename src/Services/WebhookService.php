<?php

namespace CSlant\GitHubProject\Services;

use Github\Client;

class WebhookService
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->authenticate(config('github-project.github.token'), null, Client::AUTH_ACCESS_TOKEN);
    }

    public function eventApproved(string $event): bool
    {
        return str_contains($event, 'project');
    }

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
