<?php

namespace App\Models;

use Database\Factories\EnquiryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string|null $attachment_path
 * @property string|null $attachment_name
 * @property int|null $attachment_size
 * @property string|null $attachment_mime
 * @property string|null $ip_hash
 * @property Carbon|null $handled_at
 * @property int|null $handled_by
 */
class Enquiry extends Model
{
    /** @use HasFactory<EnquiryFactory> */
    use HasFactory;

    public const TYPE_CONTACT = 'contact';

    public const TYPE_CONSULTATION = 'consultation';

    /**
     * Explicit allowlist. Server-derived fields (ip_hash, user_agent,
     * attachment_*, handled_*) are set deliberately, never mass-assigned
     * from request input.
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'company',
        'service',
        'budget',
        'message',
    ];

    protected $hidden = [
        'ip_hash',
        'user_agent',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
            'attachment_size' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path);
    }
}
