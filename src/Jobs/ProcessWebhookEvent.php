<?php

namespace CSlant\GitHubProject\Jobs;

use CSlant\GitHubProject\Constants\WebHookConstant;
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
        $events = Cache::get(WebHookConstant::WEBHOOK_CACHE_NAME, []);
        $events[] = $this->eventData;
        Cache::put(WebHookConstant::WEBHOOK_CACHE_NAME, $events, now()->addSeconds(20));

        if (count($events) === 1) {
            ProcessAggregatedEvents::dispatch()->delay(now()->addSeconds(20));
        }
    }
}
