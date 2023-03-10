<?php

namespace App\Nova\Metrics;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;
use romanzipp\QueueMonitor\Enums\MonitorStatus;
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
            ->count($request, Monitor::class, 'status')
            ->label(fn (int $status) => MonitorStatus::toNamedArray()[$status])
            ->colors([
                MonitorStatus::RUNNING => '#0099ff',
                MonitorStatus::SUCCEEDED => '#16a34a',
                MonitorStatus::FAILED => '#f5573b',
                MonitorStatus::STALE => '#a8a8b0',
            ]);
    }

    public function uriKey(): string
    {
        return Str::slug(Arr::last(explode('\\', self::class)));
    }
}
