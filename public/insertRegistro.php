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

mysql_select_db($database_conex, $conex);
$query_facultad = "SELECT * FROM facultad ORDER BY nombre";
$facultad = mysql_query($query_facultad, $conex) or die(mysql_error());
$row_facultad = mysql_fetch_assoc($facultad);
$totalRows_facultad = mysql_num_rows($facultad);

mysql_select_db($database_conex, $conex);
$query_modalidad = "SELECT * FROM modalidad ORDER BY nombre";
$modalidad = mysql_query($query_modalidad, $conex) or die(mysql_error());
$row_modalidad = mysql_fetch_assoc($modalidad);
$totalRows_modalidad = mysql_num_rows($modalidad);

$maxRows_registro = 5;
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
<title>Registrar</title>
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
<link href="../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
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
      <h1><!-- InstanceBeginEditable name="Título" -->REGISTRAR<!-- InstanceEndEditable --></h1>
      <!-- InstanceBeginEditable name="Contenido" -->
      <form action="addNormal.php" method="POST" name="form1" id="form1">
        <table border="0" align="center">
          <tr>
            <td align="right"><strong>Código:</strong></td>
            <td><span id="vcodigo">
            <input type="text" name="codigo" id="codigo" />
            <span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Apellidos:</strong></td>
            <td><span id="vapellidos">
              <input name="apellidos" type="text" id="apellidos" size="40" />
              <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Nombre(s):</strong></td>
            <td><span id="vnombres">
              <input name="nombres" type="text" id="nombres" size="40" />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Título:</strong></td>
            <td><span id="vtitulo">
              <textarea name="titulo" cols="40" rows="2" id="titulo"></textarea>
            <span class="textareaRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Tutor(es):</strong></td>
            <td><span id="vtutor">
              <textarea name="tutor" cols="40" rows="2" id="tutor"></textarea>
            <span class="textareaRequiredMsg">Se necesita un valor.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Facultad:</strong></td>
            <td><span id="vfacultad">
              <select name="idfac" id="idfac" onchange="showselect(this.value)">
                <option value="">Seleccione...</option>
                <?php
do {  
?>
                <option value="<?php echo $row_facultad['id']?>"><?php echo $row_facultad['nombre']?></option>
                <?php
} while ($row_facultad = mysql_fetch_assoc($facultad));
  $rows = mysql_num_rows($facultad);
  if($rows > 0) {
      mysql_data_seek($facultad, 0);
	  $row_facultad = mysql_fetch_assoc($facultad);
  }
?>
              </select>
<span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Carrera:</strong></td>
            <td><span id="carrera"><select name="idcar" id="idcar">
            </select></span></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Año:</strong></td>
            <td><span id="vanio">
            <input name="anio" type="text" id="anio" value="" />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldMinCharsMsg">No se cumple el mínimo de números
            .</span><span class="textfieldMaxCharsMsg">Se ha superado el número máximo de números.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span></span></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Páginas:</strong></td>
            <td><span id="vpag">
            <input name="pag" type="text" id="pag" value="" />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldMinValueMsg">El valor introducido es menor a 0.</span></span></td>
          </tr>
          <tr>
            <td align="right" valign="top"><strong>Valoración:</strong></td>
            <td><span id="vvaloracion">
            <input name="valoracion" type="text" id="valoracion" value="" />
<span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldMinValueMsg">El valor introducido es menor a 0.</span><span class="textfieldMaxValueMsg">El valor introducido es mayor a 100.</span></span></td>
          </tr>
          <tr>
            <td align="right"><strong>Modalidad:</strong></td>
            <td><span id="vmodalidad">
              <select name="idmod" id="idmod">
                <option value="">Seleccione...</option>
                <?php
do {  
?>
                <option value="<?php echo $row_modalidad['id']?>"><?php echo $row_modalidad['nombre']?></option>
                <?php
} while ($row_modalidad = mysql_fetch_assoc($modalidad));
  $rows = mysql_num_rows($modalidad);
  if($rows > 0) {
      mysql_data_seek($modalidad, 0);
	  $row_modalidad = mysql_fetch_assoc($modalidad);
  }
?>
              </select>
            <span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td align="right"><button type="reset">BORRAR</button>
            <button type="submit">REGISTRAR</button></td>
          </tr>
        </table>
        <input name="usuario" type="hidden" id="usuario" value="<?php echo $_SESSION['MM_Username']; ?>" />
      </form>
      <?php if ($totalRows_registro > 0) { // Show if recordset not empty ?>
  <table width="90%" border="1" align="center" cellpadding="5" cellspacing="0">
    <?php do { ?>
      <tr>
        <td><?php echo $row_registro['codigo']; ?></td>
        <td><?php echo $row_registro['autor']; ?></td>
        <td><?php echo $row_registro['titulo']; ?></td>
        <td><a href="find/reportefpdf.php?id=<?php echo $row_registro['id']; ?>" target="_blank"><img src="../img/pdf.png" width="30" height="30" alt="PDF" /></a></td>
        <td><a href="facultad/editError.php?id=<?php echo $row_registro['id']; ?>"><img src="../img/edit.png" width="22" height="22" alt="Editar" /></a></td>
      </tr>
      <?php } while ($row_registro = mysql_fetch_assoc($registro)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
<script type="text/javascript">
<!--
var sprytextfield2 = new Spry.Widget.ValidationTextField("vcodigo", "integer");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("vtitulo");
var spryselect1 = new Spry.Widget.ValidationSelect("vfacultad");
var sprytextfield3 = new Spry.Widget.ValidationTextField("vapellidos", "none");
var sprytextfield1 = new Spry.Widget.ValidationTextField("vnombres");
var sprytextfield4 = new Spry.Widget.ValidationTextField("vanio", "integer", {minChars:4, maxChars:4});
var sprytextfield5 = new Spry.Widget.ValidationTextField("vpag", "integer", {minValue:0});
var sprytextfield6 = new Spry.Widget.ValidationTextField("vvaloracion", "integer", {minValue:0, maxValue:100, isRequired:false});
var spryselect2 = new Spry.Widget.ValidationSelect("vmodalidad");
var sprytextarea2 = new Spry.Widget.ValidationTextarea("vtutor");
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
mysql_free_result($facultad);

mysql_free_result($modalidad);

mysql_free_result($registro);
?>
