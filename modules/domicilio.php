<?php
class Domicilio extends StandardObject {
    
    function __construct() {
        $this->domicilio_id = 0;
        $this->calle = '';
        $this->altura = '';
        $this->localidad = '';
        $this->provincia = 0;
    }

}

class DomicilioView extends View {
    
    function mostrar_form_agregar() {
        $form = file_get_contents("static/domicilio_agregar.html");
        print $this->render_template($form);
    }
    
    function ver($obj) {
        $ficha = file_get_contents("static/domicilio_ver.html");
        settype($obj, 'array');
        $comodines = array_keys($obj);
        $valores = array_values($obj);
        foreach($comodines as &$valor) $valor = "{{$valor}}";
        $render = str_replace($comodines, $valores, $ficha);
        print $this->render_template($render);
    }

    function editar($obj, $provincias) {
        $form = file_get_contents("static/domicilio_editar.html");
        foreach($provincias as $provincia) {
            $selected = ($provincia->provincia_id == $obj->provincia) ? "selected" : "";
            $provincia->selected = $selected;
        }
        $form = $this->render_regex("options", $form, $provincias);
        $estatica = $this->render_dict($obj, $form);
        print $this->render_template($estatica);
    }
}

class DomicilioController extends Controller {
    
    function agregar() {
        $this->view->mostrar_form_agregar();
    }
    
    function guardar() {
        extract($_POST);
        $this->model->calle = $calle;
        $this->model->altura = $altura;
        $this->model->localidad = $localidad;
        $this->model->provincia = $provincia;

        if(isset($domicilio_id) && $domicilio_id!=0){
            $this->model->domicilio_id = $domicilio_id;
            $this->model->update();
        }else{
            $this->model->insert();
        }

        header("Location: /cliente/listar");
    }
    
    function ver($id) {
        $this->model->domicilio_id = $id;
        $this->model->select();
        $this->view->ver($this->model);
    }

    function editar($id) {
        $p = new CollectorObject();
        $p->get('Provincia');

        $this->model->domicilio_id = $id;
        $this->model->select();
        $this->view->editar($this->model, $p->collection);
    }
    
}
    


?>