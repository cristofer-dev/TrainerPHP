<?php
require_once 'modules/domicilio.php';
require_once 'modules/datodecontacto.php';
require_once 'modules/pedido.php';
require_once 'modules/provincia.php';
require_once 'modules/pais.php';


class Cliente extends StandardObject {
    
    function __construct(Domicilio $domicilio=null) {
        $this->cliente_id = 0;
        $this->denominacion = '';
        $this->identificacion_tributaria = '';
        $this->domicilio = $domicilio;
        $this->datodecontacto_collection = array();
        $this->pedido_collection = array();
    }

    function add_datodecontacto(Datodecontacto $obj) {
        $this->datodecontacto_collection[] = $obj;
    }    

    function add_pedido(Pedido $obj){
        $this->pedido_collection[] = $obj;
    }

    function select() {
        parent::select();

        // componer colectoras
        $datos = DatoDeContacto::get_datodecontacto_id_for_cliente(
            $this->cliente_id);

        foreach($datos as $arrayasoc) {
            $id = $arrayasoc['datodecontacto_id'];
            $this->add_datodecontacto(FactoryClass::make('DatoDeContacto', $id));
        }
        
        $pedidos = Pedido::get_auxiliar($this->cliente_id);
        foreach($pedidos as $arrayasoc) {
            $id = $arrayasoc['pedido_id'];
            $this->add_pedido(FactoryClass::make('Pedido', $id));
        }
    }

}


class ClienteView extends View {
    
    function mostrar_form_agregar($provincias=array()) {
        $form = file_get_contents("static/cliente_agregar.html");
        foreach($provincias as $provincia) {
            unset($provincia->pais);
        }
        
        $form = $this->render_regex('options', $form, $provincias);
        print $this->render_template($form);
    }
    
    function ver($obj) {
        unset($obj->provincia_collection);
        $ficha = file_get_contents("static/cliente_ver.html");
    
        settype($obj, 'array');
        settype($obj['domicilio'], 'array');
        settype($obj['domicilio']['provincia'], 'array');
        settype($obj['domicilio']['provincia']['pais'], 'array');

        $obj['domicilio']['provincia'] = $obj['domicilio']['provincia']['denominacion'];
        $domicilio = $obj['domicilio'];
        
        // Sustitucón Cliente
        $render = $this->render_dict($obj, $ficha);
        
        //Sustitución Domicilio
        $render = $this->render_dict($domicilio, $render);
       
        //Sustitución DatosDeContactocal
        $DatosDeContacto = $obj['datodecontacto_collection'];
        foreach($DatosDeContacto as &$DC) {
            $comodin_dc = "{{$DC->denominacion}}";
            $render = str_replace($comodin_dc, $DC->valor, $render);
        }
        
        //Sustitución de Pedidos
        $pedidos = $obj['pedido_collection'];
        foreach($pedidos as &$pedido){
            $pedido->producto_collection = count($pedido->producto_collection);
        }
       $render = $this->render_regex('pedidos', $render, $pedidos);
       
       print $this->render_template($render);

    }
    
    function listar($clientes=array()) {
        $form = file_get_contents("static/cliente_listar.html");
        
        $regex = "/<!--fila-->(.|\n){1,}<!--fila-->/";
        $actual_recursion_limit = ini_set("pcre.recursion_limit", 10000);
        preg_match($regex, $form, $matches);
        ini_set("pcre.recursion_limit", $actual_recursion_limit);
        $options = '';
        foreach($clientes as $cliente) {
            unset($cliente->datodecontacto_collection);
            unset($cliente->pedido_collection);
            unset($cliente->domicilio);
            settype($cliente, 'array');
            $claves = array_keys($cliente);
            $valores = array_values($cliente);
            foreach($claves as &$clave) $clave = "{{$clave}}";
            $options .= str_replace($claves, $valores, $matches[0]);
        }
        $form = str_replace($matches[0], $options, $form);
        $dict = array(
            '{contenido}'=>$form
            );
        $template = file_get_contents("static/template.html");
        print str_replace(array_keys($dict), array_values($dict), $template);        
    }
    
    function listar2($clientes=array()) {
       // $form = file_get_contents("static/cliente_listar2.html");
        
        print_r($clientes); die;

        foreach($clientes as $cliente) {
            unset($cliente->domicilio);
            unset($cliente->datodecontacto_collection);

            $cliente->t = 0;
            foreach($cliente->pedido_collection as $pedido) {
                $n = count($pedido->producto_collection);
                unset($pedido->producto_collection);
                $pedido->n = $n;
                $cliente->t += $n;
            }
        }
        
        $render = $this->render_regex('cliente', $form, $clientes);
        
        foreach($clientes as $cliente) {
            $pedidos = $cliente->pedido_collection;
            $tag = "pedidos_cliente{$cliente->cliente_id}";
            $render = $this->render_regex($tag, $render, $pedidos);
        }

        print $this->render_template($render);
    }
    
    function editar($obj, $provincias) {
        unset($cl->pedido_collection);
        $form = file_get_contents("static/cliente_editar.html");
        $domicilio = $obj->domicilio;
        foreach($provincias as $pv) {
            $selected = ($domicilio->provincia == $pv->provincia_id) ? " selected" : "";
            $pv->selected = $selected;
        }
        $form = $this->render_regex("options", $form, $provincias);
        $estatica = $this->render_dict($domicilio, $form);
        unset($obj->domicilio);
        $estatica = $this->render_dict($obj, $estatica);
        print $this->render_template($estatica);
        
    }
}


class ClienteController extends Controller {

    function agregar() {
        $c = new CollectorObject();
        $c->get('Provincia');
        $this->view->mostrar_form_agregar($c->collection);
    }
    
    function guardar() {
        // domicilio
        $d = new DomicilioController();
        $d->guardar();

        // cliente
        extract($_POST);
        $this->model->denominacion = $denominacion;
        $this->model->identificacion_tributaria = $identificacion_tributaria;
        $this->model->domicilio = $d->model;
        $this->model->insert();
        
        // datos de contacto
        $dc = new DatoDeContactoController;
        $dc->guardar_todo($this->model->cliente_id);

        header("Location: /cliente/ver/{$this->model->cliente_id}");
    }
    
    function ver($id) {
        $this->model->cliente_id = $id;
        $this->model->select();
        $pv = FactoryClass::make('Provincia', $this->model->domicilio->provincia);
        $pv->pais = FactoryClass::make('Provincia', $pv->pais);
        $this->model->domicilio->provincia = $pv;
        $this->view->ver($this->model);
        
    }
    
    function listar() {
        $collector = new CollectorObject();
        $collector->get('Cliente');
        $this->view->listar($collector->collection);
    }
    
    function editar($id) {
        $cl = FactoryClass::make('Provincia', $id);
        $p = new CollectorObject();
        $p->get('Provincia');
        $this->view->editar($cl, $p->collection);
    }
    
    function actualizar() {
        print 'modificando';
    }
    
    function eliminar($id) {
        $this->model->cliente_id = $id;
        $this->model->delete();
        header("Location: /cliente/listar/");
        
    }
    
    function listar2() {
        $collector = new CollectorObject();
        $collector->get('Cliente');
        $this->view->listar2($collector->collection);
    }
}

?>