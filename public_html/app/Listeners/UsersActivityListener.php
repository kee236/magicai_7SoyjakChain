<?php

namespace App\Listeners;

use App\Events\UsersActivityEvent;
use Illuminate\Support\Facades\DB;

class UsersActivityListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(UsersActivityEvent $event)
    {
        DB::table('users_activity')->insert([
            'email'      => $event->email,
            'type'       => $event->type ?? 'user',
            'ip'         => $event->ip,
            'connection' => $event->connection,
            'created_at' => now(),
        ]);
    }
}
