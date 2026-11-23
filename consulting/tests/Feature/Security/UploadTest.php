<?php

use App\Models\Enquiry;
use App\Services\AttachmentStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake(AttachmentStorage::DISK);
});

function consultationPayload(array $overrides = []): array
{
    return enquiryPayload(array_merge([
        'service' => 'platform-modernisation',
        'budget' => '25k-75k',
    ], $overrides));
}

it('accepts a genuine image and stores it privately', function () {
    $this->post('/enquiries/consultation', consultationPayload([
        'attachment' => UploadedFile::fake()->image('brief.jpg', 800, 600),
    ]))->assertRedirect();

    $enquiry = Enquiry::sole();

    expect($enquiry->hasAttachment())->toBeTrue()
        ->and($enquiry->attachment_path)->toStartWith(AttachmentStorage::DIRECTORY.'/');

    Storage::disk(AttachmentStorage::DISK)->assertExists($enquiry->attachment_path);
});

it('never stores a file under the public web root', function () {
    $this->post('/enquiries/consultation', consultationPayload([
        'attachment' => UploadedFile::fake()->image('brief.png', 400, 400),
    ]))->assertRedirect();

    $path = Enquiry::sole()->attachment_path;

    expect(public_path($path))->not->toBeFile()
        ->and($path)->not->toContain('..')
        ->and($path)->not->toStartWith('/');
});

it('discards the original filename in favour of a random one', function () {
    $this->post('/enquiries/consultation', consultationPayload([
        'attachment' => UploadedFile::fake()->image('../../evil shell.php.jpg', 300, 300),
    ]))->assertRedirect();

    $enquiry = Enquiry::sole();

    // The stored path is a UUID; the original name survives only as a label.
    expect($enquiry->attachment_path)
        ->toMatch('#^'.AttachmentStorage::DIRECTORY.'/[0-9a-f-]{36}\.jpg$#')
        ->and($enquiry->attachment_name)
        ->not->toContain('..')
        ->not->toContain('/')
        ->not->toContain('.php');
});

it('rejects a php file renamed with an image extension', function () {
    $file = UploadedFile::fake()->createWithContent(
        'shell.jpg',
        "<?php system(\$_GET['c']); ?>",
    );

    $this->post('/enquiries/consultation', consultationPayload(['attachment' => $file]))
        ->assertSessionHasErrors('attachment');

    expect(Enquiry::count())->toBe(0);
});

it('rejects executable extensions outright', function (string $name) {
    $file = UploadedFile::fake()->createWithContent($name, 'payload');

    $this->post('/enquiries/consultation', consultationPayload(['attachment' => $file]))
        ->assertSessionHasErrors('attachment');

    expect(Enquiry::count())->toBe(0);
})->with(['shell.php', 'shell.phtml', 'shell.php5', 'run.sh', 'app.exe', 'page.html', 'vector.svg']);

it('rejects a file that exceeds the size limit', function () {
    $oversize = config('security.uploads.max_kb') + 512;

    $this->post('/enquiries/consultation', consultationPayload([
        'attachment' => UploadedFile::fake()->create('huge.pdf', $oversize, 'application/pdf'),
    ]))->assertSessionHasErrors('attachment');

    expect(Enquiry::count())->toBe(0);
});

it('strips an appended payload by re-encoding the image', function () {
    // A real JPEG with PHP appended after the image data — the classic
    // polyglot. Re-encoding decodes the pixels and writes a fresh file, so
    // anything after the image data cannot survive.
    $jpeg = UploadedFile::fake()->image('polyglot.jpg', 200, 200);
    $withPayload = $jpeg->getRealPath();
    file_put_contents($withPayload, "\n<?php system('id'); ?>", FILE_APPEND);

    $this->post('/enquiries/consultation', consultationPayload([
        'attachment' => new UploadedFile($withPayload, 'polyglot.jpg', 'image/jpeg', null, true),
    ]))->assertRedirect();

    $stored = Storage::disk(AttachmentStorage::DISK)->get(Enquiry::sole()->attachment_path);

    expect($stored)
        ->not->toContain('<?php')
        ->not->toContain("system('id')");
});

it('rejects a pdf that does not begin with the pdf signature', function () {
    $file = UploadedFile::fake()->createWithContent('fake.pdf', 'not really a pdf at all');

    $this->post('/enquiries/consultation', consultationPayload(['attachment' => $file]))
        ->assertSessionHasErrors('attachment');

    expect(Enquiry::count())->toBe(0);
});
