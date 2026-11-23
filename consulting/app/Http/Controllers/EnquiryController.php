<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use App\Services\AttachmentStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function __construct(private readonly AttachmentStorage $attachments) {}

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function consultation(): View
    {
        return view('pages.consultation');
    }

    public function store(StoreEnquiryRequest $request, string $type): RedirectResponse
    {
        abort_unless(
            in_array($type, [Enquiry::TYPE_CONTACT, Enquiry::TYPE_CONSULTATION], true),
            404
        );

        // Only the allowlisted fields reach the model; everything else on the
        // record is derived server-side below.
        $enquiry = new Enquiry($request->safe()->only([
            'name', 'email', 'phone', 'company', 'service', 'budget', 'message',
        ]));

        $enquiry->type = $type;

        // Hashed, not stored raw: enough to correlate abuse, not a plaintext
        // log of who visited. Keyed with APP_KEY so it is not rainbow-tableable.
        $enquiry->ip_hash = hash_hmac('sha256', (string) $request->ip(), config('app.key'));
        $enquiry->user_agent = mb_substr((string) $request->userAgent(), 0, 255) ?: null;

        if ($request->hasFile('attachment')) {
            $stored = $this->attachments->store($request->file('attachment'));

            $enquiry->attachment_path = $stored['path'];
            $enquiry->attachment_name = $stored['name'];
            $enquiry->attachment_size = max(0, (int) $stored['size']);
            $enquiry->attachment_mime = $stored['mime'];
        }

        $enquiry->save();

        Mail::to(config('site.contact.email'))->send(new EnquiryReceived($enquiry));

        return redirect()
            ->route($type === Enquiry::TYPE_CONTACT ? 'contact' : 'consultation')
            ->with('status', 'Thank you — your enquiry has reached us. We reply to every message within one working day.');
    }
}
