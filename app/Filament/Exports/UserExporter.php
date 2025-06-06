<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('division.name'),
            ExportColumn::make('roles')
                ->getStateUsing(fn(User $record): string => $record->roles->pluck('name')->implode(', ')),
            ExportColumn::make('todo_tasks')
                ->label('To Do Tasks Count')
                ->formatStateUsing(fn(User $record): string => $record->tasks->where('status', 'To Do')->count()),
            ExportColumn::make('in_progress_tasks')
                ->label('In Progress Tasks Count')
                ->formatStateUsing(fn(User $record): string => $record->tasks->where('status', 'In Progress')->count()),
            ExportColumn::make('done_tasks')
                ->label('Done Tasks Count')
                ->formatStateUsing(fn(User $record): string => $record->tasks->where('status', 'Done')->count()),
            ExportColumn::make('total_work_this_week')
                ->label('Week Total')
                ->state(fn($record) => $record->totalWorkSecondsThisWeek())
                ->formatStateUsing(fn($state) => gmdate('H:i:s', $state)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
