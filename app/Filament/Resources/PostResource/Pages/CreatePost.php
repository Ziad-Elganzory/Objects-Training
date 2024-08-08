<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    public function getCreatedNotification(): Notification
    {

        $recipent = auth()->user();

        return Notification::make()
        ->title('Post Created Successfully')
        ->success()
        ->sendToDatabase($recipent);
    }
}
