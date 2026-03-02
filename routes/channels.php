<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aquí se registran los canales de broadcasting que la aplicación soporta.
| Estos canales se utilizan para autenticar usuarios que pueden escuchar
| determinados eventos en tiempo real.
|
*/

Broadcast::channel('casino.{idCasino}', function ($user, $idCasino) {
    if (! $user) {
        return false;
    }

    // Permitir a roles relacionados con operación de casino ver el panel en tiempo real
    return in_array($user->role, ['casino', 'operativo', 'administrador'], true);
});

