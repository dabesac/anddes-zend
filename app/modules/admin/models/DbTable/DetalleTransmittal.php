<?php
class Admin_Model_DbTable_DetalleTransmittal extends Zend_Db_Table_Abstract
{
    protected $_name = 'detalle_transmittal';
    protected $_primary = array("detalleid");

    public function _getDetallexTramittal($transmittalid)
    {
      $id = (int)$transmittalid;
      $rows = $this->fetchAll('transmittalid = ' . $id);
      if (!$rows) {
           throw new Exception("No hay resultados para ese transmittal");
      }
      return $rows->toArray();
    }

    public function _addDetalle($data)
    {
      $newRow = $this->createRow();
      $newRow->transmittal = $data['transmittal'];
      $newRow->correlativo = $data['correlativo'];
      $newRow->entregableid = $data['entregableid'];
      $newRow->proyectoid = $data['proyectoid'];
      $newRow->revision = $data['revision'];
      $newRow->estado_revision = $data['estado_revision'];
      $newRow->emitido = $data['emitido'];
      $newRow->fecha = $data['fecha'];
      $newRow->save();
    }

    public function _updateDetalle($data)
    {
      $id = (int)$data['detalleid'];
      $row = $this->fetchRow('detalleid = ' . $id);
      if (!$row) {
           throw new Exception("No hay resultados para ese transmittal");
      }
      $row->revision = '0';
      $row->save();
    }

    public function _deleteDetalle($detalleid)
    {
      $row = $this->fetchRow('detalleid = ' . $detalleid);
      if (!$row) {
           throw new Exception("No hay resultados para ese transmittal");
      }
      $row->delete();
    }

}