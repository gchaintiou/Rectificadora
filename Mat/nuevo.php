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

<TITLE>Nuevo Item MAT</TITLE>

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
<div align="center" class="titulo1"><?php if(!$guardar)	echo "Por favor, Ingrese los Datos Necesarios Para el Material:"?></div>
<?php
$conex = $user_gr->GetConex();
if(!$guardar)
	{
	// Busco todos los datos necesarios
	$res1 = BuscarMotores($conex);
	$res2 = BuscarRubro($conex, 0);
	?>
   <div>&nbsp;</div>
	<form action="nuevo.php?guardar" method="post" name="newmat" id="newmat">
      <table width="50%" align="center">
         <tr>
            <td width="25%" class="head_item_std1">Motor:</td>
				<td width="75%" class="head_item_std2">
					<select name="motor" id="motor">
						<option value="0">Seleccionar Motor</option>
						<?php
						while(!$res1->EOF)
						{
						?>
						<option value="<?php echo $res1->fields['nro_motor']?>"><?php echo $res1->fields['desc_motor']?></option>
						<?php
						$res1->MoveNext();
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
            <td class="head_item_std1">Rubro:</td>
				<td class="head_item_std2">
					<select name="rubro" id="rubro">
						<option value="0">Seleccionar Rubro</option>
						<?php
						while(!$res2->EOF)
						{
						?>
						<option value="<?php echo $res2->fields['nro_rubro']?>"><?php echo $res2->fields['desc_rubro']?></option>
						<?php
						$res2->MoveNext();
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
            <td class="head_item_std1">Codigo:</td>
				<td class="head_item_std2"><input name="codigo" type="text" id="codigo" size="10" maxlength="10" /></td>
			</tr>
			<tr>
            <td class="head_item_std1">Descripcion:</td>
				<td class="head_item_std2"><input name="descripcion" type="text" id="descripcion" size="45" maxlength="45" /></td>
			</tr>
			<tr>
            <td class="head_item_std1">Precio 1:</td>
				<td class="head_item_std2"><input name="precio1" type="text" id="precio1" size="5" maxlength="8" /></td>
			</tr>
			<tr>
            <td class="head_item_std1">Precio 2:</td>
				<td class="head_item_std2"><input name="precio2" type="text" id="precio2" size="5" /></td>
			</tr>
			<tr>
            <td class="head_item_std1">Precio 3:</td>
				<td class="head_item_std2"><input name="precio3" type="text" id="precio3" size="5" maxlength="8" /></td>
			</tr>
			<tr>
            <td class="head_item_std1">Precio 4:</td>
				<td class="head_item_std2"><input name="precio4" type="text" id="precio4" size="5" maxlength="8" /></td>
         </tr>
      </table>
		<div>&nbsp;</div>
      <table width="50%" align="center">
         <tr>
             <td width="45%" align="right"><input type="button" name="no" id="no" value="  Cancelar  " onClick="MATCancelar()"/></td>
            <td width="10%">&nbsp;</td>
            <td width="45%" align="left"><input type="button" name="si" id="si" value="    Nuevo    " onClick="MATGuardarNew()"/></td>
         </tr>
      </table>
	</form>
	<?php
	}
else
	{
	// Levanto los Datos y Verifico que Estén Todos
	$motor = $_POST['motor'];
	$rubro = $_POST['rubro'];
	$_POST['codigo'] ? $codigo=$_POST['codigo'] : $codigo=NULL;
	$_POST['descripcion'] ? $descripcion=$_POST['descripcion'] : $descripcion=NULL;
	$_POST['precio1'] ? $precio1=$_POST['precio1'] : $precio1=0;
	$_POST['precio2'] ? $precio2=$_POST['precio2'] : $precio2=0;
	$_POST['precio3'] ? $precio3=$_POST['precio3'] : $precio3=0;
	$_POST['precio4'] ? $precio4=$_POST['precio4'] : $precio4=0;
	
	// Busco el Número que le Corresponde Dentro del Item(item=nro_rubro)
	$result = $conex->Execute("SELECT numero FROM mat WHERE nro_motor=$motor AND item=$rubro ORDER BY numero DESC LIMIT 1");
	$numero = $result->fields['numero']+1;
	
	// Inserto el Nuevo Item en la Tabla
	$query = "INSERT INTO mat(nro_motor, cantidad, item, numero, codigo, desc_mat, precio1, precio2, precio3, precio4)";
	$query = $query." VALUES($motor, 0, $rubro, $numero, '$codigo', '$descripcion', $precio1, $precio2, $precio3, $precio4)";
	
	if($conex->Execute($query))
		MostrarLinea("El Material fue Registrado con Éxito", "titulo2");
	
	MostrarLinea("Desea Ingresar un Nuevo Registro?", "titulo2");
	?>
	<table width="50%" align="center">
      <tr>
         <td width="45%"><input name="no2" type="button" onClick="MATCancelar()" value="  Cancelar  "/></td>
         <td width="10%">&nbsp;</td>
         <td width="45%"><input name="si2" type="button" onClick="MATNewItem()" value="  Aceptar  "/></td>
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
