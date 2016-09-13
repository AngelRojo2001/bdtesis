<?php require_once('../../Connections/conex.php'); ?>
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
	
  $logoutGoTo = "../../index.php";
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

$MM_restrictGoTo = "../../index.php";
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
$maxRows_login = 10;
$pageNum_login = 0;
if (isset($_GET['pageNum_login'])) {
  $pageNum_login = $_GET['pageNum_login'];
}
$startRow_login = $pageNum_login * $maxRows_login;

mysql_select_db($database_conex, $conex);
$query_login = "SELECT * FROM login ORDER BY usuario";
$query_limit_login = sprintf("%s LIMIT %d, %d", $query_login, $startRow_login, $maxRows_login);
$login = mysql_query($query_limit_login, $conex) or die(mysql_error());
$row_login = mysql_fetch_assoc($login);

if (isset($_GET['totalRows_login'])) {
  $totalRows_login = $_GET['totalRows_login'];
} else {
  $all_login = mysql_query($query_login);
  $totalRows_login = mysql_num_rows($all_login);
}
$totalPages_login = ceil($totalRows_login/$maxRows_login)-1;

$queryString_login = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_login") == false && 
        stristr($param, "totalRows_login") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_login = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_login = sprintf("&totalRows_login=%d%s", $totalRows_login, $queryString_login);
?>
<title>Lista Usuarios</title>
<!-- InstanceEndEditable -->
<link href="../../css/estilo.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body>
<div id="contenedor">
  <div id="cabecera"><img src="../../img/logoUMSA.png" alt="Logo" name="logo" width="59" height="118" id="logo" />BIBLIOTECA CENTRAL - UMSA</div>
  <div id="cuerpo">
    <div id="menuIzquierda">
      <ul class="menu_principal">
        <li><a href="../inicio.php">MENÚ PRINCIPAL</a></li>
        <li><a href="../insertRegistro.php">REGISTROS</a></li>
        <li><a href="../find/findFecha.php">BÚSQUEDA</a></li>
        <?php
		if($_SESSION['MM_UserGroup'] == "admin" || $_SESSION['MM_UserGroup']=="private"){
		?>
        <li><a href="../report/findFacultad.php">REPORTES</a></li>
        <li><a href="../facultad/facultad.php">UMSA</a></li>
        <?php
		}
		if($_SESSION['MM_UserGroup']=="admin"){
		?>
        <li><a href="insertUser.php">CREAR USUARIOS</a></li>
        <?php
		}
		?>
        <li><a href="editPerfil.php">EDITAR PERFIL</a></li>
        <li><a href="<?php echo $logoutAction ?>">CERRAR SESIÓN</a></li>
      </ul>
    </div>
    <div id="contenido">
      <div><!-- InstanceBeginEditable name="Menú" -->
        <ul class="menu_buscar">
          <li><a href="insertUser.php">INSERTAR</a></li>
          <li><a href="listUser.php">LISTADO</a></li>
        </ul>
      <!-- InstanceEndEditable -->
        <div class="salto"></div>
      </div>
      <h1><!-- InstanceBeginEditable name="Título" -->LISTA DE TODOS LOS USUARIOS<!-- InstanceEndEditable --></h1>
      <!-- InstanceBeginEditable name="Contenido" -->
      <table border="0" align="center">
        <tr>
          <th scope="col">Usuario</th>
          <th scope="col">Tipo</th>
          <th scope="col">&nbsp;</th>
          <th scope="col">&nbsp;</th>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_login['usuario']; ?></td>
            <td><?php echo $row_login['tipo']; ?></td>
            <td><a href="updateUser.php?id=<?php echo $row_login['id']; ?>"><img src="../../img/edit.png" width="22" height="22" alt="Editar" /></a></td>
            <td><a href="deleteUser.php?id=<?php echo $row_login['id']; ?>"><img src="../../img/trash.png" width="22" height="22" alt="Borrar" /></a></td>
          </tr>
          <?php } while ($row_login = mysql_fetch_assoc($login)); ?>
<tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
      <table width="50%" border="0" align="center">
        <tr>
          <td width="33%" scope="col"><?php if ($pageNum_login > 0) { // Show if not first page ?>
              <a href="<?php printf("%s?pageNum_login=%d%s", $currentPage, max(0, $pageNum_login - 1), $queryString_login); ?>">Anterior</a>
          <?php } // Show if not first page ?></td>
          <td width="33%" align="center" scope="col">&nbsp;
Registros <?php echo ($startRow_login + 1) ?> a <?php echo min($startRow_login + $maxRows_login, $totalRows_login) ?> de <?php echo $totalRows_login ?></td>
          <td width="33%" align="right" scope="col"><?php if ($pageNum_login < $totalPages_login) { // Show if not last page ?>
              <a href="<?php printf("%s?pageNum_login=%d%s", $currentPage, min($totalPages_login, $pageNum_login + 1), $queryString_login); ?>">Siguiente</a>
          <?php } // Show if not last page ?></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <!-- InstanceEndEditable --></div>
  </div>
  <div id="pie">UNIVERSIDAD MAYOR DE SAN ANDRÉS<br />
    BIBLIOTECA CENTRAL</div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($login);
?>
