<?php
require_once "../Librerias/grsys.php";
session_start();
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
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
	$hacer = $_GET['hacer'];
	if(isset($_GET['desc']))	$new_desc = $_GET['desc'];
	if(isset($_GET['total']))	$new_total = $_GET['total'];
	if($hacer == 0)
		{
		?>
		<div align="center" class="titulo2">Por Favor, Ingrese los Ajustes Necesarios:</div>
		<div>&nbsp;</div>
		<table width="60%" align="center">
			<tr>
				<td width="50%" class="head_item_std1">Descuento:</td>
				<td width="50%" class="head_item_std2"><input name="fieldnewdesc" type="text" id="fieldnewdesc" value="<?php echo $user_gr->GetDescuento() ?>" size="10" /></td>
			</tr>
			<tr>
				<td class="head_item_std1">Total:</td>
				<td class="head_item_std2"><input name="fieldnewtotal" type="text" id="fieldnewtotal" value="<?php echo sprintf("%.2f", $user_gr->GetSubTotal()*(1-$user_gr->GetDescuento()/100))?>" size="10" /></td>
			</tr>
		</table>
		<div>&nbsp;</div>
		<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.win.hide()"/></div>
		<?php
		}
	else if($hacer == 1)
		{
		$desc = $user_gr->GetDescuento();
		$total = $user_gr->GetSubTotal()*(1-$user_gr->GetDescuento()/100);
		
		if($new_desc!=$desc || $new_total!=$total)
			$user_gr->ActualizarTotales($new_desc, $new_total);
		}
	?>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
