<?php

class Timesheet_IndexController extends Zend_Controller_Action {

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
        print_r($this->sesion);
    }

    public function calendarAction(){
        try {
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $equipo = new Admin_Model_DbTable_Equipo();
        //$data_equipo = $equipo->_getProyectosXuidXEstado($uid,'A');
        //$data_clientes = $equipo ->_getClienteXuidXEstado($uid,'A');
        /*clientes de la tabla equipo*/
        //$data_clientes = $equipo ->_getClienteXEquipoXUsuario($uid, $dni ,'A');
        $act= new Admin_Model_DbTable_Activaractividad();
        $data_clientes = $act ->_getClientesXEmpleadoXEstadoActivo($uid, $dni ,'A');
        $this->view->datoscliente = $data_clientes;
        //print_r($data_clientes);
        //$this->view->equipo = $data_equipo;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        } 
    }

    public function proyectosAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $clienteid = $this->_getParam('clienteid');
        $unidadid = $this->_getParam('unidadid');
        $fecha_consulta = $this->_getParam('fecha');
        if ($clienteid=='20451530535' && $unidadid=='10')
        {
            $equipo = new Admin_Model_DbTable_Equipo();
            $data_equipo = $equipo->_getProyectosAnddes();    
            $this->view->equipo = $data_equipo;
            $this->view->fecha_consulta = $fecha_consulta;   
            $this->view->unidadid=$unidadid;
        }
        else
        {
            $equipo = new Admin_Model_DbTable_Equipo();
            $data_equipo = $equipo->_getProyectosxEquipoxUsuarioXEstadoxCliente($uid,$dni,'A',$clienteid);  
            //print_r($data_equipo);
            //$data_equipo = $equipo->_getProyectosxUidXEstadoxCliente($uid,'A',$clienteid,$unidadid);    
            $this->view->equipo = $data_equipo;
            $this->view->fecha_consulta = $fecha_consulta;
        }
        

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        } 
    }    

    public function actividadesAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $areaid=$this->sesion->personal->ucatareaid;
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        
        $this->view->uid = $this->sesion->uid;
        $this->view->dni = $this->sesion->dni;
        $this->view->areaid=$areaid;
        $proyectoid = $this->_getParam('proyectoid');
        $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $categoriaid = $this->_getParam('categoriaid');
        $fecha_consulta = $this->_getParam('fecha');
        $semana=date('W', strtotime($fecha_consulta)); 
        $this->view->semanaid = $semana;
        
        $actividad = new Admin_Model_DbTable_Activaractividad();
        $dato_padre=$actividad->_getActividadesxCppxPixUid($codigo_prop_proy, $proyectoid, $uid);

        //$actividad = new Admin_Model_DbTable_Actividad();
        //$dato_padre=$actividad->_getRepliconActividades($proyectoid,$codigo_prop_proy);
        //print_r($dato_padre);
        $this->view->actividades = $dato_padre;    
        $this->view->proyectoid = $proyectoid;
        $this->view->codigo_prop_proy = $codigo_prop_proy;
        $this->view->categoriaid = $categoriaid;
        $this->view->fecha_consulta = $fecha_consulta;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        } 
    }    


    public function actividadeshijosAction(){
        try {
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $this->view->uid = $uid;
        $this->view->dni = $dni;
        $fecha_consulta = $this->_getParam('fecha');
        $fecha_inicio_semana = date("Y-m-d", strtotime($fecha_consulta));
           
        $semana=date('W', strtotime($fecha_inicio_semana)); 
        $this->view->semanaid = $semana;

        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $proyectoid = $this->_getParam('proyectoid');
        $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $categoriaid = $this->_getParam('categoriaid');
        $actividadid = $this->_getParam('actividadid');
        $revision = $this->_getParam('revision');
        $propuestaid = $this->_getParam('propuestaid');
        $tareo = new Admin_Model_DbTable_Actividad();
        $data_tareo_hijos = $tareo->_getActividadesHijas($proyectoid,$codigo_prop_proy,$propuestaid,$revision,$actividadid);
        $codigo_act_padres_hijas = $tareo->_getActividadesHijasxActividadesPadresXCategoria($proyectoid,$codigo_prop_proy,$propuestaid,$revision,$actividadid,$categoriaid);
        // tareo con actividades para listar actividades por categoria
        $j=0;    
        foreach ($codigo_act_padres_hijas as $act_padre) {
        $actividadespadres = explode(".",$act_padre['actividad_padre']);
            if (count($actividadespadres)=='1')
            {
                $dato_tarea=$tareo->_getTareasxActividadPadrexCategoria($proyectoid,$codigo_prop_proy,$propuestaid,$revision,$act_padre['actividad_padre'],$categoriaid);
            }
            if (count($actividadespadres)=='2')
            {
                $dato_padre=$tareo->_getActividadesxActividadid($proyectoid,$codigo_prop_proy,$act_padre['actividad_padre']);
                $array[$j]=$dato_padre[0];
                $j++;
            }
        }
        $this->view->datos_tarea = $dato_tarea;
        $this->view->datos_disciplina = $array;
        $this->view->actividades_hijos = $data_tareo_hijos;
        $this->view->proyectoid = $proyectoid;
        $this->view->codigo_prop_proy = $codigo_prop_proy;
        $this->view->categoriaid = $categoriaid;
        $this->view->actividadid = $actividadid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }  

    public function actividadestareaAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $fecha_consulta = $this->_getParam('fecha');
        $fecha_inicio_semana = date("Y-m-d", strtotime($fecha_consulta));
        $semana=date('W', strtotime($fecha_inicio_semana)); 
        $this->view->semanaid = $semana;
        $this->view->uid = $uid;
        $this->view->dni = $dni;
 

        $proyectoid = $this->_getParam('proyectoid');
        $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $categoriaid = $this->_getParam('categoriaid');
        $actividadid = $this->_getParam('actividadid');
        $revision = $this->_getParam('revision');
        $propuestaid = $this->_getParam('propuestaid');
        $tareo = new Admin_Model_DbTable_Actividad();
        $data_tareas = $tareo->_getActividadesHijas($proyectoid,$codigo_prop_proy,$propuestaid,$revision,$actividadid);
        $dato_tarea=$tareo->_getTareasxActividadPadrexCategoria($proyectoid,$codigo_prop_proy,$propuestaid,$revision,$actividadid,$categoriaid);
        //$this->view->tareas = $data_tareas;
        $this->view->tareas = $dato_tarea;
        $this->view->proyectoid = $proyectoid;
        $this->view->codigo_prop_proy = $codigo_prop_proy;
        $this->view->categoriaid = $categoriaid;
        $this->view->actividadid = $actividadid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }  


    public function registroAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $fecha_inicio = $this->_getParam('fecha');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        $fecha_inicio = date("d-m-Y", strtotime($fecha_inicio));
        $fecha_mostrar = date("d", strtotime($fecha_inicio));

        $this->view->fecha = $fecha_inicio;
        $this->view->fecha_mostrar = $fecha_mostrar;
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $this->view->fecha_inicio_mod = $fecha_inicio_mod;
        $this->view->uid = $uid;
        $this->view->dni = $dni;
        $categoriaid=$this->sesion->personal->ucatid;
        $areaid=$this->sesion->personal->ucatareaid;
        $cargo=$this->sesion->personal->ucatcargo;
        $this->view->aprobacion = $this->sesion->personal->ucataprobacion;
        $this->view->cargo=$cargo;
        $this->view->areaid=$areaid;
        $tareo_persona = new Admin_Model_DbTable_Tareopersona();
        $semana=date('W', strtotime($fecha_inicio_mod)); 
        $this->view->semana = $semana;
        $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
        $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
        $wheres_hojatiempo_empleado=array('uid'=>$uid,'dni'=>$dni,'semanaid'=>$semana);
        $estado_hojatiempo=$tareo_persona->_getEstado_HojaTiempo($semana,$uid,$dni);
        //print_r($wheres_hojatiempo_empleado);
        if ($estado_hojatiempo)
        {
            $this->view->estado_hoja_tiempo= $estado_hojatiempo;    
           
        }
        //print_r($estado_hojatiempo);

        //$data_tareo = $tareo->_getTareoXUid($where);
        $this->view->actividades= $datos_tareopersona;
        // print_r($this->sesion->is_gerente);
        $this->view->is_gerente=$this->sesion->is_gerente;
        
        $this->view->actividades_NB = $datos_tareopersona_NB;

        $buscar_aprobador=$this->sesion->personal->ucataprobacion;
        $aprobacion = new Admin_Model_DbTable_Aprobacion();
        
        $wheres_filtro1=array('idaprobacion'=>$buscar_aprobador,'estado_filtro1'=>'A');
        $list_aprobacion_filtro1=$aprobacion->_getOnefiltro1($wheres_filtro1); 
        if ($list_aprobacion_filtro1)
        {
            $aprobador_filtro1= $list_aprobacion_filtro1['idaprobador_filtro1'];  
        }

        $wheres_filtro2=array('idaprobacion'=>$buscar_aprobador,'estado_filtro2'=>'A');
        $list_aprobacion_filtro2=$aprobacion->_getOnefiltro2($wheres_filtro2); 

        if ($list_aprobacion_filtro2)
        {
            $idaprobador_filtro2= $list_aprobacion_filtro2['idaprobador_filtro2'];  
            $usuario_cat = new Admin_Model_DbTable_Usuariocategoria();
            
            $wheres_ucat=array('aprobacion'=>$idaprobador_filtro2,'estado'=>'A');
            $list_aprobador=$usuario_cat->_getAprobadorxEmpleado($wheres_ucat);  


            
            if ($list_aprobador)
            {
                $aprobador_usuario = explode(".", $list_aprobador['uid']);
                $this->view->aprobador_filtro2=ucwords($aprobador_usuario[0])." ".ucwords($aprobador_usuario[1]);
            }
        }

        $planificacion = new Admin_Model_DbTable_Planificacion();
        $proyectos=$planificacion->_getOnexSemanaxGerenteProyecto($semana,$uid,$dni,$areaid);
        $k=0;
        $gerentes_proyectos=array();
        
        foreach ($proyectos as $datos) {
          $porciones = explode(".", $datos['uid']);
          $gerentes_proyectos[$k]=ucwords($porciones[0])." ".ucwords($porciones[1]);
          $k++;
        }
        $this->view->gerentes_proyectos=$gerentes_proyectos;




        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        } 
    }


    public function guardartareopersonaAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        //$categoriaid=$this->sesion->personal->ucatid;
        //$areaid=$this->sesion->personal->ucatareaid;
        //$cargo=$this->sesion->personal->ucatcargo;
        
             
        $data['proyectoid']=$proyectoid = $this->_getParam('proyectoid');
        $data['codigo_prop_proy']=$codigo_prop_proy = $this->_getParam('codigo');
        $data['actividadid']=$actividadid = $this->_getParam('actividadid');
        $data['revision']=$revision = $this->_getParam('revision');
        $data['codigo_actividad']=$codigo_actividad = $this->_getParam('codigo_actividad');
        $data['actividad_padre']=$actividad_padre = $this->_getParam('actividad_padre');
        $data['h_propuesta']=$h_propuesta = $this->_getParam('h_propuesta');
        $data['uid']=$uid;
        $data['asignado']= $dni;
        $data['dni']=$dni;
        $data['etapa']='INICIO';
        

        $fecha_inicio = $this->_getParam('fecha');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        $semana=date('W', strtotime($fecha_inicio_mod)); 

        $data['semanaid']=$semana;
        $data['fecha_tarea']=$fecha_inicio_mod;
        $data['fecha_creacion']=$fecha_inicio_mod;
        $data['fecha_planificacion']=$fecha_inicio_mod;
        if ($codigo_prop_proy=='2015' || $codigo_prop_proy=='1590.10.01' || $codigo_prop_proy=='1590.10.03' || $codigo_prop_proy=='1590.10.02')
        {
         $data['tipo_actividad']='A';   
        }
        else
        {
        $data['tipo_actividad']='P';    
        }
        
        $data['estado']='A';
        
        $equipo = new Admin_Model_DbTable_Equipo();
        $estado_usuario='A';
        $data_equipo = $equipo->_getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado_usuario,$codigo_prop_proy,$proyectoid);
        $categoriaid=$data_equipo[0]['categoriaid'];
        $areaid=$data_equipo[0]['areaid'];
        $cargo=$data_equipo[0]['cargo'];
        $data['cargo']=$cargo;
        $data['areaid']=$areaid;
        $data['categoriaid']=$categoriaid;

        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $data_tareopersona = $tareopersona->_save($data);

        $this->view->proyectoid = $proyectoid;
        $this->view->codigo_prop_proy = $codigo_prop_proy;
        $this->view->categoriaid = $categoriaid;
        $this->view->actividadid = $actividadid;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function updatetareorealAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;

        // $categoriaid=$this->sesion->personal->ucatid;
        //$areaid=$this->sesion->personal->ucatareaid;
        //$cargo=$this->sesion->personal->ucatcargo;

        //$cargoreal= $this->_getParam('tipo_actividad');      
        $fecha_inicio = $this->_getParam('fecha_tarea');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        $semanaid=date('W', strtotime($fecha_inicio_mod)); 
        $tipo_actividad_actualizar= $this->_getParam('tipo_actividad');
        $etapa_origen= $this->_getParam('etapa');
        $etapa_actualizar = str_replace("INICIO", "EJECUCION", $etapa_origen);
        

        $datos_actualizar['fecha_modificacion']=$fecha_inicio_mod;
        $datos_actualizar['h_real']=$h_real= $this->_getParam('horareal');

        //$semanaid=$this->_getParam('semanaid');
        $data['proyectoid']=$proyectoid = $this->_getParam('proyectoid');
        $data['codigo_prop_proy']=$codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $data['actividadid']=$actividadid = $this->_getParam('actividadid');
        $data['revision']=$revision = $this->_getParam('revision');
        $data['codigo_actividad']=$codigo_actividad = $this->_getParam('codigo_actividad');
        $data['actividad_padre']=$actividad_padre = $this->_getParam('actividad_padre');
        $data['h_propuesta']=$h_propuesta = $this->_getParam('h_propuesta');
        $data['fecha_tarea']=$fecha_tarea= $this->_getParam('fecha_tarea');
        $data['fecha_planificacion']=$fecha_tarea;
        $data['fecha_modificacion']=$fecha_inicio_mod;
        $data['fecha_creacion']=$fecha_inicio_mod;
        $data['h_real']=$h_real= $this->_getParam('horareal');
        $data['semanaid']=$semanaid;


        print_r($data);



         $actividad_generalid = $this->_getParam('actividad_generalid');
        // if($actividad_generalid=='')
        // {
        //     if ($tipo_actividad_actualizar=='A')
        //     {
        //     $data['actividad_generalid']=$actividad_generalid;
        //     $data['tipo_actividad']='A';
        //     $etapa= $this->_getParam('etapa');
        //     $resultado = str_replace("INICIO", "EJECUCION", $etapa);
        //     $data['etapa']=$resultado;
        //     }
        //     else
        //     {

        //     $data['actividad_generalid']=null;
        //     $data['tipo_actividad']='P';
        //     $etapa= $this->_getParam('etapa');
        //     $resultado = str_replace("INICIO", "EJECUCION", $etapa);
        //     $data['etapa']=$resultado;    
        //     }
        // }
        // else
        // {

            
        //     $data['actividad_generalid']=$actividad_generalid;
        //     $data['tipo_actividad']='G';
        //     $etapa= $this->_getParam('etapa');
        //     $resultado = str_replace("INICIO", "EJECUCION", $etapa);
        //     $data['etapa']=$resultado;
            
        // }

        if($actividad_generalid=='')
        {
            $data['actividad_generalid']=null;
            $data['tipo_actividad']=$tipo_actividad_actualizar;
            $etapa= $this->_getParam('etapa');
            $resultado = str_replace("INICIO", "EJECUCION", $etapa);
            $data['etapa']=$resultado;    
        }
        else
        {
            $data['actividad_generalid']=$actividad_generalid;
            $data['tipo_actividad']='G';
            $etapa= $this->_getParam('etapa');
            $resultado = str_replace("INICIO", "EJECUCION", $etapa);
            $data['etapa']=$resultado;
        }

        $equipo = new Admin_Model_DbTable_Equipo();
        $estado_usuario='A';
        $data_equipo = $equipo->_getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado_usuario,$codigo_prop_proy,$proyectoid);
        $categoriaid=$data_equipo[0]['categoriaid'];
        $areaid=$data_equipo[0]['areaid'];
        $cargo=$data_equipo[0]['cargo'];
        $data['cargo']=$cargo;
        $data['areaid']=$areaid;
        $data['categoriaid']=$categoriaid;        
        $data['uid']=$uid;
        $data['asignado']= $dni;
        $data['estado']= 'A';
        $data['dni']=$dni;
      
          
        $tareopersona = new Admin_Model_DbTable_Tareopersona();
      
        //  if ($h_real=='')
        //     {   echo "h_real vacio";

        //     }else
        // {

        
            $data_tareopersona = $tareopersona->_save($data);
        if ($data_tareopersona){

                //$data1['cargo']=$cargo;
                $data1['semanaid']=$semanaid;
                $data1['uid']=$uid;
                $data1['dni']=$dni;
                $data1['fecha_tarea']=$fecha_tarea= $this->_getParam('fecha_tarea');
                $data1['h_totaldia']=$h_real= $this->_getParam('horareal');
                $tipo_actividad= $this->_getParam('tipo_actividad');

                if($tipo_actividad=='G')
                {
                    $data1['nonbillable']= $this->_getParam('horareal');
                    $data1['billable']=0;
                    $data1['adm']=0;
                   
                }
                
                if ($tipo_actividad=='P') {
                    $data1['billable']= $this->_getParam('horareal'); 
                    $data1['nonbillable']=0;
                    $data1['adm']=0;                              
                }

                if ($tipo_actividad=='A') {
                    $data1['billable']= 0;
                    $data1['nonbillable']=0;
                    $data1['adm']=$this->_getParam('horareal');                               
                }



                //$data2['cargo']=$cargo;
                $data2['semanaid']=$semanaid;
                $data2['uid']=$uid;
                $data2['dni']=$dni; 


                $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
                $proyectoid = $this->_getParam('proyectoid');

                $data3['cargo']=$cargo;
                $data3['semanaid']=$semanaid;
                $data3['uid']=$uid;
                $data3['dni']=$dni;
                $data3['categoriaid']=$categoriaid;
                $data3['areaid']=$areaid;
                $data3['proyectoid']=$proyectoid;
                $data3['codigo_prop_proy']=$codigo_prop_proy;
                $data3['funcion']=$cargo;
                $data3['fecha_creacion']=date("Y-m-d");
                $data3['uid_modificacion']=$cargo;


                $wheres=array('dni'=>$dni,'uid'=>$uid,'semanaid'=>$semanaid,'fecha_tarea'=>$fecha_tarea);
                $wheres2=array('dni'=>$dni,'uid'=>$uid,'semanaid'=>$semanaid);
                $wheres3=array('dni'=>$dni,'uid'=>$uid,'cargo'=>$cargo,'semanaid'=>$semanaid,
                               'categoriaid'=>$categoriaid,'areaid'=>$areaid,'proyectoid'=>$codigo_prop_proy,
                               'codigo_prop_proy'=>$codigo_prop_proy);

                //print_r($wheres3);

         
                $sumahora = new Admin_Model_DbTable_Sumahora();
                if($versum=$sumahora->_getOne($wheres))
                {
                }
                else
                {
                    $data_sumahora = $sumahora->_save($data1);
                }

                /*inserta en la tabla suma_controsemana un registro*/
                $suma_control= new Admin_Model_DbTable_Sumahorasemana();
                if($versumcontrol=$suma_control->_getOne($wheres2))
                {

                }
                else
                {
                    $data_sumahora = $suma_control->_save($data2); 
                }


                $suma_planificacion= new Admin_Model_DbTable_Planificacion();
                if($versumcontrol=$suma_planificacion->_getOne($wheres3))
                {

                }
                else
                {
                    //echo "plani";
                    $data_sumahora = $suma_planificacion->_save($data3); 
                }

        }else
        {
        ?>
          <script>                  
            alert("Se actualizo satisfactoriamente");
          </script>
        <?php
        
         
              // $str_actualizar1="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
              //   and actividadid='$actividadid' and 
              //   revision='$revision' and codigo_actividad='$codigo_actividad'
              //   and actividad_padre='$actividad_padre' and cargo='$cargo'
              //   and semanaid='$semanaid'  and fecha_tarea='$fecha_tarea' 
              //   and etapa='$etapa_actualizar' and tipo_actividad='$tipo_actividad_actualizar'
              //   and  uid='$uid'  and  dni='$dni'  and  fecha_planificacion='$fecha_tarea'
              //   and  estado='A' 
              //   ";

              $str_actualizar="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
                categoriaid='$categoriaid' and actividadid='$actividadid' and 
                revision='$revision' and codigo_actividad='$codigo_actividad'
                and actividad_padre='$actividad_padre' and cargo='$cargo'
                and semanaid='$semanaid' and areaid='$areaid' and fecha_tarea='$fecha_tarea' 
                and etapa='$etapa_actualizar' and tipo_actividad='$tipo_actividad_actualizar' 
                and  estado='A' and dni='$dni' and uid='$uid' 
                ";
            $update=$tareopersona -> _update($datos_actualizar,$str_actualizar);

        } //}
       
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


 
    public function actividadgeneralAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $areaid='25';
        $actividad_generales = new Admin_Model_DbTable_Actividadgeneral();
        $data_generales = $actividad_generales->_getActividadgeneralxArea($areaid);
        $this->view->lista = $data_generales;

        $data['proyectoid']=$proyectoid = $this->_getParam('proyectoid');
        $this->view->proyectoid = $proyectoid;        
        $data['codigo_prop_proy']=$codigo_prop_proy = $this->_getParam('codigo');
        $this->view->codigo_prop_proy = $codigo_prop_proy;     
        $data['categoriaid']=$categoriaid = $this->_getParam('categoriaid');
        $this->view->categoriaid = $categoriaid;     
        $data['actividadid']=$actividadid = $this->_getParam('actividadid');
        $this->view->actividadid = $actividadid;     
        $data['revision']=$revision = $this->_getParam('revision');
        $this->view->revision = $revision;     
        $data['codigo_actividad']=$codigo_actividad = $this->_getParam('codigo_actividad');
        $this->view->codigo_actividad = $codigo_actividad;     
        $data['actividad_padre']=$actividad_padre = $this->_getParam('actividad_padre');
        $this->view->actividad_padre = $actividad_padre;     
        $data['h_propuesta']=$h_propuesta = $this->_getParam('h_propuesta');
        $this->view->h_propuesta = $h_propuesta;     
        $data['uid']=$uid;
        $data['asignado']= $dni;
        $data['dni']=$dni;
        $data['etapa']='EJECUCION';
        $data['fecha_tarea']=$fecha_tarea= $this->_getParam('fecha_tarea');
        $this->view->fecha_tarea = $fecha_tarea;     
        $cargo= $this->_getParam('cargo');
        $data['fecha_modificacion']=$fecha_tarea;
     
        $data['semanaid']=$semanaid= $this->_getParam('semanaid');
        $this->view->semanaid = $semanaid;     
        $data['fecha_planificacion']=$fecha_tarea;
        $data['tipo_actividad']=$tipo_actividad= $this->_getParam('tipo_actividad');
        $this->view->tipo_actividad = $tipo_actividad;     
        $data['fecha_creacion']=$fecha_tarea;
        $data['cargo']=$cargo;
        $data['areaid']=$areaid;



        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        } 

        
    }


     public function guardaractividadgeneralAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        //$categoriaid=$this->sesion->personal->ucatid;
        //$areaid=$this->sesion->personal->ucatareaid;
        //$cargo=$this->sesion->personal->ucatcargo;
        $fecha_inicio = $this->_getParam('fecha');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        $data['fecha_tarea']=$fecha_inicio_mod;
        $data['fecha_creacion']=$fecha_inicio_mod;
        $data['fecha_planificacion']=$fecha_inicio_mod;
        $data['proyectoid']=$proyectoid = $this->_getParam('proyectoid');
        $data['codigo_prop_proy']=$codigo_prop_proy = $this->_getParam('codigo');
        $data['revision']=$revision = $this->_getParam('revision');
        $data['actividadid']=$actividadid = $this->_getParam('actividadid');
        $data['codigo_actividad']=$codigo_actividad = $this->_getParam('codigo_actividad');
        $data['actividad_padre']=$actividad_padre = $this->_getParam('actividad_padre');
