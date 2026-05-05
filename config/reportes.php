<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Agrupación de sedes en filtros de reportes (/reportes)
    |--------------------------------------------------------------------------
    | Cada grupo aparece como una sola opción; al marcarla se filtran los
    | casinos de todas las sedes listadas (coincidencia por nombre, sin distinguir mayúsculas).
    */
    'sedes_agrupadas' => [
        [
            'clave' => 'cali_y_caloto',
            'etiqueta' => 'CALI Y CALOTO',
            'nombres' => ['PLANTA CALOTO', 'OFICINAS CALI'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nómina: hojas detalladas (FIJO/SENA, contratistas, otras empresas, temporales)
    |--------------------------------------------------------------------------
    | Si en el filtro de sedes se incluye alguna sede listada aquí, el Excel de
    | nómina añade las hojas indicadas (después de Data y Resumen).
    | empresa_principal_nombres: coincidencia sin distinguir mayúsculas en tabla empresas.
    */
    'nomina_layout_ibc' => [
        'sedes_nombres_disparador' => ['IBC MANIZALES'],
        'empresa_principal_nombres' => ['IBC'],
    ],

];
