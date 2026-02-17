<?php

return [
    /*
    | Roles del sistema (deben coincidir con la tabla roles empresarial si se integra).
    */
    'all' => [
        'administrador',
        'gestionhumana',
        'casino',
        'operativo',
        'empleado',
    ],

    'labels' => [
        'administrador' => 'Administrador',
        'gestionhumana' => 'Gestión Humana',
        'casino' => 'Casino',
        'operativo' => 'Operativo',
        'empleado' => 'Empleado',
    ],

    'default' => 'empleado',
];
