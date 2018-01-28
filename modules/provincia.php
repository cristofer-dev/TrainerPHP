<?php

class Provincia extends StandardObject {
    
    function __construct() {
        $this->provincia_id = 0;
        $this->denominacion = '';
        $this->pais = 0;
    }
    
    static function get_auxiliar($pais_id) {
        $sql = "SELECT provincia_id FROM provincia WHERE pais = ?";
        $datos = array($pais_id);
        return ejecutar_query($sql, $datos);
    }

}


class ProvinciaView extends View {
    
    function mostrar_form_agregar($paises=array(), $pais_id) {
        $form = file_get_contents("static/provincia_agregar.html");
        foreach($paises as $pais) {
            unset($pais->provincia_collection);
            $selected = ($pais_id == $pais->pais_id) ? " selected" : "";
            $pais->selected = $selected;
        }
        $form = $this->render_regex("options", $form, $paises);
        print $this->render_template($form);
    }
    
    function ver($obj) {
        $ficha = file_get_contents("static/provincia_ver.html");
        settype($obj, 'array');
        $comodines = array_keys($obj);
        $valores = array_values($obj);
        foreach($comodines as &$valor) $valor = "{{$valor}}";
        $render = str_replace($comodines, $valores, $ficha);
        print $this->render_template($render);
    }

    function editar($obj, $paises) {
        $form = file_get_contents("static/provincia_editar.html");
        foreach($paises as $pais) {
            unset($pais->provincia_collection);
            $selected = ($pais->pais_id == $obj->pais) ? " selected" : "";
            $pais->selected = $selected;
        }
        $form = $this->render_regex("options", $form, $paises);
        $estatica = $this->render_dict($obj, $form);
        print $this->render_template($estatica);

    }
    
}


class ProvinciaController extends Controller {
    
    function agregar($pais_id = 0) {
        $p = new CollectorObject();
        $p->get('Pais');
        $this->view->mostrar_form_agregar($p->collection, $pais_id);
    }
    
    function guardar() {
        extract($_POST);
        $this->model->denominacion = $denominacion;
        $this->model->pais = $pais;
        $this->model->insert();
        header("Location: /pais/ver/{$pais}");
    }
    
    function actualizar() {
        extract($_POST);
        $this->model->provincia_id = $provincia_id;
        $this->model->denominacion = $denominacion;
        $this->model->pais = $pais;
        $this->model->update();
        header("Location: /pais/ver/{$pais}");
    }
    
    function ver($id) {
        $this->model->provincia_id = $id;
        $this->model->select();
        $this->view->ver($this->model);
    }

    function editar($id) {
        $p = new CollectorObject();
        $p->get('Pais');

        $this->model->provincia_id = $id;
        $this->model->select();
        $this->view->editar($this->model, $p->collection);
    }
    
    function eliminar($id) {
        $this->model->provincia_id = $id;
        $this->model->select();
        $pais_id = $this->model->pais;
        $this->model->delete();
        header("Location: /pais/ver/{$pais_id}");

    }

}

?>