//        $data['actividad_generalid']=$actividad_generalid = $this->_getParam('actividad_generalid');
//        $data['area_generalid']=$area_generalid = $this->_getParam('area_generalid');
        
        $data['uid']=$uid;
        $data['asignado']= $dni;
        $data['dni']=$dni;
        $data['h_propuesta']="0";
        $data['etapa']="INICIO-NB"."-";
        $data['estado']='A';
        $data['h_real']=null;
        
        $data['fecha_modificacion']=$fecha_inicio_mod;
        $data['semanaid']=$semanaid= $this->_getParam('semanaid');

        $data['tipo_actividad']='G';
        
        $equipo = new Admin_Model_DbTable_Equipo();
        $estado_usuario='A';
        $data_equipo = $equipo->_getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado_usuario,$codigo_prop_proy,$proyectoid);
        $categoriaid=$data_equipo[0]['categoriaid'];
        $areaid=$data_equipo[0]['areaid'];
        $cargo=$data_equipo[0]['cargo'];
        $data['cargo']=$cargo;
        $data['areaid']=$areaid;
        $data['categoriaid']=$categoriaid;

   
        //datos para ctualizar
    
        //$updatetareopersona = new Admin_Model_DbTable_Tareopersona();    
        /*$str="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
        categoriaid='$categoriaid' and actividadid='$actividadid' and 
        revision='$revision' and codigo_actividad='$codigo_actividad'
        and actividad_padre='$actividad_padre' and cargo='$cargo'
        and semanaid='$semanaid' and areaid='$areaid'     ";*/
        //$update=$updatetareopersona -> _update($data,$str);
        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $data_tareopersona = $tareopersona->_save($data);
        if ($data_tareopersona){

        ?>

          <script>                  
           //alert("Se guardo satisfactoriamente");
           // document.location.href="/timesheet/index/calendar";
             
          </script>
        <?php
            
        } else
        {
            ?>

          <script>                  
           //alert("No Se guardo satisfactoriamente");
           // document.location.href="/timesheet/index/calendar";
             
          </script>
        <?php
        
                /*$str="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
            categoriaid='$categoriaid' and actividadid='$actividadid' and 
            revision='$revision' and codigo_actividad='$codigo_actividad'
            and actividad_padre='$actividad_padre' and cargo='$cargo'
            and semanaid='$semanaid' and areaid='$areaid' and fecha_tarea='$fecha_tarea' 
            and fecha_planificacion='$fecha_tarea' and etapa='EJECUCION' and tipo_actividad='$tipo_actividad'";
          //  echo $str;
            $update=$tareopersona -> _update($datos,$str);*/
        }
       
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


