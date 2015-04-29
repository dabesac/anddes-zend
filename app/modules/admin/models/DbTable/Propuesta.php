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

    public function _buscarPropuesta($propuesta)
     {
        try{
            $sql=$this->_db->query("
               select * from propuesta where lower(nombre_propuesta) like '%$propuesta%'
               order by propuestaid desc

            ");
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



}