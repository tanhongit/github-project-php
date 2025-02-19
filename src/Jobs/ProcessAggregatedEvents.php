<?php

namespace CSlant\GitHubProject\Jobs;

use CSlant\GitHubProject\Services\GithubService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessAggregatedEvents implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $nodeId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $commentAggregationCacheKey = config('github-project.comment_aggregation_cache_key')."_{$this->nodeId}";

        /** @var array<string, mixed> $eventMessages */
        $eventMessages = Cache::pull($commentAggregationCacheKey, []);

        $message = $this->aggregateMessages($eventMessages);
        $author = Cache::pull($commentAggregationCacheKey.'_author', []);

        Cache::forget($commentAggregationCacheKey);
        Cache::forget($commentAggregationCacheKey.'_author');

        $message .= view(
            'github-project::md.shared.author',
            ['name' => $author['name'], 'html_url' => $author['html_url']]
        )->render();

        $githubService = new GithubService;
        $githubService->commentOnNode($this->nodeId, $message);
    }

    /**
     * Aggregate messages from events.
     *
     * @param  array<string, mixed>  $eventMessages
     *
     * @return string
     */
    protected function aggregateMessages(array $eventMessages): string
    {
        $messages = array_map(function ($message) {
            return $message;
        }, $eventMessages);

        return implode("\n", $messages);
    }
}
