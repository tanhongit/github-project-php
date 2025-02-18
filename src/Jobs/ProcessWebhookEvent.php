<?php

namespace CSlant\GitHubProject\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessWebhookEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected array $eventData;

    /**
     * @param  array<string, mixed>  $eventData
     */
    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $nodeId = $this->eventData['projects_v2_item']['content_node_id'];
        $commentAggregationCacheKey = "comment_aggregation_{$nodeId}";
        $commentAggregationTime = (int) config('github-project.comment_aggregation_time');

        $eventMessages = Cache::get($commentAggregationCacheKey, []);
        $eventMessages[] = view('github-project::md.shared.content', ['payload' => $this->eventData])->render();

        Cache::put($commentAggregationCacheKey, $eventMessages, now()->addSeconds($commentAggregationTime));

        if (!Cache::has($commentAggregationCacheKey.'_author')) {
            Cache::put(
                $commentAggregationCacheKey.'_author',
                [
                    'name' => $this->eventData['sender']['login'],
                    'html_url' => $this->eventData['sender']['html_url'],
                ],
                now()->addSeconds($commentAggregationTime)
            );
        }

        if (count($eventMessages) === 1) {
            ProcessAggregatedEvents::dispatch($nodeId)->delay(now()->addSeconds($commentAggregationTime));
        }
    }
}
