<?php 

class FactoryClass {
    
    public static function make($clase, $id_value) {
        $cls_name = strtolower($clase);
        $p = "{$cls_name}_id";
        $obj = new $clase();
        $obj->$p = $id_value;
        $obj->select();
        return $obj;
    }
}

?>