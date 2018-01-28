<?php
class DatoDeContacto extends StandardObject {
    
    function __construct() {
        $this->datodecontacto_id = 0;
        $this->denominacion = '';
        $this->valor = '';
        $this->cliente = 0;
    }
    
    // método auxiliar para los compositores exclusivos
    static function get_datodecontacto_id_for_cliente($cliente_id) {
        $sql = "SELECT datodecontacto_id FROM datodecontacto WHERE cliente = ?";
        $datos = array($cliente_id);
        return ejecutar_query($sql, $datos);
    }
}


class DatoDeContactoView {
    
    function mostrar_form_agregar() {
        $form = file_get_contents("static/datodecontacto_agregar.html");
        $dict = array(
            '{contenido}'=>$form
            );
        $template = file_get_contents("static/template.html");
        print str_replace(array_keys($dict), array_values($dict), $template);
    }
    
    function ver($obj) {
        $ficha = file_get_contents("static/datodecontacto_ver.html");
        settype($obj, 'array');
        $comodines = array_keys($obj);
        $valores = array_values($obj);
        foreach($comodines as &$valor) $valor = "{{$valor}}";
        $render = str_replace($comodines, $valores, $ficha);

        $dict = array(
            '{contenido}'=>$render
            );
        $template = file_get_contents("static/template.html");
        print str_replace(array_keys($dict), array_values($dict), $template);
    }
}


class DatoDeContactoController extends Controller {

    function agregar() {
        $this->view->mostrar_form_agregar();
    }
    
    function guardar($denominacion, $valor) {
        $this->model->denominacion = $denominacion;
        $this->model->valor = $valor;
        $this->model->insert();
    }
    
    function guardar_todo($id_cliente) {
        extract($_POST);
        $this->model->cliente = $id_cliente;
        $this->guardar('E-mail', $mail);
        $this->guardar('Teléfono', $telefono);
    }

    function ver($id) {
        $this->model->datodecontacto_id = $id;
        $this->model->select();
        $this->view->ver($this->model);
    }

    function editar() {
        print 'mostra form editar';
    }
    
    function actualizar() {
        print 'modificando';
    }
    
    function eliminar() {
        print 'eliminando';
    }
}

?>