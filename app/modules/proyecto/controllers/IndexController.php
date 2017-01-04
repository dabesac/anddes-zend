<?php

class Proyecto_IndexController extends Zend_Controller_Action {
    public function init() {
      $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login; 
        $options = array(
            'layout' => 'inicio',
        );
        Zend_Layout::startMvc($options);
    }

    public function indexAction() {
       $uid = $this->sesion->uid;

      $this->_helper->layout()->disableLayout();
    
    }

    public function panelAction()
    {
      $this->_helper->layout()->disableLayout();
    }

    public function ratesActiolin()
    {
      $this->_helper->layout()->disableLayout();
    }


    public function listarAction() {
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $is_gerente=$this->sesion->is_gerente;
        $is_area=$this->sesion->personal->ucatareaid;
        $this->view->is_area = $is_area;
        $this->view->is_gerente = $is_gerente;
        $this->view->dni = $dni;
        if($is_gerente=='S' or $dni == '08051678')
        {
          $listaproyecto = new Admin_Model_DbTable_Proyecto();
          $lista=$listaproyecto->_getProyectosxGerente($uid);
          $this->view->listaproyecto = $lista;
        } 
        else
        {
          if($is_area=='26' || $is_area=='40')
          {
            $listaproyecto = new Admin_Model_DbTable_Proyecto();
              //$lista=$listaproyecto->_getProyectoAll();
            $lista=$listaproyecto->_getProyectosTodosAnddes();
            $this->view->listaproyecto = $lista;
          }
          else
          {
            $listaproyecto = new Admin_Model_DbTable_Equipo();
            $tabla_activaractividad = new Admin_Model_DbTable_Activaractividad();
            $estado='A';
            $lista=$tabla_activaractividad->_getProyectosXEmpleadoXEstadoActivo($uid,$dni,'A');
            //_getProyectosXuidXEstado($uid,$estado);

            
            $this->view->listaproyecto = $lista;
          }
        }
    }
    
