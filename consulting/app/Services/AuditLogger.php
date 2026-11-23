<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Single write path for the audit trail.
 *
 * Values are redacted before storage so the log never becomes a secondary
 * source of secrets — an audit log that contains password hashes or 2FA
 * secrets is a liability, not a control.
 */
class AuditLogger
{
    private const REDACTED = [
        'password', 'password_confirmation', 'current_password',
        'two_factor_secret', 'two_factor_recovery_codes',
        'remember_token', 'token', 'api_token', 'secret', 'code', 'otp',
    ];

    /** @param  array<string, mixed>  $properties */
    public function log(string $event, ?Model $subject = null, array $properties = [], ?User $user = null): AuditLog
    {
        $user ??= Auth::user();

        // request() is always bound in the HTTP lifecycle; in console context
        // it resolves to an empty Request rather than null.
        $request = request();

        return AuditLog::create([
            'user_id' => $user?->getKey(),
            'user_email' => $user?->email,
            'event' => $event,
            'auditable_type' => $subject ? $subject::class : null,
            'auditable_id' => $subject?->getKey(),
            'properties' => $this->redact($properties),
            'ip' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255) ?: null,
        ]);
    }

    /**
     * Recursively replaces sensitive values with a marker.
     *
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    private function redact(array $properties): array
    {
        foreach ($properties as $key => $value) {
            if (is_array($value)) {
                $properties[$key] = $this->redact($value);

                continue;
            }

            if (in_array(strtolower((string) $key), self::REDACTED, true)) {
                $properties[$key] = '[redacted]';
            }
        }

        return $properties;
    }

    /** Diff helper for model updates: records only what actually changed. */
    public function logModelChange(string $event, Model $model): AuditLog
    {
        $changes = $model->getChanges();
        Arr::forget($changes, ['updated_at']);

        return $this->log($event, $model, [
            'changed' => $this->redact($changes),
        ]);
    }
}
