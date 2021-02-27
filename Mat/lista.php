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
$conex = $user_gr->GetConex();
isset($_GET['motor']) ? $motor = $_GET['motor'] : $motor = 0;
isset($_GET['rubro']) ? $rubro = $_GET['rubro'] : $rubro = 0;
isset($_GET['numero']) ? $numero = $_GET['numero'] : $numero = 0;
isset($_GET['listar']) ? $listar = 1 : $listar = 0;
isset($_GET['modificar']) ? $modificar = 1 : $modificar = 0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Editar Lista MAT</TITLE>

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
<div align="center" class="titulo1">Seleccione los Filtros que Desee:</div>
<div>&nbsp;</div>
<table width="50%" align="center">
	<tr>
		<td width="28%" class="head_item_std1">Motor:</td>
		<td width="28%" class="head_item_std2">
			<select name="motor" size="1" id="motor" onChange="MATActualizarFiltros()">
			<option value="<?php echo $motor;?>"><?php if($motor) echo BuscarDescripcionMotor($conex, $motor);  else echo "TODOS";?></option>
			<?php
			$result = BuscarMotores($conex);
			while(!$result->EOF)
				{
				$nro_motor = $result->fields['nro_motor'];
				$desc_motor = htmlentities($result->fields['desc_motor']);
				?>
				<option value="<?php echo $nro_motor?>"><?php echo $desc_motor?></option>
				<?php
				$result->MoveNext();
				}
			?>
			</select>
		</td>
		<td width="44%"><input type="button" name="ajustar" id="ajustar" value=" Ajuste Global " onClick="MATAjusteGlobal(<?php echo $motor;?>, <?php echo $rubro;?>)"/></td>
	</tr>
	<tr>
		<td class="head_item_std1">Rubro:</td>
		<td class="head_item_std2">
			<select name="rubro" size="1" id="rubro" onChange="MATActualizarFiltros()">
			<option value="<?php echo $rubro;?>"><?php if($rubro) echo BuscarRubro($conex, $rubro);  else echo "TODOS"?></option>
			<?php
			$result = BuscarRubro($conex,$rubro);
			while(!$result->EOF)
				{
				$nro_rubro = $result->fields['nro_rubro'];
				$desc_rubro = htmlentities($result->fields['desc_rubro']);
				?>
				<option value="<?php echo $nro_rubro?>"><?php echo $desc_rubro?></option>
				<?php
				$result->MoveNext();
				}
			?>
			</select>
		</td>	
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="head_item_std1">Numero:</td>
		<td class="head_item_std2"><input name="selnum" id="selnum" type="text" size="5" value="<?php if($numero) echo $numero;?>" onChange="MATActualizarFiltros()"/></td>
		<td>&nbsp;</td>
	</tr>
</table>

<?php
if($listar && ($motor || $rubro))
	{
	$query = "SELECT mat.numero, mat.codigo, mat.desc_mat, mat.precio1, mat.precio2, mat.precio3, mat.precio4, rub.nro_rubro, rub.desc_rubro, mote.nro_motor, mote.desc_motor";
	$query = $query." FROM mat INNER JOIN rub INNER JOIN mote";
	$query = $query." WHERE mat.item=rub.nro_rubro AND mat.nro_motor=mote.nro_motor";
	
	if($motor)	$query = $query." AND mote.nro_motor=$motor";
	if($rubro)	$query = $query." AND rub.nro_rubro=$rubro";
	if($numero)	$query = $query." AND mat.numero=$numero";
	$query = $query." ORDER BY mat.nro_motor, mat.item, mat.numero ASC";
	$result = $conex->Execute($query);
	?>
	<div>&nbsp;</div>
	<table width="90%" align="center">
		<tr>
			<td width="4%" class="head_item_std3">Editar</td>
			<td width="14%" class="head_item_std3">Codigo</td>
			<td width="50%" class="head_item_std3">Descripcion</td>
			<td width="8%" class="head_item_std3">Imp1 ($)</td>
			<td width="8%" class="head_item_std3">Imp2 ($)</td>
			<td width="8%" class="head_item_std3">Imp3 ($)</td>
			<td width="8%" class="head_item_std3">Imp4 ($)</td>
		</tr>
		<?php
		$cont=0;
		while(!$result->EOF)
			{
			$descripcion = "";
			$num_item = $result->fields['numero'];
			$num_rub = $result->fields['nro_rubro'];
			$num_mot = $result->fields['nro_motor'];
			$codigo = $result->fields['codigo'];
			$desc_rubro = $result->fields['desc_rubro'];
			$desc_mat = htmlentities($result->fields['desc_mat']);
			$desc_motor = $result->fields['desc_motor'];
			$precio1 = $result->fields['precio1'];
			$precio2 = $result->fields['precio2'];
			$precio3 = $result->fields['precio3'];
			$precio4 = $result->fields['precio4'];
			
			if(!$rubro)	$descripcion = $desc_rubro;
			$descripcion = $descripcion." ".$desc_mat;
			if(!$motor)	$descripcion = $descripcion." ".$desc_motor;
			
			$id = $num_mot."-".$num_rub."-".$num_item;
			
			if($cont%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }?>
			
				<td align="center">
				<a href="#" onClick="MATEditItem(<?php echo $num_rub?>, <?php echo $num_item?>, <?php echo "'$desc_mat'"?>, <?php echo "'$codigo'"?>, <?php echo $num_mot?>)">
				<input type="image" name="editar" id="editar" src="../Imagenes/editar.jpg" /></a>
				</td>
				<td align="center"><span id="cod-<?php echo $id?>"><?php echo $codigo?></span></td>
				<td align="left"><span id="desc-<?php echo $id?>"><?php echo $descripcion?></span></td>
				<td align="center"><input type="text" id="precio1-<?php echo $id?>" value="<?php echo $precio1?>" size="5" onChange="MATActualizarPrecio(<?php echo $num_mot?>, <?php echo $num_rub?>, <?php echo $num_item?>, <?php echo "1"?>)"/></td>
				<td align="center"><input type="text" id="precio2-<?php echo $id?>" value="<?php echo $precio2?>" size="5" onChange="MATActualizarPrecio(<?php echo $num_mot?>, <?php echo $num_rub?>, <?php echo $num_item?>, <?php echo "2"?>)"/></td>
				<td align="center"><input type="text" id="precio3-<?php echo $id?>" value="<?php echo $precio3?>" size="5" onChange="MATActualizarPrecio(<?php echo $num_mot?>, <?php echo $num_rub?>, <?php echo $num_item?>, <?php echo "3"?>)"/></td>
				<td align="center"><input type="text" id="precio4-<?php echo $id?>" value="<?php echo $precio4?>" size="5" onChange="MATActualizarPrecio(<?php echo $num_mot?>, <?php echo $num_rub?>, <?php echo $num_item?>, <?php echo "4"?>)"/></td>
			</tr>
			<?php
			$result->MoveNext();
			$cont++;
			}
		?>
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
