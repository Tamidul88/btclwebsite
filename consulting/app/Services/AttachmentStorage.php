<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

/**
 * Stores enquiry attachments safely.
 *
 * Defence in depth, because any single check here is bypassable:
 *   1. Extension + client MIME are validated in the FormRequest.
 *   2. The real MIME is re-derived here from file content (finfo), never trusted
 *      from the client.
 *   3. Images are fully re-encoded through GD. This is the important one: it
 *      discards EXIF, trailing polyglot payloads and anything that is not
 *      actually decodable pixel data. A "JPEG" with PHP appended cannot survive.
 *   4. The filename is random. The original is kept only as a display label in
 *      the database, never used to build a path.
 *   5. Files land on the `private` disk, outside the web root entirely, so
 *      there is no URL that could reach them even if Nginx were misconfigured.
 */
class AttachmentStorage
{
    public const DISK = 'private';

    public const DIRECTORY = 'enquiry-attachments';

    /**
     * @return array{path: string, name: string, size: int, mime: string}
     */
    public function store(UploadedFile $file): array
    {
        $mime = $this->detectMime($file);

        if (! in_array($mime, config('security.uploads.mimes'), true)) {
            // Reached only if the request-level validation was bypassed.
            throw new RuntimeException('Unsupported attachment type.');
        }

        $isImage = in_array($mime, config('security.uploads.image_mimes'), true);
        $extension = $this->extensionFor($mime);
        $path = self::DIRECTORY.'/'.Str::uuid()->toString().'.'.$extension;

        $contents = $isImage
            ? $this->reencodeImage($file, $mime)
            : $this->verifiedPdf($file);

        Storage::disk(self::DISK)->put($path, $contents, [
            'visibility' => 'private',
        ]);

        return [
            'path' => $path,
            // Sanitised for display; never concatenated into a filesystem path.
            'name' => $this->safeDisplayName($file->getClientOriginalName(), $extension),
            'size' => Storage::disk(self::DISK)->size($path),
            'mime' => $mime,
        ];
    }

    public function delete(?string $path): void
    {
        if (filled($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    private function detectMime(UploadedFile $file): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detected = $finfo->file($file->getRealPath());

        return is_string($detected) ? $detected : 'application/octet-stream';
    }

    /**
     * Decode and re-encode. Anything that is not genuinely an image throws.
     */
    private function reencodeImage(UploadedFile $file, string $mime): string
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($file->getRealPath());

        $max = (int) config('security.uploads.max_image_dimension');

        if ($image->width() > $max || $image->height() > $max) {
            $image->scaleDown(width: $max, height: $max);
        }

        return match ($mime) {
            'image/png' => $image->toPng()->toString(),
            'image/webp' => $image->toWebp(quality: 85)->toString(),
            default => $image->toJpeg(quality: 85)->toString(),
        };
    }

    /**
     * PDFs cannot be re-encoded without a heavy dependency, so we verify the
     * magic bytes and store as-is. They are served with
     * Content-Disposition: attachment and X-Content-Type-Options: nosniff, and
     * are never rendered in the browser.
     */
    private function verifiedPdf(UploadedFile $file): string
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false || ! str_starts_with($contents, '%PDF-')) {
            throw new RuntimeException('File is not a valid PDF.');
        }

        return $contents;
    }

    private function extensionFor(string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/jpeg' => 'jpg',
            default => 'pdf',
        };
    }

    /**
     * Builds the label shown in the panel and used as the download filename.
     *
     * Dots are collapsed out of the base name so a double extension such as
     * "invoice.php.jpg" cannot survive into Content-Disposition — that is a
     * well-worn trick for getting a saved file to execute on the recipient's
     * machine or a misconfigured server. Exactly one extension is appended,
     * and it is derived from the detected content type, not from the input.
     */
    private function safeDisplayName(string $original, string $extension): string
    {
        $base = (string) pathinfo($original, PATHINFO_FILENAME);

        // Keep letters, digits, spaces, underscores and hyphens. Everything
        // else — dots, slashes, null bytes, RTL overrides — is removed.
        // preg_replace returns null only on error; coalesce so the type stays
        // string all the way through.
        $base = preg_replace('/[^A-Za-z0-9 _-]/', '', $base) ?? '';
        $base = trim(preg_replace('/\s+/', ' ', $base) ?? '');

        if ($base === '') {
            $base = 'attachment';
        }

        return Str::limit($base, 80, '').'.'.$extension;
    }
}
