<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnquiryResource\Pages;
use App\Models\Enquiry;
use App\Services\AuditLogger;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Enquiries';

    /** Read-only by design: an edited enquiry is no longer evidence. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereNull('handled_at')->count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Enquiry')->schema([
                TextEntry::make('type')->badge(),
                TextEntry::make('name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('phone')->placeholder('—'),
                TextEntry::make('company')->placeholder('—'),
                TextEntry::make('service')
                    ->formatStateUsing(fn (?string $state) => $state
                        ? data_get(config('site.services'), $state.'.name', $state)
                        : '—'),
                TextEntry::make('budget')
                    ->formatStateUsing(fn (?string $state) => $state
                        ? data_get(config('site.budget_bands'), $state, $state)
                        : '—'),
                TextEntry::make('created_at')->dateTime(),
            ])->columns(2),

            Section::make('Message')->schema([
                // Plain text, escaped by Filament. Never ->html().
                TextEntry::make('message')->prose()->columnSpanFull(),
            ]),

            Section::make('Attachment')
                ->visible(fn (Enquiry $record) => $record->hasAttachment())
                ->schema([
                    TextEntry::make('attachment_name')->label('File'),
                    TextEntry::make('attachment_mime')->label('Type'),
                    TextEntry::make('attachment_size')
                        ->label('Size')
                        ->formatStateUsing(fn (?int $state) => $state ? round($state / 1024).' KB' : '—'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('company')->searchable()->placeholder('—'),
                Tables\Columns\IconColumn::make('attachment_path')
                    ->label('File')
                    ->boolean()
                    ->getStateUsing(fn (Enquiry $record) => $record->hasAttachment()),
                Tables\Columns\IconColumn::make('handled_at')->label('Handled')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    Enquiry::TYPE_CONTACT => 'Contact',
                    Enquiry::TYPE_CONSULTATION => 'Consultation',
                ]),
                Tables\Filters\Filter::make('unhandled')
                    ->query(fn (Builder $query) => $query->whereNull('handled_at'))
                    ->label('Unhandled only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('download')
                    ->label('Attachment')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (Enquiry $record) => $record->hasAttachment()
                        && (bool) auth()->user()?->can('downloadAttachment', $record))
                    ->url(fn (Enquiry $record) => route('enquiries.attachment', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('markHandled')
                    ->label('Mark handled')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn (Enquiry $record) => $record->handled_at === null
                        && (bool) auth()->user()?->can('markHandled', $record))
                    ->action(function (Enquiry $record) {
                        $record->forceFill([
                            'handled_at' => now(),
                            'handled_by' => auth()->id(),
                        ])->save();

                        app(AuditLogger::class)->log('enquiry.marked_handled', $record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => (bool) auth()->user()?->isAdmin()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnquiries::route('/'),
            'view' => Pages\ViewEnquiry::route('/{record}'),
        ];
    }
}
