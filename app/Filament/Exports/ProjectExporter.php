<?php

namespace App\Filament\Exports;

use App\Models\Project;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProjectExporter extends Exporter
{
    protected static ?string $model = Project::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('name'),
            ExportColumn::make('description'),
            ExportColumn::make('users_count')
                ->label('Employee Count')
                ->formatStateUsing(fn(Project $record): string => $record->users->pluck('id')->unique()->count()),
            ExportColumn::make('tasks_count')
                ->label('Task Count')
                ->counts('tasks'),
            ExportColumn::make('todo_tasks')
                ->label('To Do Tasks Count')
                ->formatStateUsing(fn(Project $record): string => $record->tasks->where('status', 'To Do')->count()),
            ExportColumn::make('in_progress_tasks')
                ->label('In Progress Tasks Count')
                ->formatStateUsing(fn(Project $record): string => $record->tasks->where('status', 'In Progress')->count()),
            ExportColumn::make('done_tasks')
                ->label('Done Tasks Count')
                ->formatStateUsing(fn(Project $record): string => $record->tasks->where('status', 'Done')->count()),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your project export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
