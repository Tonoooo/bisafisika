<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;

class AssignUserRole
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Registered $event)
    {
        // Assign the 'siswa' role to the newly registered user
        $event->user->assignRole('siswa');
    }
}
