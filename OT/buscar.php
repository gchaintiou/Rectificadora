<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))
	{
	?>
	<script language="javascript"> window.parent.location.href = '../index.php'; </script>
	<?php
	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];

isset($_GET['buscado']) ? $buscado=1 : $buscado=0;
isset($_GET['nro']) ? $nro=$_GET['nro'] : $nro=0;
isset($_GET['pres']) ? $pres = $_GET['pres'] : $pres=0;
isset($_GET['cargado']) ? $cargado=1 : $cargado=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base-ot.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->
<TITLE>Orden de Trabajo</TITLE>
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
<link href="../estilos.css" rel="stylesheet" type="text/css">
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
    <td width="0%" class="celda_menu_ot">
    <?php
    include '../menu.php';
    ?>
    </td>
    <td width="100%" rowspan="2" valign="top" class="celda_cuerpo_ot"><!-- InstanceBeginEditable name="Trabajo" -->
<div align="center" class="titulo1"><?php if($nro) echo "ORDEN DE TRABAJO SELECCIONADA:";?></div>
<?php
if(!$buscado && !$nro) {
   PantallaBusqueda(OT);
}
else if($buscado && !$nro && !$pres) {
   MostrarBusqueda($user_gr->GetConex(), OT);
}
	
else if(!$nro && $pres) {
	$user_gr->SetTipo(OT);
	$user_gr->SetNro();
	$user_gr->SetPrioridadOT($_GET['prioridad']);
	$user_gr->GuardarPresOT();
}
else {
   $user_gr->MostrarPresOT(OT, $nro, NO_EDIT, $cargado);
}
?>
    <!-- InstanceEndEditable --></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
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
