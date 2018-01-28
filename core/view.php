<?php

class View {
    
    function __construct() {
        $this->template_file = TEMPLATE_FILE;
    }
    
    function render_template($contenido) {
        $dict = array('{contenido}'=>$contenido);
        $template = file_get_contents($this->template_file);
        print str_replace(array_keys($dict), array_values($dict), $template);
    }
    
    function render_dict($dict, $html) {
        $dict = $this->set_dict($dict);
        return str_replace(array_keys($dict), array_values($dict), $html);
    }
    
    function get_regex($tag, $html) {
        $regex = "/<!--$tag-->(.|\n){1,}<!--$tag-->/";
        $pcre = ini_set("pcre.recursion_limit", 25000);
        preg_match($regex, $html, $match);
        ini_set("pcre.recursion_limit", $pcre);
        return $match[0];
    }
    
    function set_dict($obj) {
        settype($obj, 'array');
        $claves = array_keys($obj);
        $valores = array_values($obj);
        foreach($claves as &$clave) $clave = "{{$clave}}";
        foreach($valores as &$valor) {
            if(is_array($valor)) $valor = print_r($valor, true);
        }
        return array_combine($claves, $valores);
    }

    function render_regex($tag, $html, $coleccion) {
        $render = '';
        $codigo = $this->get_regex($tag, $html);

        foreach($coleccion as $obj) {
            $obj = $this->set_dict($obj);
            $render .= str_replace(array_keys($obj), array_values($obj), $codigo);
        }

        $render = str_replace($codigo, $render, $html);
        return str_replace("<!--$tag-->", "", $render);
    }
}

?>