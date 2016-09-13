<?php
require_once('../../Connections/conex.php');
require_once('../../fpdf/fpdf.php');

$id = $_GET['id'];
mysql_select_db($database_conex);
$registro = mysql_query("SELECT r.autor autor, r.titulo titulo, m.nombre modalidad, c.nombre carrera, f.nombre facultad, now() fecha
                        FROM registro r JOIN carrera c ON r.idcar=c.id JOIN facultad f ON c.idfac=f.id JOIN modalidad m ON r.idmod=m.id
                        WHERE r.id=$id",$conex) or die("Error en la consulta");
$row_registro = mysql_fetch_array($registro);
$array = explode(";",$row_registro['autor']);
$i = count($array);
if($i > 0) {
	$autor = $array[$i-1];
}
else {
	$autor = $row_registro['autor'];
}

$alto = 8;

$pdf = new FPDF('P','mm','Letter');
$pdf->AddPage();
$pdf->SetFont('Arial','',12);
$pdf->Cell(30,71,'',0,1);
$pdf->Cell(30,0,'');
$pdf->Cell(110,$alto,utf8_decode($autor),0,1);
$pdf->Cell(40,0,'');
$pdf->Cell(100,$alto,utf8_decode($row_registro['carrera']),0,1);
$pdf->Cell(40,0,'');
$pdf->Cell(100,$alto,utf8_decode($row_registro['facultad']),0,1);
$pdf->Cell(80,0,'');
$pdf->Cell(60,$alto,utf8_decode($row_registro['modalidad']),0,1);
$pdf->MultiCell(140,$alto,utf8_decode($row_registro['titulo']),0);
$fecha = $row_registro['fecha'];
$dia = substr($fecha,8,2);
$mes = substr($fecha,5,2);
$anio = substr($fecha,2,2);
$mesLetra = "";
if (strcmp($mes,"01") == 0) {
	$mesLetra = "Enero";
}
elseif (strcmp($mes,"02") == 0) {
	$mesLetra = "Febrero";
}
elseif (strcmp($mes,"03") == 0) {
	$mesLetra = "Marzo";
}
elseif (strcmp($mes,"04") == 0) {
	$mesLetra = "Abril";
}
elseif (strcmp($mes,"05") == 0) {
	$mesLetra = "Mayo";
}
elseif (strcmp($mes,"06") == 0) {
	$mesLetra = "Junio";
}
elseif (strcmp($mes,"07") == 0) {
	$mesLetra = "Julio";
}
elseif (strcmp($mes,"08") == 0) {
	$mesLetra = "Agosto";
}
elseif (strcmp($mes,"09") == 0) {
	$mesLetra = "Septiembre";
}
elseif (strcmp($mes,"10") == 0) {
	$mesLetra = "Octubre";
}
elseif (strcmp($mes,"11") == 0) {
	$mesLetra = "Noviembre";
}
elseif (strcmp($mes,"12") == 0) {
	$mesLetra = "Diciembre";
}
$pdf->SetY(-74);
$pdf->Cell(71,0,'');
$pdf->Cell(15,$alto,$dia);
$pdf->Cell(49,$alto,$mesLetra);
$pdf->Cell(10,$alto,$anio);
$pdf->Output();
?>