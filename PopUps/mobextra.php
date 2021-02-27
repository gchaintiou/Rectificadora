<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))	{	?>	<script language="javascript"> window.parent.location.href = '../index.php'; </script>	<?php	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex=$user_gr->GetConex();

isset($_GET['justificar']) ? $justificar = 1 : $justificar=0;
isset($_GET['operario']) ? $operario = $_GET['operario'] : $operario=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base-pop.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->
<TITLE>&nbsp;</TITLE>
<!-- InstanceEndEditable -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../estilos.css" rel="stylesheet" type="text/css">
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<style type="text/css">
<!--
body {
	background-color: #CFF;
}
-->
</style></HEAD>

<BODY>

<!-- InstanceBeginEditable name="Editable" -->
<?php
if($justificar==1)	
    MostrarLinea("Justificación", "head_item_std3");
else						
    MostrarLinea("Nueva tarea para el operario: ".GES_BuscarNombreOperario($conex, $operario), "head_item_std3");
MostrarLinea("&nbsp;", "");
?>
<table width="98%" align="center">
	<tr>
		<td width="20%" class="head_item_std1">Descripción:</td>
		<td width="80%" class="head_item_std2"><input type="text" name="new_mob" id="new_mob" value="" size="50" maxlength="100"</td>
	</tr>
</table>
<div>&nbsp;</div>
<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.ventana.hide()"></div>
<div>&nbsp;</div>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
