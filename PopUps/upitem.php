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

isset($_GET['hacer']) ? $hacer=$_GET['hacer'] : $hacer=0;
isset($_GET['rub']) ? $rub=$_GET['rub'] : $rub=0;
isset($_GET['num']) ? $numero=$_GET['num'] : $numero=0;
isset($_GET['add']) ? $agregado=$_GET['add'] : $agregado=0;
isset($_GET['cant']) ? $cantidad=$_GET['cant'] : $cantidad=0;
isset($_GET['desc']) ? $desc=str_replace('|', '+', $_GET['desc']) : $desc=0;
isset($_GET['cod']) ? $codigo=str_replace('|', '+', $_GET['cod']) : $codigo=0;
isset($_GET['motor']) ? $motor=$_GET['motor'] : $motor=0;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>
<?php
Debugger("PopUps/upitem.php - hacer=$hacer");
switch($hacer)
	{
	case 0:		// Item de PRES/OT
		$user_gr->ItemSetCantidad($rub, $numero, $agregado, $cantidad);
		break;
	case 1:		// Item MAT
		$conex = $user_gr->GetConex();
		$query = "UPDATE mat SET desc_mat='$desc', codigo='$codigo'";
		$query = $query." WHERE nro_motor=$motor AND item=$rub AND numero=$numero";
        Debugger($query);
		MostrarLinea($query, "warning");
		$conex->Execute($query);
		break;
	}
?>
</body>
</html>