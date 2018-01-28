<?php

class CollectorObject {
    
    function __construct() {
        $this->collection = array();
    }

    function get($clase) {
        $tabla = strtolower($clase);
        $propiedad_id = "{$tabla}_id";
        $sql = "SELECT $propiedad_id FROM $tabla";
        $coleccion = ejecutar_query($sql);
        
        foreach($coleccion as $arrayasoc) {
            $obj = new $clase();
            $obj->$propiedad_id = $arrayasoc[$propiedad_id];
            $obj->select();
            $this->collection[] = $obj;
        }
    }
    
}



?>