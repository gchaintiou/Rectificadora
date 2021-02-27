<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))	{	?>	<script language="JavaScript" type="text/javascript"> window.parent.location.href = '../index.php'; </script>	<?php	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex=$user_gr->GetConex();

isset($_GET['operario']) ? $operario=$_GET['operario'] : $operario=0;
isset($_GET['realizado']) ? $realizado=1 : $realizado=0;
if(!isset($_SESSION['init'.$operario]))	$_SESSION['init'.$operario]=0;
$iniciada = $_SESSION['init'.$operario];
Debugger("La tarea Iniciada por el Operario $operario es: $iniciada");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="60">
<link href="../estilos.css" rel="stylesheet" type="text/css">
<title>Trabajo a Realizar</title>
<style type="text/css">
<!--
body {
	background-color: #000;
}
-->
</style></head>

<body>
<div class="titulo1">TRABAJO A REALIZAR</div>
<?php
Debugger("Gestion/Taller.php");
if(isset($_GET['verot']))	// Se desea ver una OT?
	{
	$ot=$_GET['ot'];
	$user_gr->TallerMostrarOT($ot);
	}
else		
	{
        if ($realizado) {	// Hay que marcar alguno como realizado?
            $ot=$_GET['ot'];
            $num=$_GET['num'];
            $add=$_GET['add'];
            $fecha_real = $realizado=$_GET['anio']."-".$realizado=$_GET['mes']."-".$realizado=$_GET['dia'];
        
            $query = "UPDATE otd SET real_by=$operario, fecha_real='$fecha_real' WHERE nro_ot=$ot AND item=0 AND numero=$num AND agregado=$add";
            $conex->Execute($query);
        } ?>
	<div>&nbsp;</div>
	<table width="30%" align="center">
	<tr>
		<td width="30%" class="head_item_std1">Operario:</td>
		<td width="70%" class="head_item_std2">
			<select name="operario" id="operario" onChange="TALBuscarItems()">
				<option value="0">Seleccionar Operario</option>
				<?php
                $result = BuscarOperarios($conex);
        while (!$result->EOF) {
            $id = $result->fields['id'];
            $nombre = $result->fields['nombre']; ?>
					<option value="<?php echo $id?>" <?php if ($id==$operario) {
                echo "selected";
            } ?>><?php echo $nombre?></option>
					<?php
                    $result->MoveNext();
        } ?>
			</select>		
		</td>
	</tr>
	</table>
	<div>&nbsp;</div>
	<?php
    Debugger("Muestro los items que deben realizarse");
        $items = TAL_BuscarItems($conex, $operario);
        $items_extra = $conex->Execute("SELECT * FROM opd WHERE extra=1 AND operario=$operario AND estado<>5");	// Items extras?

        Debugger("Determino la proxima tarea a realizarse, esto lo hago por si se pierde el hilo normal");
        $next = 0;
        $query = "SELECT MIN(posicion) AS next FROM opd WHERE operario=$operario AND estado<4";
        Debugger($query);
        $res = $conex->Execute($query);
        if ($res->fields['next']) {
            $next = $res->fields['next'];
        } else {
            Debugger("No la encontré por esa selección, busco par la siguiente:");
            Debugger("SELECT MIN(posicion) AS next FROM opd WHERE operario=$operario AND estado=4");
            $res2 = $conex->Execute("SELECT MIN(posicion) AS next FROM opd WHERE operario=$operario AND estado=4");
            if ($res2->fields['next']) {
                $next = $res2->fields['next'];
            }
        }
        $query = "SELECT posicion FROM opd WHERE operario=$operario AND estado=1";
        $res= $conex->Execute($query);
        if (!$res->EOF)
            $tareaIniciada = $res->fields['posicion'];
        else
            $tareaIniciada = 0;

        Debugger("La próxima tarea es $next");
        Debugger("La tarea Iniciada es $tareaIniciada");
        if ($next>0) {
            $max_reg=0;
            if (!$items->EOF && $items_extra->EOF) {
                $max_reg = $items->NumRows();
            } elseif ($items->EOF && !$items_extra->EOF) {
                $max_reg = $items_extra->NumRows();
            } else {
                $max_reg = $items->NumRows()+ $items_extra->NumRows();
            }
            Debugger("max_reg=$max_reg");
            if ($max_reg) {
                ?>
            <table width="80%" align="center">
                <tr>
                    <td width="8%" class="head_item_std3">OT</td>
                    <td width="44%" class="head_item_std3">Descripción</td>
                    <td width="8%" class="head_item_std3">Cantidad</td>
                    <td width="40%" class="head_item_std3">Item</td>
                </tr>
            <?php
            $tarea_actual= $next;
                while ($max_reg) {
                    $pos=$pos_x=100;
                    if (!$items->EOF) {
                        $pos = $items->fields['posicion'];
                    }
                    if (!$items_extra->EOF) {
                        $pos_x = $items_extra->fields['posicion'];
                    }
                    Debugger("pos = $pos, pos_x = $pos_x");
                    if ($pos_x < $pos) {
                        Debugger("muestro los datos de items_extra");
                        $ot = " ";
                        $desc_ot = " ";
                        $numero = $items_extra->fields['numero'];
                        $agregado = $items_extra->fields['agregado'];
                        $cantidad = 1;
                        $descripcion = htmlentities($items_extra->fields['descripcion']);
                        $estado = $items_extra->fields['estado'];
                        $posicion = $pos_x;
                        Debugger("numero= $numero, agregado=$agregado, cantidad=$cantidad, descripcion=$descripcion, estado=$estado, posicion=$posicion");
                        $items_extra->MoveNext();
                    } else {
                        Debugger("Muestro los datos de items");
                        $ot = $items->fields['nro_ot'];
                        $desc_ot = htmlentities($items->fields['desc_motor']);
                        $numero = $items->fields['numero'];
                        $agregado = $items->fields['agregado'];
                        $cantidad = $items->fields['cantidad'];
                        $descripcion = htmlentities($items->fields['descripcion']);
                        $estado = $items->fields['estado'];
                        $posicion = $pos;
                        Debugger("ot=$ot, desc_ot=$desc_ot, numero=$numero, agregado=$agregado, cantidad=$cantidad, descripcion=$descripcion, estado=$estado, posicion=$posicion");
                        $items->MoveNext();
                    }
                
                    if ($estado==1) {
                        $tarea_actual=$posicion;
                    }
                    $id = $ot."-".$numero."-".$agregado;
                
                    if ($estado==1) {?>	<tr class="tarea_actual"><?php } elseif ($estado!=1 && $posicion==$next) {?>	<tr class="tarea_prox"><?php } elseif ($max_reg%2) {?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php } ?>
                    <td align="center"><a href="#" onClick="TALMostrarOT(<?php echo $ot?>)"><?php echo $ot?></a></td>
                    <td align="left"><?php echo $desc_ot?></td>
                    <td align="center"><?php echo $cantidad?></td>
                    <td align="left"><?php echo $descripcion?></td>
                </tr>
                <?php
                $max_reg--;
                } ?>
            </table>
            <div>&nbsp;</div>
            <table width="15%" align="center">
                <tr>
                    <td width="15%" align="center">
                        <a href="#" onClick="TAL_TareaIniciar(<?php echo $operario?>, <?php echo $next?>, <?php echo $tareaIniciada?>)">
                            <img src="../Imagenes/play.png" width="40" height="40" />
                        </a>
                    </td>
                    <td width="15%" align="center">
                        <a href="#" onClick="TAL_TareaPausar(<?php echo $operario?>, <?php echo $tarea_actual?>, <?php echo $tareaIniciada?>)">
                            <img src="../Imagenes/pausa.png" width="40" height="40" />
                        </a>
                    </td>
                    <td width="15%" align="center">
                        <a href="#" onClick="TAL_TareaSuspender(<?php echo $operario?>, <?php echo $tarea_actual?>, <?php echo $tareaIniciada?>)">
                            <img src="../Imagenes/cruz.png" width="40" height="40" />
                        </a>
                    </td>
                    <td width="15%" align="center">
                        <a href="#" onClick="TAL_TareaOmitir(<?php echo $operario?>, <?php echo $next?>, <?php echo $tareaIniciada?>)">
                            <img src="../Imagenes/fw.png" width="40" height="40" />
                        </a>
                    </td>
                    <td width="15%" align="center">
                        <a href="#" onClick="TAL_TareaRealizada(<?php echo $operario?>, <?php echo $tarea_actual?>, <?php echo $tareaIniciada?>)">
                            <img src="../Imagenes/checkmark.png" width="40" height="40" />
                        </a>
                    </td>
                </tr>
            </table>
            <div>&nbsp;</div>
            <div align="left" class="warning">REFERENCIAS:</div>
            <table width="75%" align="left">
                <tr>
                    <td width="3%" align="right"><img src="../Imagenes/play.png" width="25" height="25" /></td>
                    <td><div class="texto_referencia">Iniciar Tarea</div></td>
                </tr>
                <tr>
                    <td align="right"><img src="../Imagenes/pausa.png" width="25" height="25" /></td>
                    <td><div class="texto_referencia">Pausar Tarea (Se Deberá Justificar)</div></td>
                </tr>
                <tr>
                    <td align="right"><img src="../Imagenes/cruz.png" width="25" height="25" /></td>
                    <td><div class="texto_referencia">Suspender Tarea (Para Pedir Asistencia)</div></td>
                </tr>
                <tr>
                    <td align="right"><img src="../Imagenes/fw.png" width="25" height="25" /></td>
                    <td><div class="texto_referencia">Pasar a Próxima Tarea (Se Deberá Justificar)</div></td>
                </tr>
                <tr>
                    <td align="right"><img src="../Imagenes/checkmark.png" width="25" height="25" /></td>
                    <td><div class="texto_referencia">Finalizar Tarea</div></td>
                </tr>
                <tr>
                    <td bgcolor="#FFFF66"></td>
                    <td><div class="texto_referencia">Próxima Tarea Designada (El Comando Seleccionado Afecta a Esta Tarea)</div></td>
                </tr>
                <tr>
                    <td bgcolor="#00FF66"></td>
                    <td><div class="texto_referencia">Tarea en Ejecución</div></td>
                </tr>
            </table>
            <input type="hidden" id="iniciada" value="0"/>
		<?php
            } // if($max_reg)
        }//
    }
?>
</body>
<iframe id="myframe" name="myframe" src="" frameborder="0" framespacing="0" scrolling="auto" border="0" style="background:#0F0; position:absolute; left:500px; top:300px; width:500px; height:500px; z-index:5; visibility:hidden;"></iframe>
</html>