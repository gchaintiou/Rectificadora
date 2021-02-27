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

isset($_GET['id']) ? $prov=$_GET['id'] : $prov=0;
isset($_GET['lista']) ? $lista=$_GET['lista'] : $lista=0;
isset($_GET['motor']) ? $motor=$_GET['motor'] : $motor='';
isset($_GET['palabra']) ? $frase=$_GET['palabra'] : $frase='';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Explorar Proveedor</TITLE>

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
	<div align="center" class="titulo1">Seleccione Los Datos Necesarios:</div>
   <div>&nbsp;</div>
	<form action="listmat.php?id=<?php echo $prov; ?>" method="get" name="sel_prov" id="sel_prov">
		<table width="50%" align="center">
			<tr>
				<td width="23%" class="head_item_std1">Proveedor:</td>
				<td width="77%" class="head_item_std2">
					<select name="id" size="1" id="id" onChange="PROVCargarListas()">
	               <option value="0">Seleccionar Proveedor</option>
						<?php
                  $res = Prov_Buscar($conex);
						while(!$res->EOF)
							{
							$id=$res->fields['id'];
							?>
                     <option value="<?php echo $id?>" <?php if($prov == $id){echo "selected=\"selected\"";}?>><?php echo $res->fields['nombre']?></option>
                     <?php
							$res->MoveNext();
							}
						?>
					</select>
				</td>
			</tr>
         <?php
			$res = Prov_BuscarListas($conex, $prov);
			if(!$res->EOF)
				{
				?>
            <tr>
               <td align="right" class="head_item_std1">Rubro:</td>
               <td class="head_item_std2">
               	<select name="lista" size="1" id="lista" onChange="PROVMostrarResultados()">
               		<option value="0">Seleccionar Lista</option>
							<?php
							while(!$res->EOF)
								{
								$numero = $res->fields['numero'];
								?>
								<option value="<?php echo $numero?>" <?php if($lista == $numero){echo "selected=\"selected\"";}?>><?php echo $numero."-".$res->fields['descripcion']?></option>
								<?php
								$res->MoveNext();
								}
							?>
            	   </select>
               </td>
            </tr>
            <?php
            }
            ?>
				<tr>
				<td class="head_item_std1">Motor:</td>
				<td class="head_item_std2">
					<label>
						<input name="motor" type="text" id="motor" size="30" maxlength="30" <?php if($motor != '') echo "value=\"".$motor."\"";?> onChange="PROVMostrarResultados()"/>
					</label>
				</td>
			</tr>
         <tr>
				<td class="head_item_std1">Palabra o Frase:</td>
				<td class="head_item_std2">
					<label>
					<input name="palabra" type="text" id="palabra" size="30" maxlength="30" <?php if($frase != '') echo "value=\"".$frase."\"";?> onChange="PROVMostrarResultados()"/>
					</label>
				</td>
			</tr>
		</table>
	</form>

<?php
if($prov && ($lista || $motor || $frase))
	{
	// Busco todos los items correspondientes a la lista y al proveedor
	$res = Prov_BuscarItems($conex, $prov, $lista, $motor, $frase);
	?>
   <table width="80%"align="center">
      <tr>
	      <td width="8%" class="head_item_std3">Alta</td>
         <td width="16%" class="head_item_std3">Código</td>
         <td width="64%" class="head_item_std3">Descripción</td>
         <td width="12%" class="head_item_std3">Precio($)</td>
		</tr>
	<?php
	$cont=0;
	while(!$res->EOF)
		{
		$estado = $res->fields['estado'];
		$codigo = $res->fields['cup'];
		$desc = $res->fields['descripcion'];
		$precio = $res->fields['precio'];
		$item = $res->fields['item'];
		
		if($cont%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }?>

	      <td width="8%" align="center"><?php if($estado < 4){?><a href="#" onClick="PROVAltaItem(<?php echo $prov?>, <?php echo $item?>)">
				<input type="image" name="editar" id="editar" src="../Imagenes/up.png" />
				</a><?php }?>
         </td>
         <td width="16%" align="left"><?php echo $codigo?></td>
         <td width="64%" align="left"><?php echo $desc?></td>
         <td width="12%" align="right"><?php echo $precio?></td>
		</tr>
      <?php
		$res->MoveNext();
		$cont++;
		}
	}
?>
</table>
<div>&nbsp;</div>
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
