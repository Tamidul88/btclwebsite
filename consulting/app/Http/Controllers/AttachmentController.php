<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Services\AttachmentStorage;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams an enquiry attachment to an authorised panel user.
 *
 * Every download is authorised through the policy and recorded in the audit
 * trail. Content-Disposition is always `attachment` and the type is forced to
 * octet-stream, so nothing is ever rendered inline in the browser — that
 * removes stored-XSS-via-upload as a category, even for SVG or HTML smuggled
 * past the type checks.
 */
class AttachmentController extends Controller
{
    public function __invoke(Enquiry $enquiry): StreamedResponse
    {
        $this->authorize('downloadAttachment', $enquiry);

        abort_unless($enquiry->hasAttachment(), 404);

        // hasAttachment() guarantees this is set, but narrow it explicitly so
        // the filesystem calls below receive a string rather than string|null.
        $path = (string) $enquiry->attachment_path;

        $disk = Storage::disk(AttachmentStorage::DISK);

        abort_unless($disk->exists($path), 404);

        app(AuditLogger::class)->log(
            'enquiry.attachment_downloaded',
            $enquiry,
            ['filename' => $enquiry->attachment_name],
        );

        return $disk->download(
            $path,
            (string) $enquiry->attachment_name,
            [
                'Content-Type' => 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
                'Content-Security-Policy' => "default-src 'none'; sandbox",
            ],
        );
    }
}
