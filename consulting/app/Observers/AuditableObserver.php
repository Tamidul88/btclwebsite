<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Records create/update/delete for any model it is attached to, but only when
 * an authenticated admin performed the action — public form submissions are
 * recorded separately and would otherwise flood the trail.
 */
class AuditableObserver
{
    public function __construct(private readonly AuditLogger $logger) {}

    public function created(Model $model): void
    {
        $this->record('created', $model);
    }

    public function updated(Model $model): void
    {
        $this->record('updated', $model);
    }

    public function deleted(Model $model): void
    {
        $this->record('deleted', $model);
    }

    private function record(string $action, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $event = class_basename($model).'.'.$action;

        $action === 'updated'
            ? $this->logger->logModelChange($event, $model)
            : $this->logger->log($event, $model);
    }
}
