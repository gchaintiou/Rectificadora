<?php
require_once "../Librerias/grsys.php";
session_start();
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex = $user_gr->GetConex();
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
// Analizo las variables que paso
isset($_GET['id_recibo']) ?	$id_recibo=$_GET['id_recibo'] : $id_recibo=0;

// Busco la info necesaria para la edicion del recibo
// Opciones de pago
$resOpt = $conex->Execute("SELECT * FROM rec_tipo_item ORDER BY id_tipo_item ASC");

// Veo si se trata de un recibo nuevo o es la edicion de uno existente
if($id_recibo) {  // Recibo existente
   $receipt = $user_gr->GetReceiptByID($id_recibo);
}
else {            // Nuevo recibo
   $receipt = RECIBO::Receipt();
   if($user_gr->cant_recibo) {   // Tomo el saldo del ultimo recibo
      $lastRecibo = $user_gr->recibos[$user_gr->cant_recibo-1];
      $receipt->debe = $lastRecibo->debe - $lastRecibo->haber;
      $receipt->haber = 0;
      
      $receipt->recItem[1][REC_ITEM_DEBE] = $receipt->debe;
      $receipt->recItem[1][REC_ITEM_HABER] = 0;
      $receipt->recItem[1][REC_ITEM_SALDO] = $receipt->debe;
   }
   else {         // Tomo el total de la OT como saldo
      $sub_mob = $user_gr->subtot_mob;
		$sub_mat = $user_gr->subtot_mat;
		$descuento = $user_gr->encabezado[DESCUENTO];
		$subtotal = $sub_mob+$sub_mat;
		$sub_desc = $subtotal*$descuento/100;
		$total = sprintf("%.2f", $subtotal*(1-$descuento/100));
      $receipt->debe = $total;
      $receipt->haber = 0;
      $receipt->recItem[1][REC_ITEM_DEBE] = $total;
      $receipt->recItem[1][REC_ITEM_HABER] = 0;
      $receipt->recItem[1][REC_ITEM_SALDO] = $total;
   }
}

?>
<div align="center" class="titulo2">Por favor, ingrese los datos necesarios:</div>
<div>&nbsp;</div>
<table width="90%" align="center">
   <tr>
      <td width="40%" class="head_item_std1">Recibo OT N°:</td>
      <td width="60" class="head_item_std2"><div id="nro_recibo"><?php echo $receipt->nro_recibo; ?></div></td>
   </tr>
   <tr>
      <td width="40%" class="head_item_std1">Fecha:</td>
      <td width="60%" class="head_item_std2"><div id="date_recibo"><?php echo $receipt->getDateUp()->format('d-m-Y'); ?></div></td>
   </tr>