    public function nuevoAction() {
      try {

        $propuestas = new Admin_Model_DbTable_Propuesta();
        $prop=$propuestas->_getPropuestaxnoproyectxganado(); 
        $this->view->propuestas=$prop;
        $cliente=new Admin_Model_DbTable_Cliente();
        $todosclientes=$cliente->_getClienteAll();
        $this->view->clientes=$todosclientes;
        $uminera = new Admin_Model_DbTable_Unidadminera();
        $unidadminera=$uminera->_getUnidadmineraAll();
        $this->view->unidadminera=$unidadminera;
        $form= new Admin_Form_Proyecto();        
        $this->view->form=$form;   

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            $codigo_prop_proy = $this->_getParam('cod_proy_prop');
            $proyectoid = $this->_getParam('proyectoid');
            $propuestaid = $this->_getParam('propuesta');
            
            $nombre_proyecto = $this->_getParam('nombre_proyecto');   
            $formdata['nombre_proyecto'] = str_replace("_"," ",$nombre_proyecto);
            $control_proyecto = $this->_getParam('control_proyecto');
            $revision = $this->_getParam('revision');   
            //$descripcion = $this->_getParam('descripcion');   
            $observacion = $this->_getParam('observacion');   
            $fecha_inicio = $this->_getParam('fecha_inicio');
            $fecha_cierre = $this->_getParam('fecha_cierre');
            $monto_total = $this->_getParam('monto_total');
            $control_documentario = $this->_getParam('control_documentario');
            $estado = $this->_getParam('estado');
            $gerente_proyecto = $this->_getParam('gerente_proyecto'); 
            $tipo_proyecto = $this->_getParam('tipo_proyecto');
            $tag = $this->_getParam('tag');
            $clienteid = $this->_getParam('cliente');
            $unidad_mineraid = $this->_getParam('uminera');
            $formdata['proyectoid']=$proyectoid['0'];
            $formdata['propuestaid']=$propuestaid;
            //$formdata['nombre_proyecto']=$nombre_proyecto;
            $formdata['codigo_prop_proy']=$codigo_prop_proy;
            $formdata['control_proyecto']=$control_proyecto;                       
            $formdata['revision']=$revision;
            //$formdata['descripcion']=$descripcion;

            $nombre_descripcion = $this->_getParam('descripcion');   
            $formdata['descripcion'] = str_replace("_"," ",$nombre_descripcion);

            $formdata['observacion']=$observacion;
            $formdata['monto_total']=$monto_total;
            $formdata['unidad_mineraid']=$unidad_mineraid;
            $formdata['clienteid']=$clienteid;
            $formdata['fecha_cierre']=$fecha_cierre;
            $formdata['fecha_inicio']=$fecha_inicio;
            $formdata['control_documentario']=$control_documentario;
            $formdata['estado']=$estado;
            $formdata['gerente_proyecto']=$gerente_proyecto;
            $formdata['tipo_proyecto']=$tipo_proyecto;
            $formdata['tag']=$tag;
            $formdata['paisid']='01';
            $formdata['oid']='AND-10';
            print_r($formdata);//exit();
            $newrec=new Admin_Model_DbTable_Proyecto();
                if($newrec->_save($formdata))
                {
                    echo "llego";
                    // $pk  =   array(                        
                    //                 'codigo_prop_proy'   =>$codigo_prop_proy,
                    //                 'propuestaid'   =>$propuestaid,
                    //                 'revision'   =>$revision,
                                        
                    //                 );
                    // $data = array(
                    //                 'isproyecto' =>  'S'
                    //              );

                    // $updisproject=new Admin_Model_DbTable_Propuesta();
                    // $updisproject->_updateX($data,$pk);
                }    
          }

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
      } 
    }

    public function codproypropAction() {
      $this->_helper->layout()->disableLayout();
      $propuestaid= $this->_getParam("propuesta");
      //$propuestaid= '15.10.015';

      $dbpropuestaid = new Admin_Model_DbTable_Propuesta();
      $where['propuesta']=$propuestaid;
      $propuesta=  $dbpropuestaid->_getFilter($propuestaid);
      $this->view->propuesta = $propuesta;
      print_r($propuesta);

    }

    public function clienteAction() {
      $this->_helper->layout()->disableLayout();
      $propuestaid= $this->_getParam("propuesta");
      //$propuestaid= '15.10.015';

      $dbpropuestaid = new Admin_Model_DbTable_Propuesta();
      $where['propuesta']=$propuestaid;
      $propuesta=  $dbpropuestaid->_getFilter($propuestaid);
      print_r($propuesta);
      $this->view->propuesta = $propuesta;
      //print_r($propuesta);

    }


    public function guardarproyectoAction() {
 $this->_helper->layout()->disableLayout();
     $codigo_prop_proy = $this->_getParam('cod_proy_prop');
            $proyectoid = $this->_getParam('proyectoid');
            $propuestaid = $this->_getParam('propuesta');
            
            $nombre_proyecto = $this->_getParam('nombre_proyecto');   
            $formdata['nombre_proyecto'] = str_replace("_"," ",$nombre_proyecto);
            $control_proyecto = $this->_getParam('control_proyecto');
            $revision = $this->_getParam('revision');   
            //$descripcion = $this->_getParam('descripcion');   
            $observacion = $this->_getParam('observacion');   
            $fecha_inicio = $this->_getParam('fecha_inicio');
            $fecha_cierre = $this->_getParam('fecha_cierre');
            $monto_total = $this->_getParam('monto_total');
            $control_documentario = $this->_getParam('control_documentario');
            $estado = $this->_getParam('estado');
            $gerente_proyecto = $this->_getParam('gerente_proyecto'); 
            $tipo_proyecto = $this->_getParam('tipo_proyecto');
            $tag = $this->_getParam('tag');
            $clienteid = $this->_getParam('cliente');
            $unidad_mineraid = $this->_getParam('uminera');
            $formdata['proyectoid']=$proyectoid;
            $formdata['propuestaid']=$propuestaid;
            //$formdata['nombre_proyecto']=$nombre_proyecto;
            $formdata['codigo_prop_proy']=$codigo_prop_proy;
            $formdata['control_proyecto']=$control_proyecto;                       
            $formdata['revision']=$revision;
            //$formdata['descripcion']=$descripcion;

            $nombre_descripcion = $this->_getParam('descripcion');   
            $formdata['descripcion'] = str_replace("_"," ",$nombre_descripcion);

            $formdata['observacion']=$observacion;
            $formdata['monto_total']=$monto_total;
            $formdata['unidad_mineraid']=$unidad_mineraid;
            $formdata['clienteid']=$clienteid;
            $formdata['fecha_cierre']=$fecha_cierre;
            $formdata['fecha_inicio']=$fecha_inicio;
            $formdata['control_documentario']=$control_documentario;
            $formdata['estado']=$estado;
            $formdata['gerente_proyecto']=$gerente_proyecto;
            $formdata['tipo_proyecto']=$tipo_proyecto;
            $formdata['tag']=$tag;
            $formdata['paisid']='01';
            $formdata['oid']='AND-10';
            //print_r($formdata);//exit();
            $newrec=new Admin_Model_DbTable_Proyecto();
                if($newrec->_save($formdata))
                {
                    //echo "llego";
                    // $pk  =   array(                        
                    //                 'codigo_prop_proy'   =>$codigo_prop_proy,
                    //                 'propuestaid'   =>$propuestaid,
                    //                 'revision'   =>$revision,
                                        
                    //                 );
                    // $data = array(
                    //                 'isproyecto' =>  'S'
                    //              );

                    // $updisproject=new Admin_Model_DbTable_Propuesta();
                    // $updisproject->_updateX($data,$pk);
                }   

              }



    public function umineraAction() {
      $this->_helper->layout()->disableLayout();
      $propuestaid= $this->_getParam("propuesta");
      

      $dbpropuestaid = new Admin_Model_DbTable_Propuesta();
      $where['propuesta']=$propuestaid;
      $propuesta=  $dbpropuestaid->_getFilter($propuestaid);
      $this->view->propuesta = $propuesta;
      //print_r($propuesta);

    }

    public function revisionAction() {
      $this->_helper->layout()->disableLayout();
      $propuestaid= $this->_getParam("propuesta");
      //$propuestaid= '15.10.015';

      $dbpropuestaid = new Admin_Model_DbTable_Propuesta();
      $where['propuesta']=$propuestaid;
      $propuesta=  $dbpropuestaid->_getFilter($propuestaid);
      $this->view->propuesta = $propuesta;
      //print_r($propuesta);

    }

    public function equipoAction() {
      $this->_helper->layout()->disableLayout();
      $proyectoid= $this->_getParam("proyectoid");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      $areaid=$this->_getParam("areaid");
      $this->view->proyectoid = $proyectoid;
      $this->view->codigo_prop_proy = $codigo_prop_proy;
      $this->view->areaid = $areaid;

  



      $bdarea_cat = new Admin_Model_DbTable_Areacategoria();
      $listcat=$bdarea_cat->_buscarCategoriaxAreaxProyecto($areaid);
      $this->view->categoria = $listcat;
    }

    public function modificarequipoAction() {
      $this->_helper->layout()->disableLayout();
      $proyectoid= $this->_getParam("proyectoid");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      $areaid=$this->_getParam("areaid");
      $dni=$this->_getParam("dni");
      $uid=$this->_getParam("uid");
      $this->view->proyectoid = $proyectoid;
      $this->view->codigo_prop_proy = $codigo_prop_proy;
      $this->view->areaid = $areaid;
      $this->view->dni = $dni;
      $this->view->uid = $uid;
      $bdequipo = new Admin_Model_DbTable_Equipo();
      $wheres=array('codigo_prop_proy'=>$codigo_prop_proy,'proyectoid'=>$proyectoid,'uid'=>$uid,'dni'=>$dni,'areaid'=>$areaid);

      $datos_usuario=$bdequipo->_getUsuarioxProyecto($wheres);
      $this->view->datos_usuario = $datos_usuario;
      //print_r($datos_usuario);
    }


      public function updateequipoAction() {

        //$this->_helper->layout()->disableLayout();
        $proyectoid= $this->_getParam("proyectoid");
        $codigo_prop_proy= $this->_getParam("codigo");
        $dni= trim($this->_getParam("dni"));
        $uid= trim($this->_getParam("uid"));
        $areaid= trim($this->_getParam("areaid"));
        $rate= $this->_getParam("rate");
         
        $pk  =   array(                        
                        'codigo_prop_proy'   =>$codigo_prop_proy,
                        'proyectoid'   =>$proyectoid,
                        'uid'   =>$uid,
                        'dni'   =>$dni,
                        'areaid'   =>$areaid,
                            
                        );
        $data = array(
                        'rate_proyecto' =>  $rate
                     );

       
        $update_equipo=new Admin_Model_DbTable_Equipo();
        //$update_equipo->_update($data,$pk);
        
        if($update_equipo->_update($data,$pk))
        {   ?>
          <script>                  
          alert("Actualizado");
            //document.location.href="/proyecto/index/listar";
          </script>
                <?php
            }
        else
        {   ?>
          <script>                  
          alert("Error al Cambiar estado verifique porfavor");
          //document.location.href="/proyecto/index/listar";                                                 
          </script>
         <?php
            }
    }
    public function cambiarestadoAction() {

        $this->_helper->layout()->disableLayout();
        $proyectoid= $this->_getParam("proyectoid");
        $propuestaid= $this->_getParam("propuesta");
        $nombre_proyecto= $this->_getParam("nombre_proyecto");
        $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
        $estado= $this->_getParam("estado");
        $revision= $this->_getParam("revision");
        $oid= $this->_getParam("oid");
        $gerente_proyecto= $this->_getParam("gerente_proyecto");
        $control_proyecto= $this->_getParam("control_proyecto");
        $control_documentario= $this->_getParam("control_documentario");
        $tipo_proyecto= $this->_getParam("tipo_proyecto");
        $paisid= $this->_getParam("paisid");
        $fecha_inicio= $this->_getParam("fecha_inicio");
         
        $pk  =   array(                        
                        'codigo_prop_proy'   =>$codigo_prop_proy,
                        'proyectoid'   =>$proyectoid,
                        'revision'   =>$revision,
                            
                        );
        $data = array(
                        'estado' =>  $estado
                     );

          
        $updrec=new Admin_Model_DbTable_Proyecto();
        if($updrec->_update($data,$pk))
        {
          echo "guardao";
           print_r($pk);
         print_r($data);
        }
        else
        {
          echo "no gurado";
         print_r($pk);
         print_r($data);

        }
        /*
        if($updrec->_update($data,$pk))
        {   ?>
          <script>                  
            document.location.href="/proyecto/index/listar";
          </script>
                <?php
            }
        else
        {   ?>
          <script>                  
          alert("Error al Cambiar estado verifique porfavor");
          document.location.href="/proyecto/index/listar";                                                 
          </script>
         <?php
            }*/
    }


    public function editarAction() {

        $proyectoid= $this->_getParam("proyectoid");
        $codigo_prop_proy= $this->_getParam("codigo_prop_proy");

        $form= new Admin_Form_Proyecto();
     
        $editproyect= new Admin_Model_DbTable_Proyecto();
        $where = array(
                      'codigo_prop_proy'    => $codigo_prop_proy,
                      'proyectoid'    => $proyectoid,
                      );
        $edit = $editproyect->_getOne($where);
        $this->view->form = $form;
        $form->populate($edit);

        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
              if ($form->isValid($formdata)) {
                    unset($formdata['save']);
                    unset($formdata['whySubmit']);
                    $pk = array(    'codigo_prop_proy'    => $codigo_prop_proy,
                                    'proyectoid'    => $proyectoid,
                                ); 
                   
                    print_r($formdata);
                    $updrec=new Admin_Model_DbTable_Proyecto();
               
                    if($updrec->_update($formdata,$pk))
                    {   ?>
                        <script>                  
                          //document.location.href="/proyecto/index/listar";
                          alert("guardado");
                        </script>
                        <?php
                    }
                    else
                    {   ?>
                          <script>                  
                          //alert("Error al guardar verifique porfavor");
                          //document.location.href="/proyecto/index/listar";                                                 
                          </script>
                         <?php
                    } 
              }
        }      
    }

    public function buscarAction() {
        $this->_helper->layout()->disableLayout();
        $buscar_proyecto=$this->_getParam('proyecto');
        $buscar_proyecto=strtolower($buscar_proyecto);
        $buscaproyecto = new Admin_Model_DbTable_Proyecto();
        //$buscar=$buscaproyecto->_buscarProyecto($buscar_proyecto);
        $buscar=$buscaproyecto->_buscarProyectoxReplicon($buscar_proyecto);

        
        $this->view->lista_buscar = $buscar;
      
       
    }  

    public function deleteAction(){
        try {
            $this->_helper->layout()->disablelayout();
     
            $proyectoid=$this->_getParam("proyectoid");
            $propuesta=$this->_getParam("propuesta");
            $codigo_prop_proy=$this->_getParam("codigo_prop_proy");
            $revision=$this->_getParam("revision");

            $pk  =   array(                        
                        'codigo_prop_proy'   =>$codigo_prop_proy,
                        'proyectoid'   =>$proyectoid,
                                                 
                        );

            print_r($pk);

            $delproy = new Admin_Model_DbTable_Proyecto();
            $delproy->_delete($pk);         


        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }



  public function leerexcelAction(){
    try {
      $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
      include ($dir);
      $data = new Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->read('replicon1.xls');
      $numero=1;
      for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
          $actividad=$data->sheets[0]['cells'][$i][7];
          $proyecto=$data->sheets[0]['cells'][$i][5];
          $moneda=$data->sheets[0]['cells'][$i][3];
          $rate=$data->sheets[0]['cells'][$i][4];
          $tipo_actividada=$data->sheets[0]['cells'][$i][6];
          $actividadeshijas = explode("/",$actividad);

          if (count($actividadeshijas)=='1'){
          }

          if (count($actividadeshijas)=='2'){
            //echo utf8_encode($actividadeshijas[1]); echo "<br>"; 
            $datosactividadpadre["actividadid"]=$numero;
            $datosactividadpadre["codigo_actividad"]=$proyecto."-".$numero;

            
            $editproyect= new Admin_Model_DbTable_Proyecto();
            $where = array(
            'proyectoid'    => $proyecto,
            );
            $edit = $editproyect->_getOnexcodigoproyecto($where);
            if ($edit)
            {
              //print_r($edit['codigo_prop_proy']); echo "<br>";
              $datosactividadpadre["codigo_prop_proy"]=$edit['codigo_prop_proy'];
              $datosactividadpadre["revision"]=$edit['revision'];
              $datosactividadpadre["areaid"]='00';
              $datosactividadpadre["proyectoid"]=$proyecto;
              $datosactividadpadre["propuestaid"]=$edit['propuestaid'];
              $datosactividadpadre["actividad_padre"]=null;
              $datosactividadpadre["nombre"]= utf8_encode(trim($actividadeshijas[1]));
              $datosactividadpadre["fecha_creacion"]=date("Y-m-d");
              $datosactividadpadre["estado"]='P';
              $datosactividadpadre["duracion_total"]='0';
              $datosactividadpadre["h_propuesta"]='0';
              $datosactividadpadre["h_extra"]='0';
              $datosactividadpadre["h_planificada"]='0';
              $datosactividadpadre["orden"]=$numero;
              $datosactividadpadre["isproyecto"]='S';
              $datosactividadpadre["moneda"]=$moneda;
             // print_r($datosactividadpadre);echo "<br>";
              
              $bdactividad = new Admin_Model_DbTable_Actividad();
              $existeactividad=$bdactividad->_existeactividad(utf8_encode(trim($actividadeshijas[1])),$proyecto,$edit['codigo_prop_proy'],$edit['revision'],$edit['propuestaid']);
              if ($existeactividad)
              {
                echo "----------------EXISTE LA ACTIVIDAD----------";
                print_r($existeactividad);  
                echo "<br>";
              }
              else {
                $guardaractividad=$bdactividad->_save($datosactividadpadre);
                if ($guardaractividad)
                  {
                    echo "---------SE GUARDO LA ACTIVIDAD ------";
                    echo "<br>";
                    $numero++;
                  }
                else
                  {
                    echo "------ ERROR NO SE GUARADO REVISARLO ---- ";
                    echo "<br>";
                  }
              }
            }
            else
            {
              echo "no existe proyecto";
            }
          }        
      }
    }
    catch (Exception $e) {
        print "Error: ".$e->getMessage();
    }
  }




  public function leerexcel2Action(){
    try {
      $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
      include ($dir);
      $data = new Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->read('replicon1.xls');
      $numero=1;
      for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
          $actividad=$data->sheets[0]['cells'][$i][7];
          $proyecto=$data->sheets[0]['cells'][$i][5];
          $moneda=$data->sheets[0]['cells'][$i][3];
          $rate=$data->sheets[0]['cells'][$i][4];
          $tipo_actividada=$data->sheets[0]['cells'][$i][6];
          $actividadeshijas = explode("/",$actividad);

          if (count($actividadeshijas)=='1'){
          }

          if (count($actividadeshijas)=='3'){
            //echo utf8_encode($actividadeshijas[1]); echo "<br>"; 
           
            $datosactividadpadre["codigo_actividad"]=$proyecto."-".$numero;

            
            $editproyect= new Admin_Model_DbTable_Proyecto();
            $where = array(
            'proyectoid'    => $proyecto,
            );
            $edit = $editproyect->_getOnexcodigoproyecto($where);
            if ($edit)
            {
              //print_r($edit['codigo_prop_proy']); echo "<br>";
              $datosactividadpadre["codigo_prop_proy"]=$edit['codigo_prop_proy'];
              $datosactividadpadre["revision"]=$edit['revision'];
              $datosactividadpadre["areaid"]='00';
              $datosactividadpadre["proyectoid"]=$proyecto;
              $datosactividadpadre["propuestaid"]=$edit['propuestaid'];
              
              $datosactividadpadre["nombre"]= utf8_encode(trim($actividadeshijas[2]));
              $datosactividadpadre["fecha_creacion"]=date("Y-m-d");
              $datosactividadpadre["estado"]='P';
              $datosactividadpadre["duracion_total"]='0';
              $datosactividadpadre["h_propuesta"]='0';
              $datosactividadpadre["h_extra"]='0';
              $datosactividadpadre["h_planificada"]='0';
              $datosactividadpadre["orden"]=$numero;
              $datosactividadpadre["isproyecto"]='S';
              $datosactividadpadre["moneda"]=$moneda;
             // print_r($datosactividadpadre);echo "<br>";
              
              $bdactividad = new Admin_Model_DbTable_Actividad();
              $existeactividad=$bdactividad->_existeactividad(utf8_encode(trim($actividadeshijas[1])),$proyecto,$edit['codigo_prop_proy'],$edit['revision'],$edit['propuestaid']);
              if ($existeactividad)
              {
                $datosactividadpadre["actividad_padre"]=$existeactividad[0]['actividadid'];
                $datosactividadpadre["actividadid"]=$existeactividad[0]['actividadid']."-".$numero;
                $guardaractividad=$bdactividad->_save($datosactividadpadre);
                //print_r($guardaractividad);
               /* if ($guardaractividad)
                  {
                    echo "---------SE GUARDO LA ACTIVIDAD ------";
                    echo "<br>";
                    $numero++;
                  }
                else
                  {
                    echo "------ ERROR NO SE GUARADO REVISARLO ---- ";
                    echo "<br>";

                  }*/                
              }
              else {

              echo "-----------no existe l actividad padre revisar------------------";                 }
            }
            else
            {
              echo "no existe proyecto";
            }
          }        
      }

    }
    catch (Exception $e) {
        print "Error: ".$e->getMessage();
    }
  }




  public function leerexceltareoAction(){
    try {
      $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
      include ($dir);
      $data = new Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->read('replicon1.xls');
      $numero=1;

    $columnas=$data->sheets[0]['numCols'];
        

      for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
          $actividad=$data->sheets[0]['cells'][$i][7];
          $proyecto=$data->sheets[0]['cells'][$i][5];
          $moneda=$data->sheets[0]['cells'][$i][3];
          $rate=$data->sheets[0]['cells'][$i][4];
          $tipo_actividada=$data->sheets[0]['cells'][$i][6];
          $actividadeshijas = explode("/",$actividad);

          if (count($actividadeshijas)=='1'){
          }

          if (count($actividadeshijas)=='2'){
            //echo utf8_encode($actividadeshijas[1]); echo "<br>"; 
            $datosactividadpadre["actividadid"]=$numero;
            $datosactividadpadre["codigo_actividad"]=$proyecto."-".$numero;

            
            $editproyect= new Admin_Model_DbTable_Proyecto();
            $where = array(
            'proyectoid'    => $proyecto,
            );
            $edit = $editproyect->_getOnexcodigoproyecto($where);
            if ($edit)
            {
                  //print_r($edit['codigo_prop_proy']); echo "<br>";
                  $datosactividadpadre["codigo_prop_proy"]=$edit['codigo_prop_proy'];
                  $datosactividadpadre["revision"]=$edit['revision'];
                  $datosactividadpadre["areaid"]='00';
                  $datosactividadpadre["proyectoid"]=$proyecto;
                  $datosactividadpadre["propuestaid"]=$edit['propuestaid'];
                  $datosactividadpadre["actividad_padre"]=null;
                  $datosactividadpadre["nombre"]= utf8_encode(trim($actividadeshijas[1]));
                  $datosactividadpadre["fecha_creacion"]=date("Y-m-d");
                  $datosactividadpadre["estado"]='P';
                  $datosactividadpadre["duracion_total"]='0';
                  $datosactividadpadre["h_propuesta"]='0';
                  $datosactividadpadre["h_extra"]='0';
                  $datosactividadpadre["h_planificada"]='0';
                  $datosactividadpadre["orden"]=$numero;
                  $datosactividadpadre["isproyecto"]='S';
                  $datosactividadpadre["moneda"]=$moneda;
                 // print_r($datosactividadpadre);echo "<br>";
                  ///// ACTIVIDAD   ////
                  /*$bdactividad = new Admin_Model_DbTable_Actividad();
                  $existeactividad=$bdactividad->_existeactividad(utf8_encode(trim($actividadeshijas[1])),$proyecto,$edit['codigo_prop_proy'],$edit['revision'],$edit['propuestaid']);
                  if ($existeactividad)
                  {
                    echo "----------------EXISTE LA ACTIVIDAD----------";
                    print_r($existeactividad);  
                    echo "<br>";
                  }
                  else {
                    $guardaractividad=$bdactividad->_save($datosactividadpadre);
                    if ($guardaractividad)
                      {
                        echo "---------SE GUARDO LA ACTIVIDAD ------";
                        echo "<br>";
                        $numero++;
                      }
                    else
                      {
                        echo "------ ERROR NO SE GUARADO REVISARLO ---- ";
                        echo "<br>";

                      }
                  }*/
                  ///// ACTIVIDAD   ////
                
                $dni=$data->sheets[0]['cells'][$i][1];
                $moneda=$data->sheets[0]['cells'][$i][3];
                $rate=$data->sheets[0]['cells'][$i][4];
                $codigoproyecto=$data->sheets[0]['cells'][$i][5];
                $tipo_actividad=$data->sheets[0]['cells'][$i][6];
                $actividad=$data->sheets[0]['cells'][$i][7];
                for ($j = 8; $j <= $columnas ; $j++) {
                  $horarealexiste=$data->sheets[0]['cells'][$i][$j];
                  $horaexiste=trim($horarealexiste);
                  

                 if (isset($horaexiste) )
                    {
                      //echo $data->sheets[0]['cells'][$i][$j];
                      //$hora=$horaexiste;

                      $bdtareo = new Admin_Model_DbTable_Tareopersona();
                      $fecha=$data->sheets[0]['cells'][1][$j];
                      $bdactividad = new Admin_Model_DbTable_Actividad();
                      $existeactividad=$bdactividad->_existeactividad(utf8_encode(trim($actividadeshijas[1])),$proyecto,$edit['codigo_prop_proy'],$edit['revision'],$edit['propuestaid']);
                      if ($existeactividad)
                      {
                        //print_r($existeactividad);

                            $datostareo["actividadid"]=$existeactividad[0]['actividadid'];
                            $datostareo["codigo_actividad"]=$existeactividad[0]['codigo_actividad'];
                            $datostareo["codigo_prop_proy"]=$existeactividad[0]['codigo_prop_proy'];
                            $datostareo["revision"]=$existeactividad[0]['revision'];
                            //$datostareo["areaid"]='00';
                            $datostareo["proyectoid"]=$proyecto;
                            $datostareo["actividad_padre"]='0';

                            $datostareo["fecha_tarea"]=$fecha;
                            $datostareo["fecha_creacion"]='2015-05-20';
                            $datostareo["semanaid"]=date('W', strtotime($fecha)); 
                            $datostareo["fecha_planificacion"]=$fecha;
                            $datostareo["asignado"]=$dni;
                            $datostareo["estado"]='C';
                            $datostareo["h_real"]=$horaexiste;
                            $datostareo["etapa"]='CIERRE';
                            $bdpersona = new Admin_Model_DbTable_Usuario();
                            
                            $where = array(
                            'dni'    => $dni,
                            );
                            $existepersona=$bdpersona->_getOne($where);
                            //print_r($existepersona);
                            if ($existepersona)
                            {
                              $datostareo["asignado"]=$dni;
                              $datostareo["dni"]=$dni;
                              $datostareo["uid"]=$existepersona['uid'];
                              $datostareo["areaid"]=$existepersona['areaid'];
                              $datostareo["categoriaid"]=$existepersona['categoriaid'];
                              $datostareo["cargo"]=$rate."-MONEDA-".$moneda;
                              $datostareo["tipo_actividad"]=$tipo_actividad;
                              //print_r($datostareo);echo  "<br>";
                              
                              if($bdtareo->_save($datostareo)){
                                $equipo = new Admin_Model_DbTable_Usuario();

                                  $dataequipo['areaid']=$existepersona['areaid'];
                                  $dataequipo['categoriaid']=$existepersona['categoriaid'];
                                  $dataequipo['proyectoid']=$proyecto;
                                  $dataequipo['codigo_prop_proy']=$existeactividad[0]['codigo_prop_proy'];
                                  $dataequipo['dni']=$dni;
                                  $dataequipo['uid']=$existepersona['uid'];
                                  $dataequipo['cargo']='EQUIPO';
                                  $dataequipo['fecha_ingreso']=date("Y-m-d");
                                  $dataequipo['estado']='A';
                                  $dataequipo['nivel']='4';
                                  $dataequipo['rate_proyecto']=$rate;
                                  $dataequipo['moneda']=$moneda;
                                 // print_r($dataequipo);

                                  $dbequipo = new Admin_Model_DbTable_Equipo();
                                  $dbequipo->_save($dataequipo);


                              echo "----------guardado -------";echo $dni;
                              echo  "<br>";
                              }
                              else
                              {
                                echo "---------ERROR NO GUARDO -----------"; echo $dni;
                              echo  "<br>";
                              }



                            }
                            else
                            { 
                              echo "---------ERROR NO EXISTE DNI -----------"; echo $dni;
                              echo  "<br>";

                            }
                           
                        }
                        else
                        {
                          echo "-----------NO EXISTE LA ACTIVIDAD ------";
                          echo "<br>";
                          /*if ($tipo_actividad='NB')
                          {
                              $datostareonofacturable["codigo_prop_proy"]=$edit['codigo_prop_proy'];
                              $datostareonofacturable["revision"]=$edit['revision'];
                              $datostareonofacturable["areaid"]='00';
                              $datostareonofacturable["proyectoid"]=$proyecto;
                              $datostareonofacturable["propuestaid"]=$edit['propuestaid'];
                              $datostareonofacturable["actividadid"]=;
                              $datostareonofacturable["codigo_actividad"]=$proyecto."-".$numero;


                   
                          }*/
                        }
                      
                      }
                  } 
              }

            else
            {
              echo "no existe proyecto";
            }

          }
        
      }


    }
    catch (Exception $e) {
        print "Error: ".$e->getMessage();
    }
  }




  public function leerexceltareo2Action(){
    try {
      $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
      include ($dir);
      $data = new Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->read('replicon1.xls');
      $numero=1;

    $columnas=$data->sheets[0]['numCols'];
        

      for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
          $actividad=$data->sheets[0]['cells'][$i][7];
          $proyecto=$data->sheets[0]['cells'][$i][5];
          $moneda=$data->sheets[0]['cells'][$i][3];
          $rate=$data->sheets[0]['cells'][$i][4];
          $tipo_actividada=$data->sheets[0]['cells'][$i][6];
          $actividadeshijas = explode("/",$actividad);

          if (count($actividadeshijas)=='1'){
          }

          if (count($actividadeshijas)=='3'){
            //echo utf8_encode($actividadeshijas[1]); echo "<br>"; 
            $datosactividadpadre["actividadid"]=$numero;
            $datosactividadpadre["codigo_actividad"]=$proyecto."-".$numero;

            
            $editproyect= new Admin_Model_DbTable_Proyecto();
            $where = array(
            'proyectoid'    => $proyecto,
            );
            $edit = $editproyect->_getOnexcodigoproyecto($where);
            if ($edit)
            {
                  //print_r($edit['codigo_prop_proy']); echo "<br>";
                 
                  $datosactividadpadre["codigo_prop_proy"]=$edit['codigo_prop_proy'];
                  $datosactividadpadre["revision"]=$edit['revision'];
                  $datosactividadpadre["areaid"]='00';
                  $datosactividadpadre["proyectoid"]=$proyecto;
                  $datosactividadpadre["propuestaid"]=$edit['propuestaid'];
                  $datosactividadpadre["actividad_padre"]=null;
                  $datosactividadpadre["nombre"]= utf8_encode(trim($actividadeshijas[1]));
                  $datosactividadpadre["fecha_creacion"]=date("Y-m-d");
                  $datosactividadpadre["estado"]='P';
                  $datosactividadpadre["duracion_total"]='0';
                  $datosactividadpadre["h_propuesta"]='0';
                  $datosactividadpadre["h_extra"]='0';
                  $datosactividadpadre["h_planificada"]='0';
                  $datosactividadpadre["orden"]=$numero;
                  $datosactividadpadre["isproyecto"]='S';
                  $datosactividadpadre["moneda"]=$moneda;
                 // print_r($datosactividadpadre);echo "<br>";
                  ///// ACTIVIDAD   ////
                  $bdactividad = new Admin_Model_DbTable_Actividad();
                  $existeactividad=$bdactividad->_existeactividad(utf8_encode(trim($actividadeshijas[1])),$proyecto,$edit['codigo_prop_proy'],$edit['revision'],$edit['propuestaid']);
                  /*if ($existeactividad)
                  {
                    echo "----------------EXISTE LA ACTIVIDAD----------";
                    print_r($existeactividad);  
                    echo "<br>";
                  }
                  else {
                    $guardaractividad=$bdactividad->_save($datosactividadpadre);
                    if ($guardaractividad)
                      {
                        echo "---------SE GUARDO LA ACTIVIDAD ------";
                        echo "<br>";
                        $numero++;
                      }
                    else
                      {
                        echo "------ ERROR NO SE GUARADO REVISARLO ---- ";
                        echo "<br>";

                      }
                  }*/
                  ///// ACTIVIDAD   ////
                
                $dni=$data->sheets[0]['cells'][$i][1];
                $moneda=$data->sheets[0]['cells'][$i][3];
                $rate=$data->sheets[0]['cells'][$i][4];
                $codigoproyecto=$data->sheets[0]['cells'][$i][5];
                $tipo_actividad=$data->sheets[0]['cells'][$i][6];
                $actividad=$data->sheets[0]['cells'][$i][7];

                for ($j = 8; $j <= $columnas ; $j++) {
                  $horarealexiste=$data->sheets[0]['cells'][$i][$j];
                  $horaexiste=trim($horarealexiste);
                  

                 if (isset($horaexiste) )
                    {
                      //echo $data->sheets[0]['cells'][$i][$j];
                      //$hora=$horaexiste;

                      $bdtareo = new Admin_Model_DbTable_Tareopersona();
                      $fecha=$data->sheets[0]['cells'][1][$j];
                      $bdactividad = new Admin_Model_DbTable_Actividad();
                      $existeactividadhija=$bdactividad->_existeactividadhija(utf8_encode(trim($actividadeshijas[2])),$proyecto,$edit['codigo_prop_proy'],$edit['revision'],$edit['propuestaid']);
                      if ($existeactividadhija)
                      {
                        

                        

                            $datostareo["actividadid"]=$existeactividadhija[0]['actividadid'];
                            $datostareo["codigo_actividad"]=$existeactividadhija[0]['codigo_actividad'];
                            $datostareo["codigo_prop_proy"]=$existeactividadhija[0]['codigo_prop_proy'];
                            $datostareo["revision"]=$existeactividadhija[0]['revision'];
                            //$datostareo["areaid"]='00';
                            $datostareo["proyectoid"]=$proyecto;
                            $datostareo["actividad_padre"]=$existeactividad[0]['actividadid'];

                            $datostareo["fecha_tarea"]=$fecha;
                            $datostareo["fecha_creacion"]='2015-05-20';
                            $datostareo["semanaid"]=date('W', strtotime($fecha)); 
                            $datostareo["fecha_planificacion"]=$fecha;
                            $datostareo["asignado"]=$dni;
                            $datostareo["estado"]='C';
                            $datostareo["h_real"]=$horaexiste;
                            $datostareo["etapa"]='CIERRE';
                            $bdpersona = new Admin_Model_DbTable_Usuario();
                            
                            $where = array(
                            'dni'    => $dni,
                            );
                            $existepersona=$bdpersona->_getOne($where);
                            //print_r($existepersona);
                            if ($existepersona)
                            {
                              $datostareo["asignado"]=$dni;
                              $datostareo["dni"]=$dni;
                              $datostareo["uid"]=$existepersona['uid'];
                              $datostareo["areaid"]=$existepersona['areaid'];
                              $datostareo["categoriaid"]=$existepersona['categoriaid'];
                              $datostareo["cargo"]=$rate."-MONEDA-".$moneda;
                              $datostareo["tipo_actividad"]=$tipo_actividad;
                             //print_r($datostareo);echo  "<br>";
                              
                              if($bdtareo->_save($datostareo)){
                                $equipo = new Admin_Model_DbTable_Usuario();

                                  $dataequipo['areaid']=$existepersona['areaid'];
                                  $dataequipo['categoriaid']=$existepersona['categoriaid'];
                                  $dataequipo['proyectoid']=$proyecto;
                                  $dataequipo['codigo_prop_proy']=$existeactividad[0]['codigo_prop_proy'];
                                  $dataequipo['dni']=$dni;
                                  $dataequipo['uid']=$existepersona['uid'];
                                  $dataequipo['cargo']='EQUIPO';
                                  $dataequipo['fecha_ingreso']=date("Y-m-d");
                                  $dataequipo['estado']='A';
                                  $dataequipo['nivel']='4';
                                  $dataequipo['rate_proyecto']=$rate;
                                  $dataequipo['moneda']=$moneda;
                                 // print_r($dataequipo);

                                  $dbequipo = new Admin_Model_DbTable_Equipo();
                                  $dbequipo->_save($dataequipo);


                              echo "----------guardado -------";echo $dni;
                              echo  "<br>";
                              }
                              else
                              {
                                echo "---------ERROR NO GUARDO -----------"; echo $dni;
                              echo  "<br>";
                              }



                            }
                            else
                            { 
                              echo "---------ERROR NO EXISTE DNI -----------"; echo $dni;
                              echo  "<br>";

                            }
                           
                        }
                        else
                        {
                          echo "-----------NO EXISTE LA ACTIVIDAD HIJA ------"; echo $actividadeshijas[2];
                          echo "-----actividadddddd----"; echo $existeactividad[0]['actividadid'];
                          echo "<br>";
                       
                        }
                      
                      }
                  } 
              }

            else
            {
              echo "no existe proyecto";
            }

          }
        
      }


    }
    catch (Exception $e) {
        print "Error: ".$e->getMessage();
    }
  }



  public function leerfechasAction(){
    try {
      $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
      include ($dir);
      $data = new Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->read('replicon1.xls');
      $numero=1;
    
$fila=$data->sheets[0]['numRows'];

for ($j = 8; $j <= $data->sheets[0]['numCols']-1; $j++) {
        for ($i = 2; $i <= $fila; $i++) {    
            $dni=$data->sheets[0]['cells'][$i][1];
            $moneda=$data->sheets[0]['cells'][$i][3];
            $rate=$data->sheets[0]['cells'][$i][4];
            $codigoproyecto=$data->sheets[0]['cells'][$i][5];
            $tipo_actividad=$data->sheets[0]['cells'][$i][6];
            $actividad=$data->sheets[0]['cells'][$i][7];
            $fecha1=$data->sheets[0]['cells'][1][$j];

            echo $dni; echo "-----";
            echo $moneda; echo "-----";
            echo $rate; echo "-----";
            echo $codigoproyecto; echo "-----";
            echo $tipo_actividad; echo "-----";
            echo utf8_encode($actividad); echo "-----";
             echo $fecha1; echo "-----";
             echo "<br>";

              $bdpersona = new Admin_Model_DbTable_Usuario();
                            
                            $where = array(
                            'dni'    => $dni,
                            );
                            $existepersona=$bdpersona->_getOne($where);
                            //print_r($existepersona);
                            if ($existepersona)
                            {
                              
                              
                                echo "---------existe -----------"; echo $dni;
                              echo  "<br>";




                            }
                            else
                            { 
                              echo "---------ERROR NOE EXISTE DNI -----------"; echo $dni;
                              echo  "<br>";

                            }

    }
}


    }
    catch (Exception $e) {
        print "Error: ".$e->getMessage();
    }
  }

