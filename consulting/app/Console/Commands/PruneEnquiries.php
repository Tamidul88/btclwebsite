<?php

namespace App\Console\Commands;

use App\Models\Enquiry;
use App\Services\AttachmentStorage;
use Illuminate\Console\Command;

/**
 * Enforces the retention period stated in the privacy notice.
 *
 * Data you no longer hold cannot be breached, and a published retention policy
 * that is not actually enforced is a compliance finding in its own right.
 */
class PruneEnquiries extends Command
{
    protected $signature = 'enquiries:prune {--months=24 : Retention period} {--dry-run}';

    protected $description = 'Delete enquiries past the retention period, with their attachments';

    public function handle(AttachmentStorage $attachments): int
    {
        $months = max(1, (int) $this->option('months'));
        $cutoff = now()->subMonths($months);

        $query = Enquiry::query()->where('created_at', '<', $cutoff);
        $count = $query->count();

        if ($count === 0) {
            $this->info('Nothing to prune.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info("Would delete {$count} enquiries created before {$cutoff->toDateString()}.");

            return self::SUCCESS;
        }

        $query->chunkById(200, function ($enquiries) use ($attachments) {
            foreach ($enquiries as $enquiry) {
                // Delete the file first: an orphaned row is recoverable, an
                // orphaned attachment is undiscovered personal data.
                $attachments->delete($enquiry->attachment_path);
                $enquiry->delete();
            }
        });

        $this->info("Pruned {$count} enquiries older than {$months} months.");

        return self::SUCCESS;
    }
}
