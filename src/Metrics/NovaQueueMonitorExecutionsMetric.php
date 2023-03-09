<?php

namespace romanzipp\QueueMonitor\Nova\Metrics;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;
use romanzipp\QueueMonitor\Models\Monitor;

class NovaQueueMonitorExecutionsMetric extends Trend
{
    public function name(): string
    {
        return 'Job Executions hourly';
    }

    public function ranges(): array
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => 'Today',
            'MTD' => 'Month To Date',
            'QTD' => 'Quarter To Date',
            'YTD' => 'Year To Date',
            'ALL' => 'All Time',
        ];
    }

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByHours($request, Monitor::query(), 'finished_at')->showLatestValue();
    }

    public function uriKey(): string
    {
        return Str::slug(Arr::last(explode('\\', self::class)));
    }
}
