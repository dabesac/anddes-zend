<?php 
class Admin_Model_DbTable_Hojaresumen extends Zend_Db_Table_Abstract
{
    protected $_name = 'hoja_resumen';
    protected $_primary =  array("codigo_prop_proy","proyectoid", "revision_hojaresumen", "propuestaid", "revision_propuesta");
   

     /* Lista toda las Personas */    
    public function _getOne($pk=null)
    {
        try{
            if ($pk['codigo_prop_proy']=='' ||  $pk['proyectoid']==''  ||  $pk['revision_hojaresumen']==''  ||  $pk['propuestaid']==''  ||  $pk['revision_propuesta']=='' ) return false;
            $where = "codigo_prop_proy = '".$pk['codigo_prop_proy']."' and proyectoid='".$pk['proyectoid']."' and revision_hojaresumen='".$pk['revision_hojaresumen']."' and propuestaid='".$pk['propuestaid']."' and revision_propuesta='".$pk['revision_propuesta']."' ";
            $row = $this->fetchRow($where);
            if ($row) return $row->toArray();
            return false;
        }catch (Exception $ex){
            print "Error: Get Info Distribution ".$ex->getMessage();
        }
    }

     /* Lista toda las Personas */
    public function _getFilter($where=null,$attrib=null,$orders=null){
        try{
            //if($where['eid']=='' || $where['oid']=='') return false;
                $select = $this->_db->select();
                if ($attrib=='') $select->from("hoja_resumen");
                else $select->from("hoja_resumen",$attrib);
                foreach ($where as $atri=>$value){
                    $select->where("$atri = ?", $value);                    
                }
                if ($orders<>null || $orders<>"") {
                    if (is_array($orders))
                        $select->order($orders);
                }
                $results = $select->query();
                $rows = $results->fetchAll();
                //print_r($results);
                if ($rows) return $rows;
                return false;
        }catch (Exception $e){
            print "Error: Read Filter competencia ".$e->getMessage();
        }
    }

    public function _save($data)
    {
        try{
            if ($data['codigo_prop_proy']=='' ||  $data['codigo_prop_proy']=='' ||  $data['revision_hojaresumen']==''  ||  $data['propuestaid']==''  ||  $data['revision_propuesta']=='') return false;
            return $this->insert($data);
            return false;
        }catch (Exception $e){
                print "Error: Registration ".$e->getMessage();
        }
    }
 
    public function _update($data,$pk)
    {
        try{
            if ($pk['codigo_prop_proy']=='' ||  $pk['proyectoid']=='' ) return false;
            $where = "codigo_prop_proy = '".$pk['codigo_prop_proy']."' and proyectoid='".$pk['proyectoid']."' ";
            return $this->update($data, $where);
            return false;
        }catch (Exception $e){
            print "Error: Update Distribution".$e->getMessage();
        }
    }


    public function _buscarProyectodetalles($proyectoid,$codigo_prop_proy,$propuestaid,$revision){
        try{
            $sql=$this->_db->query("
                select 
                pro.codigo_prop_proy,pro.proyectoid,pro.propuestaid,pro.revision,pro.nombre_proyecto,pro.descripcion,
               hr.gerente_proyecto,hr.control_proyecto,hr.control_documentario,hr.estado_hojaresumen,hr.acta_conformidad,
               hr.tipo_contrato,hr.forma_pago,hr.tipo_pago,hr.fecha_pago,hr.adelanto,hr.fecha_inicio_planificado,hr.fecha_fin_planificado,
               hr.fecha_inicio_real,hr.fecha_fin_real,hr.comentarios,hr.revision_hojaresumen,hr.observacion,hr.jefe_proyecto1,hr.jefe_proyecto2,
               cli.clienteid,cli.nombre_comercial,cli.direccion,cli.ruc,cli.tipo_cliente,
               uni.unidad_mineraid,uni.nombre,uni.direccion

                from hoja_resumen as hr
                inner join proyecto as pro
                on hr.codigo_prop_proy=pro.codigo_prop_proy
                inner join unidad_minera as uni
                on uni.unidad_mineraid= pro.unidad_mineraid
                inner join cliente as cli
                on cli.clienteid=pro.clienteid
                --inner join propuesta as propu
                --on pro.codigo_prop_proy=propu.codigo_prop_proy and pro.propuestaid=propu.propuestaid and  pro.revision=propu.revision 
                where hr.codigo_prop_proy='$codigo_prop_proy' and hr.proyectoid='$proyectoid' and hr.propuestaid='$propuestaid' and hr.revision_propuesta='$revision'
               
                ");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


}

