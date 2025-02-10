<?php

namespace CSlant\GitHubProject\Services;

class WebhookService
{
    public function eventApproved(string $event): bool
    {
        return str_contains($event, 'project');
    }
}
