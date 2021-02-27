<script language="javascript">
function SetChecked(opcion)
	{
	var completo = document.getElementById("completo");
	var parcial = document.getElementById("parcial");
	var final = document.getElementById("final");
	
	switch(opcion)
		{
		case 1:
			completo.checked = true;
			parcial.checked = false;
			final.checked = false;
			break;
		case 2:
			completo.checked = false;
			parcial.checked = true;
			final.checked = false;
			break;
		case 3:
			completo.checked = false;
			parcial.checked = false;
			final.checked = true;
			break;
		}
	document.getElementById("nivel").value=opcion;
	return true;
	}
</script>

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
	<div align="center" class="titulo2">Seleccione el Nivel de Detalles para la Impresión</div>
	<div>&nbsp;</div>
	<input type="hidden" name="nivel" id="nivel" value="1">
	<table width="90%" align="center">
		<tr>
			<td width="2%" class="head_item_std1"><input id="completo" type="radio" value="" onClick="SetChecked(<?php echo "1"?>)" checked></td>
			<td width="98%" class="head_item_std2">Completo: con importes, subtotales y total</td>
		</tr>
		<tr>
			<td class="head_item_std1"><input id="parcial" type="radio" value="" onClick="SetChecked(<?php echo "2"?>)"></td>
			<td class="head_item_std2">Parcial: con subtotales y total</td>
		</tr>
		<tr>
			<td class="head_item_std1"><input id="final" type="radio" value="" onClick="SetChecked(<?php echo "3"?>)"></td>
			<td class="head_item_std2">Final: sólo con total</td>
		</tr>
	</table>
	<div>&nbsp;</div>
	<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.ventana.hide()"/></div>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