public function subirpropuestaAction(){
  try {
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $bandera= $this->_getParam("bandera");
    if ($bandera=='S')
    {
    $this->view->bandera='S';}
    $editproyect= new Admin_Model_DbTable_Proyecto();
    $where = array(
      'codigo_prop_proy'    => $codigo_prop_proy,
      'proyectoid'    => $proyectoid,
    );
    $edit = $editproyect->_getOne($where);
    //print_r($edit);
    $this->view->proyectoid = $edit['proyectoid'];
    $this->view->codigo = $edit['codigo_prop_proy'];
    $this->view->propuestaid = $edit['propuestaid'];
    $this->view->revision = $edit['revision'];
    $this->view->moneda = $edit['moneda'];


    $nombre_fichero = './upload/proyecto/'.$proyectoid.'-HH.xls';
    if (file_exists($nombre_fichero)) {
      $this->view->bandera='S';      
      } else {
      $this->view->bandera='N';
      $this->view->cargar='S';
      }
    }
    catch (Exception $e) {
    print "Error: ".$e->getMessage();
  }
}

// Subir actvidades segun la nueva estructura //
public function subiractividadesAction(){
  try {
    $this->_helper->layout()->disablelayout();
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo");
    $editproyect= new Admin_Model_DbTable_Proyecto();
    $where = array(
                      'codigo_prop_proy'    => $codigo_prop_proy,
                      'proyectoid'    => $proyectoid,
                      );
    $edit = $editproyect->_getOne($where);
 
    $proyectoid = $edit['proyectoid'];

    $codigo = $edit['codigo_prop_proy'];
    $propuestaid = $edit['propuestaid'];
    $revision = $edit['revision'];
    $moneda = $edit['moneda'];
    $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
    include ($dir);
    $data = new Spreadsheet_Excel_Reader();
    $data->setOutputEncoding('CP1251');
    $data->read('./upload/proyecto/'.$proyectoid.'-HH.xls');
    $k=1;
    $columnas=$data->sheets[0]['numCols'];
    $filas=$data->sheets[0]['numRows'];
    print_r($columnas);
    //migrar actividades
    for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
      //$colsuma=$columnas-1;
      $actividadid=$data->sheets[0]['cells'][$i][1];
      $nombre=$data->sheets[0]['cells'][$i][2];
      //echo $actividadid; echo "<br>";
      //$suma=$data->sheets[0]['cells'][$i][$colsuma];
      $actividadint=$actividadid;
      //print_r($actividadint);
      if (ctype_digit(trim($actividadint))) {
        $k++;
        $j=0;
        $datosactividadpadre["actividadid"]=$actividadint;
        $datosactividadpadre["codigo_actividad"]=$proyectoid."-".$actividadid;
        $datosactividadpadre["codigo_prop_proy"]=$codigo;
        $datosactividadpadre["revision"]=$revision;
        //$datosactividadpadre["areaid"]=$areaid;
        $datosactividadpadre["proyectoid"]=$proyectoid;
        $datosactividadpadre["propuestaid"]=$propuestaid;
        $datosactividadpadre["actividad_padre"]='0';

        $datosactividadpadre["nombre"]=utf8_encode($nombre);
        $datosactividadpadre["fecha_creacion"]=date("Y-m-d");
        $datosactividadpadre["estado"]='P';
        $datosactividadpadre["duracion_total"]='0';
        $datosactividadpadre["h_propuesta"]='';
        $datosactividadpadre["h_extra"]='0';
        $datosactividadpadre["h_planificada"]='0';
        $datosactividadpadre["orden"]=$k-1;
        $datosactividadpadre["isproyecto"]='S';
        $datosactividadpadre["hijo"]='S';
        $datosactividadpadre["moneda"]=$moneda;
        $bdactividad = new Admin_Model_DbTable_Actividad();
        print_r($datosactividadpadre);
        if($bdactividad->_save($datosactividadpadre))
         {echo $actividadint;
          echo ": guardo bien actividad padre";  echo "<br>"; }
        
        } 
        else {
        $actividadeshijas = explode(".",$actividadint);
          if (count($actividadeshijas)=='2'){
            $j++;
            $datosactividadhija["actividadid"]=$actividadint;
            $datosactividadhija["codigo_actividad"]=$proyectoid."-".$actividadid;
            $datosactividadhija["codigo_prop_proy"]=$codigo;
            $datosactividadhija["revision"]=$revision;
            //$datosactividadhija["areaid"]=$areaid;
            $datosactividadhija["proyectoid"]=$proyectoid;
            $datosactividadhija["propuestaid"]=$propuestaid;
            $datosactividadhija["actividad_padre"]=$actividadeshijas[0];
            $datosactividadhija["nombre"]=utf8_encode($nombre);
            $datosactividadhija["fecha_creacion"]=date("Y-m-d");
            $datosactividadhija["estado"]='P';
            $datosactividadhija["duracion_total"]='0';
            $datosactividadhija["h_propuesta"]='';
            $datosactividadhija["h_extra"]='0';
            $datosactividadhija["h_planificada"]='0';
            $datosactividadhija["orden"]=$k.".".$j-1;
            $datosactividadhija["isproyecto"]='S';
            $datosactividadhija["hijo"]='N';
            $datosactividadhija["moneda"]=$moneda;
            $bdactividad = new Admin_Model_DbTable_Actividad();
           // print_r($datosactividadhija);
          if($bdactividad->_save($datosactividadhija))
            { echo $actividadint;
              echo "guardo bien actividad disciplina"; echo "<br>";
            }
            }

            if (count($actividadeshijas)=='3'){
            $datosactividadnieta["actividadid"]=$actividadint;
            $datosactividadnieta["codigo_actividad"]=$proyectoid."-".$actividadid;
            $datosactividadnieta["codigo_prop_proy"]=$codigo;
            $datosactividadnieta["revision"]=$revision;
            $datosactividadnieta["areaid"]=$areaid;
            $datosactividadnieta["proyectoid"]=$proyectoid;
            $datosactividadnieta["propuestaid"]=$propuestaid;
            $datosactividadnieta["actividad_padre"]=$actividadeshijas[0].".".$actividadeshijas[1];

            $datosactividadnieta["nombre"]=utf8_encode($nombre);
            $datosactividadnieta["fecha_creacion"]=date("Y-m-d");
            $datosactividadnieta["estado"]='P';
            $datosactividadnieta["duracion_total"]='0';
            $datosactividadnieta["h_propuesta"]=$suma;
            $datosactividadnieta["h_extra"]='0';
            $datosactividadnieta["h_planificada"]='0';
            $datosactividadnieta["orden"]=$i-1;
            $datosactividadnieta["isproyecto"]='S';
            $datosactividadnieta["moneda"]=$moneda;
            $bdactividad = new Admin_Model_DbTable_Actividad();
           
            if($bdactividad->_save($datosactividadnieta))
            {echo $actividadint;
              echo "guardo bien tarea";echo "<br>";
            }
           

            }
      }
    }


    }
  catch (Exception $e) {
    print "Error: ".$e->getMessage();
  }
}

public function verAction() {
    //$this->_helper->layout()->disablelayout();
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $propuestaid= $this->_getParam("propuesta");
    $revision= $this->_getParam("revision");
    $this->view->codigoproyecto=$codigo_prop_proy;
    $this->view->proyectoid=$proyectoid;
    $this->view->propuestaid=$propuestaid;
    $this->view->revision=$revision;

    $bandera= $this->_getParam("bandera");
    if ($bandera=='S')
    {
    $this->view->bandera='S';}
    $editproyect= new Admin_Model_DbTable_Proyecto();
    $where = array(
      'codigo_prop_proy'    => $codigo_prop_proy,
      'proyectoid'    => $proyectoid,
    );
    $edit = $editproyect->_getOne($where);
    $this->view->proyecto = $edit; 

    $codigo=$this->_getParam('codigo_prop_proy');
    $propuestaid=$edit['propuestaid'];
    $revision=$edit['revision'];
    $buscapropuesta = new Admin_Model_DbTable_Propuesta();
    $busca=$buscapropuesta->_getPropuestaxIndices($codigo,$propuestaid,$revision);
    $this->view->buscapropuesta = $busca; 
    $areacat=new Admin_Model_DbTable_Area();
    $arcat=$areacat->_getAreaxProyecto();
    $this->view->area = $arcat;  

    $data['proyectoid'] = $proyectoid;   
    $data['estado'] = 'A';   
    $orders=array('uid asc');
    $bdequipo = new Admin_Model_DbTable_Equipo();
    $equipo=$bdequipo->_getFilter($data,$attrib=null,$orders);
    $this->view->equipototal = $equipo;  
    
}   


    //Devuelve los datos de un proyecto en particular
  public function proyectoAction()
    {
      $data['proyectoid'] = $this->_getParam('proyectoid');
      $proyecto = new Admin_Model_DbTable_Proyecto();
      $datos = $proyecto->_getOnexProyectoidExtendido($data);
      $respuesta['codigo_prop_proy'] = $datos['codigo_prop_proy'];
      $respuesta['codigo'] = $datos['proyectoid'];
      $respuesta['nombre'] = $datos['nombre_proyecto'];
      $respuesta['clienteid'] = $datos['clienteid'];
      $respuesta['cliente'] = $datos['nombre_comercial'];
      $respuesta['unidad_minera'] = $datos['nombre'];
      $respuesta['estado'] = $datos['estado'];
      $respuesta['fecha_inicio'] = $datos['fecha_inicio'];
      $respuesta['fecha_cierre'] = $datos['fecha_cierre'];
      $respuesta['control_documentario'] = $datos['control_documentario'];
      $respuesta['descripcion'] = $datos['descripcion'];
      $respuesta['tipo_proyecto'] = $datos['tipo_proyecto'];
      //$ruta = APPLICATION_PATH.'/../img/cliente/'.$respuesta['clienteid'].'.jpg';
      //if(is_file($ruta)){
        $respuesta['logo_cliente'] = '../img/cliente/'.$respuesta['clienteid'].'.jpg';
      //} else {
      //  $respuesta['logo_cliente'] = '../img/cliente/anddes.jpg';
      //}
      $this->_helper->json->sendJson($respuesta);
  }

    //Devuelve los datos de un proyecto en particular
  public function proyectoxcronogramaAction()
  {
      $data['proyectoid'] = $this->_getParam('proyectoid');   


      $proyectoxcronograma = new Admin_Model_DbTable_Proyectocronograma();
      $datos = $proyectoxcronograma->_getFilter($data);

      if($datos)
      {
          $respuesta = [];
          $data = [];
          $i = 0;

          foreach ($datos as $item) {
             $data['codigo_prop_proy'] = $item['codigo_prop_proy'];
             $data['codigo_cronograma'] = $item['codigo_cronograma'];
             $data['revision_cronograma'] = $item['revision_cronograma'];
             $data['proyectoid'] = $item['proyectoid'];
             //$data['revision_propuesta'] = $item['revision_propuesta'];
             $data['cronogramaid'] = $item['cronogramaid'];  
             $data['state'] = $item['state'];
             $respuesta[$i] = $data;
             $i++;
          }

      }
      else
      {
        $respuesta = [];
      }
      $this->_helper->json->sendJson($respuesta);
  }


public function guardarxproyectoxcronogramaAction()
{

  $data['codigo_cronograma'] = $this->_getParam('codigocronograma');   
  $data['revision_cronograma'] = $this->_getParam('revision');   
  $data['state'] =$this->_getParam('estado');   
  $data['codigo_prop_proy'] = $this->_getParam('codigo_prop_proy');   
  $data['proyectoid'] = $this->_getParam('proyectoid');


  $datastate['state']='I';
  $wherestate = array(                    
                     'codigo_prop_proy' => $data['codigo_prop_proy'],
                     'proyectoid' => $data['proyectoid'],
                    );

  $modficarcronograma=new Admin_Model_DbTable_Proyectocronograma();
  $mcronograma=$modficarcronograma->_update_state($datastate,$wherestate);

  $guardarcronograma=new Admin_Model_DbTable_Proyectocronograma();
  $gcronograma=$guardarcronograma->_save($data);


  //exit();
  $this->_helper->json->sendJson($gcronograma);
}



