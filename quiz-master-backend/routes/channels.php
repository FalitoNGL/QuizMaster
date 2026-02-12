<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// --- CHANNEL BARU UNTUK NOTIFIKASI TANTANGAN ---
// Hanya user dengan ID yang sesuai yang boleh mendengar channel ini
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// --- CHANNEL UNTUK GAME ROOM ---
// Semua user yang login boleh subscribe ke update game room
Broadcast::channel('game.{roomCode}', function ($user, $roomCode) {
    return $user != null; 
});