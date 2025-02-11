<?php

namespace CSlant\GitHubProject\Actions;

use CSlant\GitHubProject\Services\WebhookService;
use Symfony\Component\HttpFoundation\Request;

class WebhookAction
{
    protected WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function __invoke()
    {
        $request = Request::createFromGlobals();
        $event = $request->server->get('HTTP_X_GITHUB_EVENT');

        if (!$this->webhookService->eventApproved($event)) {
            return;
        }

        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['action'])) {
            return;
        }
    }
}
