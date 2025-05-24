<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();

        if ($user && $user->hasRole('user')) {
            return [
                'All' => Tab::make(),
                'To Do' => Tab::make()
                    ->query(fn(Builder $query): Builder => $query->where('status', 'To Do')),
                'In Progress' => Tab::make()
                    ->query(fn(Builder $query): Builder => $query->where('status', 'In Progress')),
                'Done' => Tab::make()
                    ->query(fn(Builder $query): Builder => $query->where('status', 'Done')),
            ];
        }

        return [];
    }
}
