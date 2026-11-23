<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function create()
    {
        return view('pages.contact');
    }

    public function store(StoreContactMessageRequest $request)
    {
        ContactMessage::create($request->safe()->except('website'));

        return redirect()
            ->route('contact')
            ->with('status', 'Thank you. Your message has reached our team and we will reply shortly.');
    }
}
