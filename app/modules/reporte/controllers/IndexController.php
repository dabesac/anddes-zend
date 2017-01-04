<?php

class Reporte_IndexController extends Zend_Controller_Action {

    public function init() {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity()) {
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
        $options = array(
            'layout' => 'inicio',
        );
        Zend_Layout::startMvc($options);
    }

    //Funcion que devuelve los datos de tareopersona segun proyecto, implementado por repeticion en
    //diferentes actions
    protected function obtenerTareopersona($codigo_prop_proy){
        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $data['codigo_prop_proy'] = $codigo_prop_proy;
        $todos_tareopersona = $tareopersona->_getReporte($data);

        $respuesta = [];
        $i = 0;

        foreach ($todos_tareopersona as $fila) {
           if ($fila['tipo_actividad']=='P') {
               $fila['tipo_actividad'] = 'Facturable';
           } elseif ($fila['tipo_actividad']=='G') {
               $fila['tipo_actividad'] = 'No Facturable';
           } elseif ($fila['tipo_actividad']=='A') {
               $fila['tipo_actividad'] = 'Administración';
           }

           if ($fila['estado']=='A') {
               $fila['estado'] = 'Activo';
           } elseif ($fila['estado']=='C') {
               $fila['estado'] = 'Cerrado';
           } elseif ($fila['estado']=='E') {
               $fila['estado'] = 'Eliminado';
           } elseif ($fila['estado']=='PA') {
               $fila['estado'] = 'Paralizado';
           } elseif ($fila['estado']=='CA') {
               $fila['estado'] = 'Cancelado';
           }

           $data['codigo_prop_proy'] = $fila['codigo_prop_proy'];
           $data['codigo_actividad'] = $fila['codigo_actividad'];
           $data['uid'] = $fila['uid'];

           $data_horas = $tareopersona->_getHorasxUidxCppxCa($data);

           $fila['data_horas'] = $data_horas;

           $respuesta[$i] = $fila;
           $i++;
        }

        return $respuesta;

    }


    /*Accion que devuelve la vista principal contenida el el archivo
    ../views/scripts/index/index.phtml*/
    public function indexAction() {

    }



    /*Action que devuelde los registros con los campos necesarios para visualizacion
    de la vista de reporte tarea persona. Para lo cual han sido parseados como json
    */

    public function tareopersonajsonAction() {
        $this->_helper->layout()->disableLayout();
        $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $respuesta = $this->obtenerTareopersona($codigo_prop_proy);
        $this->_helper->json->sendJson($respuesta);
    }

    //Action que devuelve los datos de tareopersona en un archivo html
    public function tareopersonahtmlAction(){
        $this->_helper->layout()->disableLayout();
        $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $respuesta = $this->obtenerTareopersona($codigo_prop_proy);
        $this->view->tareopersona = $respuesta;
    }

    public function usuariosAction() {
        $this->_helper->layout()->disableLayout();
        $proyecto = $this->_getParam('codigo_prop_proy');
        $equipo = new Admin_Model_DbTable_Equipo();
        $usuarios = $equipo->_getUsuarioxProyectoxEstadoxNivel($proyecto, 'A', '4');
        $this->_helper->json->sendJson($usuarios);
    }

    public function clientesAction(){
        $this->_helper->layout()->disableLayout();
        $cliente = new Admin_Model_DbTable_Cliente();
        $clientes = $cliente->_getClienteAllOrdenado();
        $respuesta = [];
        $i = 0;
        foreach ($clientes as $fila) {
            $filares['id'] = $fila['clienteid'];
            $filares['nombre'] = $fila['nombre_comercial'];
            $respuesta[$i] = $filares;
            $i++;
        }
        $this->_helper->json->sendJson($respuesta);
    }

    public function gerentesAction(){
        $this->_helper->layout()->disableLayout();
        $proyecto = new Admin_Model_DbTable_Proyecto();
        $gerentes = $proyecto->_getGerentes();
        $this->_helper->json->sendJson($gerentes);
    }

    public function proyectosAction(){
        $this->_helper->layout()->disableLayout();
        if ($this->_getParam('clienteid') != '') {
            $cliente = $this->_getParam('clienteid');
            $proyecto = new Admin_Model_DbTable_Proyecto();
            $proyectos = $proyecto->_getProyectoxCliente($cliente);
        } elseif ($this->_getParam('gerenteid') != '') {
            $gerente = $this->_getParam('gerenteid');
            $proyecto = new Admin_Model_DbTable_Proyecto();
            $proyectos = $proyecto->_getProyectosxGerente($gerente);
        }
        $this->_helper->json->sendJson($proyectos);
    }

    public function unidadmineraAction(){
        $this->_helper->layout()->disableLayout();
        $clienteid = $this->_getParam('clienteid');
        $uni_min = new Admin_Model_DbTable_Unidadminera();
        $uni_mins = $uni_min->_getUnidadmineraxcliente($clienteid);
        $respuesta = [];
        $i = 0;
        foreach ($uni_mins as $fila) {
            $filares['id'] = $fila['unidad_mineraid'];
            $filares['nombre'] = $fila['nombre'];
            $respuesta[$i] = $filares;
            $i++;
        }
        $this->_helper->json->sendJson($respuesta);
    }

    public function semanalAction(){
      $tareopersona = new Admin_Model_DbTable_Tareopersona();
      $usuarios = new Admin_Model_DbTable_Usuario();
      $semana_user = [];
      $fecha = date("Y-m-d");
      $semanaid=date('W', strtotime($fecha)); 
      $usuario_area= $usuarios->UsuarioxEstado('A');
      $i=0;
      foreach ($usuario_area as $user ) {
        $semana_fecha=$usuarios->UsuarioxEstadoxAreaxFechaIngreso($user['uid'],$user['areaid']);
        for ($j=$semana_fecha[0]['semana'];$j<$semanaid;$j++)
          {
            $semana_tareo = $tareopersona->_getSemanaTareoxEstadoEnvio($j,$user['uid']);  
            if ($semana_tareo)
            {
              $semana_user[$i]= $semana_tareo[0]['row']  ;
              $remplazo= str_replace ( '('  , '' , $semana_tareo[0]['row']  );
              $remplazo= str_replace ( ')'  , '' , $remplazo  );
              $lista1 = explode(",",$remplazo); 
              $semana_user[$i]=$lista1[0];
              $semana_area[$i]=$lista1[1];
              $semana_semana[$i]=$j;
              $semana_estado[$i]=$lista1[2];
            }
            else
            {
              $semana_user[$i]=$user['uid'];
              $semana_area[$i]=$user['areaid'];
              $semana_semana[$i]=$j;
              $semana_estado[$i]='V';
            }

            print_r($semana_user[$i]);   echo ";";print_r($semana_area[$i]);  echo ";";  print_r($semana_semana[$i]);
            echo ";";
      print_r($semana_estado[$i]);
      echo "<br>";
          }
        $i++;

      }
    

    }


    public function impromirAction(){

    }


}