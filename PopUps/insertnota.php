<?php
include "../Librerias/grsys.php";
session_start();
include "../Librerias/javalib.php";
include "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];

isset($_GET['hacer']) ? $hacer = $_GET['hacer'] : $hacer = 0;
isset($_GET['nota']) ? $nota = $_GET['nota'] : $nota = $user_gr->GetNota();
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
switch($hacer)
	{
	case 0:
		{
		?>
		<div class="titulo2">Ingrese la Nota:</div>
		<table width="90%" align="center">
			<tr>
				<td align="center"><input name="nota" type="text" id="nota" value="<?php echo $nota?>" size="90" maxlength="200" /></td>
			</tr>
			<tr>
				<td align="center">&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><input name="ok" id="ok" value="Aceptar" type="button" onClick="parent.notewin.hide()"/></td>
			</tr>
		</table>
		<?
		break;
		}
		case 1:
			$user_gr->SetNota($nota);
			break;
	}
?>
<!-- InstanceEndEditable -->
</BODY>
<!-- InstanceEnd --></HTML>
