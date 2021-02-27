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
require_once "../Librerias/Excel/reader.php";
$user_gr = $_SESSION['user_gr'];
$conex = $user_gr->GetConex();

isset($_GET['ruta']) ? define("RUTA", $_GET['ruta']) : define("RUTA", "../Data/Listas");
isset($_GET['archivo']) ? $archivo=$_GET['archivo'] : $archivo='';
isset($_GET['id']) ? $id=$_GET['id'] : $id=0;

// Para Archivo de Informacion de Actualizacion
define("RUTA_FILE", "../Data/Log/");

// Indices para el array de resultados de actualizacion
define("ASOCIADOS", 0);
define("NUEVOS", 1);
define("AGREGADOS", 2);
define("ACTUALIZADOS", 3);
define("REP_ASOCIADOS", 4);
define("REP_NO_ASOCIADOS", 5);
define("REPETIDOS", 6);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Actualizar Proveedor</TITLE>

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
MostrarLinea("Actualizacion de Lista de Proveedor:", "titulo1");

// Despliego una lista con los archivos de actualizacion para seleccionar uno
if($archivo=='' && !isset($recargar))
	{
	?>
   <div>&nbsp;</div>
   <table width="40%" align="center">
      <tr>
	      <td class="proveedor_head">Seleccione la Lista del Proveedor:</td>
      </tr>
      <tr>
	      <td><?php Prov_ListarArchivos(RUTA);?></td>
      </tr>
   </table>   
   <?php
	}
	
