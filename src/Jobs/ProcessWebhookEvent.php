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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $eventData;

    /**
     * Create a new job instance.
     *
     * @param array $eventData
     */
    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $commentAggregationCacheKey = (string) config('github-project.comment_aggregation_cache_key');
        $commentAggregationTime = (int) config('github-project.comment_aggregation_time');

        $events = Cache::get($commentAggregationCacheKey, []);
        $events[] = $this->eventData;
        Cache::put($commentAggregationCacheKey, $events, now()->addSeconds($commentAggregationTime));

        if (count($events) === 1) {
            ProcessAggregatedEvents::dispatch()->delay(now()->addSeconds($commentAggregationTime));
        }
    }
}