public function eliminartareoAction(){
    try {
       $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $categoriaid=$this->sesion->personal->ucatid;
        $areaid=$this->sesion->personal->ucatareaid;
        $cargo=$this->sesion->personal->ucatcargo;
        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $proyectoid = $this->_getParam('proyectoid');
        $codigo_prop_proy = $this->_getParam('codigo');
        $actividadid = $this->_getParam('actividadid');
        $revision = $this->_getParam('revision');
        $codigo_actividad = $this->_getParam('codigo_actividad');
        $actividad_padre = $this->_getParam('actividad_padre');
        $fecha_tarea= $this->_getParam('fecha_tarea');
        $semanaid=$this->_getParam('semanaid');
        $tipo_actividad=$this->_getParam('tipo_actividad');
        $actividad_generalid=$this->_getParam('actividad_generalid');
        $etapa=$this->_getParam('etapa');

        // if($etapa=='INICIO' or $etapa=='EJECUCION-NB-' or $etapa=='INICIO-NB-')
        // {
        //     $pk1  =   array(                        
        //     'codigo_prop_proy'   =>$codigo_prop_proy,
        //     'codigo_actividad'   =>$codigo_actividad,
        //     'actividadid'   =>$actividadid,
        //     'revision'   =>$revision,
        //     'actividad_padre'   =>$actividad_padre,
        //     'proyectoid'   =>$proyectoid,
        //     'semanaid'   =>$semanaid,
        //     'uid'   =>$uid,
        //     'dni'   =>$dni,
        //     'tipo_actividad'   =>$tipo_actividad,
        //     'etapa'  => 'EJECUCION',
        //     );
        //     $tareopersona->_deleteTareasxSemanaX($pk1);

        // }


         if($actividad_generalid=='' and $tipo_actividad!='G' )
         {
            $pk1  =   array(                        
            'codigo_prop_proy'   =>$codigo_prop_proy,
            'codigo_actividad'   =>$codigo_actividad,
            'actividadid'   =>$actividadid,
            'revision'   =>$revision,
            'actividad_padre'   =>$actividad_padre,
            'proyectoid'   =>$proyectoid,
            'semanaid'   =>$semanaid,
            'uid'   =>$uid,
            'dni'   =>$dni,
            'tipo_actividad'   => $tipo_actividad,
            //'etapa'  => 'EJECUCION',
            );
            $tareopersona->_deleteTareasxSemanaX($pk1);

         }
         else
         {



            if($actividad_generalid==null and $etapa=='INICIO-NB-')
            {              

          
                $tareopersona->deletenb($codigo_prop_proy,$codigo_actividad,$actividadid,$revision,$actividad_padre,$proyectoid,$semanaid,$uid,$dni,$tipo_actividad,$etapa);

            }
            else
            {

             $pk  =   array(                        
            'codigo_prop_proy'   =>$codigo_prop_proy,
            'codigo_actividad'   =>$codigo_actividad,
            'actividadid'   =>$actividadid,
            'revision'   =>$revision,
            'actividad_padre'   =>$actividad_padre,
            'proyectoid'   =>$proyectoid,
            'semanaid'   =>$semanaid,
            'uid'   =>$uid,
            'dni'   =>$dni,
            'tipo_actividad'   =>$tipo_actividad,
            'actividad_generalid'  => $actividad_generalid,
             );
            $tareopersona->_deleteTareasxSemana($pk);

            }

        
         }


         
   
       
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


public function sumatareorealAction(){
    try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $dbucat= new Admin_Model_DbTable_Usuariocategoria();
        $datosucat = $dbucat->_getUsuarioxPersona($uid,$dni);
        $cargo = $datosucat[0]['cargo'];
        $areaid = $datosucat[0]['areaid']; 
        $semanaid=$this->_getParam('semanaid');
        $fecha_tarea=$this->_getParam('fecha_tarea');
        $cargoid=$this->_getParam('cargo');
        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $sumar=$tareopersona-> _getHorasRealxDia($semanaid,$fecha_tarea,$uid,$dni,$cargoid);
        print_r($sumar[0]['tareo_persona_horas_reales']);
          ?>
              <script>                  
                alert("No se elimino");
              </script>
            <?php
        
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function pruebaAction(){

        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;


        
        $tiempo_id=$this->_getParam('tiempo_id');
        $codigo_prop_proy=$this->_getParam('codigo_prop_proy');
        $codigo_actividad=$this->_getParam('codigo_actividad');
      
        $revision=$this->_getParam('revision');
        $actividad_padre=$this->_getParam('actividad_padre');
       
        $proyectoid=$this->_getParam('proyectoid');
        $semanaid=$this->_getParam('semanaid');
        $asignado=$this->_getParam('asignado');
        $areaid=$this->_getParam('areaid');      
        $tipo_actividad=$this->_getParam('tipo_actividad');

        $actividadid=$this->_getParam('actividadid');

        $etapa=$this->_getParam('etapa');

        $fecha_tarea=$this->_getParam('fecha_tarea');
        $actividad_generalid=$this->_getParam('actividad_generalid');
        $fecha_creacion=$this->_getParam('fecha_creacion');

        // print_r($etapa);
        // print_r($actividadid);
        print_r($tipo_actividad);
  
        // print_r($actividad_generalid);
        // print_r($asignado);
        // print_r($fecha_tarea);
        // print_r($semanaid);
        // print_r($actividad_padre);


        for ($i=1; $i <= 30; $i++) { 

            for ($j=0; $j < 7; $j++) { 

                $equipo = new Admin_Model_DbTable_Equipo();
                $estado_usuario='A';
                $data_equipo = $equipo->_getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado_usuario,$codigo_prop_proy[$i][$j],$proyectoid[$i][$j]);
                $categoriaid=$data_equipo[0]['categoriaid'];
                $areaid=$data_equipo[0]['areaid'];
                $cargo=$data_equipo[0]['cargo'];
                $data['cargo']=$cargo;
                $data['areaid']=$areaid;
                $data['categoriaid']=$categoriaid;
                # code...
             //echo $i;
             $data['h_real'] = $tiempo_id[$i][$j];
             $data['codigo_prop_proy'] = $codigo_prop_proy[$i][$j];
             $data['codigo_actividad'] = $codigo_actividad[$i][$j];
             $data['actividadid'] = $actividadid[$i][$j];
             $data['revision'] = $revision[$i][$j];
             $data['actividad_padre'] = $actividad_padre[$i][$j];
             $data['proyectoid'] = $proyectoid[$i][$j];
             $data['semanaid'] = $semanaid[$i][$j];
             $data['fecha_tarea'] = $fecha_tarea[$i][$j];
             $data['fecha_planificacion'] = $fecha_tarea[$i][$j];
             $data['asignado'] = $asignado[$i][$j];
             //$data['areaid'] = $areaid[$i][$j];

             //if($tipo_actividad[$i][$j]=='' or $tipo_actividad[$i][$j]==null) { $type='';} else { $type=$tipo_actividad[$i][$j];} 
             $data['tipo_actividad'] = $tipo_actividad[$i][$j];
           
             $resultado = str_replace("INICIO", "EJECUCION", $etapa[$i][$j]);
             $data['etapa']=$resultado;

             $data['estado'] = 'A';
             $data['actividad_generalid'] = $actividad_generalid[$i][$j];
             $data['uid'] = $uid;
             $data['dni'] = $dni;
             $data['fecha_creacion'] = date("Y-m-d");;

                 // echo $tiempo_id[$i][$j];
                 // echo "nhndf";
        
            $wheres=array('codigo_prop_proy'=>$codigo_prop_proy[$i][$j],'codigo_actividad'=>$codigo_actividad[$i][$j],
                'actividadid'=>$actividadid[$i][$j],'revision'=>$revision[$i][$j],
                'actividad_padre'=>$actividad_padre[$i][$j],'proyectoid'=>$proyectoid[$i][$j],'semanaid'=>$semanaid[$i][$j] 
                ,'fecha_tarea'=>$fecha_tarea[$i][$j],'uid'=>$uid,'dni'=>$dni,'cargo'=>$cargo,
                'fecha_planificacion'=>$fecha_tarea[$i][$j],'etapa'=>$resultado,'tipo_actividad'=>$tipo_actividad[$i][$j]);

            //print_r($data);//exit();

           $verdata = new Admin_Model_DbTable_Tareopersona();
           $ty=$verdata->_getOne($wheres);
            //print_r($ty);

           if($ty){
            
              //echo "update";
                $etapa_actualizar = str_replace("INICIO", "EJECUCION", $etapa[$i][$j]);                
                $datos_actualizar['fecha_modificacion']=date("Y-m-d");
                $datos_actualizar['h_real']=$tiempo_id[$i][$j];              

                // $str_actualizar="codigo_prop_proy=$code and proyectoid='$proyectoid[$i][$j]' and 
                // categoriaid='$categoriaid[$i][$j]' and actividadid='$actividadid[$i][$j]' and 
                // revision='$revision[$i][$j]' and codigo_actividad='$codigo_actividad[$i][$j]'
                // and actividad_padre='$actividad_padre[$i][$j]' and cargo='$cargo[$i][$j]'
                // and semanaid='$semanaid[$i][$j]' and areaid='$areaid[$i][$j]' and fecha_tarea='$fecha_tarea[$i][$j]' 
                // and etapa='$etapa_actualizar' and tipo_actividad='$tipo_actividad[$i][$j]' 
                // and  estado='A' ";

                //print_r($data);                
                $update=$verdata -> _updateX($datos_actualizar,$wheres);

            }
             else
            {
                $data_tareopersona = $verdata->_save($data);
       
            }//print_r($data);

            }
        }

        //print_r($nive);
            // $nive=array();
            // for ($i=3; $i <=4 ; $i++) 
            // {       
            // $where['nivel']=(string)$i;                
            // $nive[]=$verequipo->_getFilter($where);
        
        // $proyectoid = '1416.10.07';
        // $codigo_prop_proy = 'PROP-2015-20100079501-1416-14.10.230-B';
        // $actividadid ='3';
        // $revision = 'B';
        // $codigo_actividad = '1416.10.07-3';
        // $actividad_generalid = '5';
        // $semanaid='21';
        // $tipo_actividad= 'G';

        // //echo $codigo_prop_proy;

        // $conteotareo =new Admin_Model_DbTable_Tareopersona();
        // $ctareo=$conteotareo->_getConteotareo($semanaid,$codigo_actividad,$actividad_generalid ,$tipo_actividad,$codigo_prop_proy,$proyectoid,$revision,$actividadid,$uid,$dni);
        // // print_r($ctareo);
        // echo $ctareo[0]['count'];

    }


     public function updateetapanbAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        //$categoriaid=$this->sesion->personal->ucatid;
        //$areaid=$this->sesion->personal->ucatareaid;
        //$cargo=$this->sesion->personal->ucatcargo;

        $fecha_inicio = $this->_getParam('fecha');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        
        $proyectoid = $this->_getParam('proyectoid');
        $codigo_prop_proy = $this->_getParam('codigo');
        $actividadid = $this->_getParam('actividadid');
        $revision = $this->_getParam('revision');
        $codigo_actividad = $this->_getParam('codigo_actividad');
        $actividad_padre = $this->_getParam('actividad_padre');
        $actividad_generalid = $this->_getParam('tarea_general');
        $semanaid=$this->_getParam('semanaid');
        $tipo_actividad= $this->_getParam('tipo_actividad');
        $actividad_gid= $this->_getParam('actividad_generalid');
        $ban= $this->_getParam('ban');

        // if($actividad_gid=='' and $tipo_actividad=='P')
        // {
        //     $count=2;
        // }

        if($actividad_gid=='' and $tipo_actividad=='G')
        {
            $conteotareo =new Admin_Model_DbTable_Tareopersona();
            $ctareo=$conteotareo->_getConteotareo2($semanaid,$codigo_actividad,$tipo_actividad,$codigo_prop_proy,$proyectoid,$revision,$actividadid,$uid,$dni);
            // print_r($ctareo);
            $count= $ctareo[0]['count'];
            
        }
        elseif ($actividad_gid=='' and $tipo_actividad=='P' )

            {
                //$count=2;
                $conteotareo =new Admin_Model_DbTable_Tareopersona();
                $ctareo=$conteotareo->_getConteotareo2($semanaid,$codigo_actividad,$tipo_actividad,$codigo_prop_proy,$proyectoid,$revision,$actividadid,$uid,$dni);
                // print_r($ctareo);
                $count= $ctareo[0]['count'];
            }
            else
            {  
                $conteotareo =new Admin_Model_DbTable_Tareopersona();
                $ctareo=$conteotareo->_getConteotareo($actividad_gid,$semanaid,$codigo_actividad,$tipo_actividad,$codigo_prop_proy,$proyectoid,$revision,$actividadid,$uid,$dni);
                // print_r($ctareo);
                $count= $ctareo[0]['count'];
            }
     

        

        $etapa_inicio = $this->_getParam('etapa');
        
        $datos_inicio['actividad_generalid']=$this->_getParam('tarea_general');
        $datos_inicio['tipo_actividad']='G';
        $datos_inicio['etapa']='INICIO-NB-'.$actividad_generalid;
        $datos_inicio['fecha_modificacion']=$fecha_inicio_mod;

        $etapa_ejecucion = str_replace("INICIO", "EJECUCION", $etapa_inicio);
        
        $datos_ejecucion['actividad_generalid']= $this->_getParam('tarea_general');
        $datos_ejecucion['tipo_actividad']='G';
        $datos_ejecucion['etapa']='EJECUCION-NB-'.$actividad_generalid;
        $datos_ejecucion['fecha_modificacion']=$fecha_inicio_mod;

        
        
        $equipo = new Admin_Model_DbTable_Equipo();
        $estado_usuario='A';
        $data_equipo = $equipo->_getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado_usuario,$codigo_prop_proy,$proyectoid);
        $categoriaid=$data_equipo[0]['categoriaid'];
        $areaid=$data_equipo[0]['areaid'];
        $cargo=$data_equipo[0]['cargo'];
        $data['cargo']=$cargo;
        $data['areaid']=$areaid;
        $data['categoriaid']=$categoriaid;

        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $str_inicio="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
            categoriaid='$categoriaid' and actividadid='$actividadid' and 
            revision='$revision' and codigo_actividad='$codigo_actividad'
            and actividad_padre='$actividad_padre'   and semanaid='$semanaid' and areaid='$areaid' 
            and etapa='$etapa_inicio' and tipo_actividad='$tipo_actividad' 
            and estado='A' and uid='$uid' and dni='$dni'";

        $str_ejecucion="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
            categoriaid='$categoriaid' and actividadid='$actividadid' and 
            revision='$revision' and codigo_actividad='$codigo_actividad'
            and actividad_padre='$actividad_padre'   and semanaid='$semanaid' and areaid='$areaid' 
            and  etapa='$etapa_ejecucion' and tipo_actividad='$tipo_actividad' 
            and estado='A' and uid='$uid' and dni='$dni'
            ";
            
        ?>
        <script>                  
           // alert("<?php echo $count ?>");
        </script>
        <?php

        if($count>1)
        {
            $update_inicio=$tareopersona -> _update($datos_inicio,$str_inicio);
            $update_ejecucion=$tareopersona -> _update($datos_ejecucion,$str_ejecucion);

            if($update_inicio) { 
                if($update_ejecucion)
                {

                }
                 else
                { ?>
                     <script>                  
                         alert("La actividad ya ha sido ocupada.revise");
                     </script>  <?php               
                }
            }
            else
            { ?>
                <script>                  
                    alert("La actividad ya ha sido ocupada....revise");
                </script>
            <?php
            }
        }
        else
        {  
            $update_inicio=$tareopersona -> _update($datos_inicio,$str_inicio);
            if($update_inicio)
            { 
                
            }
            else
            { ?>
                <script>                  
                    alert("La actividad ya ha sido ocupada..revise");
                </script>
            <?php
            }

        }        
       
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function updateetapabAction(){
        try {
        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        //$categoriaid=$this->sesion->personal->ucatid;
        //$areaid=$this->sesion->personal->ucatareaid;
        //$cargo=$this->sesion->personal->ucatcargo;

        $fecha_inicio = $this->_getParam('fecha');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        

        $proyectoid = $this->_getParam('proyectoid');
        $codigo_prop_proy = $this->_getParam('codigo');
        $actividadid = $this->_getParam('actividadid');
        $revision = $this->_getParam('revision');
        $codigo_actividad = $this->_getParam('codigo_actividad');
        $actividad_padre = $this->_getParam('actividad_padre');
        $actividad_generalid = $this->_getParam('tarea_general');
        $semanaid=$this->_getParam('semanaid');
        $tipo_actividad= $this->_getParam('tipo_actividad');

        $etapa_inicio = $this->_getParam('etapa');
        $etapa_ejecucion = str_replace("INICIO", "EJECUCION", $etapa_inicio);
        
        $datos_inicio['actividad_generalid']=null;
        $datos_ejecucion['actividad_generalid']= null;
        
        $datos_inicio['tipo_actividad']='P';
        $datos_ejecucion['tipo_actividad']='P';

        $datos_inicio['etapa']='INICIO';
        $datos_ejecucion['etapa']='EJECUCION';

        
        $datos_inicio['fecha_modificacion']=$fecha_inicio_mod;
        $datos_ejecucion['fecha_modificacion']=$fecha_inicio_mod;
        
        $equipo = new Admin_Model_DbTable_Equipo();
        $estado_usuario='A';
        $data_equipo = $equipo->_getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado_usuario,$codigo_prop_proy,$proyectoid);
        $categoriaid=$data_equipo[0]['categoriaid'];
        $areaid=$data_equipo[0]['areaid'];
        $cargo=$data_equipo[0]['cargo'];
        $data['cargo']=$cargo;
        $data['areaid']=$areaid;
        $data['categoriaid']=$categoriaid;

        $tareopersona = new Admin_Model_DbTable_Tareopersona();
        $str_inicio="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
            categoriaid='$categoriaid' and actividadid='$actividadid' and 
            revision='$revision' and codigo_actividad='$codigo_actividad'
            and actividad_padre='$actividad_padre'   and semanaid='$semanaid' and areaid='$areaid' 
            and etapa='$etapa_inicio' and tipo_actividad='$tipo_actividad' 
            and estado='A' and uid='$uid' and dni='$dni'
            ";
        $str_ejecucion="codigo_prop_proy='$codigo_prop_proy' and proyectoid='$proyectoid' and 
            categoriaid='$categoriaid' and actividadid='$actividadid' and 
            revision='$revision' and codigo_actividad='$codigo_actividad'
            and actividad_padre='$actividad_padre'   and semanaid='$semanaid' and areaid='$areaid' 
            and  etapa='$etapa_ejecucion' and tipo_actividad='$tipo_actividad' 
            and estado='A' and uid='$uid' and dni='$dni'
            ";
            $update_inicio=$tareopersona -> _update($datos_inicio,$str_inicio);
            $update_ejecucion=$tareopersona -> _update($datos_ejecucion,$str_ejecucion);
            if ($update_inicio || $update_ejecucion) { //echo "guardo";
            }
            else
            {?>
                <script>                  
                    alert("No se guardo cargue nuevamente la pagina o ya tiene una tarea facturable creada.");
                </script>
            <?php
        }
       
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }



    public function sumahorasAction(){
        try {

    // '/timesheet/index/sumahoras/semanaid/'+semanaid+/'fecha'/+fecha+/'fechainiciomod'/+fechainiciomod+/'uid'/+uid+/'dni'/+dni_+/'cargo'/+cargo;  
        $this->_helper->layout()->disableLayout();

        $semanaid = $this->_getParam('semanaid');
        $fecha = $this->_getParam('fecha');
        $fecha_inicio_mod = $this->_getParam('fechainiciomod');    
        $uid = $this->_getParam('uid');
        $dni = $this->_getParam('dni');
        $cargo = $this->_getParam('cargo');

        $tareo_persona = new Admin_Model_DbTable_Tareopersona();
        $semana=date('W', strtotime($fecha_inicio_mod)); 
        $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
      
        $this->view->actividades= $datos_tareopersona;      

        $this->view->semanaid = $semanaid;
        $this->view->fecha = $fecha;
  
        $this->view->uid = $uid;
        $this->view->dni = $dni;
        $this->view->cargo = $cargo;

        }
        catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


    public function traersumahorasAction(){
        try {

    $this->_helper->layout()->disableLayout();

        $semanaid = $this->_getParam('semanaid');
        $fecha = $this->_getParam('fecha');
        $fecha_inicio_mod = $this->_getParam('fechainiciomod');    
        $uid = $this->_getParam('uid');
        $dni = $this->_getParam('dni');
        $cargo = $this->_getParam('cargo');

        $wheres=array('dni'=>$dni,'uid'=>$uid,'cargo'=>$cargo,'semanaid'=>$semanaid,'fecha_tarea'=>$fecha);

        $sumahora = new Admin_Model_DbTable_Sumahora();
        $versum=$sumahora->_getOne($wheres);

        $this->view->versum= $versum;
        $this->view->semanaid = $semanaid;
        $this->view->fecha = $fecha;
  
        $this->view->uid = $uid;
        $this->view->dni = $dni;
        $this->view->cargo = $cargo;

        }
        catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


    public function historialaprobadoAction(){
        try {
       // print_r($this->sesion);

        $user= new Admin_Model_DbTable_Usuario();
        $vuser=$user->_getUsuarioAll();
        $this->view->usuarios=$vuser;
        //print_r($vuser);

        $codigo_prop_proy='PROP-2015-20100079501-1416-15.10.042-A';
        $proyectoid='1214.10.20';
        
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        //$uid='walter.melgarejo';
        //$dni='43362864';
        //codigo_prop_proy, proyectoid, uid, dni, categoriaid, areaid, cargo
               


        $nivel='0';
        $this->view->ni=$nivel;
                                    
        $verequipo = new Admin_Model_DbTable_Equipo();
        $where['codigo_prop_proy']=$codigo_prop_proy;
        $where['proyectoid']=$proyectoid;
        $where['estado']='A';

        if($nivel=='0')
          {
          $nive=array();
          for ($i=1; $i <=4 ; $i++) 
            {
            $where['nivel']=(string)$i;
            $nive[]=$verequipo->_getFilter($where);
            }           
        }

        if($nivel=='1')
            {            
            $where['nivel']='2';            
            $nive=$verequipo->_getFilter($where);
                                        
            //print_r($nive);
            }        

        if($nivel=='2')
            {
            $nive=array();
            for ($i=3; $i <=4 ; $i++) 
            {       
            $where['nivel']=(string)$i;                
            $nive[]=$verequipo->_getFilter($where);
                                                                                     
            }                                            
        }
                                    
        if($nivel=='3')
        {                       
        $where['nivel']='4';                
        $nive=$verequipo->_getFilter($where);
                                       
        }

        //print_r($nive);


        $this->view->nive=$nive;
        print_r($nive);         
        //echo $nive['uid'];  


        $suma_hora = new Admin_Model_DbTable_Sumahorasemana();
        $versuma=$suma_hora->_getSumahorasemanaAll();
        $this->view->listasuma=$versuma;
        //print_r($versuma);


        }
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function filtrosAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $usuario = $this->_getParam('usuario');
            $dateinicio = $this->_getParam('dateinicio');
            //$dateinicio = '2015-05-12';
            $semana=date('W', strtotime($dateinicio)); 
            // $fechavista = date("Y-m-d", strtotime($dateinicio));
            // $this->view->fecharecuperada=$fechavista;
            
            $this->view->fecharecuperada=$dateinicio;

            $datefin = $this->_getParam('datefin');    
            $estado = $this->_getParam('estado');


            if ($usuario=='T') {

                if ($estado=='T') {
                    $wheresumsemana = array( 'semanaid' => $semana);                    
                }
                else
                {
                    $wheresumsemana = array( 'estado' => $estado, 'semanaid' => $semana);
                }
            
            }
            else
            {
                if ($estado=='T') {
                    $wheresumsemana = array( 'uid' => $usuario, 'semanaid' => $semana);
                }
                else
                {
                    $wheresumsemana = array( 'uid' => $usuario, 'estado' => $estado, 'semanaid' => $semana);                    
                }
            }


            //$attrib = array('dni', 'uid');
            $order = array('dni ASC');
            $suma_horasemana = new Admin_Model_DbTable_Sumahorasemana();
            $versumasemana=$suma_horasemana->_getFilter($wheresumsemana,$attrib=null,$order);
            $this->view->listasuma=$versumasemana;
            //print_r($versumasemana);



            }
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


    public function timesheetsemanaAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');

            $uid_validacion = $this->sesion->uid;
            $dni_validacion = $this->sesion->dni;
            $this->view->uid_validacion=$uid_validacion;
            $this->view->dni_validacion=$dni_validacion;
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->cargo = $areaid;
            $this->view->uid = $uid;
            $this->view->dni = $dni;

            $ano=date("Y");/*ojo cambiar  con el tiempo --revisar */
            $enero = mktime(1,1,1,1,1,$ano); 
            //$mos = (11-date('w',1))%7-3; 
            $mos = (11-date('w',$enero))%7-3;

            $this->view->mos=$mos;
            $this->view->enero=$enero;
            $this->view->semana = $semana;


            $tareo_persona = new Admin_Model_DbTable_Tareopersona();
            //$semana=date('W', strtotime($fecha_inicio_mod)); 

            $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
            $this->view->actividades= $datos_tareopersona;

        }    
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function guardarcomentarioAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $estado = $this->_getParam('estado');
            $uid_validacion=$this->_getParam('uid_validacion');
            $dni_validacion=$this->_getParam('dni_validacion');
            $fecha_validacion=$this->_getParam('fecha');
            
            $time = time();
            $datetime=date("d-m-Y (H:i:s)", $time);
            $fecha_actualizar=$datetime;

            $etapa_validacion=$this->_getParam('etapa');

            $data['cargo']=$cargo;
            $data2['cargo']=$cargo;

            $data['semanaid']=$semana;
            $data2['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data2['uid']=$uid;
            $data2['dni']=$dni;
            $data['uid_validacion']=$uid_validacion;
            $data['dni_validacion']=$dni_validacion;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;
            $data['fecha_validacion']=$fecha_actualizar;
            $data['etapa']=$etapa_validacion;

            $where['uid']=$uid;
            $where['dni']=$dni;
            //$where['uid_validacion']=$uid_validacion;
            //$where['dni_validacion']=$dni_validacion;
            $where['cargo']=$cargo;
            $where['semanaid']=$semana;

//            print_r($data);

            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOne($where))
            {
                echo "existe";
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);

                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_actualizar;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

                print_r($pk1);
            //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                //$usecoment=$coment->_updateX($data2,$pk);
                
                $usecoment=$vercoment->_updateXUsuario($data3,$pk1);}
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                if ( $estado=='R'){
                        $datos_actualizar_sumahoras['estado']='O';   
                        $datos_actualizar_sumahoras['estado_real']='';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);
                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='1';    
                }
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
                }



            else
            {echo "no existe";
                $coment=new Admin_Model_DbTable_Usuariovalidacion();
              $usercoment=$coment->_save($data);
                //echo "save";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
               if ( $estado=='R'){
                    $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semanaid' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);
                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='1';    
                }
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
            }

            
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }


     public function timesheetaprobacionAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
             $is_gerente_general= $this->sesion->personal->ucatcargo;    
            //$isresponsable=$this->sesion->is_responsable;    
            $isjefe=$this->sesion->is_jefe;    
            if ($is_gerente_general=='JEFE') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
                //$equipo_aprobacion = $equipo->_getListarNivel4xNivel3($uid,$dni,'4','2',$areaid);
                $equipo_aprobacion = $equipo->_getListarEquipoArea($areaid);
                //print_r($equipo_aprobacion);
                $this->view->equipos_horas_aprobar= $equipo_aprobacion;    
            }


            

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

     public function enviartimesheetAction(){
        try {
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;
            $this->_helper->layout()->disableLayout();            
            $fecha_inicio = $this->_getParam('fecha_calendario');
            $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
            $semanaid=date('W', strtotime($fecha_inicio_mod)); 
            $tareopersona = new Admin_Model_DbTable_Tareopersona();
            $data_tareopersona = $tareopersona->_getTareoxPersonaxSemana($uid,$dni,$semanaid);
            if ($data_tareopersona)
            {
                $datos_actualizar['estado']='C';
                $str_actualizar="semanaid='$semanaid' and uid='$uid' and dni='$dni' and
                estado='A' 
                ";
                $update=$tareopersona -> _update($datos_actualizar,$str_actualizar);
           }

            $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
            $wheres=array('dni'=>$dni,'uid'=>$uid,'semanaid'=>$semanaid);
            $tareosemana=$sumahorassemana->_getOne($wheres);
            //print_r($tareosemana);
            if ($tareosemana)
            {
                $datos_actualizar_sumahoras['estado']='P';
                $str_actualizar_sumahoras="semanaid='$semanaid' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
                  
            }

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

    public function timesheetaprobaciongerenteAction(){
        try {
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    
            $this->view->aprobacion=$this->sesion->personal->ucataprobacion;  
            $areaid=$this->sesion->personal->ucatareaid;   
            $isgerente=$this->sesion->is_gerente;    
            if ($isgerente=='S') 
            {
                if ($this->view->aprobacion)
                {
                    if ($this->view->aprobacion=='3.B1')       
                    {   
                        $equipo = new Admin_Model_DbTable_Equipo();
                        $equipo_aprobacion = $equipo->_getListarEquipoxProyectoxGerentexAprobacion($uid,$dni);
                        $this->view->equipos_horas_aprobar= $equipo_aprobacion;        
                    }
                    else
                    {
                    $equipo = new Admin_Model_DbTable_Equipo();
                    $equipo_aprobacion = $equipo->_getListarEquipoxProyectoxGerente($uid,$dni);
                    $this->view->equipos_horas_aprobar= $equipo_aprobacion;    
                    }
                }
                else
                {
                $equipo = new Admin_Model_DbTable_Equipo();
                $equipo_aprobacion = $equipo->_getListarEquipoxProyectoxGerente($uid,$dni);
                $this->view->equipos_horas_aprobar= $equipo_aprobacion;    
                }
            }
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

   

public function timesheetsemanagerenteAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');

            $uid_validacion = $this->sesion->uid;
            $dni_validacion = $this->sesion->dni;
            $this->view->uid_validacion=$uid_validacion;
            $this->view->dni_validacion=$dni_validacion;
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->cargo = $areaid;
            $this->view->uid = $uid;
            $this->view->dni = $dni;
            $ano=date("Y");/*ojo cambiar  con el tiempo --revisar */
            $enero = mktime(1,1,1,1,1,$ano); 
            //$mos = (11-date('w',1))%7-3; 
            $mos = (11-date('w',$enero))%7-3;

            $this->view->mos=$mos;
            $this->view->enero=$enero;
            $this->view->semana = $semana;


            $tareo_persona = new Admin_Model_DbTable_Tareopersona();
            //$semana=date('W', strtotime($fecha_inicio_mod)); 


            $this->view->semana = $semana;
            $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
            $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
            //$data_tareo = $tareo->_getTareoXUid($where);
            $this->view->actividades= $datos_tareopersona;

        }    
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }



/*public function guardarcomentariogerenteAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $estado = $this->_getParam('estado');

            $data['cargo']=$cargo;
            $data['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;

            $where['uid']=$uid;
            $where['dni']=$dni;
            $where['cargo']=$cargo;
            $where['semanaid']=$semana;

            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOne($where))
            {
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana );
                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $coment=new Admin_Model_DbTable_Usuariovalidacion();
                $usecoment=$coment->_updateX($data2,$pk);
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                $datos_actualizar_sumahoras['estado']='2';
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);


            }
            else
            {
                $coment=new Admin_Model_DbTable_Usuariovalidacion();
                $usercoment=$coment->_save($data);
                //echo "save";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                $datos_actualizar_sumahoras['estado']='2';
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
            }

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }
*/

public function guardarcomentariogerenteAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $estado = $this->_getParam('estado');
            $uid_validacion=$this->_getParam('uid_validacion');
            $dni_validacion=$this->_getParam('dni_validacion');
            //$fecha_validacion=$this->_getParam('fecha');
            $etapa_validacion=$this->_getParam('etapa');

            $data['cargo']=$cargo;
            $data2['cargo']=$cargo;
            $data['semanaid']=$semana;
            $data2['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data2['uid']=$uid;
            $data2['dni']=$dni;
            $data['uid_validacion']=$uid_validacion;
            $data['dni_validacion']=$dni_validacion;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;
            $time = time();
            $datetime=date("d-m-Y (H:i:s)", $time);
            $fecha_validacion=$datetime;

            $data['fecha_validacion']=$fecha_validacion;
            $data['etapa']=$etapa_validacion;

            $where['uid']=$uid;
            $where['dni']=$dni;
            //$where['uid_validacion']=$uid_validacion;
            //$where['dni_validacion']=$dni_validacion;
            $where['cargo']=$cargo;
            $where['semanaid']=$semana;
            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOnexUsuario($where))
            {

                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);
                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_validacion;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

               // print_r($pk1);
                //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                    //$usecoment=$coment->_updateX($data2,$pk);
                
                    $usecoment=$vercoment->_updateXUsuario($data3,$pk1);
                }
                echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                
                 if ( $estado=='R'){
                  $datos_actualizar_sumahoras1['estado']='0';   
                  $datos_actualizar_sumahoras1['estado_real']='RECHAZADO';
                  $str_actualizar_sumahoras1="semanaid='$semana' and uid='$uid' and dni='$dni'";
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);

                        $update=$sumahorassemana -> _update($datos_actualizar_sumahoras1,$str_actualizar_sumahoras1);
                }
                if ( $estado=='B'){
                    $tareopersona = new Admin_Model_DbTable_Tareopersona();
                    $datos_actualizar['estado']='C';
                    $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='A' ";
                    $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);  
                    $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
                    $planificacion = new Admin_Model_DbTable_Planificacion();
                    $proyectos=$planificacion->_getSemanaxGerenteProyecto($semana,$uid,$dni);
                    print_r($proyectos);
                    $validado=0;
                    $novalidado=0;
                        foreach ($proyectos as $datos) {
                            $where_validador['uid_validacion']=$datos['uid'];
                            $where_validador['dni_validacion']=$datos['dni'];
                            $where_validador['estado_usuario']='B';
                            $where_validador['etapa']='2';
                            $where_validador['semanaid']=$semana; 
                            $where_validador['uid']=$uid; 
                            $where_validador['dni']=$dni; 

                            $cant_validadores=$vercoment->_getOnexUsuarioxValidador($where_validador);
                            print_r($cant_validadores);
                            if ($cant_validadores){
                                $validado++;
                            }
                            else
                            {
                                $novalidado++;
                            }
                                                
                        }
                             echo "cantidad d evlaidacioes"; echo $validado;
                             echo "cantidad d eno validados";  echo $novalidado;
                             echo "cantidad d evalidsdotres"; echo count($proyectos);

                        if (count($proyectos)==$validado)
                        {
                               echo "apribadoooo";
                            $datos_actualizar_validador['estado_real']='APROBADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni' ";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras2['estado']='2';
                            $str_actualizar_sumahoras2="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras2,$str_actualizar_sumahoras2);

                        }
                        else
                        {
                              echo "rechazadodoodod";
                            $datos_actualizar_validador['estado_real']='PENDIENTE';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras3['estado']='3';
                            $str_actualizar_sumahoras3="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras3,$str_actualizar_sumahoras3);

                        }   
                    }

            }
            else
            {//echo "no existe";
                $coment=new Admin_Model_DbTable_Usuariovalidacion();
              //$usercoment=$coment->_save($data);
                //echo "save";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                 if ( $estado=='R'){
                  $datos_actualizar_sumahoras['estado']='0';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);
                }
                 if ( $estado=='B'){
                   $datos_actualizar_sumahoras['estado']='2';
                         $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='C';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='A' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);  
                }

                
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                //$update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
            }

            
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }


 public function timesheethistoricoAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
            //$isresponsable=$this->sesion->is_responsable;    
            $isgerente=$this->sesion->is_gerente;    
            if ($isgerente=='S') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
                $equipo_aprobacion = $equipo->_getListarEquipoxProyectoxGerente($uid,$dni);

                $this->view->equipos_horas_aprobar= $equipo_aprobacion;    
            }


            

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

