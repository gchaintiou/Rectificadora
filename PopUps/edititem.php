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
isset($_GET['caso']) ? $caso=$_GET['caso'] : $caso=0;
isset($_GET['guardar']) ? $guardar=1 : $guardar=0;
isset($_GET['enc']) ? $encabezado=1 : $encabezado=0;

// Analizo las variables que paso
isset($_GET['item']) ?	$item=$_GET['item'] : $item=0;
isset($_GET['num']) ?	$numero=$_GET['num'] : $numero=0;
isset($_GET['cant']) ? $cantidad=$_GET['cant'] : $cantidad=0;
isset($_GET['cod']) ? $codigo=str_replace('|', '+', $_GET['cod']) : $codigo='';
isset($_GET['desc']) ?	$descripcion=str_replace('|', '+', $_GET['desc']) : $descripcion='';
isset($_GET['imp']) ? $importe=$_GET['imp'] : $importe=0;
isset($_GET['add']) ? $agregado=$_GET['add'] : $agregado=0;

if(!$encabezado && !$guardar)				// Seteo los campos a mostrar segun corresponda
	{
	// Seteo las vistas de los campos en cero ---> No se ven
	$ver_cant=$ver_desc=$ver_cod=$ver_imp=0;
	if(!$caso)
		{
		if($item)	$caso=MAT;
		else			$caso=MOB;
		}

	switch($caso)
		{
		case MOB:		// Editar un item MOB de presupuesto a cliente
			$ver_cant=1;
			$ver_desc=1;
			$ver_imp=1;
			break;
		case MAT:		// Editar un item MAT de presupuesto a cliente
			$ver_cant=1;
			$ver_desc=1;
			$ver_cod=1;
			$ver_imp=1;
			break;
		case 3:		// Editar un item MOB de la base
			$ver_desc=1;
			break;
		case 4:		// Editar un item MAT de la base
			$ver_desc=1;
			$ver_cod=1;
			break;
		}
	?>

	<div align="center" class="titulo2">Por favor ingrese los datos necesarios:</div>
	<div>&nbsp;</div>
	<table width="90%" align="center">
	<?php
	if($ver_cant)
		{
		?>
		<tr>
			<td width="40%" class="head_item_std1">Cantidad:</td>
			<td width="60" class="head_item_std2"><input name="cantidad" type="text" id="cantidad" value="<?php echo $cantidad?>" size="5" maxlength="4"/></td>
		</tr>
		<?php
		}
	if($ver_desc)
		{
		?>
		<tr>
			<td width="40%" class="head_item_std1">Descripción:</td>
			<td width="60%" class="head_item_std2"><input name="descripcion" type="text" id="descripcion" value="<?php echo $descripcion?>" size="40" maxlength="50"/></td>
		</tr>
		<?php
		}
	if($ver_cod)
		{
		?>
		<tr>
			<td class="head_item_std1">Código:</td>
			<td class="head_item_std2"><input type="text" name="codigo" id="codigo" value="<?php echo $codigo?>"/></td>
		</tr>
		<?php
		}
	if($ver_imp)
		{
		?>
		<tr>
			<td class="head_item_std1">Importe ($):</td>
			<td class="head_item_std2"><input name="importe" type="text" id="importe" value="<?php echo $importe?>" size="8"/></td>
		</tr>
		<?php
		}
	?>
	</table>
	<div>&nbsp;</div>
	<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.ventana.hide()"/></div>
	<?php
	}
else if(!$encabezado && $guardar)		// Guardo el item correspondiente
	$user_gr->ItemEdit($item, $numero, $agregado, $cantidad, $descripcion, $codigo, $importe);
else if($encabezado)
	$user_gr->ItemEncabezadoEdit($item, $descripcion);
	?>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
