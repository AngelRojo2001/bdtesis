<?php require_once('../Connections/conex.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE registro SET codigo=%s, autor=%s, titulo=%s, tutor=%s, idcar=%s, anio=%s, valoracion=%s, idmod=%s, npag=%s WHERE id=%s",
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['autor'], "text"),
                       GetSQLValueString($_POST['titulo'], "text"),
                       GetSQLValueString($_POST['tutor'], "text"),
                       GetSQLValueString($_POST['idcar'], "int"),
                       GetSQLValueString($_POST['anio'], "date"),
                       GetSQLValueString($_POST['valoracion'], "int"),
                       GetSQLValueString($_POST['idmod'], "int"),
                       GetSQLValueString($_POST['npag'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_conex, $conex);
  $Result1 = mysql_query($updateSQL, $conex) or die(mysql_error());

  $updateGoTo = "registro.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_editar = "-1";
if (isset($_GET['id'])) {
  $colname_editar = $_GET['id'];
}
mysql_select_db($database_conex, $conex);
$query_editar = sprintf("SELECT r.* FROM registro r JOIN carrera c ON r.idcar=c.id JOIN facultad f ON c.idfac=f.id JOIN modalidad m ON r.idmod=m.id WHERE r.id=%s", GetSQLValueString($colname_editar, "int"));
$editar = mysql_query($query_editar, $conex) or die(mysql_error());
$row_editar = mysql_fetch_assoc($editar);
$totalRows_editar = mysql_num_rows($editar);

mysql_select_db($database_conex, $conex);
$query_carrera = "SELECT * FROM carrera ORDER BY nombre ASC";
$carrera = mysql_query($query_carrera, $conex) or die(mysql_error());
$row_carrera = mysql_fetch_assoc($carrera);
$totalRows_carrera = mysql_num_rows($carrera);

mysql_select_db($database_conex, $conex);
$query_modalidad = "SELECT * FROM modalidad ORDER BY nombre ASC";
$modalidad = mysql_query($query_modalidad, $conex) or die(mysql_error());
$row_modalidad = mysql_fetch_assoc($modalidad);
$totalRows_modalidad = mysql_num_rows($modalidad);
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
<title>Editar Registro Normal</title>
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.10.1.custom.js"></script>
<script src="../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
<script src="../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="../css/jquery-ui-1.10.1.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	var x = $(document);
	x.ready(inicio);
	
	function inicio(){
		var x = $("#fdefensa");
		x.datepicker({changeMonth: true, changeYear: true});
		x.datepicker("option","dateFormat","yy-mm-dd");
	}
	
	function showselect(str){
		var xmlhttp;
		if(str == ""){
			document.getElementById("carrera").innerHTML="";
			return;
		}
        if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}
        else{
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
        xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
				document.getElementById("carrera").innerHTML = xmlhttp.responseText;
			}
		}
        xmlhttp.open("GET","showCarrera.php?idfac=" + str,"true");
        xmlhttp.send();
	}
</script>
<!-- InstanceEndEditable -->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<link href="../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="../SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
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
      <div><!-- InstanceBeginEditable name="Menú" --><!-- InstanceEndEditable -->
        <div class="salto"></div>
      </div>
      <h1><!-- InstanceBeginEditable name="Título" -->EDITAR REGISTRO<!-- InstanceEndEditable --></h1>
      <!-- InstanceBeginEditable name="Contenido" -->
      <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
        <table border="0" align="center">
          <tr>
            <td align="right"><strong>Código:</strong></td>
            <td><span id="vcodigo">
              <input name="codigo" type="text" id="codigo" value="<?php echo $row_editar['codigo']; ?>" />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Autor:</strong></td>
            <td><span id="vautor">
              <input name="autor" type="text" id="autor" value="<?php echo $row_editar['autor']; ?>" />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Título:</strong></td>
            <td><span id="vtitulo">
              <textarea name="titulo" cols="40" rows="2" id="titulo"><?php echo $row_editar['titulo']; ?></textarea>
            <span class="textareaRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Tutor:</strong></td>
            <td><span id="vtutor">
              <input name="tutor" type="text" id="tutor" value="<?php echo $row_editar['tutor']; ?>" />
</span></td>
          </tr>
          <tr>
            <td align="right"><strong>Carrera:</strong></td>
            <td><span id="carrera">
              <select name="idcar" id="idcar">
                <?php
do {  
?>
                <option value="<?php echo $row_carrera['id']?>"<?php if (!(strcmp($row_carrera['id'], $row_editar['idcar']))) {echo "selected=\"selected\"";} ?>><?php echo $row_carrera['nombre']?></option>
                <?php
} while ($row_carrera = mysql_fetch_assoc($carrera));
  $rows = mysql_num_rows($carrera);
  if($rows > 0) {
      mysql_data_seek($carrera, 0);
	  $row_carrera = mysql_fetch_assoc($carrera);
  }
?>
              </select>
            </span></td>
          </tr>
          <tr>
            <td align="right"><strong>Año:</strong></td>
            <td><span id="vanio">
            <input name="anio" type="text" id="anio" value="<?php echo $row_editar['anio']; ?>" />
<span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Valoración:</strong></td>
            <td><span id="vvaloracion">
            <input name="valoracion" type="text" id="valoracion" value="<?php echo $row_editar['valoracion']; ?>" />
<span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldMinValueMsg">Tiene que ser mayor a 0.</span><span class="textfieldMaxValueMsg">Tiene que ser menor a 100.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Modalidad:</strong></td>
            <td><select name="idmod" id="idmod">
              <?php
do {  
?>
              <option value="<?php echo $row_modalidad['id']?>"<?php if (!(strcmp($row_modalidad['id'], $row_editar['idmod']))) {echo "selected=\"selected\"";} ?>><?php echo $row_modalidad['nombre']?></option>
              <?php
} while ($row_modalidad = mysql_fetch_assoc($modalidad));
  $rows = mysql_num_rows($modalidad);
  if($rows > 0) {
      mysql_data_seek($modalidad, 0);
	  $row_modalidad = mysql_fetch_assoc($modalidad);
  }
?>
            </select></td>
          </tr>
          <tr>
            <td align="right"><strong>Nº de Página:</strong></td>
            <td><span id="vpag">
            <input name="npag" type="text" id="npag" value="<?php echo $row_editar['npag']; ?>" />
<span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td align="right"><button type="submit">EDITAR</button></td>
          </tr>
        </table>
        <input name="id" type="hidden" id="id" value="<?php echo $row_editar['id']; ?>" />
        <input type="hidden" name="MM_update" value="form1" />
      </form>
      <p>&nbsp;</p>
      <script type="text/javascript">
<!--
var sprytextfield2 = new Spry.Widget.ValidationTextField("vcodigo", "none");
var sprytextfield3 = new Spry.Widget.ValidationTextField("vautor");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("vtitulo");
var sprytextfield4 = new Spry.Widget.ValidationTextField("vtutor", "none", {isRequired:false});
var sprytextfield5 = new Spry.Widget.ValidationTextField("vanio", "integer", {isRequired:false});
var sprytextfield7 = new Spry.Widget.ValidationTextField("vvaloracion", "integer", {minValue:0, maxValue:100, isRequired:false});
var sprytextfield8 = new Spry.Widget.ValidationTextField("vpag", "integer", {isRequired:false});
//-->
      </script>
      <!-- InstanceEndEditable --></div>
  </div>
  <div id="pie">UNIVERSIDAD MAYOR DE SAN ANDRÉS<br />
    BIBLIOTECA CENTRAL</div>
</div>
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($editar);

mysql_free_result($carrera);

mysql_free_result($modalidad);
?>
