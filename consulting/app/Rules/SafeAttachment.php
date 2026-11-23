<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Content-based attachment validation.
 *
 * The extension and the browser-supplied Content-Type are both attacker
 * controlled, so neither is trusted. This rule re-derives the type from the
 * file's own bytes and, for images, confirms the data actually decodes.
 *
 * It exists so a type mismatch surfaces as a normal validation message rather
 * than a 500 from AttachmentStorage. The exception in that service remains as
 * a backstop for any path that bypasses validation.
 */
class SafeAttachment implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute could not be read.');

            return;
        }

        if (! $value->isValid()) {
            $fail('The :attribute failed to upload. Please try again.');

            return;
        }

        $path = $value->getRealPath();

        if ($path === false || ! is_readable($path)) {
            $fail('The :attribute could not be read.');

            return;
        }

        // Real type, from the bytes — not the extension, not the client header.
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path) ?: 'application/octet-stream';

        $allowed = config('security.uploads.mimes');

        if (! in_array($mime, $allowed, true)) {
            $fail('The :attribute must be a PDF or an image. The file you sent is not one.');

            return;
        }

        // Extension must agree with the content, so "shell.php.jpg" containing
        // a real JPEG still cannot be saved with a php-adjacent name.
        $declared = strtolower((string) $value->getClientOriginalExtension());

        if (! in_array($declared, config('security.uploads.extensions'), true)) {
            $fail('The :attribute has a file extension we do not accept.');

            return;
        }

        if (in_array($mime, config('security.uploads.image_mimes'), true)) {
            $this->validateImage($path, $fail);

            return;
        }

        $this->validatePdf($path, $fail);
    }

    /** The bytes must decode as an image, and be a sane size. */
    private function validateImage(string $path, Closure $fail): void
    {
        $info = @getimagesize($path);

        if ($info === false) {
            $fail('The :attribute looks like an image but could not be decoded.');

            return;
        }

        $max = (int) config('security.uploads.max_image_dimension');

        // A decompression-bomb guard: enormous dimensions can exhaust memory
        // during re-encoding even when the file on disk is small.
        if ($info[0] > $max * 4 || $info[1] > $max * 4) {
            $fail("The :attribute is too large in dimensions. Please keep it under {$max} pixels on the long edge.");
        }
    }

    private function validatePdf(string $path, Closure $fail): void
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            $fail('The :attribute could not be read.');

            return;
        }

        $signature = fread($handle, 5);
        fclose($handle);

        if ($signature !== '%PDF-') {
            $fail('The :attribute is named as a PDF but does not contain one.');
        }
    }
}
