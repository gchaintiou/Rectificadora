<?
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))	{	?>	<script language="javascript"> window.parent.location.href = 'index.php'; </script>	<?	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex = $user_gr->GetConex();

$prov=$_GET['id'];
$item=$_GET['item'];
isset($_GET['motor']) ? $motor=$_GET['motor'] : $motor=0;
isset($_GET['rubro']) ? $rubro=$_GET['rubro'] : $rubro=0;
isset($_GET['codigo']) ? $codigo=$_GET['codigo'] : $codigo='';
isset($_GET['descripcion']) ? $desc=$_GET['descripcion'] : $desc='';
isset($_GET['asociar']) ? $asociar=$_GET['asociar'] : $asociar=0;
isset($_GET['guardar']) ? $guardar=$_GET['guardar'] : $guardar=0;
isset($_GET['eliminar']) ? $eliminar=$_GET['eliminar'] : $eliminar=0;
if(isset($_GET['numero']))	$numero=$_GET['numero'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Asociar Item</TITLE>

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
// Busco el item seleccionado para el alta
$query = "SELECT * FROM prov_mat WHERE id=$prov AND item=$item";
$res = $conex->Execute($query);

$cup = $res->fields['cup'];
$descripcion = $res->fields['descripcion'];
$precio = $res->fields['precio'];

if(!$asociar && !$guardar && !$eliminar)
	{
	?>
	<div align="center" class="titulo1">Complete los Datos Necesarios Para el Alta o Seleccione Motor y Rubro y Asocie al GR:</div>
	<div>&nbsp;</div>
	<form action="altaitem.php?guardar=1" method="get" name="alta" id="alta">
		<table width="60%" align="center">
			<tr>
				<td><input type="hidden" name="id" id="id" value="<? echo $prov?>"/></td>
				<td><input type="hidden" name="item" id="item" value="<? echo $item?>"/></td>
			</tr>
			<tr>
				<td width="26%" class="head_item_std1">Código Proveedor:</td>
				<td width="44%" class="head_item_std2"><? echo $cup?></td>
			</tr>
			<tr>
				<td class="head_item_std1">Descripción Proveedor:</td>
				<td class="head_item_std2"><? echo $descripcion?></td>
			</tr>
			<tr>
				<td class="head_item_std1">Motor:</td>
				<td class="head_item_std2">
					<select name="motor" size="1" id="motor" onChange="PROVRefrezcarPagina(<? echo $prov?>, <? echo $item?>)">
						<option value="0">Seleccionar Motor</option>
						<?
						$res = BuscarMotores($conex);
						while(!$res->EOF)
							{
							$nro_motor = $res->fields['nro_motor'];
							$desc_motor = $res->fields['desc_motor'];
							?>
							<option value="<? echo $nro_motor?>" <? if($motor == $nro_motor){echo "selected=\"selected\"";}?>><? echo $desc_motor?></option>
							<?
							$res->MoveNext();	
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="head_item_std1">Rubro:</td>
				<td class="head_item_std2">
					<select name="rubro" size="1" id="rubro" onChange="PROVRefrezcarPagina(<? echo $prov?>, <? echo $item?>)">
						<option value="0">Seleccionar Rubro</option>
						<?
						$res = BuscarRubro($conex, 0);
						while(!$res->EOF)
							{
							$nro_rubro = $res->fields['nro_rubro'];
							$desc_rubro = $res->fields['desc_rubro'];
							?>
							<option value="<? echo $nro_rubro?>" <? if($rubro == $nro_rubro){echo "selected=\"selected\"";}?>><? echo $desc_rubro?></option>
							<?
							$res->MoveNext();	
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="head_item_std1">Código:</td>
				<td class="head_item_std2"><label>
				  <input name="codigo" type="text" id="codigo" size="50" maxlength="100" value="<? echo $cup?>" />
				</label></td>
			</tr>
			<tr>
				<td class="head_item_std1">Descripción:</td>
				<td class="head_item_std2"><label>
				  <input name="descripcion" type="text" id="descripcion" size="50" maxlength="100" value="<? echo $descripcion?>" />
				</label></td>
			</tr>
		</table>
      <div>&nbsp;</div>
		<table width="70%" align="center">
		  <tr>
				<td width="15%" class="head_item_std1">Precio 1:</td>
				<td width="10%" class="head_item_std2"><label>
				  <input name="precio1" type="text" id="precio1" size="10" maxlength="9" />
				</label></td>
				<td width="15%" class="head_item_std1">Precio 2:</td>
				<td width="10%" class="head_item_std2"><label>
				  <input name="precio2" type="text" id="precio2" size="10" maxlength="9" />
				</label></td>
				<td width="15%" class="head_item_std1">Precio 3:</td>
				<td width="10%" class="head_item_std2"><label>
				  <input name="precio3" type="text" id="precio3" size="10" maxlength="9" />
				</label></td>
				<td width="15%" class="head_item_std1">Precio 4:</td>      
				<td width="10%" class="head_item_std2"><label>
				  <input name="precio4" type="text" id="precio4" size="10" maxlength="9" value="<? echo $precio?>"/>
				</label></td>
			</tr>
		</table>
		<div>&nbsp;</div>
		<table width="50%" border="0" align="center">
			<tr>
				<td width="34%">&nbsp;</td>
				<td width="14%"><input type="button" name="cancel" id="cancel" value="   Cancelar   " onClick="PROVGoBack(<? echo $prov?>)"/></td>
				<td width="5%"></td>
				<td width="47%">
						<?
						if($motor && $rubro)
							{
							?>
							<label>
							  <input type="button" name="aceptar" id="aceptar" value="    Dar Alta    " onClick="PROVGuardar()"/>
							</label>
							<?
							}
							?>
				</td>
			</tr>
		</table>
	<?	
   // Si se designo motor y rubro, posibilito asociación con item del GR
   if($motor && $rubro && !$eliminar)
      {
		if(Prov_PosibleAsociar($conex, $prov, $cup, $motor, $rubro))	// Si es posible una asociacion con ese motor y rubro
			{
			$res = Prov_BuscarParaAsociar($conex, $motor, $rubro);
			if(!$res->EOF)
				{
				?>
				<div>&nbsp;</div>
				<div align="left" class="titulo2">O Bien, Asociar al Material:</div>
				<table width="50%" border="1" align="center">
				<tr>
					<td width="12%" class="head_item_std3">Asociar</td>
					<td width="21%" class="head_item_std3">Código</td>
					<td width="67%" class="head_item_std3">Descripción</td>
				</tr>
				<?
				$cont=0;
				while(!$res->EOF)
					{
					$numero = $res->fields['numero'];
					$codigo = $res->fields['codigo'];
					$desc_mat = $res->fields['desc_mat'];
	
					if($cont%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<? } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<? }?>
					<td align="center">
					<a href="#" onClick="AsociarItem(<? echo $prov?>, <? echo $item?>, <? echo $numero?>); return false">
					<input type="image" name="editar" id="editar" src="../Imagenes/group.png" />
					</a>
					</td>
					<td align="left"><? echo $codigo?></td>
					<td align="left"><? echo $desc_mat?></td>
					</tr>
					<?
					$res->MoveNext();
					$cont++;
					}
				}
			}
		else																				// De lo contrario
			{
			?>
         <div>&nbsp;</div>
			<div align="center" class="titulo2">El Item ya está Asociado a ese Motor y Rubro...</div>
			<div>&nbsp;</div>
			<div align="center">
			<input type="button" name="quitar" id="quitar" value="Eliminar Asociacion" onClick="PROVEliminarAsociacion(<? echo $prov?>, <? echo $item?>, <? echo $motor?>, <? echo $rubro?>)"/>
			</div>
         <?
			}
      }
	?>
   </table>
   <?
	}
	?>
</form>
<div>&nbsp;</div>
<?
if($asociar)
	{
	Prov_AsociarItems($conex, $prov, $item, $motor, $rubro, $numero);
	
	$res1 = $conex->Execute("SELECT id_prov FROM mat WHERE nro_motor=$motor AND item=$rubro AND numero=$numero");
	if($res1->fields['id_prov'] != 0)
		{
		$res2 = $conex->Execute("SELECT estado FROM prov_mat WHERE id=$prov AND item=$item");
		if($res2->fields['estado'] == 1)
			MostrarLinea("El Item fue Asociado Correctamente", "titulo2");
		else
			MostrarLinea("Error al Asociar el Item", "warning");		
		}
	else
		MostrarLinea("Error al Asociar el Item", "warning");		
	}
else if($guardar)
	{
	// Verifico que la asociacion no exista
	$query = "SELECT * FROM asocia_mat WHERE motor=$motor AND item=$rubro AND id_prov=$prov AND cup='$cup'";
	$res = $conex->Execute($query);
						
	if(!$res->NumRows())	// No existe, entonces lo asocio y creo como nuevo
		{
		// Busco el numero para nuevo item
		$res = $conex->Execute("SELECT numero FROM mat WHERE nro_motor=$motor AND item=$rubro ORDER BY numero DESC LIMIT 1");
		$numero = $res->fields['numero']+1;
	
		// Lo Guardo
		$precio = array();
		$precio[0]=$_GET['precio1'];
		$precio[1]=$_GET['precio2'];
		$precio[2]=$_GET['precio3'];
		$precio[3]=$_GET['precio4'];
		$numero = Prov_GuardarItem($conex, $prov, $item, $motor, $numero, $rubro, $codigo, $desc, $precio);

		// Asocio
		Prov_AsociarItems($conex, $prov, $item, $motor, $rubro, $numero);

		$res1 = $conex->Execute("SELECT id_prov FROM mat WHERE nro_motor=$motor AND item=$rubro AND numero=$numero");
		if($res1->fields['id_prov'] != 0)
			{
			$res2 = $conex->Execute("SELECT estado FROM prov_mat WHERE id=$prov AND item=$item");
			if($res2->fields['estado'] == 1)
				MostrarLinea("Item Guardado Correctamente", "titulo2");
			else
				MostrarLinea("Error al Guardar Item", "warning");
			}
		else
			MostrarLinea("Error al Guardar Item", "warning");
		}
	else
		MostrarLinea("El Item ya Existe en la Base de Datos", "warning");
	}
else if($eliminar)
	{
	// Busco el numero que tiene el item
	$res = $conex->Execute("SELECT numero FROM asocia_mat WHERE id_prov=$prov AND cup='$cup' AND motor=$motor AND item=$rubro");
	$numero = $res->fields['numero'];
	// Le quito los datos de asociacion en la tabla mat
	$conex->Execute("UPDATE mat SET id_prov=0, `update`='0000-00-00' WHERE nro_motor=$motor AND item=$rubro AND numero=$numero");
	// Elimino la asociacion de la tabla asocia_mat
	$conex->Execute("DELETE FROM asocia_mat WHERE id_prov=$prov AND cup='$cup' AND motor=$motor AND item=$rubro AND numero=$numero");
	
	$res = $conex->Execute("SELECT id_prov FROM asocia_mat WHERE id_prov=$prov AND cup='$cup' AND motor=$motor AND item=$rubro AND numero=$numero");
	if($res->EOF)
		{
		MostrarLinea("Operacion Realizada con Exito", "titulo2");
		$res = $conex->Execute("SELECT * FROM asocia_mat WHERE id_prov=$prov AND cup='$cup'");
		if($res->EOF)
			$conex->Execute("UPDATE prov_mat SET estado=0 WHERE id=$prov AND cup='$cup'");
		}
	else
		MostrarLinea("Error, no se Pudo Llevar a Cabo la Operación", "warning");
	}

if($asociar || $guardar || $eliminar)
	{
	?>
   <div align="center">
     <label>
       <input type="button" name="ok" id="ok" value="   Aceptar   " onClick="PROVGoBack(<? echo $prov?>)"/>
     </label>
   </div>
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
