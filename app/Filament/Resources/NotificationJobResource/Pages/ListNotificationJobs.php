<?php

namespace App\Filament\Resources\NotificationJobResource\Pages;

use App\Filament\Resources\NotificationJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationJobs extends ListRecords
{
    protected static string $resource = NotificationJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
