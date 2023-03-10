<?php

namespace romanzipp\QueueMonitor\Nova\Filters;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use romanzipp\QueueMonitor\Enums\MonitorStatus;

class NovaQueueMonitorStatusFilter extends Filter
{
    private const ALL = 'all';
    private const RUNNING = 'running';
    private const SUCCESS = 'success';
    private const FAIL = 'fail';
    private const STALE = 'stale';

    public $name = 'Status';

    public function default(): string
    {
        return self::ALL;
    }

    public function apply(NovaRequest $request, $query, $value): Builder
    {
        switch ($value) {
            case self::RUNNING:
                $query->where('status', MonitorStatus::RUNNING);
                break;
            case self::SUCCESS:
                $query->where('status', MonitorStatus::SUCCEEDED);
                break;
            case self::FAIL:
                $query->where('status', MonitorStatus::FAILED);
                break;
            case self::STALE:
                $query->where('status', MonitorStatus::STALE);
                break;
        }

        return $query;
    }

    public function options(NovaRequest $request): array
    {
        return [
            'All' => self::ALL,
            'Running' => self::RUNNING,
            'Succeeded' => self::SUCCESS,
            'Failed' => self::FAIL,
            'Stale' => self::STALE,
        ];
    }
}
