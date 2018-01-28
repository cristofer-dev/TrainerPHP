<?php
require_once 'settings.php';
require_once 'core/dblayer.php';
require_once 'core/collector.php';
require_once 'core/standardobject.php';
require_once 'core/controller.php';
require_once 'core/view.php';
require_once 'core/factory.php';

require_once 'modules/pais.php';

$solicitud = $_SERVER['REQUEST_URI'];
@list($null, $modulo, $recurso, $arg) = explode('/', $solicitud);

if($modulo == "") $modulo = "default";
if($recurso == NULL) $recurso = "_default";

if(!file_exists(APP_DIR . "/modules/$modulo.php")) $modulo = "default";
require_once "modules/$modulo.php";
$cname = ucwords($modulo) . "Controller";
$controlador = new $cname();
if(!method_exists($controlador, $recurso)) $recurso = "_default";
$controlador->$recurso($arg);
?>