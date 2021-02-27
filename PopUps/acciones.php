<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))	{	?>	<script language="javascript"> window.parent.location.href = '../index.php'; </script>	<?php	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex=$user_gr->GetConex();

$accion = $_GET['accion'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php
Debugger("PopUps/acciones.php - $accion");
switch($accion)
	{
	case 'set_estado_mat':
		$ot = $_GET['ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		$estado = $_GET['estado'];
		$fecha = date("Y-m-d");
		
		$query = "UPDATE otd SET asig_to=$estado, fecha_asig='$fecha'";
		$query = $query." WHERE nro_ot=$ot AND item=$item AND numero=$numero AND agregado=$agregado";
        Debugger($query);
		$conex->Execute($query);
		break;
	case 'ver_estado_mob':
		$ot = $_GET['ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		$query = "SELECT opd.estado FROM opd";
		$query = $query." WHERE opd.nro_ot=$ot AND opd.item=$item AND opd.numero=$numero AND opd.agregado=$agregado";
        Debugger($query);
		$res = $conex->Execute($query);
		if(!$res->EOF)
			{
                Debugger("estado = ".$res->fields['estado']);
			$estado = $res->fields['estado'];
			if($estado!=0)
				{
				$res = $conex->Execute("SELECT descripcion FROM estado WHERE tipo=1 AND id=$estado");
				$descripcion = $res->fields['descripcion'];
                  Debugger("Armo el link a GES_SetEstadoMOB(descripcion=$descripcion, ot=$ot, item=$item, numero=$numero,agregado=$agregado");
				?>                    
				<script language="javascript">	GES_SetEstadoMOB(<?php echo "'$descripcion'"?>, <?php echo $ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>) </script>
				<?php
				}
			else
				{
				$descripcion = "-";
                Debugger("Armo el link a GES_SetEstadoMOB(descripcion=$descripcion, ot=$ot, item=$item, numero=$numero,agregado=$agregado");
				?>
				<script language="javascript">	GES_SetEstadoMOB(<?php echo "'$descripcion'"?>, <?php echo $ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>) </script>
				<?php
				}
			}
		break;
	case 'unset_mob':
		$ot = $_GET['ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		$fecha = $_GET['fecha'];
				
		$query = "DELETE FROM opd WHERE nro_ot=$ot AND item=$item AND numero=$numero AND agregado=$agregado";
        Debugger($query);
		$conex->Execute($query);
		break;
	case 'set_mob':
		$ot = $_GET['ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		$fecha = $_GET['fecha'];
		
		// Busco los datos del item en la OT
		$res = $conex->Execute("SELECT descripcion FROM otd WHERE nro_ot=$ot AND item=$item AND numero=$numero AND agregado=$agregado");
		$descripcion = $res->fields['descripcion'];
		// Inserto el item en la OP
		$query = "INSERT INTO opd(nro_ot, item, numero, agregado, extra, descripcion)";        
		$query = $query." VALUES($ot, $item, $numero, $agregado, 0, '$descripcion')";
        Debugger($query);
		$conex->Execute($query);
		break;
	case 'ver_operario':
		$ot = $_GET['ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		$operario = $_GET['operario'];
		
		$query = "SELECT opd.estado FROM opd";
		$query = $query." WHERE opd.nro_ot=$ot AND opd.item=$item AND opd.numero=$numero AND opd.agregado=$agregado";
        Debugger($query);
		$res = $conex->Execute($query);
		$estado = $res->fields['estado'];
        Debugger("Estado = $estado");
		if($estado!=0)
			{
			$res = $conex->Execute("SELECT descripcion FROM estado WHERE tipo=1 AND id=$estado");
			$descripcion = $res->fields['descripcion'];
			?>
			<script language="javascript">	
				GES_SetOperario(<?php echo "'$descripcion'"?>, <?php echo $ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>, <?php echo $operario?>)
			</script>
			<?php
			}
		else
			{
			$descripcion = "-";
			?>
			<script language="javascript">	
				GES_SetOperario(<?php echo "'$descripcion'"?>, <?php echo $ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>, <?php echo $operario?>)
			</script>
			<?php
			}		
		break;
	case 'set_operario':
		$ot = $_GET['ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		$operario = $_GET['operario'];
		$query = "UPDATE opd SET operario=$operario WHERE nro_ot=$ot AND item=$item AND numero=$numero AND agregado=$agregado";
        Debugger($query);
		$conex->Execute($query);
		break;
	case 'subir_mob':
	case 'bajar_mob':
		$operario = $_GET['operario'];		
		$linea = $_GET['linea'];

		// Que tengo que hacer?
		$pos = $linea;
		$accion == 'subir_mob' ? $linea-- : $linea++;
		// Levanto los datos de la linea destino para reconocerla luego
		$LINEA = $conex->Execute("SELECT * FROM opd WHERE operario=$operario AND posicion=$linea");
		$nro_ot = $LINEA->fields['nro_ot'];
		$item = $LINEA->fields['item'];
		$numero = $LINEA->fields['numero'];
		$agregado = $LINEA->fields['agregado'];
		// Intercambio las posiciones
		// Linea que se desea mover
		$conex->Execute("UPDATE opd SET posicion=$linea WHERE operario=$operario AND posicion=$pos");
		// Linea que cedio gentilmente su lugar
		$conex->Execute("UPDATE opd SET posicion=$pos WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado");
		break;
	case 'reset_mob':		// El reset es solo de real_by(operario que lo hizo) y fecha_real(fecha de realizacion) en 'otd'; estado(estado actual) y fecha en 'opd'
		$nro_ot = $_GET['nro_ot'];
		$item = $_GET['item'];
		$numero = $_GET['numero'];
		$agregado = $_GET['agregado'];
		
		// Reseteo en 'otd'
		$conex->Execute("UPDATE otd SET real_by=0, fecha_real='0000-00-00' WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado");
		// Reseteo en 'opd'
		$conex->Execute("UPDATE opd SET estado=0, fecha_ini='0000-00-00' WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado");
		break;
	case 'mob_extra':
		$operario = $_GET['operario'];
		$descripcion = $_GET['descripcion'];
		
		$res = $conex->Execute("SELECT MAX(posicion) AS posicion, MAX(extra) AS extra FROM opd WHERE operario=$operario");
		$posicion = $res->fields['posicion']+1;
		$extra = $res->fields['extra']+1;
		$query = "INSERT INTO opd(nro_ot, item, numero, agregado, extra, descripcion, operario, posicion)";
		$query = $query." VALUES(0, $operario, 0, 0, $extra, '$descripcion', $operario, $posicion)";
		$conex->Execute($query);
		?>
		<script> window.parent.location.href = '../Gestion/mob.php?ordenar'; </script>
		<?php
		break;
	case 'init_tarea':
		$operario = $_GET['operario'];
		$posicion = $_GET['posicion'];
		$fecha = date('Y-m-d');
        $hora = date('H:i:s');
		Debugger("PopUps/acciones.php?init_tarea?operario=$operario,posicion=$posicion,fecha=$fecha, hora=$hora");
		$query = "UPDATE opd SET estado=1, fecha_ini='$fecha', hora_ini='$hora'";
		$query = $query." WHERE operario=$operario AND posicion=$posicion";
		$conex->Execute($query);
        Debugger($query);
        Debugger("Seteo la vaiable de sesión init.$operario=1");
		$_SESSION['init'.$operario]=1;
		?>
		<script> window.parent.location.href = '../Gestion/taller.php?cargar&operario='+<?php echo $operario?>; </script>
		<?php
		break;
	case 'pausa_tarea':
		$operario = $_GET['operario'];
		$tarea = $_GET['tarea'];
		$comentario = "PAUSADA: ".$_GET['comentario'];
		$fecha = date('Y-m-d');
		
		// Ubico la tarea
		$query = "SELECT opd.* FROM opd";
		$query = $query." WHERE opd.operario=$operario AND opd.posicion=$tarea";
        Debugger($query);
		$res = $conex->Execute($query);
		$nro_ot = $res->fields['nro_ot'];
		$item = $res->fields['item'];
		$numero = $res->fields['numero'];
		$agregado = $res->fields['agregado'];
		$extra = $res->fields['extra'];
		$hora_ini = $res->fields['hora_ini'];
		$tiempo_acumulado = $res->fields['duracion'];

		// Calculo el tiempo
		$hora = date('H');
		$min = date('i');
		$seg = date('s');
		$tiempo=explode(':', $hora_ini);
		$duracion = ($hora-$tiempo[0])*3600 + ($min-$tiempo[1])*60 + $seg-$tiempo[2] + $tiempo_acumulado;
		
		// Actualizo su estado y tiempo acumulado de tarea
		$query = "UPDATE opd SET estado=2, duracion=$duracion";
		$query = $query." WHERE operario=$operario AND posicion=$tarea";
        Debugger($query);
		$conex->Execute($query);
		
		// Guardo la justificacion de la pausa
		$time = $hora.":".$min.":".$seg;
		$query = "INSERT INTO opo(nro_ot,item,numero,agregado,extra,fecha,hora,observacion)";
        $query = $query + " VALUES($nro_ot, $item, $numero, $agregado, $extra, '$fecha', '$time', '$comentario')";
		$conex->Execute($query);		

		$_SESSION['init'.$operario]=0;
		?>
		<script> window.parent.location.href = '../Gestion/taller.php?cargar&operario='+<?php echo $operario?>; </script>
		<?php	
		break;
	case 'suspender_tarea':        
		$operario = $_GET['operario'];
		$tarea = $_GET['tarea'];
		$fecha = date('Y-m-d');
		
		// Ubico la tarea
		$query = "SELECT opd.* FROM opd";
		$query = $query." WHERE opd.operario=$operario AND opd.posicion=$tarea";
		$res = $conex->Execute($query);
		$hora_ini = $res->fields['hora_ini'];
		$tiempo_acumulado = $res->fields['duracion'];

		// Calculo el tiempo
		$hora = date('H');
		$min = date('i');
		$seg = date('s');
		$tiempo=explode(':', $hora_ini);
		$duracion = ($hora-$tiempo[0])*3600 + ($min-$tiempo[1])*60 + $seg-$tiempo[2] + $tiempo_acumulado;
		
		// Actualizo su estado y tiempo acumulado de tarea
		$query = "UPDATE opd SET estado=3, duracion=$duracion";
		$query = $query." WHERE operario=$operario AND posicion=$tarea";
        Debugger($query);
		$conex->Execute($query);

		$_SESSION['init'.$operario]=0;
		?>
		<script>
			window.parent.location.href = '../Gestion/taller.php?cargar&operario='+<?php echo $operario?>;
			alert("Aguarde un Momento Hasta que Llegue la Asistencia");
		</script>
		<?php	
		break;
	case 'omitir_tarea':
		$operario = $_GET['operario'];
		$tarea = $_GET['tarea'];
		$comentario = "OMITIDA: ".$_GET['comentario'];
		$fecha = date('Y-m-d');
		$time = date('H-i-s');
		
		// Ubico la tarea
		$query = "SELECT opd.* FROM opd";
		$query = $query." WHERE opd.operario=$operario AND opd.posicion=$tarea";
		$res = $conex->Execute($query);
		$nro_ot = $res->fields['nro_ot'];
		$item = $res->fields['item'];
		$numero = $res->fields['numero'];
		$agregado = $res->fields['agregado'];
		$extra = $res->fields['extra'];

		// La omito
		$query = "UPDATE opd SET estado=4";
		$query = $query." WHERE operario=$operario AND posicion=$tarea";
        Debugger($query);
		$conex->Execute($query);
		
		// Guardo la justificacion de la omision
		
		$query = "INSERT INTO opo (nro_ot,item,numero,agregado,extra,fecha,hora,observacion)";
        $query = $query + " VALUES($nro_ot, $item, $numero, $agregado, $extra, '$fecha', '$time', '$comentario')";
		$conex->Execute($query);	
		?>
			<script> window.parent.location.href = '../Gestion/taller.php?cargar&operario='+<?php echo $operario?>; </script>
		<?php		
		break;
	case 'check_tarea':        
		$operario = $_GET['operario'];
		$tarea = $_GET['tarea'];
		$fecha = date('Y-m-d');
		Debugger("PopUps/acciones.php?accion=check_tarea?operario=$operario, tarea=$tarea, fecha=$fecha");
		// Ubico la tarea
		$query = "SELECT opd.* FROM opd";
		$query = $query." WHERE opd.operario=$operario AND opd.posicion=$tarea";
        Debugger($query);
		$res = $conex->Execute($query);
		$nro_ot = $res->fields['nro_ot'];
		$item = $res->fields['item'];
		$numero = $res->fields['numero'];
		$agregado = $res->fields['agregado'];
		$hora_ini = $res->fields['hora_ini'];
		$tiempo_acumulado = $res->fields['duracion'];

		// Calculo el tiempo
		$hora = date('H');
		$min = date('i');
		$seg = date('s');
		$tiempo=explode(':', $hora_ini);
		$duracion = ($hora-$tiempo[0])*3600 + ($min-$tiempo[1])*60 + $seg-$tiempo[2] + $tiempo_acumulado;
		
		Debugger("Doy por finalizada");
		//$query = "UPDATE opd SET estado=5, duracion=$duracion";
		//$query = $query." WHERE operario=$operario AND posicion=$tarea";
		$query = "DELETE FROM opd";
		$query = $query." WHERE operario=$operario AND posicion=$tarea";

        Debugger($query);

		$conex->Execute($query);
		
		// Lo asiento en otd tambien
        Debugger("Actualizo la Orden de Trabajo");
		$query = "UPDATE otd SET real_by=$operario, fecha_real='$fecha'";
		$query = $query." WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado";
        Debugger($query);
		$conex->Execute($query);		
		
		MostrarLinea($query, "");
		
		$_SESSION['init'.$operario]=0;
		?>
		<script> window.parent.location.href = '../Gestion/taller.php?cargar&operario='+<?php echo $operario?>; </script>
		<?php		
		break;
	}
?>
</body>
</html>