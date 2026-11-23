<?php

use App\Models\AuditLog;
use App\Models\Enquiry;
use App\Models\User;
use App\Services\AttachmentStorage;
use App\Services\AuditLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function enquiryWithAttachment(): Enquiry
{
    Storage::fake(AttachmentStorage::DISK);

    $stored = app(AttachmentStorage::class)->store(
        UploadedFile::fake()->image('brief.jpg', 300, 300)
    );

    $enquiry = Enquiry::create([
        'type' => Enquiry::TYPE_CONSULTATION,
        'name' => 'Dana Whitfield',
        'email' => 'dana@example.com',
        'message' => 'A message long enough to satisfy the validation rules.',
    ]);

    $enquiry->forceFill([
        'attachment_path' => $stored['path'],
        'attachment_name' => $stored['name'],
        'attachment_size' => $stored['size'],
        'attachment_mime' => $stored['mime'],
    ])->save();

    return $enquiry;
}

it('refuses an attachment download to an anonymous visitor', function () {
    $enquiry = enquiryWithAttachment();

    $this->get(route('enquiries.attachment', $enquiry))
        ->assertRedirect();   // pushed to login, never served
});

it('serves an attachment to an authorised panel user', function () {
    $enquiry = enquiryWithAttachment();

    $this->actingAs(panelUser(User::ROLE_EDITOR))
        ->get(route('enquiries.attachment', $enquiry))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('forces attachments to download rather than render inline', function () {
    $enquiry = enquiryWithAttachment();

    $response = $this->actingAs(panelUser())
        ->get(route('enquiries.attachment', $enquiry));

    expect($response->headers->get('Content-Disposition'))->toStartWith('attachment;')
        ->and($response->headers->get('Content-Type'))->toBe('application/octet-stream');
});

it('records every attachment download in the audit trail', function () {
    $enquiry = enquiryWithAttachment();
    $user = panelUser();

    $this->actingAs($user)->get(route('enquiries.attachment', $enquiry))->assertOk();

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'event' => 'enquiry.attachment_downloaded',
        'auditable_id' => $enquiry->id,
    ]);
});

it('blocks the admin panel for an unauthenticated visitor', function () {
    $this->get('/'.config('security.admin_path'))->assertRedirect();
});

it('blocks the panel for a user with no role', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    expect($user->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();
});

it('blocks the panel for a deactivated user', function () {
    $user = panelUser();
    $user->forceFill(['is_active' => false])->save();

    expect($user->fresh()->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();
});

it('blocks the panel for an unverified user', function () {
    $user = panelUser();
    $user->forceFill(['email_verified_at' => null])->save();

    expect($user->fresh()->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();
});

it('does not let an editor delete enquiries', function () {
    $enquiry = enquiryWithAttachment();

    expect(panelUser(User::ROLE_EDITOR)->can('delete', $enquiry))->toBeFalse()
        ->and(panelUser(User::ROLE_ADMIN)->can('delete', $enquiry))->toBeTrue();
});

it('never allows an enquiry to be edited, even by an admin', function () {
    $enquiry = enquiryWithAttachment();

    expect(panelUser(User::ROLE_ADMIN)->can('update', $enquiry))->toBeFalse()
        ->and(panelUser(User::ROLE_ADMIN)->can('create', Enquiry::class))->toBeFalse();
});

it('stops an admin deleting their own account', function () {
    $admin = panelUser(User::ROLE_ADMIN);

    expect($admin->can('delete', $admin))->toBeFalse();
});

it('stops the last remaining admin being deleted', function () {
    $first = panelUser(User::ROLE_ADMIN);
    $second = panelUser(User::ROLE_ADMIN);

    // Two admins: one may remove the other.
    expect($first->can('delete', $second))->toBeTrue();

    $second->delete();

    $third = panelUser(User::ROLE_EDITOR);
    expect($third->can('delete', $first->fresh()))->toBeFalse();
});

it('keeps the audit log append-only', function () {
    $log = app(AuditLogger::class)->log('test.event');

    $log->event = 'tampered';

    expect($log->save())->toBeFalse()
        ->and($log->fresh()->event)->toBe('test.event')
        ->and($log->delete())->toBeFalse()
        ->and(AuditLog::whereKey($log->id)->exists())->toBeTrue();
});
