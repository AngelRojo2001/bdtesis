<?php
require_once('../../Connections/conex.php');
require_once('../../excel/PHPExcel.php');

mysql_select_db($database_conex, $conex);

$id = $_REQUEST['id'];

$registro = mysql_query("SELECT r.codigo codigo, r.autor autor, r.titulo titulo, c.nombre carrera, f.nombre facultad, m.nombre modalidad
						FROM registro r JOIN carrera c ON r.idcar=c.id JOIN facultad f ON c.idfac=f.id JOIN modalidad m ON r.idmod=m.id
						WHERE r.id=$id",$conex);
$row_registro = mysql_fetch_array($registro);
$codigo = $row_registro['codigo'];
$autor = $row_registro['autor'];
$carrera = $row_registro['carrera'];
$facultad = $row_registro['facultad'];
$modalidad = $row_registro['modalidad'];
$titulo = $row_registro['titulo'];

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("CENTRAL")
							->setLastModifiedBy("CENTRAL")
							->setTitle("CENTRAL");
		
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', 'AUTOR')
									->setCellValue('B3', 'FACULTAD')
									->setCellValue('B4', 'CARRERA')
									->setCellValue('B5', 'MODALIDAD')
									->setCellValue('B6', 'TITULO');
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', $autor);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', $facultad);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C4', $carrera);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C5', $modalidad);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C6', $titulo);

$objPHPExcel->getActiveSheet()->setTitle('Usuarios');
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getStyle('C2:C6')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$codigo.$autor.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
$objWriter->save('php://output');
exit;
?>