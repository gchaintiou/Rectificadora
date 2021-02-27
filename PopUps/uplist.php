<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))
	{
	?>
	<script language="javascript"> window.parent.location.href = 'index.php'; </script>
	<?php
	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];

$opc = $_GET['opc'];
if(isset($_GET['origen']))		$origen = $_GET['origen'];
if(isset($_GET['destino']))	$destino = $_GET['destino'];
if(isset($_GET['indice']))		$indice = $_GET['indice'];
if(isset($_GET['numero']))		$numero = $_GET['numero'];
if(isset($_GET['imp']))			$importe = $_GET['imp'];
if(isset($_GET['lista']))		$lista = $_GET['lista'];
if(isset($_GET['motor']))		$motor = $_GET['motor'];
if(isset($_GET['rubro']))		$rubro = $_GET['rubro'];
isset($_GET['mostrar']) ? $mostrar=1 : $mostrar=0;
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
$conex = $user_gr->GetConex();
switch($opc)
	{
	case 0:	// Global MOB
		$result1 = $conex->Execute("SELECT * FROM mobd WHERE columna=$origen ORDER BY numero ASC");
		$result2 = $conex->Execute("SELECT * FROM mobd WHERE columna=$destino ORDER BY numero ASC");
		
		while(!$result1->EOF)
			{
			$numero = $result2->fields['numero'];
			$columna = $result2->fields['columna'];
			$importe = $result1->fields['importe'] * $indice;
		
			$conex->Execute("UPDATE mobd SET importe=$importe WHERE numero=$numero AND columna=$columna");
			$result1->MoveNext();
			$result2->MoveNext();
			}
		break;

	case 1:	// Item MOB
		$query = "UPDATE mobd SET importe=$importe WHERE numero=$numero AND columna=$lista";
		$conex->Execute($query);
		break;

	case 2:	// Global MAT
		if($mostrar)
			{
			?>
			<div align="center" class="titulo2">Por favor ingrese los datos necesarios:</div>
			<div>&nbsp;</div>
			<table width="50%" align="center">
				<tr>
					<td width="40%" align="left" class="head_item_std3">Lista Origen</td>
					<td width="20%" align="center" class="head_item_std3">Indice</td>
					<td width="40%" align="right" class="head_item_std3">Lista Destino</td>
				</tr>
				<tr>
					<td width="40%" align="right" class="etiqueta1">
					<select id="lista1" name="lista1">
					<option value="0">Seleccionar Lista</option>
					<?php
					$lista=1;
					while($lista<5)
						{
						?>
						<option value="<?php echo $lista?>">Lista <?php echo $lista?></option>
						<?php
						$lista++;
						}
					?>
					</select></td>
					<td width="20%" align="center" class="etiqueta1"><input id="indice" name="indice" type="text" value="1.00" size="5" maxlength="5"/></td>
					<td width="40%" align="left" class="etiqueta1">
					<select id="lista2" name="lista2">
					<option value="0">Seleccionar Lista</option>
					<?php
					$lista=1;
					while($lista<5)
						{
						?>
						<option value="<?php echo $lista?>">Lista <?php echo $lista?></option>
						<?php
						$lista++;
						}
					?>
					</select>
					</td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<div align="center"><input type="button" name="ok" id="ok" value="Aceptar" onClick="parent.ventana.hide()"/></div>
			<?php
			}
		else
			{
			$precio_origen = "precio".$origen;
			$precio_destino = "precio".$destino;
	
			$query = "SELECT nro_motor, item, numero, $precio_origen AS precio FROM mat";
			if($motor)	$query = $query." WHERE nro_motor=$motor";
			if($rubro) {
				if($motor)	$query = $query." AND item=$rubro";
				else			$query = $query." WHERE item=$rubro";
			}
			$query = $query." ORDER BY codigo ASC";
			$result1 = $conex->Execute($query);
/*			
			$handle = fopen("/var/www/logGR.txt", "w+");
			fwrite($handle, $query);
			fclose($handle);
*/			
			while(!$result1->EOF)
				{
				$importe = $result1->fields['precio'] * $indice;
				$motor=$result1->fields['nro_motor'];
				$rubro=$result1->fields['item'];
				$numero=$result1->fields['numero'];
	
				$query = "UPDATE mat SET $precio_destino=$importe WHERE nro_motor=$motor AND item=$rubro AND numero=$numero";
				$conex->Execute($query);
				$result1->MoveNext();
				}
			}
		break;
	case 3:	// Item MAT
		break;
	}
?>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
