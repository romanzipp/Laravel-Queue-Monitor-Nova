<?php

namespace romanzipp\QueueMonitor\Nova\Fields;

use Laravel\Nova\Fields\Status;
use romanzipp\QueueMonitor\Enums\MonitorStatus;
use romanzipp\QueueMonitor\Models\Monitor;

class NovaMonitorStatusField extends Status
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        $attribute = fn (Monitor $monitor) => MonitorStatus::toNamedArray()[$monitor->status];

        parent::__construct($name, $attribute, $resolveCallback);
    }

    protected function resolveStatusType()
    {
        return match ($this->value) {
            'Running' => 'RUNNING',
            'Succeeded' => 'SUCCEEDED',
            'Failed' => 'FAILED',
            'Stale' => 'STALE',
        };
    }

    protected function resolveTypeClass()
    {
        return match ($this->value) {
            'Running' => 'bg-blue-100 text-blue-500 dark:bg-blue-900 dark:text-blue-400',
            'Succeeded' => 'bg-green-100 text-green-600 dark:bg-green-400 dark:text-green-900',
            'Failed' => 'bg-red-100 text-red-600 dark:bg-red-400 dark:text-red-900',
            'Stale' => 'bg-gray-100 text-gray-500 dark:bg-gray-900 dark:text-gray-400',
        };
    }
}
