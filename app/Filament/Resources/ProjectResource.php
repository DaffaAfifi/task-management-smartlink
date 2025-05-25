<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProjectExporter;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use App\Models\Project;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Employee Count')
                    ->counts('users')
                    ->formatStateUsing(function ($record) {
                        return $record->users->pluck('id')->unique()->count();
                    }),
                TextColumn::make('tasks_count')->counts('tasks'),
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
                //
            ])
            ->headerActions([
                ExportAction::make()->exporter(ProjectExporter::class)
                    ->visible(fn() => !Auth::user()?->hasRole('user')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(ProjectExporter::class)
                    ->visible(fn() => !Auth::user()?->hasRole('user')),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Project Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Project Name'),
                        TextEntry::make('users')
                            ->visible(fn($state): bool => filled($state))
                            ->label('Employees')
                            ->formatStateUsing(function ($state, $record) {
                                return implode(', ', $record->users->pluck('name')->unique()->toArray());
                            }),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Task Status Aggregate')
                    ->schema([
                        TextEntry::make('tasks')
                            ->visible(fn($state): bool => filled($state))
                            ->label('To Do')
                            ->formatStateUsing(function ($state, $record) {
                                return $record->tasks->where('status', 'To Do')->count();
                            }),
                        TextEntry::make('tasks')
                            ->visible(fn($state): bool => filled($state))
                            ->label('In Progress')
                            ->formatStateUsing(function ($state, $record) {
                                return $record->tasks->where('status', 'In Progress')->count();
                            }),
                        TextEntry::make('tasks')
                            ->visible(fn($state): bool => filled($state))
                            ->label('Done')
                            ->formatStateUsing(function ($state, $record) {
                                return $record->tasks->where('status', 'Done')->count();
                            }),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
