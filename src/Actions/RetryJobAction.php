<?php

namespace romanzipp\QueueMonitor\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use romanzipp\QueueMonitor\Models\Monitor;

class RetryJobAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $name = 'Retry';

    public $showInline = true;

    public function __construct()
    {
        $this->canRun(fn (Monitor $monitor) => $monitor->canBeRetried());
    }

    public function handle(ActionFields $fields, Collection $models): void
    {
        /** @var \romanzipp\QueueMonitor\Models\Monitor[] $models */
        foreach ($models as $model) {
            if ( ! $model->canBeRetried()) {
                continue;
            }

            $model->retry();
        }
    }
}
