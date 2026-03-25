<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Empresa IBC (id_empresa en tabla empresas)
    |--------------------------------------------------------------------------
    |
    | Usuarios con rol empleado y este id_empresa ven el selector refrigerio/cena
    | en la ventana horaria configurada. Si no se define, se reconoce IBC por el
    | nombre de la empresa (texto "IBC", sin distinguir mayúsculas).
    |
    */
    'empresa_ibc_id' => env('EMPRESA_IBC_ID'),

];
