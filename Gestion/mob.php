<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))	{	?>	<script language="javascript"> window.parent.location.href = '../index.php'; </script>	<?php	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex=$user_gr->GetConex();

isset($_GET['orden']) ? $orden=$_GET['orden'] : $orden=OT;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<?php
if(isset($_GET['ordenar']) || isset($_GET['ver']))
	{
	?>
	<meta http-equiv="refresh" content="60">
	<?php
	}
?>
<TITLE>Gestión de Producción</TITLE>
<!-- InstanceEndEditable -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="refresh" content="60">
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
MostrarLinea("Gestión de Orden de Producción", "titulo1");

if(isset($_GET['editar']))
	{
	 Debugger("Gestion/Mob.php?editar");
	MostrarLinea("Selección de Tareas", "titulo1");
	MostrarLinea("&nbsp;", "");
	?>
	<form id="produccion" name="produccion" method="post" action="mob.php?operario">
	<?php	
    GES_MostrarItems($conex, $orden, MOB); // Librerias\funciones.php
	?>
	<div>&nbsp;</div>
	<div align="center"><input type="submit" name="ok" value="    Asignar Operarios    " /></div>
	<div>&nbsp;</div>
	</form>
	<?php
	}
else if(isset($_GET['operario']))
	{
        Debugger("Gestion/Mob.php?operario");
	MostrarLinea("Asignación de Operarios", "titulo1");
	Mostrarlinea("&nbsp;", "");
	// Levanto los datos de las tareas seleccionadas
	$query = "SELECT opd.*, otd.cantidad, ote.desc_motor, ote.prioridad FROM opd INNER JOIN otd INNER JOIN ote";
	$query = $query." WHERE opd.estado < 4 AND otd.nro_ot=opd.nro_ot AND otd.item=opd.item AND otd.numero=opd.numero AND otd.agregado=opd.agregado";
	$query = $query." AND ote.nro_ot=opd.nro_ot";
    Debugger($query);
	$tarea = $conex->Execute($query);
	
	if(!$tarea->EOF)
		{
		$cant_items = $tarea->NumRows();
		?>
		<form name="asig_operario" action="" method="get">
		<table width="90%" align="center">
		<tr>
			<td width="8%" class="head_item_std3">OT</td>
			<td width="30%" class="head_item_std3">Descripción</td>
			<td width="5%" class="head_item_std3">Cant.</td>
			<td width="40%" class="head_item_std3">Item</td>
			<td width="5%" class="head_item_std3">Prior.</td>
			<td width="12%" class="head_item_std3">Operario</td>
		</tr>
		<?php
		$i=0;
		while(!$tarea->EOF)
			{
			$nro_ot = $tarea->fields['nro_ot'];
			$desc_ot = $tarea->fields['desc_motor'];
			$item = $tarea->fields['item'];
			$numero = $tarea->fields['numero'];
			$agregado = $tarea->fields['agregado'];
			$cantidad = $tarea->fields['cantidad'];
			$descripcion = htmlentities($tarea->fields['descripcion']);
			$prioridad = $tarea->fields['prioridad'];
			$operario = $tarea->fields['operario'];
			$id = $nro_ot."-".$item."-".$numero."-".$agregado;

			if(($i++)%2)
				{?><tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php }
			else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';"><?php }?>
			<td align="center"><?php echo $nro_ot?></td>
			<td><?php echo $desc_ot?></td>
			<td align="center"><?php echo $cantidad?></td>
			<td><?php echo $descripcion?></td>
			<td align="center"><?php echo $prioridad?></td>
			<td>
				<select name="op<?php echo $i?>" id="operario<?php echo $id?>" onChange="GES_VerificarOperario(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>)">
					<option value="0">Seleccionar</option>
					<?php
					$result = BuscarOperarios($conex);
					while(!$result->EOF)
						{
						$nro = $result->fields['id'];
						$nombre = $result->fields['nombre'];
						?>
						<option value="<?php echo $nro?>" <?php if($nro==$operario) echo "selected"; ?>><?php echo $nombre?></option>
						<?php
						$result->MoveNext();
						}
					?>
				</select>
			</td>
			</tr>
			<?php
			$tarea->MoveNext();
			}
		?>
		</table>
		</form>
		<div>&nbsp;</div>
		<div align="center"><input name="orden" id="orden" type="button" value="  Ordenar Tareas  " onClick="GES_OrdenarTareas(<?php echo $cant_items?>)"></div>		
		<?php
		}
	else
		MostrarLinea("ATENCION: Debe seleccionar alguna tarea para asignar", "warning");
	}
else if(isset($_GET['ordenar']) || isset($_GET['ver']))
	{
        Debugger("Gestion/Mob.php?ordenar o ver");
	// Verifico si se necesita asistencia en alguna tarea    
	$res = $conex->Execute("SELECT descripcion FROM opd WHERE estado=3");
	if(!$res->EOF)
		while(!$res->EOF)
			{
			$descripcion = $res->fields['descripcion'];
			?>
			<script>	POP_Asistencia(<?php echo "'$descripcion'"?>);	</script>
			<?php
			$res->MoveNext();
			}
            MostrarLinea("Ordenar las Tareas de Cada Operario", "titulo1");	
        
		
	if(isset($_GET['id']))
		{
		$id = $_GET['id'];
		}
	
	// Tareas de Todos los Operarios
	$query = "SELECT opd.*, otd.cantidad, ote.desc_motor, ote.prioridad FROM opd INNER JOIN otd INNER JOIN ote";
	$query = $query." WHERE opd.operario > 0 AND opd.estado < 4";
	$query = $query." AND otd.nro_ot=opd.nro_ot AND otd.item=opd.item AND otd.numero=opd.numero AND otd.agregado=opd.agregado";
	$query = $query." AND ote.nro_ot=opd.nro_ot";
	$query = $query." ORDER BY opd.operario, posicion ASC, ote.prioridad DESC, opd.nro_ot ASC";
    Debugger("Ejecuto query tarea");
    Debugger($query);
	$tarea = $conex->Execute($query);

	$operario_ant=0;
	$comienzo=1;
    $i=0;
	Debugger("Recorro tarea");
	while(!$tarea->EOF){
		$operario = $tarea->fields['operario'];
		Debugger("operario = $operario");
		if($operario_ant != $operario){	
                if(!$comienzo){
                    Debugger("if not comienzo");
				    if(!$extras->EOF)
					    {
                            Debugger("Recorro extras");
                            while(!$extras->EOF)
                                {
                                    Debugger("Tomo los datos del registro de extras");
                                    $nro_ot = $extras->fields['nro_ot'];
                                    $desc_ot = "";
                                    $item = $extras->fields['item'];
                                    $numero = $extras->fields['numero'];
                                    $agregado = $extras->fields['agregado'];
                                    $cantidad = 1;
                                    $descripcion = htmlentities($extras->fields['descripcion']);
                                    $prioridad = " ";
                                    $extra = $extras->fields['extra'];
                                    $id = $operario_ant."-".$i;
                                    
                                    if(($i)%2)
                                        {?><tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php }
                                    else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';"><?php }?>
                                        <td align="center"><span id="ot<?php echo $id?>"><?php echo $nro_ot?></span></td>
                                        <td><span id="desc_ot<?php echo $id?>"><?php echo $desc_ot?></span></td>
                                        <td align="center"><span id="cantidad<?php echo $id?>"><?php echo $cantidad?></span></td>
                                        <td><span id="desc<?php echo $id?>"><?php echo $descripcion?></span></td>
                                        <td align="center"><span id="prioridad<?php echo $id?>"><?php echo $prioridad?></span></td>
                                        <td align="center">
                                            <input name="estado<?php echo $id?>" id="estado<?php echo $id?>" type="checkbox" value="" <?php if(GES_CheckEstadoMOB($conex, $nro_ot, $item, $numero, $agregado)==5) echo "checked";?> onchange="GES_ReactivarMOB(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>, <?php echo $operario_ant?>, <?php echo $i?>)">
                                        </td>
                                        <td width="4%" align="center">
                                            <a href="#" onClick="GES_PosicionarMOB(<?php echo $operario_ant?>, <?php echo $i?>, <?php echo "'subir_mob'"?>, <?php echo $nro_ot?>, <?php echo $tope_inf?>)">
                                                <img src="../Imagenes/arriba.png" width="20" height="20" alt="imagen">
                                            </a>
                                        </td>
                                        <td width="4%" align="center">
                                            <a href="#" onClick="GES_PosicionarMOB(<?php echo $operario_ant?>, <?php echo $i?>, <?php echo "'bajar_mob'"?>, <?php echo $nro_ot?>, <?php echo $tope_inf?>)">
                                                <img src="../Imagenes/abajo.png" width="20" height="20" alt="imagen">
                                            </a>
                                        </td>
                                        </tr>
                                    <?php
                                    $conex->Execute("UPDATE opd SET posicion=$i WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado AND extra=$extra");
                                    $extras->MoveNext();
                                    $i++;
                                } // Recorro extras
					    } // if (!extras.eof)
                    ?>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                        <td colspan="3" align="center">
                        <input name="add" id="add<?php echo $operario_ant?>" type="button" value="Agregar" onClick="GES_TareaExtra(<?php echo $operario_ant?>)">
                        </td>
                    </tr>
                    </table>
                    <div>&nbsp;</div>
                    <?php
                } // if (!comienzo)
                Debugger("Armo el encabezao de la tabla");
                ?>
                
                <table width="90%" align="center">
                    <tr><td colspan="8" class="head_seccion"><?php echo GES_BuscarNombreOperario($conex, $operario)?></td></tr>
                    <tr>
                    <td width="8%" class="head_item_std3">OT</td>
                    <td width="30%" class="head_item_std3">Descripción</td>
                    <td width="5%" class="head_item_std3">Cant.</td>
                    <td width="40%" class="head_item_std3">Item</td>
                    <td width="5%" class="head_item_std3">Prior.</td>
                    <td width="4%" class="head_item_std3">OK</td>
                    <td width="8%" class="head_item_std3" colspan="2">Orden</td>
                    </tr>
    			<?php
			$i=1;
			$operario_ant=$operario;
            $query = "SELECT posicion FROM opd WHERE operario=$operario ORDER BY posicion DESC LIMIT 1";
            Debugger($query);
			$res = $conex->Execute($query);
			$tope_inf = $res->fields['posicion'];
            Debugger("tope_inf=$tope_inf");
			$comienzo=0;
			
			// Tareas Extras por Operario
			$query = "SELECT opd.* FROM opd";
			$query = $query." WHERE opd.extra<>0 AND operario=$operario";
			$query = $query." ORDER BY opd.operario, opd.posicion ASC";
            Debugger("Ejecuto Query extras");
            Debugger($query);
			$extras = $conex->Execute($query);
		} // if($operario_ant != $operario)

		$pos=$pos_x=100;
        Debugger("pos=$pos, pos_x=$pos_x");
		if(!$tarea->EOF)	
            $pos = $tarea->fields['posicion'];        
        if (isset($extras))
		    if(!$extras->EOF)	
                $pos_x = $extras->fields['posicion'];

        Debugger("pos=$pos, pos_x=$pos_x");
		if($pos_x < $pos)
			{
                Debugger("Obtengo los datos de extras");
			$nro_ot = $extras->fields['nro_ot'];
			$desc_ot = "";
			$item = $extras->fields['item'];
			$numero = $extras->fields['numero'];
			$agregado = $extras->fields['agregado'];
			$cantidad = 1;
			$descripcion = htmlentities($extras->fields['descripcion']);
			$prioridad = " ";
			$extra = $extras->fields['extra'];
			$extras->MoveNext();
			}
		else
			{
                Debugger("Obtengo los datos de tarea");
			$nro_ot = $tarea->fields['nro_ot'];
			$desc_ot = $tarea->fields['desc_motor'];
			$item = $tarea->fields['item'];
			$numero = $tarea->fields['numero'];
			$agregado = $tarea->fields['agregado'];
			$cantidad = $tarea->fields['cantidad'];
			$descripcion = htmlentities($tarea->fields['descripcion']);
			$prioridad = $tarea->fields['prioridad'];
			$extra = $tarea->fields['extra'];
			$tarea->MoveNext();
			}
        
		$id = $operario."-".$i;
		Debugger("id = $id");
		
        if(($i)%2)
			{?><tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php }
		else 
            {?>
            <tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';"> <?php }
            Debugger("id=$id, nro_ot=$nro_ot, desc_ot=$desc_ot, cantidad=$cantidad, descripcion=$descripcion, prioridad=$prioridad");            
            ?>
			<td align="center"><span id="ot<?php echo $id?>"><?php echo $nro_ot?></span></td>
			<td><span id="desc_ot<?php echo $id?>"><?php echo $desc_ot?></span></td>
			<td align="center"><span id="cantidad<?php echo $id?>"><?php echo $cantidad?></span></td>
			<td><span id="desc<?php echo $id?>"><?php echo $descripcion?></span></td>
			<td align="center"><span id="prioridad<?php echo $id?>"><?php echo $prioridad?></span></td>
			<td align="center">

				<input name="estado<?php echo $id?>" id="estado<?php echo $id?>" type="checkbox" value="" 
                <?php 
                    if(GES_CheckEstadoMOB($conex, $nro_ot, $item, $numero, $agregado)==5) echo "checked";
                ?> 
                onclick="GES_ReactivarMOB(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>, <?php echo $operario?>, <?php echo $i?>)">
			</td>
			<td width="4%" align="center">
				<a href="#" onClick="GES_PosicionarMOB(<?php echo $operario?>, <?php echo $i?>, <?php echo "'subir_mob'"?>, <?php echo $nro_ot?>, <?php echo $tope_inf?>)">
					<img src="../Imagenes/arriba.png" width="20" height="20" alt="imagen">
				</a>
			</td>
			<td width="4%" align="center">
				<a href="#" onClick="GES_PosicionarMOB(<?php echo $operario?>, <?php echo $i?>, <?php echo "'bajar_mob'"?>, <?php echo $nro_ot?>, <?php echo $tope_inf?>)">
					<img src="../Imagenes/abajo.png" width="20" height="20" alt="imagen">
				</a>
			</td>
			</tr>
		<?php
        Debugger("Actualizo posicion= $i");
		$conex->Execute("UPDATE opd SET posicion=$i WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado AND extra=$extra");
		$i++;
	}
    Debugger("fin de Recorro tarea");
	if(!$comienzo)
		{
	if(!$extras->EOF)
		{
            Debugger("Recorro extras");
		while(!$extras->EOF){
			$nro_ot = $extras->fields['nro_ot'];
			$desc_ot = "";
			$item = $extras->fields['item'];
			$numero = $extras->fields['numero'];
			$agregado = $extras->fields['agregado'];
			$cantidad = 1;
			$descripcion = htmlentities($extras->fields['descripcion']);
			$prioridad = " ";
			$extra = $extras->fields['extra'];
			$id = $operario_ant."-".$i;
			
			if(($i)%2)
				{?><tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php }
			else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';"><?php }?>
				<td align="center"><span id="ot<?php echo $id?>"><?php echo $nro_ot?></span></td>
				<td><span id="desc_ot<?php echo $id?>"><?php echo $desc_ot?></span></td>
				<td align="center"><span id="cantidad<?php echo $id?>"><?php echo $cantidad?></span></td>
				<td><span id="desc<?php echo $id?>"><?php echo $descripcion?></span></td>
				<td align="center"><span id="prioridad<?php echo $id?>"><?php echo $prioridad?></span></td>
				<td align="center">
					<input name="estado<?php echo $id?>" id="estado<?php echo $id?>" type="checkbox" value="" <?php if(GES_CheckEstadoMOB($conex, $nro_ot, $item, $numero, $agregado)==5) echo "checked";?> onclick="GES_ReactivarMOB(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>, <?php echo $operario_ant?>, <?php echo $i?>)">
				</td>
				<td width="4%" align="center">
					<a href="#" onClick="GES_PosicionarMOB(<?php echo $operario_ant?>, <?php echo $i?>, <?php echo "'subir_mob'"?>, <?php echo $nro_ot?>, <?php echo $tope_inf?>)">
						<img src="../Imagenes/arriba.png" width="20" height="20" alt="imagen">
					</a>
				</td>
				<td width="4%" align="center">
					<a href="#" onClick="GES_PosicionarMOB(<?php echo $operario_ant?>, <?php echo $i?>, <?php echo "'bajar_mob'"?>, <?php echo $nro_ot?>, <?php echo $tope_inf?>)">
						<img src="../Imagenes/abajo.png" width="20" height="20" alt="imagen">
					</a>
				</td>
				</tr>
			<?php
            Debugger("Actualizo posiciion = $i");
			$conex->Execute("UPDATE opd SET posicion=$i WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado AND extra=$extra");
			$extras->MoveNext();
			$i++;
			}
		} // fin de recorro extras
		?>
		<tr>
			<td colspan="5">&nbsp;</td>
			<td colspan="3" align="center">
			<input name="add" id="add<?php echo $operario_ant?>" type="button" value="Agregar" onClick="GES_TareaExtra(<?php echo $operario_ant?>)">
			</td>
		</tr>
		</table>
		<?php
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