public function guardarcomentarioequipoAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $fecha_duplicidad = $this->_getParam('fecha');
            $estado = $this->_getParam('estado');
            $uid_validacion=$this->_getParam('uid_validacion');
            $dni_validacion=$this->_getParam('dni_validacion');
            $time = time();
            $datetime=date("d-m-Y (H:i:s)", $time);
            $fecha_validacion=$datetime;
            $etapa_validacion=$this->_getParam('etapa');
            $fechaduplica=$this->_getParam('fecha_calendario');   
            $data['cargo']=$cargo;
            $data['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data['uid_validacion']=$uid;
            $data['dni_validacion']=$dni;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;
            $data['fecha_validacion']=$fecha_validacion;
            $data['etapa']=$etapa_validacion;
            $data['orden']='1';
            $data['estado']='A';
            $where['uid']=$uid;
            $where['dni']=$dni;
            $where['semanaid']=$semana;
            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOnexUsuario($where))
            {
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);
                $data2['comentario']=$coment;
                $data2['fecha_validacion']=$fecha_validacion;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                $data2['cargo']=$cargo;
                $data2['semanaid']=$semana;
                $data2['uid']=$uid;
                $data2['dni']=$dni;
                $data2['uid_validacion']=$uid;
                $data2['dni_validacion']=$dni;
                $data['estado_usuario']=$estado;
                $usercoment=$vercoment->_save($data2);
            }
            else
            {
                $vercoment=new Admin_Model_DbTable_Usuariovalidacion();
                $usercoment=$vercoment->_save($data);

                //$duplica=new Admin_Model_DbTable_Tareopersona();   
                //$dupl=$duplica->_getDuplicarTareo($fecha_duplicidad,$semana,$uid);

            }
                $fecha_inicio = $this->_getParam('fecha_calendario');
                $tareopersona = new Admin_Model_DbTable_Tareopersona();
                $data_tareopersona = $tareopersona->_getTareoxPersonaxSemana($uid,$dni,$semana);
                if ($data_tareopersona)
                {
                    $datos_actualizar['estado']='C';
                    $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and estado='A'";
                    $update=$tareopersona -> _update($datos_actualizar,$str_actualizar);
                }
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                $wheres=array('dni'=>$dni,'uid'=>$uid,'semanaid'=>$semana);
                $tareosemana=$sumahorassemana->_getOne($wheres);

                if ($this->sesion->personal->ucataprobacion)
                {
                    $aprobacion = new Admin_Model_DbTable_Aprobacion();
                    $wheres=array('idaprobacion'=>$this->sesion->personal->ucataprobacion,'estado'=>'A');
                    $list_aprobacion=$aprobacion->_getOne($wheres); 
                    $aprobador= $list_aprobacion['idaprobador'];;
                    $datos_actualizar_sumahoras1['estado']=$aprobador;
                    $str_actualizar_sumahoras1="semanaid='$semana' and uid='$uid' and dni='$dni'";
                    $update=$sumahorassemana -> _update($datos_actualizar_sumahoras1,$str_actualizar_sumahoras1);
                }
                else
                {
                    if ($this->sesion->personal->ucatcargo== 'EQUIPO' OR $this->sesion->personal->ucatcargo == 'RESP-EQUIPO-PROY')
                        {
                            if ($tareosemana)
                            {
                                echo "equipo";
                                $datos_actualizar_sumahoras['estado']='P';
                                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni'";
                                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
                            }
                        }
                }

                if ($this->sesion->personal->ucatcargo== 'JEFE' && $this->sesion->personal->ucatareaid=!'27' && $this->sesion->personal->ucatareaid!='16')
                {   
                    if ($tareosemana)
                {   
                    $datos_actualizar_sumahoras1['estado']='J';
                    $str_actualizar_sumahoras1="semanaid='$semana' and uid='$uid' and dni='$dni'";
                    $update=$sumahorassemana -> _update($datos_actualizar_sumahoras1,$str_actualizar_sumahoras1);
                }   
            }
            /*
            if ($this->sesion->personal->ucatcargo== 'GERENTE' )
            {   
                if ($tareosemana)
                {   
                    $datos_actualizar_sumahoras1['estado']='G';
                    $str_actualizar_sumahoras1="semanaid='$semana' and uid='$uid' and dni='$dni'";
                    $update=$sumahorassemana -> _update($datos_actualizar_sumahoras1,$str_actualizar_sumahoras1);
                }   
            }*/

            
        }
        catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function timesheetaprobaciongerenteoperacionesAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
            //$isresponsable=$this->sesion->is_responsable;    
            $isgerente=$this->sesion->is_gerente;    
            if ($isgerente=='S' ) 
            {

                //$equipo = new Admin_Model_DbTable_Equipo();
                //$equipo_aprobacion = $equipo->_getListarEquipoxProyectoxGerenteOperaciones();

                //$this->view->equipos_horas_aprobar= $equipo_aprobacion;    
            }
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

 public function aprobaciongerenteareaAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
            
            //$isresponsable=$this->sesion->is_responsable;    
            $isgerente=$this->sesion->is_gerente;    
            if ($this->sesion->personal->ucatcargo== 'GERENTE-AREA') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
                if ($areaid=='20')
                {
                    $jefes_area = $equipo->_getListarEquipoxGerenteAreaGeotecnia();    
                }
                if ($areaid=='02')
                {
                    $jefes_area = $equipo->_getListarEquipoxGerenteAreaIngenieria();    
                }

             
                $this->view->equipos_horas_aprobar= $jefes_area;    
            }
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }



   

