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
isset($_GET['hacer']) ? $hacer=$_GET['hacer'] : $hacer=0;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Editar Motor</TITLE>

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
$conex=$user_gr->GetConex();
switch($hacer)
	{
	case 0:
		MOTMostrarEdicion($conex, 1);
		break;
	case 1:
		{
		$selitems=array();	// Aca guardo los items seleccionados en la edicion
		$delitems=array();	// Aca guardo los items que se quitaron y estan en algun presupuesto estandar
		$pres_afect=array();	// Aca guardo el listado de presupuestos afectados por quitar un item del motor
		$notificar=0;
		$i=0;
		
		// Levanto los datos Necesarios
		$nro_motor = $_POST['motor'];
		$desc_motor = $_POST['desc_motor'];
		$nro_lista = $_POST['lista'];
		
		// Guardo los items seleccionados en la edicion
		$result = $conex->Execute("SELECT numero FROM mobe ORDER BY numero ASC");
		while(!$result->EOF)
			{
			$numero = $result->fields['numero'];
			if(isset($_POST['item'.$numero]))
			$selitems[$i++]=$numero;
			$result->MoveNext();
			}
		
		// Miro si se destildo algun item que esta en algun presupuesto estandar
		$i=0;
		$j=0;
		$result1 = $conex->Execute("SELECT numero FROM motd WHERE nro_motor=$nro_motor ORDER BY numero ASC");
		while(!$result1->EOF)
			{
			$numero = $result1->fields['numero'];
			if(!isset($_POST['item'.$numero]))
				{
				// Busco presupuestos estandar que usen el motor y tengan ese item
				$res = $conex->Execute("SELECT nro_pres FROM pse WHERE nro_motor=$nro_motor");
				if(!$res->EOF)		// Si existe algun presupuesto STD
					{
					$item_reg=0;
					while(!$res->EOF)		// Recorro los presupuestos para ver si tienen ese item
						{
						$marcar_pres=1;
						$pres = $res->fields['nro_pres'];
						$r = $conex->Execute("SELECT numero FROM psd WHERE nro_pres=$pres AND numero=$numero");
												
						if(!$r->EOF)	// Si tiene el item
							{
							for($cont=0; $cont<count($pres_afect); $cont++)	// Si ya esta marcado, no lo marco
								if($pres_afect[$cont] == $pres)	$marcar_pres=0;
							if($marcar_pres)												// Lo marco como afectado
								$pres_afect[$j++] = $pres;
							if(!$item_reg)													// Si no se ha registrado ese item como que afecta, lo hago
								{
								$delitems[$i++] = $numero;
								$item_reg=1;
								$notificar=1;
								}
							}
						$res->MoveNext();
						}
					}
				}
			$result1->MoveNext();
			}
		
		// Muestro como quedara el motor luego de la edicion
		?>
		<div>&nbsp;</div>
		<div align="center" class="titulo2">Seguro que Desea Guardar el Siguiente Motor?</div>
		<div>&nbsp;</div>
		<table width="60%">
			<tr>
				<td width="20%" class="head_item_std1">Descripción:</td>
				<td width="80%" class="head_item_std2"><?php echo $desc_motor?></td>
			</tr>
			<tr>
				<td class="head_item_std1">Número de Lista:</td>
				<td class="head_item_std2"><?php echo $nro_lista?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<table width="50%" align="center">
			<tr>
				<td class="head_std">Items</td>
			</tr>
		<?php
		// Muestro los items seleccionados en la edicion
		$cont=0;
		$cant=count($selitems);
		for($i=0; $i<$cant; $i++,$cont++)
			{
			if($cont%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }?>
				<td><?php echo htmlentities(MOBBuscarDescripcion($conex, $selitems[$i]));?></td>
			</tr>
			<?php
			}
		?>
		</table>
		<?php
		
		// Indico si alguno de los quitados afecta a un presupuesto
		if($notificar)
			{
			MostrarLinea("&nbsp;", "");
			MostrarLinea("ATENCION: Los Siguientes Items Afectan a Presupuestos Estandar y Serán Borrados de los Mismos:", "warning");

			for($i=0; $i<count($delitems); $i++)
				MostrarLinea(htmlentities(MOBBuscarDescripcion($conex, $delitems[$i])), "etiqueta1");

			// Presupuestos estandar afectados
			MostrarLinea("&nbsp;", "");
			MostrarLinea("Presupuestos Afectados:", "warning");
			for($i=0; $i<count($pres_afect); $i++)
				MostrarLinea(htmlentities(PRESTD_BuscarDescripcion($conex, $pres_afect[$i])), "etiqueta1");
			}
		
		
		// Preparo los arrays para enviarlos por url
		$selitems = implode("|", $selitems);
		$delitems = implode("|", $delitems);
		$pres_afect = implode("|", $pres_afect);
		?>
		<form action="editar.php?hacer=2" method="post" name="formedit" id="formedit">
			<input name="nro_motor" type="hidden" value="<?php echo $nro_motor?>">
			<input name="desc_motor" type="hidden" value="<?php echo $desc_motor?>">
			<input name="nro_lista" type="hidden" value="<?php echo $nro_lista?>">
			<input name="selitems" type="hidden" value="<?php echo $selitems?>">
			<input name="delitems" type="hidden" value="<?php echo $delitems?>">
			<input name="pres_afect" type="hidden" value="<?php echo $pres_afect?>">
		</form>
		<table align="center" width="50%">
			<tr>
				<td width="48%" align="right"><input name="cancel" type="button" value="Cancelar" onClick="MOTCancelar()"/></td>
				<td width="5%">&nbsp;</td>
				<td width="47%" align="left"><input name="aceptar" type="button" value="Aceptar" onClick="MOTAceptarEdicion()"/></td>
			</tr>
		</table>
		<?php
		break;
		}
	case 2:
		{
		// Levanto los datos
		$nro_motor = $_POST['nro_motor'];
		$desc_motor = $_POST['desc_motor'];
		$nro_lista = $_POST['nro_lista'];
		$selitems=$_POST['selitems'];
		$delitems=$_POST['delitems'];
		$pres_afect=$_POST['pres_afect'];
		
		// Exploto los Arrays
		$selitems=explode("|", $selitems);
		$delitems=explode("|", $delitems);
		$pres_afect=explode("|", $pres_afect);

		// Borro los items, si es necesario, de cada presupuesto
		for($i=0; $i<count($pres_afect); $i++)
			{
			$pres=$pres_afect[$i];
			$cant=count($delitems);
			while($cant)
				{
				$numero = $delitems[$cant-1];
				$conex->Execute("DELETE FROM psd WHERE item=0 AND numero=$numero AND nro_pres=$pres");
				$cant--;
				}
			}
		
		// Actualizo la lista y la descripcion
		$conex->Execute("UPDATE mote SET desc_motor='$desc_motor', nro_lista=$nro_lista WHERE nro_motor=$nro_motor");
		
		// Actualizo los items del motor.... finalmente....
		$conex->Execute("DELETE FROM motd WHERE nro_motor=$nro_motor");
		for($i=0; $i<count($selitems); $i++)
			{
			$numero=$selitems[$i];
			$conex->Execute("INSERT INTO motd(nro_motor, numero) VALUES($nro_motor, $numero)");
			}
		
		MostrarLinea("Los Datos Han Sido Guardados Con Éxito!... Qué Desea hacer?", "titulo2");
		?>
		<p>&nbsp;</p>
		<table align="center" width="50%">
			<tr>
				<td width="48%" align="right"><input name="cancel" type="button" value="  Cancelar  " onClick="MOTCancelar()"/></td>
				<td width="5%">&nbsp;</td>
				<td width="47%" align="left"><input name="aceptar" type="button" value="Nueva Edicion" onClick="MOTNewEdit()"/></td>
			</tr>
		</table>
		<?php
		break;
		}
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
