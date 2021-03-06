<?php 
class Admin_Model_DbTable_Propuesta extends Zend_Db_Table_Abstract
{
    protected $_name = 'propuesta';
    protected $_primary = array("codigo_prop_proy", "propuestaid", "revision");

     /* Lista toda las Personas */    
    public function _getPropuestaAll(){
        try{
            $f = $this->fetchAll();

            if ($f) return $f->toArray ();
            return false;
        }catch (Exception $e){
            print "Error: Al momento de leer todas las personas".$e->getMessage();
        }
    }
    
     public function _getPropuestaxIndices($codigo,$propuestaid,$revision)
     {
        try{
            $sql=$this->_db->query("
               select * from propuesta 
               where propuestaid='$propuestaid' and revision='$revision' and codigo_prop_proy='$codigo' 

            ");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


    public function _getPropuestaxnoproyectxganado()
     {
        try{
            $sql=$this->_db->query("
               select * from propuesta 
               where estado_propuesta='G' and  isproyecto!='S' 

            ");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }



    public function _getPropuestaAllOrdenadoxEstadoPropuesta($estado_propuesta)
     {
        try{
            $sql=$this->_db->query("
               select * from propuesta where estado_propuesta='$estado_propuesta'
               order by propuestaid desc

            ");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


     public function _getPropuestaAllOrdenadoOrdenEstado(){
        try{
            $sql=$this->_db->query("
                select * from propuesta as pro inner join cliente as cli on
                pro.clienteid=cli.clienteid 
                order by pro.orden_estado asc");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }
    
    
     public function _buscarPropuesta($propuesta){
        try{
            $sql=$this->_db->query("
select * from propuesta as pro inner join cliente as cli on
                pro.clienteid=cli.clienteid where lower(pro.nombre_propuesta) like '%$propuesta%' 
                or lower(cli.nombre_comercial) like '%$propuesta%'
                order by pro.orden_estado asc");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }

     public function _buscarPropuestaxPropuestaid($propuestaid){
        try{
            $sql=$this->_db->query("
                select * from propuesta as pro inner join cliente as cli on
                pro.clienteid=cli.clienteid where pro.propuestaid like '%$propuestaid%' 
                and pro.estado_propuesta='EE'
                ");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


    public function _getFilter($propuestaid){
        try{
            $sql=$this->_db->query("
               select * from propuesta 
               where propuestaid='$propuestaid'  ");
            $row=$sql->fetchAll();
            return $row;           
            }  
            
           catch (Exception $ex){
            print $ex->getMessage();
        }
    }


    public function _update($data,$str=''){
        try{
            if ($str=="") return false;
            return $this->update($data,$str);
        }catch (Exception $ex){
            print "Error: Actualizando un registro de Propuesta".$ex->getMessage();
        }
    }

    public function _updateX($data,$pk)
    {
        try{
            if ($pk['codigo_prop_proy']=='' ||  $pk['propuestaid']=='' ||  $pk['revision']=='' ) return false;
            $where = "codigo_prop_proy = '".$pk['codigo_prop_proy']."' and propuestaid='".$pk['propuestaid']."' and revision='".$pk['revision']."' ";
            return $this->update($data, $where);
            return false;
        }catch (Exception $e){
            print "Error: Update Distribution".$e->getMessage();
        }
    }

        public function _save($data)
    {
        try{
            if ($data['codigo_prop_proy']=='' ||  $data['codigo_prop_proy']=='' ) return false;
            return $this->insert($data);
            return false;
        }catch (Exception $e){
                print "Error: Registration ".$e->getMessage();
        }
    }

    public function _getOne($where=array()){
        try{           
            if ($where['codigo_prop_proy']=='' || $where['propuestaid']=='' || $where['revision']=='') return false;
            $wherestr="codigo_prop_proy = '".$where['codigo_prop_proy']."' and propuestaid = '".$where['propuestaid']."' and revision = '".$where['revision']."'  ";
            $row = $this->fetchRow($wherestr);
            if($row) return $row->toArray();
            return false;
        }catch (Exception $e){
            print "Error: Read One Add_reportacad_adm ".$e->getMessage();
        }
    }

    


}