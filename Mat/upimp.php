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
$conex = $user_gr->GetConex();
$num_mot = $_GET['mot'];
$num_rub = $_GET['rub'];
$num_item = $_GET['item'];
$num_precio = $_GET['id_pre'];
$precio = $_GET['pre'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GUARDAR PRECIO MATERIAL</title>
</head>

<body>
<?php
$id="precio".$num_precio;
$query = "UPDATE mat SET $id=$precio";
$query = $query." WHERE nro_motor=$num_mot AND item=$num_rub AND numero=$num_item";
$conex->Execute($query);
?>
</body>
</html>
