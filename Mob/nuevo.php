<?php
require_once "../Librerias/grsys.php";
session_start();
if(!isset($_SESSION['user_gr']))
	{
	?>
	<script language="javascript"> window.parent.location.href = '../index.php'; </script>
	<?php
	}
require_once "../Librerias/javalib.php";
require_once "../Librerias/funciones.php";
$user_gr = $_SESSION['user_gr'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<HEAD>
<!-- InstanceBeginEditable name="doctitle" -->

<TITLE>Nuevo Item MOB</TITLE>

<!-- InstanceEndEditable -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- InstanceBeginEditable name="head" -->

<!-- InstanceEndEditable -->
<style type="text/css">
<!--
body {
	background-color: #172047;
}
-->
</style>
<script type="text/javascript" src="<?php echo $_SESSION['menu']?>"></script>
<link href="../estilos.css" rel="stylesheet" type="text/css">
</HEAD>

<BODY>
<table width="98%" align="center">
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	 	<!-- InstanceBeginEditable name="Data" -->

		<!-- InstanceEndEditable -->
    </td>
  </tr>
  <tr>
    <td width="1%" class="celda_menu">
    <span>
    	<a href="http://www.dhtml-menu-builder.com"  style="display:none;visibility:hidden;">Javascript DHTML Drop Down Menu Powered by dhtml-menu-builder.com</a>
      <script id="sothink_widgets:dwwidget_dhtmlmenu6_24_2009.pgt" type="text/javascript">
<!--
stm_bm(["menu4d80",900,"<?php echo $_SESSION['imagenes'] ?>","blank.gif",0,"","",0,0,250,0,1000,1,0,0,"<?php echo $_SESSION['raiz']?>","",0,0,1,2,"default","hand","",1,25],this);
stm_bp("p0",[1,4,0,0,0,0,25,14,100,"",-2,"",-2,50,0,0,"#999999","#000000","",3,0,0,"#000000"]);
stm_ai("p0i0",[6,1,"#000000","",-1,-1,0]);
stm_ai("p0i1",[0,"  INICIO","","",-1,-1,0,"index.php","_self","","","Home%20-%205.png","Home%20-%205.png",20,20,0,"","",0,0,0,0,1,"#E6EFF9",1,"#E6EFF9",1,"round18_m[3].gif","round18_m[3].gif",3,3,0,0,"#E6EFF9","#000000","#00FF00","#00CCFF","bold 10pt Microsoft Sans Serif","bold 10pt Microsoft Sans Serif",0,0,"round18_l[3].gif","round18_l[3].gif","round18_r[3].gif","round18_r[3].gif",5,5,26],160,0);
stm_aix("p0i2","p0i0",[]);
stm_aix("p0i3","p0i1",[0,"  PRESUPUESTO","","",-1,-1,0,"","_self","","","Documents2.png","Documents2.png",20,20,0,"","",0,0,0,0,1,"#E6EFF9",1,"#E6EFF9",1,"round18_m[3].gif","round18_m[3].gif",3,3,0,0,"#E6EFF9","#000000","#00FF00","#00FF00","bold 9pt Microsoft Sans Serif","bold 9pt Microsoft Sans Serif"],160,0);
stm_ai("p0i4",[0,"     Estandar","","",-1,-1,0,"","_self","","","","",0,0,0,"arrow_40.gif","arrow_40[1].gif",14,14,0,0,1,"#E6EFF9",0,"#E6EFF9",0,"bg_02[5].gif","bg_02[4].gif",3,3,0,0,"#E6EFF9","#000000","#00FFCC","#0000FF","bold 10pt Microsoft Sans Serif","italic bold 10pt Microsoft Sans Serif",0,0,"","","","",0,0,0],160,22);
stm_bpx("p1","p0",[1,2,0,0,0,0,0,0,100,"stEffect(\"slip\")",-2,"progid:DXImageTransform.Microsoft.Wipe(GradientSize=1.0,wipeStyle=0,motion=reverse,enabled=0,Duration=0.20)",7,90,2,2,"#006699"]);
stm_aix("p1i0","p0i4",[0,"  Buscar","","",-1,-1,0,"PreEstandar/buscar.php","_self","","","","",0,0,0,"","",0,0,0,0,1,"#E6EFF9",0,"#E6EFF9",0,"bg_02[5].gif","bg_02[4].gif",3,3,0,0,"#E6EFF9","#000000","#00FFCC","#0000FF","bold 9pt Microsoft Sans Serif","italic bold 9pt Microsoft Sans Serif"],60,22);
stm_aix("p1i1","p1i0",[0,"  Crear","","",-1,-1,0,"PreEstandar/nuevo.php"],60,22);
stm_ep();
stm_aix("p0i5","p0i0",[]);
stm_aix("p0i6","p0i4",[0,"     Cliente"],160,22);
stm_bpx("p2","p1",[]);
stm_aix("p2i0","p1i0",[0,"  Buscar","","",-1,-1,0,"PreCliente/buscar.php"],60,22);
stm_aix("p2i1","p1i0",[0,"  Crear","","",-1,-1,0,"PreCliente/nuevo.php"],60,22);
stm_ep();
stm_aix("p0i7","p0i0",[]);
stm_aix("p0i8","p0i3",[0,"  O. TRABAJO","","",-1,-1,0,"","_self","","","","",25,25],160,0);
stm_aix("p0i9","p0i4",[0,"     Buscar","","",-1,-1,0,"OT/buscar.php","_self","","","","",0,0,0,"","",0,0],160,22);
stm_aix("p0i10","p0i0",[]);
stm_aix("p0i11","p0i3",[0," MANO DE OBRA","","",-1,-1,0,"","_self","","","","",0,0],160,0);
stm_aix("p0i12","p0i9",[0,"     Nuevo Item","","",-1,-1,0,"Mob/nuevo.php"],160,22);
stm_aix("p0i13","p0i0",[]);
stm_aix("p0i14","p0i4",[0,"     Editar Lista"],160,22);
stm_bpx("p3","p1",[]);
stm_aix("p3i0","p1i0",[0,"  Global","","",-1,-1,0,"Mob/lista.php?opc=0"],65,22);
stm_aix("p3i1","p3i0",[0,"  Por Item","","",-1,-1,0,"Mob/lista.php?opc=1"],65,22);
stm_ep();
stm_aix("p0i15","p0i0",[]);
stm_aix("p0i16","p0i11",[0,"  MATERIAL"],160,0);
stm_aix("p0i17","p0i9",[0,"     Nuevo Item","","",-1,-1,0,"Mat/nuevo.php"],160,22);
stm_aix("p0i18","p0i0",[]);
stm_aix("p0i19","p0i9",[0,"     Editar Lista","","",-1,-1,0,"Mat/lista.php"],160,22);
stm_aix("p0i20","p0i0",[]);
stm_aix("p0i21","p0i11",[0,"  RUBRO"],160,0);
stm_aix("p0i22","p0i9",[0,"     Nuevo","","",-1,-1,0,"Rubro/nuevo.php"],160,22);
stm_aix("p0i23","p0i0",[]);
stm_aix("p0i24","p0i9",[0,"     Editar","","",-1,-1,0,"Rubro/editar.php"],160,22);
stm_aix("p0i25","p0i0",[]);
stm_aix("p0i26","p0i11",[0,"  MOTOR"],160,0);
stm_aix("p0i27","p0i9",[0,"     Nuevo","","",-1,-1,0,"Motor/nuevo.php"],160,22);
stm_aix("p0i28","p0i0",[]);
stm_aix("p0i29","p0i9",[0,"     Editar","","",-1,-1,0,"Motor/editar.php"],160,22);
stm_aix("p0i30","p0i0",[]);
stm_aix("p0i31","p0i3",[0,"  PROVEEDOR","","",-1,-1,0,"","_self","","","exet.ru%20%2842%29.png","exet.ru%20%2842%29.png"],160,0);
stm_aix("p0i32","p0i9",[0,"     Actualizar","","",-1,-1,0,"Proveedor/update.php"],160,22);
stm_aix("p0i33","p0i0",[]);
stm_aix("p0i34","p0i9",[0,"     Explorar Lista","","",-1,-1,0,"Proveedor/lista.php"],160,22);
stm_aix("p0i35","p0i0",[]);
stm_aix("p0i36","p0i3",[0,"   GESTION","","",-1,-1,0,"","_self","","","","",0,0],160,0);
stm_aix("p0i37","p0i9",[0,"     Operario","","",-1,-1,0,"Gestion/operario.php"],160,22);
stm_aix("p0i38","p0i0",[]);
stm_aix("p0i39","p0i4",[0,"     Informe"],160,22);
stm_bpx("p4","p1",[]);
stm_aix("p4i0","p1i0",[0,"  Crear","","",-1,-1,0,"Gestion/informe.php?new"],65,22);
stm_aix("p4i1","p4i0",[0,"  Mostrar","","",-1,-1,0,"Gestion/informe.php"],65,22);
stm_ep();
stm_aix("p0i40","p0i0",[]);

stm_aix("p0i41","p0i4",[0,"     Produccion"],160,22);
stm_bpx("p5","p1",[]);
stm_aix("p5i0","p1i0",[0,"  Actual","","",-1,-1,0,"Gestion/mob.php?editar"],65,22);
stm_aix("p5i1","p5i0",[0,"  Buscar","","",-1,-1,0,"Gestion/mob.php?ver"],65,22);
stm_ep();
stm_aix("p0i42","p0i0",[]);
stm_aix("p0i43","p0i9",[0,"     Materiales","","",-1,-1,0,"Gestion/material.php"],160,22);
stm_aix("p0i44","p0i0",[]);
stm_ep();
stm_em();
//-->
       </script>
		 </span>
    </td>
    <td width="100%" rowspan="2" valign="top" class="celda_cuerpo">
		 <!-- InstanceBeginEditable name="Trabajo" -->
		 <div>&nbsp;</div>
		 <div align="center" class="titulo1">Ingrese los Datos Necesarios para el Nuevo Item:</div>
       <?php
		 if(isset($_GET['guardar']))
		 	{
			$conex = $user_gr->GetConex();
			// Doy de Alta el Item con la Descripcion
			$desc = $_POST['descripcion'];
			$sec = $_POST['seccion'];
			$res = $conex->Execute("SELECT numero FROM mobe ORDER BY numero DESC LIMIT 1");
			$numero = $res->fields['numero']+1;
			$conex->Execute("INSERT INTO mobe VALUES($numero, '$desc', $sec)");

			// Doy de Alta los Precios de las Distintas Listas
			for($i=1; $i<19; $i++)
				{
				if($_POST['importe'.$i] == '')	$importe=0;
				else										$importe=$_POST['importe'.$i];
				$conex->Execute("INSERT INTO mobd VALUES($numero, $i, $importe)");
				}
			?>
			<div>&nbsp;</div>
			<div align="center" class="titulo2">El Item se ha Agregado con Exito</div>
			<div>&nbsp;</div>
			<table align="center" width="50%">
				<tr>
					<td width="40%" align="right"><input name="aceptar" type="button" value="Aceptar" width="30" onClick="MOBCancelar()" on/></td>
					<td width="20%">&nbsp;</td>
					<td width="40%" align="left"><input name="nuevo" type="button" value="Nuevo" width="30" onClick="MOBNewItem()"/></td>
				</tr>
			</table>				
			<?php
			}
		else
			{
			?>
			<div>&nbsp;</div>
			<table align="center" width="50%">
				<tr>
					<td width="40%" align="right"><input name="cancelar" type="button" value="Cancelar" width="30" onClick="MOBCancelar()" on/></td>
					<td width="20%">&nbsp;</td>
					<td width="40%" align="left"><input name="aceptar" type="button" value="Guardar" width="30" onClick="MOBGuardarNew()"/></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<form action="nuevo.php?guardar" method="post" name="formnew" id="formnew">
			<table align="center" width="60%">
				<tr>
					<td width="40%" class="head_item_std1">Descripcion:</td>
					<td width="60%" class="head_item_std2"><input name="descripcion" id="descripcion" type="text" value="" size="55" maxlength="100"/></td>
				</tr>
				<tr>
					<td width="40%" class="head_item_std1">Seccion:</td>
					<td width="60%" class="head_item_std2">
						<select name="seccion" size="1" id="seccion">
							<option value="0">Seleccionar Seccion</option>
							<?php
							$result = MOB_BuscarSecciones($user_gr->GetConex());
							while(!$result->EOF)
								{
								$nro = $result->fields['nro'];
								$desc = $result->fields['descripcion'];
								?>
								<option value="<?php echo $nro?>"><?php echo htmlentities($desc)?></option>
								<?php
								$result->MoveNext();
								}
							?>
						</select>
					</td>
				</tr>
				<?php
				for($i=1; $i<19; $i++)
					{
					?>
					<tr>
						<td class="head_item_std1">Precio Lista<?php echo $i?>:</td>
						<td class="head_item_std2"><input name="importe<?php echo $i?>" id="importe<?php echo $i?>" type="text" value="0" size="10" maxlength="10" onChange="MOBValidarImporte(<?php echo $i?>, <?php echo "0"?>)"/></td>
					</tr>
					<?php
					}
					?>
			</table>
			<div>&nbsp;</div>
			<table align="center" width="50%">
				<tr>
					<td width="40%" align="right"><input name="cancelar" type="button" value="Cancelar" width="30" onClick="MOBCancelar()" on/></td>
					<td width="20%">&nbsp;</td>
					<td width="40%" align="left"><input name="aceptar" type="button" value="Guardar" width="30" onClick="MOBGuardarNew()"/></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			</form>
			<?php
			}
		 ?>
       <!-- InstanceEndEditable -->
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
    		<div align="center" class="pie"><?php echo "Joaquín V. Gonzalez 769 - Santa Rosa - La Pampa"?></div>
    		<div align="center" class="pie">Tel: 02954-424916</div>
         <div align="center" class="pie">e-mail: gilesrec@gopertec.com.ar</div>
    </td>
  </tr>
</table>

<iframe id="myframe" name="myframe" src="" frameborder="0" framespacing="0" scrolling="auto" border="0" style="background:#0F0; position:absolute; left:500px; top:300px; width:500px; height:500px; z-index:5; visibility:hidden;"></iframe>

</BODY>
<!-- InstanceEnd --></HTML>
