<?php
require_once 'modules/provincia.php';


class Pais extends StandardObject {
    
    function __construct() {
        $this->pais_id = 0;
        $this->denominacion = '';
        $this->provincia_collection = array();
    }

    function add_provincia(Provincia $obj){
        $this->provincia_collection[] = $obj;
    }

    function select() {
        parent::select();
        
        // componer colector
        $datos = Provincia::get_auxiliar($this->pais_id);
        
        foreach($datos as $arrayasoc) {
            $pv = FactoryClass::make('Provincia',$arrayasoc['provincia_id']);
            $this->add_provincia($pv);
        }
    }

}

class PaisView extends View {
    
    function mostrar_form_agregar() {
        $form = file_get_contents("static/pais_agregar.html");
        print $this->render_template($form);
    }
    
    function ver($obj) {
        $ficha = file_get_contents("static/pais_ver.html");
        $form = $this->render_regex("provincias", $ficha, $obj->provincia_collection);
        $estatica = $this->render_dict($obj, $form);
        print $this->render_template($estatica);
        
    }

    function listar($coleccion) {
        $tabla = file_get_contents("static/pais_listar.html");
        $render = $this->render_regex('fila', $tabla, $coleccion);
        print $this->render_template($render);
    }
    
    function editar($obj) {
        $form = file_get_contents("static/pais_editar.html");
        $estatica = $this->render_dict($obj, $form);
        print $this->render_template($estatica);
    }
}

class PaisController extends Controller {

    function agregar() {
        $this->view->mostrar_form_agregar();
    }
    
    function guardar() {
        extract($_POST);
        $this->model->denominacion = $denominacion;
        $this->model->insert();
        print_r($this->model);
        //header("Location: /pais/listar");
    }

    function ver($id) {
        $this->model->pais_id = $id;
        $this->model->select();
        $this->view->ver($this->model);
    }

    function listar() {
        $col = new CollectorObject();
        $col->get('Pais');
        $this->view->listar($col->collection);
    }
    
    function editar($id) {
        $this->model->pais_id = $id;
        $this->model->select();
        $this->view->editar($this->model);
    }

    function actualizar() {
        extract($_POST);
        $this->model->pais_id = $pais_id;
        $this->model->denominacion = $denominacion;
        $this->model->update();
        header("Location: /pais/ver/{$pais_id}");
    }   
    
    function eliminar($id) {
        $this->model->pais_id = $id;
        $this->model->delete();
        header("Location: /pais/listar");
    }   

}


?>