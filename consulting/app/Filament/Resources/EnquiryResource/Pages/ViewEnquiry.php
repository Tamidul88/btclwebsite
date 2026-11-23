<?php

namespace App\Filament\Resources\EnquiryResource\Pages;

use App\Filament\Resources\EnquiryResource;
use App\Services\AuditLogger;
use Filament\Resources\Pages\ViewRecord;

class ViewEnquiry extends ViewRecord
{
    protected static string $resource = EnquiryResource::class;

    /** Reading an enquiry is itself auditable — it contains personal data. */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        app(AuditLogger::class)->log('enquiry.viewed', $this->getRecord());
    }
}
