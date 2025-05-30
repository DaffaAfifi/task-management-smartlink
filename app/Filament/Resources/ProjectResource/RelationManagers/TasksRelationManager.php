<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

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
                        if ($record->isFinishedOnTime()) {
                            return 'success';
                        }

                        if ($record->isFinishedLate()) {
                            return 'warning';
                        }

                        if ($record->isOverdue()) {
                            return 'danger';
                        }

                        return null;
                    })
                    ->icon(function ($record) {
                        if ($record->isFinishedOnTime()) {
                            return 'heroicon-m-check-circle';
                        }

                        if ($record->isFinishedLate()) {
                            return 'heroicon-m-exclamation-circle';
                        }

                        if ($record->isOverdue()) {
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
                    ->color(function ($record) {
                        return match ($record->status) {
                            'To Do' => 'gray',
                            'In Progress' => 'warning',
                            'Done' => 'success',
                            default => null
                        };
                    }),
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
