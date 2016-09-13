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

$colname_carrera = "-1";
if (isset($_REQUEST['idfac'])) {
  $colname_carrera = $_REQUEST['idfac'];
}
mysql_select_db($database_conex, $conex);
$query_carrera = sprintf("SELECT * FROM carrera WHERE idfac=%s ORDER BY nombre", GetSQLValueString($colname_carrera, "int"));
$carrera = mysql_query($query_carrera, $conex) or die(mysql_error());
$row_carrera = mysql_fetch_assoc($carrera);
$totalRows_carrera = mysql_num_rows($carrera);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<select name="idcar" id="idcar">
  <?php
do {  
?>
  <option value="<?php echo $row_carrera['id']?>"><?php echo $row_carrera['nombre']?></option>
  <?php
} while ($row_carrera = mysql_fetch_assoc($carrera));
  $rows = mysql_num_rows($carrera);
  if($rows > 0) {
      mysql_data_seek($carrera, 0);
	  $row_carrera = mysql_fetch_assoc($carrera);
  }
?>
</select>
</body>
</html>
<?php
mysql_free_result($carrera);
?>
