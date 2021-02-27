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
if($user_gr->GetTipo()==OT)	$ot=1;
else									$ot=0;
isset($_GET['caso']) ? $caso=$_GET['caso'] : $caso=0;

if(!$caso)
	{
	?>
	<div align="center" class="titulo2"><?php echo "Qué tipo de item desea agregar?"?></div>
	<p>&nbsp;</p>
	<form name="form2" method="post" action="newitem.php?caso=1<?php if($ot) echo "&ot=1"?>">
		<div align="center"><input type="submit" name="mob" id="mob" value="Mano de Obra"></div>
	</form>
   <p>&nbsp;</p>
	<form name="form3" method="post" action="newitem.php?caso=2<?php if($ot) echo "&ot=1"?>">
		<div align="center"><input type="submit" name="mat" id="mat" value="     Material    "></div>
	</form>
	<?php
	}
else if($caso==1 || $caso==2)
	{
	?>
	<input type="hidden" name="fieldcaso" id="fieldcaso" value="<?php echo $caso;?>">
	<div align="center" class="titulo2">Por Favor, Ingrese los Datos Necesarios:</div>
   <div>&nbsp;</div>
	<table width="90%" align="center">
		<tr>
			<td width="30%" class="head_item_std1">Cantidad:</td>
			<td width="70%" class="head_item_std2"><input name="cantidad" type="text" id="cantidad" size="5"></td>
		</tr>
		<tr>
			<td class="head_item_std1">Descripción:</td>
			<td class="head_item_std2"><input name="descripcion" type="text" id="descripcion" size="40" maxlength="40"></td>
		</tr>
		<?php
		if($caso==2)
			{
			?>
			<tr>
				<td class="head_item_std1">Código:</td>
				<td class="head_item_std2"><input name="codigo" type="text" id="codigo" size="10" maxlength="12"></td>
			</tr>
			<?php
			}
		?>
		<tr>
			<td class="head_item_std1">Importe ($):</td>
			<td class="head_item_std2"><input name="importe" type="text" id="importe" size="10" maxlength="10"></td>
		</tr>
	</table>
	<?php
	if($ot && 1==0)
	{
	?>
	<table width="80%" align="center">
		<tr>
			<td width="30%" class="head_item_std1">Confirmado:</td>
			<td width="20%" align="right">No<input name="no_conf" type="checkbox" id="no_conf" value="checkbox" onClick="SetCheck(<?php echo "0"?>)" checked></td>
			<td width="20%" align="left">Si<input name="si_conf" type="checkbox" id="si_conf" value="checkbox" onClick="SetCheck(<?php echo "1"?>)"></td>
			<td width="30%">&nbsp;</td>
		</tr>
	</table>
	<?php
	}
	?>
	<div>&nbsp;</div>
	<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.newitemwin.hide()"></div>
	<?php
	}
else if($caso==3)
	{
	$cant=$_GET['cant'];
	$desc=$_GET['desc'];
	$imp=$_GET['imp'];
	isset($_GET['cod']) ? $cod=$_GET['cod'] : $cod='';
	isset($_GET['MAT']) ? $tipo=MAT : $tipo=MOB;
	$user_gr->ItemAdd($cant, $cod, $desc, $imp, $tipo);
	}
?>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
