<?php
require_once('../../Connections/conex.php');
require_once('../../excel/PHPExcel.php');
require_once '../../excel/PHPExcel/Cell/AdvancedValueBinder.php';
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$nMes = $_REQUEST['mes'];
$anio = $_REQUEST['anio'];
$mes = "";
switch ($nMes) {
	case 1:
        $mes = "Enero";
		$nMes = '01';
        break;
    case 2:
        $mes = "Febrero";
		$nMes = '02';
        break;
    case 3:
        $mes = "Marzo";
		$nMes = '03';
        break;
    case 4:
        $mes = "Abril";
		$nMes = '04';
        break;
    case 5:
        $mes = "Mayo";
		$nMes = '05';
        break;
    case 6:
        $mes = "Junio";
		$nMes = '06';
        break;
    case 7:
        $mes = "Julio";
		$nMes = '07';
        break;
    case 8:
        $mes = "Agosto";
		$nMes = '08';
        break;
    case 9:
        $mes = "Septiembre";
		$nMes = '09';
        break;
    case 10:
        $mes = "Octubre";
        break;
    case 11:
        $mes = "Noviembre";
        break;
	case 12:
        $mes = "Diciembre";
        break;
}

mysql_select_db($database_conex,$conex) or die("Error en la base de datos");
$objPHPExcel = new PHPExcel();

$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);

$objPHPExcel->getProperties()->setCreator("CENTRAL")
        ->setLastModifiedBy("CENTRAL")
        ->setTitle("REPORTE MES");


$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName("Arial");
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A1',"Reporte del mes de $mes del $anio");

$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setName("Times New Roman");
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->setCellValue('A2',"Nro");
$objPHPExcel->getActiveSheet()->setCellValue('B2',"CODIGO");
$objPHPExcel->getActiveSheet()->setCellValue('C2',"AUTOR");
$objPHPExcel->getActiveSheet()->setCellValue('D2',"TITULO");
$objPHPExcel->getActiveSheet()->setCellValue('E2',"CARRERA");
$objPHPExcel->getActiveSheet()->setCellValue('F2',"AÑO");
$objPHPExcel->getActiveSheet()->setCellValue('G2',"FECHA");

$registro = mysql_query("SELECT r.codigo codigo, r.titulo titulo, r.autor autor, c.nombre carrera, r.anio anio, DATE_FORMAT(r.fregistro,'%d/%m/%Y') fecha
    FROM registro r JOIN carrera c ON r.idcar=c.id
    WHERE DATE_FORMAT(r.fregistro,'%m')='$nMes' AND DATE_FORMAT(r.fregistro,'%Y')='$anio'
    ORDER BY r.autor", $conex);
$i = 3;
while ($row_registro = mysql_fetch_array($registro)) {
    $codigo = $row_registro['codigo'];
    $codigo1 = separar($codigo);
    $titulo = $row_registro['titulo'];    
    $titulo1 = recortar($titulo, 40);        
    $autor = $row_registro['autor'];
    $autor1 = separar($autor);
    $carrera = $row_registro['carrera'];
	$anio = $row_registro['anio'];
    $fecha = $row_registro['fecha'];
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $i-2);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $codigo1);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $autor1);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $titulo1);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $carrera);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $anio);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $fecha);
    $i++;
}

$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
    ),
);
$objPHPExcel->getActiveSheet()->getStyle('A2:G'.($i-1))
        ->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()->getStyle('A3:G'.($i-1))
        ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(28);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11);

$objPHPExcel->getActiveSheet()->setTitle("Hoja 1");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte '.$mes.' '.$anio.'.xlsx"');
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