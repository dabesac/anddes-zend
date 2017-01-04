<?php
class Admin_Model_DbTable_Equipo extends Zend_Db_Table_Abstract
{
    protected $_name = 'equipo';
    protected $_primary = array("codigo_prop_proy","proyectoid","categoriaid","uid","dni","areaid","cargo");

     /* Lista toda las Personas */
    public function _getEquipoAll(){
        try{
            $f = $this->fetchAll();
            if ($f) return $f->toArray ();
            return false;
        }catch (Exception $e){
            print "Error: Al momento de leer todas las personas".$e->getMessage();
        }
    }

    public function _getProyectosXuidXEstado($uid,$estado)
     {
        try{
            $sql=$this->_db->query("
               select * from equipo e inner join proyecto p
               on e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid = p.proyectoid
               where e.uid = '$uid' and e.estado = '$estado'");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

    public function _getProyectosXuidXEstadoXnivelXcategoria($uid,$categoriaid,$nivel,$estado)
     {
        try{
            $sql=$this->_db->query("
               select * from equipo e inner join proyecto p
               on e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid = p.proyectoid
               where e.uid='$uid' and e.estado = '$estado' and e.categoriaid = '$categoriaid'
               and nivel = '$nivel' order by e.proyectoid");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

/*
select p.codigo_prop_proy, p.proyectoid, e.estado, e.categoriaid, e.cargo, e.areaid, pro.propuestaid, pro.revision, p.nombre_proyecto,
                p.tipo_proyecto, pro.moneda, pro.descripcion
                from equipo e inner join proyecto p
                on e.codigo_prop_proy = p.codigo_prop_proy
                inner join propuesta pro on
                p.codigo_prop_proy=pro.codigo_prop_proy
                where e.uid = '$uid' and e.estado = '$estado' and pro.clienteid='$clienteid' and pro.unidad_mineraid='$unidad_mineraid'
*/



    public function _getProyectosXuidXEstadoXnivel($where=null){
        try{
            if ($where['uid']=='' || $where['dni']=='' || $where['estado']=='' || $where['nivel']=='') return false;
            $wherestr="uid = '".$where['uid']."' and dni = '".$where['dni']."' and estado = '".$where['estado']."' and nivel = '".$where['nivel']."'";
            $row = $this->fetchRow($wherestr);
            if($row) return $row->toArray();
            return false;
            } catch (Exception $ex){
            print $ex->getMessage();
        }
    }



public function _getProyectosxUidXEstadoxCliente($uid,$estado,$clienteid,$unidad_mineraid)
     {
        try{
            $sql=$this->_db->query("

                select e.codigo_prop_proy, e.proyectoid,e.estado,e.uid,e.dni,e.categoriaid,e.areaid,e.cargo,e.nivel,p.propuestaid, p.revision, p.nombre_proyecto, p.gerente_proyecto, p.control_documentario, p.control_proyecto,
                p.tipo_proyecto,p.clienteid,p.unidad_mineraid

                from equipo e inner join proyecto p
                on e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid=p.proyectoid
                where e.uid = '$uid' and e.estado = '$estado' and
                p.clienteid='$clienteid' and
                p.unidad_mineraid='$unidad_mineraid'
                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

/*Nuevo Para Listar Clientes con sus Proyectos sin repetir el nommbre

select e.codigo_prop_proy, e.proyectoid,e.estado,e.uid,e.dni,e.categoriaid,e.areaid,e.cargo,e.nivel,p.propuestaid, p.revision, p.nombre_proyecto, p.gerente_proyecto, p.control_documentario, p.control_proyecto,
                p.tipo_proyecto,p.clienteid,p.unidad_mineraid

                from equipo e inner join proyecto p
                on e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid=p.proyectoid
                where e.uid = '$uid' and e.dni='$dni' and e.estado = '$estado' and
                p.clienteid='$clienteid' order by e.proyectoid

*/
    public function _getProyectosxEquipoxUsuarioXEstadoxCliente($uid,$dni,$estado,$clienteid)
     {
        try{
            $sql=$this->_db->query("



                select  distinct e.codigo_prop_proy, e.proyectoid,e.estado,e.uid,e.dni,e.categoriaid,p.propuestaid, p.revision, p.nombre_proyecto, p.gerente_proyecto, p.control_documentario, p.control_proyecto,
                p.tipo_proyecto,p.clienteid,p.unidad_mineraid

                from equipo e inner join proyecto p
                on e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid=p.proyectoid
                where e.uid = '$uid' and e.dni='$dni' and e.estado = '$estado' and p.estado='$estado' and
                p.clienteid='$clienteid' order by e.proyectoid


                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }



public function _getProyectosAnddes()
     {
        try{
            $sql=$this->_db->query("

                select *

                from  proyecto
                where clienteid='20451530535' and unidad_mineraid='10'
                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


/*  consulta anterior de la muestra d eclientes
            select
               c.clienteid,c.nombre_comercial,c.nombre as nombre_cliente,u.unidad_mineraid,
               u.nombre as nombre_unidad, p.nombre_propuesta, p.tipo_servicio
               from equipo e inner join propuesta p
               on e.codigo_prop_proy = p.codigo_prop_proy
               inner join unidad_minera u on
               p.clienteid=u.clienteid and p.unidad_mineraid=u.unidad_mineraid
               inner join cliente c on
               u.clienteid=c.clienteid
               where e.uid = '$uid' and e.estado = '$estado'*/


    public function _getClienteXuidXEstado($uid,$estado)
     {
        try{
            $sql=$this->_db->query("
                select distinct (p.clienteid), c.nombre_comercial, p.unidad_mineraid
                   from equipo e inner join proyecto p
                ON e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid=p.proyectoid
                inner join cliente c on
                p.clienteid=c.clienteid
                where e.uid = '$uid' and e.estado = '$estado' and not unidad_mineraid='10'
                order by c.nombre_comercial
               ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }
/*Nuevo Para Listar Clientes con sus Proyectos sin repetir el nommbre*/
    public function _getClienteXEquipoXUsuario($uid,$dni,$estado)
     {
        try{
            $sql=$this->_db->query("
                select distinct (p.clienteid), c.nombre_comercial
                   from equipo e inner join proyecto p
                ON e.codigo_prop_proy = p.codigo_prop_proy and e.proyectoid=p.proyectoid
                inner join cliente c on
                p.clienteid=c.clienteid
                where e.uid = '$uid' and e.dni='$dni' and p.estado='$estado' and e.estado = '$estado' order by c.nombre_comercial
               ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }



    public function _getFilter($where=null,$attrib=null,$orders=null){
        try{
            //if($where['codigo_prop_proy']=='' || $where['proyectoid']=='' || $where['categoriaid']=='') return false;
                $select = $this->_db->select();
                if ($attrib=='') $select->from("equipo");
                else $select->from("equipo",$attrib);
                foreach ($where as $atri=>$value){
                    $select->where("$atri = ?", $value);
                }
                if ($orders<>null || $orders<>"") {
                    if (is_array($orders))
                        $select->order($orders);
                }
                $results = $select->query();
                $rows = $results->fetchAll();
                if ($rows) return $rows;
                return false;
        }catch (Exception $ex){
            print $ex->getMessage();

        }
    }

     public function _buscarEquipoxProyectoxArea($codigo,$proyectoid,$areaid)
     {
        try{
            $sql=$this->_db->query("
               select *  from equipo
               where codigo_prop_proy='$codigo' and proyectoid='$proyectoid' and areaid='$areaid'
               and not dni in ('00000000')
               and estado='A'
            ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

     public function _buscarEquipoxProyectoxAreaxCategoria($codigo,$proyectoid,$areaid,$categoriaid)
     {
        try{
            $sql=$this->_db->query("
               select * from equipo
               where codigo_prop_proy='$codigo' and proyectoid='$proyectoid' and areaid='$areaid'
               and categoriaid='$categoriaid'
            ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


        public function _save($data)
    {
        try{
            if ($data['areaid']=='' ||  $data['dni']=='' ) return false;
            return $this->insert($data);
            return false;
        }catch (Exception $e){
                //print "Error: Registration ".$e->getMessage();
        }
    }

public function _getDatosxProyectoxUidXEstadoxCliente($uid,$dni,$estado,$codigo_prop_proy,$proyectoid)
     {
        try{
            $sql=$this->_db->query("

                select *
                from equipo
                where uid = '$uid' and estado = '$estado' and
                codigo_prop_proy='$codigo_prop_proy' and
                proyectoid='$proyectoid' and dni='$dni'
                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }



public function _getListarNivel4xNivel3($uid,$dni,$nivel_inferior,$nivel_superior,$areaid)
     {
        try{
            $sql=$this->_db->query("



                select distinct uid, dni from equipo where proyectoid in  (select proyectoid from equipo
                where uid='$uid' and nivel='$nivel_superior' and areaid='$areaid') and nivel='$nivel_inferior' and areaid='$areaid'

                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

/*

                select distinct uid,dni from equipo where proyectoid in
                (select distinct proyectoid from
                 equipo  where uid='$uid' and dni='$dni' and nivel='0')
                  and nivel in ('4','2','1','3')
*/
public function _getListarEquipoxProyectoxGerente($uid,$dni)
     {
        try{
            $sql=$this->_db->query("


                  select distinct uid,dni from planificacion where proyectoid in
                (select distinct proyectoid from
                 equipo   where uid='$uid' and dni='$dni'  and nivel='0') and h_totaldia is not null



                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


 public function _buscarGerentexProyecto($proyectoid)
     {
        try{
            $sql=$this->_db->query("
                select * from equipo
               where proyectoid='$proyectoid' and nivel='0' and cargo='GER-PROY'

            ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

     public function _getListarEquipoArea($areaid)
     {
        try{
            $sql=$this->_db->query("
               select  distinct uid, dni
                from equipo where areaid='$areaid' and nivel='4'
                and not dni='10664960'

            ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


    public function _getListarEquipoxGerenteAreaGeotecnia()
     {
        try{
            $sql=$this->_db->query("

                select * from usuario_categoria where cargo='JEFE' and areaid in('22','21')


                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


    public function _getListarEquipoxGerenteAreaIngenieria()
     {
        try{
            $sql=$this->_db->query("

               select * from usuario_categoria where cargo='JEFE' and areaid in('10','02','22')

                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

        public function _getListarEquipoxGerenteGeneralIngenieria()
     {
        try{
            $sql=$this->_db->query("

               select * from usuario_categoria where cargo='GERENTE-AREA' and areaid in('10','02','20') or uid in ('romy.valdivia','jorge.alvarez','daniel.ttito')

                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


    public function _getListarEquipoxGerenteGeneral()
    {
        try{
            $sql=$this->_db->query("
                    select * from usuario_categoria where cargo='GERENTE'
                ");
            $row=$sql->fetchAll();
            return $row;
            }
            catch (Exception $ex){
            print $ex->getMessage();
        }
    }

    public function _getUsuarioxProyecto($where=null){
        try{
            if ($where['uid']=='' || $where['dni']=='' ) return false;
            $wherestr="
                uid = '".$where['uid']."' and dni = '".$where['dni']."'
                and codigo_prop_proy = '".$where['codigo_prop_proy']."'
                and proyectoid = '".$where['proyectoid']."'
                and areaid = '".$where['areaid']."' and estado='A'
                ";
            $row = $this->fetchRow($wherestr);
            if($row) return $row->toArray();
            return false;
            } catch (Exception $ex){
            print $ex->getMessage();
        }
    }

    public function _update($data,$pk)
    {
        try{
            if ($pk['codigo_prop_proy']=='' ||  $pk['proyectoid']=='' ) return false;
            $where = "codigo_prop_proy = '".$pk['codigo_prop_proy']."' and proyectoid='".$pk['proyectoid']."'
                and  uid = '".$pk['uid']."' and dni = '".$pk['dni']."'
                 and areaid = '".$pk['areaid']."'
             ";
            return $this->update($data, $where);
            return false;
        }catch (Exception $e){
            print "Error: Update Distribution".$e->getMessage();
        }
    }

    public function _getOne($where=array()){
        try {
                //if ($where["dni"]=='') return false;
                $wherestr= "codigo_prop_proy = '".$where['codigo_prop_proy']."'
                            and proyectoid='".$where['proyectoid']."'
                            and uid='".$where['uid']."' and dni='".$where['dni']."'  and areaid='".$where['areaid']."'
                             ";

                //print_r($wherestr);

                $row = $this->fetchRow($wherestr);

                if($row) return $row->toArray();
                return false;
        } catch (Exception $e) {
            print "Error: Read One Condition".$e->getMessage();
        }
    }

    public function _getListarEquipoxProyectoxGerentexAprobacion($uid,$dni)
     {
        try{
            $sql=$this->_db->query("

                    select distinct uid,dni
                from planificacion where proyectoid in
                (select distinct proyectoid from
                equipo   where uid='$uid' and dni='$dni'  and nivel='0')
                union
                select uid,dni from usuario_categoria where aprobacion like '4.B1.%';


                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }





    //Metodo para obtener a los usuarios por proyecto
    public function _getUsuarioxProyectoxEstadoxNivel($proyecto, $estado, $nivel)
    {
        try{
            $sql=$this->_db->query("select uid, dni from equipo where codigo_prop_proy='".$proyecto."' and estado='".$estado."' and nivel='".$nivel."'");
            $row=$sql->fetchAll();
            return $row;
            }
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


public function _getDatosxEquipoxProyecto($uid,$dni,$codigo_prop_proy,$proyectoid)

     {
        try{
            $sql=$this->_db->query("

                select *
                from equipo
                where uid = '$uid'  and
                codigo_prop_proy='$codigo_prop_proy' and
                proyectoid='$proyectoid' and dni='$dni'
                    ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

    public function _getDisciplinasxProyecto($proyectoid)
    {
      try {
        $sql = $this->_db->query("select distinct e.areaid, a.nombre
        from equipo as e inner join area as a
        on e.areaid = a.areaid
        where e.proyectoid='".$proyectoid."'");
        $rows = $sql->fetchAll();
        return $rows;
      } catch (Exception $e) {
        $e->getMessage();
      }

    }

    public function _getTareoxProyectoxhoras($proyectoid,$uid)
     {
        try{
            $sql=$this->_db->query("
                select sum(cast((case when h_real='' or h_real is null then '0' else h_real end) as double precision)) from tareo_persona where proyectoid='$proyectoid' and uid='$uid'
            ");
            $row=$sql->fetchAll();
            return $row;
            }

           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


}
