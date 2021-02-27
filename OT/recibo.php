<?php
require_once "../Librerias/grsys.php";
session_start();
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex = $user_gr->GetConex();

// Analizo las variables que paso
isset($_GET['opt']) ?	$opt=$_GET['opt'] : $opt=0;
isset($_GET['id_recibo']) ?	$id_recibo=$_GET['id_recibo'] : $id_recibo=0;
isset($_GET['params']) ?	$params=$_GET['params'] : $params=array();

switch($opt) {
   case 1:  // Add/Edit
      $items = json_decode($params);
      $edit = $items->hdr->edit;

      // --------------- Update/Insert Receipt Header ---------------
      If($edit == 1) {   // Update existing receipt (edition)
         $id_recibo = $items->hdr->id_recibo;
         // Update Header
         $query = "UPDATE rece SET nota='".$items->hdr->nota."' WHERE id_recibo=".$id_recibo;
         $conex->Execute($query);
         
         // Remove existing items
         $query = "DELETE FROM recd WHERE id_recibo=".$id_recibo;
         $conex->Execute($query);
      }
      else {         // Insert new receipt
         // Determino numero de recibo
         $resCount = $conex->Execute("SELECT COUNT(id_recibo) AS numero FROM rece WHERE nro_ot=".$items->hdr->nro_ot);
         $items->hdr->nro_recibo = sprintf("%s-%d", $items->hdr->nro_ot, $resCount->fields['numero']+1);
         
         $query = "INSERT INTO rece(id_recibo, nro_ot, nro_recibo, nota) VALUES(NULL,".$items->hdr->nro_ot.", '".$items->hdr->nro_recibo."', '".$items->hdr->nota."')";
         $conex->Execute($query);
         
         $res = $conex->Execute("SELECT id_recibo FROM rece ORDER BY id_recibo DESC LIMIT 1");
         $id_recibo = $res->fields['id_recibo'];
      }
      
      
      // --------------- Insert Receipt Items ---------------
      // Insert pay items
      foreach($items as $key=>$item) {
         if(strcmp($key, "hdr")) {
            $query = "INSERT INTO recd(id_recibo, id_item, id_tipo_item, otro_desc, haber, banco, localidad, nro_operacion, fecha) VALUES(".$id_recibo.", ".$key.", ".$item->tipoPago;

            if($item->tipoPago == 5) {
               $query = $query.", '".$item->entryOtro."'";
            }
            else {
               $query = $query.", ''";
            }
            
            $query = $query.", ".$item->haber;
            
            if($item->tipoPago == 2 || $item->tipoPago == 3) {
               $query = $query.", '".$item->entryBanco."', '".$item->entryLocalidad."', '".$item->entryNumero."', '".$item->entryFecha."')";
            }
            else {
               $query = $query.", '', '', '', 0)";
            }
            
            $conex->Execute($query);
         }
      }
      break;
      
   case 3:     // Delete
      // Delete the receipt and its items
      $res = $conex->Execute("SELECT nro_ot FROM rece WHERE id_recibo=".$id_recibo);
      $ot = $res->fields['nro_ot'];
      $conex->Execute("DELETE FROM rece WHERE id_recibo=".$id_recibo);
      $conex->Execute("DELETE FROM recd WHERE id_recibo=".$id_recibo);
      
      unset($user_gr->recibo[$user_gr->cant_recibo]);
      $user_gr->cant_recibo--;
      break;

   default:
      var_dump(json_decode($params));
}
?>
<script language="javascript">
   window.parent.location.reload();
</script>