public function modificarxproyectoxcronogramaAction()
{
//codigocronograma/' + codigocronograma+"/revision/"+revision+"/codigoproyecto/"+codigoproyecto+"/proyectoid/"+proyectoid
  $data['codigo_cronograma'] = $this->_getParam('codigocronograma');   
  $data['revision_cronograma'] = $this->_getParam('revision');
  $data['codigo_prop_proy'] = $this->_getParam('codigo_prop_proy');   
  $data['proyectoid'] = $this->_getParam('proyectoid');  
  $data['cronogramaid'] = $this->_getParam('cronogramaid'); 
  //$data['state'] = $this->_getParam('state'); 

  $where = array('codigo_prop_proy' =>  $data['codigo_prop_proy'],
          //'codigo_cronograma' => $data['codigo_cronograma'],
          //'revision_cronograma' => $data['revision_cronograma'],
          'cronogramaid' => $data['cronogramaid'],
          'proyectoid' =>  $data['proyectoid'] );


  $modificarcronograma=new Admin_Model_DbTable_Proyectocronograma();
  $mcronograma=$modificarcronograma->_update($data,$where);

  $this->_helper->json->sendJson($mcronograma);

}

public function eliminarxproyectoxcronogramaAction()
{
  $data['codigo_prop_proy'] = $this->_getParam('codigoproyecto');   
  $data['proyectoid'] = $this->_getParam('proyectoid');  
  $data['cronogramaid'] = $this->_getParam('cronogramaid');

  $eliminarcronograma=new Admin_Model_DbTable_Proyectocronograma();
  $ecronograma=$eliminarcronograma->_delete($data);

  $this->_helper->json->sendJson($ecronograma);

}
  
public function listaproyectosAction()
{
      $estado = $this->_getParam('estado');
      $proyecto = new Admin_Model_DbTable_Proyecto();
      $proyectos = $proyecto->_getAllExtendido($estado);
      $respuesta = [];
      $data = [];
      $i = 0;

      foreach ($proyectos as $item) {
        $data['codigo'] = $item['proyectoid'];
        $data['codigo_prop_proy'] = $item['codigo_prop_proy'];
        $data['cliente'] = $item['nombre_comercial'];
        $data['nombre'] = $item['nombre_proyecto'];
        $data['gerente'] = $item['gerente_proyecto'];
        $data['control_proyecto'] = $item['control_proyecto'];
        $data['control_documentario'] = $item['control_documentario'];
        $data['estado'] = $item['estado'];
        $respuesta[$i] = $data;
        $i++;
      }
      $this->_helper->json->sendJson($respuesta);
}


public function setcambioestadoproyectoAction() {
  $estado = $this->_getParam('estado');
  $proyectoid = $this->_getParam('codigo');
  $codigo_prop_proy = $this->_getParam('codigoproyecto');

  $where = array('codigo_prop_proy' => $codigo_prop_proy, 'proyectoid' => $proyectoid );
  $data['estado']=$estado;

  print_r($where);
  print_r($data);exit();

  $cambiarestadoproyecto = new Admin_Model_DbTable_Proyecto();
  $cestadoproyecto=$cambiarestadoproyecto->_update($data,$where);
  
  $this->_helper->json->sendJson($cestadoproyecto);
}

public function verjsonAction() {
 
    $bdequipoarea = new Admin_Model_DbTable_Equipoarea();
    $area=$bdequipoarea->_buscarAreasxProyecto('14.10.134-1101.10.09-D','1101.10.09');
    $i=0;
    foreach ($area as $verarea) {
      $bdequipo = new Admin_Model_DbTable_Equipo();
      $equipo=$bdequipo->_buscarEquipoxProyectoxArea('14.10.134-1101.10.09-D','1101.10.09',$verarea['areaid']);

      $ek[] = array('name' =>$verarea['nombre'],'area'=>$verarea['areaid'] ,'items'=> $equipo);
      $i++;
    }    
    //es el formato de renderiar :  [{"bbb":"1","name":"2","items":[{"nombre":"books"}]}]
    //$arr = array(['bbb' =>'1', 'name' => '2','items'=>  array(['nombre' =>'books'])]);
    $this->_helper->json->sendJson($ek);  

}



public function gettareoxactividadesxproyectoAction() {
  $proyectoid = $this->_getParam("proyectoid");
  $fecha_inicio = $this->_getParam("fecha_inicio");
  $fecha_corte = $this->_getParam("fecha_corte");
  $actividadid = $this->_getParam("actividadid");

  $tareopersona=new Admin_Model_DbTable_Performance();
  $tpersona=$tareopersona->_getSumaxHoraxTareopxActividades($proyectoid,$fecha_inicio,$fecha_corte,$actividadid);
  
  $i=0;
  $ek=[];
  foreach ($tpersona as $value) {   
      $ek['actividadid']=$actividadid;
      $ek['suma']=$value['suma']; 
    $i++;
  }
  $this->_helper->json->sendJson($ek);  
}

public function usuariosjsonAction() {
  $user=new Admin_Model_DbTable_Usuario();
  $us=$user->_getUsuarioAll();
  $this->_helper->json->sendJson($us);  
}


public function modificarperformanceAction() {

   $codigo_prop_proy = $this->_getParam("codigo_prop_proy");
   $codigo_actividad = $this->_getParam("codigo_actividad");
   $actividadid = $this->_getParam("actividadid");
   $cronogramaid = $this->_getParam("cronogramaid");
   $codigo_cronograma = $this->_getParam("codigo_cronograma");
   $codigo_performance = $this->_getParam("codigo_performance");
   $porcentaje_performance = $this->_getParam("porcentaje_performance");


   $proyectoid = $this->_getParam("proyectoid");
   $revision_cronograma = $this->_getParam("revision_cronograma");
   $fecha_ingreso_performance = $this->_getParam("fecha_ingreso_performance"); 
   $fecha_performance = $this->_getParam("fecha_performance");

   $where = array('codigo_prop_proy' => $codigo_prop_proy,'codigo_actividad' => $codigo_actividad,'actividadid' => $actividadid,
   'cronogramaid' => $cronogramaid,'codigo_cronograma' => $codigo_cronograma,'codigo_performance' => $codigo_performance,
   'proyectoid' => $proyectoid,'revision_cronograma' => $revision_cronograma,'fecha_performance' => $fecha_performance );

   $data = array( 'porcentaje_performance' => $porcentaje_performance,
   'fecha_ingreso_performance' => date("Y-m-d"));

   $modperformancedetalles=new Admin_Model_DbTable_Performancedetalle();
   $mpdetalle=$modperformancedetalles->_update($data,$where);



   $this->_helper->json->sendJson($mpdetalle);  
}

public function modificarperformancepadreAction() {
  $codigo_prop_proy = $this->_getParam("codigo_prop_proy");
   $codigo_actividad = $this->_getParam("codigo_actividad");
   $actividadid = $this->_getParam("actividadid");
   $cronogramaid = $this->_getParam("cronogramaid");
   $codigo_cronograma = $this->_getParam("codigo_cronograma");
   $codigo_performance = $this->_getParam("codigo_performance");   
   $proyectoid = $this->_getParam("proyectoid");
   $revision_cronograma = $this->_getParam("revision_cronograma");

   $costo_real = $this->_getParam("costo_real");  
   $horas_real = $this->_getParam("horas_real");  
   $costo_propuesta = $this->_getParam("costo_propuesta");  
   $horas_propuesta = $this->_getParam("horas_propuesta");  
   $horas_planificado = $this->_getParam("horas_planificado");  
   $costo_planificado = $this->_getParam("costo_planificado");  
   $porcentaje_planificado = $this->_getParam("porcentaje_planificado");  
   $porcentaje_real = $this->_getParam("porcentaje_real"); 

   $fecha_comienzo_real = $this->_getParam("fecha_comienzo_real");  
   $fecha_fin_real = $this->_getParam("fecha_fin_real");
   $fecha_fin = $this->_getParam("fecha_fin"); 
   $fecha_comienzo = $this->_getParam("fecha_comienzo");
   $nivel_esquema = $this->_getParam("nivel_esquema");
   $predecesoras = $this->_getParam("predecesoras");

   $sucesoras = $this->_getParam("sucesoras");    
   $duracion = $this->_getParam("duracion"); 

   if($costo_real=='null'){ echo $costo_real=''; }
   if($horas_real=='null'){ echo $horas_real=''; }
   if($costo_propuesta=='null'){ echo $costo_propuesta=''; }
   if($horas_propuesta=='null'){ echo $horas_propuesta=''; }
   if($horas_planificado=='null' or $horas_planificado=='NaN'){ echo $horas_planificado=''; }
   if($costo_planificado=='null' or $costo_planificado=='NaN'){ echo $costo_planificado=''; }
   if($porcentaje_planificado=='null' or $porcentaje_planificado=='NaN' ){ echo $porcentaje_planificado=''; }
   if($porcentaje_real=='null'){ echo $porcentaje_real=''; }
   if($fecha_comienzo_real=='null'){ echo $fecha_comienzo_real=''; }
   if($fecha_fin_real=='null'){ echo $fecha_fin_real=''; }

   if($nivel_esquema=='null'){ echo $nivel_esquema=''; }
   if($predecesoras=='null'){ echo $predecesoras=''; }
   if($sucesoras=='null'){ echo $sucesoras=''; }
   if($duracion=='null' or $porcentaje_planificado=='NaN'){ echo $duracion=''; }
   if($fecha_comienzo=='null' or $fecha_comienzo=='NaN-NaN-NaN' or $fecha_comienzo=='NaN' or $fecha_comienzo==null ){ echo $fecha_comienzo=null; }
   if($fecha_fin=='null' or $fecha_fin=='NaN-NaN-NaN' or $fecha_fin=='NaN' or $fecha_fin==null){ echo $fecha_fin=null; }   

   $predecesoras = str_replace(" ", "+", $predecesoras);

   $where = array('codigo_prop_proy' => $codigo_prop_proy,'codigo_actividad' => $codigo_actividad,'actividadid' => $actividadid,
   'cronogramaid' => $cronogramaid,'codigo_cronograma' => $codigo_cronograma,'codigo_performance' => $codigo_performance,
   'proyectoid' => $proyectoid,'revision_cronograma' => $revision_cronograma,);

  $data = array('costo_real' => $costo_real,'horas_real' => $horas_real,
  'costo_propuesta' => $costo_propuesta,'horas_propuesta' => $horas_propuesta,
  'horas_planificado' => $horas_planificado,'costo_planificado' => $costo_planificado,
  'porcentaje_planificado' => $porcentaje_planificado,'porcentaje_real' => $porcentaje_real,
  
  'fecha_comienzo_real' => $fecha_comienzo_real,'fecha_fin_real' => $fecha_fin_real,
  'fecha_fin' => $fecha_fin,'fecha_comienzo' => $fecha_comienzo,
  'predecesoras' => $predecesoras,
  'nivel_esquema' => $nivel_esquema,
  'sucesoras' => $sucesoras,'duracion' => $duracion,
  'fecha_ingreso_performance' => date("Y-m-d")
   );

  $modificarperformance= new Admin_Model_DbTable_Performance();
  $mperformance=$modificarperformance->_update($data,$where);

  $this->_helper->json->sendJson($mperformance);  

}

 
public function cronogramaxactivoAction() {
  $proyectoid = $this->_getParam("proyectoid");
  $proyectocronograma= new Admin_Model_DbTable_Proyectocronograma();
  $pcronograma=$proyectocronograma->_getCronogramaxActivo($proyectoid);
  if($pcronograma)
  {

  }
  else
  {
    $pcronograma=[];
  }

  $this->_helper->json->sendJson($pcronograma); 
}




public function proyectoxperformanceAction() {
  $proyectoid = $this->_getParam("proyectoid");
  $revision = $this->_getParam("revision");
  
  $proyecto= new Admin_Model_DbTable_Proyecto();
  $codigo = array('proyectoid' =>$proyectoid, );
  $datosproyecto = $proyecto->_getOnexcodigoproyecto($codigo);
  $fecha_inicio=$datosproyecto['fecha_inicio'];
  $fecha_final=$datosproyecto['fecha_cierre'];

  $performance=new Admin_Model_DbTable_Performance(); 
  $perf=$performance->_getBuscarActividadxPerformance($proyectoid,$revision);

  $state_fechacorte=new Admin_Model_DbTable_Proyectofechacorte();
  $where = array('revision_cronograma' => $revision ,
               'proyectoid' => $proyectoid ,'state_performance' =>'A');
  
  $fecha_corte=$state_fechacorte->_getFilter($where);
  $fecha_corte_activa=$fecha_corte[0]['fecha'];

  if($perf)
  {
      $i=0;
      foreach ($perf as $keyper) 
      {
        if(strlen($keyper['actividadid'])=='1')
        {
          $actividadpadre=true;
          //print_r($actividadid);exit();
        }
        else
        {
          $actividadpadre=false;
        }
       
        $wheredet['codigo_prop_proy']=$keyper['codigo_prop_proy'];
        $wheredet['codigo_actividad']=$keyper['codigo_actividad']; 
        $wheredet['proyectoid']=$keyper['proyectoid'];      
        $wheredet['cronogramaid']=$keyper['cronogramaid'];
        $wheredet['codigo_cronograma']=$keyper['codigo_cronograma'];
        $wheredet['revision_cronograma']=$keyper['revision_cronograma'];
        $wheredet['actividadid']=$keyper['actividadid'];
        $wheredet['codigo_performance']=$keyper['codigo_performance']; 
        $attrib = null;
        $order = array('fecha_performance asc');

        $performancedetalle=new Admin_Model_DbTable_Performancedetalle();
        $pdetalle=$performancedetalle->_getFilter($wheredet,$attrib,$order);
        
        $shorastareo=$performance->_getSumaxHoraxTareopxActividades($proyectoid,$fecha_inicio,$fecha_corte_activa,$keyper['actividadid']);
        $costohoras=$performance->_getCostoxHoraxTareopxActividades($proyectoid,$fecha_inicio,$fecha_corte_activa,$keyper['actividadid']);
        $horas_tareo=$shorastareo[0]['suma'];  
        $costohoras=$costohoras[0]['costo'];  

        if($keyper['costo_propuesta']==null || $keyper['costo_propuesta']=='null')
        {
          $porcentaje_real=0;
        }
        else
        {
        $porcentaje_real= round((floatval($costohoras)/floatval($keyper['costo_propuesta']))*100);
        }

        //$porcentaje_planificado= (floatval($keyper['costo_planificado'])/floatval($keyper['costo_propuesta']))*100;

        $ek[] = array(
          'nombre' =>$keyper['nombre'],
          //'fecha_corte_activa'=> $fecha_corte_activa,
          'actividadpadre'=>$actividadpadre,
          'codigo_prop_proy' =>$keyper['codigo_prop_proy'],
          'proyectoid' =>$keyper['proyectoid'],
          'codigo_actividad' =>$keyper['codigo_actividad'],
          'actividadid' =>$keyper['actividadid'],
          'cronogramaid' =>$keyper['cronogramaid'],
          'codigo_cronograma' =>$keyper['codigo_cronograma'],
          'revision_cronograma' =>$keyper['revision_cronograma'],
          'codigo_performance' =>$keyper['codigo_performance'],
          'revision_propuesta' =>$keyper['revision_propuesta'],
          'fecha_ingreso_performance' =>$keyper['fecha_ingreso_performance'],
          'costo_real' =>$costohoras,
          'costo_propuesta' =>$keyper['costo_propuesta'],
          'horas_propuesta' =>$keyper['horas_propuesta'],
          'horas_planificado' =>$keyper['horas_planificado'],
          'costo_planificado' =>$keyper['costo_planificado'],

          'duracion' =>$keyper['duracion'],         
          'porcentaje_planificado' =>$keyper['porcentaje_planificado'],
          'porcentaje_real' =>$porcentaje_real,        

          'fecha_comienzo_real' =>$keyper['fecha_comienzo_real'],
          'fecha_fin_real' =>$keyper['fecha_fin_real'],
          'fecha_comienzo' =>$keyper['fecha_comienzo'],
          'fecha_fin' =>$keyper['fecha_fin'],
          
          'nivel_esquema' =>$keyper['nivel_esquema'],
          'predecesoras' =>$keyper['predecesoras'],
          'sucesoras' =>$keyper['sucesoras'],     

          'horas_real' =>$horas_tareo,
          'items'=> $pdetalle);
    
        $i++;  
      } 
  }
  else
  {
    $ek=[];
  }
 


  $this->_helper->json->sendJson($ek);

}


public function curvasjsonAction() {

  $revision_perf_curva = $this->_getParam("revision");
  $proyectoid = $this->_getParam("proyectoid");
  $codigo_prop_proy = $this->_getParam("codigo");  

  $where = array('proyectoid'=>$proyectoid,'revision_cronograma'=>$revision_perf_curva,'codigo_prop_proy' =>$codigo_prop_proy );
  $attrib = array('fecha_curvas','fecha_ingreso_curvas','porcentaje_ejecutado','porcentaje_propuesta','codigo_curvas','revision_cronograma');
  $order = array('fecha_curvas ASC');

  $tiempo=new Admin_Model_DbTable_Tiempoproyecto();
  $tmp=$tiempo->_getFilter($where,$attrib,$order);

  $arr = array(['1' =>$tmp]);
  $this->_helper->json->sendJson($arr);

}

public function cambiarfechaproyetoAction(){
    $value= $this->_getParam("value");
    $id= $this->_getParam("id");
    $column= $this->_getParam("column");

    $data[$column]=$value;
    $data['fecha_ingreso_curvas']=date("Y-m-d");
    $pk = array('codigo_curvas' => $id, );

    $fecha_proyecto= new Admin_Model_DbTable_Tiempoproyecto();   
    $fproyecto=$fecha_proyecto->_update($data,$pk);

    $this->_helper->json->sendJson($fproyecto);    
}

