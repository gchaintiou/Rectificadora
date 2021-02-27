<?
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))
	{
	?>
	<script language="javascript"> window.parent.location.href = '../index.php'; </script>
	<?
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

<TITLE>Editar Rubro</TITLE>

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
<script type="text/javascript" src="<? echo $_SESSION['menu']?>"></script>
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
<?
$conex = $user_gr->GetConex();
if(!$guardar)
	{
	?>
	<div align="center" class="titulo1">Seleccione los Datos Necesarios Para la Edición:</div>
	<div>&nbsp;</div>
	<form action="editar.php?guardar" method="post" name="FormEdit" id="FormEdit">
		<table width="80%" align="center">
			<tr>
			<td width="50%" class="head_item_std1" align="left">Seleccione el Rubro a Editar:
			<select name="rubro" id="rubro" size="1" onChange="RUBCargarDescripcion()">
			<option value="0">Seleccionar Rubro</option>
			<?
			$result = BuscarRubro($conex, 0);
			while(!$result->EOF)
				{
				?>
				<option value="<? echo $result->fields['nro_rubro']?>"><? echo $result->fields['desc_rubro']?></option>
				<?
				$result->MoveNext();
				}
			?>
			</select></td>
			<td width="50%" class="head_item_std2"><strong>Modificar a:</strong><input name="desc" type="text" id="desc" size="40" maxlength="40" value="" /></td>
			<tr>
		</table>
	</form>
	<div>&nbsp;</div>
	<table align="center" width="50%">
		<tr>
		<td width="48%" align="right"><input name="cancel" type="button" value="  Cancelar  " onClick="RUBCancelar()" /></td>
		<td width="5%">&nbsp;</td>
		<td width="47%" align="left"><input name="aceptar" type="button" value="   Aceptar   " onClick="RUBAceptarEdit()" /></td>
		</tr>
	</table>
	<?
	}
else
	{
	// Preparo los datos necesarios
	$rubro = $_POST['rubro'];
	$descripcion = $_POST['desc'];
	$desc_ant = BuscarRubro($conex, $rubro);
	
	// Realizo la modificacion del rubro
	$conex->Execute("UPDATE rub SET desc_rubro='$descripcion' WHERE nro_rubro=$rubro");
	
	// Levanto el dato de la tabla e informo la modificacion
	MostrarLinea("La descripción Ha Sido Modificada de: ".$desc_ant." a ".BuscarRubro($conex, $rubro), "titulo2")
	?>
	<div>&nbsp;</div>
	<table align="center" width="50%">
		<tr>
		<td width="45%" align="right"><input name="cancel" type="button" value="   Inicio   " onClick="RUBCancelar()" /></td>
		<td width="10%">&nbsp;</td>
		<td width="45%" align="left"><input name="aceptar" type="button" value="    Nuevo    " onClick="RUBNewEdit()" /></td>
		</tr>
	</table>
	<?
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