</table>
<div id="parentItems" style="width: 90%; margin: 10px auto;">
   <div class="head_item_std3" style="float: left; width: 35%; text-align: left;">Item de Pago</div>
   <div class="head_item_std3" style="float: left; width: 20%; text-align: right;">Debe($)</div>
   <div class="head_item_std3" style="float: left; width: 20%; text-align: right;">Haber ($)</div>
   <div class="head_item_std3" style="float: left; width: 20%; text-align: right;">Saldo ($)</div>
   <div class="head_item_std3" style="display: block; float: left; width: 5%; text-align: right;"></div>
   
   <?php
   foreach($receipt->recItem as $payItem) {
      $resOpt->MoveFirst();
      ?>
      <div id="<?php echo "group_pay_".$payItem[REC_ITEM_ID]; ?>" name="group_pay" class="divReciboItemGroup">
         <div class="divReciboItemType">
            <select name="id_type" size="1" id="<?php echo "combobox_".$payItem[REC_ITEM_ID];?>" onChange="ReciboSelectedTypeItem(this)">
               <?php
               while(!$resOpt->EOF) {
                  $id=$resOpt->fields['id_tipo_item'];
                  ?>
                  <option value="<?php echo $id; ?>" <?php if($payItem[REC_ITEM_TIPO] == $id){echo "selected=\"selected\"";}?>><?php echo $resOpt->fields['descripcion']?></option>
                  <?php
                  $resOpt->MoveNext();
                  }
               ?>
            </select>
         </div>
         <div id="<?php echo "debe_".$payItem[REC_ITEM_ID]; ?>" class="divReciboItemDebe"><?php echo sprintf("%.2f", $payItem[REC_ITEM_DEBE]); ?></div>
         <div class="divReciboItemHaber">
            <input type="text" name="haber" id="<?php echo "haber_".$payItem[REC_ITEM_ID]; ?>" style="text-align: right;" size="10" maxlength="50" value="<?php echo $payItem[REC_ITEM_HABER]; ?>" onchange="ReciboItemUpdate(this)"/>
         </div>
         <div id="<?php echo "saldo_".$payItem[REC_ITEM_ID]; ?>" class="divReciboItemSaldo"><?php echo sprintf("%.2f", $payItem[REC_ITEM_SALDO]); ?></div>
         <div class="divReciboItemRemove">
            <a href="#">
               <input type="image" title="Eliminar Item" name="btnItemRemove" id="<?php echo "btnItemRemove_".$payItem[REC_ITEM_ID];?>" src="../Imagenes/deleteB.png" style="padding: 3px" width="15" height="15" onclick="ReciboItemRemove(this); return false;" />
            </a>
         </div>
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
                        <div class="divReciboItemExtraHdr">Fecha:</div>
                        <div style="float: left; width: 80%;">
                           <?php
                           $date = new DateTime($payItem[REC_ITEM_FECHA]);
                           ?>
                           <input id="entryFecha_<?php echo $payItem[REC_ITEM_ID]; ?>" type="date" size="15" maxlength="12" placeholder="ej: 24-09-1984" value="<?php echo $date->format('Y-m-d'); ?>">
                        </div>
                     </div>
                     <div style="width: 100%; height: 22px;">
                        <div class="divReciboItemExtraHdr">Banco:</div>
                        <div style="float: left; width: 80%;">
                           <input id="entryBanco_<?php echo $payItem[REC_ITEM_ID]; ?>" type="text" size="25" maxlength="100" value="<?php echo $payItem[REC_ITEM_BANCO]; ?>">
                        </div>
                     </div>
                     <div style="width: 100%; height: 22px;">
                        <div class="divReciboItemExtraHdr">Localidad:</div>
                        <div style="display: block; float: left; width: 80%;">
                           <input id="entryLocalidad_<?php echo $payItem[REC_ITEM_ID]; ?>" type="text" size="25" maxlength="100" value="<?php echo $payItem[REC_ITEM_LOCALIDAD]; ?>">
                        </div>
                     </div>
                     <div>
                        <div class="divReciboItemExtraHdr"><?php if($payItem[REC_ITEM_TIPO] == 2) { echo "N° Cheque:"; } else { echo "N° Operación:"; } ?></div>
                        <div style="display: block; float: left; width: 80%;">
                           <input id="entryNumero_<?php echo $payItem[REC_ITEM_ID]; ?>" type="text" size="25" maxlength="100" value="<?php echo $payItem[REC_ITEM_NRO_OP]; ?>">
                        </div>
                     </div>
                     <?php
                     break;
                  
                  case 5:
                     ?>
                     <div style="width: 100%; height: 22px;">
                        <input id="entryOtro_<?php echo $payItem[REC_ITEM_ID]; ?>" type="text" size="30" maxlength="100" placeholder="Descripción..." value="<?php echo $payItem[REC_ITEM_OTRO]; ?>">
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
<div class="gris_claro" style="width: 90%; height: 25px; margin: 10px auto; border-top: 1px #0CF double; overflow: auto; font-weight: bold; padding-top: 5px;">
   <div style="float: left; width: 35%; text-align: right;">Resumen Total ($):</div>
   <div id="totalDebe" style="float: left; width: 20%; text-align: right;"><?php echo sprintf("%.2f", $receipt->debe); ?></div>
   <div id="totalHaber" style="float: left; width: 20%; text-align: right;"><?php echo sprintf("%.2f", $receipt->haber); ?></div>
   <div id="totalSaldo" style="float: left; width: 20%; text-align: right;"><?php echo sprintf("%.2f", $receipt->debe - $receipt->haber); ?></div>
   <div style="display: block; float: left; width: 5%; text-align: right;"></div>
</div>
<span id="cant_item" hidden><?php echo $receipt->cant_item; ?></span>
<div style="width: 90%; margin: 10px auto; clear: both;">
   <a href="#" onclick="ReciboItemAdd(); return false;"><input type="image" title="Agregar Item" name="btnItemAdd" id="btnItemAdd" src="../Imagenes/add.png" style="padding: 3px" width="22" height="22" /></a>
</div>
<div id="textoHaber" style="width: 90%; height: 25px; margin: 10px auto; border-top: 1px #0CF double; font-weight: bold; padding-top: 5px;">
</div>
<div style="width: 90%; margin: 10px auto; clear: both;">
   <div>NOTA: <input type="text" id="entryNota" size="75" maxlength="150"  value="<?php echo $receipt->nota; ?>"></div>
</div>
<div style="width: 100%;">
   <div style="width: 50%; margin: 20px auto; text-align: center;">
      <input type="button" name="ok" id="ok" value="Aceptar" style="width: 100px; margin: auto auto;" onClick="parent.ventana.hide()"/>
   </div>
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>

<script language="javascript">
      var texto = numeroALetras(parseFloat(<?php echo $receipt->haber; ?>).toFixed(2), {
      plural: 'pesos',
      singular: 'peso',
      centPlural: 'centavos',
      centSingular: 'centavo'
   });
   
   document.getElementById("textoHaber").innerHTML = "SON: "+texto;
</script>