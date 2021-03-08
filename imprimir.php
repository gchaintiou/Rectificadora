<?php
require_once "../Rectificadora/Librerias/grsys.php";
session_start();

if(!isset($_SESSION['user_gr']))	{	?>	
   <script language="javascript"> window.parent.location.href = 'index.php'; </script>
<?php
}
require_once "../Rectificadora/Librerias/javalib.php";
require_once "../Rectificadora/Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$nivel = $_GET['nivel'];
isset($_GET['id_recibo']) ? $id=$_GET['id_recibo'] : $id=0;
isset($_GET['num_ot']) ? $num_ot=$_GET['num_ot'] : $num_ot=0;
?>

<script type="text/javascript" src="./Js/num2text.js"></script>
   
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <link href="../Rectificadora/estilos.css" rel="stylesheet" type="text/css">
   <title>Imprimir</title>
   </head>
   <body>
   <?php
   if ($num_ot){
       Debugger("Imprimir todos los recibos de la Orden de trabajo nro. $num_ot");
       $user_gr->ImprimirAllRecibos($num_ot);
   }
   else
        if($id) {   // Recibo de OT
            $user_gr->ImprimirRecibo($id,true);
        }
        else {      // Presupuesto u OT
            $user_gr->ImprimirPresOT($nivel);
        }
      
   ?>
   </body>
</html>