// Si se selecciono un archivo y no se ha procesado un proveedor, lo proceso
if($archivo!='' && !$id)
	{
	$data = Prov_AbrirExcel($archivo);	// Abro el archivo y lo cargo en $data
	
	// Ubico el encabezado de la tabla
	for($i=1; $i<=$data->sheets[0]['numCols']; $i++)   {
		for ($j=1; $j<=$data->sheets[0]['numRows']; $j++)  {
			if($data->sheets[0]['cells'][$j][$i] == 'CODIGO')
				{
				$cup_col = $i;
				$cup_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'DESCRIPCION')
				{
				$des_col = $i;
				$des_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'PRECIO')
				{
				$imp_col = $i;
				$imp_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'LISTA')
				{
				$lis_col = $i;
				$lis_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'MOTOR')
				{
				$mot_col = $i;
				$mot_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'RUBRO')
				{
				$rub_col = $i;
				$rub_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'NUMERO')
				{
				$num_col = $i;
				$num_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'DESCGR')
				{
				$des_gr_col = $i;
				$des_gr_row = $j; 
				}
			else if($data->sheets[0]['cells'][$j][$i] == 'CODGR')
				{
				$cod_col = $i;
				$cod_row = $j; 
         }
      }
   }

	// Busco el proveedor
	$nombre=Prov_ExtraerNombre($archivo);
	$res=Prov_Buscar($conex);
	$id=0;
	while(!$res->EOF)
		{
		$prov = $res->fields['nombre'];
		if($nombre==$prov)
			{
			$id = $res->fields['id'];
			break;
			}
		$res->MoveNext();
		}

	// Si no existe, lo registro
	if(!$id)	$id = Prov_Registrar($conex, $nombre);
	
	// Actualizo la fecha de actualizacion del proveedor
	Prov_ActualizarFecha($conex, $id, $archivo);

	// Cargo la info en una Tabla temporal de proveedor
	$estadistica = array(0,0,0,0,0,0,0);
	$cont=1;
	$conex->Execute("DELETE FROM prov_temp");
	
	$no=0;
	for($j=$cup_row+1; $j<=$data->sheets[0]['numRows']; $j++)   {
      
		$cup = $data->sheets[0]['cells'][$j][$cup_col];
		$descripcion = $data->sheets[0]['cells'][$j][$des_col];
		$importe = $data->sheets[0]['cells'][$j][$imp_col];
		$lista = $data->sheets[0]['cells'][$j][$lis_col] == '' ? '-' : $data->sheets[0]['cells'][$j][$lis_col];
		$motor = 0;
		$rubro = 0;
		$numero = 0;
		$descgr = "";
		$codgr = "";

		if(isset($mot_col)) {
			if($data->sheets[0]['cells'][$j][$mot_col] != '')
				{
				$motor = $data->sheets[0]['cells'][$j][$mot_col];
				$rubro = $data->sheets[0]['cells'][$j][$rub_col];
            if($data->sheets[0]['cells'][$j][$num_col] != '')  {	
               $numero = $data->sheets[0]['cells'][$j][$num_col];
            }
				else {
               $numero = 0;
            }
				$descgr = $data->sheets[0]['cells'][$j][$des_gr_col];
				$codgr = $data->sheets[0]['cells'][$j][$cod_col];
				}
      }
		$query = "INSERT INTO prov_temp VALUES($cont, '$cup', '$descripcion', $importe, '$lista', $motor, $rubro, $numero, '$descgr', '$codgr', 0)";
		$res = $conex->Execute($query);
		if(!$res->EOF)	$NoInsert[$no++]=$cup;

		$cont++;
		}

	// Agrego las listas del proveedor a la tabla
	$res = $conex->Execute("SELECT DISTINCT lista FROM prov_temp");
	if($res->NumRows() != 0) {
		Prov_GrabarListas($conex, $id);
   }

	// Comienzo con las etapas de filtrado de la tabla temporal de proveedor
	$data = array();
	$data=Prov_MarcarRepetidos($conex);		// Marco los repetidos
	$estadistica[REP_ASOCIADOS]=$data[0];
	$estadistica[REP_NO_ASOCIADOS]=$data[1];
	$estadistica[REPETIDOS]=$data[2];

	Prov_MarcarAsociados($conex);				// Marco los que estan en la lista local

	// Actualizo la tabla del proveedor y del GR
	$ini=0;
	$fecha = Prov_UltimoUpdate($conex, $id);
	$res = $conex->Execute("SELECT item FROM prov_mat WHERE id=$id");			// Busco el numero para el proximo item que se inserte
	$cont = $res->NumRows()+1;
	$res_temp = $conex->Execute("SELECT * FROM prov_temp WHERE estado<=2");	// Levanto los items de la tabla que deben ser actualizados o dados de alta
	
	while(!$res_temp->EOF)
		{
		$cup=$res_temp->fields['cup'];
		$descripcion=$res_temp->fields['descripcion'];
		$importe=$res_temp->fields['precio'];
		if($res_temp->fields['lista']=='')	$lista = '0';
		else											$lista = Prov_BuscarNroLista($conex, $id, $res_temp->fields['lista']);
		
		$query = "SELECT id FROM prov_mat FORCE INDEX(id_cup) WHERE cup='$cup' AND id=$id";
		$res_find = $conex->Execute($query);
		if($res_find->fields['id'] != $id)
			{
			$query = "INSERT INTO prov_mat VALUES($id, '$cup', '$descripcion', $importe, $lista, 0, $cont, '$fecha')";
			$item=$cont++;
			$estadistica[AGREGADOS]++;
			}
		else
			{
			$query = "UPDATE prov_mat SET descripcion='$descripcion', precio=$importe, lista=$lista, `update`='$fecha' WHERE cup='$cup' AND id=$id";
			$item = Prov_BuscarItem($conex, $id, $cup);
			}
		$conex->Execute($query);
		
		// Esta parte hace las asociaciones
		if(isset($mot_col))
			{
			if($res_temp->fields['motor'] && $res_temp->fields['rubro'])
				{
				$motor = $res_temp->fields['motor'];
				$rubro = $res_temp->fields['rubro'];
				if($res_temp->fields['numero']!=0)				// Si se le dio un numero, lo asocio al item directamente
					{
					$numero = $res_temp->fields['numero'];
					$valor = Prov_AsociarItems($conex, $id, $item, $motor, $rubro, $numero);
					if($valor == 2)
						{
						if(!$ini)
							{
							$ini=1;
							$txt_items = "items.log";
							if(!$fd_items = fopen(RUTA_FILE.$txt_items, "a+"))
								MostrarLinea("No se puede abrir el archivo ".RUTA_FILE.$txt_items);
							else
								{
								fwrite($fd_items, "\r\n");
								fwrite($fd_items, "\r\n***********************************************************************");
								fwrite($fd_items, "\r\n***********************************************************************");
								fwrite($fd_items, "\r\nActualizacion de Lista de Proveedor");
								fwrite($fd_items, "\r\nProveedor: ".Prov_BuscarNombre($conex, $id));
								fwrite($fd_items, "\r\nArchivo: ".$archivo);
								fwrite($fd_items, "\r\nFecha: ".date("d-m-Y"));
								fwrite($fd_items, "\r\nHora: ".date("H:i:s"));
								fwrite($fd_items, "\r\n***********************************************************************");
								fwrite($fd_items, "\r\nItems que no se han asociado porque no existen en el GR, borrar el campo 'numero' del item");
								fwrite($fd_items, "\r\nen el excel y al cargar nuevamente la lista se daran de alta automaticamente");
								fwrite($fd_items, "\r\nMOTOR\tRUBRO\tNUMERO");
								}
							}
						fwrite($fd_items, "\r\n".$motor."\t".$rubro."\t".$numero);					
						}
					else
						$estadistica[ASOCIADOS]+=$valor;
					}
				else																			// Sino, si el item no existe en 'asocia_mat', lo asocio en 'asocia_mat' 
					{																			// y lo agrego a 'mat' como item nuevo
					// Verifico que la asociacion no exista
					$query = "SELECT * FROM asocia_mat WHERE motor=$motor AND item=$rubro AND id_prov=$id AND cup='$cup'";
					$res1 = $conex->Execute($query);
										
					if(!$res1->NumRows())	// No existe, entonces lo asocio y creo como nuevo
						{
						// Busco el proximo numero para ser asignado al item
						$res1 = $conex->Execute("SELECT numero FROM mat WHERE nro_motor=$motor AND item=$rubro ORDER BY numero DESC LIMIT 1");
						$numero = $res1->fields['numero']+1;
	
						// Creo
						$precio = array();
						$precio[0]=0;
						$precio[1]=0;
						$precio[2]=0;
						$precio[3]=$importe;
						$codigo = $res_temp->fields['codgr'];
						$desc = $res_temp->fields['descgr'];
						Prov_GuardarItem($conex, $id, $item, $motor, $numero, $rubro, $codigo, $desc, $precio);
						$estadistica[NUEVOS]++;
						
						// Asocio
						$estadistica[ASOCIADOS]+=Prov_AsociarItems($conex, $id, $item, $motor, $rubro, $numero);
						}
					}
				}
			}
		$res_temp->MoveNext();
		}

	// Actualizo los precios de la lista local y la fecha de actualizacion
	$estadistica[ACTUALIZADOS] = Prov_ActualizarItems($conex, $id);
	
	//	INFORME DE LA ACTUALIZACION
	?>
	<div>&nbsp;</div>
	<table width="70%" align="center">
		<tr>
			<td class="head_std" colspan="2" align="center">Estadisticas de la Actualización</td>
		</tr>
		<tr>
			<td width="60%" class="head_item_std1">Items Nuevos del Proveedor:</td>
			<td width="40%" class="head_item_std2"><?php echo $estadistica[AGREGADOS]?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Items Asociados al GR:</td>
			<td class="head_item_std2"><?php echo $estadistica[ASOCIADOS]?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Items Nuevos en GR:</td>
			<td class="head_item_std2"><?php echo $estadistica[NUEVOS]?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Items Actualizados en GR:</td>
			<td class="head_item_std2"><?php echo $estadistica[ACTUALIZADOS]?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Items con Codigo Repetido que Debian Asociarse:</td>
			<td class="head_item_std2"><?php echo $estadistica[REP_NO_ASOCIADOS]?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Items con Codigo Repetido que no Tienen Asociacion:</td>
			<td class="head_item_std2"><?php echo $estadistica[REPETIDOS]?></td>
		</tr>
	</table>

	<?php
	if($ini)
		{
		?>
		<script language="javascript">alert("ATENCION: Revisar el archivo items.log")</script>
		<?php		
		}
	// DETALLES DE ITEMS CON PROBLEMAS
	if($no || $estadistica[REP_NO_ASOCIADOS] || $estadistica[REPETIDOS])
		{
		$txt="proveedor.log";
		if(!$fd = fopen(RUTA_FILE.$txt, "a+"))
			MostrarLinea("No se Puede Abrir el Archivo ".RUTA_FILE.$txt);
		else
			{
			$cant1=0;
			$cant2=0;
			if($estadistica[REP_NO_ASOCIADOS])
				{
				$res1 = $conex->Execute("SELECT cup, descripcion, precio FROM prov_temp WHERE estado=3");
				$cant1 = $res1->NumRows();
				}
			if($estadistica[REPETIDOS])
				{
				$res2 = $conex->Execute("SELECT cup FROM prov_temp WHERE estado=4");
				$cant2 = $res2->NumRows();
				}
			
			fwrite($fd, "\r\n***********************************************************************");
			fwrite($fd, "\r\n***********************************************************************");
			fwrite($fd, "\r\nActualizacion de Lista de Proveedor");
			fwrite($fd, "\r\nProveedor: ".Prov_BuscarNombre($conex, $id));
			fwrite($fd, "\r\nArchivo: ".$archivo);
			fwrite($fd, "\r\nFecha: ".date("d-m-Y"));
			fwrite($fd, "\r\nHora: ".date("H:i:s"));
			fwrite($fd, "\r\nARTICULOS CON ERROR DE DATOS: ". $no);
			fwrite($fd, "\r\nARTICULOS QUE DESEAN ASOCIARSE PERO POSEEN CODIGO DE PROVEEDOR REPETIDO: ". $cant1);
			fwrite($fd, "\r\nARTICULOS QUE POSEEN SOLO CODIGO DE PROVEEDOR REPETIDO: ". $cant2);
			
			// Items no insertados por problemas de caracteres
			if($no)
				{
				fwrite($fd, "\r\n");
				fwrite($fd, "\r\n***********************************************************************");
				fwrite($fd, "\r\nARTICULOS CON ERROR DE DATOS\r\nCantidad: ".$no."\r\nCODIGO\r\n");
				for($i=0; $i<$no; $i++)
					fwrite($fd, "\r\n".$NoInsert[$i]);
				}
			
			// Items con cup repetido y que se quieren asociar
			if($estadistica[REP_NO_ASOCIADOS])
				{
				fwrite($fd, "\r\n");
				fwrite($fd, "\r\n***********************************************************************");
				fwrite($fd, "\r\nARTICULOS QUE DESEAN ASOCIARSE PERO POSEEN CODIGO DE PROVEEDOR REPETIDO");
				fwrite($fd, "\r\nCODIGO\t\t\tDESCRIPCION\t\tPRECIO\r\n");
				while(!$res1->EOF)
					{
					fwrite($fd, "\r\n".$res1->fields['cup']."\t\t".$res1->fields['descripcion']."\t\t\t".$res1->fields['precio']);
					$res1->MoveNext();
					}
				}
			
			// Items con cup repetido y que NO se quieren asociar
			if($estadistica[REPETIDOS])
				{
				fwrite($fd, "\r\n");
				fwrite($fd, "\r\n***********************************************************************");
				fwrite($fd, "\r\nARTICULOS QUE POSEEN SOLO CODIGO DE PROVEEDOR REPETIDO");
				fwrite($fd, "\r\nCODIGO\r\n");
				while(!$res2->EOF)
					{
					fwrite($fd, "\r\n".$res2->fields['cup']);
					$res2->MoveNext();
					}
				}
			}
		fwrite($fd, "\r\n");
		fclose($fd);
		?>
		<script language="javascript">alert("ATENCION: Revisar el archivo proveedor.log")</script>
		<?php
		}
	?>
	<div>&nbsp;</div>
	<table width="40%" align="center">
		<tr>
			<td width="40%" align="right"><input type="button" name="ok" id="ok" value="   Aceptar   " onClick="PROVCancelar()"/></td>
			<td width="20%">&nbsp;</td>
			<td width="40%" align="left"><input type="button" name="ok" id="ok" value="   Nueva   " onClick="PROVNewUpdate()"/></td>
		</tr>
	</table>
	<?php
	$conex->Execute("DELETE FROM prov_temp");
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
