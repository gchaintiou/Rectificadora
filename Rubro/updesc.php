<?
require_once "../Librerias/grsys.php";
session_start();
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
$conex = $user_gr->GetConex();
$rubro = $_GET['rubro'];
$descripcion = BuscarRubro($conex, $rubro);
?>

<script language="javascript"> parent.document.FormEdit.desc.value = <?php echo "'$descripcion'"?>; </script>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>
<body>

</body>
</html>