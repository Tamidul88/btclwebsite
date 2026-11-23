<?php

use App\Console\Commands\PruneEnquiries;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Schedule;

// Retention, as promised in the privacy notice.
Schedule::command(PruneEnquiries::class, ['--months=24'])
    ->weeklyOn(1, '03:00')
    ->onOneServer();

// Expired sessions are rows containing an authenticated identity; do not keep
// them around after they stop being valid.
Schedule::command('session:prune')->daily();

// Trim the audit trail to a two-year window. Long enough to investigate,
// short enough not to become an archive of personal data in its own right.
Schedule::call(function () {
    AuditLog::query()
        ->where('created_at', '<', now()->subYears(2))
        ->delete();
})->monthly()->name('prune-audit-log')->onOneServer();