public function guardarcurvaAction(){

    $cronogramaid= $this->_getParam("cronogramaid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $proyectoid= $this->_getParam("proyectoid");
    //echo  $codigo_curvas= $this->_getParam("codigo_curvas");
    $fecha_curvas= $this->_getParam("fecha_curvas");
    $porcentaje_ejecutado= $this->_getParam("porcentaje_ejecutado");
    $porcentaje_propuesta= $this->_getParam("porcentaje_propuesta");
    $revision_cronograma= $this->_getParam("revision_cronograma");
    $codigo_cronograma= $this->_getParam("codigo_cronograma");
    $revision_propuesta= $this->_getParam("revision_propuesta");


    $data = array('codigo_prop_proy' => $codigo_prop_proy,'codigo_cronograma' => $codigo_cronograma,
    'proyectoid' => $proyectoid,
    //'codigo_curvas' => $codigo_curvas,
    'revision_cronograma' => $revision_cronograma,'fecha_curvas' => $fecha_curvas,
    'fecha_ingreso_curvas'=>$fecha_ingreso_curvas,
    'porcentaje_ejecutado' => $porcentaje_ejecutado,'porcentaje_propuesta' => $porcentaje_propuesta,
    'cronogramaid' => $cronogramaid, 'revision_propuesta' => $revision_propuesta );


    $guardarcurva=new Admin_Model_DbTable_Tiempoproyecto();
    $gcurva=$guardarcurva->_save($data);

    //exit();
}

public function eliminarcurvaAction(){
  $codigo_curvas= $this->_getParam("codigo_curvas");
  //echo $codigo_curvas;
  $where = array('codigo_curvas' =>$codigo_curvas, );
  $delcurvas=new Admin_Model_DbTable_Tiempoproyecto();
  $dcurvas=$delcurvas->_delete($where);

  $this->_helper->json->sendJson($dcurvas);

}


public function datosedtAction(){
  $proyecto= $this->_getParam("proyectoid");
  //echo $proyecto;

  $edt= new Admin_Model_DbTable_ProyectoEdt();
  $veredt=$edt->_getEdtxProyectoid($proyecto);

  $this->_helper->json->sendJson($veredt);

}

public function setguardaredtAction(){
    $proyectoid= $this->_getParam("proyectoid");
    $nombre= $this->_getParam("nombre");
    $descripcion= $this->_getParam("descripcion");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $codigo= $this->_getParam("codigoedt");

    $data = array('codigo_prop_proy' => $codigo_prop_proy, 
        'nombre_edt' => $nombre,
        'descripcion_edt' => $descripcion,
        'proyectoid' => $proyectoid,
        'codigo_edt' => $codigo,
    );

    $guardaredt=new Admin_Model_DbTable_ProyectoEdt();
    $gedt=$guardaredt->_save($data);

    $this->_helper->json->sendJson($gedt);

}

public function seteliminaredtAction(){
  $codigoedt= $this->_getParam("codigoedt");
  $codigoproyecto= $this->_getParam("codigoproyecto");
  $proyectoid= $this->_getParam("proyectoid");
  $where = array('codigo_edt' => $codigoedt,'proyectoid' => $proyectoid,'codigo_prop_proy' =>$codigoproyecto );
  $eliminaredt=new Admin_Model_DbTable_ProyectoEdt();
  $eedt=$eliminaredt->_delete($where);
  $this->_helper->json->sendJson($eedt);

}


public function setmodificaredtAction(){

    $proyectoid= $this->_getParam("proyectoid");
    $codigoedt= $this->_getParam("codigoedt");
    $codigoproyecto= $this->_getParam("codigoproyecto");
    
    $codigoedtmodificado= $this->_getParam("codigoedtmodificado");
    $nombremodificado= $this->_getParam("nombremodificado");
    $descripcionmodificado= $this->_getParam("descripcionmodificado");

    $data = array('codigo_edt' => $codigoedtmodificado, 'nombre_edt'=> $nombremodificado,'descripcion_edt'=> $descripcionmodificado);
    $pk = array('codigo_edt' => $codigoedt,
      'codigo_prop_proy' => $codigoproyecto,
      'proyectoid' => $proyectoid,      
     );

    $modificaredt=new Admin_Model_DbTable_ProyectoEdt();
    $medt=$modificaredt->_update($data,$pk);  

    $this->_helper->json->sendJson($medt);

}

public function proyectoxfechaxcorteAction()
{
  $codigoproy= $this->_getParam("codigoproy");
  $proyectoid= $this->_getParam("proyectoid");
  $revision= $this->_getParam("revision");
  $where = array('proyectoid' =>$proyectoid , 'revision_cronograma' =>$revision,'codigo_prop_proy' =>$codigoproy);  
  $attrib = null;
  $order = array('fecha ASC');

  $fechaxcorte=new Admin_Model_DbTable_Proyectofechacorte();
  $fcorte=$fechaxcorte->_getFilter($where,$attrib,$order);

  $this->_helper->json->sendJson($fcorte);

}

public function eliminardxfechaxcorteAction()
{
  $fechacorteid= $this->_getParam("fechacorteid");
  $where = array('fechacorteid' => $fechacorteid , );
  $eliminarfechaxcorte=new Admin_Model_DbTable_Proyectofechacorte();
  $efechacorte=$eliminarfechaxcorte->_delete($where);;

  $this->_helper->json->sendJson($efechacorte);
}

public function guardarxfechaxcorteAction()
{
  $data['fecha']= $this->_getParam("fechacorte");
  $data['revision_cronograma']= $this->_getParam("revision");
  $data['codigo_prop_proy']= $this->_getParam("codigoproyecto");
  $data['proyectoid']= $this->_getParam("proyectoid");
  $data['tipo_corte']= $this->_getParam("tipocorte");
  $data['state']= 'A';

  $guardarfechaxcorte=new Admin_Model_DbTable_Proyectofechacorte();
  $where = array('revision_cronograma' => $data['revision_cronograma'] ,
               'proyectoid' => $data['proyectoid'] ,'state_performance' =>'A');
  $vfechaxcorte=$guardarfechaxcorte->_getFilter($where);

  if($vfechaxcorte)
  {
    $state_per='I';
  }
  else
  {
    $state_per='A';
  }
  $data['state_performance']= $state_per;
  $gfechaxcorte=$guardarfechaxcorte->_save($data);
// print_r($gfechaxcorte);exit();
 $this->_helper->json->sendJson($gfechaxcorte);
}

public function cerrarxfechaxcorteAction()
{
   $proyectoid= $this->_getParam("proyectoid");
   $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
   $fecha_corte= $this->_getParam("fecha_corte");

   $fechacorte_cambiar = $this->_getParam("fechacorte_cambiar");
   //echo $fechacorte_cambiar=isset($fechacorte_cambiar); 
  $fechacorte=new Admin_Model_DbTable_Proyectofechacorte();
  $copy=$fechacorte->_postActualizarPerformanceDetalle($proyectoid,$codigo_prop_proy);


  $data1['state_performance']='C';  
  $where_anterior = array('proyectoid' => $proyectoid,'codigo_prop_proy' => $codigo_prop_proy,'fechacorteid' => $fecha_corte,);
  $fcorte_anterior=$fechacorte->_update($data1,$where_anterior);


  if($fechacorte_cambiar=='undefined')
  {
   //echo 'dd';
  }else{
   
    $data2['state_performance']='A';

    $where_posterior = array('proyectoid' => $proyectoid,'codigo_prop_proy' => $codigo_prop_proy,'fechacorteid' => $fechacorte_cambiar,);
    $fcorte_posterior=$fechacorte->_update($data2,$where_posterior);
  }

  $this->_helper->json->sendJson($copy);

}

public function cambiarxfechaxcorteAction()
{
  $valorcolumna= $this->_getParam("valorcolumna");
  $codigoproyecto= $this->_getParam("codigoproyecto");
  $proyectoid= $this->_getParam("proyectoid");
  $fechacorteid= $this->_getParam("fechacorteid");
  $columna= $this->_getParam("columna");

  $pk = array('codigo_prop_proy' =>  $codigoproyecto ,
    'proyectoid' => $proyectoid , 
    'fechacorteid' =>$fechacorteid , 
  );

  $data[$columna]=$valorcolumna;
  $modificarfechaxcorte=new Admin_Model_DbTable_Proyectofechacorte();
  $mfechaxcorte=$modificarfechaxcorte->_update($data,$pk);
  $this->_helper->json->sendJson($mfechaxcorte);
}

public function generarrevisionAction()
{
  $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
  $proyectoid= $this->_getParam("proyectoid");

  $generarrevision=new Admin_Model_DbTable_Proyectofechacorte();
  $data = array('codigo_prop_proy' =>$codigo_prop_proy,'proyectoid' =>$proyectoid,);

  $grevision=$generarrevision->_getRevisionxGenerar($data);

  $this->_helper->json->sendJson($grevision);

}

public function getentregablesAction()
{
$proyectoid= $this->_getParam("proyectoid");
$where = array('proyectoid' =>$proyectoid );
$listaentregable=new Admin_Model_DbTable_Listaentregable();
$lentregable=$listaentregable->_getFilter($where);
$this->_helper->json->sendJson($lentregable);
}

public function getlistaentregablesAction()
{
  $proyectoid= $this->_getParam("proyectoid");
  $revision_entregable= $this->_getParam("revision");
  //$area= $this->_getParam("area");

  //$where = array('proyectoid' =>$proyectoid ,'revision_entregable'=>$revision_entregable);
  //$attrib=null;
  //$order = array('disciplina ASC');

  $listaentregable=new Admin_Model_DbTable_Listaentregabledetalle();
  //$lentregable=$listaentregable->_getFilter($where,$attrib,$order);
  $lentregable=$listaentregable->_getFilteristaentregable($proyectoid, $revision_entregable);

  //print_r($lentregable);exit();

  $this->_helper->json->sendJson($lentregable);
}



public function setguardarentregablesAction()
{
  $data['proyectoid']= $this->_getParam("proyectoid");
  $data['codigo_prop_proy']= $this->_getParam("codigoproyecto");
  $data['revision_entregable']= $this->_getParam("revisionentregable");
  $data['state']= 'A';
  $datastate['state']='I';

  $where = array('codigo_prop_proy' =>$data['codigo_prop_proy'],'proyectoid' =>$data['proyectoid'],);
 
  $modficarentregable=new Admin_Model_DbTable_Listaentregable();
  $mentregable=$modficarentregable->_update_state($datastate,$where);

  $guardarentregable=new Admin_Model_DbTable_Listaentregable();
  $gentregable=$guardarentregable->_save($data);

  $this->_helper->json->sendJson($gentregable);

  
}


public function setguardarlistaentregablesAction()
{
  $data['proyectoid']= $this->_getParam("proyectoid");
  $data['codigo_prop_proy']= $this->_getParam("codigo_prop_proy");
  $data['revision_entregable']= $this->_getParam("revision_entregable");
  $data['edt']= $this->_getParam("edt");
  $data['tipo_documento']= $this->_getParam("tipo_documento");
  $data['disciplina']= $this->_getParam("disciplina");
  $data['codigo_anddes']= $this->_getParam("codigo_anddes");
  $data['codigo_cliente']= $this->_getParam("codigo_cliente");
  $data['fecha_0']= $this->_getParam("fecha_0");
  $data['fecha_a']= $this->_getParam("fecha_a");
  $data['fecha_b']= $this->_getParam("fecha_b");
  $data['descripcion_entregable']= $this->_getParam("descripcion_entregable");
  $data['clase']= 'Tecnico';
  $data['revision_documento']= 'A';
  $data['estado']= 'Ultimo';

  if($data['tipo_documento']=='null' or $data['tipo_documento']=='undefined' ){ echo $data['tipo_documento']=''; }
  if($data['disciplina']=='null' or $data['disciplina']=='undefined' ){ echo $data['disciplina']=''; }
  if($data['codigo_anddes']=='null' or $data['codigo_anddes']=='undefined' ){ echo $data['codigo_anddes']=''; }
  if($data['codigo_cliente']=='null' or $data['codigo_cliente']=='undefined' ){ echo $data['codigo_cliente']=''; }
  if($data['fecha_0']=='null' or $data['fecha_0']=='undefined' ){ echo $data['fecha_0']=''; }
  if($data['fecha_a']=='null' or $data['fecha_a']=='undefined' ){ echo $data['fecha_a']=''; }
  if($data['fecha_b']=='null' or $data['fecha_b']=='undefined' ){ echo $data['fecha_b']=''; }
  if($data['descripcion_entregable']=='null' or $data['descripcion_entregable']=='undefined' ){ echo $data['descripcion_entregable']=''; }

  // $whereone['proyectoid']=$this->_getParam("proyectoid");
  // $whereone['codigo_prop_proy']=$this->_getParam("codigo_prop_proy");
  // $whereone['revision_entregable']=$this->_getParam("revision_entregable");
  // $whereone['edt']=$this->_getParam("edt");

  $whereone['cod_le']=$this->_getParam("cod_le");

  $actualizarlistaentregable=new Admin_Model_DbTable_Listaentregabledetalle();

  if($whereone['cod_le']=='undefined')
  {
    $glentregable=$actualizarlistaentregable->_save($data);
  }
  else
  {    
    $glentregable=$actualizarlistaentregable->_update($data,$whereone);
  }

  $this->_helper->json->sendJson($glentregable);
}


public function seteliminarentregableAction()
{
  $cod_le= $this->_getParam("id");

  $eliminarentregable=new Admin_Model_DbTable_Listaentregabledetalle();

  $whereone['cod_le']=$this->_getParam("id");
  $data['estado_entregable']=10;

  $eentregable=$eliminarentregable->_update($data,$whereone);

  //$eentregable=$eliminarentregable->_deleteEntregable($cod_le);

  $this->_helper->json->sendJson($eentregable);
}

public function setcambiarestadolentregableAction()
{
  $data['estado_entregable']= $this->_getParam("valor");
  $disciplina= $this->_getParam("area");
  $codigo_prop_proy= $this->_getParam("codigoproyecto");
  $proyectoid= $this->_getParam("proyectoid");
  $revision_entregable= $this->_getParam("revision");
  $gerente= $this->_getParam("gerente");
  $status= $this->_getParam("status");
 
  if($gerente=='S' and $status=='gr')
  {

    $pk = array('codigo_prop_proy' =>$codigo_prop_proy , 
                'proyectoid' =>$proyectoid , 
                'revision_entregable' =>$revision_entregable,
                'disciplina' =>null
              );  
  }
  else
  {
    $pk = array('codigo_prop_proy' =>$codigo_prop_proy , 
                'proyectoid' =>$proyectoid , 
                'revision_entregable' =>$revision_entregable,
                'disciplina' =>$disciplina
              );    
  }

  $estadolentregable=new Admin_Model_DbTable_Listaentregabledetalle();
  $elentregable=$estadolentregable->_update_state($data,$pk);
  //exit();
  $this->_helper->json->sendJson($elentregable);


}


public function getleersessionusuarioAction()
{
  
  $proyectoid= $this->_getParam("proyectoid");

  $session['uid']=$this->sesion->uid;


  $equipo = new Admin_Model_DbTable_Equipo();
  $where = array('proyectoid' =>$proyectoid,'uid'=>$session['uid']);
  $equiporoles=$equipo->_getFilter($where);

  $session['areaid']=$equiporoles[0]['areaid'];
  $session['cargo']=$equiporoles[0]['cargo'];


  foreach ($equiporoles as $value) {     
    if($value['nivel']=='0' )
    {
      $is_gerente='S';
    }
    else
    {
      $is_gerente='N';     
    }

    if($value['nivel']=='3'  )
    {
       $is_responsable='S';
    }
    else
    {
       $is_responsable='N';      
    }

    if($value['cargo']=='JEFE'  )
    {
       $is_jefearea='S';
    }
    else
    {
       $is_jefearea='N';      
    }
  }

  $session['is_jefe']=$is_jefearea;
  $session['is_responsableproyecto']=$is_responsable;
  $session['is_gerente']=$is_gerente;

  // if($session['uid']==$is_gerente['uid'])
  // {
  //     $session['is_gerente']='S';
  //     $session['areaid']=$is_gerente['areaid'];
  // }
  // else
  // {
  //     $session['is_gerente']='N';      
  // }

  // if($session['uid']==$is_responsable['uid'])
  // {
  //   $session['is_responsableproyecto']='S';
  //   $session['areaid']=$is_responsable['areaid'];
  // }
  // else
  // {
  //   $session['is_responsableproyecto']='N';  
    
  // }

  // if($session['uid']==$is_jefearea['uid'])
  // {
  //   $session['is_jefe']='S';
  //   $session['areaid']=$is_jefearea['areaid'];    
  // }
  // else
  // {
  //   $session['is_jefe']='N';
  // }

  //print_r($session);exit();

  $this->_helper->json->sendJson($session);
}

public function getleerestadoslistaentregableAction()
{
  $proyectoid= $this->_getParam("proyectoid");
  $areaid= $this->_getParam("areaid");
  $gerente= $this->_getParam("gerente");
  $jefearea= $this->_getParam("jefearea");
  $responsable= $this->_getParam("responsable");
  $entregableactivo= new Admin_Model_DbTable_Listaentregable();
  $eactivo=$entregableactivo->_getentregablexActivo($proyectoid);

  if($eactivo)
  {
    $revisionentregable=$eactivo[0]['revision_entregable'];
    $leerestadoLE=new Admin_Model_DbTable_Listaentregabledetalle();
    $estados = array('1','2','3','4','5','6','7','8','9');     
    if($gerente=='S' and $jefearea=='S')
    {

      $leerLE=$leerestadoLE->_getFilteristaentregable($proyectoid,$revisionentregable);    
      $numberlista=count($leerLE);

      $leerLE1=$leerestadoLE->_getFilteristaentregablexArea($proyectoid,$revisionentregable,$areaid);  
      $numberlista1=count($leerLE1);
      /*  GERENTE GENERAL*/
      //$concentrar=[]; 
      for ($i=0; $i <count($estados) ; $i++) {        
        $cont[$i]=0;   
          
        foreach ($leerLE as $value) 
        {
          if($value['estado_entregable']==$estados[$i])
          { 
            $cont[$i]=$cont[$i]+1;     
          } 
        }       
      }       
     
      $indice=1;
      $data1=[];

      foreach ($cont as $value) {
        if($value==$numberlista)
        {
          $data1['state']=true;
          $data1['indice']=$indice;
          $data1['status']='gr';
        }
        $indice++;
      }  

      /*  AREAID*/
      /************************/
        for ($j=0; $j <count($estados) ; $j++) { 
          
          $cont1[$j]=0;   
          
          foreach ($leerLE1 as $value) 
          {
            if($value['estado_entregable']==$estados[$j])
            { 
              $cont1[$j]=$cont1[$j]+1;     
            } 
          }       
        }       
      //print_r($cont1);
      $indice1=1;
      $data2=[];

      foreach ($cont1 as $value) {
        if($value==$numberlista1)
        {
          $data2['state']=true;
          $data2['indice']=$indice1;
          $data2['status']='jf';
        }
        $indice1++;
      }

      if($data1)
      {
        $data=$data1;
      
      }
      else
      {
        $data=$data2;
      }

    }
    else
    {

      if($gerente=='S' and $jefearea=='N')
      {
          $data=[];
          $leerLE=$leerestadoLE->_getFilteristaentregable($proyectoid,$revisionentregable);    
          $data['status']='gr';
      }
      else
      {
          $data=[];      
          $leerLE=$leerestadoLE->_getFilteristaentregablexArea($proyectoid,$revisionentregable,$areaid);
          //echo "llego akakak";
          //print_r($leerLE);
      }

      $numberlista=count($leerLE);
      //echo $numberlista;
      if($numberlista==0)
      {
        $data=[];
      }
      else
      {
         for ($i=0; $i <count($estados) ; $i++) {        
          $cont[$i]=0;
          foreach ($leerLE as $value) 
          {
            if($value['estado_entregable']==$estados[$i])
            { 
              $cont[$i]=$cont[$i]+1;     
            } 
          }       
        } 
        //print_r($cont);   
        $indice=0;
        foreach ($cont as $value) 
        {
          if($value==$numberlista)
          {    
            $data['state']=true;
            $data['indice']=$indice+1; 
            //echo $value;    
          }
          $indice++;
        }    
        //  print_r($data);
        //  exit();     
      }


    }
  }
  else
  {
    $data=[];
  }

  $this->_helper->json->sendJson($data);
  
}

public function disciplinasAction() {
  $proyectoid= $this->_getParam("proyectoid");
  $gerente= $this->_getParam("gerente");
  $areaid= $this->_getParam("areaid");

  $equipoarea=new Admin_Model_DbTable_Equipoarea();
  //if($gerente=='S')
  //{
  $earea=$equipoarea->_buscarAreasxProyectoid($proyectoid);    
  //}
  //else
  //{
  //  $earea=$equipoarea->_buscarAreasxProyectoidxArea($proyectoid,$areaid);    
  //}

  $this->_helper->json->sendJson($earea);
}

public function setguardarxporcentajexcurvasAction() 
{
  $proyectoid= $this->_getParam("proyectoid");
  $revision= $this->_getParam("revision");
  $fecha= $this->_getParam("fecha");
  $data['porcentaje_ejecutado']= $this->_getParam("porcentaje");
  $where = array('proyectoid' =>$proyectoid,'revision_cronograma' =>$revision,'fecha_curvas' =>$fecha);
  // print_r($where);
  // print_r($data);
  // exit();
  $uporcentajecurvas=new Admin_Model_DbTable_Tiempoproyecto(); 
  $uporc=$uporcentajecurvas->_update_revision($data,$where);
  $this->_helper->json->sendJson($uporc);
}

public function getdatosxperformancexllamarAction() 
{
  $proyectoid= $this->_getParam("proyectoid");
  $fechaperformance= $this->_getParam("fechaperformance");
  $revision_cronograma= $this->_getParam("revision_cronograma");
  $codigoproy= $this->_getParam("codigoproy");
  
 
  $verperformance= new Admin_Model_DbTable_Performancedetalle();  
  $traerperformance=$verperformance->_getBuscarActividadxPerformancexfecha($proyectoid,$revision_cronograma,$fechaperformance,$codigoproy);

  $i=0;
  foreach ($traerperformance as $keyper) 
  {
    if(strlen($keyper['codigo_actividad'])=='1')
    {
      $actividadpadre=true;
    }
    else
    {
          $actividadpadre=false;
        }
    $wheredet['codigo_prop_proy']=$keyper['codigo_prop_proy'];
    $wheredet['codigo_actividad']=$keyper['codigo_actividad']; 
    $wheredet['proyectoid']=$keyper['proyectoid'];      
    $wheredet['cronogramaid']=$keyper['cronogramaid'];
    $wheredet['codigo_cronograma']=$keyper['codigo_cronograma'];
    $wheredet['revision_cronograma']=$keyper['revision_cronograma'];
    $wheredet['actividadid']=$keyper['actividadid'];
    $wheredet['codigo_performance']=$keyper['codigo_performance']; 
    $attrib = null;
    $order = array('fecha_performance asc');

    $performancedetalle=new Admin_Model_DbTable_Performancedetalle();
    $pdetalle=$performancedetalle->_getFilter($wheredet,$attrib,$order);

    $ek[] = array(
      'nombre' =>$keyper['nombre'],       
      'actividadpadre'=>$actividadpadre,
      'codigo_prop_proy' =>$keyper['codigo_prop_proy'],
      'proyectoid' =>$keyper['proyectoid'],
      'codigo_actividad' =>$keyper['codigo_actividad'],
      'actividadid' =>$keyper['actividadid'],
      'cronogramaid' =>$keyper['cronogramaid'],
      'codigo_cronograma' =>$keyper['codigo_cronograma'],
      'revision_cronograma' =>$keyper['revision_cronograma'],
      'codigo_performance' =>$keyper['codigo_performance'],   
      'fecha_ingreso_performance' =>$keyper['fecha_ingreso_performance'],
      'costo_real' =>$keyper['costo_real'],
      'costo_propuesta' =>$keyper['costo_propuesta'],
      'horas_propuesta' =>$keyper['horas_propuesta'],
      'horas_planificado' =>$keyper['horas_planificado'],
      'costo_planificado' =>$keyper['costo_planificado'],
      'duracion' =>$keyper['duracion'],         
      'porcentaje_planificado' =>$keyper['porcentaje_planificado'],
      'porcentaje_real' =>$keyper['porcentaje_real'],        
      'fecha_comienzo_real' =>$keyper['fecha_comienzo_real'],
      'fecha_fin_real' =>$keyper['fecha_fin_real'],
      'fecha_comienzo' =>$keyper['fecha_comienzo'],
      'fecha_fin' =>$keyper['fecha_fin'],
      'nivel_esquema' =>$keyper['nivel_esquema'],
      'predecesoras' =>$keyper['predecesoras'],
      'sucesoras' =>$keyper['sucesoras'],
      'horas_real' =>$keyper['horas_real'],
      'items'=> $pdetalle);
      
      $i++;  
  } 

  //print_r($ek);exit();

  $this->_helper->json->sendJson($ek);


}


////////////////// /F I N  D E  F U N C I O N E S  A N G U L A R //////

public function subirareacategoriaAction() {
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo");
    $editproyect= new Admin_Model_DbTable_Proyecto();
    $where = array(
                      'codigo_prop_proy'    => $codigo_prop_proy,
                      'proyectoid'    => $proyectoid,
                      );
    $edit = $editproyect->_getOne($where);
    $proyectoid = $edit['proyectoid'];
    $codigo = $edit['codigo_prop_proy'];
    $propuestaid = $edit['propuestaid'];
    $revision = $edit['revision'];
    $moneda = $edit['moneda'];
    $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
    include ($dir);
    $data = new Spreadsheet_Excel_Reader();
    $data->setOutputEncoding('CP1251');
    $data->read('./upload/proyecto/'.$proyectoid.'-HH.xls');
    //$data->read('proyectosss.xls');
    $columnas=$data->sheets[0]['numCols'];

    $filas=$data->sheets[0]['numRows'];
   // for ($i = 2; $i <= $filas; $i++) {
      for ($j =5; $j <= $columnas ; $j++) {
        //$areaid=$data->sheets[0]['cells'][$i][1];
        //$actividadid=$data->sheets[0]['cells'][$i][2];    
        $categoria=$data->sheets[0]['cells'][1][$j];
        $categoria_hija = explode("_",$categoria);
        if (count($categoria_hija)==2)
        {
            $bdcategoria = new Admin_Model_DbTable_Categoria();
            $datoscat=$bdcategoria-> _buscarCategoriaxTag(strtolower($categoria_hija[0]));
            $idcategoria=$datoscat[0]['categoriaid'];
        }
        if (count($categoria_hija)==3)
        {
            $areaid=$categoria_hija[0];
            $bdcategoria = new Admin_Model_DbTable_Categoria();
            $datoscat=$bdcategoria-> _buscarCategoriaxTag(strtolower($categoria_hija[1]));
            $idcategoria=$datoscat[0]['categoriaid'];
            $rate=$categoria_hija[2];
            $dataequipoarea['areaid']=$areaid;
            $dataequipoarea['codigo_prop_proy']=$codigo;
            $dataequipoarea['proyectoid']=$proyectoid;
            $dataequipoarea['categoriaid']=$idcategoria;
            $dataequipoarea['fecha_creacion']=date("Y-m-d");
            $dataequipoarea['estado']='A';
            $dataequipoarea['funcion']='PROYECTO';
            $bdequipo_area = new Admin_Model_DbTable_Equipoarea();
            $bdequipo_area->_save($dataequipoarea);
        }
    }
}   

public function subirtareoAction() {
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo");
    $editproyect= new Admin_Model_DbTable_Proyecto();
    $where = array(
        'codigo_prop_proy'    => $codigo_prop_proy,
        'proyectoid'    => $proyectoid,
    );
    $edit = $editproyect->_getOne($where);
    $proyectoid = $edit['proyectoid'];
    $codigo = $edit['codigo_prop_proy'];
    $propuestaid = $edit['propuestaid'];
    $revision = $edit['revision'];
    $moneda = $edit['moneda'];
    $dir = APPLICATION_LIBRARY . "/excel/excel/reader.php";
    include ($dir);
    $data = new Spreadsheet_Excel_Reader();
    $data->setOutputEncoding('CP1251');
    $data->read('./upload/proyecto/'.$proyectoid.'-HH.xls');
    //$data->read('proyectosss.xls');
    $columnas=$data->sheets[0]['numCols'];
    $filas=$data->sheets[0]['numRows'];
    for ($i = 2; $i <= $filas; $i++) 
    {
        for ($j = 5; $j <= $columnas-2 ; $j++) 
        {
            $actividadid=$data->sheets[0]['cells'][$i][1];
            $categoria=$data->sheets[0]['cells'][1][$j];
            $categoria_hija = explode("_",$categoria);
            $nombre_actividad=$data->sheets[0]['cells'][$i][2];
            $areaid=$categoria_hija[0];
            if (isset( $data->sheets[0]['cells'][$i][$j] ))
            {
                $suma=$data->sheets[0]['cells'][$i][$j];
                $horas_propuesta=utf8_encode($data->sheets[0]['cells'][$i][$j]);
                $bdcategoria = new Admin_Model_DbTable_Categoria();
                $datoscat=$bdcategoria-> _buscarCategoriaxTag(strtolower($categoria_hija[1]));
                $idcategoria=$datoscat[0]['categoriaid'];
                $actividadint=$actividadid;
                $actividadeshijas = explode(".",$actividadint);
                if (count($actividadeshijas)=='2')
                {
                    $codigo_actividad=$actividadid;
                    $bdtareo = new Admin_Model_DbTable_Tareo();
                    $existe_tareo=$bdtareo->_getTareoxProyectoxActividadHijaxAreaxCategoria($proyectoid,$codigo,$revision,$actividadeshijas[0],$actividadid,$codigo_actividad,$areaid,$idcategoria);
                    if(isset($existe_tareo)) 
                    { 
                        $datostareo["actividadid"]=$actividadid;
                        $datostareo["codigo_actividad"]=$actividadid;
                        $datostareo["codigo_prop_proy"]=$codigo;
                        $datostareo["revision"]=$revision;
                        $datostareo["areaid"]=$areaid;
                        $datostareo["proyectoid"]=$proyectoid;
                        $datostareo["actividad_padre"]=$actividadeshijas[0];
                        $datostareo["fecha_creacion"]=date("Y-m-d");
                        $datostareo["estado"]='P';
                        $datostareo["h_propuesta"]=$suma;
                        $datostareo["duracion"]='0';
                        $datostareo["h_extra"]='0';
                        $datostareo["h_planificada"]='0';
                        $datostareo["h_real"]='0';
                        $datostareo["isproyecto"]='S';
                        $datostareo["categoriaid"]=$idcategoria;
                        $datostareo["nombre"]=utf8_encode($nombre_actividad);
                        if($bdtareo->_save($datostareo))
                        {
                            echo "guardado ";
                        }
                    }
                }

                if (count($actividadeshijas)=='3')
                {
                    $codigo_actividad=$areaid."-".$actividadid;
                    $bdtareo = new Admin_Model_DbTable_Tareo();
                    $existe_tareo=$bdtareo->_getTareoxProyectoxActividadHijaxAreaxCategoria($proyectoid,$codigo,$revision,$actividadeshijas[0],$actividadid,$codigo_actividad,$areaid,$idcategoria);
                    if(isset($existe_tareo)) 
                    { 
                        $datostareo2["actividadid"]=$actividadid;
                        $datostareo2["codigo_actividad"]=$areaid."-".$actividadid;
                        $datostareo2["codigo_prop_proy"]=$codigo;
                        $datostareo2["revision"]=$revision;
                        $datostareo2["areaid"]=$areaid;
                        $datostareo2["proyectoid"]=$proyectoid;
                        $datostareo2["actividad_padre"]=$actividadeshijas[0].".".$actividadeshijas[1];
                        $datostareo2["fecha_creacion"]=date("Y-m-d");
                        $datostareo2["estado"]='P';
                        $datostareo2["h_propuesta"]=$suma;
                        $datostareo2["duracion"]='0';
                        $datostareo2["h_extra"]='0';
                        $datostareo2["h_planificada"]='0';
                        $datostareo2["h_real"]='0';
                        $datostareo2["isproyecto"]='S';
                        $datostareo2["categoriaid"]=$idcategoria;
                        $datostareo2["nombre"]=utf8_encode($nombre_actividad);
                    }
                }
            }
        }   
    }
}   
  
public function cargarhorasAction() {
    $ano=date("Y");
    $semana=date("W");
    /*echo "semana nro: ".(date("W"));
    echo "dia del mes nro: ".(date("j"));
    echo "# dias de la semana".(date("N"));*/
    $dias = array('lunes', 'martes', 'miercoles', 
    'jueves', 'viernes', 'sabado','domingo');
    $enero = mktime(1,1,1,1,1,$ano); 
    //$mos = (11-date('w',1))%7-3; 
    $mos = (11-date('w',$enero))%7-3;
    $inicios = strtotime(($semana-1) . ' weeks '.$mos.' days', $enero); 
    for ($x=0; $x<=6; $x++) {
    $dias[] = date('d/m/Y', strtotime("+ $x day", $inicios));
    $dia[] = date('w', strtotime("+ $x day", $inicios));
    }
    $this->view->diassemana=$dias;
    $this->view->semanalabor=$semana;
}

public function cargartareaAction() {
        $codigo='PROP-2015-20209133394-1407-15.10.053-B';
        $proyectoid='1215.10.25';
        $propuestaid='15.10.053';
        $revision='B';

        $editproyect= new Admin_Model_DbTable_Proyecto();
        $where = array(
                      'codigo_prop_proy'    => $codigo,
                      'proyectoid'    => $proyectoid,
                      );
        $edit = $editproyect->_getOne($where);
        $this->view->proyecto = $edit;
         
    }

public function cargartarea2Action() {
   $this->_helper->layout()->disablelayout();
        $codigo='PROP-2015-20209133394-1407-15.10.053-B';
        $proyectoid='1215.10.25';
        $propuestaid='15.10.053';
        $revision='B';

        $editproyect= new Admin_Model_DbTable_Proyecto();
        $where = array(
                      'codigo_prop_proy'    => $codigo,
                      'proyectoid'    => $proyectoid,
                      );
        $edit = $editproyect->_getOne($where);
        $this->view->proyecto = $edit;


        $actividadpadre= new Admin_Model_DbTable_Actividad();
        $list=$actividadpadre->_getActividadesPadres($proyectoid,$codigo,$propuestaid,$revision);
          $this->view->list = $list;
    }

    public function cargartarea3Action() {
   $this->_helper->layout()->disablelayout();
        $codigo='PROP-2015-20209133394-1407-15.10.053-B';
        $proyectoid='1215.10.25';
        $propuestaid='15.10.053';
        $revision='B';
        $actividadid= $this->_getParam("actividadid");

        $editproyect= new Admin_Model_DbTable_Proyecto();
        $where = array(
                      'codigo_prop_proy'    => $codigo,
                      'proyectoid'    => $proyectoid,
                      );
        $edit = $editproyect->_getOne($where);
        $this->view->proyecto = $edit;


        $actividadpadre= new Admin_Model_DbTable_Actividad();
        $list=$actividadpadre->_getActividadesHijas($proyectoid,$codigo,$propuestaid,$revision,$actividadid);
          $this->view->list = $list;
    }



    public function cargartarea4Action() {
   $this->_helper->layout()->disablelayout();
        $codigo='PROP-2015-20209133394-1407-15.10.053-B';
        $proyectoid='1215.10.25';
        $propuestaid='15.10.053';
        $revision='B';
        $actividadid= $this->_getParam("disciplinaid");

        $editproyect= new Admin_Model_DbTable_Proyecto();
        $where = array(
                      'codigo_prop_proy'    => $codigo,
                      'proyectoid'    => $proyectoid,
                      );
        $edit = $editproyect->_getOne($where);
        $this->view->proyecto = $edit;


        $actividadpadre= new Admin_Model_DbTable_Actividad();
        $list=$actividadpadre->_getActividadesHijas($proyectoid,$codigo,$propuestaid,$revision,$actividadid);
          $this->view->list = $list;
    }

  public function buscarcategoriaAction() {
    $this->_helper->layout()->disablelayout();
    $areaid= $this->_getParam("areaid");
    $bdarea_cat = new Admin_Model_DbTable_Areacategoria();
    $listcat=$bdarea_cat->_buscarCategoriaxAreaxProyecto($areaid);
    $this->view->categoria = $listcat;
  }
  
  /* al parecer no es necesario esta accion*/
  public function buscarpersonasxcategoriaAction() {
    $this->_helper->layout()->disablelayout();
    $areaid= $this->_getParam("areaid");
    $categoriaid= $this->_getParam("categoria");
    $proyectoid= $this->_getParam("proyectoid");
    $codigo= $this->_getParam("codigo_prop_proy");
    $this->view->areaid = $areaid;
    $this->view->categoriaid = $categoriaid;
    $this->view->proyectoid = $proyectoid;
    $this->view->codigo = $codigo;

    $wheres=array('areaid'=>$areaid);

    $order = array('uid ASC');
    $usercat=new Admin_Model_DbTable_Usuariocategoria();
    $ucat=$usercat->_getFilter($wheres,$attrib=null,$order);
    //$this->view->usercat = $ucat;
    //print_r($ucat);

   // $bdarea_cat = new Admin_Model_DbTable_Usuariocategoria();
   // $listusuarios=$bdarea_cat->_buscarUsuarioxAreaxCategoria($areaid,$categoriaid);
    $this->view->listusuarios = $ucat;
  }

  

  public function guardarpersonaequipoAction() {
    $this->_helper->layout()->disablelayout();
    $dataequipo['areaid']=$areaid= $this->_getParam("areaid");
    $dataequipo['categoriaid']=$categoriaid= $this->_getParam("categoriaid");
    $dataequipo['proyectoid']=$proyectoid= $this->_getParam("proyectoid");
    $dataequipo['codigo_prop_proy']=$codigo= $this->_getParam("codigo");
    $dataequipo['dni']=$dni= $this->_getParam("dni");
    $dataequipo['uid']=$uid= $this->_getParam("uid");
    $dataequipo['cargo']=$cargo= $this->_getParam("cargo");
    $dataequipo['estado']=$estado= $this->_getParam("estado");
    $dataequipo['fecha_ingreso']=date("Y-m-d");
    $wheres=array('codigo_prop_proy'=>$codigo,'proyectoid'=>$proyectoid,'uid'=>$uid,'dni'=>$dni,'areaid'=>$areaid);
    $equipo= new Admin_Model_DbTable_Equipo();
    $activar= $equipo->_getOne($wheres);
    if($activar)
      {
        echo "existe";
        print_r($wheres);
        $datact['fecha_ingreso']=date("Y-m-d");
        $datact['estado']=$estado;
        $upactiv= $equipo->_update($datact,$wheres);
      }
    else
      {      
        echo "no existe";
        print_r($dataequipo);
        $gactiv= $equipo->_save($dataequipo);
      }
  }

  public function asignaractividadAction(){
    try
    {
      $this->_helper->layout()->disablelayout();
      $proyectoid= $this->_getParam("proyectoid");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      $areaid= $this->_getParam("areaid");
      $this->view->proyectoid = $proyectoid;
      $this->view->codigo_prop_proy = $codigo_prop_proy;
      $this->view->areaid = $areaid;
      $where = array( 'proyectoid' => $proyectoid,'codigo_prop_proy'=>$codigo_prop_proy,'estado' =>'P','isproyecto'=>'S');
      $veract = new Admin_Model_DbTable_Actividad();
      $viewactivity=$veract->_getFilter($where);
      //$this->view->actividades = $viewactivity;

      $actividadpadre= new Admin_Model_DbTable_Actividad();
      $list=$actividadpadre->_getActividadesOrdenadas($proyectoid,$codigo_prop_proy);
      $this->view->actividades = $list;

      $wherekip = array( 'proyectoid' => $proyectoid,'codigo_prop_proy'=>$codigo_prop_proy,'estado' =>'A','areaid'=>$areaid);    
      $verequipo= new Admin_Model_DbTable_Equipo();
      $viewequipo=$verequipo->_getFilter($wherekip);
      $this->view->equipo = $viewequipo;
      $area = new Admin_Model_DbTable_Area();
      $area_view = $area->_getAreaAll();
      $this->view->area = $area_view;
    }
    catch (Exception $e) {
      print "Error: ".$e->getMessage();
    }
  }



  public function agregaactividadAction(){
    try
    {  
      $cargo= $this->_getParam("cargo");
      $areaid= $this->_getParam("areaid");
      $uid= $this->_getParam("uid");
      $dni= $this->_getParam("dni");
      $proyectoid= $this->_getParam("proyectoid");
      $categoriaid= $this->_getParam("categoriaid");  
      $actividadid= $this->_getParam("actividadid");
      $revision= $this->_getParam("revision");
      $codigo_actividad= $this->_getParam("codigo_actividad");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      $estado= $this->_getParam("estado");
      $actividad_padre= $this->_getParam("actividad_padre");
      //"codigo_prop_proy","codigo_actividad", "proyectoid", "actividadid", "uid", "dni","cargo", "areaid", "categoriaid");
      $wheres=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividadid'=>$actividadid
              ,'uid'=>$uid,'dni'=>$dni,'cargo'=>$cargo,'areaid'=>$areaid,'categoriaid'=>$categoriaid);
      $act= new Admin_Model_DbTable_Activaractividad();
      $activar= $act->_getOne($wheres);
        //EXISTE ACTIVIDAD
        if($activar)
        {
          $datact['fecha']=date("Y-m-d");
          $datact['estado']=$estado;
          $upactiv= $act->_updateX($datact,$wheres);

          $wheres_padre=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividadid'=>$actividad_padre
              ,'uid'=>$uid,'dni'=>$dni,'areaid'=>$areaid,'actividad_padre'=>'0');
           $existe_padre= $act->_getExisteActividadPadre($wheres_padre);
           if ($existe_padre)
           {

           }
           else
           {
            $actividadpadre= new Admin_Model_DbTable_Actividad();
            $wheres_actividadpadre=array('codigo_prop_proy'=>$codigo_prop_proy,'proyectoid'=>$proyectoid,'actividadid'=>$actividad_padre);
            $list_actpadre=$actividadpadre->_getExisteActividadPadre($wheres_actividadpadre);
            $data_padre['codigo_prop_proy']=$codigo_prop_proy;
            $data_padre['proyectoid']=$proyectoid;
            $data_padre['codigo_actividad']=$list_actpadre['codigo_actividad'];
            $data_padre['actividadid']=$list_actpadre['actividadid']; 
            $data_padre['revision']=$list_actpadre['revision']; 
            $data_padre['cargo']=$cargo;
            $data_padre['categoriaid']=$categoriaid;
            $data_padre['areaid']=$areaid;
            $data_padre['uid']=$uid;
            $data_padre['dni']=$dni;
            $data_padre['fecha']=date("Y-m-d");
            $data_padre['estado']='A';
            $data_padre['actividad_padre']=$list_actpadre['actividad_padre'];    
            $gactiv= $act->_save($data_padre);
           }
        }
        else
        {      
          $data['codigo_prop_proy']=$codigo_prop_proy;
          $data['proyectoid']=$proyectoid;
          $data['codigo_actividad']=$codigo_actividad;
          $data['actividadid']=$actividadid; 
          $data['revision']=$revision;
          $data['cargo']=$cargo;
          $data['categoriaid']=$categoriaid;
          $data['areaid']=$areaid;
          $data['uid']=$uid;
          $data['dni']=$dni;
          $data['fecha']=date("Y-m-d");
          $data['estado']=$estado;
          $data['actividad_padre']=$actividad_padre;    
          $gactiv= $act->_save($data);
          $wheres_padre=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividadid'=>$actividad_padre
              ,'uid'=>$uid,'dni'=>$dni,'areaid'=>$areaid,'actividad_padre'=>'0');
          $existe_padre= $act->_getExisteActividadPadre($wheres_padre);
          if ($existe_padre)
          {
          }
          else
          {
            $actividadpadre= new Admin_Model_DbTable_Actividad();
            $wheres_actividadpadre=array('codigo_prop_proy'=>$codigo_prop_proy,'proyectoid'=>$proyectoid,'actividadid'=>$actividad_padre);
            $list_actpadre=$actividadpadre->_getExisteActividadPadre($wheres_actividadpadre);
            $data_padre['codigo_prop_proy']=$codigo_prop_proy;
            $data_padre['proyectoid']=$proyectoid;
            $data_padre['codigo_actividad']=$list_actpadre['codigo_actividad'];
            $data_padre['actividadid']=$list_actpadre['actividadid']; 
            $data_padre['revision']=$list_actpadre['revision']; 
            $data_padre['cargo']=$cargo;
            $data_padre['categoriaid']=$categoriaid;
            $data_padre['areaid']=$areaid;
            $data_padre['uid']=$uid;
            $data_padre['dni']=$dni;
            $data_padre['fecha']=date("Y-m-d");
            $data_padre['estado']='A';
            $data_padre['actividad_padre']=$list_actpadre['actividad_padre'];    
            //print_r($data_padre);
            $gactiv= $act->_save($data_padre);
           }
        }

        if ($estado=='I')
        {
          $contar_estado_inactivo= $act->_getConteoActividadesxEstado($codigo_prop_proy,$proyectoid,$uid,$dni,$areaid,'I',$actividad_padre);
          $contar_actividades= $act->_getConteoActividadesxPadre($codigo_prop_proy,$proyectoid,$uid,$dni,$areaid,$actividad_padre);
          /*if ($contar_estado_inactivo[0]['count']=='1')
          {
            $datos_actualizar['estado']='I';
            $str_actualizar="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and actividadid='$actividad_padre' 
            and actividad_padre='0' and uid='$uid' and dni='$dni' and areaid='$areaid' and actividad_padre='0' ";
            $upactiv= $act-> _updateestado($datos_actualizar,$str_actualizar);
          }*/
          if ($contar_estado_inactivo==$contar_actividades)
          {
            $datos_actualizar['estado']='I';
            $wheres_padre=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividadid'=>$actividad_padre
              ,'uid'=>$uid,'dni'=>$dni,'areaid'=>$areaid,'actividad_padre'=>'0');
            $existe_padre= $act->_getExisteActividadPadre($wheres_padre);
            $str_actualizar="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and actividadid='$actividad_padre' 
            and actividad_padre='0' and uid='$uid' and dni='$dni' and areaid='$areaid' and actividad_padre='0'  ";
            $upactiv= $act-> _updateestado($datos_actualizar,$str_actualizar);
            if ($upactiv)
              {
                echo "actualizadooooo ";
              }
          }
        }
        if ($estado=='A')
        {
          $contar_estado_activo= $act->_getConteoActividadesxEstado($codigo_prop_proy,$proyectoid,$uid,$dni,$areaid,'A',$actividad_padre);
          $contar_actividades= $act->_getConteoActividadesxPadre($codigo_prop_proy,$proyectoid,$uid,$dni,$areaid,$actividad_padre);
          if ($contar_estado_activo[0]['count']=='1')
          {
            $datos_actualizar['estado']='A';
            $str_actualizar="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and actividadid='$actividad_padre' 
            and actividad_padre='0' and uid='$uid' and dni='$dni' and areaid='$areaid' and actividad_padre='0' ";
            $upactiv= $act-> _updateestado($datos_actualizar,$str_actualizar);
          }
          if ($contar_estado_activo==$contar_actividades)
          {
            $datos_actualizar['estado']='A';
            $wheres_padre=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividadid'=>$actividad_padre
             ,'uid'=>$uid,'dni'=>$dni,'areaid'=>$areaid,'actividad_padre'=>'0');
            $existe_padre= $act->_getExisteActividadPadre($wheres_padre);
            $str_actualizar="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and actividadid='$actividad_padre' 
            and actividad_padre='0' and uid='$uid' and dni='$dni' and areaid='$areaid' and actividad_padre='0' ";
            $upactiv= $act-> _updateestado($datos_actualizar,$str_actualizar);
            if ($upactiv)
            {
              echo "actualizadooooo";
            }
          }
        }
     } 
      catch (Exception $e) {
      print "Error: ".$e->getMessage();
    }

  }


  public function agregarpersonaequipoAction(){
    try
    {  
      $cargo= $this->_getParam("cargo");
      $areaid= $this->_getParam("areaid");
      $uid= $this->_getParam("uid");
      $dni= $this->_getParam("dni");
      $proyectoid= $this->_getParam("proyectoid");
      $categoriaid= $this->_getParam("categoriaid");  
      $actividadid= $this->_getParam("actividadid");
      $revision= $this->_getParam("revision");
      $codigo_actividad= $this->_getParam("codigo_actividad");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      $estado= $this->_getParam("estado");
      $actividad_padre= $this->_getParam("actividad_padre");
      //"codigo_prop_proy","codigo_actividad", "proyectoid", "actividadid", "uid", "dni","cargo", "areaid", "categoriaid");
      $wheres=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividadid'=>$actividadid
              ,'uid'=>$uid,'dni'=>$dni,'cargo'=>$cargo,'areaid'=>$areaid,'categoriaid'=>$categoriaid);
      $act= new Admin_Model_DbTable_Activaractividad();
      $activar= $act->_getOne($wheres);
      //print_r($wheres);
        if($activar)
        {
          $datact['fecha']=date("Y-m-d");
          $datact['estado']=$estado;
          $upactiv= $act->_updateX($datact,$wheres);
        }
        else
        {      
          $data['codigo_prop_proy']=$codigo_prop_proy;
          $data['proyectoid']=$proyectoid;
          $data['codigo_actividad']=$codigo_actividad;
          $data['actividadid']=$actividadid; 
          $data['revision']=$revision;
          $data['cargo']=$cargo;
          $data['categoriaid']=$categoriaid;
          $data['areaid']=$areaid;
          $data['uid']=$uid;
          $data['dni']=$dni;
          $data['fecha']=date("Y-m-d");
          $data['estado']=$estado;
          $data['actividad_padre']=$actividad_padre;    

          $gactiv= $act->_save($data);

        }
     } 
      catch (Exception $e) {
      print "Error: ".$e->getMessage();
    }

  }

  public function agregartodoactividadAction(){
    try
    {
      echo $cargo= $this->_getParam("cargo");
      $areaid= $this->_getParam("areaid");
      $uid= $this->_getParam("uid");
      $dni= $this->_getParam("dni");
      $proyectoid= $this->_getParam("proyectoid");
      $categoriaid= $this->_getParam("categoriaid");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");   
      $estado= $this->_getParam("estado");   
      $act= new Admin_Model_DbTable_Actividad();
      $activar= $act->_getRepliconActividades($proyectoid,$codigo_prop_proy);
      //print_r($activar);

      //exit();

      for($i=0;$i<count($activar);$i++)
      {

        if(($activar[$i]['hijo'])=='N')
        {
          //print_r(strlen($activar[$i]['actividadid']));echo "</br>";
          //print_r($activar[$i]['actividadid']);echo "</br>";
          //echo "ggg";
          //exit();
          $codigo_actividad = $activar[$i]['codigo_actividad'];
          $actividadid = $activar[$i]['actividadid'];
          $revision = $activar[$i]['revision'];
          $actividad_padre = $activar[$i]['actividad_padre'];
          $wheres=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$activar[$i]['codigo_actividad'],
                 'proyectoid'=>$proyectoid,'actividadid'=>$activar[$i]['actividadid'],'uid'=>$uid,'dni'=>$dni,'cargo'=>$cargo,
                 'areaid'=>$areaid,'categoriaid'=>$categoriaid);
          $acti= new Admin_Model_DbTable_Activaractividad();
          $veract= $acti->_getOne($wheres);
          if($veract)
          {
            $datact['fecha']=date("Y-m-d");
            $datact['estado']=$estado;
            $upactiv= $acti->_updateX($datact,$wheres);
          }
          else
          {
          $data['codigo_prop_proy']=$codigo_prop_proy;
          $data['proyectoid']=$proyectoid;
          $data['codigo_actividad']=$activar[$i]['codigo_actividad'];
          $data['actividadid']=$actividadid;
          $data['revision']=$revision;
          $data['cargo']=$cargo;
          $data['categoriaid']=$categoriaid;
          $data['areaid']=$areaid;
          $data['uid']=$uid;
          $data['dni']=$dni;
          $data['fecha']=date("Y-m-d");
          $data['estado']=$estado;
          $data['actividad_padre']=$actividad_padre;
          $gactiv= $acti->_save($data);
          
          }
      
        }
      } 
    }
      catch (Exception $e) {
      print "Error: ".$e->getMessage();
    }
  }



  public function guardarareaequipoAction(){
    try
    {
      $this->_helper->layout()->disablelayout();      
      $proyectoid= $this->_getParam("proyectoid");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      //$categoriaid= $this->_getParam("cat");
      $areaid= $this->_getParam("area");
      $funcion= $this->_getParam("funcion");

      $data['codigo_prop_proy']=$codigo_prop_proy;
      $data['proyectoid']=$proyectoid;      
      $data['categoriaid']='';
      $data['areaid']=$areaid;   
      $data['funcion']=$funcion;   
      $data['fecha_creacion']=date("Y-m-d");
      $data['estado']='A';
   
      //print_r($data);

      $where = array('codigo_prop_proy' =>$codigo_prop_proy, 'proyectoid' =>$proyectoid , 'areaid' =>$areaid );


      $equiparea= new Admin_Model_DbTable_Equipoarea();

      $eki=$equiparea->_getOne($where);

      if($eki)
      { ?>

        <script type="text/javascript">
          alert("ya esta contenido ese area");
          window.location.reload();


        </script>
      <?php
      }
      else
      {

        $gequiparea= $equiparea->_save($data);
        if($gequiparea)
        { ?>
          <script>
            //alert('ha sido creada la nueva area');
          </script>
        <?php }

      }  
      

    }
    catch (Exception $e) {
      print "Error: ".$e->getMessage();
    }
  }

  public function hojaresumenAction() 
  {

    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $propuestaid= $this->_getParam("propuestaid");
    $revision= $this->_getParam("revision");

    $this->view->proyectoid=$proyectoid;
    $this->view->codigo_prop_proy=$codigo_prop_proy;
    $this->view->propuestaid=$propuestaid;
    $this->view->revision=$revision;

    $proyect = new Admin_Model_DbTable_Proyecto();
    $verproyect=$proyect->_buscarProyectodetalles($proyectoid,$codigo_prop_proy);
    //print_r($verproyect);


    $this->view->proyectdetail=$verproyect;

    if($verproyect)
    {

      $cliente=$verproyect[0]['clienteid'];

      $where = array('clienteid' =>$cliente);

      if($cliente!='')
      {
      //print_r($where);
      $contact = new Admin_Model_DbTable_Contacto();
      $cc=$contact->_getFilter($where);
      $this->view->contact=$cc;

      }

      /* para visualizar el comntacto por defecto*/
      $contactunico=$verproyect[0]['contactoid'];
      $where = array('contactoid' =>$contactunico);
  
      if($contactunico!='')
      {
      //print_r($where);      
      $contunic = new Admin_Model_DbTable_Contacto();
      $ccunico=$contunic->_getFilter($where);
      $this->view->contactunico=$ccunico[0];

      }

      $usercat= new Admin_Model_DbTable_Usuariocategoria();    
   
      $ucat=$usercat->_getUsuariocategoriaAllxUid();
      $this->view->ucat=$ucat;

      //exit();

    }

    $clientes = new Admin_Model_DbTable_Cliente();
    $nombreliente=$clientes->_getClienteAll();
    $this->view->cliente=$nombreliente;

    $unidadm=new Admin_Model_DbTable_Unidadminera();
    $uminera=$unidadm->_getUnidadmineraAll();
    $this->view->unidad=$uminera;

    $hojaselect = new Admin_Model_DbTable_Hojaresumen();
    $where = array('proyectoid' => $proyectoid, 'codigo_prop_proy' => $codigo_prop_proy,
                   'propuestaid' => $propuestaid,'revision_propuesta' => $revision);

    $hselect=$hojaselect->_getFilter($where);
    //print_r($hselect);
    $this->view->select_revision=$hselect;

    //exit();


  }

  public function imphojaresumenAction()
  {
    //this->_helper->layout()->disablelayout();    
    
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $revision_hojaresumen= $this->_getParam("revision_hojaresumen");
    $revision= $this->_getParam("revision_propuesta");
    $propuestaid= $this->_getParam("propuestaid");

    $this->view->proyectoid=$proyectoid;
    $this->view->codigo_prop_proy=$codigo_prop_proy;
    //$this->view->imp=$imp;

 

    $hoja= new Admin_Model_DbTable_Hojaresumen();  
 
    $verproyect=$hoja->_buscarProyectodetallesxhojaresumen($proyectoid,$codigo_prop_proy,$propuestaid,$revision,$revision_hojaresumen);
  
    //print_r($verproyect);


    $this->view->proyectdetail=$verproyect;

    if($verproyect)
    {


      $contact=$verproyect[0]['contactoid'];
      $where = array('contactoid' =>$contact);
      // //print_r($where);

      $contact = new Admin_Model_DbTable_Contacto();
      $cc=$contact->_getFilter($where);

      //print_r($cc);
      $this->view->contact=$cc[0];
      //exit();
     // print_r($verproyect);
    }

    $clientes = new Admin_Model_DbTable_Cliente();
    $nombreliente=$clientes->_getClienteAll();
    $this->view->cliente=$nombreliente;

    $unidadm=new Admin_Model_DbTable_Unidadminera();
    $uminera=$unidadm->_getUnidadmineraAll();
    $this->view->unidad=$uminera;
  }

  public function procesarcontactoAction()
  {
      
      echo $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      echo $proyectoid= $this->_getParam("proyectoid");
      echo $cliente_contacto= $this->_getParam("cliente_contacto");
      echo $tipo_cliente= $this->_getParam("tipo_cliente");
      echo $email= $this->_getParam("email");
      echo $puesto_trabajo= $this->_getParam("puesto_trabajo");
      echo $dni= $this->_getParam("dni");
      echo $estado_contacto= $this->_getParam("estado_contacto");
      echo $unidad_contacto= $this->_getParam("unidad_contacto");
      echo $nomunidad= $this->_getParam("nomunidad");
      echo $direccion= $this->_getParam("direccion");
      echo $anexo= $this->_getParam("anexo");
      echo $telefono= $this->_getParam("telefono");
      echo $paterno= $this->_getParam("paterno");
      echo $materno= $this->_getParam("materno");
      echo $nombre= $this->_getParam("nombre");
      echo $celular1= $this->_getParam("celular1");
      echo $celular2= $this->_getParam("celular2");
      echo $isunidad= $this->_getParam("isunidad");



      $where = array('clienteid' =>$cliente_contacto , 'tipo_cliente' => $tipo_cliente,'correo' => $email, 'puesto_trabajo' => $puesto_trabajo, 'dni' => $dni, 
      'isunidad' => $isunidad , 'estado' =>$estado_contacto , 'unidad_mineraid' => $unidad_contacto , 'nombre_unidad' => $nomunidad , 
      'direccion' => $direccion, 'anexo' => $anexo, 'telefono' =>$telefono, 'ape_paterno' => $paterno, 'ape_materno' =>$materno , 'nombre1' =>$nombre , 'numero1' =>$celular1 ,'numero2' => $celular2,  );

      //print_r($where);break;

      $newcontact=new Admin_Model_DbTable_Contacto();
      $ncontact=$newcontact->_save($where);

      if($ncontact)
      {
        //echo "ffffffff"; break;
        $pk = array('codigo_prop_proy' =>$codigo_prop_proy ,'proyectoid' =>$proyectoid  );
        $data = array('clienteid' =>$cliente_contacto ,'unidad_mineraid' => $unidad_contacto,  );
        $actproyecto= new Admin_Model_DbTable_Proyecto();
        $updcontactproyect=$actproyecto->_update($data,$pk);
      }
      
      exit();
  }

  public function updatecontactoproyectAction()
  {
      echo $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      echo $proyectoid= $this->_getParam("proyectoid");
      echo $valor= $this->_getParam("valor");
      echo $contactoid=str_replace("cont_","",$valor);
      //exit();
      
      $pk = array('codigo_prop_proy' =>$codigo_prop_proy ,'proyectoid' =>$proyectoid  );
      $data = array('contactoid' =>$contactoid);
      $actproyecto= new Admin_Model_DbTable_Proyecto();
      $updcontactproyect=$actproyecto->_update($data,$pk);
      if($updcontactproyect)
        {echo "gg";}
  }

  public function generarresumenAction()
  {
    echo $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    echo "&";    
    echo $proyectoid= $this->_getParam("proyectoid");
    echo "&";
    echo $revision_propuesta= $this->_getParam("revision");
    echo "&";
    echo $revision_hojaresumen= $this->_getParam("revision_hojaresumen");
    echo $propuestaid= $this->_getParam("propuestaid");
    echo "&";
    echo $contact= $this->_getParam("contact");


    echo $gerente_proyecto= $this->_getParam("gerente_proyecto");
    echo $jefe_proyecto1= $this->_getParam("jefe_proyecto1");
    echo $jefe_proyecto2= $this->_getParam("jefe_proyecto2");
    echo $control_documentario= $this->_getParam("control_documentario");
    echo $fecha_inicio_planificado= $this->_getParam("fecha_inicio_planificado");
    echo $fecha_fin_planificado= $this->_getParam("fecha_fin_planificado");
    echo $fecha_inicio_real= $this->_getParam("fecha_inicio_real");
    echo $fecha_fin_real= $this->_getParam("fecha_fin_real");

    echo $adelanto= $this->_getParam("adelanto");
    echo $comentario= $this->_getParam("comentario");
    echo $tipo_contrato= $this->_getParam("tipo_contrato");
    echo $observacion= $this->_getParam("observacion");
    // $contacto= new Admin_Model_DbTable_Contacto();
    // $wherecont = array('contactoid' =>$contact , );
    // $cc=$contacto->_getOne($wherecont);
    // print_r($cc);

    $where = array('codigo_prop_proy' =>$codigo_prop_proy ,'proyectoid' =>$proyectoid , 'revision_propuesta' =>$revision_propuesta,
                   'revision_hojaresumen' =>$revision_hojaresumen, 'propuestaid' =>$propuestaid  );

    $hoja= new Admin_Model_DbTable_Hojaresumen();
    $verhoja=$hoja->_getOne($where);

    
    if($verhoja)
    {
      //echo "no llego";
    }
    else
    {
    
      //echo $cc['clienteid'];
      $data = array('codigo_prop_proy' =>$codigo_prop_proy ,'proyectoid' =>$proyectoid , 'revision_propuesta' =>$revision_propuesta,
                   'revision_hojaresumen' =>$revision_hojaresumen, 'propuestaid' =>$propuestaid ,'contactoid' =>$contact , 
                   'gerente_proyecto' =>$gerente_proyecto, 'jefe_proyecto1' =>$jefe_proyecto1 ,'jefe_proyecto2' =>$jefe_proyecto2 , 
                   'control_documentario' =>$control_documentario, 'fecha_inicio_planificado' =>$fecha_inicio_planificado ,'fecha_fin_planificado' =>$fecha_fin_planificado , 
                   'fecha_inicio_real' =>$fecha_inicio_real, 'fecha_fin_real' =>$fecha_fin_real ,
                   'adelanto' =>$adelanto, 'comentarios' =>$comentario ,
                   'tipo_contrato' =>$tipo_contrato, 'observacion' =>$observacion , );

      //print_r($data);
      ///echo "llego";
      $guardarhoja=$hoja->_save($data);
     //exit();

    }


    exit();
  }
    
  public function historialresumenAction()
  {
    $this->_helper->layout()->disablelayout();    

    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $propuestaid= $this->_getParam("propuestaid");
    $revision= $this->_getParam("revision");

    $hoja= new Admin_Model_DbTable_Hojaresumen();  
    //$wherehistorial = array('codigo_prop_proy' =>$codigo_prop_proy,'proyectoid' =>$proyectoid,'propuestaid' =>$propuestaid, 'revision_propuesta' =>$revision);
    $traerhistorial=$hoja->_buscarProyectodetalles($proyectoid,$codigo_prop_proy,$propuestaid,$revision);

    //print_r($traerhistorial);

    $this->view->historialresumen=$traerhistorial;

    $usercat= new Admin_Model_DbTable_Usuariocategoria();

    $ucat=$usercat->_getUsuariocategoriaAllxUid();
    $this->view->ucat=$ucat;
 
    //print_r($ucat);

  }

 public function updatehojaresumenAction()
 {
     $codigo_prop_proy= $this->_getParam("codigo_prop_proy");    
     $proyectoid= $this->_getParam("proyectoid");
     $revision_propuesta= $this->_getParam("revision_propuesta");
     $revision_hojaresumen= $this->_getParam("revision_hojaresumen");
     $propuestaid= $this->_getParam("propuestaid");
   // echo $contact= $this->_getParam("contact");

     $gerente_proyecto= $this->_getParam("gerente_proyecto");
     $jefe_proyecto1= $this->_getParam("jefe_proyecto1");
     $jefe_proyecto2= $this->_getParam("jefe_proyecto2");
     $control_documentario= $this->_getParam("control_documentario");

     $fecha_inicio_planificado= $this->_getParam("fecha_inicio_planificado");
     $fecha_fin_planificado= $this->_getParam("fecha_fin_planificado");
     $fecha_inicio_real= $this->_getParam("fecha_inicio_real");
     $fecha_fin_real= $this->_getParam("fecha_fin_real");

     $adelanto= $this->_getParam("adelanto");
     $comentario= $this->_getParam("comentario");
     $tipo_contrato= $this->_getParam("tipo_contrato");
     $observacion= $this->_getParam("observacion");
     $direccion= $this->_getParam("direccion");

     $nombre_comercial= $this->_getParam("nombre_comercial");
     $ruc= $this->_getParam("ruc");
     $correo= $this->_getParam("correo");
     $numero1= $this->_getParam("numero1");
     $numero2= $this->_getParam("numero2");
     $anexo= $this->_getParam("anexo");
     $telefono= $this->_getParam("telefono");
     $puesto_trabajo= $this->_getParam("puesto_trabajo");
     $contactoid= $this->_getParam("contactoid");    
    $pk = array('codigo_prop_proy' => $codigo_prop_proy,'proyectoid' => $proyectoid,'revision_hojaresumen' => $revision_hojaresumen,'propuestaid' => $propuestaid,
          'revision_propuesta' => $revision_propuesta, );
    $wherehoja = array(
     'gerente_proyecto' => $gerente_proyecto,'jefe_proyecto1' => $jefe_proyecto1,'jefe_proyecto2' => $jefe_proyecto2,'control_documentario' => $control_documentario,
     'fecha_inicio_planificado' => $fecha_inicio_planificado,'fecha_fin_planificado' => $fecha_fin_planificado,'fecha_inicio_real' => $fecha_inicio_real,'fecha_fin_real' => $fecha_fin_real,
     'adelanto' => $adelanto, 'comentarios' => $comentario, 'tipo_contrato' => $tipo_contrato, 
     'observacion' => $observacion,
    );
    // 'ruc' => $ruc,
    $str = array('contactoid' => $contactoid,);
    $wherecontacto = array('direccion' => $direccion,'correo' => $correo,
     'numero1' => $numero1,'numero2' => $numero2,'anexo' => $anexo,'telefono' => $telefono,'puesto_trabajo' => $puesto_trabajo, );
 // 'nombre_comercial' => $nombre_comercial,    
    $uphoja= new Admin_Model_DbTable_Hojaresumen(); 
    $uhoja=$uphoja->_update($wherehoja,$pk);
                    //_update($data,$pk)
    //print_r($uhoja);
    if($uhoja)
    { 
      $updcontac=new Admin_Model_DbTable_Contacto();
      $ucontact=$updcontac->_update($wherecontacto,$str);
    ?>
      <script>
        alert("aaaa--aaa");
      </script>
    <?php
    }
    else
    {
    ?>
      <script>
        alert("bbbbb--bbbb");
      </script>
    <?php
    }
    exit();
 }

