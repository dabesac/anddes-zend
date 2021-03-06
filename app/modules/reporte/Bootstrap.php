<?php

class Reporte_Bootstrap extends Zend_Application_Module_Bootstrap
{

   protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Reporte_',
            'basePath'  => APPLICATION_PATH .'/modules/reporte',
            'resourceTypes' => array (
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form',
                ),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model',
                ),
                'datatable' => array(
                    'path' => 'datatables',
                    'namespace' => 'DataTable',
                ),

            )
        ));
        return $autoloader;
    }

}

