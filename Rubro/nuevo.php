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
isset($_GET['guardar']) ? $guardar=1 : $guardar=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Nuevo Rubro</TITLE>

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
    <td width="1%" class="celda_menu">
    <?php
    include '../menu.php';
    ?>
    </td>
    <td width="100%" rowspan="2" valign="top" class="celda_cuerpo">
		 <!-- InstanceBeginEditable name="Trabajo" -->
<?php
if(!$guardar)
	{
	?>
	<div align="center" class="titulo1">Ingrese los Datos Necesarios Para el Nuevo Rubro:</div>
	<div>&nbsp;</div>
	<form action="nuevo.php?guardar" method="post" name="formrubro" id="formrubro">
	<table width="50%" align="center">
		<tr>
			<td width="40%" class="head_item_std1">Descripcion del Rubro:</td>
			<td width="60%" class="head_item_std2"><input name="desc_rubro" id="desc_rubro" type="text" size="40" maxlength="40" /></td>
 		</tr>
		<tr>
			<td width="40%" class="head_item_std1">Sección:</td>
			<td width="60%" class="head_item_std2">
				<select name="seccion" size="1" id="seccion">
					<option value="0">Seleccionar Seccion</option>
					<?php
					$result = MOB_BuscarSecciones($user_gr->GetConex());
					while(!$result->EOF)
						{
						$nro = $result->fields['nro'];
						$desc = $result->fields['descripcion'];
						?>
						<option value="<?php echo $nro?>"><?php echo htmlentities($desc)?></option>
						<?php
						$result->MoveNext();
						}
					?>
				</select>
			</td>
 		</tr>
	</table>
	</form>
	<div>&nbsp;</div>
	<table align="center" width="50%">
		<tr>
			<td width="45%" align="right"><input name="cancel" type="button" value="Cancelar" onClick="RUBCancelar()" /></td>
			<td width="10%">&nbsp;</td>
			<td width="45%" align="left"><input name="aceptar" type="button" value="Aceptar" onClick="RUBGuardarNew	()" /></td>
		</tr>
	</table>            
	<?php
	}
else	// Guardo e informo
	{
	$conex = $user_gr->GetConex();
	// Preparo los datos para guardar el rubro
	$res = $conex->Execute("SELECT nro_rubro FROM rub ORDER BY nro_rubro DESC LIMIT 2");
	$res->MoveNext();
	$nro_rubro = $res->fields['nro_rubro']+1;
	$desc_rubro = $_POST['desc_rubro'];
	$seccion = $_POST['seccion'];
	
	// Guardo el rubro
	$conex->Execute("INSERT INTO rub(nro_rubro, desc_rubro, seccion) VALUES($nro_rubro, '$desc_rubro', $seccion)");
	
	// Informo que se guardo
	$result = $conex->Execute("SELECT * FROM rub WHERE nro_rubro=$nro_rubro");
	if(!$result->EOF)	MostrarLinea("El Rubro ".$result->fields['desc_rubro']." Ha Sido Guardado con Éxito", "titulo2");
	else					MostrarLinea("No Se Pudo Guardar El Rubro", "warning");
	?>
	<table align="center" width="50%">
		<tr>
			<td width="48%" align="right"><input name="cancel" type="button" value="Inicio" onClick="RUBCancelar()" /></td>
			<td width="5%">&nbsp;</td>
			<td width="47%" align="left"><input name="aceptar" type="button" value="Nuevo" onClick="RUBNewItem()" /></td>
		</tr>
	</table> 	
	<?php
	}
?>

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