public function verproyectoAction() {
    $proyectoid= $this->_getParam("proyectoid");
    $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
    $propuestaid= $this->_getParam("propuesta");
    $revision= $this->_getParam("revision");
    $this->view->codigoproyecto=$codigo_prop_proy;
    $this->view->proyectoid=$proyectoid;
    $this->view->propuestaid=$propuestaid;
    $this->view->revision=$revision;

    $bandera= $this->_getParam("bandera");
    if ($bandera=='S')
    {
    $this->view->bandera='S';}
    $editproyect= new Admin_Model_DbTable_Proyecto();
    $where = array(
      'codigo_prop_proy'    => $codigo_prop_proy,
      'proyectoid'    => $proyectoid,
    );
    $edit = $editproyect->_getOne($where);
    $this->view->proyecto = $edit;

    // $codigo=$this->_getParam('codigo_prop_proy');
    // $propuestaid=$edit['propuestaid'];
    // $revision=$edit['revision'];
    // $buscapropuesta = new Admin_Model_DbTable_Propuesta();
    // $busca=$buscapropuesta->_getPropuestaxIndices($codigo,$propuestaid,$revision);
    // $this->view->buscapropuesta = $busca; 

    $areacat=new Admin_Model_DbTable_Area();
    $arcat=$areacat->_getAreaxProyecto();
    $this->view->area = $arcat; 
}    

