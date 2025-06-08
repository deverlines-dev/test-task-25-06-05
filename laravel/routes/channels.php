<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * @todo можно удалить
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
