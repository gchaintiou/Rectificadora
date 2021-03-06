<?php
session_start();
require_once "../Rectificadora/Librerias/grsys.php";
require_once "../Rectificadora/Librerias/javalib.php";
require_once "../Rectificadora/Librerias/funciones.php";

// Rutas
$_SESSION['address']= "10.0.0.200";
//$_SESSION['address']= "localhost";
//$_SESSION['address']= "192.168.0.14";
$_SESSION['raiz']="http://".$_SESSION['address']."/Rectificadora/";
$_SESSION['imagenes']="http://".$_SESSION['address']."/Rectificadora/Imagenes";
$_SESSION['menu']="http://".$_SESSION['address']."/Rectificadora/Js/stmenu.js";
$_SESSION['pathroot'] = getcwd();
$_SESSION['debug'] = false;
$_SESSION['user_gr'] = new GR();
Debugger('INICIO DE LA APLICACION');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>INICIO</TITLE>

<!-- InstanceEndEditable -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- InstanceBeginEditable name="head" -->

<!-- InstanceEndEditable -->
<style type="text/css">
<!--
body {
	background-color: #172047;
}
-->
</style>
<script type="text/javascript" src="<?php echo $_SESSION['menu']?>"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</HEAD>

<BODY>
<table width="98%" align="center">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	 	<!-- InstanceBeginEditable name="Data" -->
		<!-- InstanceEndEditable -->
    </td>
  </tr>
  <tr>
    <td width="1%" class="celda_menu">
    <?php
    include '../Rectificadora/menu.php';
    ?>
    </td>
    <td width="100%" rowspan="2" valign="top" class="celda_cuerpo">
		 <!-- InstanceBeginEditable name="Trabajo" -->
<div class="titulo1">Usar La Versión:</div>
<div>&nbsp;</div>
<table width="50%" align="center">
	<td width="45%" align="right"><input type="button" id="completa" value="  Completa  " onClick="GRVersionCompleta()"></td>
	<td width="10%">&nbsp;</td>
	<td width="45%" align="left"><input type="button" id="completa" value=" Sólo Taller " onClick="GRVersionTaller()"></td>
</table>
<!-- InstanceEndEditable -->
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
    		<div align="center" class="pie"><?php echo "Joaquín V. Gonzalez 769 - Santa Rosa - La Pampa"?></div>
    		<div align="center" class="pie">Tel: 02954-424916</div>
         <div align="center" class="pie">e-mail: gilesrec@gopertec.com.ar</div>
    </td>
  </tr>
</table>

<iframe id="myframe" name="myframe" src="" frameborder="0" framespacing="0" scrolling="auto" border="0" style="background:#0F0; position:absolute; left:500px; top:300px; width:500px; height:500px; z-index:5; visibility:hidden;"></iframe>

</BODY>
<!-- InstanceEnd --></HTML>
