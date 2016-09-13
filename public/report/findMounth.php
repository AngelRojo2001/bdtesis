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
<title>Reportes Por Mes</title>
<!-- InstanceEndEditable -->
<link href="../../css/estilo.css" rel="stylesheet" type="text/css" />
<!-- InstanceBeginEditable name="head" -->
<script src="../../SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="../../SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<link href="../../SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
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
        <li><a href="findFacultad.php">REPORTES</a></li>
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
          <li><a href="findFacultad.php">FACULTAD</a></li>
          <li><a href="findYear.php">AÑO</a></li>
          <li><a href="findMounth.php">MES</a></li>
        </ul>
        <!-- InstanceEndEditable -->
        <div class="salto"></div>
      </div>
      <h1><!-- InstanceBeginEditable name="Título" -->REPORTES POR MES<!-- InstanceEndEditable --></h1>
      <!-- InstanceBeginEditable name="Contenido" -->
      <form id="form1" name="form1" method="post" action="reportMounth.php">
        <table border="0" align="center">
          <tr>
            <td valign="top">Mes:</td>
            <td valign="top"><span id="spryselect1">
              <select name="mes" id="mes">
                <option>Seleccione...</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
              </select>
              <br />
              <span class="selectRequiredMsg">Seleccione un elemento.</span></span></td>
            <td valign="top"><span id="vanio">
            <input name="anio" type="text" id="anio" size="4" maxlength="4" /><br />
            <span class="textfieldRequiredMsg">Se necesita un valor.</span><span class="textfieldInvalidFormatMsg">Formato no válido.</span><span class="textfieldMinCharsMsg">No se cumple el mínimo de caracteres.</span><span class="textfieldMaxCharsMsg">Se ha superado el número máximo de caracteres.</span><span class="textfieldMinValueMsg">Es inferior al mínimo permitido.</span></span></td>
            <td valign="top"><input type="image" name="imageField" id="imageField" src="../../img/excel.png" /></td>
          </tr>
        </table>
      </form>
      <p>&nbsp;</p>
      <script type="text/javascript">
<!--
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1");
var sprytextfield1 = new Spry.Widget.ValidationTextField("vanio", "integer", {minChars:4, maxChars:4, minValue:2013});
//-->
      </script>
    <!-- InstanceEndEditable --></div>
  </div>
  <div id="pie">UNIVERSIDAD MAYOR DE SAN ANDRÉS<br />
    BIBLIOTECA CENTRAL</div>
</div>
</body>
<!-- InstanceEnd --></html>