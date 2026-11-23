<x-mail::message>
# New {{ $enquiry->type }} enquiry

**Name:** {{ $enquiry->name }}
**Email:** {{ $enquiry->email }}
@if ($enquiry->company)
**Company:** {{ $enquiry->company }}
@endif
@if ($enquiry->phone)
**Phone:** {{ $enquiry->phone }}
@endif
@if ($enquiry->service)
**Service:** {{ data_get(config('site.services'), $enquiry->service.'.name', $enquiry->service) }}
@endif
@if ($enquiry->budget)
**Budget:** {{ data_get(config('site.budget_bands'), $enquiry->budget, $enquiry->budget) }}
@endif

**Message:**

{{ $enquiry->message }}

@if ($enquiry->hasAttachment())
An attachment was included ({{ $enquiry->attachment_name }}). It is available in the admin panel — attachments are never emailed.
@endif

<x-mail::button :url="config('app.url')">
Open the admin panel
</x-mail::button>
</x-mail::message>
