<?php

namespace romanzipp\QueueMonitor\Nova\Metrics;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use romanzipp\QueueMonitor\Models\Monitor;

class NovaQueueMonitorSuccessMetric extends Partition
{
    public function name(): string
    {
        return 'Job Success Rate';
    }

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this
            ->count($request, Monitor::class, 'failed')
            ->label(fn (bool $failed) => $failed ? 'Fail' : 'Success')
            ->colors([
                0 => '#16a34a',
                1 => '#f5573b',
            ]);
    }

    public function uriKey(): string
    {
        return Str::slug(Arr::last(explode('\\', self::class)));
    }
}
