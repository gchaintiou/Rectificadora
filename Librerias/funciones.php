<?php
if (!defined('FUNCIONES'))
{
define('FUNCIONES', 101);


//****************************************************************************************************************************************************
//****************************************************        UTILIDADES         *********************************************************************
//****************************************************************************************************************************************************

//****************************************************************************************************************************************************
// Muestra una linea de texto formateada al estilo requerido 
function MostrarLinea($texto, $estilo)
	{
	?>
	<div class="<?php echo $estilo; ?>"><?php echo $texto; ?></div>
	<?php
	}
function Debugger($texto){
    if ($_SESSION['debug'])
        file_put_contents($_SESSION['pathroot'].'/log_'.date("j.n.Y").'.log', $texto.PHP_EOL, FILE_APPEND);
}
//****************************************************************************************************************************************************
// Conexión a Base SQL
function Conectar($dbhost, $dbusuario, $dbpassword, $db)
	{
	$link = ADONewConnection('mysqli');
	$link->Connect($dbhost, $dbusuario, $dbpassword, $db);
	
	if(!$link->ErrorMsg())
		return $link;
	else
		MostrarLinea("No se Pudo Conectar a la Base. Eror: ".$link->ErrorMsg(), "warning");
	}

//****************************************************************************************************************************************************
// Estos botones se muestran cuando se despliega un Presupuesto/OT consultado
function BotonesDecision($tipo, $pres, $ot)
	{
	?>
   <div>&nbsp;</div>
   <table width="80%" align="center">
      <tr>
         <td width="81%" align="right">
			<?php 
			if($tipo == CLI)
			{
			?>
			<input type="button" name="ot" value="<?php if($ot) echo "  Ver OT  "; else echo "  Crear OT  ";?>" onClick="PRECLI_ProcesarOT(<?php echo $pres?>, <?php echo $ot?>)"/>
			<?php 
			}
			?>
			</td>
         <td width="10%" align="right"><input type="button" name="editar" value="     Editar     " onClick="EditarPresOT(<?php echo $tipo?>, <?php echo $pres?>, <?php echo $ot?>)"/></td>
			<?php 
			if($tipo != STD)
			{
			?>
         <td width="9%" align="right"><input type="button" name="printver" value="   Imprimir   " onClick="Imprimir(<?php echo "1";?>, <?php echo $pres;?>)"/></td>
			<?php 
			}
			?>
      </tr>
   </table>
   <?php
	}

//****************************************************************************************************************************************************
// Estos botones se despliegan cuando se esta editando un Presupuesto/OT
function BotonesEdicion($tipo, $nro)
	{
	if($tipo==STD)
		{
		?>
		<div>&nbsp;</div>
		<table width="80%" align="center">
			<tr>
				<td width="96%">&nbsp;</td>
				<td width="2%"><input type="button" name="cancelar" id="cancelar" value="  Cancelar  " onClick="CancelarEdicion(<?php echo $nro?>)"/></td>
				<td width="2%"><input type="button" name="aceptar" id="aceptar" value="  Guardar  " onClick="AceptarEdicion()"/></td>
			</tr>
		</table>
		<?php
		}
	else
		{
		?>
		<div>&nbsp;</div>
		<table width="80%" align="center">
			<tr>
				<td width="82%">&nbsp;</td>
				<td width="2%"><input type="button" name="nota" id="nota" value="     Nota     " onClick="InsertarNota()"/></td>
				<td><input type="button" name="ajustar" id="ajustar" value="Ajustar Total" onClick="AjustarTotal()"/></td>
				<td width="8%"><input type="button" name="nuevo" value="Nuevo Item" onClick="NuevoItem()"/></td>
				<td width="8%"><input type="button" name="seleccion" id="seleccion" value="     Listo     " onClick="AceptarEdicion()"/></td>
			</tr>
		</table>
		<?php
		}
	}

//****************************************************************************************************************************************************
// Referencias sobre los estados òsibles de MOB y MAT en una OT
function MostrarReferencias()
	{
	MostrarLinea("Referencias:", "titulo2");
	?>
	<table align="center" width="40%" border="1">
		<tr>
			<td width="10%"></td>
			<td width="45%" class="head_item_std3">Mano de Obra</td>
			<td width="45%" class="head_item_std3">Material</td>
		</tr>
		<tr>
			<td class="etiqueta1"><img src="../Imagenes/interrogacion.png" width="20" height="20" alt="imagen"></td>
			<td class="etiqueta1">&nbsp;</td>
			<td class="etiqueta1">Indeterminado</td>
		</tr>
		<tr>
			<td class="etiqueta1"><img src="../Imagenes/checkmark.png" width="20" height="20" alt="imagen"></td>
			<td class="etiqueta1">Realizado</td>
			<td class="etiqueta1">En Existencia</td>
		</tr>
		<tr>
			<td class="etiqueta1"><img src="../Imagenes/cruz.png" width="20" height="20" alt="imagen"></td>
			<td class="etiqueta1">Sin Realizar</td>
			<td class="etiqueta1">A Encargar</td>
		</tr>
		<tr>
			<td class="etiqueta1"><img src="../Imagenes/pausa.png" width="20" height="20" alt="imagen"></td>
			<td class="etiqueta1">&nbsp;</td>
			<td class="etiqueta1">Encargado</td>
		</tr>
	</table>
	<?php 
	}

//****************************************************************************************************************************************************
//*******************************************   PRESUPUESTO A CLIENTE Y ORDEN DE TRABAJO   ***********************************************************
//****************************************************************************************************************************************************

//****************************************************************************************************************************************************
// Presenta una pantalla para realizar busqueda de presupuestos a cliente
function PantallaBusqueda($tipo)
	{
        Debugger("PantallaBusqueda($tipo)");
	?>
	<div class="titulo1" align="center">Complete los Datos Deseados Para la Búsqueda:</div>
	<div>&nbsp;</div>
	<form id="buscar_prescli" name="buscar_prescli" method="post" action="buscar.php?buscado">
		<table width="80%" align="center">
			<?php 
			if($tipo==OT)
			{
			?>
			<tr>
				<td width="15%" class="head_item_std1">Nº OT:</td>
				<td width="15%" class="head_item_std2"><input type="text" name="num_ot" id="num_ot" size="40" maxlength="50"/></td>
				<td width="20%">&nbsp;</td>
				<td width="30%">&nbsp;</td>
			</tr>
			<?php 
			}
			?>
			<tr>
				<td width="15%" class="head_item_std1">Cliente:</td>
				<td width="15%" class="head_item_std2"><input type="text" name="cliente" id="cliente" size="40" maxlength="50"/></td>
				<td width="20%">&nbsp;</td>
				<td width="30%">&nbsp;</td>
			</tr>
			<tr>
				<td class="head_item_std1">Motor:</td>
				<td class="head_item_std2"><input type="text" name="motor" id="motor" size="40" maxlength="50"/></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="head_item_std1">Cheque Nro.:</td>
				<td class="head_item_std2"><input type="text" name="cheque" id="cheque" size="40" maxlength="50"/></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="head_item_std1">Deuda:</td>
				<td class="head_item_std2">    
                    <select name="condicionDeuda" id="condicionDeuda">
                        <option value="">&nbsp;</option>
                        <option value="CON_DEUDA">CON_DEUDA</option>
                        <option value="SALDADA">SALDADA</option>
                    </select>
                </td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="head_item_std1">Mano de Obra:</td>
				<td class="head_item_std2">    
                    <select name="condicionMO" id="condicionMO">
                        <option value="">&nbsp;</option>
                        <option value="PENDIENTE">PENDIENTE</option>
                        <option value="A_REALIZAR">A REALIZAR</option>
                        <option value="FINALIZADA">FINALIZADA</option>
                    </select>
                </td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<div>&nbsp;</div>
		<table width="80%" align="center">
			<tr>
				<td colspan="3"  class="head_std">Desde Fecha:</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="14%" class="etiqueta2"><label>Día:<input type="text" name="dia1" id="dia1" size="4" maxlength="4" /></label></td>
				<td width="14%" class="etiqueta2"><label>Mes:<input type="text" name="mes1" id="mes1" size="4" maxlength="4" /></label></td>
				<td width="14%" class="etiqueta2"><label>Año:<input type="text" name="anio1" id="anio1" size="4" maxlength="4" /></label></td>
				<td width="28%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" class="head_std">Hasta Fecha:</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="etiqueta2"><label>Día:<input type="text" name="dia2" id="dia2" size="4" maxlength="4" /></label></td>
				<td class="etiqueta2"><label>Mes:<input type="text" name="mes2" id="mes2" size="4" maxlength="4" /></label></td>
				<td class="etiqueta2"><label>Año:<input type="text" name="anio2" id="anio2" size="4" maxlength="4" /></label></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	<div>&nbsp;</div>
	<div align="center"><label><input type="submit" name="buscar" id="buscar" value="  Buscar  " /></label></div>
	</form>
	<?php
	}

//****************************************************************************************************************************************************
function DeterminarCondicionOT($conex,$nro_ot){
    $query = "SELECT mob, mat,prioridad FROM ote WHERE nro_ot = $nro_ot";
    $ote = $conex->Execute($query);
    
    $mob = $ote->fields['mob'];
    $mat = $ote->fields['mat'];
    $total = $mob + $mat;
    $query = "SELECT id_recibo FROM rece WHERE nro_ot = $nro_ot";
    $rece = $conex->Execute($query);
    $totalPagado = 0;
    $rece->MoveFirst();
    While (!$rece->EOF){
        $id_recibo = $rece->fields['id_recibo'];
        $query = "SELECT SUM(haber) as pagado FROM recd WHERE id_recibo = $id_recibo";
        $recd = $conex->Execute($query);
        $pagado = $recd->fields['pagado'];
        $totalPagado += $pagado;        
        $rece->MoveNext();
    }
    $diferencia = $total - $totalPagado;
    Debugger("nro_ot = $nro_ot, mat=$mat, mob=$mob, total=$total, pagado=$totalPagado, diferencia=$diferencia");
    $CondicionDeuda = "";
    if ($diferencia > 0)
        $CondicionDeuda = "CON_DEUDA";
    else
        $CondicionDeuda = "SALDADA";
        
    $CondicionMO = "";
    if ($ote->fields['prioridad'] <> 0){
        $query="SELECT * FROM `otd` WHERE nro_ot = $nro_ot AND item = 0 and fecha_real = '0000-00-00'";
        $otd = $conex->Execute($query);
        if (!$otd->EOF)
            $CondicionMO = "A_REALIZAR";
        else
            $CondicionMO="FINALIZADA";
    }
    else
        $CondicionMO = "PENDIENTE";
    return "$CondicionDeuda,$CondicionMO";
}
function MostrarBusqueda($conex, $tipo)
	{
        Debugger("MostarBusqueda(tipo $tipo)");
	?>
	<div class="titulo1" align="center">Resultados de la Búsqueda de Presupuestos a Clientes:</div>    
	<div>&nbsp;
    </div>
	<table width="100%" align="center">
		<tr>
			<td class="head_item_std3">Número</td>
			<td class="head_item_std3">Fecha</td>
			<td class="head_item_std3">Cliente</td>
			<td class="head_item_std3">Motor</td>
            <td class="head_item_std3">Estado</td>
		</tr>
		<?php
		// Recupero los datos de los filtros ingresados
		$num_ot=0;
		if(isset($_POST['num_ot']))	$_POST['num_ot'] != "" ? $num_ot=$_POST['num_ot'] : $num_ot=0;
		$_POST['cliente'] != "" ? $nombre=$_POST['cliente'] : $nombre=NULL;
		$_POST['motor'] != "" ? $motor = $_POST['motor'] : $motor = NULL;
        $_POST['cheque'] != "" ? $cheque = $_POST['cheque'] : $cheque = NULL;
        $_POST['condicionDeuda'] != "" ? $findCondicionDeuda = $_POST['condicionDeuda'] : $findCondicionDeuda = NULL;
        $_POST['condicionMO'] != "" ? $findCondicionMO = $_POST['condicionMO'] : $findCondicionMO = NULL;
		if($_POST['anio1'] != "" && $_POST['mes1'] != "" && $_POST['dia1'] != "")	$date1 = $_POST['anio1'].'-'.$_POST['mes1'].'-'.$_POST['dia1'];
		else																								$date1 = NULL;
		if($_POST['anio2'] != "" && $_POST['mes2'] != "" && $_POST['dia2'] != "")	$date2 = $_POST['anio2'].'-'.$_POST['mes2'].'-'.$_POST['dia2'];
		else																								$date2 = NULL;

		// Busco los presupuestos que cumplen con los requisitos
		$res = PRECLI_BuscarPresupuestos($conex, $num_ot, $nombre, $motor,$cheque, $date1, $date2, $tipo);

		Debugger("funciones.php - Muestro los presupuestos hallados");
        Debugger("funciones.php - findCondicionDeuda = $findCondicionDeuda");
        Debugger("funciones.php - findCondicionMO = $findCondicionMO");
        if ($findCondicionMO)
            Debugger("Filtro por Condicion de MO = $findCondicionMO");
		$cont=0;
		while(!$res->EOF)
			{
			if($res->fields['nro_std'] != 0)
				{
                    //Debugger("res.nro_std = ".$res->fields['nro_std']);
                $condicion = DeterminarCondicionOT($conex,$res->fields['nro']);
                $arrayCondicion = explode(",",$condicion);
                $CondicionDeuda = $arrayCondicion[0];
                $CondicionMO = $arrayCondicion[1];
                Debugger("CondicionDeuda = $CondicionDeuda, CondicionMO = $CondicionMO");
                if ($findCondicionDeuda && $findCondicionDeuda !== $CondicionDeuda)
                    {
                        Debugger("Saltear");
                        $res->MoveNext();
                        continue;
                    }
                if ($findCondicionMO && $findCondicionMO !== $CondicionMO)
                    {
                        Debugger("Saltear");
                        $res->MoveNext();
                        continue;
                    }
                $clase="normal";
                switch ($condicion){
                    case "SALDADA,FINALIZADA":
                        $clase="normal";
                        break;
                    case "CON_DEUDA,A_REALIZAR":
                        $clase="con_deuda_a_realizar";
                        break;
                    case "CON_DEUDA,PENDIENTE":
                        $clase="con_deuda_pendiente";
                        break;
                        case "SALDADA,PENDIENTE":
                        $clase="saldada_pendiente";
                        break;
                    case "CON_DEUDA,FINALIZADA":
                        $clase="rojo";
                        break;
                }
                if($clase !== "normal"){
                    ?>
                        <tr class="<?php echo $clase ?>" onMouseOver="this.className='cell_over';" onMouseOut="this.className='<?php echo $clase ?>';">
                    <?php
                }
                else
				if($cont%2)
					{?>
					<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } 
                    else {
                        ?>
                        <tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">
                        <?php
                    }
                    ?>
					<td align="right"><a href="buscar.php?buscado&nro=<?php echo $res->fields['nro']?>"><?php echo $res->fields['nro']?></a></td>
					<td align="center"><?php echo $res->fields['fecha']?></td>
					<td><?php echo $res->fields['nombre'];?></td>
					<td><?php echo $res->fields['desc_motor'];?></td>
                    <td><?php echo $condicion; ?></td>
					</tr>
				<?php
				}
			$res->MoveNext();
			$cont++;
			}
		?>
	</table>
	<div>&nbsp;</div>
	<?php
	}

//****************************************************************************************************************************************************
// Busca los presupuestos a partir de los filtros pasados y retorna la busqueda
function PRECLI_BuscarPresupuestos($conex, $num_ot, $nombre, $motor,$cheque, $fecha1, $fecha2, $tipo)
	{
    Debugger("funciones.php-PRECLI_BuscarPresupuestos. tipo =$tipo");

	if($tipo==CLI)	
        $query = "SELECT fecha, nombre, desc_motor, nro_pres AS nro, nro_std FROM pce WHERE 1";
	else
        {
            if ($cheque)
                {
                    $query = "SELECT rece.nro_ot FROM recd, rece WHERE recd.nro_operacion = '$cheque' AND rece.id_recibo = recd.id_recibo";
                    $res = $conex->Execute($query);
                    if (!$res->EOF)
                        {                            
                            $num_ot = $res->fields['nro_ot'];
                            Debugger("nro_ot = $num_ot");
                        }  
                    else
                        {
                            Debugger("No encontré ni mierda");
                            return($res);                  
                        }
                }
            
            $query = "SELECT fecha, nombre, desc_motor, nro_ot AS nro, nro_std FROM ote WHERE 1";
        }

	if($num_ot)
		$query = $query." AND nro_ot=$num_ot";
	if($nombre)
		$query = $query." AND nombre LIKE '%".$nombre."%'";
	if($motor)
		$query = $query." AND desc_motor LIKE '%".$motor."%'";
    
	if($fecha1 && !$fecha2)
		$query = $query." AND fecha >= '$fecha1'";
	if(!$fecha1 && $fecha2)
		$query = $query." AND fecha <= '$fecha2'";
	if($fecha1 && $fecha2)
		$query = $query." AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	
	if($tipo==CLI)
		$query = $query." ORDER BY nro_pres DESC";
	else
		$query = $query." ORDER BY nro_ot DESC";
    Debugger($query);		
	return($conex->Execute($query));
	}

//****************************************************************************************************************************************************
// Busca los presupuestos a partir de los filtros pasados y retorna la busqueda
function GetIndiceMOB($conex, $tipo, $nro)
	{
	$query = "SELECT numero";
	if($tipo==CLI)
		$query = $query." FROM pcd WHERE nro_pres=$nro";
	else
		$query = $query." FROM otd WHERE nro_ot=$nro";
	$query = $query." AND agregado=1 AND item=0 ORDER BY numero DESC LIMIT 1";
	$res = $conex->Execute($query);
	$num = $res->fields['numero']+1;
	return($num);
	}

//****************************************************************************************************************************************************
// Busca los presupuestos a partir de los filtros pasados y retorna la busqueda
function GetIndiceMAT($conex, $tipo, $nro)
	{
	$query = "SELECT item";
	if($tipo==CLI)
		$query = $query." FROM pcd WHERE nro_pres=$nro";
	else
		$query = $query." FROM otd WHERE nro_ot=$nro";
	$query = $query." AND agregado=1 AND numero=0 ORDER BY item DESC LIMIT 1";
	$res = $conex->Execute($query);
	$numero = $res->fields['item']+1;
	return($numero);
	}

//****************************************************************************************************************************************************
//**********************************************        PRESUPUESTO ESTANDART         ****************************************************************
//****************************************************************************************************************************************************

// Busca los presupuestos estandart presentes en la base y los retorna
function PRESTD_BuscarPresupuestos($conex)
	{
	$query = "SELECT nro_pres, desc_pres FROM pse WHERE desc_pres IS NOT NULL ORDER BY desc_pres ASC";
	return($conex->Execute($query));
	}

// Retorna la descripcion del presupuesto STD especificado
function PRESTD_BuscarDescripcion($conex, $nro)
	{
	$query = "SELECT desc_pres FROM pse WHERE nro_pres=$nro";
	$res = $conex->Execute($query);
	return($res->fields['desc_pres']);
	}

// Buscar y retorna los motores presentes en la base
function BuscarMotores($conex)
	{
	$query = "SELECT nro_motor, desc_motor FROM mote ORDER BY desc_motor ASC";
	return($conex->Execute($query));
	}

// Buscar y retorna los motores presentes en la base
function BuscarRubro($conex, $rubro)
	{
	if($rubro)
		{
		$res = $conex->Execute("SELECT desc_rubro FROM rub WHERE nro_rubro=$rubro");
		$result = $res->fields['desc_rubro'];
		}
	else
		$result = $conex->Execute("SELECT nro_rubro, desc_rubro FROM rub WHERE desc_rubro!='' ORDER BY desc_rubro ASC");

	return($result);
	}

// Buscar y retorna la descripcion de un motor
function BuscarDescripcionMotor($conex, $motor)
	{
	$res = $conex->Execute("SELECT desc_motor FROM mote WHERE nro_motor=$motor");
	$descripcion = $res->fields['desc_motor'];
	return($descripcion);
	}

// Busca y retorna el numero de lista de precios de un motor
function BuscarNroLista($conex, $motor)
	{
	$result = $conex->Execute("SELECT nro_lista FROM mote WHERE nro_motor=$motor");
	$lista = $result->fields['nro_lista'];
	return($lista);
	}


//****************************************************************************************************************************************************
//***************************************************        MANO DE OBRA         ********************************************************************
//****************************************************************************************************************************************************

// Retorna la descripcion de un item
function MOBBuscarDescripcion($conex, $numero)
	{
	$query = "SELECT descripcion FROM mobe WHERE numero=$numero";
	$res = $conex->Execute($query);
	return($res->fields['descripcion']);
	}

// Retorna las secciones de items presentes en la base
function MOB_BuscarSecciones($conex)
	{
	return($conex->Execute("SELECT * FROM secciones ORDER BY nro ASC"));
	}

// Muestra la pantalla para ajuste global de lista MOB
function MOB_PantallaEdicionGlobal($conex, $opc)
	{
	?>
	<div align="center" class="titulo1"><?php echo "Seleccione Los Datos Necesarios Para el Ajuste Global:"?></div>
	<div>&nbsp;</div>
	<form action="" method="post" name="formindice" id="formindice">
	<table width="80%" align="center">
		<tr>
			<td width="40%" align="right" class="etiqueta1">Lista Origen:</td>
			<td width="20%" align="center" class="etiqueta1">Indice:</td>
			<td width="40%" align="left" class="etiqueta1">Lista Destino:</td>
		</tr>
		<tr>
			<td align="right"><select id="lista1" name="lista1">
			<option value="0">Seleccionar Lista</option>
			<?php 
			$lista = 1;
			$query = "SELECT columna FROM mobd ORDER BY columna DESC LIMIT 1";
			$result = $conex->Execute($query);
			$maxlist = $result->fields['columna'];
			
			while($lista <= $maxlist)
				{
				?>
				<option value="<?php echo $lista?>">Lista <?php echo $lista?></option>
				<?php
				$lista++;
				}
			?>
			</select></td>
			<td align="center"><input id="indice" name="indice" type="text" value="1.00" size="5" maxlength="5"/></td>
			<td align="left"><select id="lista2" name="lista2"><option value="0">Seleccionar Lista</option>
			<?php
			$lista = 1;
			while($lista <= $maxlist)
				{
				?>
				<option value="<?php echo $lista?>">Lista <?php echo $lista?></option>
				<?php
				$lista++;
				}
			?>
			</select></td>
		</tr>
	</table>
	</form>
   <div>&nbsp;</div>
		<div align="center">
      	<input name="cancelar" type="button" value="Cancelar" width="30" onClick="MOBCancelar()"/>
      	<input name="aceptar" type="button" value=" Aceptar " width="30" onClick="MOBAjusteGlobal(<?php echo $opc?>)"/>
      </div>  
   <div>&nbsp;</div>
	<?php 
	}

// Muestra la pantalla para ajuste de lista MOB por item
function MOB_PantallaEdicionItem($conex, $opc, $lista)
	{
	?>
	<div align="center" class="titulo1">Seleccione una lista para realizar los cambios en los items</div>
	<div>&nbsp;</div>
	<table width="25%" align="center">
		<tr>
		<td width="39" align="center" class="head_std">Seleccionar: <select id="lista" name="lista" onChange="MOBMostrarLista(<?php echo $opc?>)"><option value="<?php echo $lista?>"><?php if($lista != 0) echo "Lista ".$lista; else echo "Seleccionar Lista";?></option>
		<?php 
		$cont_list = 1;
		$query = "SELECT columna FROM mobd ORDER BY columna DESC LIMIT 1";
		$result = $conex->Execute($query);
		$maxlist = $result->fields['columna'];
		
		while($cont_list <= $maxlist)
			{
			?>
			<option value="<?php echo $cont_list?>">Lista <?php echo $cont_list?></option>
			<?php
			$cont_list++;
			}
		?>
		</select></td>
		</tr>
	</table>
	<?php
	}

// Presenta una lista MOB para la edicion de los importes
function MOB_MostrarListaEdicion($conex, $opc, $lista)
	{
	?>
	<div>&nbsp;</div>
	<table width="75%" align="center">
		<tr>
			<td width="10%" class="head_item_std3">Item</td>
			<td width="63%" class="head_item_std3">Descripcion</td>
			<td width="14%" class="head_item_std3">Actual ($)</td>
			<td width="13%" class="head_item_std3">Nuevo ($)</td>
		</tr>
	<?php
	// Busco los items de la lista requerida
	$query = "SELECT mobd.numero, mobe.descripcion, mobd.importe FROM mobd INNER JOIN mobe";
	$query = $query." WHERE mobd.columna=$lista AND mobe.numero=mobd.numero ORDER BY mobd.numero ASC";
	$result = $conex->Execute($query);
	$cont=0;
	while(!$result->EOF)
		{
		$numero = $result->fields['numero'];
		$descripcion = htmlentities($result->fields['descripcion']);
		$importe = $result->fields['importe'];
		
		if($cont%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }?>
		
		<td width="10%" align="center"><?php echo $numero;?></td>
		<td width="63%" align="left"><?php echo $descripcion;?></td>
		<td width="14%" align="right"><span id="span_imp<?php echo $numero;?>"><?php echo $importe;?></span></td>
		<td width="13%" align="right"><input id="importe<?php echo $numero;?>" type="text" value="<?php echo $importe;?>" size="5" align="middle" onchange="MOBValidarImporte(<?php echo $numero?>, <?php echo "1"; ?>)"/></td>
		</tr>
		<?php 
		$result->MoveNext();
		$cont++;
		}
	?>
	</table>
	</form>
	<div>&nbsp;</div>
	<?php 
	}

// Presenta la pantalla que se utiliza en el taller
function MOB_PantallaTaller()
	{
	return 0;
	}

//****************************************************************************************************************************************************
//*******************************************************        MOTOR         ***********************************************************************
//****************************************************************************************************************************************************

function MOTMostrarEdicion($conex, $existe)
	{
	?>
	<div align="center" class="titulo1">Selecciones los Items de Mano de Obra Para el Motor, la Lista de Precios Correspondiente al Mismo Será la Que Quede Seleccionada:</div>
   <div>&nbsp;</div>
	<form id="SelectItems" name="SelectItems" method="post" action="<?php if($existe) echo "editar.php?hacer=1";	else echo "nuevo.php?guardar"?>">
	<table width="70%" align="center">
		<?php 
		if($existe)
			{
			?>
			<tr>
				<td width="40%" class="head_item_std1">Motor:</td>
				<td width="60%" class="head_item_std2">
				<select name="motor" id="motor" onChange="MOTCargarMotor()">
					<option value="0">Seleccionar Motor</option>
					<?php 
					$result = BuscarMotores($conex);
					
					while(!$result->EOF)
					{
					$nro_motor = $result->fields['nro_motor'];
					$desc_motor = $result->fields['desc_motor'];
					?>
					<option value="<?php echo $nro_motor?>"><?php echo $desc_motor?></option>
					<?php 
					$result->MoveNext();
					}
					?>
					</select>
				</td>
			</tr>
			<?php 
			}
		?>
      <tr>
         <td width="40%" class="head_item_std1">Descripcion del Motor:</td>
         <td width="60" class="head_item_std2"><input name="desc_motor" id="desc_motor" type="text" size="40" maxlength="40" /></td>
         </tr>
         <tr>
         <td class="head_item_std1">Lista de Precios: </td>
         <td class="head_item_std2"><span id="span_lista">No Seleccionada</span></td>
      </tr>
	</table>
	<div>&nbsp;</div>
	<table width="50%" align="center">
      <tr>
         <td width="45%" align="right"><input name="cancel" type="button" value="Cancelar" onClick="MOTCancelar()" /></td>
         <td width="10%">&nbsp;</td>
         <td width="45%" align="left"><input name="aceptar" type="button" value="Aceptar" onClick="MOTGuardarNew()" /></td>
      </tr>
	</table>
	<table width="70%" align="center">
      <tr>
         <td width="3%" class="head_item_std3">Sel</td>
         <td width="71%" class="head_item_std3">Descripción del Item</td>
         <td width="26%" class="head_item_std3">Precio ($)
         	<select name="lista" id="lista" onChange="MOTMostrarPrecios(<?php echo "0"?>)">
               <option value="0">Seleccionar Lista</option>
               <?php 
               $query = "SELECT DISTINCT columna FROM mobd ORDER BY columna ASC";
               $result = $conex->Execute($query);
               
               while(!$result->EOF)
                  {
						$lista=$result->fields['columna'];
                  ?>
                  <option value="<?php echo $lista?>">Lista <?php echo $lista?></option>
                  <?php
                  $result->MoveNext();
                  }
               ?>
            </select>
         </td>
      </tr>
      <?php
		$cont=0;
      $result = $conex->Execute("SELECT * FROM mobe ORDER BY numero, seccion ASC");
      while(!$result->EOF)
			{
			$item = $result->fields['numero'];
			$descripcion = htmlentities($result->fields['descripcion']);

			if($cont%2)	{?>
            <tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">
         <?php } 
         else {?>
            <tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">
         <?php }?>
				<td align="center"><input name="<?php echo "item".$item?>" type="checkbox" value="1" /></td>
				<td><?php echo $descripcion?></td>
				<td align="right"><span id="span_precio<?php echo $item?>"></span></td>
			</tr>
			<?php
			$result->MoveNext();
			$cont++;
			}
      ?>
   </table>
   </form>
	<table align="center" width="50%">
      <tr>
         <td width="45%" align="right"><input name="cancel" type="button" value="Cancelar" onClick="MOTCancelar()" /></td>
         <td width="10%">&nbsp;</td>
         <td width="45%" align="left"><input name="aceptar" type="button" value="Aceptar" onClick="MOTGuardarNew()" /></td>
      </tr>
	</table> 
   <div>&nbsp;</div>
	<?php
	}

//****************************************************************************************************************************************************
//*******************************************************      PROVEEDOR       ***********************************************************************
//****************************************************************************************************************************************************

// Lista los archivos excel presentes en la variable 'ruta'
function Prov_ListarArchivos($ruta)
	{
	// abrir un directorio y listarlo recursivo 
	if (is_dir($ruta))
		{ 
		if ($dh = opendir($ruta))
			{ 
			while (($file = readdir($dh)) != false)
				{
				if($file!="." && $file!="..")
					{ 
					if(!is_dir($ruta."/".$file))
						{
						$ext = explode('.', $file);
						if($ext[1] == "xls")
							{
							?>
                     <div class="proveedor_celda"><a class="A" href="update.php?archivo=<?php echo $ruta."/".$file?>"><?php echo $file;?></a></div>
							<?php
							}
						} 
					}
				}
			closedir($dh); 
			}
		} 
	else 
		MostrarLinea("No es ruta valida",""); 
	}

// Abre el archivo excel especificado
function Prov_AbrirExcel($archivo)
	{
	$file = new Spreadsheet_Excel_Reader();
//	$file->setOutputEncoding('CP1251');
	$file->setOutputEncoding('UTF-16LE');
	$file->read($archivo);
	error_reporting(E_ALL ^ E_NOTICE);
	return($file);
	}

// Extrae el nombre del proveedor a partir del nombre del mismo
function Prov_ExtraerNombre($archivo)
	{
	$aux1 = explode('/', $archivo);	// Exploto el nombre del archivo en las barras
	$cant = count($aux1);				// Busco la cantidad de elementos que quedan
	$aux2 = $aux1[$cant-1];				// Me quedo con el ultimo elemento. Contiene el nombre del archivo, la fecha y la extension 
	$aux3 = explode('_',$aux2);		// Lo exploto en el guion bajo
	return($aux3[0]);						// Finalmente, me quedo con el nombre del proveedor
	}

// Extrae la fecha del archivo a partir del nombre del mismo
function Prov_ExtraerFecha($archivo)
	{
	$aux1 = explode('/', $archivo);	// Exploto el nombre del archivo en las barras
	$cant = count($aux1);				// Busco la cantidad de elementos que quedan
	$aux2 = $aux1[$cant-1];				// Me quedo con el ultimo elemento. Contiene el nombre del archivo, la fecha y la extension 
	$aux3 = explode('_',$aux2);		// Lo exploto en el guion bajo
	$aux4 = explode('.',$aux3[1]);	// Exploto la fecha y la extension en el punto
	return($aux4[0]);						// Finalmente, me quedo con la fecha
	}

// Retorna los proveedores presentes en la base
function Prov_Buscar($conex)
	{
	$query = "SELECT id, nombre FROM prov_info ORDER BY nombre ASC";
	return($conex->Execute($query));
	}

// Registra un nuevo proveedor
function Prov_Registrar($conex, $nombre)
	{
	// Busco el ultimo id asignado y asi determino el del nuevo proveedor
	$res1 = $conex->Execute("SELECT id FROM prov_info ORDER BY id DESC");
	$id = $res1->fields['id']+1;
	
	// Agrego el nuevo proveedor a la base de datos
	$query = "INSERT INTO prov_info VALUES($id, '$nombre','','','','')";
	$conex->Execute($query);
	return($id);
	}

// Retorna la ultima fecha de actualizacion de un proveedor
function Prov_UltimoUpdate($conex, $id)
	{
	$res = $conex->Execute("SELECT `update` FROM prov_info WHERE id=$id");
	$fecha = $res->fields['update'];
	return($fecha);
	}

// Actualiza la fecha de actualizacion de un proveedor
function Prov_ActualizarFecha($conex, $id, $archivo)
	{
	// Extraigo la fecha del archivo
	$new_up = Prov_ExtraerFecha($archivo);
	$new_anio = substr($new_up, 0, 4);
	$new_mes = substr($new_up, 4, 2);
	$new_dia = substr($new_up, 6, 2);

	$fecha = $new_anio."-".$new_mes."-".$new_dia;
	$query = "UPDATE prov_info SET `update`='$fecha' WHERE id=$id";
	$conex->Execute($query);

/*
	//Si quiero comparar fehcas para no volver a una anterior...	
	// Busco la última fecha de actualizacion
	$res = Prov_UltimoUpdate($conex, $id);
	$last_up = $res->fields['update'];
	$aux = explode('-', $last_up);
	$last_anio = $aux[0];
	$last_mes = $aux[1];
	$last_dia = $aux[2];
	// Acá iria el bucle de comparacion y la accion correspondiente
*/	
	}

// Quita los items ignorados de la base
function Prov_QuitarIgnorados($conex, $id)
	{
	// Busco los ignorados en la tabla local
	$query = "SELECT cup FROM prov_mat WHERE estado=3 ORDER BY cup ASC";
	$res_local = $conex->Execute($query);
	
	// Busco los datos de la tabla temporal
	$query = "SELECT cup FROM prov_mat ORDER BY cup ASC";
	$res_temp = $conex->Execute($query);
	
	// Quito de la tabla temporal los ignorados
	while(!$res_local->EOF)
		{
		$res_temp->MoveFirst();
		while(!$res_temp->EOF)
			{
			$cup_temp = $res_temp->fields['cup'];
			$cup_local = $res_local->fields['cup'];
			if($cup_temp == $cup_local)
				{
				$conex->Execute("UPDATE prov_mat SET estado=3 WHERE cup='$cup_temp'");
				break;
				}
			$res_temp->MoveNext();
			}
		$res_local->MoveNext();
		}
	}

// Marca los items repetidos
function Prov_MarcarRepetidos($conex)
	{
	$cant = array(0,0,0);	// Array para estadistica

	// Busco los items de la tabla temporal que tienen igual cup pero distinta descripcion o precio
	// Esto implica que es un error del proveedor
	$query = "SELECT cup, COUNT(*) AS Registros FROM prov_temp GROUP BY cup HAVING COUNT(*)>1 ORDER BY cup ASC";
	$res1 = $conex->Execute($query);

	// Asigno el estado "REPETIDO" a los elementos temporales
	// Estado=2 si el item se debe asociar y se  puede
	// Estado=3 si el item se debe asociar pero otro material tambien posee ese cup
	// Estado=4 si el item no se debe asociar pero esta repetido su cup
	while(!$res1->EOF)
		{
		$cup = $res1->fields['cup'];

		// Busco los items de la tabla temporal que tienen igual cup, descripcion y precio
		// Lo que implica que fueron hechos a proposito para asociarlos al GR
		$query = "SELECT cup, descripcion, precio, COUNT(*) AS Registros FROM prov_temp WHERE cup='$cup' GROUP BY cup, descripcion, precio HAVING COUNT(*)>1 ORDER BY cup ASC";
		$res2 = $conex->Execute($query);

		// Comparo que las cantidades de las dos consultas sean iguales
		// Esto implica que todos los repetidos se deben a asociaciones del GR
		if($res1->fields['Registros'] == $res2->fields['Registros'] && $res2->fields['Registros']!=0)
			$conex->Execute("UPDATE prov_temp SET estado=2 WHERE cup='$cup'");
		else if($res2->fields['Registros'])
			$conex->Execute("UPDATE prov_temp SET estado=3 WHERE cup='$cup'");
		else
			$conex->Execute("UPDATE prov_temp SET estado=4 WHERE cup='$cup'");

		$res1->MoveNext();
		}
	
	$res = $conex->Execute("SELECT estado FROM prov_temp WHERE estado=2");	$cant[0]=$res->NumRows();
	$res = $conex->Execute("SELECT estado FROM prov_temp WHERE estado=3");	$cant[1]=$res->NumRows();
	$res = $conex->Execute("SELECT estado FROM prov_temp WHERE estado=4");	$cant[2]=$res->NumRows();
	return($cant);
	}

// Marca los items asociados con el GR
function Prov_MarcarAsociados($conex)
	{
	// Actualizo el estado de los items que se quieren asociar al GR
	$conex->Execute("UPDATE prov_temp SET estado=1 WHERE estado=0 AND motor<>0");
	$query = "SELECT estado FROM prov_temp WHERE estado=1";
	$res1 = $conex->Execute($query);
	return($res1->NumRows());
	}

// Analiza variaciones en las descripciones de los items
function Prov_AnalizarDescripciones($conex, $id)
	{
	// Busco los datos filtrados de la tabla temporal
	$query = "SELECT cup, descripcion FROM prov_mat WHERE estado=0 AND id=$id ORDER BY cup ASC";
	$res1 = $conex->Execute($query);
	
	// Comparo los elementos con los de la base local
	while(!$res1->EOF)
		{
		$cup = $res1->fields['cup'];
		$desc_temp = $res1->fields['descripcion'];
		
		$res2 = $conex->Execute("SELECT desc_mat FROM mat WHERE cup='$cup' AND id_prov=$id");
		$desc_local = $res2->fields['desc_mat'];
			if($desc_local != $desc_temp)
				$conex->Execute("UPDATE prov_mat SET estado=1 WHERE cup='$cup' AND estado=0 AND id=$id");
		$res1->MoveNext();
		}
	}

// Actualiza los items en la base
function Prov_ActualizarItems($conex, $id)
	{
	// Busco la fecha de actualizacion de la lista
	$fecha = Prov_UltimoUpdate($conex, $id);
	
	// Busco los datos filtrados de la tabla del proveedor, los que tienen
	// estado 1 seran actualizados en la base del GR
	$query = "SELECT * FROM prov_mat WHERE estado=1 AND id=$id ORDER BY cup ASC";		//  AND `update`='$fecha'
	$res = $conex->Execute($query);
	
	// Actualizo los items
	$cant=0;
	while(!$res->EOF)
		{
		$cup = $res->fields['cup'];
		$precio = $res->fields['precio'];
		
		$query = "SELECT * FROM asocia_mat WHERE id_prov=$id AND cup='$cup'";
		$res1 = $conex->Execute($query);
		while(!$res1->EOF)
			{
			$motor=$res1->fields['motor'];
			$item=$res1->fields['item'];
			$numero=$res1->fields['numero'];
			$query = "UPDATE mat SET precio4=$precio, `update`='$fecha' WHERE nro_motor=$motor AND item=$item AND numero=$numero";
			$conex->Execute($query);
			$cant++;
			$res1->MoveNext();
			}
		$res->MoveNext();
		}
	return($cant);
	}

// Retorna el id de una lista
function Prov_BuscarNroLista($conex, $id, $desc)
	{
	$query = "SELECT numero FROM prov_lista WHERE id=$id AND descripcion='$desc'";
	$result = $conex->Execute($query);
	return($result->fields['numero']);
	}

// Retorna las listas de materiales (rubros) presentes para un proveedor
function Prov_BuscarListas($conex, $id)
	{
	$query = "SELECT numero, descripcion FROM prov_lista WHERE id=$id ORDER BY descripcion ASC";
	$result = $conex->Execute($query);
	return($result);
	}

// Graba las listas del proveedor (rubros) en la base
function Prov_GrabarListas($conex, $id)
	{
	// Selecciono las listas de la nueva lista de precio
	$query = "SELECT DISTINCT lista FROM prov_temp ORDER BY lista ASC";
	$result = $conex->Execute($query);
	
	$query = "SELECT descripcion FROM prov_lista WHERE id=$id ORDER BY descripcion ASC";
	$result1 = $conex->Execute($query);
	
	while(!$result->EOF)
		{
		$cont=0;
		$result1->MoveFirst();
		while(!$result1->EOF)
			{
			if($result1->fields['descripcion'] == $result->fields['lista'] || $result->fields['lista'] == '-')
				break;
			$result1->MoveNext();
			$cont++;
			}
			
		// Si la lista no esta, la agrego al proveedor
		if($cont == $result1->NumRows() && $result->fields['lista'] != '-')
			{
			$lista = $result->fields['lista'];
			// Busco el ultimo numero de lista del id
			$r = $conex->Execute("SELECT numero FROM prov_lista WHERE id=$id ORDER BY numero DESC LIMIT 1");
			$numero = $r->fields['numero']+1;
			// Agrego la lista
			$conex->Execute("INSERT INTO prov_lista(id, numero, descripcion) VALUES($id, $numero, '$lista')");
			}
		$result->MoveNext();
		}
	}

// Busca items de proveedor en base a los filtros pasados
function Prov_BuscarItems($conex, $id, $lista, $motor, $frase)
	{
	$query = "SELECT * FROM prov_mat WHERE id=$id";
	if($motor != '' && $frase != '')
		$query = $query." AND descripcion LIKE '%".$motor."%' AND descripcion LIKE '%".$frase."%'";
	else if($motor != '' && $frase == '')
			$query = $query." AND descripcion LIKE '%".$motor."%'";	
	else if($motor == ''  && $frase != '')
			$query = $query." AND descripcion LIKE '%".$frase."%";
	if($lista != 0)
		$query = $query." AND lista=$lista";	
	$query = $query." ORDER BY descripcion ASC";

	$res = $conex->Execute($query);
	return($res);
	}

// Busco posibles items en GR para asociar con este de proveedor
function Prov_BuscarParaAsociar($conex, $motor, $rubro)
	{
	$query = "SELECT * FROM mat WHERE nro_motor=$motor AND item=$rubro AND id_prov=0 ORDER BY numero ASC";
	$res = $conex->Execute($query);
	return($res);
	}

// Asocia item de proveedor con item de GR
function Prov_AsociarItems($conex, $prov, $item, $motor, $rubro, $numero)
	{
	// Busco el item seleccionado para el alta
	$query = "SELECT * FROM prov_mat WHERE id=$prov AND item=$item";
	$res = $conex->Execute($query);
	$cup = $res->fields['cup'];
	$precio = $res->fields['precio'];
	$fecha = $res->fields['update'];

	// Verifico que la asociacion no exista y que el item exista en mat
	$query = "SELECT * FROM asocia_mat WHERE motor=$motor AND item=$rubro AND numero=$numero";
	$res1 = $conex->Execute($query);
	$query = "SELECT * FROM mat WHERE nro_motor=$motor AND item=$rubro AND numero=$numero";
	$res2 = $conex->Execute($query);

	if($res1->fields['id_prov']==$prov && $res1->fields['cup']==$cup)	return(0);	// Existe la asociacion por lo tanto no la hago
	else if($res2->EOF)																return(2);	// No existe en mat
	else																									// Hago la asociacion
		{
		// Asocio los items en la tabla 'asocia_mat'
		$query = "INSERT INTO asocia_mat VALUES($prov, '$cup', $motor, $rubro, $numero)";
		$res = $conex->Execute($query);
		
		// Asocio el proveedor al material y actualizo su fecha de update
		$query = "UPDATE mat SET id_prov=$prov, `update`='$fecha' WHERE nro_motor=$motor AND item=$rubro AND numero=$numero";
		$res = $conex->Execute($query);
		
		// Actualizo el estado del item en la tabla del proveedor
		$query = "UPDATE prov_mat SET estado=1 WHERE id=$prov AND item=$item";
		$conex->Execute($query);
		return(1);
		}
	}

// Guarda item de proveedor en GR y lo deja asociado
function Prov_GuardarItem($conex, $prov, $item, $motor, $numero, $rubro, $codigo, $desc, $precio)
	{
	// Si algun precio queda en blanco, le asigno cero
	for($i=0; $i<4; $i++)
		if($precio[$i] == '')
			$precio[$i]=0;

	// Busco la fecha de actualizacion del item nuevo
	$query = "SELECT cup, `update` FROM prov_mat WHERE id=$prov AND item=$item";
	$res = $conex->Execute($query);
	$fecha = $res->fields['update'];
	
	// Guardo el nuevo item
	$query = "INSERT INTO mat(nro_motor, item, numero, codigo, desc_mat, precio1, precio2, precio3, precio4, id_prov, `update`)";
	$query = $query." VALUES($motor, $rubro, $numero, '$codigo', '$desc', $precio[0], $precio[1], $precio[2], $precio[3], $prov, '$fecha')";
	$conex->Execute($query);
	
	// Actualizo el estado del item en la tabla del proveedor
	$conex->Execute("UPDATE prov_mat SET estado=1 WHERE id=$prov AND item=$item");
	
	return($numero);
	}

// Me fijo si es posible asociar tal item con motor y rubro del GR
function Prov_PosibleAsociar($conex, $id, $cup, $motor, $rubro)
	{
	$query = "SELECT * FROM asocia_mat WHERE id_prov=$id AND cup='$cup' AND motor=$motor AND item=$rubro";
	$res = $conex->Execute($query);
	if(!$res->EOF)	return 0;
	else 				return 1;
	}

// Busca un item en la base del proveedor para saber si darle alta o editarlo
function Prov_BuscarItem($conex, $id, $cup)
	{
	$query = "SELECT item FROM prov_mat WHERE id=$id AND cup='$cup'";
	$res = $conex->Execute($query);
	return($res->fields['item']);
	}

// Retorna el nombre de un proveedor en base a su id
function Prov_BuscarNombre($conex, $id)
	{
	$query = "SELECT nombre FROM prov_info WHERE id=$id";
	$res = $conex->Execute($query);
	return($res->fields['nombre']);
	}

//****************************************************************************************************************************************************
//*********************************************************      GESTION       ***********************************************************************
//****************************************************************************************************************************************************

// Retorna la descripcion de una seccion
function GES_BuscarDescSeccion($conex, $seccion)
	{
	$res = $conex->Execute("SELECT descripcion FROM secciones WHERE nro=$seccion");
	return($res->fields['descripcion']);
	}

// Retorna la descripcion de un informe
function GES_BuscarDescInforme($conex, $nro)
	{
	$res = $conex->Execute("SELECT desc_inf FROM infe WHERE nro_inf=$nro");
	return($res->fields['desc_inf']);
	}

// Retorna el nombre de un operario
function GES_BuscarNombreOperario($conex, $operario)
	{
        Debugger("GES_BuscarNombreOperario($operario)");
	if(!$operario)		
        $nombre = "Todos";
	else
		{
		$res = $conex->Execute("SELECT nombre FROM operario WHERE id=$operario");
		$nombre = $res->fields['nombre'];        
		}        
	return($nombre);
	}

// Retorna todos los operarios que hay en GR
function BuscarOperarios($conex)
	{
	return($conex->Execute("SELECT * FROM operario ORDER BY id ASC"));
	}

// Retorna el listado de los informes preestablecidos en la base
function ListarInformes($conex)
	{
	$query = "SELECT * FROM infe ORDER BY nro_inf ASC";
	return($conex->Execute($query));
	}

// Muestra la pantalla para seleccionar informe a mostrar
function GES_PantallaBusqueda($conex)
	{
	?>
	<div class="titulo1">Selecccione el Informe y los Filtros que Desee:</div>
	<div>&nbsp;</div>
	<form name="form_informe" id="form_informe" action="informe.php?hacer=1" method="post">
		<table width="40%" align="center">
			<tr>
				<td width="40%" class="head_item_std1">Informe:</td>
				<td width="60%" class="head_item_std2">
					<select name="nro_informe" id="nro_informe">
					<?php 
					$result = ListarInformes($conex);
					while(!$result->EOF)
						{
						$nro_inf = $result->fields['nro_inf'];
						$desc_inf = $result->fields['desc_inf'];
						?>
						<option value="<?php echo $nro_inf?>"><?php echo $desc_inf?></option>
						<?php 
						$result->MoveNext();
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40%" class="head_item_std1">Operario:</td>
				<td width="60%" class="head_item_std2">
					<select name="operario" id="operario">
						<option value="0">Todos</option>
						<?php 
						$result = BuscarOperarios($conex);
						while(!$result->EOF)
							{
							$id = $result->fields['id'];
							$nombre = $result->fields['nombre'];
							?>
							<option value="<?php echo $id?>"><?php echo $nombre?></option>
							<?php 
							$result->MoveNext();
							}
						?>
					</select>
				</td>
			</tr>
		</table>
		<table width="40%" align="center">
			<tr>
				<td colspan="3" class="head_std">Desde Fecha:</td>
			</tr>
			<tr>
				<td width="33%" class="etiqueta2"><label>Día:<input type="text" name="dia1" id="dia1" size="4" maxlength="4" /></label></td>
				<td width="33%" class="etiqueta2"><label>Mes:<input type="text" name="mes1" id="mes1" size="4" maxlength="4" /></label></td>
				<td width="34%" class="etiqueta2"><label>Año:<input type="text" name="anio1" id="anio1" size="4" maxlength="4" /></label></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" class="head_std">Hasta Fecha:</td>
			</tr>
			<tr>
				<td class="etiqueta2"><label>Día:<input type="text" name="dia2" id="dia2" size="4" maxlength="4" /></label></td>
				<td class="etiqueta2"><label>Mes:<input type="text" name="mes2" id="mes2" size="4" maxlength="4" /></label></td>
				<td class="etiqueta2"><label>Año:<input type="text" name="anio2" id="anio2" size="4" maxlength="4" /></label></td>
			</tr>
		</table>
		<div>&nbsp;</div>
		<div align="center"><input name="buscar" type="submit" value="  Buscar  "/></div>
	</form>
	<?php 
	}

// Muestra un informe en base a los filtros pedidos
function GES_MostrarInforme($conex)
	{
	// Recupero los datos de los filtros ingresados
	$informe = $_POST['nro_informe'];
	$operario = $_POST['operario'];
	if($_POST['anio1'] != "" && $_POST['mes1'] != "" && $_POST['dia1'] != "")
		{
		$fecha1 = $_POST['anio1'].'-'.$_POST['mes1'].'-'.$_POST['dia1'];
		$date1 = $_POST['dia1'].'-'.$_POST['mes1'].'-'.$_POST['anio1'];
		}
	else
		{
		$fecha1=NULL;
		$date1='Inicio';
		}
	if($_POST['anio2'] != "" && $_POST['mes2'] != "" && $_POST['dia2'] != "")
		{
		$fecha2 = $_POST['anio2'].'-'.$_POST['mes2'].'-'.$_POST['dia2'];
		$date2 = $_POST['dia2'].'-'.$_POST['mes2'].'-'.$_POST['anio2'];
		}
	else
		{
		$fecha2=NULL;
		$date2='Actual';
		}

	// Encabezado de Informe
	?>
	<div class="titulo1">Informe Solicitado:</div>
	<div>&nbsp;</div>
	<table width="40%" align="center">
		<tr>
			<td width="35%" class="head_item_std1">Número:</td>
			<td width="65%" class="head_item_std2"><?php echo $informe?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Descripción:</td>
			<td class="head_item_std2"><?php echo htmlentities(GES_BuscarDescInforme($conex, $informe))?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Operario:</td>
			<td class="head_item_std2"><?php echo htmlentities(GES_BuscarNombreOperario($conex, $operario))?></td>
		</tr>		<tr>
			<td class="head_item_std1">Desde Fecha:</td>
			<td class="head_item_std2"><?php echo $date1?></td>
		</tr>
		<tr>
			<td class="head_item_std1">Hasta Fecha:</td>
			<td class="head_item_std2"><?php echo $date2?></td>
		</tr>
	</table>
	<div>&nbsp;</div>
	<?php 
	// Busco las secciones que abarca el informe
	$secciones = $conex->Execute("SELECT seccion FROM infd WHERE nro_inf=$informe");
	// Por cada seccion busco y muestro los resultados de los items involucrados
	$totalMOB=$totalMAT=$total_global=0;
	while(!$secciones->EOF)
		{
		$seccion=$secciones->fields['seccion'];	// Seccion en proceso
		$subtot_MOB=$subtot_MAT=0;
		
		// Busco los items MOB que tiene la seccion
		$query = "SELECT mobe.numero, mobe.descripcion, SUM(otd.cantidad) AS cantidad, SUM(otd.importe*otd.cantidad) AS total";
		$query = $query." FROM mobe INNER JOIN otd INNER JOIN ote";
		$query = $query." WHERE otd.numero=mobe.numero AND otd.item=0 AND mobe.seccion=$seccion AND otd.nro_ot=ote.nro_ot";
		if($fecha1 && !$fecha2)
			$query = $query." AND ote.fecha >= '$fecha1'";
		else if(!$fecha1 && $fecha2)
			$query = $query." AND ote.fecha <= '$fecha2'";
		else if($fecha1 && $fecha2)
			$query = $query." AND ote.fecha BETWEEN '$fecha1' AND '$fecha2'";
		if($operario)
			$query = $query." AND real_by=$operario";	// Si quiero filtrar por operario que realizo
		$query = $query." GROUP BY mobe.numero";
		$MOB = $conex->Execute($query);		
		
		// Busco los items MAT que tiene la seccion (zarpada-consulta.com jeje)
		$query = "SELECT SUM(otd.cantidad) AS cantidad, mat.codigo, CONCAT_WS(' ', rub.desc_rubro, mat.desc_mat) AS descripcion, SUM(otd.importe*otd.cantidad) AS total";
		$query = $query." FROM ote INNER JOIN otd INNER JOIN mat INNER JOIN rub INNER JOIN pse";
		$query = $query." WHERE otd.item=mat.item AND otd.numero=mat.numero AND mat.item=rub.nro_rubro AND otd.nro_ot=ote.nro_ot";
		$query = $query." AND ote.nro_std=pse.nro_pres AND pse.nro_motor=mat.nro_motor AND rub.seccion=$seccion";
		if($fecha1 && !$fecha2)
			$query = $query." AND ote.fecha >= '$fecha1'";
		else if(!$fecha1 && $fecha2)
			$query = $query." AND ote.fecha <= '$fecha2'";
		else if($fecha1 && $fecha2)
			$query = $query." AND ote.fecha BETWEEN '$fecha1' AND '$fecha2'";
		if($operario)
			$query = $query." AND real_by=$operario";	// Si quiero filtrar por operario que realizo
		$query = $query." GROUP BY otd.item";
		$MAT = $conex->Execute($query);

		// Encabezado de Seccion
		if(!$MOB->EOF || !$MAT->EOF)
			{
			?>
			<table width="75%" align="center">
				<tr>
					<td class="head_seccion">Sección: <?php echo htmlentities(GES_BuscarDescSeccion($conex, $seccion))?></td>
				</tr>
			</table>
			<?php 
			// Muestro MOB
			if(!$MOB->EOF)
				{
				?>
				<table width="75%" align="center">
					<tr>
						<td colspan="3" class="head_std">MANO DE OBRA</td>
					</tr>
					<tr>
						<td width="10%" class="head_item_std3">Cantidad</td>
						<td width="75%" class="head_item_std3">Descripción</td>
						<td width="15%" class="head_item_std3">Total ($)</td
					></tr>
				<?php
				$i=0;
				while(!$MOB->EOF)
					{
					$cant=$MOB->fields['cantidad'];
					$desc=$MOB->fields['descripcion'];
					$subtot=$MOB->fields['total'];
					if($i%2)	{?><tr class="gris_claro"><?php } else {?><tr class="gris_oscuro"><?php }?>
						<td align="center"><?php echo $cant?></td>
						<td><?php echo htmlentities($desc)?></td>
						<td align="right"><?php echo $subtot?></td
					></tr>
					<?php
					$i++;
					$subtot_MOB+=$subtot;
					$MOB->MoveNext();
					}
					?>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="subtotal"><?php echo "$ ".sprintf("%.2f", $subtot_MOB)?></td>
					</tr>
				</table>
				<div>&nbsp;</div>
				<?php
				}
	
			// Muestro MAT
			if(!$MAT->EOF)
				{
				?>
				<table width="75%" align="center">
					<tr>
						<td colspan="4" class="head_std">MATERIALES</td>
					</tr>
					<tr>
						<td width="10%" class="head_item_std3">Cantidad</td>
						<td width="15%" class="head_item_std3">Código</td>
						<td width="60%" class="head_item_std3">Descripción</td>
						<td width="15%" class="head_item_std3">Total ($)</td
					></tr>
				<?php
				$i=0;
				while(!$MAT->EOF)
					{
					$cant=$MAT->fields['cantidad'];
					$cod=$MAT->fields['codigo'];
					$desc=$MAT->fields['descripcion'];
					$subtot=$MAT->fields['total'];
					if($i%2)	{?><tr class="gris_claro"><?php } else {?><tr class="gris_oscuro"><?php }?>
						<td align="center"><?php echo $cant?></td>
						<td><?php echo htmlentities($cod)?></td>
						<td><?php echo htmlentities($desc)?></td>
						<td align="right"><?php echo $subtot?></td
					></tr>
					<?php
					$i++;
					$subtot_MAT+=$subtot;
					$MAT->MoveNext();
					}
					?>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="subtotal"><?php echo "$ ".sprintf("%.2f", $subtot_MAT)?></td>
					</tr>
				</table>
				<div>&nbsp;</div>
				<?php
				}
			}
		
		$totalMOB+=$subtot_MOB;
		$totalMAT+=$subtot_MAT;
		$secciones->MoveNext();
		}

	// Seccion AGREGADOS
	// Busco los items MOB
	$query = "SELECT otd.numero, otd.descripcion, otd.cantidad, SUM(otd.importe*otd.cantidad) AS total";
	$query = $query." FROM otd INNER JOIN ote";
	$query = $query." WHERE otd.item=0 AND otd.agregado=1 AND otd.nro_ot=ote.nro_ot";
	if($fecha1 && !$fecha2)
		$query = $query." AND ote.fecha >= '$fecha1'";
	else if(!$fecha1 && $fecha2)
		$query = $query." AND ote.fecha <= '$fecha2'";
	else if($fecha1 && $fecha2)
		$query = $query." AND ote.fecha BETWEEN '$fecha1' AND '$fecha2'";
	if($operario)
		$query = $query." AND real_by=$operario";	// Si quiero filtrar por operario que realizo
	$query = $query." GROUP BY otd.descripcion";
	$MOB = $conex->Execute($query);		
	
	// Busco los items MAT
	$query = "SELECT otd.item, otd.numero, otd.codigo, otd.descripcion, otd.cantidad, SUM(otd.importe*otd.cantidad) AS total";
	$query = $query." FROM otd INNER JOIN ote";
	$query = $query." WHERE otd.item<>0 AND otd.agregado=1 AND otd.nro_ot=ote.nro_ot";
	if($fecha1 && !$fecha2)
		$query = $query." AND ote.fecha >= '$fecha1'";
	else if(!$fecha1 && $fecha2)
		$query = $query." AND ote.fecha <= '$fecha2'";
	else if($fecha1 && $fecha2)
		$query = $query." AND ote.fecha BETWEEN '$fecha1' AND '$fecha2'";
	if($operario)
		$query = $query." AND real_by=$operario";	// Si quiero filtrar por operario que realizo
	$query = $query." GROUP BY otd.descripcion";
	$MAT = $conex->Execute($query);

	// Encabezado de Seccion
	$subtot_MOB=$subtot_MAT=0;
	if(!$MOB->EOF || !$MAT->EOF)
		{
		?>
		<table width="75%" align="center">
			<tr>
				<td class="head_seccion">Sección: AGREGADOS</td>
			</tr>
		</table>
		<?php
		// Muestro MOB
		if(!$MOB->EOF)
			{
			?>
			<table width="75%" align="center">
				<tr>
					<td colspan="3" class="head_std">MANO DE OBRA</td>
				</tr>
				<tr>
					<td width="10%" class="head_item_std3">Cantidad</td>
					<td width="75%" class="head_item_std3">Descripción</td>
					<td width="15%" class="head_item_std3">Total ($)</td
				></tr>
			<?php
			$i=0;
			while(!$MOB->EOF)
				{
				$cant=$MOB->fields['cantidad'];
				$desc=$MOB->fields['descripcion'];
				$subtot=$MOB->fields['total'];
				if($i%2)	{?><tr class="gris_claro"><?php } else {?><tr class="gris_oscuro"><?php }?>
					<td align="center"><?php echo $cant?></td>
					<td><?php echo htmlentities($desc)?></td>
					<td align="right"><?php echo $subtot?></td
				></tr>
				<?php
				$i++;
				$subtot_MOB+=$subtot;
				$MOB->MoveNext();
				}
				?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="subtotal"><?php echo "$ ".sprintf("%.2f", $subtot_MOB)?></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<?php
			}

		// Muestro MAT
		if(!$MAT->EOF)
			{
			?>
			<table width="75%" align="center">
				<tr>
					<td colspan="4" class="head_std">MATERIALES</td>
				</tr>
				<tr>
					<td width="10%" class="head_item_std3">Cantidad</td>
					<td width="15%" class="head_item_std3">Código</td>
					<td width="60%" class="head_item_std3">Descripción</td>
					<td width="15%" class="head_item_std3">Total ($)</td
				></tr>
			<?php
			$i=0;
			while(!$MAT->EOF)
				{
				$cant=$MAT->fields['cantidad'];
				$cod=$MAT->fields['codigo'];
				$desc=$MAT->fields['descripcion'];
				$subtot=$MAT->fields['total'];
				if($i%2)	{?><tr class="gris_claro"><?php } else {?><tr class="gris_oscuro"><?php }?>
					<td align="center"><?php echo $cant?></td>
					<td><?php echo htmlentities($cod)?></td>
					<td><?php echo htmlentities($desc)?></td>
					<td align="right"><?php echo $subtot?></td
				></tr>
				<?php
				$i++;
				$subtot_MAT+=$subtot;
				$MAT->MoveNext();
				}
				?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="subtotal"><?php echo "$ ".sprintf("%.2f", $subtot_MAT)?></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<?php
			}
		}
	
	$totalMOB+=$subtot_MOB;
	$totalMAT+=$subtot_MAT;
	
	// Muestro los totales del informe
	?>
	<table width="75%" align="center">
		<tr>
			<td width="10%">&nbsp;</td>
			<td width="60%">&nbsp;</td>
			<td width="15%" class="total">Total MOB:</td>
			<td width="15%" class="total"><?php echo "$ ".sprintf("%.2f", $totalMOB)?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="total">Total MAT:</td>
			<td class="total"><?php echo "$ ".sprintf("%.2f", $totalMAT) ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="total">Total:</td>
			<td class="total"><?php echo "$ ".sprintf("%.2f", $totalMOB+$totalMAT) ?></td>
		</tr>
	</table>
	<div>&nbsp;</div>
	<?php
	}

// Muestra pantalla para crear informe nuevo
function GES_NuevoInforme($conex)
	{
	// Busco las secciones
	$res =  MOB_BuscarSecciones($conex);
	?>
	<div class="titulo1">Nuevo Informe</div>
	<div>&nbsp;</div>
	<form id="newinf" name="newinf" method="post" action="informe.php?new&hacer=1">
	<table width="50%" align="center">
		<tr>
			<td width="25%" class="head_item_std1">Descripción:</td>
			<td width="75%" class="head_item_std2"><input type="text" id="descripcion" name="descripcion" maxlength="100" size="60"/></td>
		</tr>
	</table>
	<table width="50%" align="center">
		<tr>
			<td colspan="2" class="head_std" align="center">Secciones</td>
		</tr>
		<?php
		while(!$res->EOF)
			{
			$numero = $res->fields['nro'];
			$desc = $res->fields['descripcion'];
			?>
			<tr>
				<td width="2%" class="head_item_std1"><input type="checkbox" id="items<?php echo $numero?>" name="items<?php echo $numero?>" onclick="GESContarSecciones(<?php echo $numero?>)"/></td>
				<td width="98%" class="head_item_std2"><?php echo htmlentities($desc)?></td>
			</tr>
			<?php
			$res->MoveNext();
			}
		?>
	</table>
	<div>&nbsp;</div>
	<div align="center"><input type="button" value="  Guardar  " name="guardar" onclick="GESGuardarInforme()"/></div>
	<input type="hidden" id="secciones" value="0" />
	</form>
	<?php
	}

// Guarda el nuevo informe
function GES_GuardarInforme($conex)
	{
	// Recupero los datos de la pantalla de creacion
	$desc = $_POST['descripcion'];			// Lenvanto la descripcion
	$res =  MOB_BuscarSecciones($conex);	// Busco las secciones
	$i=0;
	$secciones=array();
	while(!$res->EOF)
		{
		$num = $res->fields['nro'];
		if(isset($_POST['items'.$num]))	$secciones[$i++]=$num;
		$res->MoveNext();
		}

	// Guardo el informe
	// Encabezado
	$res = $conex->Execute("SELECT nro_inf FROM infe ORDER BY nro_inf DESC LIMIT 1");
	$nro = $res->fields['nro_inf']+1;
	$conex->Execute("INSERT INTO infe VALUES($nro, '$desc')");
	// Secciones que contiene
	for($i=0; $i<count($secciones); $i++)
		{
		$sec=$secciones[$i];
		$conex->Execute("INSERT INTO infd VALUES($nro, $sec)");
		}
	?>
	<div class="titulo2" align="center">El Informe ha Sido Guardado con Éxito</div>
	<div>&nbsp;</div>
	<table width="50%" align="center">
		<tr>
			<td width="45%" align="right"><input type="button" name="cancelar" value="   Cancelar   " onclick="GESCancelar()" /></td>
			<td width="10%">&nbsp;</td>
			<td width="45%" align="left"><input type="button" name="nuevo" value=" Nuevo Informe" onclick="GESNewInforme()" /></td>
		</tr>
	</table>
	<?php
	}

// Muestra los botones para ordenar los items por OT o Seccion
function GES_BotonesOrden($tipo)
	{
	?>
	<div>&nbsp;</div>
   <table width="90%" align="center">
      <tr>
			<td width="80%">&nbsp;</td>
         <td width="10%" align="right"><input type="button" name="orden1" value="   Ordenar por OT   " onClick="GES_OrdenItem(<?php echo $tipo?>, <?php echo "2"?>)"/></td>
         <td width="10%" align="right"><input type="button" name="orden2" value="Ordenar por Sección" onClick="GES_OrdenItem(<?php echo $tipo?>, <?php echo "3"?>)"/></td>
      </tr>
   </table>
	<?php
	}

// Busca los estados posibles para un item MOB o MAT
function GES_BuscarEstados($conex, $tipo)
	{
	$query = "SELECT * FROM estado WHERE tipo=$tipo";
	return($conex->Execute($query));
	}

// Chequea si un item MOB fue realizado
function GES_CheckEstadoMOB($conex, $nro_ot, $item, $numero, $agregado)
	{
        Debugger("GES_CheckEstadoMOB($nro_ot, $item, $numero, $agregado)");
        $query = "SELECT estado FROM opd WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado";
        Debugger($query);
	    $res = $conex->Execute($query);
        Debugger('Estado = '.$res->fields['estado']);
	    return($res->fields['estado']);
	}

// Verifica si un item MOB existe en la orden de produccion de la fecha pasada como parametro
function GES_BuscarEstadoMOB($conex, $nro_ot, $item, $numero, $agregado)
	{
        Debugger("Librerias/funciones.php - GES_BuscarEstadoMOB(nro_ot=$nro_ot, item=$item, numero=$numero, agregado=$agregado)");
        Debugger("Verifica si un item MOB existe en la orden de produccion");

	$query = "SELECT COUNT(*) AS estado FROM opd ";
	$query = $query." WHERE opd.nro_ot=$nro_ot AND opd.item=$item AND opd.numero=$numero AND opd.agregado=$agregado";

	$res = $conex->Execute($query);
    Debugger($query);
    Debugger("res.estado = ".$res->fields['estado']);
	return($res->fields['estado']);
	}

// Busca los items MOB para conformar la orden de produccion y los items MAT para Gestionarlos
function GES_MostrarItems($conex, $orden, $tipo)
	{
	// Busco los items requeridos en base al orden solicitado y al tipo(MOB-MAT)
    Debugger("Librerias/funciones.php - GES_MostarItems(orden=$orden,tipo=$tipo");
	if($orden==OT)	// Ordenado por Orden de Trabajo
		{
           Debugger("OT = Ordenado por Orden de Trabajo");
		$query = "SELECT otd.*, ote.prioridad, ote.desc_motor FROM otd INNER JOIN ote";
		$query = $query." WHERE otd.nro_ot=ote.nro_ot AND ote.prioridad<>0 AND otd.fecha_real='0000-00-00' AND";
		if($tipo==MOB)	
            $query = $query." otd.item=0";
		else				
            $query = $query." otd.item<>0";
		$query = $query." ORDER BY ote.prioridad DESC, ote.nro_ot, otd.item, otd.numero ASC";
		}
	else				// Ordenado por Seccion
		{
            Debugger("Ordenado por Seccion");
		if($tipo==MOB)	// Se trata de items MOB
			{
			$query = "SELECT otd.*, ote.prioridad, ote.desc_motor, mobe.seccion FROM mobe INNER JOIN otd INNER JOIN ote";
			$query = $query." WHERE otd.numero=mobe.numero AND otd.item=0 AND otd.nro_ot=ote.nro_ot AND ote.prioridad<>0 AND otd.agregado=0 AND otd.fecha_real='0000-00-00'";
			$query = $query." ORDER BY mobe.seccion ASC, ote.prioridad DESC, otd.nro_ot, otd.item, otd.numero ASC";
			}
		else				// Se trata de items MAT
			{
			$query = "SELECT otd.*, ote.prioridad, ote.desc_motor, rub.seccion FROM ote INNER JOIN otd INNER JOIN rub";
			$query = $query." WHERE otd.item=rub.nro_rubro AND otd.nro_ot=ote.nro_ot AND ote.prioridad<>0 AND otd.agregado=0 AND otd.fecha_real='0000-00-00'";
			$query = $query." ORDER BY rub.seccion ASC, ote.prioridad DESC, otd.nro_ot, otd.item, otd.numero ASC";
			}
		}
    Debugger($query);
	$items = $conex->Execute($query);
	
	if(!$items->EOF)	// Muestro los items
		{
            Debugger("Encontré ítems, armo el encabezado de la tabla y recorro los ítems");
		GES_BotonesOrden($tipo);
		?>
		<table width="90%" align="center">
		<tr>
			<td width="8%" class="head_item_std3">OT</td>
			<td width="30%" class="head_item_std3">Descripción</td>
			<td width="8%" class="head_item_std3">Cantidad</td>
			<td width="40%" class="head_item_std3">Item</td>
			<td width="8%" class="head_item_std3">Prioridad</td>
			<td width="6%" class="head_item_std3">Estado</td>
		</tr>
		<?php
		$seccion_ant=0;
		$i=0;
		while(!$items->EOF)
			{
			$nro_ot = $items->fields['nro_ot'];
			$desc_ot = $items->fields['desc_motor'];
			$cantidad = $items->fields['cantidad'];
			$descripcion = $items->fields['descripcion'];
			$prioridad = $items->fields['prioridad'];
			$item = $items->fields['item'];
			$numero = $items->fields['numero'];
			$agregado = $items->fields['agregado'];
			$id = $nro_ot."-".$item."-".$numero."-".$agregado;
			
			if($orden==SECCION)
				if($seccion_ant != $items->fields['seccion'])
					{
					$seccion_ant = $items->fields['seccion'];
					?>
					<tr><td colspan="6" class="head_seccion">Sección <?php echo htmlentities(GES_BuscarDescSeccion($conex, $seccion_ant))?></td></tr>
					<?php
					}
			if(($i++)%2)	{?><tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php }
			else 				{?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';"><?php }?>
			
			<td align="center"><a href="#" onClick="GES_MostrarOT(<?php echo $nro_ot?>)"><?php echo $nro_ot?></a></td>
			<td align="left"><?php echo htmlentities($desc_ot)?></td>
			<td align="center"><?php echo $cantidad?></td>
			<td align="left"><?php echo htmlentities($descripcion)?></td>
			<td align="center"><?php echo $prioridad?></td>
			<?php
			if($tipo==MOB)
				{
				// Verifico si el item actual existe en la orden de produccion
				//isset($_GET['fecha']) ? $fecha = $_GET['fecha'] : $fecha=date("Y-m-d");
				$estado = GES_BuscarEstadoMOB($conex, $nro_ot, $item, $numero, $agregado);
				?>
				<td align="center">
					<input name="estado" id="estado<?php echo $id?>" type="checkbox" <?php if($estado==1) echo "checked";?> onchange="GES_VerificarMOB(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>)"/>
				</td>
				<?php
				}
			else
				{
				?>
				<td>
					<select name="estado" id="estado<?php echo $id?>" onchange="GES_SetEstadoMaterial(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>)">
					<?php
					$ESTADOS = GES_BuscarEstados($conex, $tipo);
					$res = $conex->Execute("SELECT asig_to FROM otd WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado");
					$estado = $res->fields['asig_to'];
					while(!$ESTADOS->EOF)
						{
						$nro = $ESTADOS->fields['id'];
						$desc = $ESTADOS->fields['descripcion'];
						?>
						<option value="<?php echo $nro?>" <?php if($nro==$estado) echo "selected";?>><?php echo $desc?></option>
						<?php
						$ESTADOS->MoveNext();
						}
					?>
					</select>
				</td>
				<?php
				}
			?>
			</tr>
			<?php
			$items->MoveNext();
			}
		if($orden==SECCION)	// Pongo al final los agregados en una seccion 'AGREGADOS'
			{
			    Debugger("Busco los agregados");
			$query = "SELECT otd.*, ote.prioridad, ote.desc_motor FROM otd INNER JOIN ote";
			$query = $query." WHERE otd.nro_ot=ote.nro_ot AND ote.prioridad<>0 AND otd.agregado=1 AND otd.fecha_real='0000-00-00' AND";
			if($tipo==MOB)	
                $query = $query." item=0";
			else				
                $query = $query." item<>0";
			$query = $query." ORDER BY ote.prioridad DESC, ote.nro_ot, otd.item, otd.numero ASC";

			$items = $conex->Execute($query);
			// Los muestro
			if(!$items->EOF)
				{
				?>
				<tr><td colspan="6" class="head_seccion">Sección AGREGADOS</td></tr>
				<?php
				while(!$items->EOF)
					{
					$nro_ot = $items->fields['nro_ot'];
					$desc_ot = $items->fields['desc_motor'];
					$cantidad = $items->fields['cantidad'];
					$descripcion = $items->fields['descripcion'];
					$prioridad = $items->fields['prioridad'];
					$item = $items->fields['item'];
					$numero = $items->fields['numero'];
					$agregado = $items->fields['agregado'];
					$id = $nro_ot."-".$item."-".$numero."-".$agregado;
					
					if(($i++)%2)
						{?><tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php }
					else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';"><?php }?>
					
					<td align="center"><a href="#" onClick="GES_MostrarOT(<?php echo $nro_ot?>)"><?php echo $nro_ot?></a></td>
					<td align="left"><?php echo htmlentities($desc_ot)?></td>
					<td align="center"><?php echo $cantidad?></td>
					<td align="left"><?php echo htmlentities($descripcion)?></td>
					<td align="center"><?php echo $prioridad?></td>
					<?php
					if($tipo==MOB)
						{
						// Verifico si el item actual existe en la orden de produccion
						isset($_GET['fecha']) ? $fecha = $_GET['fecha'] : $fecha=date("Y-m-d");
						$estado = GES_BuscarEstadoMOB($conex, $fecha, $nro_ot, $item, $numero, $agregado);
						?>
						<td align="center">
							<input name="estado" id="estado<?php echo $id?>" type="checkbox" <?php if($estado==1) echo "checked";?> onchange="GES_VerificarMOB(<?php echo $nro_ot?>, <?php echo $item?>, <?php echo $numero?>, <?php echo $agregado?>, <?php echo "'$fecha'"?>)"/>
						</td>
						<?php
						}
					else
						{
						?>
						<td>
							<select name="estado" id="estado<?php echo $id?>" onchange="GES_">
							<?php
							$ESTADOS = GES_BuscarEstados($conex, $tipo);
							$res = $conex->Execute("SELECT asig_to FROM otd WHERE nro_ot=$nro_ot AND item=$item AND numero=$numero AND agregado=$agregado");
							$estado = $res->fields['asig_to'];
							while(!$ESTADOS->EOF)
								{
								$nro = $ESTADOS->fields['id'];
								$desc = $ESTADOS->fields['descripcion'];
								?>
								<option value="<?php echo $nro?>" <?php if($nro==$estado) echo "selected";?>><?php echo $desc?></option>
								<?php
								$ESTADOS->MoveNext();
								}
							?>
							</select>
						</td>
						<?php
						}
					?>
					</tr>
					<?php
					$items->MoveNext();
					}
				}
			}
			?>
		</table>
		<?php
		}
	}

//****************************************************************************************************************************************************
//*********************************************************      TALLER        ***********************************************************************
//****************************************************************************************************************************************************

// Retorna los items asignados de MOB
function TAL_BuscarItems($conex, $operario)
	{	
        Debugger("TAL_BuscarItems(operario=$operario");
	$query = "SELECT opd.*, otd.cantidad, otd.item, otd.numero, otd.agregado, ote.desc_motor FROM opd INNER JOIN otd INNER JOIN ote";
	$query = $query." WHERE opd.estado<4 AND operario=$operario AND opd.nro_ot=otd.nro_ot AND otd.nro_ot=ote.nro_ot";
	$query = $query." AND opd.item=otd.item AND opd.numero=otd.numero AND opd.agregado=otd.agregado";
	$query = $query." ORDER BY posicion ASC";
    Debugger($query);
	return($conex->Execute($query));
	}

}	// FIN DE DEFINICION "FUNCIONES"
