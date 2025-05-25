<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\TasksRelationManager;
use App\Models\User;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Users Management';

    public static function getNavigationLabel(): string
    {
        $user = Auth::user();

        if ($user && $user->hasRole('admin')) {
            return 'Employees';
        }

        return 'Users';
    }

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user && $user->hasRole('admin')) {
            return $query->whereHas('roles', function ($q) {
                $q->where('name', 'user');
            });
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required(fn($context) => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
                Select::make('division_id')
                    ->relationship('division', 'name')
                    ->preload()
                    ->searchable(),
                Select::make('roles')
                    ->visible(fn() => !Auth::user()->hasRole('admin'))
                    ->required()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('division.name')
                    ->searchable(),
                TextColumn::make('roles')
                    ->getStateUsing(fn(User $record): string => $record->roles->pluck('name')->implode(', ')),
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
                SelectFilter::make('division_id')
                    ->label('Division')
                    ->relationship(name: 'division', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()->exporter(UserExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()->exporter(UserExporter::class),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('division.name'),
                        TextEntry::make('roles')
                            ->formatStateUsing(function ($state, $record) {
                                return $record->roles->pluck('name')->implode(', ');
                            })
                    ])
                    ->columns(2),
                Section::make('Tasks Status Aggregate')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
