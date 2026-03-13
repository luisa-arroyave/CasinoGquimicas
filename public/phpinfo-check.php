<?php
/**
 * Archivo temporal para verificar la configuración de PHP en Apache.
 * Visitar: http://localhost/CasinoGquimicas/phpinfo-check.php
 * ELIMINAR después de diagnosticar.
 */
header('Content-Type: text/html; charset=utf-8');
echo '<h2>Diagnóstico PHP (Apache)</h2>';
echo '<p><strong>Extensión Zip cargada:</strong> ' . (extension_loaded('zip') ? 'SÍ ✓' : 'NO ✗') . '</p>';
echo '<p><strong>Clase ZipArchive existe:</strong> ' . (class_exists('ZipArchive') ? 'SÍ ✓' : 'NO ✗') . '</p>';
echo '<p><strong>Archivo php.ini:</strong> ' . php_ini_loaded_file() . '</p>';
echo '<hr><h3>phpinfo() completo:</h3>';
phpinfo();
