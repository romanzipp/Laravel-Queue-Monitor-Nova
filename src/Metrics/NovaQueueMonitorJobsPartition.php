<?php

namespace romanzipp\QueueMonitor\Nova\Metrics;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use romanzipp\QueueMonitor\Models\Monitor;

class NovaQueueMonitorJobsPartition extends Partition
{
    public function name(): string
    {
        return 'Job Types';
    }

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this
            ->count($request, Monitor::class, 'name')
            ->label(fn (string $jobClass) => Arr::last(explode('\\', $jobClass)));
    }

    public function uriKey(): string
    {
        return Str::slug(Arr::last(explode('\\', self::class)));
    }
}
