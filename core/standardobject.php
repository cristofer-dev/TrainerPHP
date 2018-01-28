<?php

abstract class StandardObject { 
    
    public function delete() {
        $tabla = strtolower(get_class($this));
        $pid = "{$tabla}_id";
        $sql = "DELETE FROM $tabla WHERE $pid = ?";
        $datos = array($this->$pid);
        ejecutar_query($sql, $datos);
    }

    public function insert() {
        $tabla = strtolower(get_class($this));
        $pid = "{$tabla}_id";
        $propiedades = get_object_vars($this);
        unset($propiedades[$pid]);
        foreach($propiedades as $clave=>$valor) {
            if(is_array($valor)) unset($propiedades[$clave]);
            if(is_object($valor)) {
                $t = strtolower(get_class($valor));
                $p = "{$t}_id";
                $id = $valor->$p;
                $propiedades[$clave] = $id;
            }
        }
    
        $campos = implode(", ", array_keys($propiedades));
        $modificadores = array_pad(array(), count($propiedades), "?");
        $valores = implode(", ", $modificadores);
        $sql = "INSERT INTO $tabla ($campos) VALUES ($valores)";
        $datos = array_values($propiedades);
        $this->$pid = ejecutar_query($sql, $datos);
    }

    public function update() {
        $tabla = strtolower(get_class($this));
        $pid = "{$tabla}_id";
        $propiedades = get_object_vars($this);
        unset($propiedades[$pid]);
        foreach($propiedades as $clave=>$valor) {
            if(is_array($valor)) unset($propiedades[$clave]);
            if(is_object($valor)) {
                $t = strtolower(get_class($valor));
                $p = "{$t}_id";
                $id = $valor->$p;
                $propiedades[$clave] = $id;
            }
        }

        $campos = array_keys($propiedades);
        foreach($campos as &$campo) $campo = "{$campo} = ?";
        $campos = implode(", ", $campos);
        $modificadores = array_pad(array(), count($propiedades), "?");
        $valores = implode(", ", $modificadores);
        $sql = "UPDATE $tabla SET $campos WHERE $pid = ?";
        $datos = array_values($propiedades);
        $datos[] = $this->$pid;
        $this->$pid = ejecutar_query($sql, $datos);
    }
    
    function select() {
        $tabla = strtolower(get_class($this));
        $pid = "{$tabla}_id";
        $propiedades = get_object_vars($this);
        unset($propiedades[$pid]);
        foreach($propiedades as $clave=>$valor) {
            if(is_array($valor)) unset($propiedades[$clave]);
        }
        
        $campos = implode(", ", array_keys($propiedades));
        
        $sql = "SELECT $campos FROM $tabla WHERE $pid = ?";
        $datos = array($this->$pid);
        $resultados = ejecutar_query($sql, $datos);
        foreach($resultados[0] as $clave=>$valor) {
            if(!is_null($this->$clave)) {
                $this->$clave = $valor;
            } else {
                $obj = new $clave();
                $pid = "{$clave}_id";
                $obj->$pid = $valor;
                $obj->select();
                $this->$clave = $obj;
            }
        }
    }

}

?>