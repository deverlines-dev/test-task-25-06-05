<?php

namespace App\Events\Broadcasts;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserImportRowsCreatedEvent implements ShouldBroadcast, ShouldQueue
{
    public function __construct(public string $uuid, public int $rows)
    {

    }

    /**
     * @todo перевести на приватный
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('import.users_import'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'rows_created';
    }
}
