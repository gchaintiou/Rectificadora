<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))
	{
	?>
	<script language="javascript"> window.parent.location.href = 'index.php'; </script>
	<?php
	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$lista = $_GET['lista'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php
$conex=$user_gr->GetConex();
$result = $conex->Execute("SELECT numero, importe FROM mobd WHERE columna=$lista");

while(!$result->EOF)
	{
	$item = $result->fields['numero'];
	$importe = $result->fields['importe'];
	?>
	<script language="javascript"> MOTActualizarImporte(<?php echo $item?>, <?php echo $importe?>)</script>
	<?php
	$result->MoveNext();
	}
?>
<script language="javascript"> parent.document.getElementById("span_lista").innerHTML = <?php echo $lista?>; </script>

</body>
</html>