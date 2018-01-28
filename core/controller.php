<?php

class Controller {
    
    function __construct() {
        $controlador = get_class($this);
        $vista = str_replace("Controller", "View", $controlador);
        $modelo = str_replace("Controller", "", $controlador);
        $this->view = new $vista();
        $this->model = new $modelo();
    }
    
    function _default() {
        header("Location: /");
    }
    
}

?>