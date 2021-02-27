<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))	{	?>	<script language="javascript"> window.parent.location.href = '../index.php'; </script>	<?php	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex=$user_gr->GetConex();
isset($_GET['new']) ? $new=1 : $new=0;
isset($_GET['eliminar']) ? $delete=1 : $delete=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Operarios</TITLE>

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
if($new)
	{
	$nombre = $_GET['nombre'];
	$conex->Execute("INSERT INTO operario(nombre) VALUES('$nombre')");
	}
else if($delete)
	{
	$id = $_GET['operario'];
	$conex->Execute("DELETE FROM operario WHERE id=$id");
	}
$operarios = BuscarOperarios($conex);
?>
<div class="titulo1">Operarios en GR:</div>
<div>&nbsp;</div>
<table width="40%" align="center">
	<tr>
		<td width="10%" class="head_item_std3">Id</td>
		<td width="75%" class="head_item_std3">Operario</td>
		<td width="15%" class="head_item_std3">Borrar</td>
	</tr>
	<?php
	while(!$operarios->EOF)
		{
		$id = $operarios->fields['id'];
		$nombre = htmlentities($operarios->fields['nombre']);
		?>
		<tr>
			<td align="center" class="gris_claro"><?php echo $id?></td>
			<td class="gris_claro"><?php echo $nombre?></td>
			<td align="center" class="gris_claro">
				<a href="#" onClick="GESBorrarOperario(<?php echo $id?>, <?php echo "'$nombre'"?>)">
					<img src="../Imagenes/Delete.png" width="25" height="25" alt="imagen">
				</a>
			</td>
		</tr>
		<?php
		$operarios->MoveNext();
		}
		?>
</table>
<div>&nbsp;</div>
<table width="30%" align="center">
	<tr>
		<td colspan="2" class="head_item_std3">Agregar Operario</td>
	</tr>
	<tr>
		<td width="90%" class="head_item_std1"><input id="new_operario" type="text" size="50" maxlength="100"></td>
		<td width="10%" class="head_item_std3">
			<a href="#" onClick="GESNewOperario()">
				<img src="../Imagenes/add.png" width="25" height="25" alt="imagen">
			</a>
		</td>
	</tr>
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
