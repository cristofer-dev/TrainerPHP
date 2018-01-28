<?php
require_once 'modules/producto.php';
require_once 'modules/cliente.php';


class Pedido extends StandardObject {
    
    function __construct() {
        $this->pedido_id = 0;
        $this->fecha = '';
        $this->estado = 0;
        $this->cliente = 0;
        $this->producto_collection = array();
    }

    function add_producto(Producto $obj) {
        $this->producto_collection[] = $obj;
    }

    // método auxiliar para los compositores exclusivos
    static function get_auxiliar($cliente_id) {
        $sql = "SELECT pedido_id FROM pedido WHERE cliente = ?";
        $datos = array($cliente_id);
        return ejecutar_query($sql, $datos);
    }

    function select() {
        parent::select();

        $cl = new ProductoPedido($this);
        $cl->select();
    }
}


class ProductoPedido {
    
    function __construct(Pedido $pedido=null) {
        $this->productopedido_id = 0;
        $this->compuesto = $pedido;
        $this->compositor = $pedido->producto_collection;
    }

    function select() {
        $sql = "SELECT producto FROM productopedido WHERE pedido = ?";
        $datos = array($this->compuesto->pedido_id);
        $resultados = ejecutar_query($sql, $datos);
        foreach($resultados as $arrayasoc) {
            $p = FactoryClass::make('Producto',$arrayasoc['producto']);
            $this->compuesto->add_producto($p);
        }
    }

    function insert() {
        $this->delete();
        $id_compuesto = $this->compuesto->pedido_id;
        $tuplas = array();
        $tuplas = array_pad($tuplas, count($this->compositor), "(?, ?, ?)");
        $tuplas = implode(', ', $tuplas);
        $sql = "INSERT INTO productopedido (pedido, producto, cantidad) VALUES $tuplas";
        $datos = array();
        foreach($this->compositor as $producto) {
            print_r($producto); 
            $datos[] = $id_compuesto;
            $datos[] = $producto->producto_id;
            $datos[] = $producto->cantidad;
        }
        ejecutar_query($sql, $datos);
    }

    function delete() {
        $sql = "DELETE FROM productopedido WHERE pedido = ?";
        $datos = array($this->compuesto->pedido_id);
        ejecutar_query($sql, $datos);
    }
}


class PedidoView extends View {
    
    function mostrar_form_agregar($clientes=array(), $cliente_id) {
        $form = file_get_contents("static/pedido_agregar.html");
        foreach($clientes as $cliente) {
            unset($cliente->domicilio);
            $selected = ($cliente_id == $cliente->cliente_id) ? " selected" : "";
            $cliente->selected = $selected;
        }
        $form = $this->render_regex("options", $form, $clientes);
        print $this->render_template($form);
    }

    function ver($obj, $productos) {
        $ficha = file_get_contents("static/pedido_ver.html");
        settype($obj, 'array');
        //$obj['N'] = count($obj['producto_collection']);
        $render = $this->render_regex('listado-productos', $ficha, $productos );
        $render = $this->render_regex('productos', $render, $obj['producto_collection']);
        $estatica = $this->render_dict($obj, $render);
        
        print $this->render_template($estatica);
    }

    function listar($pedidos=array()) {
        $form = file_get_contents("static/pedido_listar.html");
        $render = $this->render_regex('fila', $form, $pedidos);
        print $this->render_template($render);
    }    
    
}

class PedidoController extends Controller {
    
    function agregar($cliente_id=0) {
        $c = new CollectorObject();
        $c->get('Cliente');
        $this->view->mostrar_form_agregar($c->collection, $cliente_id);
    }
    
    function guardar() {
        extract($_POST);
        $this->model->fecha = $fecha;
        $this->model->estado = $estado;
        $this->model->cliente = $cliente;
        $this->model->insert();
        header("Location: /cliente/ver/{$this->model->cliente}");
    }
    
    function actualizar() {
        extract($_POST);
      //  print_r($_POST);
        $this->model->pedido_id = $pedido_id;
        $this->model->select();
        $this->model->producto_collection = array();

        foreach($productos as $key=>$producto_id) {
            if($productos[$key] != 0){
                $p = FactoryClass::make('Producto', $producto_id);
                $this->model->add_producto($p);
                $p->cantidad = $cantidades[$key];
            }
        }
        
        $cl = new ProductoPedido($this->model);
        $cl->insert();

        header("Location: /pedido/ver/{$this->model->pedido_id}");
    }
    
    function ver($id) {
        $this->model->pedido_id = $id;
        $this->model->select();
        $pedido = $this->model;

        $cliente = FactoryClass::make('Cliente', $pedido->cliente);
        $pedido->cliente = $cliente->denominacion;
        
        $collector = new CollectorObject();
        $collector->get('Producto');
        $productos = $collector->collection;
        
        $this->view->ver($this->model, $productos);
    }

    function listar() {
        $collector = new CollectorObject();
        $collector->get('Pedido');
        $pedidos = $collector->collection;
        
        foreach ($pedidos as $pedido) {
            $cliente = FactoryClass::make('Cliente', $pedido->cliente);
            $pedido->cliente = $cliente->denominacion;
        }
        $this->view->listar($pedidos);
    }
    
    function eliminar($id){
        $this->model->pedido_id = $id;
        $this->model->delete();
        header("Location: /pedido/listar/");
    }

    
}



?>