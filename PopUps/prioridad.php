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
<div align="center" class="titulo2">Prioridad:
	<select id="prioridad" name="prioridad">
		<?php
		$prioridad=0;
		while($prioridad<10)
			{
			?>
			<option value="<?php echo $prioridad?>" <?php if($prioridad==5) echo "selected";?>><?php echo $prioridad?></option>
			<?php
			$prioridad++;
			}
		?>
	</select>
</div>
<div>&nbsp;</div>
<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.ventana.hide()"></div>
<div>&nbsp;</div>
<div align="center" class="print_head_tabla">Nota: 0=inactiva ... 9=inmediato</div>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
