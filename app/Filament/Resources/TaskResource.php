<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TaskExporter;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user && $user->hasRole('user')) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'To Do' => 'To Do',
                        'In Progress' => 'In Progress',
                        'Done' => 'Done',
                    ])
                    ->default('To Do')
                    ->native(false)
                    ->required(),
                DateTimePicker::make('deadline')
                    ->native(false)
                    ->required(),
                Select::make('user_id')
                    ->label('Assigned To')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) =>
                        $query->whereHas('roles', fn($q) => $q->where('name', 'user'))
                    )
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->native(false)
                    ->required(),
                Select::make('project_id')
                    ->label('In Project')
                    ->relationship(name: 'project', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'To Do' => 'gray',
                        'In Progress' => 'warning',
                        'Done' => 'success',
                    })
                    ->sortable(),
                TextColumn::make('deadline')
                    ->dateTime()
                    ->sortable()
                    ->color(function ($record) {
                        if ($record->isFinished('on_time')) {
                            return 'success';
                        }

                        if ($record->isFinished('late')) {
                            return 'warning';
                        }

                        if ($record->isOverdue()) {
                            return 'danger';
                        }

                        return null;
                    })
                    ->icon(function ($record) {
                        if ($record->isFinished('on_time')) {
                            return 'heroicon-m-check-circle';
                        }

                        if ($record->isFinished('late')) {
                            return 'heroicon-m-exclamation-circle';
                        }

                        if ($record->isOverdue()) {
                            return 'heroicon-m-exclamation-triangle';
                        }

                        return null;
                    }),
                TextColumn::make('user.name')
                    ->visible(fn() => !Auth::user()?->hasRole('user'))
                    ->label('Assigned To')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('In Project')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('total_duration')
                    ->label('Duration of Work')
                    ->state(fn($record) => $record->taskSessions->sum('duration_seconds'))
                    ->formatStateUsing(fn($state) => gmdate('H:i:s', $state))
                    ->color(function ($record) {
                        return match ($record->status) {
                            'To Do' => 'gray',
                            'In Progress' => 'warning',
                            'Done' => 'success',
                            default => null
                        };
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'To Do' => 'To Do',
                        'In Progress' => 'In Progress',
                        'Done' => 'Done',
                    ])
                    ->native(false),
                Filter::make('deadline')
                    ->form([
                        DatePicker::make('deadline'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['deadline'],
                                fn(Builder $query, $date): Builder => $query->whereDate('deadline', '=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['deadline'] ?? null) {
                            $indicators[] = Indicator::make('Deadline on ' . Carbon::parse($data['deadline'])->toFormattedDateString())
                                ->removeField('deadline');
                        }

                        return $indicators;
                    }),
                SelectFilter::make('user_id')
                    ->label('Assigned To')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) =>
                        $query->whereHas('roles', fn($q) => $q->where('name', 'user'))
                    )
                    ->searchable(['name', 'email'])
                    ->preload()
                    ->native(false),
                SelectFilter::make('project_id')
                    ->label('In Project')
                    ->relationship(name: 'project', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->headerActions([
                ExportAction::make()->exporter(TaskExporter::class)
                    ->visible(fn() => !Auth::user()?->hasRole('user')),
            ])
            ->actions([
                ViewAction::make(),

                EditAction::make()
                    ->visible(fn() => !Auth::user()?->hasRole('user')),

                Action::make('do_task')
                    ->label('Do Task')
                    ->color('primary')
                    ->icon('heroicon-m-play')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->beginSession())
                    ->visible(
                        fn($record) =>
                        Auth::user()?->hasRole('user') && $record->status === 'To Do'
                    ),

                Action::make('pause_task')
                    ->label('Pause')
                    ->color('warning')
                    ->icon('heroicon-m-pause')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->pauseSession())
                    ->visible(
                        fn($record) =>
                        Auth::user()?->hasRole('user') && $record->status === 'In Progress' && $record->isSessionRunningForCurrentUser()
                    ),

                Action::make('resume_task')
                    ->label('Resume')
                    ->color('warning')
                    ->icon('heroicon-m-play')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->beginSession())
                    ->visible(
                        fn($record) =>
                        Auth::user()?->hasRole('user') && $record->status === 'In Progress'  && !$record->isSessionRunningForCurrentUser()
                    ),

                Action::make('finish')
                    ->label('Finish')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->finishTask())
                    ->visible(
                        fn($record) =>
                        Auth::user()?->hasRole('user') && $record->status === 'In Progress'
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(TaskExporter::class)
                    ->visible(fn() => !Auth::user()?->hasRole('user')),
            ])
            ->defaultSort('deadline', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('title'),
                TextEntry::make('user.name')
                    ->label('Assigned To'),
                TextEntry::make('project.name')
                    ->label('In Project'),
                TextEntry::make('deadline')
                    ->dateTime()
                    ->color(function ($record) {
                        if ($record->isFinished('on_time')) {
                            return 'success';
                        }

                        if ($record->isFinished('late')) {
                            return 'warning';
                        }

                        if ($record->isOverdue()) {
                            return 'danger';
                        }

                        return null;
                    })
                    ->icon(function ($record) {
                        if ($record->isFinished('on_time')) {
                            return 'heroicon-m-check-circle';
                        }

                        if ($record->isFinished('late')) {
                            return 'heroicon-m-exclamation-circle';
                        }

                        if ($record->isOverdue()) {
                            return 'heroicon-m-exclamation-triangle';
                        }

                        return null;
                    }),
                TextEntry::make('finished_at')
                    ->label('Finished At')
                    ->dateTime()
                    ->visible(fn($record) => $record->status === 'Done')
                    ->state(fn($record) => $record->taskSessions->sortByDesc('ended_at')->first()?->ended_at),
                TextEntry::make('total_duration')
                    ->label('Duration of Work')
                    ->state(fn($record) => $record->taskSessions->sum('duration_seconds'))
                    ->formatStateUsing(fn($state) => gmdate('H:i:s', $state))
                    ->color(function ($record) {
                        return match ($record->status) {
                            'To Do' => 'gray',
                            'In Progress' => 'warning',
                            'Done' => 'success',
                            default => null
                        };
                    }),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'To Do' => 'gray',
                        'In Progress' => 'warning',
                        'Done' => 'success',
                    }),
                TextEntry::make('description')
                    ->columnSpanFull(),
            ])->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