public function estadomostraractividadAction(){
    try
    {        
      $proyectoid= $this->_getParam("proyectoid");
      $codigo_prop_proy= $this->_getParam("codigo_prop_proy");
      $revision= $this->_getParam("revision");
      $actividad_padre= $this->_getParam("actividad_padre");
      $codigo_actividad= $this->_getParam("codigo_actividad");
      $estado= $this->_getParam("estadomostrar");

      $pk=array('revision'=>$revision,'codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,'proyectoid'=>$proyectoid,'actividad_padre'=>$actividad_padre);
      $act= new Admin_Model_DbTable_Actividad();     

              $str_padre="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and revision='$revision' and codigo_actividad='$codigo_actividad' and actividadid='$actividad_padre'";      
              $str_hijas="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and revision='$revision' and  actividad_padre='$actividad_padre'";    

      //$data = array('estado_mostrar' =>$estado);
       $data["estado_mostrar"]=$estado;
       echo $str;

       print_r($data);
      $updateactpadre=$act->_update($data,$str_padre);
      $updateacthijas=$act->_update($data,$str_hijas);

          if($updateactpadre)
          {
            echo "existe";
          }
          else{
            echo "no existe";
          }

             if($updateacthijas)
          {
            echo "existe";
          }
          else{
            echo "no existe";
          }              
          //$upactiv= $act->_update($datact,$wheres);    
     } 
      catch (Exception $e) {
      print "Error: ".$e->getMessage();
    }
  } 

  public function equipoxproyectoAction() {
      $data['proyectoid'] = '1101.10.10';   
      $orders=array('uid asc');
      $bdequipo = new Admin_Model_DbTable_Equipo();
      $equipo=$bdequipo->_getFilter($data,$attrib=null,$orders);

      // $bdequipoarea = new Admin_Model_DbTable_Equipoarea();
      // $area=$bdequipoarea->_buscarAreasxProyecto('14.10.134-1101.10.09-D','1101.10.09');
      // $i=0;
      // foreach ($area as $verarea) {
      //   $bdequipo = new Admin_Model_DbTable_Equipo();
      //   $equipo=$bdequipo->_buscarEquipoxProyectoxArea('14.10.134-1101.10.09-D','1101.10.09',$verarea['areaid']);

      //   $ek[] = array('name' =>$verarea['nombre'],'area'=>$verarea['areaid'] ,'items'=> $equipo);
      //   $i++;
      // }    

      //es el formato de renderiar :  [{"bbb":"1","name":"2","items":[{"nombre":"books"}]}]
      //$arr = array(['bbb' =>'1', 'name' => '2','items'=>  array(['nombre' =>'books'])]);
      $this->_helper->json->sendJson($equipo);  

  }

}
