<?php
class Admin_Model_DbTable_Formatocp extends Zend_Db_Table_Abstract
{
  protected $formatos = [];
  protected $modelo = '';
  protected $cabecera = [];
  protected $data = [];
  protected $fileName = '';

  public function _setFormato($formato)
  {
    $this->formatos['anddes'] = 'formatos/tr-anddes.pdf';
    $this->formatos['barrick'] = 'formatos/tr-barrick.pdf';
    $this->formatos['cerro_verde'] = 'formatos/tr-cerro-verde.pdf';
    $this->formatos['edt'] = 'formatos/edt.pdf';
    $this->formatos['proyectos'] = 'formatos/proyectos.pdf';
    $this->formatos['carpetas'] = 'formatos/carpetas.pdf';
    $this->formatos['reporte_transmittal'] = 'formatos/reporte-tr.pdf';
    $this->formatos['reporte_cliente'] = 'formatos/cliente.pdf';
    $this->modelo = $this->formatos[$formato];
  }

  public function _setCabecera($data)
  {
    $this->cabecera = $data;
  }

  public function _setData($data)
  {
    $this->data = $data;
  }

  private function _rellenarFormato($pdf)
  {
    $page = $pdf->pages[0];
    $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
    $page->setFont($font, 9);

    //echo "estoy en formato";
    //print_r($pdf);exit();

    if ($this->modelo == $this->formatos['anddes']) {

      //datos de cabecera
      // $page->drawText($this->cabecera['nombre_comercial'], 70, 740, 'UTF-8');
      // $page->drawText($this->cabecera['puesto_trabajo'], 70, 725, 'UTF-8');
      // $page->drawText($this->cabecera['nombre_atencion'], 70, 710, 'UTF-8');
      // $page->drawText($this->cabecera['codificacion'].'-'.$this->cabecera['correlativo'], 360, 740);
      // $page->drawText($this->cabecera['proyectoid'], 360, 725);
      // $page->drawText('¿de que estamos hablando?', 360, 710, 'UTF-8');

      // if ($this->cabecera['modo_envio'] == 'C') {
      //   $page->drawText('X', 96, 693);
      // } elseif ($this->cabecera['modo_envio'] == 'F') {
      //   $page->drawText('X', 205, 693);
      // }

      // $page->drawText('¿Codigo de que?', 360, 695, 'UTF-8');
      // $page->drawText(date("d-m-Y"), 510, 695);

      // //cuerpo
      // $page->setFont($font, 7);
      // for ($i=0; $i < sizeof($this->data); $i++) {
      //   $page->drawText((string)$i + 1, 7, 650 - ($i * 20));
      //   $page->drawText($this->data[$i]['codigo_anddes'], 30, 650 - ($i * 20));
      //   $page->drawText($this->data[$i]['revision'], 145, 650 - ($i * 20));
      //   $page->drawText($this->data[$i]['descripcion_entregable'], 170, 650 - ($i * 20), 'UTF-8');
      //   $page->drawText($this->data[$i]['tipo_documento'], 505, 650 - ($i * 20));

      // }
      // $page->setFont($font, 12);

      // if ($this->data[0]['emitido'] == 'A') {
      //   $page->drawText('X', 40, 155);
      // } elseif ($this->data[0]['emitido'] == 'B') {
      //   $page->drawText('X', 40, 133);
      // } elseif ($this->data[0]['emitido'] == 'C') {
      //   $page->drawText('X', 40, 110);
      // } elseif ($this->data[0]['emitido'] == 'AP') {
      //   $page->drawText('X', 325, 155);
      // } elseif ($this->data[0]['emitido'] == 'AC') {
      //   $page->drawText('X', 325, 133);
      // } elseif ($this->data[0]['emitido'] == 'NA') {
      //   $page->drawText('X', 325, 110);
      // }

      // $this->fileName = $this->cabecera['codificacion'].'-'.$this->cabecera['correlativo'].'.pdf';

      //datos de entregables
    // } 
    // elseif ($this->modelo == $this->formatos['barrick']) {

    // } elseif ($this->modelo == $this->formatos['cerro_verde']) {

    // } elseif ($this->modelo == $this->formatos['edt']) {
    //   //datos de cabecera
    //   $page->drawText(date("d-m-Y"), 500, 800);
    //   $page->drawText($this->cabecera['nombre_proyecto'], 140, 740, 'UTF-8');
    //   $page->drawText($this->cabecera['proyectoid'], 450, 740);
    //   //datos del cuerpo
    //   for ($i=0; $i < sizeof($this->data); $i++) {
    //     $page->drawText($this->data[$i]['codigo'], 140, 650 - ($i * 20));
    //     $page->drawText($this->data[$i]['nombre'], 200, 650 - ($i * 20));
    //   }

    //   $this->fileName = $this->cabecera['proyectoid'].'-EDT.pdf';

     } elseif ($this->modelo == $this->formatos['proyectos']) {
      //estados
      // $estados = [];
      // $estados['A'] = 'Activo';
      // $estados['P'] = 'Paralizado';
      // $estados['C'] = 'Cerrado';
      // $estados['CA'] = 'Cancelado';
      // //datos cabecera
      // $page->drawText(date("d-m-Y"), 750, 572);
      // $page->drawText(sizeof($this->data), 750, 543);
      //datos del cuerpo
      // for ($i=0; $i < sizeof($this->data); $i++) {
      //   $page->drawText((string)$i + 1, 7, 470 - ($i * 20));
      //   $page->drawText($this->data[$i]['proyectoid'], 30, 470 - ($i * 20));
      //   $page->drawText($this->data[$i]['nombre_comercial'], 85, 470 - ($i * 20), 'UTF-8');
      //   $page->drawText($this->data[$i]['nombre_proyecto'], 260, 470 - ($i * 20), 'UTF-8');
      //   $page->drawText($this->data[$i]['gerente_proyecto'], 495, 470 - ($i * 20));
      //   $page->drawText($this->data[$i]['control_proyecto'], 590, 470 - ($i * 20));
      //   $page->drawText($this->data[$i]['control_documentario'], 685, 470 - ($i * 20));
      //   $page->drawText($estados[$this->data[$i]['estado']], 780, 470 - ($i * 20));
      // }

      //$this->fileName = 'Lista de Proyectos.pdf';
    } 
    //elseif ($this->modelo == $this->formatos['carpetas']) {
    //   //cabecera
    //   $page->drawText(date("d-m-Y"), 510, 800);

    //   //datos
    //   for ($i=0; $i < sizeof($this->data); $i++) {
    //     $page->drawText((string)$i + 1, 7, 720 - ($i * 20));
    //     $page->drawText($this->data[$i]['nombre'], 30, 720 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['A'], 380, 720 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['P'], 440, 720 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['CA'], 500, 720 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['C'], 560, 720 - ($i * 20), 'UTF-8');
    //   }

    //   $page->drawText($this->cabecera['A'], 380, 10);
    //   $page->drawText($this->cabecera['P'], 440, 10);
    //   $page->drawText($this->cabecera['CA'], 500, 10);
    //   $page->drawText($this->cabecera['C'], 560, 10);

    //   $this->fileName = 'Lista de Carpetas.pdf';
    // } elseif ($this->modelo == $this->formatos['reporte_transmittal']) {
    //   //cabecera
    //   $page->drawText(date("d-m-Y"), 730, 550);
    //   $page->drawText($this->cabecera['nombre_proyecto'], 85, 490, 'UTF-8');
    //   $page->drawText($this->cabecera['proyectoid'], 85, 455);
    //   $page->drawText($this->cabecera['gerente_proyecto'], 700, 490, 'UTF-8');
    //   $page->drawText($this->cabecera['control_documentario'], 700, 455);
    //   //cuerpo
    //   $page->setFont($font, 6);
    //   for ($i=0; $i < sizeof($this->data); $i++) {
    //     $page->drawText((string)$i + 1, 5, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['edt'], 20, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['tipo_documento'], 43, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['disciplina'], 85, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['codigo_anddes'], 130, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['codigo_cliente'], 220, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['descripcion_entregable'], 310, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['revision_entregable'], 515, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['estado_revision'], 530, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['transmittal'], 555, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['emitido'], 600, 385 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['fecha'], 633, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['respuesta_transmittal'], 660, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['respuesta_emitido'], 703, 385 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['respuesta_fecha'], 737, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['estado'], 762, 385 - ($i * 20));
    //     $page->drawText($this->data[$i]['comentario'], 793, 385 - ($i * 20));
    //   }

    //   $this->fileName = 'Reporte Transmittal.pdf';
    // } elseif ($this->modelo == $this->formatos['reporte_cliente']) {
    //   $page->drawText(date("d-m-Y"), 730, 550);
    //   $page->drawText($this->cabecera['nombre_proyecto'], 85, 490, 'UTF-8');
    //   $page->drawText($this->cabecera['proyectoid'], 85, 455);
    //   $page->drawText($this->cabecera['gerente_proyecto'], 700, 490, 'UTF-8');
    //   $page->drawText($this->cabecera['control_documentario'], 700, 455);

    //   for ($i=0; $i < sizeof($this->data); $i++) {
    //     $page->drawText((string)$i + 1, 5, 400 - ($i * 20));
    //     $page->drawText($this->data[$i]['transmittal'], 20, 400 - ($i * 20));
    //     $page->drawText($this->data[$i]['codigo_anddes'], 137, 400 - ($i * 20));
    //     $page->drawText($this->data[$i]['codigo_cliente'], 250, 400 - ($i * 20));
    //     $page->drawText($this->data[$i]['descripcion'], 380, 400 - ($i * 20));
    //     $page->drawText($this->data[$i]['revision'], 610, 400 - ($i * 20));
    //     $page->drawText($this->data[$i]['emitido'], 635, 400 - ($i * 20), 'UTF-8');
    //     $page->drawText($this->data[$i]['fecha'], 775, 400 - ($i * 20));
    //   }

    //   $this->fileName = 'Reporte Cliente.pdf';
    // }
    return $pdf;
  }

  public function _print()
  {
    try {
      $pdf = Zend_Pdf::load($this->modelo);

      $pdf_rellenado = $this->_rellenarFormato($pdf);
      $pdf_rellenado->save($this->fileName);
      $ruta['archivo'] = $this->fileName;
        return $ruta;
        
    } catch (Exception $e) {
      print $e->getMessage();
    }
  }
}