public function semanagerenteareaAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');

            $uid_validacion = $this->sesion->uid;
            $dni_validacion = $this->sesion->dni;
            $this->view->uid_validacion=$uid_validacion;
            $this->view->dni_validacion=$dni_validacion;
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->cargo = $areaid;
            $this->view->uid = $uid;
            $this->view->dni = $dni;
            $ano=date("Y");/*ojo cambiar  con el tiempo --revisar */
            $enero = mktime(1,1,1,1,1,$ano); 
            //$mos = (11-date('w',1))%7-3; 
            $mos = (11-date('w',$enero))%7-3;

            $this->view->mos=$mos;
            $this->view->enero=$enero;
            $this->view->semana = $semana;


            $tareo_persona = new Admin_Model_DbTable_Tareopersona();
            //$semana=date('W', strtotime($fecha_inicio_mod)); 


            $this->view->semana = $semana;
            $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
            $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
            //$data_tareo = $tareo->_getTareoXUid($where);
            $this->view->actividades= $datos_tareopersona;

        }    
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }




    public function guardarcomentariogerenteareaAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $estado = $this->_getParam('estado');
            $uid_validacion=$this->_getParam('uid_validacion');
            $dni_validacion=$this->_getParam('dni_validacion');
            $fecha_validacion=$this->_getParam('fecha');
            
            $time = time();
            $datetime=date("d-m-Y (H:i:s)", $time);
            $fecha_actualizar=$datetime;

            $etapa_validacion=$this->_getParam('etapa');

            $data['cargo']=$cargo;
            $data2['cargo']=$cargo;

            $data['semanaid']=$semana;
            $data2['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data2['uid']=$uid;
            $data2['dni']=$dni;
            $data['uid_validacion']=$uid_validacion;
            $data['dni_validacion']=$dni_validacion;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;
            $data['fecha_validacion']=$fecha_actualizar;
            $data['etapa']=$etapa_validacion;

            $where['uid']=$uid;
            $where['dni']=$dni;
            //$where['uid_validacion']=$uid_validacion;
            //$where['dni_validacion']=$dni_validacion;
            $where['cargo']=$cargo;
            $where['semanaid']=$semana;

//            print_r($data);

            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOne($where))
            {
                echo "existe";
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);

                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_actualizar;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

                print_r($pk1);
            //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                //$usecoment=$coment->_updateX($data2,$pk);
                
                $usecoment=$vercoment->_updateXUsuario($data3,$pk1);}
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                if ( $estado=='R'){
                  $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);
                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='1';    
                }
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
                }



            else
            {echo "no existe";
                $coment=new Admin_Model_DbTable_Usuariovalidacion();
              $usercoment=$coment->_save($data);
                //echo "save";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
               if ( $estado=='R'){
                    $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semanaid' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);
                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='1';    
                }
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
            }

            
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

    public function aprobaciongerenteoperacionesAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->aprobacion=$this->sesion->personal->ucataprobacion;  
            $isgerente=$this->sesion->is_gerente;    
            if ($this->sesion->personal->ucatcargo== 'GERENTE') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
                if ($areaid=='02')
                {
                    $jefes_area = $equipo->_getListarEquipoxGerenteGeneralIngenieria();    
                }
                //echo "gerente operaciones";
                //print_r($jefes_area);
                $this->view->equipos_horas_aprobar= $jefes_area;    
            }
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }




