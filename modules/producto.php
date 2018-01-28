<?php

class Producto extends StandardObject {
    
    function __construct() {
        $this->producto_id = 0;
        $this->denominacion = '';
        $this->precio = 0.0;
    }

}

class ProductoView extends View {
    
    function mostrar_form_agregar() {
        $form = file_get_contents("static/producto_agregar.html");
        print $this->render_template($form);
    }
    
    function ver($obj) {
        $ficha = file_get_contents("static/producto_ver.html");
        settype($obj, 'array');
        $comodines = array_keys($obj);
        $valores = array_values($obj);
        foreach($comodines as &$valor) $valor = "{{$valor}}";
        $render = str_replace($comodines, $valores, $ficha);

        print $this->render_template($render);
    }
    
    function listar($productos=array()) {
        $tabla = file_get_contents("static/producto_listar.html");
        $render = $this->render_regex('fila', $tabla, $productos);
        print $this->render_template($render);
    }
    
    function editar($obj) {
        $form = file_get_contents("static/producto_editar.html");
        $estatica = $this->render_dict($obj, $form);
        print $this->render_template($estatica);
    }
}


class ProductoController extends Controller {

    function agregar() {
        $this->view->mostrar_form_agregar();
    }
    
    function guardar() {
        extract($_POST);
        $this->model->denominacion = $denominacion;
        $this->model->precio = $precio;
        $this->model->insert();
        header("Location: /producto/listar");
    }
    
    function ver($id) {
        $this->model->producto_id = $id;
        $this->model->select();
        $this->view->ver($this->model);
    }
    
    function listar() {
        $collector = new CollectorObject();
        $collector->get('Producto');
        $this->view->listar($collector->collection);
    }

    function eliminar($id) {
        $this->model->producto_id = $id;
        $this->model->delete();
        header("Location: /producto/listar/");
    }
    
    function editar($id) {
        $this->model->producto_id = $id;
        $this->model->select();
        $this->view->editar($this->model);
    }
    
    function actualizar() {
        extract($_POST);
        $this->model->producto_id = $producto_id;
        $this->model->denominacion = $denominacion;
        $this->model->precio = $precio;
        $this->model->update();
        header("Location: /producto/listar");
    }
}


?>