<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only record of admin activity.
 *
 * Deliberately has no $fillable: every write goes through AuditLogger, and
 * updates/deletes are blocked at the model level so a compromised panel
 * session cannot rewrite history.
 */
class AuditLog extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
