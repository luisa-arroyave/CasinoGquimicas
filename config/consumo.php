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

    /*
    | Hora de corte (H:i o H:i:s) para distinguir turnos el mismo día calendario.
    | periodo_turno_dia = 1 si hora_consumo es antes de esta hora; 2 si es igual o después.
    | Permite dos mismos tipos de comida el mismo día (ej. refrigerio 00:55 y 23:15).
    */
    'periodo_turno_hora_corte' => env('CONSUMO_PERIODO_TURNO_HORA_CORTE', '12:00'),

];
