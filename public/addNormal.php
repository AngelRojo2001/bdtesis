<?php require_once('../Connections/conex.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<p>
  <?php
mysql_select_db($database_conex, $conex) or die("Error en la base de datos");
$codigo = $_REQUEST['codigo'];
$apellidos = $_REQUEST['apellidos'];
$nombres = $_REQUEST['nombres'];
$titulo = $_REQUEST['titulo'];
$tutor = $_REQUEST['tutor'];
$idcar = $_REQUEST['idcar'];
$idmod = $_REQUEST['idmod'];
$anio= $_REQUEST['anio'];
$pag = $_REQUEST['pag'];
$valoracion = $_REQUEST['valoracion'];
$usuario = $_REQUEST['usuario'];
$autor = "$apellidos, $nombres";

$query_find = sprintf("SELECT * FROM registro");
$find = mysql_query($query_find, $conex) or die("Error en la busqueda");
$sw = 0;
$id_old = -1;
$codigo_old = "";
$autor_old = "";
while($row_find = mysql_fetch_array($find)) {
	$titulo_old = $row_find['titulo'];
	$idcar_old = $row_find['idcar'];
	$idmod_old = $row_find['idmod'];
	if (strcmp($titulo_old,$titulo)==0 && $idcar_old==$idcar && $idmod_old==$idmod){
		$sw = 1;
		$id_old = $row_find['id'];
		$codigo_old = $row_find['codigo'];
		$autor_old = $row_find['autor'];
		break;
	}
}
if ($sw == 0){
	$query_insert = sprintf("INSERT INTO registro (codigo, autor, titulo, tutor, idcar, anio, valoracion, idmod, npag, responsable) VALUES ('%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, '%s')", $codigo, $autor, $titulo, $tutor, $idcar, $anio, $valoracion, $idmod, $pag, $usuario);
	mysql_query($query_insert, $conex) or die("Error en la insercion");
}
else {
	$codigo_new = $codigo_old."; ".$codigo;
	$autor_new = $autor_old."; ".$autor;
	$query_update = sprintf("UPDATE registro SET codigo='%s', autor='%s', fregistro=CURRENT_TIMESTAMP, responsable='%s' WHERE id=%d", $codigo_new, $autor_new, $usuario, $id_old);
	mysql_query($query_update, $conex) or die("Error en la actualizacion");
}
header('Location:insertRegistro.php');
?>
</p>
</body>
</html>