public function semanagerenteoperacionesAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');

            $uid_validacion = $this->sesion->uid;
            $dni_validacion = $this->sesion->dni;
            $this->view->uid_validacion=$uid_validacion;
            $this->view->dni_validacion=$dni_validacion;
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->cargo = $areaid;
            $this->view->uid = $uid;
            $this->view->dni = $dni;
            $ano=date("Y");/*ojo cambiar  con el tiempo --revisar */
            $enero = mktime(1,1,1,1,1,$ano); 
            //$mos = (11-date('w',1))%7-3; 
            $mos = (11-date('w',$enero))%7-3;

            $this->view->mos=$mos;
            $this->view->enero=$enero;
            $this->view->semana = $semana;


            $tareo_persona = new Admin_Model_DbTable_Tareopersona();
            //$semana=date('W', strtotime($fecha_inicio_mod)); 


            $this->view->semana = $semana;
            $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
            $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
            //$data_tareo = $tareo->_getTareoXUid($where);
            $this->view->actividades= $datos_tareopersona;

        }    
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function guardarcomentariogerenteoperacionesAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $estado = $this->_getParam('estado');
            $uid_validacion=$this->_getParam('uid_validacion');
            $dni_validacion=$this->_getParam('dni_validacion');
            $fecha_validacion=$this->_getParam('fecha');
            
            $time = time();
            $datetime=date("d-m-Y (H:i:s)", $time);
            $fecha_actualizar=$datetime;

            $etapa_validacion=$this->_getParam('etapa');

            $data['cargo']=$cargo;
            $data2['cargo']=$cargo;

            $data['semanaid']=$semana;
            $data2['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data2['uid']=$uid;
            $data2['dni']=$dni;
            $data['uid_validacion']=$uid_validacion;
            $data['dni_validacion']=$dni_validacion;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;
            $data['fecha_validacion']=$fecha_actualizar;
            $data['etapa']=$etapa_validacion;

            $where['uid']=$uid;
            $where['dni']=$dni;
            //$where['uid_validacion']=$uid_validacion;
            //$where['dni_validacion']=$dni_validacion;
            $where['cargo']=$cargo;
            $where['semanaid']=$semana;

//            print_r($data);

            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOne($where))
            {
                echo "existe";
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);

                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_actualizar;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

                print_r($pk1);
            //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                //$usecoment=$coment->_updateX($data2,$pk);
                
                $usecoment=$vercoment->_updateXUsuario($data3,$pk1);}
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                if ( $estado=='R'){
                    echo "rechazadodododood";
                  $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);


                            $datos_actualizar_validador['estado_real']='RECHAZADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras3['estado']='0';
                            $str_actualizar_sumahoras3="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras3,$str_actualizar_sumahoras3);


                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='2';    

                            $datos_actualizar_validador['estado_real']='APROBADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni' ";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras2['estado']='2';
                            $str_actualizar_sumahoras2="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras2,$str_actualizar_sumahoras2);

                     
                     

                }
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
                }



            else
            {echo "no existe";
                echo "existe";
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);

                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_actualizar;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

                print_r($pk1);
            //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                //$usecoment=$coment->_updateX($data2,$pk);
                
                $usecoment=$vercoment->_updateXUsuario($data3,$pk1);}
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                if ( $estado=='R'){
                    echo "rechazadodododood";
                  $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);


                            $datos_actualizar_validador['estado_real']='RECHAZADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras3['estado']='0';
                            $str_actualizar_sumahoras3="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras3,$str_actualizar_sumahoras3);


                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='2';    

                            $datos_actualizar_validador['estado_real']='APROBADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni' ";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras2['estado']='2';
                            $str_actualizar_sumahoras2="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras2,$str_actualizar_sumahoras2);

                }
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
            }

            
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }


    public function timesheetsemanagerentehistoricoAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');

            $uid_validacion = $this->sesion->uid;
            $dni_validacion = $this->sesion->dni;
            $this->view->uid_validacion=$uid_validacion;
            $this->view->dni_validacion=$dni_validacion;
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->cargo = $areaid;
            $this->view->uid = $uid;
            $this->view->dni = $dni;
            $ano=date("Y");/*ojo cambiar  con el tiempo --revisar */
            $enero = mktime(1,1,1,1,1,$ano); 
            //$mos = (11-date('w',1))%7-3; 
            $mos = (11-date('w',$enero))%7-3;

            $this->view->mos=$mos;
            $this->view->enero=$enero;
            $this->view->semana = $semana;


            $tareo_persona = new Admin_Model_DbTable_Tareopersona();
            //$semana=date('W', strtotime($fecha_inicio_mod)); 


            $this->view->semana = $semana;
            $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
            $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
            //$data_tareo = $tareo->_getTareoXUid($where);
            $this->view->actividades= $datos_tareopersona;

        }    
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    
    
    
     public function historicogerenteoperacionesAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
            //$isresponsable=$this->sesion->is_responsable;    
           // $isgerente=$this->sesion->is_gerente;    
            if ($this->sesion->personal->ucatcargo== 'GERENTE') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
                
                if ($areaid=='02')
                {
                    $jefes_area = $equipo->_getListarEquipoxGerenteGeneralIngenieria();    
                }
                //echo "gerente operaciones";
                //print_r($jefes_area);
             
                $this->view->equipos_horas_aprobar= $jefes_area;    
            }


            

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }   

    public function aprobaciongerentegeneralAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
                       $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
            
            //$isresponsable=$this->sesion->is_responsable;    
            $isgerente=$this->sesion->is_gerente;    
            if ($this->sesion->personal->ucatcargo== 'GERENTE-GENERAL') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
              
              
                    $jefes_area = $equipo->_getListarEquipoxGerenteGeneral();    
              
                 //   print_r($jefes_area);
             
                $this->view->equipos_horas_aprobar= $jefes_area;    
            }
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }





