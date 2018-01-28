<?php

# Inicialización de constantes de la aplicación

$config = parse_ini_file('config.ini', true);
foreach($config as $seccion=>$array) {
    foreach($array as $constante=>$valor) {
        define($constante, $valor);
    }
}

# Ruta de includes x defecto (php.ini)
ini_set('include_path', APP_DIR);

/*
    TODO    por hacer
    FIXME   corregime cuando puedas
    XXX     corregime ya porque exploto
*/

?>