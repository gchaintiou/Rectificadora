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
isset($_GET['init']) ? $iniciado=1 : $iniciado=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Nuevo Pres. Ciente</TITLE>

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
if(!$iniciado)
	{
	?>
	<div align="center" class="titulo1">INGRESE LOS DATOS PARA EL NUEVO PRESUPUESTO:</div>
	<div>&nbsp;</div>
	<form id="form_newcli" name="form1" method="post" action="nuevo.php?init">
		<table width="80%" align="center">
			<tr>
				<td width="30%" class="head_item_std1">Cliente:</td>
				<td width="70%" class="head_item_std2"><input name="cliente" type="text" id="cliente" size="30" /></td>
			</tr>
			<tr>
				<td class="head_item_std1">Dirección:</td>
				<td class="head_item_std2"><input name="direccion" type="text" id="direccion" size="30" /></td>
			</tr>
			<tr>
				<td class="head_item_std1">Teléfono:</td>
				<td class="head_item_std2"><input name="telefono" type="text" id="telefono" size="30" /></td>
			</tr>
			<tr>
				<td class="head_item_std1">Localidad:</td>
				<td class="head_item_std2"><input name="localidad" type="text" id="localidad" size="30" /></td>
			</tr>
			<tr>
				<td class="head_item_std1">Descuento (%):</td>
				<td class="head_item_std2"><input name="descuento" type="text" id="descuento" size="5" /></td>
			</tr>
			<tr>
				<td class="head_item_std1">Presupuesto Estandar:</td>
				<td class="head_item_std2"><select name="nro_pres" size="1" id="nro_pres">
					<option value="0"><?php echo "Seleccionar Presupuesto";?></option>
						<?php
						$resultado = PRESTD_BuscarPresupuestos($user_gr->GetConex());
						while(!$resultado->EOF)
						{
						?>
						<option value="<?php echo $resultado->fields['nro_pres']?>"><?php echo $resultado->fields['desc_pres']?></option>
						<?php
						$resultado->MoveNext();
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="head_item_std1">Utilizar Descripción:</td>
				<td class="head_item_std2"><input name="descripcion" type="text" id="descripcion" size="40" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="head_item_std2"><input type="checkbox" name="items" id="items" /> Seleccionar Items del Presupuesto Estandar</td>
			</tr>
		</table>
		<div>&nbsp;</div>
		<table width="40%" align="center">
		<tr>
			<td colspan="2" class="head_item_std3" align="center">Incluir Sólo las Siguientes Secciones de Items</td>
		</tr>
		<?php
		$res =  MOB_BuscarSecciones($user_gr->GetConex());
		while(!$res->EOF)
			{
			$numero = $res->fields['nro'];
			$desc = $res->fields['descripcion'];
			?>
			<tr>
				<td width="2%" class="head_item_std1"><input type="checkbox" id="items<?php echo $numero?>" name="items<?php echo $numero?>"/></td>
				<td width="98%" class="head_item_std2"><?php echo htmlentities($desc)?></td>
			</tr>
			<?php
			$res->MoveNext();
			}
		?>
		</table>
		<div>&nbsp;</div>
		<div align="center"><input type="button" name="armar" id="armar" value="  Armar  " onClick="PRECLI_CheckNew()"/></div>
	</form>
	<?php
	}
else
	{
	$campos = array();
	$filtros=0;
	$items_std=0;
	// Recupero los valores de los campos del formulario inicial y los pongo en un array
	$campos[NUM_STD] = $_POST['nro_pres'];					// Número de presupuesto estandar
	$_POST['cliente']=="" ? $campos[CLIENTE]=NULL : $campos[CLIENTE]=$_POST['cliente'];
	$_POST['direccion']=="" ? $campos[DIRECCION]=NULL : $campos[DIRECCION]=$_POST['direccion'];
	$_POST['telefono']=="" ? $campos[TELEFONO]=NULL : $campos[TELEFONO]=$_POST['telefono'];
	$_POST['localidad']=="" ? $campos[LOCALIDAD]=NULL : $campos[LOCALIDAD]=$_POST['localidad'];
	$_POST['descuento']=="" ? $campos[DESCUENTO]=0 : $campos[DESCUENTO]=$_POST['descuento'];
	$_POST['descripcion']=="" ? $campos[DESC_PRES]=PRESTD_BuscarDescripcion($user_gr->GetConex(), $campos[NUM_STD]) : $campos[DESC_PRES]=$_POST['descripcion'];	
	if(isset($_POST['items'])) $items_std=1;
	
	// Chequeo si se pidieron filtros de items y los pongo en un array tambien
	$res =  MOB_BuscarSecciones($user_gr->GetConex());
	$i=1;
	while(!$res->EOF)
		{
		$numero = $res->fields['nro'];
		if(isset($_POST['items'.$numero]))	$filtros |= (1<<($i));
		$i++;
		$res->MoveNext();
		}

	// Continuo con el alta del presupuesto cargando los items y llevando a edicion
	$user_gr->NuevoPresupuestoCLI($campos, $filtros, $items_std);
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
