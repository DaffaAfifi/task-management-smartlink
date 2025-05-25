<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Form $form): Form
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
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
                        if ($record->status === 'Done' && now()->lessThanOrEqualTo($record->deadline)) {
                            return 'success';
                        }

                        if (now()->greaterThan($record->deadline) && $record->status !== 'Done') {
                            return 'danger';
                        }

                        return null;
                    })
                    ->icon(function ($record) {
                        if ($record->status === 'Done' && now()->lessThanOrEqualTo($record->deadline)) {
                            return 'heroicon-m-check-circle';
                        }

                        if (now()->greaterThan($record->deadline) && $record->status !== 'Done') {
                            return 'heroicon-m-exclamation-triangle';
                        }

                        return null;
                    }),
                TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->searchable(),
                TextColumn::make('total_duration')
                    ->label('Duration of Work')
                    ->state(fn($record) => $record->taskSessions->sum('duration_seconds'))
                    ->formatStateUsing(fn($state) => gmdate('H:i:s', $state))
                    ->icon(
                        fn($record) => $record->status == 'In Progress'
                            ? 'heroicon-m-clock'
                            : null
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
