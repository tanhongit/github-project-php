<?php

namespace CSlant\GitHubProject\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

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
        $events = Cache::pull($commentAggregationCacheKey, []);

        if (!empty($events)) {
            $message = $this->aggregateMessages($events);
        }
    }

    /**
     * Aggregate messages from events.
     *
     * @param  array  $events
     *
     * @return string
     */
    protected function aggregateMessages(array $events): string
    {
        $messages = array_map(function ($event) {
            return $event['message'];
        }, $events);

        return implode("\n", $messages);
    }
}
