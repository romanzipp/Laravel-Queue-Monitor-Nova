<?php

namespace romanzipp\QueueMonitor\Nova\Resources;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use romanzipp\QueueMonitor\Models\Monitor;
use romanzipp\QueueMonitor\Nova\Fields\NovaMonitorStatusField;
use romanzipp\QueueMonitor\Nova\Filters\NovaQueueMonitorStatusFilter;
use romanzipp\QueueMonitor\Nova\Metrics\NovaQueueMonitorExecutionsMetric;
use romanzipp\QueueMonitor\Nova\Metrics\NovaQueueMonitorJobsPartition;
use romanzipp\QueueMonitor\Nova\Metrics\NovaQueueMonitorSuccessMetric;

class NovaQueueMonitorJob extends Resource
{
    public static string $model = Monitor::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static function authorizable(): bool
    {
        return true;
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(),

            Stack::make('Job', [
                Line::make('Job', fn (Monitor $monitor) => $monitor->getBasename())->asHeading(),
                Line::make('Meta', fn (Monitor $monitor) => sprintf('Queue: %s', $monitor->queue))->asSmall(),
            ]),

            NovaMonitorStatusField::make('Status'),

            Number::make('Duration', fn (Monitor $monitor) => $monitor->getElapsedInterval()->cascade()->forHumans(null, true)),
            Number::make('Attempt'),

            Text::make('Info', function (Monitor $monitor) {
                if ($monitor->hasFailed()) {
                    return sprintf('<div class="text-red-600 font-semibold">%s</div>', Str::limit($monitor->exception_message, 120));
                }

                if ( ! $monitor->data) {
                    return '-';
                }

                return sprintf(
                    '<textarea class="w-full p-1 rounded border font-mono text-xs dark:bg-gray-800 dark:border-gray-700" rows="4" readonly>%s</textarea>',
                    json_encode(json_decode($monitor->data), JSON_PRETTY_PRINT)
                );
            })->asHtml(),

            Text::make('Exception', function (Monitor $monitor) {
                if ( ! $monitor->exception) {
                    return '-';
                }

                return sprintf(
                    '<textarea class="w-full p-1 rounded border font-mono text-xs dark:bg-gray-800 dark:border-gray-700" rows="32" readonly>%s</textarea>',
                    $monitor->exception
                );
            })->onlyOnDetail()->asHtml(),

            Stack::make('Started / Ended', [
                DateTime::make('Started at'),
                DateTime::make('Finished at'),
            ]),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new NovaQueueMonitorStatusFilter(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [
            new NovaQueueMonitorExecutionsMetric(),
            new NovaQueueMonitorJobsPartition(),
            new NovaQueueMonitorSuccessMetric(),
        ];
    }
}
