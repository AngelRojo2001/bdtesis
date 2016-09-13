<?php
require_once('../../Connections/conex.php');
require_once('../../excel/PHPExcel.php');
require_once '../../excel/PHPExcel/Cell/AdvancedValueBinder.php';
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

mysql_select_db($database_conex, $conex);
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("CENTRAL")
        ->setLastModifiedBy("CENTRAL")
        ->setTitle("Facultades");

$idfac = $_REQUEST['idfac'];
$facultad = mysql_query("SELECT * FROM facultad WHERE id=$idfac",$conex);
$row_facultad = mysql_fetch_array($facultad);
$nomfac = $row_facultad['nombre'];
$carrera = mysql_query("SELECT *
    FROM carrera
    WHERE idfac=$idfac
    ORDER BY nombre",$conex) or die(mysql_error());

$pos = 0;
while($row_carrera = mysql_fetch_array($carrera)){
    $idcar = $row_carrera['id'];
    $nomcar = $row_carrera['nombre'];
    $i = 4;
    $registro = mysql_query("
        SELECT r.nro nro, r.autor autor, r.titulo titulo, r.tutor tutor,
            r.anio anio, DATE_FORMAT(r.fdefensa,'%d/%m/%Y') fdefensa, r.valoracion valoracion,
            m.nombre modalidad, r.npag npag, r.descriptor descriptor,
            DATE_FORMAT(r.fregistro,'%d/%m/%Y') fregistro
        FROM registro r JOIN modalidad m ON r.idmod=m.id
        WHERE idcar=$idcar
        ORDER BY r.fregistro ASC",$conex) or die(mysql_error());
    
    $objPHPExcel->setActiveSheetIndex($pos);
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
    
    $objPHPExcel->getActiveSheet()->setCellValue('A1',"TESIS BIBLIOTECA CENTRAL");
    $objPHPExcel->getActiveSheet()->setCellValue('A2',strtoupper($nomcar));
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:M3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:M3')->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->getStyle('A1:M3')->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:M2');
    
    $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getFont()
            ->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
    $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A3')->getFill()
            ->getStartColor()->setARGB('FF008000');
    $objPHPExcel->getActiveSheet()->setCellValue('A3',"NRO");
    $objPHPExcel->getActiveSheet()->getStyle('B3:C3')->getFill()
            ->getStartColor()->setARGB('FFFF0000');
    $objPHPExcel->getActiveSheet()->setCellValue('B3',"AUTOR");
    $objPHPExcel->getActiveSheet()->setCellValue('C3',"TITULO");
    $objPHPExcel->getActiveSheet()->getStyle('D3:F3')->getFill()
            ->getStartColor()->setARGB('FF008000');
    $objPHPExcel->getActiveSheet()->setCellValue('D3',"TUTOR");
    $objPHPExcel->getActiveSheet()->setCellValue('E3',"FACULTAD");
    $objPHPExcel->getActiveSheet()->setCellValue('F3',"CARRERA");
    $objPHPExcel->getActiveSheet()->getStyle('G3:H3')->getFill()
            ->getStartColor()->setARGB('FFFF0000');
    $objPHPExcel->getActiveSheet()->setCellValue('G3',"AÑO");
    $objPHPExcel->getActiveSheet()->setCellValue('H3',"DEFENSA");
    $objPHPExcel->getActiveSheet()->getStyle('I3:J3')->getFill()
            ->getStartColor()->setARGB('FF008000');
    $objPHPExcel->getActiveSheet()->setCellValue('I3',"VALORACION");
    $objPHPExcel->getActiveSheet()->setCellValue('J3',"MODALIDAD");
    $objPHPExcel->getActiveSheet()->getStyle('K3:M3')->getFill()
            ->getStartColor()->setARGB('FF333399');
    $objPHPExcel->getActiveSheet()->setCellValue('K3',"Nº PAG");
    $objPHPExcel->getActiveSheet()->setCellValue('L3',"DESCRIPTOR");
    $objPHPExcel->getActiveSheet()->setCellValue('M3',"FECHA");
    
    while($row_registro = mysql_fetch_array($registro)){
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$i-3);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i,separar($row_registro['autor']));
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i,recortar($row_registro['titulo'], 60));
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i,separar($row_registro['tutor']));
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$nomfac);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$nomcar);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$row_registro['anio']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$row_registro['fdefensa']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$row_registro['valoracion']);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$row_registro['modalidad']);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$row_registro['npag']);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$row_registro['descriptor']);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$row_registro['fregistro']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->getFont()->setSize(10);
        $i++;
    }
    if(strlen($nomcar) > 12) {
        $nomcar = substr($nomcar,0,12);
    }
    $objPHPExcel->getActiveSheet()->setTitle($nomcar);
    $objPHPExcel->createSheet();
    $pos++;
    
    $styleArray = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            ),
        ),
    );
    $objPHPExcel->getActiveSheet()->getStyle('A3:M'.($i-1))
            ->applyFromArray($styleArray);
    
    $objPHPExcel->getActiveSheet()->getStyle('A4:M'.($i-1))
        ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(7);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(9);
}

$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nomfac.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
$objWriter->save('php://output');

function recortar($cadena, $tam) {
    $titulo_es = explode(" ", $cadena);
    $sum = strlen($titulo_es[0]);
    $titulo_nuevo = $titulo_es[0];
    for ($i = 1; $i < count($titulo_es); $i++) {
        $sum += strlen($titulo_es[$i]) + 1;
        if ($sum <= $tam) {
            $titulo_nuevo .= " $titulo_es[$i]";
        }
        else {
            $titulo_nuevo .= "\n$titulo_es[$i]";
            $sum = strlen($titulo_es[$i]);
        }
    }
    return $titulo_nuevo;
}

function separar($cadena) {
    $cadena_old = explode(";", $cadena);
    $cadena_new = "";
    if (count($cadena_old)>0) {
        for ($i = 0; $i < count($cadena_old)-1; $i++) {
            $cadena_new .= "$cadena_old[$i]\n";
        }
        $cadena_new .= "$cadena_old[$i]";
        return $cadena_new;
    }
    else
        return $cadena;
}
?>