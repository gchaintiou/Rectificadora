<?php
require_once "adodb/adodb.inc.php";
require_once "funciones.php";

if(!defined('GR_SYS')) // DEFINO LA CLASE GR
{
define('GR_SYS', 100);

// Datos de Conexión
define("HOST", "localhost");
define("USER", "root");
define("PASS", "02954424916");
//define("PASS", "");
define("BASE", "rectificadora");

// Indices para los campos del encabezado
define("NUM_OT", 0);
define("NUM_PRES", 1);
define("NUM_MOTOR", 2);
define("DESC_PRES", 3);
define("DESC_MOTOR", 4);
define("CLIENTE", 5);
define("DIRECCION", 6);
define("TELEFONO", 7);
define("LOCALIDAD", 8);
define("FECHA", 9);
define("HORA", 10);
define("LISTA_MAT", 11);
define("DESCUENTO", 12);
define("NUM_STD", 13);
define("LISTA_MOB", 14);
define("PRIORIDAD", 15);
define("CAMPOS_ENC", 16);

// Flags que Indican los Campos de Cada Tipo de Encabezado
define("ENC_CLI", '0X7EE');
define("ENC_STD", '0X181A');
define("ENC_OT", '0X87EF');

// Definiciones de Tipos y Estados de Variables
define("STD", 0);
define("CLI", 1);
define("OT", 2);
define("EDIT", 1);
define("NO_EDIT", 0);
define("MOB", 1);
define("MAT", 2);
define("SECCION", 3);

// Definiciones de Items de Recibos
define("REC_ITEM_ID", 0);
define("REC_ITEM_TIPO", 1);
define("REC_ITEM_TIPO_DESC", 2);
define("REC_ITEM_OTRO", 3);
define("REC_ITEM_HABER",4);
define("REC_ITEM_BANCO",5);
define("REC_ITEM_LOCALIDAD",6);
define("REC_ITEM_NRO_OP",7);
define("REC_ITEM_FECHA",8);
define("REC_ITEM_DEBE",9);
define("REC_ITEM_SALDO",10);

//********************************************************************************************************************
//*********************************************** CLASE GR ***********************************************************
//********************************************************************************************************************

class GR
	{
	//************************************ VARIABLES PUBLICAS ***************************************
	var $con_sql;					// Conexion a base SQL
	var $encabezado=array();	// Array que contiene info de encabezado
	var $campos_enc;				// Variable de flags para campos de encabezado

	var $tipo;						// Indica si se trata de CLI, STD u OT
	var $nro;						// Indica el indice de CLI, STD u OT
	var $filtros;					// Flags que indican filtros en items a la hora de alta de presupuesto a cliente
	var $items_std;				// Flag que indica si se desean los items STD en un nuevo presupuesto CLI
	var $item_mob=array();		// Array donde se albergan las clases ITEM de MOB
	var $cant_item_mob;			// Cantidad de items MOB
	var $item_mat=array();		// Array donde se albergan las clases ITEM de MAT
	var $cant_item_mat;			// Cantidad de items MAT
	var $subtot_mob;				// Subtotal de MOB
	var $subtot_mat;				// Subtotal de MAT
	var $nota;						// Nota de presupuestos u OT
	var $prioridad;				// Prioridad de una Orden de Trabajo
	var $recibos=array();      // Recibos de una OT
   var $cant_recibo;          // Cantidad de recibos de una OT
   
	var $add_mob;					// Items agregados a MOB
	var $add_mat;					// Items agregados a MAT

	//****************************************** FUNCIONES ******************************************
	// CONSTRUCTOR
	function __construct()
		{
		for($i=0; $i<CAMPOS_ENC; $i++)	$this->encabezado[$i]=0;
		$this->campos_enc=0;
		$this->cant_item_mob=0;
		$this->cant_item_mat=0;
		$this->nota=0;
		$this->tipo=99;
		$this->filtros=0;
		$this->items_std=0;
		$this->add_mob=0;
		$this->add_mat=0;
		$this->prioridad=0;
      $this->cant_recibo=0;
		}

	//***********************************************************************************************
	//************************************** PRESUPUESTOS Y OT **************************************
	//***********************************************************************************************
	
	// Prepara y muestra un presupuesto u orden de trabajo, lo que se especifique en el campo 'tipo'
	// el campo 'nro' indica nro_pres o nro_ot y el campo edit si debe mostrarse para edicion
	function MostrarPresOT($tipo, $nro, $edit, $cargado)
		{
		// Seccion Encabezado
        Debugger("grsys.php - user_gr->MostrarPresOT(tipo=$tipo, nro=$nro, edit=$edit, cargado=$cargado");
		if(!$cargado)	
            $this->ArmarEncabezado($tipo, $nro);
		$this->MostrarEncabezado($edit);
		// Botones
		if($edit)	
            BotonesEdicion($tipo, $nro);
		else			
            BotonesDecision($tipo, $this->encabezado[NUM_PRES], $this->encabezado[NUM_OT]);
		// Seccion MOB
		if(!$cargado)	$this->ArmarMOB($tipo, $nro, $edit);
		$this->MostrarMOB($tipo, $edit);
		// Seccion MAT
		if(!$cargado)	$this->ArmarMAT($tipo, $nro, $edit);
		$this->MostrarMAT($tipo, $edit);
		// Seccion Total
		$this->MostrarTotal();
		// Botones
		if($edit)	BotonesEdicion($tipo, $nro);
		else			BotonesDecision($tipo, $this->encabezado[NUM_PRES], $this->encabezado[NUM_OT]);
		?>
		<p><span class="nota" id="span_nota"><?php if($this->HayNota()) echo "NOTA: ".$this->GetNota()?></span></p>
		<?php
      // Recibos de pagos de OT
      if($tipo==OT) {
         // Seccion Recibos
         if(!$cargado)	$this->ArmarRECIBOS($nro);
         $this->MostrarRECIBOS($nro);
      }
		}
	
	// Guarda el Presupuesto/OT que esta almacenado en la clase
	function GuardarPresOT()
		{
		$this->con_sql = $this->GetConex();
        Debugger("GuardarPresOT - tipo =".$this->tipo. ", nro=".$this->nro);
		switch($this->tipo)
			{
			case STD: // 0
				if(!$this->nro)	$query = "SELECT nro_pres AS nro FROM pse ORDER BY nro_pres DESC LIMIT 1";
				else 					$query = "DELETE FROM psd WHERE nro_pres=$this->nro";
				break;
			case CLI: // 1
				if(!$this->nro)	$query = "SELECT nro_pres AS nro FROM pce ORDER BY nro_pres DESC LIMIT 1";
				else 					$query = "DELETE FROM pcd WHERE nro_pres=$this->nro";
				break;
			case OT: // 2
				if(!$this->nro)	$query = "SELECT nro_ot AS nro FROM ote ORDER BY nro_ot DESC LIMIT 1";
				else 					$query = "SELECT * FROM otd WHERE nro_ot=$this->nro AND item=0 ORDER BY agregado, numero ASC";
				break;
			}
        Debugger($query);
		$res = $this->con_sql->Execute($query);
		if(!$this->nro)	{	$this->nro = $res->fields['nro']+1;	$nuevo=1;	}

		// Bucle que guarda los items de MOB
		$suma_man=0;
		$fecha=date("Y-m-d");
		for($i=0; $i<$this->cant_item_mob; $i++)
			{
			$numero = $this->item_mob[$i]->GetNumero();
			$agregado = $this->item_mob[$i]->GetAgregado();			

			if($this->item_mob[$i]->GetCantidad())
				{
				$descripcion = html_entity_decode($this->item_mob[$i]->GetDescripcion());
				$cantidad = $this->item_mob[$i]->GetCantidad();
				$importe = $this->item_mob[$i]->GetImporte();
				
				$suma_man += $importe * $cantidad;
				switch($this->tipo)
					{
					case STD:
						$query = "INSERT INTO psd VALUES($this->nro, $cantidad, 0, $numero)";
						break;
					case CLI:
						$query = "INSERT INTO pcd(`nro_pres`, `cantidad`, `item`, `numero`, `agregado`, `codigo`, `descripcion`, `importe`)";
						$query = $query." VALUES($this->nro, $cantidad, 0, $numero, $agregado, 'MANODEOBRA', '$descripcion', $importe)";
						break;
					case OT:
						$existe = $this->con_sql->Execute("SELECT * FROM otd WHERE nro_ot=$this->nro AND item=0 AND numero=$numero AND agregado=$agregado");
						if(!$existe->EOF)
							{
							$fecha = $existe->fields['fecha_asig'];
							if($fecha=='0000-00-00')
								$fecha=date("Y-m-d");
							$query = "UPDATE otd set cantidad=$cantidad, descripcion='$descripcion', importe=$importe, fecha_asig='$fecha'";
							$query = $query." WHERE nro_ot=$this->nro AND item=0 AND numero=$numero AND agregado=$agregado";
							}
						else
							{
							$query = "INSERT INTO otd(`nro_ot`, `cantidad`, `item`, `numero`, `agregado`, `actualizacion`, `codigo`, `descripcion`, `importe`, `fecha_asig`)";
							$query = $query." VALUES($this->nro, $cantidad, 0, $numero, $agregado, 0, 'MANODEOBRA', '$descripcion', $importe, '$fecha')";
							}
						break;
					}
				$this->con_sql->Execute($query);
				}
			else
				{
				if($this->tipo==OT)
					{
					$existe = $this->con_sql->Execute("SELECT real_by FROM otd WHERE nro_ot=$this->nro AND item=0 AND numero=$numero AND agregado=$agregado");
					if(!$existe->EOF)
						{
						if($existe->fields['real_by'])	MostrarLinea("No se Puede Eliminar: ".$this->item_mob[$i]->GetDescripcion().", el Trabajo ya fue Realizado", "etiqueta1");
						else	$this->con_sql->Execute("DELETE FROM otd WHERE nro_ot=$this->nro AND item=0 AND numero=$numero AND agregado=$agregado");
						}
					}
				}
			}
	Debugger("Bucle que guarda los items MAT");
	  $suma_mat=0;
      //$query = "DELETE FROM otd WHERE nro_ot=$this->nro AND item<>0";
      //Debugger($query);
	  //$this->con_sql->Execute($query);
		for($i=0; $i<$this->cant_item_mat; $i++) {
			if($this->item_mat[$i]->GetCantidad())
				{
				$item = $this->item_mat[$i]->GetRubro();
				$numero = $this->item_mat[$i]->GetNumero();
				$cantidad = $this->item_mat[$i]->GetCantidad();
				$codigo = $this->item_mat[$i]->GetCodigo();
				$descripcion = html_entity_decode($this->item_mat[$i]->GetDescripcion());
				$importe = $this->item_mat[$i]->GetImporte();
				$agregado = $this->item_mat[$i]->GetAgregado();			

				$suma_mat += $importe * $cantidad;
                Debugger("this.tipo = ".$this->tipo);
				switch($this->tipo)
					{
                        case STD:
                            $query = "INSERT INTO psd VALUES($this->nro, $cantidad, $item, $numero)";
                            break;
                        case CLI:
                            $query = "INSERT INTO pcd(`nro_pres`, `cantidad`, `item`, `numero`, `agregado`, `codigo`, `descripcion`, `importe`)";
                            $query = $query." VALUES($this->nro, $cantidad, $item, $numero, $agregado, '$codigo', '$descripcion', $importe)";
                            break;
                        case OT:
                            $query = "SELECT * FROM otd WHERE otd.nro_ot = ".$this->nro." AND otd.item = $item AND otd.numero=$numero";                        
                            Debugger($query);
                            $res1 = $this->GetConex()->Execute($query);
                            if ($res1 != null) {
                                if ($res1->EOF) {
                                    $query = "INSERT INTO otd(`nro_ot`, `cantidad`, `item`, `numero`, `agregado`, `actualizacion`, `codigo`, `descripcion`, `importe`)";
                                    $query = $query." VALUES($this->nro, $cantidad, $item, $numero, $agregado, 0, '$codigo', '$descripcion', $importe)";
                                } else {
                                    $query = "UPDATE otd SET cantidad = $cantidad, codigo = $codigo, descripcion = '$descripcion', importe = $importe";
                                    $query = $query. " WHERE otd.nro_ot = ".$this->nro. " AND otd.item = $item AND otd.numero=$numero";
                                }
                            }
                            else
                                Debugger("res1 es Null");

                            break;
					}
                Debugger($query);
				$this->con_sql->Execute($query);
		  		} // if($this->item_mat[$i]->GetCantidad())
      } 
        Debugger("Fin del bucle FOR");  
		// Encabezado del PRES/OT
		$desc_pres=$this->encabezado[DESC_PRES];
		$descuento=$this->encabezado[DESCUENTO];
		if($this->tipo!=STD)
			{
			$cliente=$this->encabezado[CLIENTE];
			$direccion=$this->encabezado[DIRECCION];
			$telefono=$this->encabezado[TELEFONO];
			$localidad=$this->encabezado[LOCALIDAD];
			$lista=$this->encabezado[LISTA_MOB];
			$fecha=date("Y-m-d");
			$hora=date("H:i:s");
			$nro_std=$this->encabezado[NUM_STD];
			$mob=$this->subtot_mob;
			$mat=$this->subtot_mat;
			$nro_ot=$this->encabezado[NUM_OT];
			$nota=$this->GetNota();
			}
		else
			{
			$motor=$this->encabezado[NUM_MOTOR];
			$lista=$this->encabezado[LISTA_MAT];
			}

		if(isset($nuevo))
			{
			switch($this->tipo)
				{
				case STD:
					$query = "INSERT INTO pse VALUES($this->nro, $motor, '$desc_pres', 1, $descuento)";
					break;
				case CLI:
					$query = "INSERT INTO pce VALUES('$desc_pres', $this->nro, '$cliente', '$direccion', '$telefono', '$localidad',";
					$query = $query." $lista, $descuento, '$fecha', '$hora', $nro_std, $mob, $mat, 0, '$nota')";
					break;
				case OT:
					$nro_pres=$this->encabezado[NUM_PRES];
					$this->con_sql->Execute("UPDATE pce SET nro_ot=$this->nro WHERE nro_pres=$nro_pres");
					$query = "INSERT INTO ote VALUES('$desc_pres', $this->nro, '$cliente', '$direccion', '$telefono', '$localidad',";
					$query = $query." $lista, $descuento, '$fecha', '$hora', $nro_std, $mob, $mat, $nro_pres, '$nota', $this->prioridad)";
					break;
				}
			}
		else
			{
			switch($this->tipo)
				{
				case STD:
					$query = "UPDATE pse SET desc_pres='$desc_pres', descuento=$descuento WHERE nro_pres=$this->nro";
					break;
				case CLI:
					$query = "UPDATE pce SET `desc_motor`='$desc_pres', `nombre`='$cliente', `direccion`='$direccion', `telefono`='$telefono', `localidad`='$localidad'";
					$query = $query.", `descuento`=$descuento, `fecha`='$fecha', `hora`='$hora', `mob`=$mob, `mat`=$mat, `nota`='$nota' WHERE nro_pres=$this->nro";
					break;
				case OT:
					$query = "UPDATE ote SET `desc_motor`='$desc_pres', `nombre`='$cliente', `direccion`='$direccion', `telefono`='$telefono', `localidad`='$localidad'";
					$query = $query.", `descuento`=$descuento, `fecha`='$fecha', `hora`='$hora', `mob`=$mob, `mat`=$mat, `nota`='$nota', prioridad=$this->prioridad";
					$query = $query." WHERE nro_ot=$this->nro";
					break;
				}
			}
		$this->con_sql->Execute($query);
		
		$this->MostrarPresOT($this->tipo, $this->nro, NO_EDIT, 0);
		if($this->tipo)
			{
			?>
			<script language="javascript"> Imprimir(); </script>
			<?php	
			}
		}
	
	// Presenta Presupuesto/OT en formato de impresion (Lo hago aca porque estan mas accesibles los datos)
	function ImprimirPresOT($nivel)
		{
		?>
		<p class="print_titulo">
			<?php 
			if($this->tipo==CLI) {	echo ("Presupuesto a Cliente Nº: ".$this->encabezado[NUM_PRES]);	}
			else						{	echo ("Orden de Trabajo Nº: ".$this->encabezado[NUM_OT]);			}
			?>
		</p>
		<table width="80%" align="center">
			<tr>
				<td width="19%" class="print_enc" align="right">Descripción:</td>
				<td colspan="3" class="print_enc" align="left"><?php echo $this->encabezado[DESC_PRES]?></td>
			</tr>
			<tr>
				<td class="print_enc" align="right">Cliente:</td>
				<td colspan="3" class="print_enc" align="left"><?php echo $this->encabezado[CLIENTE]?></td>
			</tr>
			<tr>
				<td class="print_enc" align="right">Dirección:</td>
				<td colspan="3" class="print_enc" align="left"><?php echo $this->encabezado[DIRECCION]?></td>
			</tr>
			<tr>
				<td class="print_enc" align="right">Teléfono:</td>
				<td colspan="3" class="print_enc" align="left"><?php echo $this->encabezado[TELEFONO]?></td>
			</tr>
			<tr>
				<td class="print_enc" align="right">Localidad</td>
				<td colspan="3" class="print_enc" align="left"><?php echo $this->encabezado[LOCALIDAD]?></td>
			</tr>
			<tr>
				<td height="21" class="print_enc" align="right">Fecha:</td>
				<td width="15%" class="print_enc" align="left"><?php echo $this->encabezado[FECHA]?></td>
				<td width="4%" class="print_enc" align="right">Hora:</td>
				<td width="62%" class="print_enc" align="left"><?php echo $this->encabezado[HORA]?></td>
			</tr>
		</table>
		<p>&nbsp;</p>
		<?php
		if($this->cant_item_mob)
			{
			?>
			<table width="80%" border="1" align="center">
				<tr>
					<td colspan="4" class="print_head_tabla">MANO DE OBRA</td>
				</tr>
				<tr>
					<?php
					if($nivel<2)
						{
						?>
						<td width="5%" class="print_head_celda" align="center">Cant.</td>
						<td width="75%" class="print_head_celda" align="left">Descripción</td>
						<td width="10%" class="print_head_celda" align="right">Unitario ($)</td>
						<td width="10%" class="print_head_celda" align="right">Total ($)</td>
						<?php
						}
					else
						{
						?>
						<td width="5%" class="print_head_celda" align="center">Cant.</td>
						<td width="95%" class="print_head_celda" align="left">Descripción</td>
						<?php
						}
						?>
				</tr>
			</table>
			<table width="80%" align="center">
			<?php
			for($i=0; $i<$this->cant_item_mob; $i++)
				{
				?>
				<tr>
					<td width="5%" class="print_item" align="center"><?php echo $this->item_mob[$i]->GetCantidad()?></td>
					<td width="75%" class="print_item" align="left"><?php echo $this->item_mob[$i]->GetDescripcion()?></td>
					<?php
					if($nivel<2)
						{
						?>
						<td width="10%" class="print_item" align="right"><?php echo $this->item_mob[$i]->GetImporte()?></td>
						<td width="10%" class="print_item" align="right"><?php echo sprintf("%.2f", $this->item_mob[$i]->GetImporte() * $this->item_mob[$i]->GetCantidad())?></td>
						<?php
						}
					else
						{
						?>
						<td width="10%"></td>
						<td width="10%"></td>
						<?php
						}
						?>
				</tr>
				<?php
				}
			if($nivel<3)
				{
				?>
				<tr>
					<td class="print_total_celda">&nbsp;</td>
					<td class="print_total_celda">&nbsp;</td>
					<td class="print_total_celda">SUBTOTAL:</td>
					<td class="print_total_celda">$ <?php echo sprintf("%.2f", $this->subtot_mob)?></td>
				</tr>
				<?php
				}
				?>
			</table>
			<?php
			}
			?>
		<p>&nbsp;</p>
		<?php
		if($this->cant_item_mat)
			{
			?>
			<table width="80%" border="1" align="center">
				<tr>
					<td colspan="5" class="print_head_tabla">MATERIALES</td>
				</tr>
				<tr>
					<?php
					if($nivel<2)
						{
						?>
						<td width="5%" scope="col" class="print_head_celda" align="center">Cant.</td>
						<td width="10%" scope="col" class="print_head_celda" align="left">Código</td>
						<td width="65%" scope="col" class="print_head_celda" align="left">Descripción</td>
						<td width="10%" scope="col" class="print_head_celda" align="right">Unitario ($)</td>
						<td width="10%" scope="col" class="print_head_celda" align="right">Total ($)</td>
						<?php
						}
					else
						{
						?>
						<td width="5%" scope="col" class="print_head_celda" align="center">Cant.</td>
						<td width="10%" scope="col" class="print_head_celda" align="left">Código</td>
						<td width="85%" scope="col" class="print_head_celda" align="left">Descripción</td>
						<?php
						}
					?>
				</tr>
			</table>
			<table width="80%" align="center">
			<?php
			for($i=0; $i<$this->cant_item_mat; $i++)
				{
				?>
				<tr>
					<td width="5%" class="print_item" align="center"><?php echo $this->item_mat[$i]->GetCantidad()?></td>
					<td width="10%" class="print_item" align="left"><?php echo $this->item_mat[$i]->GetCodigo()?></td>
					<td width="65%" class="print_item" align="left"><?php echo $this->item_mat[$i]->GetDescripcion()?></td>
					<?php
					if($nivel<2)
						{
						?>
						<td width="10%" class="print_item" align="right"><?php echo $this->item_mat[$i]->GetImporte()?></td>
						<td width="10%" class="print_item" align="right"><?php echo sprintf("%.2f", $this->item_mat[$i]->GetImporte() * $this->item_mat[$i]->GetCantidad())?></td>
						<?php
						}
					else
						{
						?>
						<td width="10%"></td>
						<td width="10%"></td>
						<?php
						}
						?>
				</tr>
				<?php
				}
			if($nivel<3)
				{
				?>
				<tr>
					<td class="print_total_celda">&nbsp;</td>
					<td class="print_total_celda">&nbsp;</td>
					<td class="print_total_celda">&nbsp;</td>
					<td class="print_total_celda">SUBTOTAL:</td>
					<td class="print_total_celda">$ <?php echo sprintf("%.2f", $this->subtot_mat)?></td>
				</tr>
				<?php
				}
				?>
			</table>
			<?php
			}
			?>
		<p>&nbsp;</p>
		<?php
		$subtotal = $this->subtot_mob+$this->subtot_mat;
		$desc = $this->encabezado[DESCUENTO];
		?>    
		<table width="80%" align="center">
		<?php
		if($desc!=0)
			{
			?>
			<tr>
				<td width="88%" class="print_total">SUBTOTAL:</td>
				<td width="12%" class="print_total"><?php echo "$ ".sprintf("%.2f", $subtotal) ?></td>
			</tr>
			<tr>
				<td class="print_total">DESCUENTO(<?php echo $desc?>%):</td>
				<td class="print_total"><?php echo "$ ".sprintf("%.2f", $subtotal*($desc/100)) ?></td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td width="88%" class="print_total">TOTAL:</td>
				<td width="12%" class="print_total"><?php echo "$ ".sprintf("%.2f", $subtotal*(1-$desc/100)) ?></td>
			</tr>
		</table>
      <p style="padding-left:10%; padding-right:10%;"><span class="print_head_tabla" id="span_nota"><?php if($this->HayNota()) { echo "NOTA: ".$this->GetNota(); } ?></span></p>
		<?php
		}
      
   // Presenta Recibo de OT en formato de impresion
   function ImprimirRecibo($id_recibo,$unico) {
              // Si viene el Nro de orden de trabajo significa que imprimen todos.
      Debugger("ImprimirRecibo($id_recibo, $unico)");
      $receipt = $this->GetReceiptByID($id_recibo);
      $resDesc = $this->GetConex()->Execute("SELECT * FROM rec_tipo_item");
      $tipoDesc = array();
      
      while(!$resDesc->EOF) {
         $tipoDesc[$resDesc->fields['id_tipo_item']] = $resDesc->fields['descripcion'];
         $resDesc->MoveNext();
      }
    if ($unico) {
        ?>   
            <table width="90%" align="center">
                <tr>
                    <td width="20%" class="head_item_std1">Recibo OT N°:</td>
                    <td width="80" class="head_item_std2"><div id="nro_recibo"><?php echo $receipt->nro_recibo; ?></div></td>
                </tr>
                <tr>
                    <td class="head_item_std1">Fecha:</td>
                    <td class="head_item_std2"><div id="date_recibo"><?php echo $receipt->getDateUp()->format('d-m-Y'); ?></div></td>
                </tr>
            </table>
        <?php
        } // if $unico
?>
<div id="parentItems" style="width: 90%; margin: 10px auto;">
   <div class="head_item_std3" style="float: left; width: 40%; text-align: left; padding: 5px 0;">Item de Pago</div>
   <div class="head_item_std3" style="float: left; width: 20%; text-align: right; padding: 5px 0;">Debe($)</div>
   <div class="head_item_std3" style="float: left; width: 20%; text-align: right; padding: 5px 0;">Haber ($)</div>
   <div class="head_item_std3" style="float: left; width: 20%; text-align: right; padding: 5px 0;">Saldo ($)</div>
   
   <?php
   foreach($receipt->recItem as $payItem) {
      ?>
      <div id="<?php echo "group_pay_".$payItem[REC_ITEM_ID]; ?>" name="group_pay" class="divReciboItemGroup_print">
         <div class="divReciboItemType_print"><?php echo $tipoDesc[$payItem[REC_ITEM_TIPO]]; ?></div>
         <div id="<?php echo "debe_".$payItem[REC_ITEM_ID]; ?>" class="divReciboItemDebe"><?php echo sprintf("%.2f", $payItem[REC_ITEM_DEBE]); ?></div>
         <div class="divReciboItemHaber"><?php echo $payItem[REC_ITEM_HABER]; ?></div>
         <div id="<?php echo "saldo_".$payItem[REC_ITEM_ID]; ?>" class="divReciboItemSaldo"><?php echo sprintf("%.2f", $payItem[REC_ITEM_SALDO]); ?></div>
         <?php
         if($payItem[REC_ITEM_TIPO] == 2 || $payItem[REC_ITEM_TIPO] == 3 || $payItem[REC_ITEM_TIPO] == 5) {
            ?>
            <div id="divInfo_<?php echo $payItem[REC_ITEM_ID]; ?>" style="clear: both;">
               <?php
               switch($payItem[REC_ITEM_TIPO]) {
                  case 2:
                  case 3:
                     ?>
                     <div style="width: 100%; height: 22px;">
                        <div class="divReciboItemExtraHdr">Fecha: </div>
                        <div style="float: left; width: 80%; margin: 2px 0 0 5px;">
                           <?php
                           $date = new DateTime($payItem[REC_ITEM_FECHA]);
                           echo $date->format('Y-m-d');
                           ?>
                        </div>
                     </div>
                     <div style="width: 100%; height: 22px;">
                        <div class="divReciboItemExtraHdr">Banco: </div>
                        <div style="float: left; width: 80%; margin: 2px 0 0 5px;">
                           <?php echo $payItem[REC_ITEM_BANCO]; ?>
                        </div>
                     </div>
                     <div style="width: 100%; height: 22px;">
                        <div class="divReciboItemExtraHdr">Localidad: </div>
                        <div style="display: block; float: left; width: 80%; margin: 2px 0 0 5px;">
                           <?php echo $payItem[REC_ITEM_LOCALIDAD]; ?>
                        </div>
                     </div>
                     <div>
                        <div class="divReciboItemExtraHdr"><?php if($payItem[REC_ITEM_TIPO] == 2) { echo "N° Cheque: "; } else { echo "N° Operación: "; } ?></div>
                        <div style="display: block; float: left; width: 80%; margin: 2px 0 0 5px;">
                           <?php echo $payItem[REC_ITEM_NRO_OP]; ?>
                        </div>
                     </div>
                     <?php
                     break;
                  
                  case 5:
                     ?>
                     <div style="width: 100%; height: 22px;">
                        <?php echo $payItem[REC_ITEM_OTRO]; ?>
                     </div>
                     <?php
                     break;
               }
               ?>
            </div>
         <?php
         }
         ?>
      </div>
   <?php
   }
   ?>
   </div>
   <div class="gris_claro" style="width: 90%; margin: 10px auto; border-top: 1px #0CF double; overflow: auto; font-weight: bold; padding-top: 10px;">
      <div style="float: left; width: 40%; text-align: right;">Resumen Total ($):</div>
      <div id="totalDebe" style="float: left; width: 20%; text-align: right;"><?php echo sprintf("%.2f", $receipt->debe); ?></div>
      <div id="totalHaber" style="float: left; width: 20%; text-align: right;"><?php echo sprintf("%.2f", $receipt->haber); ?></div>
      <div id="totalSaldo" style="float: left; width: 20%; text-align: right;"><?php echo sprintf("%.2f", $receipt->debe - $receipt->haber); ?></div>
      <div style="display: block; float: left; width: 5%; text-align: right;"></div>
   </div>

   <div id="textoHaber" style="width: 90%; margin: 10px auto; border-top: 1px #0CF double; font-weight: bold; padding-top: 25px;"></div>

   <div style="width: 90%; margin: 10px auto; clear: both;">
      <div><?php if(strlen($receipt->nota)) { echo "NOTA: ".$receipt->nota; } ?></div>
   </div>

   <script language="javascript">
      var texto = numeroALetras(parseFloat(<?php echo $receipt->haber; ?>).toFixed(2), {
         plural: 'pesos',
         singular: 'peso',
         centPlural: 'centavos',
         centSingular: 'centavo'
      });

      document.getElementById("textoHaber").innerHTML = "SON: "+texto;
   </script>
   <?php
   } // ImprimirRecibo()

   function ImprimirAllRecibos($ot_nro){
       Debugger("grsys.php - ImprimirAllRecibos($ot_nro)");
        ?>   
        <table width="90%" align="center">
            <tr>
                <td width="20%" class="head_item_std1">Orden de Trabajo N°:</td>
                <td width="80" class="head_item_std2"><div id="nro_recibo"><?php echo $ot_nro ?></div></td>
            </tr>
        </table>
        <?php

        $allRecibos = $this->GetConex()->Execute("SELECT rece.id_recibo FROM rece WHERE rece.nro_ot = $ot_nro");
        while(!$allRecibos->EOF) {
            $id_recibo = $allRecibos->fields['id_recibo'];
            $this->ImprimirRecibo($id_recibo,false);
            $allRecibos->MoveNext();
        }       
    }

   // Localiza y retorna recibo con el $id indicado
   function GetReceiptByID($id) {
   
      foreach($this->recibos as $recibo) {
         if($recibo->id_recibo == $id) {
            return $recibo;
         }
      }
      return NULL;
   }
   
   //***************************************** ENCABEZADO ******************************************
	// Prepara el encabezado pedido y rellena el array $encabezado de la clase
	// Tipos:	0:PresSTD; 1:PresCLI; 2:OT
	function ArmarEncabezado($tipo, $nro)
		{
            Debugger("grsys.php - user_gr->ArmarEncabezado(tipo=$tipo, nro=$nro");
		$this->con_sql = $this->GetConex();
		switch($tipo)
			{
			case STD:
                Debugger("Asigno this->campos_enc=ENC_STD=".ENC_STD);
				$this->campos_enc=ENC_STD;
				$query = "SELECT pse.nro_pres, pse.desc_pres, pse.nro_motor, pse.lista_mat, pse.descuento, mote.desc_motor FROM `pse` INNER JOIN mote";
				$query = $query." WHERE pse.nro_pres=$nro AND pse.nro_motor=mote.nro_motor";
				$res = $this->con_sql->Execute($query);
				
				$this->encabezado[NUM_MOTOR] = $res->fields['nro_motor'];
				$this->encabezado[DESC_PRES] = $res->fields['desc_pres'];
				$this->encabezado[LISTA_MAT] = $res->fields['lista_mat'];
				$this->tipo=STD;
				$this->nro=$res->fields['nro_pres'];
				break;
			case CLI:
			case OT:
				$query = "SELECT * FROM";
				if($tipo==1)
					{
                        Debugger("Asigno this->campos_enc=ENC_CLI=".ENC_CLI);
					$this->campos_enc=ENC_CLI;
					$query = $query." pce WHERE nro_pres=$nro";
					$res = $this->con_sql->Execute($query);
					$this->tipo=CLI;
					$this->nro=$res->fields['nro_pres'];
					}
				else
					{
                        Debugger("Asigno this->campos_enc=ENC_OT=".ENC_OT);
					$this->campos_enc=ENC_OT;
					$query = $query." ote WHERE nro_ot=$nro";
					$res = $this->con_sql->Execute($query);
					$this->tipo=OT;
					$this->nro=$res->fields['nro_ot'];
					$this->prioridad=$res->fields['prioridad'];
					}
				$this->encabezado[NUM_OT] = $res->fields['nro_ot'];
				$this->encabezado[NUM_STD] = $res->fields['nro_std'];
				$this->encabezado[NUM_MOTOR] = $this->GetNroMotor($this->encabezado[NUM_STD]);
				$this->encabezado[DESC_PRES] = $res->fields['desc_motor'];
				$this->encabezado[CLIENTE] = $res->fields['nombre'];
				$this->encabezado[DIRECCION] = $res->fields['direccion'];
				$this->encabezado[TELEFONO] = $res->fields['telefono'];
				$this->encabezado[LOCALIDAD] = $res->fields['localidad'];
				$this->encabezado[FECHA] = $res->fields['fecha'];
				$this->encabezado[HORA] = $res->fields['hora'];
				$this->nota = $res->fields['nota'];
				break;
			}
		$this->encabezado[NUM_PRES] = $res->fields['nro_pres'];
		$this->encabezado[DESC_MOTOR] = $res->fields['desc_motor'];
		$this->encabezado[DESCUENTO] = $res->fields['descuento'];
		$this->encabezado[LISTA_MOB] = $this->GetNroListaMOB($this->encabezado[NUM_MOTOR]);
		}

	// Muestra un encabezado en base a los campos pasados y si es para editar
	function MostrarEncabezado($edit)
		{
           Debugger("grsys.php - user_gr->MostrarEncabezado(edit=$edit)");
		?>
		<div>&nbsp;</div>
		<table width="80%" align="center">
			<tr>
				<td colspan="2" class="head_std">
					<?php
				    //if($this->campos_enc & 1<<NUM_OT)
                    if($this->campos_enc == ENC_OT)
						echo "ENCABEZADO DE LA ORDEN DE TRABAJO";
					else
						echo "ENCABEZADO DEL PRESUPUESTO";
					?>
				</td>
			</tr>
			<?php
            Debugger("this->campos_enc = ".$this->campos_enc.", NUM_OT =".NUM_OT);
			//if($this->campos_enc & 1<<NUM_OT)
            //if($this->campos_enc == ENC_OT)
            if (isset($this->encabezado[NUM_OT]))
				{
                    Debugger("Armar encabezado de Orden de trabajo");
				?>
				<tr>
					<td width="30%" class="head_item_std1">Nº OT:</td>
					<td width="70%" class="head_item_std2"><?php echo $this->encabezado[NUM_OT]?></td>
				</tr>
				<?php
				}
            else
                Debugger("No armar encabezado de Orden de Trabajo");
			//if($this->campos_enc & 1<<NUM_PRES)
            if(isset($this->encabezado[NUM_PRES]))
				{
                    Debugger("Armar encabezado de Presupuesto");
				?>
				<tr>
					<td width="30%" class="head_item_std1">Nº Presupuesto:</td>
					<td width="70%" class="head_item_std2"><?php echo $this->encabezado[NUM_PRES]?></td>
				</tr>
				<?php
				}
            else
                Debugger("No armar encabezado de Pruesupesto");
			//if($this->campos_enc & 1<<DESC_PRES)
            if(isset($this->encabezado[DESC_PRES]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Descripción Presupuesto:</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->encabezado[DESC_PRES];}
								else	{?> <input type="text" name="desc_pres" id="desc_pres" size="40" maxlength="50" value="<?php echo $this->encabezado[DESC_PRES]?>" onchange="EditarItemEncabezado(<?php echo DESC_PRES?>, <?php echo "'desc_pres'"?>)"/><?php }?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<DESC_MOTOR)
            if (isset($this->encabezado[DESC_MOTOR]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Descripcion Motor:</td>
					<td width="70%" class="head_item_std2"><?php echo $this->encabezado[DESC_MOTOR]?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<CLIENTE)
            if (isset($this->encabezado[CLIENTE]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Cliente:</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->encabezado[CLIENTE];}
								else{?> <input type="text" name="cliente" id="cliente" size="40" maxlength="50" value="<?php echo $this->encabezado[CLIENTE]?>"  onchange="EditarItemEncabezado(<?php echo CLIENTE?>, <?php echo "'cliente'"?>)"/><?php }?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<DIRECCION)
            if (isset($this->encabezado[DIRECCION]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Dirección:</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->encabezado[DIRECCION];}
								else{?> <input type="text" name="direccion" id="direccion" size="40" maxlength="50" value="<?php echo $this->encabezado[DIRECCION]?>"  onchange="EditarItemEncabezado(<?php echo DIRECCION?>, <?php echo "'direccion'"?>)"/><?php }?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<TELEFONO)
            if (isset($this->encabezado[TELEFONO]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Teléfono:</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->encabezado[TELEFONO];}
								else{?> <input type="text" name="telefono" id="telefono" size="40" maxlength="50" value="<?php echo $this->encabezado[TELEFONO]?>"  onchange="EditarItemEncabezado(<?php echo TELEFONO?>, <?php echo "'telefono'"?>)"/><?php }?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<LOCALIDAD)
            if (isset($this->encabezado[LOCALIDAD]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Localidad:</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->encabezado[LOCALIDAD];}
								else{?> <input type="text" name="localidad" id="localidad" size="40" maxlength="50" value="<?php echo $this->encabezado[LOCALIDAD]?>"  onchange="EditarItemEncabezado(<?php echo LOCALIDAD?>, <?php echo "'localidad'"?>)"/><?php }?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<FECHA)
            if (isset($this->encabezado[FECHA]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Fecha:</td>
					<td width="70%" class="head_item_std2"><?php echo $this->encabezado[FECHA]?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<HORA)
            if (isset($this->encabezado[HORA]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Hora:</td>
					<td width="70%" class="head_item_std2"><?php echo $this->encabezado[HORA]?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<LISTA_MAT)
            if (isset($this->encabezado[LISTA_MOB]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Lista de Precios:</td>
					<td width="70%" class="head_item_std2"><?php echo $this->encabezado[LISTA_MOB]?></td>
				</tr>
				<?php
				}
			//if($this->campos_enc & 1<<DESCUENTO)
            if (isset($this->encabezado[DESCUENTO]))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Descuento(%):</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->encabezado[DESCUENTO];}
								else{?> <input type="text" name="descuento" id="descuento" size="40" maxlength="50" value="<?php echo $this->encabezado[DESCUENTO]?>"  onchange="EditarItemEncabezado(<?php echo DESCUENTO?>, <?php echo "'descuento'"?>)"/><?php }?></td>
				</tr>
				<?php
				}            
			//if($this->campos_enc & 1<<PRIORIDAD)
            if (isset($this->prioridad))
				{
				?>
				<tr>
					<td width="30%" class="head_item_std1">Prioridad:</td>
					<td width="70%" class="head_item_std2">
						<?php if(!$edit) {echo $this->prioridad;}
								else{?> <input type="text" name="prioridad" id="prioridad" size="6" maxlength="1" value="<?php echo $this->prioridad?>"  onchange="EditarItemEncabezado(<?php echo PRIORIDAD?>, <?php echo "'prioridad'"?>)"/><?php }?></td>
				</tr>
				<?php
				}
		?>
		</table>
		<?php
		}
	
	//************************************ MANO DE OBRA ****************************************
	// Prepara la mano de obra requerida para el presupuesot u orden de trabajo
	function ArmarMOB($tipo, $nro, $edit)
		{
            Debugger("grsys.php - user_gr->ArmarMOB(tipo=$tipo,nro=$nro,edit=$edit)");
		$this->con_sql = $this->GetConex();
		$this->add_mob=0;
		if($nro)
			{
			switch($tipo)
				{
				case STD:
					// Busco el numero de lista de precios del presupuesto
					$lista = $this->encabezado[LISTA_MOB];
					$query = "SELECT mobe.numero, psd.cantidad, mobe.descripcion, mobd.importe";
					$query = $query." FROM psd INNER JOIN mobe INNER JOIN mobd";
					$query = $query." WHERE mobe.numero=psd.numero AND mobd.numero=psd.numero AND psd.nro_pres=$nro AND mobd.columna=$lista AND psd.cantidad<>0 AND psd.item=0";
					$query = $query." ORDER BY mobe.numero ASC";
					break;
				case CLI:
				case OT:
					$query = "SELECT * FROM";
					if($tipo==CLI)	$query = $query." pcd WHERE nro_pres=$nro";
					else				$query = $query." otd WHERE nro_ot=$nro";
					$query = $query." AND item=0 ORDER BY agregado, numero ASC";
					break;
				}
            Debugger($query);
			$res = $this->con_sql->Execute($query);
			}
			
		if(!$edit)
			{
			$i=0;
			while(!$res->EOF)
				{                    
				if($tipo==STD)	
                    $agregado=0;
				else				
                    $agregado=$res->fields['agregado'];
				$this->item_mob[($i++)] = new ITEM(0, $res->fields['numero'], $res->fields['cantidad'], 0, htmlentities($res->fields['descripcion']), $res->fields['importe'], $agregado, isset($res->fields['fecha_real'])? $res->fields['fecha_real']:'');
				$res->MoveNext();
				}
			$this->cant_item_mob=$i;
			}
		else if($edit)
			{
			// Busco todos los item posibles para este motor
			$motor = $this->encabezado[NUM_MOTOR];
			$query = "SELECT mobe.numero, mobe.descripcion";
			$query = $query." FROM mobe INNER JOIN motd";
			$query = $query." WHERE mobe.numero=motd.numero AND motd.nro_motor=$motor";
			if($tipo=CLI && $this->filtros)
				{
				$secciones = MOB_BuscarSecciones($this->con_sql);
				$i=1;
				$cant=0;
				while(!$secciones->EOF)
					{
					$seccion = $secciones->fields['nro'];
					if($this->filtros&(1<<$i))
						{
						if(!$cant)	$query = $query." AND (mobe.seccion=$seccion";
						else			$query = $query." OR mobe.seccion=$seccion";
						$cant++;
						}
					$i++;
					$secciones->MoveNext();
					}
				if($cant)	$query = $query.") ORDER BY mobe.numero ASC";
				}
			else	$query = $query." ORDER BY mobe.numero ASC";
			$res1 = $this->con_sql->Execute($query);
			
			// Si es para dar de alta un presupuesto a cliente busco las cantidades del STD
			// ya que se pueden pedir a la hora de crear el presupuesto
			if($this->items_std)
				{
				$pres = $this->encabezado[NUM_STD];
				$query = "SELECT cantidad, numero FROM psd";
				$query = $query." WHERE item=0 AND nro_pres=$pres ORDER BY numero ASC";
				$res2 = $this->con_sql->Execute($query);
				}
		
			// Busco los importes de la mano de obra completa
			$lista = $this->encabezado[LISTA_MOB];
			$query = "SELECT mobd.importe FROM mobd INNER JOIN mobe INNER JOIN motd";
			$query = $query." WHERE mobe.numero=motd.numero AND mobd.numero = mobe.numero AND mobd.columna=$lista AND motd.nro_motor=$motor ORDER BY mobe.numero";
			$res3 = $this->con_sql->Execute($query);

			// Para un PRECLI u OT puede que tengan mas items que el motor en cuestion dado que
			// pueden exisitr items agregados que son propios, por eso analizo quien tiene mas
			// y uso esa cantidad en el bucle principal de creacion de MOB
			// Los chequeos de !EOF los hago para evitar un error en NumRows() ante una consulta vacia
			if($nro)
				{
				if(!$res1->EOF && $res->EOF)			$max_reg = $res1->NumRows();				// Si esta vacio el PRES/OT, asigno la cantidad del motor
				else if($res1->EOF && !$res->EOF)	$max_reg = $res->NumRows();				// Si esta vacio motor, asigno la cantidad del PRES/OT
				else																								// Sino, analizo cual de los dos es el mayor en registros
					{
					$res1->NumRows() > $res->NumRows() ? $max_reg = $res1->NumRows() : $max_reg = $res->NumRows();
					}
				}
			else	$max_reg = $res1->NumRows();
				
			$i=0;
			while($max_reg--)
				{
				$agregado=0;
				$new_add=0;
				$numero = $res1->fields['numero'];
				if($nro)	{	$num_pres=$res->fields['numero']; if(isset($res->fields['agregado'])) $agregado=$res->fields['agregado'];	}
				else		$num_pres='a';
				$descripcion = $res1->fields['descripcion'];
				$importe = $res3->fields['importe'];

				// Si se pidieron los items std en un nuevo presupuesto a cliente, asigno esa cantidad y los demas campos anteriores
				if($this->items_std && $res1->fields['numero'] == $res2->fields['numero'])
					{
					$cantidad = $res2->fields['cantidad'];
					$res2->MoveNext();
					}
				// Si el pres existe y tiene ese item MOB, asigno las que tiene el pres
				else if($nro && ($num_pres == $numero || $agregado))
					{
					if(isset($res->fields['agregado']) && $res->fields['agregado'])	$agregado=1;
					$cantidad = $res->fields['cantidad'];
					$numero = $res->fields['numero'];
					$descripcion = $res->fields['descripcion'];
					$importe = $res->fields['importe'];
					$res->MoveNext();
					if(isset($res->fields['agregado']))	$new_add=$res->fields['agregado'];
					}
				else	$cantidad=0;		// Sino quedan las del std y la cantidad en cero

				$this->item_mob[($i++)] = new ITEM(0, $numero, $cantidad, 0, htmlentities($descripcion), $importe, $agregado,'');

				if(!$agregado)	{	$res1->MoveNext();	$res3->MoveNext();	}
				if(($agregado || $new_add) && !$res->EOF)	$max_reg++;
				}
			$this->cant_item_mob=$i;
			}
		}

	// Muestra los datos de mano de obra almacenados en la clase
	function MostrarMOB($tipo, $edit)
		{
            Debugger("grsys.php - user_gr->MostarMOB(tipo=$tipo, edit=$edit");
		$this->subtot_mob=0;
            Debugger("cant_item_mob = ".$this->cant_item_mob);
		if($this->cant_item_mob)
			{
			?>
			<table width="80%" align="center">
				<tr>
					<td colspan="5" class="head_std">MANO DE OBRA</td>
				</tr>
				<tr>
					<td width="10%" class="head_item_std3">Cantidad</td>
					<?php
					if($edit==EDIT && $tipo!=STD)
						{
						?>
						<td width="6%" class="head_item_std3">Editar</td>
						<td width="60%" class="head_item_std3">Descripción</td>
						<td width="12%" class="head_item_std3">Precio ($)</td>
						<td width="12%" class="head_item_std3">Total ($)</td>
						<?php
						}
					else if($edit==2)
						{
						?>
						<td width="90%" class="head_item_std3">Descripción</td>
						<?php
						}
					else if($edit==3)
						{
						?>
						<td width="75%" class="head_item_std3">Descripción</td>
						<td width="15%" class="head_item_std3">Estado</td>
						<?php
						}
					else
						{
						?>
						<td class="head_item_std3">Descripción</td>
						<td class="head_item_std3">Precio ($)</td>
						<td class="head_item_std3">Total ($)</td>
                        <td class="head_item_std3">Realizado</td>
						<?php
						}
						?>
				</tr>
			<?php
			for($i=0; $i<$this->cant_item_mob; $i++)
				{
				$this->subtot_mob+= ($this->item_mob[$i]->GetCantidad() * $this->item_mob[$i]->GetImporte());
				if($i%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }?>
				<?php
                Debugger("Ejecuto Mostrar - 1");
				$this->item_mob[$i]->Mostrar($tipo, $edit);
				?>
				</tr>
				<?php
				}
			if($edit<2)
				{
				?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<?php if($edit && $tipo!=STD) {?><td>&nbsp;</td><?php }?>
					<td class="subtotal">Subtotal:</td>
					<td class="subtotal"><span id="sub_mob"><?php echo "$ ".sprintf("%.2f", $this->subtot_mob)?></span></td>
                    <td>&nbsp;</td>
				</tr>
				<?php
				}
			else if($edit==3)
				{
				?>
				<tr>
					<td colspan="3" align="center" class="celda_progreso">
						<?php
						 $conex= $this->GetConex();
						$res = $conex->Execute("SELECT COUNT(*) AS realizados FROM otd WHERE nro_ot=$this->nro AND item=0 AND real_by<>0");
						$realizados = $res->fields['realizados'];
						$res = $conex->Execute("SELECT COUNT(*) AS total FROM otd WHERE nro_ot=$this->nro AND item=0");
						$total = $res->fields['total'];
						echo "Se Realizaron ".$realizados."/".$total." Tareas (".$realizados/$total*100.."%)";
						?>
					</td>
				</tr>
				<?php
				}
				?>
			</table>
			<div>&nbsp;</div>
			<?php
			}
		}
		
	//************************************ MATERIALES ******************************************
	// Prepara los materiales requeridos para el presupuesto u orden de trabajo
	function ArmarMAT($tipo, $nro, $edit)
		{
            Debugger("grsys.php - user_gr->ArmarMAT(tipo=$tipo,nro=$nro,edit=$edit)");
		$this->con_sql = $this->GetConex();
		$this->add_mat=0;
		if($nro)
			{
			switch($tipo)
				{
				case STD:
					// Busco el numero de lista de precios del presupuesto
					$motor = $this->encabezado[NUM_MOTOR];
					$query = "SELECT psd.cantidad, mat.codigo, CONCAT_WS(' ', rub.desc_rubro, mat.desc_mat) AS descripcion, mat.precio1 AS importe, psd.item, psd.numero";
					$query = $query." FROM psd INNER JOIN mat INNER JOIN rub";
					$query = $query." WHERE psd.item = mat.item AND psd.numero = mat.numero AND rub.nro_rubro = psd.item";
					$query = $query." AND mat.nro_motor = $motor AND psd.nro_pres = $nro";
					$query = $query." ORDER BY psd.item, psd.numero ASC";
					break;
				case CLI:
				case OT:
					$query = "SELECT * FROM";
					if($tipo==CLI)	$query = $query." pcd WHERE nro_pres=$nro";
					else				$query = $query." otd WHERE nro_ot=$nro";
					$query = $query." AND item!=0 ORDER BY agregado, item, numero ASC";
					break;
				}
            Debugger($query);
			$res = $this->con_sql->Execute($query);
			}
			
		if(!$edit)
			{
			$i=0;
			while(!$res->EOF)
				{
				if($tipo==STD)	$agregado=0;
				else				$agregado=$res->fields['agregado'];
				$this->item_mat[($i++)] = new ITEM($res->fields['item'], $res->fields['numero'], $res->fields['cantidad'], $res->fields['codigo'], htmlentities($res->fields['descripcion']), $res->fields['importe'], $agregado,'');
				$res->MoveNext();
				}
			$this->cant_item_mat=$i;
			}
		else if($edit)
			{
			// Busco todos los item posibles para este motor
			$motor = $this->encabezado[NUM_MOTOR];
			$query = "SELECT mat.item, mat.numero, CONCAT_WS(' ', rub.desc_rubro, mat.desc_mat) AS descripcion, mat.codigo, mat.precio1 AS importe";
			$query = $query." FROM mat INNER JOIN rub";
			$query = $query." WHERE rub.nro_rubro=mat.item AND mat.nro_motor=$motor";
			if($tipo=CLI && $this->filtros)
				{
				$secciones = MOB_BuscarSecciones($this->con_sql);
				$i=1;
				$cant=0;
				while(!$secciones->EOF)
					{
					$seccion = $secciones->fields['nro'];
					if($this->filtros&(1<<$i))
						{
						if(!$cant)	$query = $query." AND (rub.seccion=$seccion";
						else			$query = $query." OR rub.seccion=$seccion";
						$cant++;
						}
					$i++;
					$secciones->MoveNext();
					}
				if($cant)	$query = $query.") ORDER BY mat.item, mat.numero ASC";
				}
			else		$query = $query." ORDER BY mat.item, mat.numero ASC";
			$res1 = $this->con_sql->Execute($query);

			// Si es para dar de alta un presupuesto a cliente busco los datos del STD
			// ya que se pueden pedir a la hora de crear el presupuesto
			if($this->items_std)		// Si se pidieron los items std en un nuevo presupuesto a cliente, asigno esa cantidad y los demas campos anteriores
				{
				$nro_std = $this->encabezado[NUM_STD];
				$query = "SELECT psd.item, psd.numero, psd.cantidad";
				$query = $query." FROM psd INNER JOIN mat INNER JOIN rub";
				$query = $query." WHERE psd.item = mat.item AND psd.numero = mat.numero AND rub.nro_rubro = psd.item";
				$query = $query." AND mat.nro_motor = $motor AND psd.nro_pres = $nro_std";
				$query = $query." ORDER BY psd.item, psd.numero ASC";
				$res2 = $this->con_sql->Execute($query);
				}
				
			// Para un PRECLI u OT puede que tengan mas items que el motor en cuestion dado que
			// pueden exisitr items agregados que son propios, por eso analizo quien tiene mas
			// y uso esa cantidad en el bucle principal de creacion de MAT
			// Los chequeos de !EOF los hago para evitar un error en NumRows() ante una consulta vacia
			if($nro)
				{
				if(!$res1->EOF && $res->EOF)			$max_reg = $res1->NumRows();				// Si esta vacio el PRES/OT, asigno la cantidad del motor
				else if($res1->EOF && !$res->EOF)	$max_reg = $res->NumRows();				// Si esta vacio motor, asigno la cantidad del PRES/OT
				else																								// Sino, analizo cual de los dos es el mayor en registros
					{
					$res1->NumRows() > $res->NumRows() ? $max_reg = $res1->NumRows() : $max_reg = $res->NumRows();
					}
				}
			else	$max_reg = $res1->NumRows();
				
			$i=0;
			while($max_reg--)
				{
				$agregado=0;
				$new_add=0;
				if($nro)	{	$item = $res->fields['item'];	$num = $res->fields['numero']; if(isset($res->fields['agregado'])) $agregado=$res->fields['agregado'];	}
				else		{	$item = 'a';	$num = 'a';	}
				$item_mat = $res1->fields['item'];
				$num_mat = $res1->fields['numero'];
				$it = $item_mat;
				$numero = $num_mat;
				$descripcion = $res1->fields['descripcion'];
				$codigo = $res1->fields['codigo'];
				$importe = $res1->fields['importe'];

				// Si se pidieron los items std en un nuevo presupuesto a cliente, asigno esa cantidad y los demas campos anteriores
				if($this->items_std && $item_mat == $res2->fields['item'] && $num_mat == $res2->fields['numero'])
					{
					$cantidad = $res2->fields['cantidad'];
					$res2->MoveNext();
					}
				// Si el pres existe y tiene ese item MAT, asigno las que tiene el pres
				else if($nro && (($item==$item_mat && $num==$num_mat) || $agregado))
					{
					if(isset($res->fields['agregado']) && $res->fields['agregado'])	$agregado=1;
					$it = $item;
					$numero = $num;
					$cantidad = $res->fields['cantidad'];
					$codigo = $res->fields['codigo'];
					$descripcion = $res->fields['descripcion'];
					$importe = $res->fields['importe'];
					$res->MoveNext();
					if(isset($res->fields['agregado']))	$new_add=$res->fields['agregado'];
					}
				else 		// Sino quedan las del motor y la cantidad en cero
					$cantidad=0;

				$this->item_mat[($i++)] = new ITEM($it, $numero, $cantidad, $codigo, htmlentities($descripcion), $importe, $agregado,'');
				if(!$agregado)		$res1->MoveNext();
				if(($agregado || $new_add) && !$res->EOF)	$max_reg++;
				}
			$this->cant_item_mat=$i;
			}
		}
	
	// Muestra los datos de materiales almacenados en la clase
	function MostrarMAT($tipo, $edit)
		{
            Debugger("grsys.php - user_gr->MostrarMAT(tipo=$tipo, edit=$edit");
		$this->subtot_mat=0;
		if($this->cant_item_mat)
			{
			?>
			<table width="80%" align="center">
				<tr>
					<td colspan="<?php if($edit==EDIT && $tipo!=STD) echo 6; else echo 5;?>" class="head_std">MATERIALES</td>
				</tr>
				<tr>
					<?php
					if($edit==EDIT && $tipo!=STD)
						{
						?>
						<td width="9%" class="head_item_std3">Cantidad</td>
						<td width="6%" class="head_item_std3">Editar</td>
						<td width="15%" class="head_item_std3">Código</td>
						<td width="50%" class="head_item_std3">Descripción</td>
						<td width="10%" class="head_item_std3">Precio ($)</td>
						<td width="10%" class="head_item_std3">Total ($)</td>						
						<?php
						}
					else if($edit==2)
						{
						?>
						<td width="10%" class="head_item_std3">Cantidad</td>
						<td width="16%" class="head_item_std3">Código</td>
						<td width="74%" class="head_item_std3">Descripción</td>
						<?php
						}
					else if($edit==3)
						{
						?>
						<td width="10%" class="head_item_std3">Cantidad</td>
						<td width="16%" class="head_item_std3">Código</td>
						<td width="50%" class="head_item_std3">Descripción</td>
						<td width="12%" class="head_item_std3">Estado</td>
						<td width="12%" class="head_item_std3">Fecha</td>
						<?php
						}
					else
						{
						?>
						<td width="10%" class="head_item_std3">Cantidad</td>
						<td width="16%" class="head_item_std3">Código</td>
						<td width="50%" class="head_item_std3">Descripción</td>
						<td width="12%" class="head_item_std3">Precio ($)</td>
						<td width="12%" class="head_item_std3">Total ($)</td>
						<?php
						}
						?>
				</tr>
			<?php
			for($i=0; $i<$this->cant_item_mat; $i++)
				{
				$this->subtot_mat+= ($this->item_mat[$i]->GetCantidad() * $this->item_mat[$i]->GetImporte());
				if($i%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } else {?><tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }?>
				<?php
				$this->item_mat[$i]->Mostrar($tipo, $edit);
				?>
				</tr>
				<?php
				}
			if($edit<2)
				{
				?>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<?php if($edit && $tipo!=STD) {?><td>&nbsp;</td><?php }?>
					<td class="subtotal">Subtotal:</td>
					<td class="subtotal"><span id="sub_mat"><?php echo "$ ".sprintf("%.2f", $this->subtot_mat)?></span></td>
				</tr>
				<?php
				}
			else if($edit==3)
				{
				?>
				<tr>
					<td colspan="5" align="center" class="celda_progreso">
						<?php
						$conex = $this->GetConex();
						$res = $conex->Execute("SELECT COUNT(*) AS realizados FROM otd WHERE nro_ot=$this->nro AND item<>0 AND asig_to=1");
						$realizados = $res->fields['realizados'];
						$res = $conex->Execute("SELECT COUNT(*) AS total FROM otd WHERE nro_ot=$this->nro AND item<>0");
						$total = $res->fields['total'];
						echo "Hay ".$realizados."/".$total." Materiales en Existencia (".$realizados/$total*100.."%)";
						?>
					</td>
				</tr>
				<?php
				}
				?>
			</table>
			<div>&nbsp;</div>
			<?php
			}
		}

   //************************************ RECIBOS ******************************************
	// Prepara los recibos de una orden de trabajo
	function ArmarRECIBOS($nro) {        
		$this->con_sql = $this->GetConex();
        $lastRecibo = NULL;
        Debugger("ArmarRECIBOS($nro)");
		if($nro) {
         // Limpio listado de Recibos
         unset($this->recibos);
         $this->cant_recibo = 0;
         
         // Busco encabezados de recibos
         $query = "SELECT RH.*, SUM(RD.haber) AS haber FROM rece RH"
                 . " INNER JOIN recd RD on RD.id_recibo=RH.id_recibo"
                 . " WHERE nro_ot=$nro"
                 . " GROUP BY RH.id_recibo";
         
		 $resRecibos = $this->con_sql->Execute($query);
         
         while(!$resRecibos->EOF) {
            
            // Al primer recibo le asigno el total de la OT como monto 'Debe'
            if($this->cant_recibo == 0) {
               $sub_mob = $this->subtot_mob;
               $sub_mat = $this->subtot_mat;
               $descuento = $this->encabezado[DESCUENTO];
               $subtotal = $sub_mob+$sub_mat;
               $total = sprintf("%.2f", $subtotal*(1-$descuento/100));
               $resRecibos->fields['debe'] = $total;
            }
            else {
               //Debugger("Este es el error que marca VSC");
               $resRecibos->fields['debe'] = $lastRecibo->debe - $lastRecibo->haber;
            }
      
            // Busco y Cargo los items de pago de cada recibo
            $query = "SELECT RD.*, RTI.descripcion AS recTipo FROM recd RD";
            $query = $query." INNER JOIN rec_tipo_item RTI ON RTI.id_tipo_item=RD.id_tipo_item";
            $query = $query." WHERE RD.id_recibo=".$resRecibos->fields['id_recibo'];
            $query = $query." ORDER BY RD.id_item ASC";
            Debugger($query);
            $res1 = $this->con_sql->Execute($query);
            // Creo el objeto RECIBO y le cargo la info
            $this->recibos[$this->cant_recibo] = RECIBO::Receipt($resRecibos->fields, $res1);
            $lastRecibo = $this->recibos[$this->cant_recibo];
            $this->cant_recibo++;
            
            $resRecibos->moveNext();
         }
      }
   }
	
	// Muestra los datos de materiales almacenados en la clase
	function MostrarRECIBOS($nro_ot)  {
        Debugger("grsys.php - MostrarRECIBOS($nro_ot)");
      ?>
      <div align="center">
        <input type="button" onclick="OT_ImprimirTodosLosRecibos(<?php echo $nro_ot ?>);" value="Imprimir Todos los Recibos">
      </div>
      <table width="80%" align="center">
         <tr>
            <td colspan="7" class="head_std">RESUMEN DE RECIBOS</td>
         </tr>
         <tr>
            <td width="20%" class="head_item_std3">Fecha</td>
            <td width="20%" class="head_item_std3">Número</td>
            <td width="20%" class="head_item_std3">Importe ($)</td>
            <td width="20%" class="head_item_std3">Saldo ($)</td>
            <td width="20%" class="head_item_std3" colspan="3">Acciones</td>
         </tr>
      <?php
      if($this->cant_recibo) {
         for($i=0; $i<$this->cant_recibo; $i++) {
            if($i%2)	{?>	<tr class="gris_claro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_claro';">	<?php } 
            else     {?>   <tr class="gris_oscuro" onMouseOver="this.className='cell_over';" onMouseOut="this.className='gris_oscuro';">	<?php }

            $this->recibos[$i]->MostrarResumen(($i+1 == $this->cant_recibo ? true : false));
            ?>
            </tr>
         <?php
         }
      }
      else {
         ?>
            <tr>
               <td colspan="7" class="gris_claro" align="center" style="height: 30px; vertical-align: middle;">
                  <div class="message">
                     No hay recibos para la OT, presione el botón <input type="image" title="Agregar Recibo" name="btnReciboAdd" id="btnReciboAdd" src="../Imagenes/add.png" width="15" height="15" /> para agregar uno
                  </div>
               </td>
            </tr>
      <?php
      }
      ?>
      <tr>
         <td colspan="7" class="gris_claro">
            <a href="#" onclick="ReciboAdd(<?php echo $nro_ot; ?>); return false;"><input type="image" title="Agregar Recibo" name="btnReciboAdd" id="btnReciboAdd" src="../Imagenes/add.png" style="padding: 3px" width="22" height="22" /></a>
         </td>
      </tr>
      </table>
      <div>&nbsp;</div>
   <?php
   }
      
	//*************************************** TOTAL ********************************************
	// Muestra el cuadro de totales y descuento si hubiese
	function MostrarTotal()
		{
		$descuento = $this->encabezado[DESCUENTO];
		$subtotal = $this->subtot_mob+$this->subtot_mat;
		?>
		<div>&nbsp;</div>
		<table width="80%" align="center">
			<?php
			if($descuento != 0)
				{
				?>
				<tr>
					<td width="25%">&nbsp;</td>
					<td width="42%">&nbsp;</td>
					<td width="21%" class="total">Subtotal:</td>
					<td width="12%" class="total"><span id="subtotal"><?php echo "$ ".sprintf("%.2f", $subtotal)?></span></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td class="total">Descuento (<?php echo sprintf("%.2f", $descuento)?> %):</td>
					<td class="total"><span id="sub_desc"><?php echo "$ ".sprintf("%.2f", $subtotal*$descuento/100) ?></span></td>
				</tr>
				<?php
				}
				?>
			<tr>
				<td width="25%">&nbsp;</td>
				<td width="42%">&nbsp;</td>
				<td width="21%" class="total">Total:</td>
				<td width="12%" class="total"><span id="total"><?php echo "$ ".sprintf("%.2f", $subtotal*(1-$descuento/100)) ?></span></td>
			</tr>
		</table>
		<?php
		}

	// Muestra el cuadro de totales y descuento si hubiese
	function ActualizarTotal()
		{
		$sub_mob = $this->subtot_mob;
		$sub_mat = $this->subtot_mat;
		$descuento = $this->encabezado[DESCUENTO];
		$subtotal = $this->GetSubTotal();
		$sub_desc = sprintf("%.2f", $subtotal*$descuento/100);
		$total = sprintf("%.2f", $subtotal*(1-$descuento/100));
		?>
		<script language="javascript">
			ActualizarTotales(<?php echo $sub_mob?>, <?php echo $sub_mat?>, <?php echo $subtotal?>, <?php echo $sub_desc?>, <?php echo $total?>);
		</script>
		<?php
		}

	//***********************************************************************************************
	//*************************************** PRESUPUESTO STD ***************************************
	//***********************************************************************************************

	// Prepara un  nuevo presupuesto STD y lanza la edicion
	function NuevoPresupuestoSTD($motor)
		{
		$conex = $this->GetConex();
		$this->LimpiarClase();
		$this->encabezado[NUM_MOTOR]=$motor;
		$this->encabezado[DESC_MOTOR]=BuscarDescripcionMotor($conex, $motor);
		$this->encabezado[DESC_PRES]=$this->encabezado[DESC_MOTOR];
		$this->encabezado[LISTA_MOB]=BuscarNroLista($conex, $motor);
		$this->campos_enc=ENC_STD;
		$this->tipo=STD;
		$this->ArmarMOB(STD, $this->nro, EDIT);
		$this->ArmarMAT(STD, $this->nro, EDIT);
		$this->MostrarPresOT(STD, $this->nro, EDIT, 1);
		}

	//***********************************************************************************************
	//*************************************** PRESUPUESTO CLI ***************************************
	//***********************************************************************************************

	// Prepara un nuevo presupuesto CLI y lanza la edicion
	function NuevoPresupuestoCLI($campos, $filtros, $items_std)
		{
		$this->con_sql = $this->GetConex();
		$this->LimpiarClase();
		
		$this->encabezado[NUM_OT] = 0;
		$this->encabezado[NUM_PRES] = 0;		
		$this->encabezado[NUM_STD] = $campos[NUM_STD];
		$this->encabezado[NUM_MOTOR] = $this->GetNroMotor($this->encabezado[NUM_STD]);
		$this->encabezado[DESC_PRES] = $campos[DESC_PRES];
		$this->encabezado[CLIENTE] = $campos[CLIENTE];
		$this->encabezado[DIRECCION] = $campos[DIRECCION];
		$this->encabezado[TELEFONO] = $campos[TELEFONO];
		$this->encabezado[LOCALIDAD] = $campos[LOCALIDAD];
		$this->encabezado[DESC_MOTOR] = BuscarDescripcionMotor($this->con_sql, $this->encabezado[NUM_MOTOR]);
		$this->encabezado[DESCUENTO] = $campos[DESCUENTO];
		$this->encabezado[LISTA_MOB]=BuscarNroLista($this->con_sql, $this->encabezado[NUM_MOTOR]);

		$this->campos_enc=ENC_CLI;
		$this->tipo=CLI;
		$this->filtros=$filtros;
		$this->items_std=$items_std;
		$this->ArmarMOB(CLI, $this->nro, EDIT);
		$this->ArmarMAT(CLI, $this->nro, EDIT);
		$this->MostrarPresOT(CLI, $this->nro, EDIT, 1);
		}

	//***********************************************************************************************
	//******************************************** GESTION ******************************************
	//***********************************************************************************************

	// Presenta una orden de trabajo en la version del taller
	function GestionMostrarOT($nro)
		{
		$tipo=OT;
		$this->ArmarEncabezado($tipo, $nro);
		$this->MostrarEncabezado(0);
		// Seccion MOB
		$this->ArmarMOB($tipo, $nro, 0);
		MostrarLinea("&nbsp;", "");
		for($i=0; $i<$this->cant_item_mob; $i++)	$this->item_mob[$i]->SetOT($nro);
		$this->MostrarMOB($tipo, 3);
		// Seccion MAT
		$this->ArmarMAT($tipo, $nro, 0);
		for($i=0; $i<$this->cant_item_mat; $i++)	$this->item_mat[$i]->SetOT($nro);
		$this->MostrarMAT($tipo, 3);
		?>
		<p><span class="nota" id="span_nota"><?php if($this->HayNota()) echo "NOTA: ".$this->GetNota()?></span></p>
		<?php
		MostrarReferencias();
		}

	//***********************************************************************************************
	//******************************************** TALLER *******************************************
	//***********************************************************************************************

	// Presenta una orden de trabajo en la version del taller
	function TallerMostrarOT($nro)
		{
		$tipo=OT;
		$this->ArmarEncabezado($tipo, $nro);
		$this->MostrarEncabezado(0);
		// Seccion MOB
		$this->ArmarMOB($tipo, $nro, 0);
		MostrarLinea("&nbsp;", "");
		$this->MostrarMOB($tipo, 2);
		// Seccion MAT
		$this->ArmarMAT($tipo, $nro, 0);
		$this->MostrarMAT($tipo, 2);
		?>
		<p><span class="nota" id="span_nota"><?php if($this->HayNota()) echo "NOTA: ".$this->GetNota()?></span></p>
		<?php
		}
	
	//***********************************************************************************************
	//****************************************** UTILIDADES *****************************************
	//***********************************************************************************************
	
	// Retorna su conexion a SQL
	function LimpiarClase()
		{
		for($i=0; $i<CAMPOS_ENC; $i++)	$this->encabezado[$i]=0;
		$this->campos_enc=0;
		$this->cant_item_mob=0;
		$this->cant_item_mat=0;
		$this->nota='';
		$this->nro=0;
		$this->tipo=99;
		$this->filtros=0;
		$this->add_mob=0;
		$this->add_mat=0;
		}
	
	// Retorna su conexion a SQL
	function GetConex()	{	return(Conectar(HOST, USER, PASS, BASE));	}

	// Retorna 1 si hay una nota sino 0
	function HayNota()	{	if($this->nota=="")	return 0;	return 1;	}

	// Retorna la nota almacenada
	function GetNota()	{	return($this->nota);	}

	// Setea la nota pasada
	function SetNota($nota)	{	$this->nota = $nota;	}
	
	// Setea el tipo de presupuesto u OT
	function SetTipo($tipo)	{	$this->tipo=$tipo;	}
	
	// Retorna el tipo de presupuesto u OT
	function GetTipo()	{	return($this->tipo);	}
	
	// Setea el campo nro de acuerdo al caso que sea (STD, CLI, OT)
	function SetNro()
		{
		if($this->tipo == OT)	$this->nro=$this->encabezado[NUM_OT];
		else							$this->nro=$this->encabezado[NUM_PRES];
		}
		
	// Setea la prioridad de una Orden de Trabajo
	function SetPrioridadOT($prioridad)	{	$this->prioridad=$prioridad;	}

	// Busca el numero de motor para un presupuesto dado
	function GetNroMotor($nro_std)
		{
		$query = "SELECT pse.nro_motor FROM `pse` INNER JOIN mote";
		$query = $query." WHERE pse.nro_pres=$nro_std AND pse.nro_motor=mote.nro_motor";
		$res = $this->con_sql->Execute($query);
		return($res->fields['nro_motor']);
		}

	// Busca el numero de lista de MOB para el motor dado
	function GetNroListaMOB($motor)
		{
		$query = "SELECT nro_lista FROM mote WHERE nro_motor = $motor";
		$res = $this->con_sql->Execute($query);
		return($res->fields['nro_lista']);		
		}

	// Retorna el descuento que posee un Presupuesto/OT
	function GetDescuento()	{	return($this->encabezado[DESCUENTO]);	}
	
	// Retorna el total del Presupuesto/OT
	function GetSubTotal()	{	return(sprintf("%.2f", $this->subtot_mob+$this->subtot_mat)); }
	
	// Actualiza los items para ajustar el total y descuento deseados
	function ActualizarTotales($new_desc, $new_total)
		{
		$subtot_mob=$subtot_mat=0;
		$subtotal = $this->GetSubTotal();
		$new_subtotal = $new_total / (1 - $new_desc/100);
		$var_item = ($new_subtotal - $subtotal) / $subtotal;
		
		// Modifico los precios unitarios en los items MOB
		for($i=0; $i<$this->cant_item_mob; $i++)
			{
			if($this->item_mob[$i]->GetCantidad() != 0)
				{
				$importe = sprintf("%.2f", $this->item_mob[$i]->GetImporte() * (1 + $var_item));
				$this->item_mob[$i]->SetImporte($importe);
				$subtot_mob += ($this->item_mob[$i]->GetImporte() * $this->item_mob[$i]->GetCantidad());
				}
			}
			
		// Modifico los precios unitarios en los items MAT
		for($i=0; $i<$this->cant_item_mat; $i++)
			{
			if($this->item_mat[$i]->GetCantidad() != 0)
				{
				$importe = sprintf("%.2f", $this->item_mat[$i]->GetImporte() * (1 + $var_item));
				$this->item_mat[$i]->SetImporte($importe);
				$subtot_mat += ($this->item_mat[$i]->GetImporte() * $this->item_mat[$i]->GetCantidad());
				}
			}

		// Actualizo el porcentaje del presupuesto en regtemp
		$this->encabezado[DESCUENTO]=$new_desc;
		// Actualizo los subtotales de MOB y MAT
		$this->subtot_mob = sprintf("%.2f", $subtot_mob);
		$this->subtot_mat = sprintf("%.2f", $subtot_mat);
		
		?>
		<script language="javascript">
			window.parent.location.href = '../<?php if($this->tipo==CLI) echo "PreCliente"; else echo "OT";?>/editar.php?cargado&nro='+<?php echo $this->nro?>;
		</script>
		<?php
		}

	// Localiza un item MOB/MAT y retorna su indice
	function ItemLocalizar($item, $numero, $agregado)
		{
            Debugger("ItemLocalizar(item=$item, numero=$numero, agregado=$agregado)");
		if($item==0)
			{
			for($i=0; $i<$this->cant_item_mob; $i++)
				{
				if($this->item_mob[$i]->GetNumero()==$numero && $this->item_mob[$i]->GetAgregado()==$agregado)
					{
					return($i);
					break;
					}
				}
			}
		else
			{
			for($i=0; $i<$this->cant_item_mat; $i++)
				{
				if($this->item_mat[$i]->GetRubro()==$item && $this->item_mat[$i]->GetNumero()==$numero && $this->item_mat[$i]->GetAgregado()==$agregado)
					{
					return($i);
					break;
					}
				}
			}
		}
			
	// Actualiza la cantidad de un item MOB/MAT
	function ItemSetCantidad($item, $numero, $agregado, $cantidad)
		{
            Debugger("ItemSetCantidad(item=$item, numero=$numero, agregado=$agregado, cantidad=$cantidad");
		$i=$this->ItemLocalizar($item, $numero, $agregado);
        Debugger("i = $i");
		if(!$item)	$this->item_mob[$i]->SetCantidad($cantidad);
		else
            {
                Debugger("this->item_mat[$i] =".$this->item_mat[$i]->descripcion);
                $this->item_mat[$i]->SetCantidad($cantidad);
            }
		?>
		<script language="javascript">
			window.parent.location.href = '../<?php if($this->tipo==STD) echo "PreEstandar"; else if($this->tipo==CLI) echo "PreCliente"; else echo "OT";?>/editar.php?cargado&nro='+<?php echo $this->nro?>;
		</script>
		<?php
		}

	// Agrega un item al Presupuesto/OT
	function ItemAdd($cant, $cod, $desc, $imp, $tipo)
		{
		if($tipo==MOB)
			{
			$numero = GetIndiceMOB($this->GetConex(), $this->tipo, $this->nro) + $this->add_mob;
			$this->item_mob[$this->cant_item_mob] = new ITEM(0, $numero, $cant, $cod, $desc, $imp, 1,'');
			$this->cant_item_mob++;
			$this->add_mob++;
			}
		else
			{
			$item = GetIndiceMAT($this->GetConex(), $this->tipo, $this->nro) + $this->add_mat;
			$this->item_mat[$this->cant_item_mat] = new ITEM($item, 0, $cant, $cod, $desc, $imp, 1,'');
			$this->cant_item_mat++;
			$this->add_mat++;
			}
		?>
		<script language="javascript">
			window.parent.location.href = '../<?php if($this->tipo==CLI) echo "PreCliente"; else echo "OT";?>/editar.php?cargado&nro='+<?php echo $this->nro?>;
		</script>
		<?php
		}

	// Edita un item de Presupuesto/OT
	function ItemEdit($item, $num, $add, $cant, $desc, $cod, $imp)
		{
		$i=$this->ItemLocalizar($item, $num, $add);		// Localizo el Item
		if(!$item)		// Es MOB
			{
			$total = $this->item_mob[$i]->GetCantidad() * $this->item_mob[$i]->GetImporte();	// Busco su cantidad y precio
			$this->subtot_mob-=$total;																			// Actualizo el subtotal, restando lo que aportaba el item
			$this->item_mob[$i]->Editar($cant, $desc, $cod, $imp);									// Actualizo el Item
			$this->subtot_mob+=$cant*$imp;																	// Actualizo el subtotal, sumando lo que ahora aporta
			}
		else				// Es MAT
			{
			$total = $this->item_mat[$i]->GetCantidad() * $this->item_mat[$i]->GetImporte();	// Busco su cantidad y precio
			$this->subtot_mat-=$total;																			// Actualizo el subtotal, restando lo que aportaba el item
			$this->item_mat[$i]->Editar($cant, $desc, $cod, $imp);									// Actualizo el Item
			$this->subtot_mat+=$cant*$imp;																	// Actualizo el subtotal, sumando lo que ahora aporta
			}
		$this->ActualizarTotal();
		}

	// Edita un item del encabezado
	function ItemEncabezadoEdit($item, $texto)
		{
		if($item==PRIORIDAD)	$this->prioridad=$texto;
		else						$this->encabezado[$item]=$texto;
		if($item==DESCUENTO && $this->tipo==STD)		// En caso de ser el descuento en un STD, actualizo los totales
			{
			?>
			<script language="javascript">
				window.parent.location.href = '../PreEstandar/editar.php?cargado&nro='+<?php echo $this->nro?>;
			</script>
			<?php
			}
		}
	}  // TERMINA DEFINICION DE CLASE GR

//********************************************************************************************************************
//*********************************************** CLASE ITEM *********************************************************
//********************************************************************************************************************

class ITEM
	{
	// Variables propias de items
	var $rubro;
	var $numero;
	var $cantidad;
	var $codigo;
	var $descripcion;
	var $importe;
	var $agregado;
	// Indices de spans
	var $id_cant;
	var $id_cod;
	var $id_desc;
	var $id_imp;
	var $id_tot;
	
	var $conex;					// Conexion a base SQL
	var $nro_ot;				// Numero de OT a la que pertenece el item
    var $realizado;
	
	//****************************************** FUNCIONES ******************************************
	// CONSTRUCTOR
	function __construct($rubro, $numero, $cantidad, $codigo, $descripcion, $importe, $agregado,$fecha_real)
		{
		$this->rubro=$rubro;		$this->numero=$numero;					$this->cantidad=$cantidad;
		$this->codigo=$codigo;	$this->descripcion=$descripcion;		$this->importe=$importe;
		$this->agregado=$agregado;
        $this->realizado=$fecha_real;
		if($rubro)
			{
			$this->id_cant="cant_mat-".$rubro."-".$numero."-".$agregado;
			$this->id_cod="cod_mat-".$rubro."-".$numero."-".$agregado;
			$this->id_desc="desc_mat-".$rubro."-".$numero."-".$agregado;
			$this->id_imp="imp_mat-".$rubro."-".$numero."-".$agregado;
			$this->id_tot="tot_mat-".$rubro."-".$numero."-".$agregado;
			}
		else
			{
			$this->id_cant="cant_mob-".$rubro."-".$numero."-".$agregado;
			$this->id_cod="cod_mob-".$rubro."-".$numero."-".$agregado;
			$this->id_desc="desc_mob-".$rubro."-".$numero."-".$agregado;
			$this->id_imp="imp_mob-".$rubro."-".$numero."-".$agregado;
			$this->id_tot="tot_mob-".$rubro."-".$numero."-".$agregado;
			}
		}

	// Muestra el Item en formato html
	function Mostrar($tipo, $edit)
		{
            Debugger("grsys.php - Mostar(tipo=$tipo, edit=$edit)");
		$tot=$this->cantidad*$this->importe;		
		?>
		<td align="center">
		<?php
		if($edit==EDIT)
			{
			?>
			<input name="<?php echo $this->id_cant?>" type="text" id="<?php echo $this->id_cant?>" value="<?php echo $this->cantidad?>" size="3" onChange="ActualizarTotalItem(<?php echo $this->rubro?>, <?php echo $this->numero?>, <?php echo $this->agregado?>)"/>
			<?php
			}
		else	
            { 
                echo $this->cantidad;
            }
		?>
		</td>
		<?php
		if($edit==EDIT && $tipo!=STD)
			{
			?>
			<td align="center"><a href="#" onclick="EditItem(<?php echo $this->rubro?>, <?php echo $this->numero?>, <?php echo $this->agregado?>, <?php echo $this->cantidad?>, '<?php echo $this->descripcion?>', '<?php echo $this->codigo?>', <?php echo $this->importe?>); return false;"><input type="image" name="edit" id="edit" src="../Imagenes/editar.jpg" /></a></td>
			<?php
			}
		if($this->rubro)
			{
			?>
			<td align="left"><span id="<?php echo $this->id_cod?>"><?php echo $this->codigo?></span></td>
			<?php
			}
			?>
		<td align="left"><span id="<?php echo $this->id_desc?>"><?php echo $this->descripcion?></span></td>
		<?php
		if($edit<2)
			{
			?>
			<td align="right"><span id="<?php echo $this->id_imp?>"><?php echo $this->importe?></span></td>
			<td align="right"><span id="<?php echo $this->id_tot?>"><?php if($tot)	echo sprintf("%.2f", $tot);?></span></td>
			<?php
			}
		else if($edit==3)
			{
			?>            
			<td align="center"><img src="../Imagenes/<?php $this->MostrarEstado($this->rubro, $this->numero, $this->agregado)?>" width="20" height="20" alt="imagen"></td>
			<?php
			if($this->rubro)	{?>	<td align="center"><?php $this->MostrarFecha($this->rubro, $this->numero, $this->agregado)?></td>	<?php }
			}
        if ($this->rubro==0) {
            ?>
        <td>
        &nbsp;
            <?php 
                //echo(($this->realizado<>'0000-00-00' && $this->realizado<>'') ? date_format(date_create($this->realizado), 'd/m/Y') : ''); 
            ?>
        </td>
        <?php        
        }
        Debugger("Fin de la función Mostrar()");
	}

	// Edita el item en base a los parametros pasados
	function Editar($cantidad, $descripcion, $codigo, $importe)
		{
		$this->cantidad=$cantidad;	$this->codigo=$codigo;	$this->descripcion=$descripcion;		$this->importe=$importe;
		}

	// Retorna su conexion a SQL
	function GetConex()	{	return(Conectar(HOST, USER, PASS, BASE));	}

	// Retorna valor rubro
	function GetRubro()	{	return($this->rubro);	}

	// Retorna valor numero
	function GetNumero()	{	return($this->numero);	}

	// Retorna valor cantidad
	function GetCantidad()	{	return($this->cantidad);	}
	
	// Retorna valor codigo
	function GetCodigo()	{	return($this->codigo);	}
	
	// Retorna valor descripcion
	function GetDescripcion()	{	return($this->descripcion);	}
	
	// Retorna valor importe
	function GetImporte()	{	return($this->importe);	}
	
	// Retorna valor agregado
	function GetAgregado()	{	return($this->agregado);	}

	// Setea el valor de la cantidad
	function SetCantidad($cantidad)	{	$this->cantidad = $cantidad;	}

	// Setea el valor de importe
	function SetImporte($importe)	{	$this->importe = $importe;	}
	
	// Setea el numero de OT a un item
	function SetOT($nro_ot)	{	$this->nro_ot = $nro_ot;	}
	
	// Busca y muestra el estado de un item
	function MostrarEstado($rubro, $numero, $agregado)
		{
		$conex = $this->GetConex();
		if($rubro)	$query = "SELECT asig_to";
		else			$query = "SELECT real_by";
		$query = $query."  AS estado FROM otd WHERE nro_ot=$this->nro_ot AND item=$rubro AND numero=$numero AND agregado=$agregado";		
		$res = $conex->Execute($query);
		switch($res->fields['estado'])
			{
			case 0:
				if($rubro)	echo "interrogacion.png";
				else			echo "cruz.png";
				break;
			case 1:
				echo "checkmark.png";
				break;
			case 2:
				echo "cruz.png";
				break;
			case 3:
				echo "pausa.png";
				break;
			}
		}
	
	// Busca y muestra la fecha de un material
	function MostrarFecha($rubro, $numero, $agregado)
		{
		$conex = $this->GetConex();
		$res = $conex->Execute("SELECT fecha_asig AS fecha FROM otd WHERE nro_ot=$this->nro_ot AND item=$rubro AND numero=$numero AND agregado=$agregado");
		echo $res->fields['fecha'];
		}
	}  // TERMINA DEFINICION DE CLASE ITEM

//********************************************************************************************************************
//*********************************************** CLASE ITEM *********************************************************
//********************************************************************************************************************
   
   class RECIBO {
      // Variables del recibo
      var $id_recibo;
      var $nro_ot;
      var $nro_recibo;
      var $debe;
      var $haber;
      var $fecha;
      var $recItem = array();
      var $cant_item;
      var $nota;
      
      //****************************************** FUNCIONES ******************************************
      // CONSTRUCTOR
      private function __construct() {
         
      }
      
      // 'Sobrecarga' CONSTRUCTOR con parametros definidos
      public static function Receipt($recibo=NULL, $items=NULL) {
         $obj = new RECIBO();
         
         if($recibo) {
            $obj->id_recibo = $recibo['id_recibo'];
            $obj->nro_ot = $recibo['nro_ot'];
            $obj->nro_recibo = $recibo['nro_recibo'];
            $obj->nota = $recibo['nota'];
            $obj->debe = $recibo['debe'];
            $obj->haber = $recibo['haber'];
            $obj->fecha = DateTime::createFromFormat('Y-m-d H:i:s', $recibo['last_update']);
         }
         else {
            $obj->id_recibo = 0;
            $obj->nro_ot = 0;
            $obj->nro_recibo = "Sin definir";
            $obj->nota = "";
            $obj->debe = 0;
            $obj->haber = 0;
            $obj->fecha = new DateTime();
            $obj->cant_item = 0;
            $obj->cant_item++;
            $obj->recItem[$obj->cant_item][REC_ITEM_ID] = 1;
            $obj->recItem[$obj->cant_item][REC_ITEM_TIPO] = 1;
         }
         
         if($items) {
            $obj->cant_item = 0;
            $lastItem = NULL;
            foreach($items as $item) {
               $obj->recItem[$item['id_item']][REC_ITEM_ID] = $item['id_item'];
               $obj->recItem[$item['id_item']][REC_ITEM_TIPO] = $item['id_tipo_item'];
               $obj->recItem[$item['id_item']][REC_ITEM_TIPO_DESC] = $item['recTipo'];
               $obj->recItem[$item['id_item']][REC_ITEM_OTRO] = $item['otro_desc'];
               $obj->recItem[$item['id_item']][REC_ITEM_HABER] = $item['haber'];
               $obj->recItem[$item['id_item']][REC_ITEM_BANCO] = $item['banco'];
               $obj->recItem[$item['id_item']][REC_ITEM_LOCALIDAD] = $item['localidad'];
               $obj->recItem[$item['id_item']][REC_ITEM_NRO_OP] = $item['nro_operacion'];
               $obj->recItem[$item['id_item']][REC_ITEM_FECHA] = $item['fecha'];

               if($obj->cant_item) {
                  $obj->recItem[$item['id_item']][REC_ITEM_DEBE] = $lastItem[REC_ITEM_SALDO];
                  $obj->recItem[$item['id_item']][REC_ITEM_SALDO] = $lastItem[REC_ITEM_SALDO] - $item['haber'];
               }
               else {
                  $obj->recItem[$item['id_item']][REC_ITEM_DEBE] = $recibo['debe'];
                  $obj->recItem[$item['id_item']][REC_ITEM_SALDO] = $recibo['debe'] - $item['haber'];
               }
               
               $lastItem = $obj->recItem[$item['id_item']];
               $obj->cant_item++;
            }   
         }
         return $obj;
      }
      
      //-------------------------------- GETTERS -------------------------------------
      function getDateUp() {
         return $this->fecha;
      }
      
      //-------------------------------- SETTERS -------------------------------------

      //------------------------------- FUNCIONES -------------------------------------
      function MostrarResumen($lastOne) {
          Debugger("MostrarResumen($lastOne)");
         ?>
         <td align="center"><?php echo $this->fecha->format('d-m-Y'); ?></td>
         <td align="center"><?php echo $this->nro_recibo; ?></td>
         <td align="right"><?php echo sprintf("%.2f", $this->haber); ?></td>
         <td align="right"><?php echo sprintf("%.2f", $this->debe - $this->haber); ?></td>
         <td align="center">
            <a href="#" onclick="ReciboPrint(<?php echo $this->id_recibo; ?>); return false;"><input type="image" title="Imprimir Recibo" name="btnReciboPrint" id="btnReciboPrint" src="../Imagenes/print.png" style="padding: 3px" width="22" height="22" /></a>
         </td>
         <td align="center">
            <?php
               if($lastOne) {
                  ?>
                  <a href="#" onclick="ReciboEdit(<?php echo $this->id_recibo; ?>); return false;"><input type="image" title="Editar Recibo" name="btnReciboEdit" id="btnReciboEdit" src="../Imagenes/edit.png" style="padding: 3px" width="22" height="22" /></a>
               <?php
               }
            ?>
         </td>
         <td align="center">
            <?php
               if($lastOne) {
                  ?>
                  <a href="#" onclick="ReciboDelete(<?php echo $this->id_recibo; ?>, <?php echo "'".$this->nro_recibo."'"; ?>); return false;"><input type="image" title="Eliminar Recibo" name="btnReciboDelete" id="btnReciboDelete" src="../Imagenes/deleteB.png" style="padding: 3px" width="22" height="22" /></a>
               <?php
               }
            ?>
         </td>
         <?php
      }
      
   }  // TERMINA DEFINICION DE CLASE RECIBO
   
}	// TERMINA DEFINICION DE GRSYS
