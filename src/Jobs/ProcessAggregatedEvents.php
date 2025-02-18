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
        $commentAggregationCacheKey = "comment_aggregation_{$this->nodeId}";
        $eventMessages = Cache::pull($commentAggregationCacheKey, []);

        if (empty($eventMessages)) {
            Cache::forget($commentAggregationCacheKey.'_author');

            return;
        }

        $message = $this->aggregateMessages($eventMessages);
        Cache::forget($commentAggregationCacheKey);
        $author = Cache::pull($commentAggregationCacheKey.'_author', '');

        $message .= '\n\n'.view(
            'github-project::md.shared.author',
            ['name' => $author['name'], 'html_url' => $author['html_url']]
        )->render();

        $githubService = new GithubService;
        $githubService->commentOnNode($this->nodeId, $message);
    }

    /**
     * Aggregate messages from events.
     *
     * @param  list<string>  $eventMessages
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
