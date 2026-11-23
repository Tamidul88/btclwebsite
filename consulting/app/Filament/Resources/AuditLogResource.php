<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Strictly read-only view of the audit trail.
 *
 * The model itself blocks updates and deletes, so even a bug here cannot
 * rewrite history — the UI restrictions are convenience, not the control.
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Security';

    protected static ?string $navigationLabel = 'Audit log';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i:s')->sortable(),
                Tables\Columns\TextColumn::make('user_email')->label('Actor')->searchable()->placeholder('system'),
                Tables\Columns\TextColumn::make('event')->badge()->searchable(),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                Tables\Columns\TextColumn::make('auditable_id')->label('#')->placeholder('—'),
                Tables\Columns\TextColumn::make('ip')->label('IP')->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options(fn () => AuditLog::query()
                        ->distinct()
                        ->orderBy('event')
                        ->pluck('event', 'event')
                        ->all()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