public function semanagerentegeneralAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');

            $uid_validacion = $this->sesion->uid;
            $dni_validacion = $this->sesion->dni;
            $this->view->uid_validacion=$uid_validacion;
            $this->view->dni_validacion=$dni_validacion;
            $areaid=$this->sesion->personal->ucatareaid;   
            $this->view->cargo = $areaid;
            $this->view->uid = $uid;
            $this->view->dni = $dni;
            $ano=date("Y");/*ojo cambiar  con el tiempo --revisar */
            $enero = mktime(1,1,1,1,1,$ano); 
            //$mos = (11-date('w',1))%7-3; 
            $mos = (11-date('w',$enero))%7-3;

            $this->view->mos=$mos;
            $this->view->enero=$enero;
            $this->view->semana = $semana;


            $tareo_persona = new Admin_Model_DbTable_Tareopersona();
            //$semana=date('W', strtotime($fecha_inicio_mod)); 


            $this->view->semana = $semana;
            $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
            $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
            //$data_tareo = $tareo->_getTareoXUid($where);
            $this->view->actividades= $datos_tareopersona;

        }    
         catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


  public function guardarcomentariogerentegeneralAction(){
        try {
            $this->_helper->layout()->disableLayout();            
            $uid = $this->_getParam('uid');
            $dni = $this->_getParam('dni');
            $cargo = $this->_getParam('cargo');
            $semana = $this->_getParam('semanaid');
            $coment = $this->_getParam('coment');
            $estado = $this->_getParam('estado');
            $uid_validacion=$this->_getParam('uid_validacion');
            $dni_validacion=$this->_getParam('dni_validacion');
            $fecha_validacion=$this->_getParam('fecha');
            
            $time = time();
            $datetime=date("d-m-Y (H:i:s)", $time);
            $fecha_actualizar=$datetime;

            $etapa_validacion=$this->_getParam('etapa');

            $data['cargo']=$cargo;
            $data2['cargo']=$cargo;

            $data['semanaid']=$semana;
            $data2['semanaid']=$semana;
            $data['uid']=$uid;
            $data['dni']=$dni;
            $data2['uid']=$uid;
            $data2['dni']=$dni;
            $data['uid_validacion']=$uid_validacion;
            $data['dni_validacion']=$dni_validacion;
            $data['comentario']=$coment;
            $data['estado_usuario']=$estado;
            $data['fecha_validacion']=$fecha_actualizar;
            $data['etapa']=$etapa_validacion;

            $where['uid']=$uid;
            $where['dni']=$dni;
            //$where['uid_validacion']=$uid_validacion;
            //$where['dni_validacion']=$dni_validacion;
            $where['cargo']=$cargo;
            $where['semanaid']=$semana;

//            print_r($data);

            $vercoment= new Admin_Model_DbTable_Usuariovalidacion();
            if($vcoment=$vercoment->_getOne($where))
            {
                echo "existe";
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);

                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_actualizar;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

                print_r($pk1);
            //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                //$usecoment=$coment->_updateX($data2,$pk);
                
                $usecoment=$vercoment->_updateXUsuario($data3,$pk1);}
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                if ( $estado=='R'){
                    echo "rechazadodododood";
                  $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);


                            $datos_actualizar_validador['estado_real']='RECHAZADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras3['estado']='0';
                            $str_actualizar_sumahoras3="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras3,$str_actualizar_sumahoras3);


                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='2';    

                            $datos_actualizar_validador['estado_real']='APROBADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni' ";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras2['estado']='2';
                            $str_actualizar_sumahoras2="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras2,$str_actualizar_sumahoras2);

                     
                     

                }
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
                }



            else
            {echo "no existe";
                echo "existe";
                $pk = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, );
                
                $count=$vercoment->_getUsuarioxValidacion($semana,$uid,$dni);

                $data2['comentario']=$coment;
                $data2['estado_usuario']=$estado;
                $data2['fecha_validacion']=$fecha_actualizar;
                $data2['etapa']=$etapa_validacion;
                $data2['orden']=count($count)+1;
                $data2['estado']="A";
                
                $data2['uid_validacion']=$uid_validacion;
                $data2['dni_validacion']=$dni_validacion;
                $usercoment=$vercoment->_save($data2);
                if ($usercoment){

                $pk1 = array('dni' => $dni  ,'uid' => $uid,'cargo' => $cargo ,'semanaid' => $semana, 'orden' => count($count));
                $data3['estado']="C";

                print_r($pk1);
            //    $coment=new Admin_Model_DbTable_Usuariovalidacion();
                //$usecoment=$coment->_updateX($data2,$pk);
                
                $usecoment=$vercoment->_updateXUsuario($data3,$pk1);}
                //echo "update";
                $sumahorassemana = new Admin_Model_DbTable_Sumahorasemana();
                if ( $estado=='R'){
                    echo "rechazadodododood";
                  $datos_actualizar_sumahoras['estado']='O';   
                        $tareopersona = new Admin_Model_DbTable_Tareopersona();
                        $datos_actualizar['estado']='A';
                        $str_actualizar="semanaid='$semana' and uid='$uid' and dni='$dni' and   estado='C' ";
                        $update_tareopersona=$tareopersona -> _update($datos_actualizar,$str_actualizar);


                            $datos_actualizar_validador['estado_real']='RECHAZADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras3['estado']='0';
                            $str_actualizar_sumahoras3="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras3,$str_actualizar_sumahoras3);


                }
                if ( $estado=='B'){
                    $datos_actualizar_sumahoras['estado']='2';    

                            $datos_actualizar_validador['estado_real']='APROBADO';
                            $str_actualizar_validador="semanaid='$semana' and uid='$uid' and dni='$dni' ";
                            $update_validador=$sumahorassemana -> _update($datos_actualizar_validador,$str_actualizar_validador);
                            $datos_actualizar_sumahoras2['estado']='2';
                            $str_actualizar_sumahoras2="semanaid='$semana' and uid='$uid' and dni='$dni'";
                            $update=$sumahorassemana -> _update($datos_actualizar_sumahoras2,$str_actualizar_sumahoras2);

                }
                
                $str_actualizar_sumahoras="semanaid='$semana' and uid='$uid' and dni='$dni' 
                ";
                $update=$sumahorassemana -> _update($datos_actualizar_sumahoras,$str_actualizar_sumahoras);
            }

            
        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }


     public function historicogerentegeneralAction(){
        try {
           // $this->_helper->layout()->disableLayout();       
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;     
            
            $fecha = date("Y-m-d");
            $semanaid=date('W', strtotime($fecha)); 
            $this->view->semanaid= $semanaid;    

            $areaid=$this->sesion->personal->ucatareaid;   
            //$isresponsable=$this->sesion->is_responsable;    
           // $isgerente=$this->sesion->is_gerente;    

              if ($this->sesion->personal->ucatcargo== 'GERENTE-GENERAL') 
            {
                $equipo = new Admin_Model_DbTable_Equipo();
              
              
                    $jefes_area = $equipo->_getListarEquipoxGerenteGeneral();    
              
                 //   print_r($jefes_area);
             
                $this->view->equipos_horas_aprobar= $jefes_area;    
            }           
           

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }


   public function updatecomentAction(){
        try {

        $this->_helper->layout()->disableLayout();
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
      
        $comentario = $this->_getParam('comentario');
        $codigo_prop_proy = $this->_getParam('codigo_prop_proy');
        $codigo_actividad = $this->_getParam('codigo_actividad');
        $actividadid = $this->_getParam('actividadid');
        $revision = $this->_getParam('revision');
        $actividad_padre = $this->_getParam('actividad_padre');
        $semanaid=$this->_getParam('semanaid');
        $fecha_tarea= $this->_getParam('fecha_tarea');
        $cargo= $this->_getParam('cargo');
        $tipo_actividad=$this->_getParam('tipo_actividad');
        $etapa=$this->_getParam('etapa');
        $actividad_generalid=$this->_getParam('actividad_generalid');
        $hora=$this->_getParam('hora');
        $proyectoid = $this->_getParam('proyectoid');
        $fecha_planificacion=$this->_getParam('fecha_planificacion');      
      

        $wheres=array('codigo_prop_proy'=>$codigo_prop_proy,'codigo_actividad'=>$codigo_actividad,
                'actividadid'=>$actividadid,'revision'=>$revision,
                'actividad_padre'=>$actividad_padre,'proyectoid'=>$proyectoid,'semanaid'=>$semanaid 
                ,'fecha_tarea'=>$fecha_tarea,'uid'=>$uid,'dni'=>$dni,'cargo'=>$cargo
                ,'tipo_actividad'=>$tipo_actividad,'etapa'=>$etapa,'fecha_planificacion'=>$fecha_tarea);

        print_r($wheres);//exit();
        //break;

        $verdata = new Admin_Model_DbTable_Tareopersona();       

        $datos_actualizar['comentario']=$comentario;
           
        $update=$verdata -> _updateX($datos_actualizar,$wheres);

        if($update)
        {
            echo "guardo";
        }

        }
        catch (Exception $e) {
                print "Error: ".$e->getMessage();
        }
    }

    public function copiartareoAction(){
    try {

            $this->_helper->layout()->disableLayout();            
            $uid = $this->sesion->uid;
                  
            $semana = $this->_getParam('semanaid');      
           
            $fechaduplica=$this->_getParam('fecha_calendario');

            $duplica=new Admin_Model_DbTable_Tareopersona();   
            $dupl=$duplica->_getDuplicarTareo($fechaduplica,$semana,$uid);
    }
    catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }


    public function traercomentAction(){
         try {

            $this->_helper->layout()->disableLayout();
            $uid = $this->sesion->uid;
            $dni = $this->sesion->dni;
            $semana = $this->_getParam('semanaid');

            $respuesta = [];
            $i = 0;

            $tareopersona = new Admin_Model_DbTable_Tareopersona();
            $wheres=array('dni'=>$dni,'uid'=>$uid,'semanaid'=>$semana);                
            $tpercoment = $tareopersona->_getFilter($wheres);

            if ($tpercoment) {
                foreach ($tpercoment as $value) {              
                    if(trim($value['comentario'])!='' and trim($value['h_real'])!=''){
                        $fila = [];             
                        $wherep=array('codigo_prop_proy'=>$value['codigo_prop_proy'],'proyectoid'=>$value['proyectoid']); 
                        $nompro = new Admin_Model_DbTable_Proyecto();
                        $npro = $nompro->_getOne($wherep); 

                        $wherea=array('codigo_prop_proy'=>$value['codigo_prop_proy'],'codigo_actividad'=>$value['codigo_actividad'],'actividadid'=>$value['actividadid'],'revision'=>$value['revision']);                
                        $nomact = new Admin_Model_DbTable_Actividad();
                        $noma=$nomact->_getOne($wherea);
                        $nombrepro = $value['proyectoid'].' '.$npro['nombre_proyecto'];
                        $fila['fecha_tarea'] = $value['fecha_tarea'];
                        $fila['proyecto'] = $nombrepro;
                        $fila['actividad'] = $noma['nombre'];
                        $fila['h_real'] = $value['h_real'];
                        $fila['comentario'] = $value['comentario'];

                        $respuesta[$i] = $fila;

                        $i++;
                    }
                }
            }

            $this->_helper->json->sendJson($respuesta);
                                    

         }
         catch (Exception $e) {
                 print "Error: ".$e->getMessage();
         }
    }

    public function imprimirtareoAction(){

        try {

        $this->_helper->layout()->disableLayout();
        $fecha_inicio = $this->_getParam('fecha');
        $fecha_inicio_mod = date("Y-m-d", strtotime($fecha_inicio));
        $fecha_inicio = date("d-m-Y", strtotime($fecha_inicio));
        $fecha_mostrar = date("d", strtotime($fecha_inicio));

        $this->view->fecha = $fecha_inicio;
        $this->view->fecha_mostrar = $fecha_mostrar;
        $uid = $this->sesion->uid;
        $dni = $this->sesion->dni;
        $this->view->fecha_inicio_mod = $fecha_inicio_mod;
        $this->view->uid = $uid;
        $this->view->dni = $dni;
        $categoriaid=$this->sesion->personal->ucatid;
        $areaid=$this->sesion->personal->ucatareaid;
        $cargo=$this->sesion->personal->ucatcargo;
        $this->view->aprobacion = $this->sesion->personal->ucataprobacion;
        $this->view->cargo=$cargo;
        $this->view->areaid=$areaid;
        $tareo_persona = new Admin_Model_DbTable_Tareopersona();
        $semana=date('W', strtotime($fecha_inicio_mod)); 
        $this->view->semana = $semana;
        $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemana($uid,$dni,$semana);
        $datos_tareopersona_NB=$tareo_persona->_getTareoxPersonaxSemanaxNB($uid,$dni,$semana);
        $wheres_hojatiempo_empleado=array('uid'=>$uid,'dni'=>$dni,'semanaid'=>$semana);
        $estado_hojatiempo=$tareo_persona->_getEstado_HojaTiempo($semana,$uid,$dni);
        //print_r($wheres_hojatiempo_empleado);
        if ($estado_hojatiempo)
        {
            $this->view->estado_hoja_tiempo= $estado_hojatiempo;    
           
        }
        //print_r($estado_hojatiempo);

        //$data_tareo = $tareo->_getTareoXUid($where);
        $this->view->actividades= $datos_tareopersona;
        // print_r($this->sesion->is_gerente);
        $this->view->is_gerente=$this->sesion->is_gerente;
        
        $this->view->actividades_NB = $datos_tareopersona_NB;

        $buscar_aprobador=$this->sesion->personal->ucataprobacion;
        $aprobacion = new Admin_Model_DbTable_Aprobacion();
        
        $wheres_filtro1=array('idaprobacion'=>$buscar_aprobador,'estado_filtro1'=>'A');
        $list_aprobacion_filtro1=$aprobacion->_getOnefiltro1($wheres_filtro1); 
        if ($list_aprobacion_filtro1)
        {
            $aprobador_filtro1= $list_aprobacion_filtro1['idaprobador_filtro1'];  
        }

        $wheres_filtro2=array('idaprobacion'=>$buscar_aprobador,'estado_filtro2'=>'A');
        $list_aprobacion_filtro2=$aprobacion->_getOnefiltro2($wheres_filtro2); 

        if ($list_aprobacion_filtro2)
        {
            $idaprobador_filtro2= $list_aprobacion_filtro2['idaprobador_filtro2'];  
            $usuario_cat = new Admin_Model_DbTable_Usuariocategoria();
            
            $wheres_ucat=array('aprobacion'=>$idaprobador_filtro2,'estado'=>'A');
            $list_aprobador=$usuario_cat->_getAprobadorxEmpleado($wheres_ucat);  


            
            if ($list_aprobador)
            {
                $aprobador_usuario = explode(".", $list_aprobador['uid']);
                $this->view->aprobador_filtro2=ucwords($aprobador_usuario[0])." ".ucwords($aprobador_usuario[1]);
            }
        }

        $planificacion = new Admin_Model_DbTable_Planificacion();
        $proyectos=$planificacion->_getOnexSemanaxGerenteProyecto($semana,$uid,$dni,$areaid);
        $k=0;
        $gerentes_proyectos=array();
        
        foreach ($proyectos as $datos) {
          $porciones = explode(".", $datos['uid']);
          $gerentes_proyectos[$k]=ucwords($porciones[0])." ".ucwords($porciones[1]);
          $k++;
        }
        $this->view->gerentes_proyectos=$gerentes_proyectos;

        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        } 

        // $semana = $this->_getParam('semanaid');
        // $uid = $this->_getParam('uid');
        // $dni = $this->_getParam('dni');
        // $tareo_persona = new Admin_Model_DbTable_Tareopersona(); 
        // $datos_tareopersona=$tareo_persona->_getTareoxPersonaxSemanaimrprmir($uid,$dni,$semana); 

        // $this->view->tareo= $datos_tareopersona;  

        //print_r($datos_tareopersona);
        //exit();
    }

};