<?php require_once('../../Connections/conex.php'); ?>
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

$colname_registro = "-1";
if (isset($_POST['fecha'])) {
  $colname_registro = $_POST['fecha'];
}
mysql_select_db($database_conex, $conex);
$query_registro = sprintf("SELECT id, codigo, autor, titulo FROM registro WHERE DATE_FORMAT(fregistro,'%%Y-%%m-%%d')=%s ORDER BY autor ASC", GetSQLValueString($colname_registro, "text"));
$registro = mysql_query($query_registro, $conex) or die(mysql_error());
$row_registro = mysql_fetch_assoc($registro);
$totalRows_registro = mysql_num_rows($registro);
?>
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
<title>Búsqueda por Fecha</title>
<script type="text/javascript" src="../../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.10.1.custom.js"></script>
<link href="../../css/jquery-ui-1.10.1.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	var x = $(document);
	x.ready(inicio);
	
	function inicio(){
		var x = $("#fecha");
		x.datepicker({changeMonth: true, changeYear: true});
		x.datepicker("option","dateFormat","yy-mm-dd");
	}
</script>
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
        <li><a href="findFecha.php">BÚSQUEDA</a></li>
        <?php
		if($_SESSION['MM_UserGroup'] == "admin" || $_SESSION['MM_UserGroup']=="private"){
		?>
        <li><a href="../report/findFacultad.php">REPORTES</a></li>
        <li><a href="../facultad/facultad.php">UMSA</a></li>
        <?php
		}
		if($_SESSION['MM_UserGroup']=="admin"){
		?>
        <li><a href="../perfil/insertUser.php">CREAR USUARIOS</a></li>
        <?php
		}
		?>
        <li><a href="../perfil/editPerfil.php">EDITAR PERFIL</a></li>
        <li><a href="<?php echo $logoutAction ?>">CERRAR SESIÓN</a></li>
      </ul>
    </div>
    <div id="contenido">
      <div><!-- InstanceBeginEditable name="Menú" -->
        <ul class="menu_buscar">
          <li><a href="findFecha.php">FECHA</a></li>
          <li><a href="findCodigo.php">CÓDIGO</a></li>
        </ul>
      <!-- InstanceEndEditable -->
        <div class="salto"></div>
      </div>
      <h1><!-- InstanceBeginEditable name="Título" -->BÚSQUEDA POR FECHA<!-- InstanceEndEditable --></h1>
      <!-- InstanceBeginEditable name="Contenido" -->
      <form id="form1" name="form1" method="post" action="">
        <table border="0" align="center">
          <tr>
            <td>Fecha:</td>
            <td><input name="fecha" type="text" id="fecha" readonly="readonly" /></td>
            <td><button type="submit" name="boton">BUSCAR</button></td>
          </tr>
        </table>
      </form>
      <?php if ($totalRows_registro > 0) { // Show if recordset not empty ?>
        <p><strong><em>Resultado:</em></strong></p>
        <table width="90%" border="1" align="center" cellpadding="5" cellspacing="0">
          <tr>
            <th scope="col">Código</th>
            <th scope="col">Autor</th>
            <th scope="col">Título</th>
            <th scope="col">&nbsp;</th>
            <th scope="col">&nbsp;</th>
            <th scope="col">&nbsp;</th>
          </tr>
          <?php do { ?>
            <tr>
              <td><?php echo $row_registro['codigo']; ?></td>
              <td><?php echo $row_registro['autor']; ?></td>
              <td><?php echo $row_registro['titulo']; ?></td>
              <td><a href="reportefpdf.php?id=<?php echo $row_registro['id']; ?>" target="_blank"><img src="../../img/pdf.png" width="30" height="30" alt="PDF" /></a></td>
              <td><a href="../facultad/updateRegistro.php?id=<?php echo $row_registro['id']; ?>"><img src="../../img/edit.png" width="22" height="22" alt="Editar" /></a></td>
              <td><a href="../facultad/deleteRegistro.php?id=<?php echo $row_registro['id']; ?>"><img src="../../img/trash.png" width="22" height="22" alt="Borrar" /></a></td>
            </tr>
            <?php } while ($row_registro = mysql_fetch_assoc($registro)); ?>
        </table>
        <table width="80%" border="0" align="center">
          <tr>
            <td width="33%" align="left">&nbsp;</td>
            <td width="33%" align="center">&nbsp;</td>
            <td width="33%" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
            <td align="center">&nbsp;Total <?php echo $totalRows_registro ?> registros</td>
            <td align="right">&nbsp;</td>
          </tr>
        </table>
        <?php } // Show if recordset not empty ?>
<?php
                if(isset($_REQUEST['boton'])){
				?>
<?php if ($totalRows_registro == 0) { // Show if recordset empty ?>
  <p>No se devolvieron resultados.</p>
  <?php } // Show if recordset empty ?>
<?php
				}
  ?>
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
