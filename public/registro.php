<?php require_once('../Connections/conex.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/base.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin,private,public";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];
?>
<?php
$maxRows_registro = 10;
$pageNum_registro = 0;
if (isset($_GET['pageNum_registro'])) {
  $pageNum_registro = $_GET['pageNum_registro'];
}
$startRow_registro = $pageNum_registro * $maxRows_registro;

mysql_select_db($database_conex, $conex);
$query_registro = "SELECT id, codigo, autor, titulo, DATE_FORMAT(fregistro,'%d/%m/%Y') fecha FROM registro ORDER BY fregistro DESC";
$query_limit_registro = sprintf("%s LIMIT %d, %d", $query_registro, $startRow_registro, $maxRows_registro);
$registro = mysql_query($query_limit_registro, $conex) or die(mysql_error());
$row_registro = mysql_fetch_assoc($registro);

if (isset($_GET['totalRows_registro'])) {
  $totalRows_registro = $_GET['totalRows_registro'];
} else {
  $all_registro = mysql_query($query_registro);
  $totalRows_registro = mysql_num_rows($all_registro);
}
$totalPages_registro = ceil($totalRows_registro/$maxRows_registro)-1;

$queryString_registro = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_registro") == false && 
        stristr($param, "totalRows_registro") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_registro = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_registro = sprintf("&totalRows_registro=%d%s", $totalRows_registro, $queryString_registro);
?>
<title>Editar Registro Normal</title>
<link href="../css/jquery-ui-1.10.1.custom.css" rel="stylesheet" type="text/css" />
<!-- InstanceEndEditable -->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
<div id="contenedor">
  <div id="cabecera"><img src="../img/logoUMSA.png" alt="Logo" name="logo" width="59" height="118" id="logo" />BIBLIOTECA CENTRAL - UMSA</div>
  <div id="cuerpo">
    <div id="menuIzquierda">
      <ul class="menu_principal">
        <li><a href="inicio.php">MENÚ PRINCIPAL</a></li>
        <li><a href="insertRegistro.php">REGISTROS</a></li>
        <li><a href="find/findFecha.php">BÚSQUEDA</a></li>
        <?php
		if($_SESSION['MM_UserGroup'] == "admin" || $_SESSION['MM_UserGroup']=="private"){
		?>
        <li><a href="report/findFacultad.php">REPORTES</a></li>
        <li><a href="facultad/facultad.php">UMSA</a></li>
        <?php
		}
		if($_SESSION['MM_UserGroup']=="admin"){
		?>
        <li><a href="perfil/insertUser.php">CREAR USUARIOS</a></li>
        <?php
		}
		?>
        <li><a href="perfil/editPerfil.php">EDITAR PERFIL</a></li>
        <li><a href="<?php echo $logoutAction ?>">CERRAR SESIÓN</a></li>
      </ul>
    </div>
    <div id="contenido">
      <div><!-- InstanceBeginEditable name="Menú" -->
        <ul class="menu_buscar">
          <li><a href="insertRegistro.php">REGISTRO</a></li>
          <li><a href="registro.php">LISTADO</a></li>
        </ul>
      <!-- InstanceEndEditable -->
        <div class="salto"></div>
      </div>
      <h1><!-- InstanceBeginEditable name="Título" -->LISTADO DE REGISTRO<!-- InstanceEndEditable --></h1>
      <!-- InstanceBeginEditable name="Contenido" -->
      <?php if ($totalRows_registro > 0) { // Show if recordset not empty ?>
        <table width="90%" border="1" align="center" cellpadding="5" cellspacing="0">
          <tr>
            <th scope="col">Código</th>
            <th scope="col">Autor</th>
            <th scope="col">Título</th>
            <th scope="col">Fecha</th>
            <th scope="col">&nbsp;</th>
            <th scope="col">&nbsp;</th>
          </tr>
          <?php do { ?>
            <tr>
              <td><?php echo $row_registro['codigo']; ?></td>
              <td><?php echo $row_registro['autor']; ?></td>
              <td><?php echo $row_registro['titulo']; ?></td>
              <td><?php echo $row_registro['fecha']; ?></td>
              <td><a href="updateRegistro.php?id=<?php echo $row_registro['id']; ?>"><img src="../img/edit.png" width="22" height="22" alt="Editar" /></a></td>
              <td><a href="find/reportefpdf.php?id=<?php echo $row_registro['id']; ?>" target="_blank"><img src="../img/pdf.png" width="30" height="30" alt="PDF" /></a></td>
            </tr>
            <?php } while ($row_registro = mysql_fetch_assoc($registro)); ?>
        </table>
        <table width="80%" border="0" align="center">
          <tr>
            <td width="33%">&nbsp;</td>
            <td width="33%" align="center">&nbsp;</td>
            <td width="33%" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td><?php if ($pageNum_registro > 0) { // Show if not first page ?>
                <a href="<?php printf("%s?pageNum_registro=%d%s", $currentPage, max(0, $pageNum_registro - 1), $queryString_registro); ?>">Anterior</a>
            <?php } // Show if not first page ?></td>
            <td align="center">&nbsp;
Registros <?php echo ($startRow_registro + 1) ?> a <?php echo min($startRow_registro + $maxRows_registro, $totalRows_registro) ?> de <?php echo $totalRows_registro ?></td>
            <td align="right"><?php if ($pageNum_registro < $totalPages_registro) { // Show if not last page ?>
                <a href="<?php printf("%s?pageNum_registro=%d%s", $currentPage, min($totalPages_registro, $pageNum_registro + 1), $queryString_registro); ?>">Siguiente</a>
            <?php } // Show if not last page ?></td>
          </tr>
        </table>
        <?php } // Show if recordset not empty ?>
        <?php if ($totalRows_registro == 0) { // Show if recordset empty ?>
  <p><strong><em>No hay registros para editar</em></strong>.</p>
  <?php } // Show if recordset empty ?>
      <!-- InstanceEndEditable --></div>
  </div>
  <div id="pie">UNIVERSIDAD MAYOR DE SAN ANDRÉS<br />
    BIBLIOTECA CENTRAL</div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($registro);
?>
