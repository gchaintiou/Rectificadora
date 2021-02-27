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
$conex=$user_gr->GetConex();
$motor = $_GET['motor'];
$desc_motor = BuscarDescripcionMotor($conex, $motor);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php
// Actualizo los ticks de los items seleccionados 
$result1 = $conex->Execute("SELECT numero FROM mobe ORDER BY numero ASC");
$result2 = $conex->Execute("SELECT numero FROM motd WHERE nro_motor=$motor ORDER BY numero ASC");

while(!$result1->EOF)
	{
	$num_mob = $result1->fields['numero'];
	$num_mot = $result2->fields['numero'];
	
	if($num_mob == $num_mot)
		{
		?>
		<script language="javascript"> TildarItem(<?php echo $num_mob?>)</script>
		<?php
		$result2->MoveNext();
		}
	else
		{
		?>
		<script language="javascript"> DesTildarItem(<?php echo $num_mob?>)</script>
		<?php
		}
	$result1->MoveNext();
	}
	
// Actualizo la lista de precios
$lista = BuscarNroLista($conex, $motor);
?>
<script language="javascript">
	MOTMostrarPrecios(<?php echo $lista?>); 	
	parent.document.SelectItems.desc_motor.value = <?php echo "'$desc_motor'"?>;
	parent.document.SelectItems.lista.value = <?php echo $lista?>;
</script>
</body>
</html>