<?php
//session_start();
//require('../adodb/adodb.inc.php');

define("SITIO", "http://127.0.0.1:81/hogaresnuevos.php/");

//<sumary>
//Conectar a la Base de Datos
//</sumary>
function conectar(){
	$c=ADONewConnection('mysql');
	//$c->Connect('localhost','ga000619_mariano','mariano','ga000619_generico');
	//$c->Connect('localhost','ur000361_mariano','mariano','ur000361_marinelli');
	$c->Connect('localhost','root','','marinelli');
	return $c;
}

//<sumary>
//Vista de productos
//</sumary>
function vProductos() {
	$strSQL = "SELECT P.id AS id, P.precio, P.descripcion, P.otropago, P.detalle, O.precio AS preciooferta, C.descripcion AS Categoria, M.simbolo, C.id AS idcategoria ";
	$strSQL = $strSQL." , P.URLimagen, P.URLimagen2, P.URLimagen3, CA.Descripcion AS categoria, CA2.Descripcion AS categoria2, CA3.Descripcion AS categoria3, PD.producto, PN.producto AS Nuevo  ";
	$strSQL = $strSQL." FROM tbproductos P INNER JOIN tbcategorias C ON C.id = P.categoria";
	$strSQL = $strSQL." INNER JOIN tbmonedas M ON M.id = P.moneda ";
	$strSQL = $strSQL." LEFT JOIN tbofertas O ON P.id = O.producto ";
	$strSQL = $strSQL." LEFT JOIN tbcategorias CA ON CA.id = P.categoria";
	$strSQL = $strSQL." LEFT JOIN tbcategorias CA2 ON CA2.id = CA.padre";
	$strSQL = $strSQL." LEFT JOIN tbcategorias CA3 ON CA3.id = CA2.padre";
	$strSQL = $strSQL." LEFT JOIN tbproductosdestacados PD ON PD.producto = P.id";
	$strSQL = $strSQL." LEFT JOIN tbproductosnuevos PN ON PN.producto = P.id";
	return($strSQL);
}

//<sumary>
//Retorna Categorias de Productos
//</sumary>
function obtenerCategorias($conexion, $padre) {

	$strSQL = "SELECT * FROM tbcategorias";
	if ($padre > 0) 
		$strSQL = $strSQL." WHERE padre = $padre";
	$strSQL = $strSQL." ORDER BY id";	

	return($conexion->Execute($strSQL));
}

//<sumary>
//Retorna todas las ofertas.
//</sumary>
function obtenerOfertas($conexion) {
	$strSQL = "SELECT P.categoria, P.id, O.precio AS preciooferta, P.nombre, P.precio AS precioproducto, P.URLimagen, P.URLimagen2, URLimagen3, P.descripcion, M.simbolo FROM tbofertas O ";
	$strSQL = $strSQL . " INNER JOIN tbproductos P ON P.id = O.producto";
	$strSQL = $strSQL . " INNER JOIN tbmonedas M ON M.id = P.moneda";

	$strSQL = $strSQL." ORDER BY O.id desc LIMIT 0,4";	

	return($conexion->Execute($strSQL));
}

//<sumary>
//Retorna todas las ofertas de dsititas categorias.
//</sumary>
function obtenerOfertasDistintasCategorias($conexion) {
	$strSQL = "SELECT P.categoria AS categoria, P.id, O.precio AS preciooferta, P.descripcion, P.precio AS precioproducto, P.URLimagen, P.URLimagen2, URLimagen3, P.descripcion, M.simbolo FROM tbofertas O ";
	$strSQL = $strSQL . " INNER JOIN tbproductos P ON P.id = O.producto";
	$strSQL = $strSQL . " INNER JOIN tbmonedas M ON M.id = P.moneda";

	$strSQL = $strSQL." ORDER BY P.categoria";	

	return($conexion->Execute($strSQL));
}

//<sumary>
//Retorna todas las ofertas de libros
//</sumary>
function obtenerOfertasDeLibros($conexion) {
	$strSQL = "SELECT P.categoria AS categoria, P.id, O.precio AS preciooferta, P.descripcion, P.precio AS precioproducto, P.URLimagen, P.URLimagen2, URLimagen3, P.descripcion, M.simbolo FROM tbofertas O ";
	$strSQL = $strSQL . " INNER JOIN tbproductos P ON P.id = O.producto";
	$strSQL = $strSQL . " INNER JOIN tbmonedas M ON M.id = P.moneda";
	$strSQL = $strSQL . " WHERE P.categoria_raiz = 18";

	$strSQL = $strSQL." ORDER BY P.categoria";	

	return($conexion->Execute($strSQL));
}
//<sumary>
//Retorna todas las ofertas segun una categoria dada.
//</sumary>
function obtenerOfertasConCategoria($conexion, $idcategoria) {
	$strSQL = "SELECT P.id, O.precio AS preciooferta, P.nombre, P.precio AS precioproducto, P.URLimagen, P.URLimagen2, URLimagen3, P.descripcion, M.simbolo FROM tbofertas O ";
	$strSQL = $strSQL . " INNER JOIN tbproductos P ON P.id = O.producto";
	$strSQL = $strSQL . " INNER JOIN tbmonedas M ON M.id = P.moneda";
	$strSQL = $strSQL . " WHERE P.categoria_raiz = '$idcategoria'";

	$strSQL = $strSQL." ORDER BY O.id desc LIMIT 0,4";	

	return($conexion->Execute($strSQL));
}

//<sumary>
//Retorna todas las ofertas segun una categoria dada.
//</sumary>
function obtenerOfertasDelRubro($conexion, $idcategoria) {
	$strSQL = "SELECT P.id, O.precio AS preciooferta, P.nombre, P.precio AS precioproducto, P.URLimagen, P.URLimagen2, URLimagen3, P.descripcion, M.simbolo FROM tbofertas O ";
	$strSQL = $strSQL . " INNER JOIN tbproductos P ON P.id = O.producto";
	$strSQL = $strSQL . " INNER JOIN tbmonedas M ON M.id = P.moneda";
	$strSQL = $strSQL . " WHERE O.rubro = '$idcategoria'";

	$strSQL = $strSQL." ORDER BY O.id desc LIMIT 0,4";	

	return($conexion->Execute($strSQL));
}
//<sumary>
//Devuelve verdadero si idcategoria es una categoria padre.
//</sumary>
function esCategoriaPadre($conexion, $idcategoria) {
	$strSQL = "SELECT padre FROM tbcategorias WHERE id = '$idcategoria'";
	
	$rs = $conexion->Execute($strSQL);
	if ($rs->fields["padre"] <> 0)
		return(0); // No es categoria padre
	else
		return(1);
	
}

//<sumary>
//Devuelve verdadero si existen ofertas para dicho rubro (o categoria)
//</sumary>
function hayOfertasDelRubro($conexion, $idcategoria) {
	$strSQL = "SELECT rubro FROM tbofertas WHERE rubro = '$idcategoria'";
	
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return(1);
	else
		return(0);
		
}

//<sumary>
//Devuelve verdadero si existen ofertas para dicho rubro (o categoria)
//</sumary>
function hayOfertasDeRubrosInternos($conexion, $idcategoria) {
	$strSQL = "SELECT rubro FROM tbofertas WHERE rubro = '$idcategoria'";
	//
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return(1);
	else
		return(0);
		
}


//<sumary>
//Devuelve la descripcion de la categoria.
//</sumary>
function descripcionCategoria($conexion, $idcategoria) {
	$strSQL = "SELECT descripcion FROM tbcategorias WHERE id = '$idcategoria'";
	
	$rs = $conexion->Execute($strSQL);
	
	return($rs->fields["descripcion"]);
	
}

//<sumary>
//Retorna el valor de un parametro.
//</sumary>
function obtenerValorDeParametro($conexion, $parametro) {
	$strSQL = "SELECT valor FROM tbparametros WHERE nombre = '$parametro'";
	
	$rs = $conexion->Execute($strSQL);
	if ($rs->recordCount() > 0)
		return($rs->fields["valor"]);
	else
		return(-1);
}

//<sumary>
//Retorna noticias.
//</sumary>
function obtenerNoticias($conexion, $limite) {
	$strSQL = "SELECT * FROM tbnoticias ORDER BY fecha";
	if ($limite > 0)
		$strSQL .= " LIMIT ".$limite;
	$rsPensamientos = $conexion->Execute($strSQL);

	return $rsPensamientos;
}

//<sumary>
//Retorna noticias.
//</sumary>
function obtenerNoticia($conexion, $id) {

	$strSQL = "SELECT T.descripcion, N.id, N.imagen, N.titulo, N.noticia, N.anchoimagen, N.altoimagen, N.fecha, N.copete FROM tbnoticias AS N INNER JOIN tbtiposdenoticia AS T ON T.id = N.tipodenoticia WHERE N.id=".$id;
	//$strSQL = "SELECT * FROM tbnoticias WHERE id=".$id;
	
	$rsNoticia = $conexion->Execute($strSQL);

	return $rsNoticia;
}

function obtenerOportunidadComercial($conexion, $id) {

	$strSQL = "SELECT * FROM tboportunidadescomerciales WHERE id=".$id;
	
	$rsOportunidad = $conexion->Execute($strSQL);

	return $rsOportunidad;
}

//<sumary>
//Retorna todos los datos de una empresa.
//</sumary>
function obtenerEmpresa($conexion, $id) {
	$strSQL = "SELECT * FROM tbempresas WHERE id = ".$id;
	return($conexion->Execute($strSQL));
}

function obtenerPublicidad($conexion, $pagina, $tipo) {
	$strSQL = "SELECT * FROM tbpublicidades INNER JOIN tbtiposdepublicidad T ON T.id = tipo WHERE pagina = ".$pagina." AND tipo=".$tipo." LIMIT 1 ";
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		echo("<img src='".$rs->fields["imagen"]."' width=".$rs->fields["ancho"]."><br>");
}

function obtenerPublicidades($conexion, $pagina, $tipo, $limite) {
	$strSQL = "SELECT * FROM tbpublicidades INNER JOIN tbtiposdepublicidad T ON T.id = tipo WHERE pagina = ".$pagina." AND tipo=".$tipo." LIMIT ".$limite;
	//echo($strSQL);
	return($conexion->Execute($strSQL));
}

//<sumary>
//Registrar usuario.
//</sumary>
function registrarUsuario($usuario, $llave, $conexion) {
	
	//falta validar entrada!
	
	//	if (preg_match("/^\w{2,20}$/", $usuario, $vusuario) && preg_match("/^\w{2,20}$/", $llave, $vllave))	{

		$strSQL = "SELECT COUNT(*) AS cant_usuarios, id, nombres FROM tbclientes WHERE usuario = '".$usuario."' AND llave = '".$llave."' GROUP BY nombres, id";
		$rsUsuarios = $conexion->Execute($strSQL);
		
	//	echo($rsUsuarios->fields["cant_usuarios"]);
		if ($rsUsuarios->fields["cant_usuarios"] > 0) {	
			//echo("hay usuario");
			$_SESSION["USUARIO"] = $rsUsuarios->fields["id"];
			//$_SESSION["ROL"] = $rsUsuarios->fields["rol"];
			$_SESSION["NOMBRE"] = $rsUsuarios->fields["nombres"];

			if ($_SESSION["VENDER"] == "1") {
				$_SESSION["COMPRA"] = obtenerIdDeCompraPendiente($conexion, $_SESSION["USUARIO"]);
				if ($_SESSION["COMPRA"] == "-1") {
					agregarCompra($conexion, $_SESSION["USUARIO"]);
					$_SESSION["COMPRA"] = obtenerIdDeCompraPendiente($conexion, $_SESSION["USUARIO"]);
				}
			}
			return true;
		}
		else {
			//echo("no hay usuario");
			return false;
		}
//}
}

//<sumary>
//Unregister user
//</sumary>
function cerrarSesionUsuario($usuario, $llave, $conexion) {
	unset($_SESSION["USUARIO"]);
	//unset($_SESSION["ROL"]);
	//unset($_SESSION["NOMBRE"]);
}

function agregarUsuario($conexion, $nombre, $localidad, $domicilio, $telefono, $email, $usuario, $llave, $rol, $documento) {
	$strSQL = "SELECT * FROM tbclientes WHERE usuario = '".$usuario."' AND llave = '".$llave."'";
	$usuarios = $conexion->Execute($strSQL);
	
	if ($usuarios->recordCount() > 0) 
		return(-1);
	else {
		$strSQL = "INSERT INTO tbclientes (nombres, localidad, domicilio, telefono, email, usuario, llave, rol, habilitado, documento) VALUES (";
		$strSQL = $strSQL . "'".$nombre."',".$localidad.",'".$domicilio."','".$telefono."','".$email."','".$usuario."','".$llave."',".$rol.",'S', ".$documento.")";
		$conexion->Execute($strSQL);
		return(1);
	}

}

//<sumary>
//Genera las filas de las noticias.
//</sumary>
function generarNoticias($conexion) {
	
	$tr = 1;
	$noticias = 0;
	$intercambiar = 0;
		
	$strSQL = "SELECT T.descripcion, N.id, N.imagen, N.titulo, N.noticia, N.anchoimagen, N.altoimagen FROM tbnoticias AS N INNER JOIN tbtiposdenoticia AS T ON T.id = N.tipodenoticia ORDER BY id desc limit 2";
	
	$rsN = $conexion->Execute($strSQL);
	
	echo("<table width=97% border=0 align=center cellpadding=0 cellspacing=0>");
	
	while (!$rsN->EOF) {
		//si tiene imagen
		if ($rsN->fields["imagen"] != "") {
			if ($intercambiar == 0) {
				echo("<tr>");
				echo("<td colspan='2' valign='top'><table width='98%'  border='0' align='center' cellpadding='0' cellspacing='0'>");
				echo("<table width=98%  border=0 align=center cellpadding=0 cellspacing=0><tr>");
				echo("<td width='45%' rowspan='3'><img src='contenido/".$rsN->fields["imagen"]."' width='".$rsN->fields["anchoimagen"]."' height='".$rsN->fields["altoimagen"]."' border='0'></td)>");
				echo("<td width='53%'><span class='tipodelanoticia'>".$rsN->fields["descripcion"]."</span></td>");
				echo("<tr><td class='titulodelanoticia'><a href='detalledenoticia.php?id=".$rsN->fields["id"]."' class='titulodelanoticia'>".$rsN->fields["titulo"]."</a></td></tr>");
				echo("</tr><tr><td height='89'><span class='notadelanoticia'>".substr($rsN->fields["noticia"],0,300)."..."."<a href='detalledenoticia.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></span></td>");
				echo("</tr></table></td></tr>");	
				$intercambiar = 1;
			}
			else {
				echo("<tr>");
				echo("<td colspan='2' valign='top'><table width='98%'  border='0' align='center' cellpadding='0' cellspacing='0'>");
				echo("<table width=98%  border=0 align=center cellpadding=0 cellspacing=0><tr>");
				//echo("<td width='53%'><span class='tipodelanoticia'>".$rsN->fields["categoria"]."</span></td>");
				echo("<td width='53%'><span class='tipodelanoticia'>".$rsN->fields["descripcion"]."</span></td>");
				echo("<tr><td class='titulodelanoticia'><a href='detalledenoticia.php?id=".$rsN->fields["id"]."' class='titulodelanoticia'>".$rsN->fields["titulo"]."</a></td></tr>");
				echo("</tr><tr><td height='89'><span class='notadelanoticia'>".substr($rsN->fields["noticia"],0,300)."..."."<a href='detalledenoticia.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></span></td>");
				echo("<td width='45%' rowspan='3'><img src='contenido/".$rsN->fields["imagen"]."' width='".$rsN->fields["anchoimagen"]."' height='".$rsN->fields["altoimagen"]."' border='0'></td)>");
				echo("</tr></table></td></tr>");
				$intercambiar = 0;	
			}
			$noticias = 0;
			
		}
		else { //Si no hay imagen...
		
			if($noticias<4) {
				//si hay que generar una nueva fila
				if ($tr == 1) {
					echo("<tr><td colspan=2 height=5></td></tr>");
					echo("<tr>");
					echo("<td width='50%' valign='top'>");
					$tr = 0;
				}
				else
					echo("<td width='50%' valign=top>");
					
				echo("<table width='98%'  border='0' align='center' cellpadding='0' cellspacing='0'><tr>");
				echo("<td class='tipodelanoticia'>".$rsN->fields["descripcion"]."</td></tr>");
				echo("<tr><td class='titulodelanoticia'><a href='detalledenoticia.php?id=".$rsN->fields["id"]."' class='titulodelanoticia'>".$rsN->fields["titulo"]."</a></td></tr>");
				echo("<tr><td valign='top' class='notadelanoticia'>".substr($rsN->fields["noticia"],0,300)."..."."<a href='detalledenoticia.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></td></tr></table>");
				echo("</tr>");
				
				$noticias = $noticias + 1;
				if ($noticias % 2 == 0) {
					echo("</tr>");
					$tr = 1;
				} 
			} //fin noticia<5
			else {
				echo("<tr><td height=5></td></tr>");
				echo("<tr><td colspan=2 class=titulodenoticia>".$rsN->fields["titulo"]."<a href='detalledenoticia.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></td></tr>");
			}
			
		}
		$rsN->moveNext();
	} //fin while
	$rsN->Close();
	
	echo("</table>");
}

//<sumary>
//Genera tabla de oportunidades comerciales.
//</sumary>
function generarOportunidadesComerciales($conexion) {
	
	$tr = 1;
	$noticias = 0;
	$intercambiar = 0;
		
	$strSQL = "SELECT * FROM tboportunidadescomerciales ORDER BY id desc LIMIT 2";

	$rsN = $conexion->Execute($strSQL);
	
	echo("<table width=97% border=0 align=center cellpadding=0 cellspacing=0>");
	
	while (!$rsN->EOF) {
		//si tiene imagen
		if ($rsN->fields["URLimagen1"] != "") {
			if ($intercambiar == 0) {
				echo("<tr>");
				echo("<td colspan='2' valign='top'><table width='98%'  border='0' align='center' cellpadding='0' cellspacing='0'>");
				echo("<table width=98% border=0 align=center cellpadding=0 cellspacing=0><tr>");
				echo("<td width='45%' rowspan='3'><img src='contenido/".$rsN->fields["URLimagen1"]."' width='".$rsN->fields["anchoimagen1"]."' height='".$rsN->fields["altoimagen1"]."' border='0'></td)>");
				//echo("<td width='53%'><span class='titulodelanoticia'>".$rsN->fields["descripcion"]."</span></td>");
				
				echo("<tr><td><a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."' class='titulodelaoportunidadcomercial'>".$rsN->fields["descripcion"]."</a></td></tr>");
				
				echo("</tr><tr><td height='89'><span class='detalleoportunidadcomercial'>".substr($rsN->fields["detalle"],0,300)."..."."<a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></span></td>");
				echo("</tr></table></td></tr>");	
				$intercambiar = 1;
			}
			else {
				echo("<tr>");
				echo("<td colspan='2' valign='top'><table width='98%'  border='0' align='center' cellpadding='0' cellspacing='0'>");
				echo("<table width=98%  border=0 align=center cellpadding=0 cellspacing=0><tr>");
				//echo("<td width='53%'><span class='titulodelanoticia'>".$rsN->fields["descripcion"]."</span></td>");
					
				echo("<tr><td><a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."' class='titulodelaoportunidadcomercial'>".$rsN->fields["descripcion"]."</a></td></tr>");
				
				echo("</tr><tr><td height='89'><span class='detalleoportunidadcomercial'>".substr($rsN->fields["detalle"],0,300)."..."."<a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></span></td>");
				echo("<td width='45%' rowspan='3'><img src='contenido/".$rsN->fields["URLimagen1"]."' width='".$rsN->fields["anchoimagen1"]."' height='".$rsN->fields["altoimagen1"]."' border='0'></td)>");
				echo("</tr></table></td></tr>");
				$intercambiar = 0;
			}
			$noticias = 0;
		}
		else { //Si no hay imagen...
	
			if($noticias<4) {
				//si hay que generar una nueva fila
				if ($tr == 1) {
					echo("<tr><td colspan=2 height=5></td></tr>");
					echo("<tr>");
					echo("<td width='50%' valign='top'>");
					$tr = 0;
				}
				else
					echo("<td width='50%' valign=top>");
					
				echo("<table width='98%'  border='0' align='center' cellpadding='0' cellspacing='0'><tr>");
				//echo("<td class='tipodelanoticia'>".$rsN->fields["descripcion"]."</td></tr>");
				echo("<tr><td class='titulodelaoportunidadcomercial'><a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."' class='titulodelaoportunidadcomercial'>".$rsN->fields["descripcion"]."</a></td></tr>");
				echo("<tr><td valign='top' class='detalleoportunidadcomercial'>".substr($rsN->fields["detalle"],0,303)."..."."<a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></td></tr></table>");
				echo("</tr>");
				$noticias = $noticias + 1;
				if ($noticias % 2 == 0) {
					echo("</tr>");
					$tr = 1;
				} 
			} //fin noticia<5
			else {
				echo("<tr><td height=5></td></tr>");
				echo("<tr><td colspan=2>".$rsN->fields["titulo"]."<a href='detalleoportunidadcomercial.php?id=".$rsN->fields["id"]."'><img src='Libreria/Imagenes/carpeta.gif' border=0></a></td></tr>");
			}
			
		} // FIN --> Si no hay imagen...
		$rsN->moveNext();
	} // fin while
	$rsN->Close();
	
	echo("</table>");
	
	return($conexion->Execute($strSQL));
}


//<sumary>
//Retorna los productos mas visitados segun el parametro cantidad.
//</sumary>
function obtenerProductosMasVisitados($conexion) {
//	$strSQL = "SELECT id, nombre, precio FROM tbproductos ORDER BY visitas desc LIMIT 0,10";
	$strSQL = "SELECT P.id, P.descripcion, P.precio, M.simbolo FROM tbproductos P INNER JOIN tbmonedas M ON P.moneda = M.id ORDER BY visitas desc LIMIT 0,10";
	return($conexion->Execute($strSQL));
}

//<sumary>
//Retorna los productos mas visitados segun el parametro cantidad.
//</sumary>
function obtenerLibrosMasVisitados($conexion) {
//	$strSQL = "SELECT id, nombre, precio FROM tbproductos ORDER BY visitas desc LIMIT 0,10";
	$strSQL = "SELECT P.id, P.descripcion, P.precio, M.simbolo FROM tbproductos P INNER JOIN tbmonedas M ON P.moneda = M.id WHERE P.categoria = 18 ORDER BY visitas desc LIMIT 0,10";
	return($conexion->Execute($strSQL));
}

//<sumary>
//Retorna todos los productos de una compra.
//</sumary>
function obtenerProductosDeLaCompra($conexion, $id) {
	$strSQL = "SELECT P.id, P.descripcion, M.simbolo, C.cantidad, P.precio, total FROM tbproductos P INNER JOIN tbproductosdelacompra C ON P.id = C.producto INNER JOIN tbmonedas M ON P.moneda = M.id WHERE compra = $id";
	return($conexion->Execute($strSQL));
}

//<sumary>
//Genera un combo en funcion de la tabla pasada.
//</sumary>
function generarCombo($conexion, $tabla, $nombre, $id, $vacio, $clase) {
	echo("<select name=".$nombre." class=".$clase.">");
	
	$rs = $conexion->Execute("SELECT * FROM ".$tabla." ORDER BY descripcion");
	
	if ($vacio > 0) 
		echo("<option selected value='-1'>TODOS</option>");
	
	while (!$rs->EOF) {
		if ($id > 0 && $id == $rs->fields["id"]) 
			echo("<option selected value='".$rs->fields["id"]."'>".$rs->fields["descripcion"]."</option>");
		else
			echo("<option value='".$rs->fields["id"]."'>".$rs->fields["descripcion"]."</option>");
		$rs->moveNext();
	}
	echo("</select>");
}


//<sumary>
//Genera un combo en funcion de la tabla pasada y ademas filtra los rubros padre.
//</sumary>
function generarComboRubros($conexion, $tabla, $nombre, $id, $vacio, $clase) {
	echo("<select name=".$nombre." class=".$clase.">");
	
	$rs = $conexion->Execute("SELECT * FROM ".$tabla." WHERE padre = 0 ORDER BY descripcion");
	
	if ($vacio > 0) 
		echo("<option selected value='-1'>TODOS</option>");
	
	while (!$rs->EOF) {
		if ($id > 0 && $id == $rs->fields["id"]) 
			echo("<option selected value='".$rs->fields["id"]."'>".$rs->fields["descripcion"]."</option>");
		else
			echo("<option value='".$rs->fields["id"]."'>".$rs->fields["descripcion"]."</option>");
		$rs->moveNext();
	}
	echo("</select>");
}



//<sumary>
//Retorna la cantidad de productos en el cesto de compras.
//</sumary>
function obtenerCantidadDeProductosDeCompra($conexion, $usuario) {
	$strSQL = "SELECT SUM(cantidad) AS cantidad FROM tbcompras C INNER JOIN tbproductosdelacompra PC ON PC.compra = C.id WHERE C.usuario = $usuario AND estado = 1";
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return($rs->fields["cantidad"]);
	else
		return(0);
}

//<sumary>
//Retorna el monto actual de la compra vigente.
//</sumary>
function obtenerTotalDeLaCompra($conexion, $usuario) {
	$strSQL = "SELECT SUM(total) AS total FROM tbcompras C INNER JOIN tbproductosdelacompra PC ON PC.compra = C.id WHERE C.usuario = $usuario AND estado = 1";
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return($rs->fields["total"]);
	else
		return(0);
}

function crearTablaDeResultados($conexion, $query) {
	$fila = 0;
	$maxpage = 1;
	$pagina = 1;
	$offset = 0;
	$registrosporpagina = 15;
	$registros = 0;
	
	if (isset($_GET["page"]))
		$pagina=$_GET["page"];
	
	$rs = $conexion->Execute($query);
	$registros = $rs->recordCount();
	
	$maxpage = ceil($registros/$registrosporpagina);
	$offset = ($pagina - 1) * $registrosporpagina;
	
	$query = $query . " LIMIT ".$offset.", ".$registrosporpagina;
	$rs = $conexion->Execute($query);	
	
	if(!isset($nav)){
		$nav = "";
	}
	
	for($page = 1; $page <= $maxpage; $page++) {
   	if ($page == $pagina) {
       	$nav .= " $page "; // no need to create a link to current page
   	}
    else  {
     // $nav .= " <a href=\"$self?page=$page\">$page</a> ";
        if(isset($_REQUEST["idcategoria"]))
			$nav .= " <a href=\"?page=$page&idcategoria=".$_REQUEST["idcategoria"]."\" style='color: #333333'>$page</a> ";
		else
			$nav .= " <a href=\"?page=$page\">$page</a> ";	
     } 
    }

	echo("<table class=tabladeresultado width='100%' cellpadding=1 cellspacing=0>");
	echo("<tr><td height=25 colspan=4 class=resultadodelabusqueda align=right>Se encontraron <strong>$registros</strong> productos. || Páginas: $nav</td><tr>");

	echo("<tr><td width=15% class=titulodelistado>&nbsp;</td><td width=60% class=titulodelistado><a href='resultados.php?orden=nombre' class=titulodelistado>Producto</a></td><td width=25% class=titulodelistado><a href='resultados.php?orden=precio' class=titulodelistado>Precio</a></td>");
	echo("</tr>");
	
	while(!$rs->EOF) {
	
		if (($fila % 2) == 0)
			$clase = "filaderesultado";
		else
			$clase = "filaderesultadoalternado";
	
		echo("<tr class='$clase'>");

		if ($rs->fields["URLimagen"] != "") 
			echo("<td width=15%><img border=1 src='contenido/".$rs->fields["URLimagen"]."' width=50 height=50></td>");
		else
			echo("<td width=15%>&nbsp;</td>");
			
		echo("<td width=50%>");
		
		// Si el producto es una oferta se agrega el icono de oferta.
		if ($rs->fields["preciooferta"] != "")
			echo("<a href=ofertas.php><img src='Libreria/Imagenes/iconos/ico-oferta16x16.gif' border=0>&nbsp;</a>");
		
		echo("<a href='detalle.php?id=".$rs->fields["id"]."' class='enlacedelproducto'>".$rs->fields["descripcion"]."</a><br><div class=categoriasdeproductos>".$rs->fields["categoria"]."</br></td>");
		
		echo("<td width=25% class=preciodelproducto>".$rs->fields["simbolo"]." ".$rs->fields["precio"]."</td>");
		//echo("<td width=10%><a href='".$_SESSION["SERVIDOR"]."contenido/cuenta.php?id=".$rs->fields["id"]."'><img border=0 src='../Libreria/Imagenes/iconos/ico-comprar.gif'></a></td>");
		echo("<td width=10%><a href='detalle.php?id=".$rs->fields["id"]."'><img border=0 src='Libreria/Imagenes/iconos/ico-comprar.gif'></a></td>");
		
		echo("</tr>");
		
		$fila++;

		$rs->moveNext();
	}
	echo("<tr><td height=25 colspan=4 align=center class=resultadodelabusqueda>Páginas: $nav</td><tr>");
	echo("</table>");
}

/*******/


function crearTablaDeResultados_libros($conexion, $query) {
	$fila = 0;
	$maxpage = 1;
	$pagina = 1;
	$offset = 0;
	$registrosporpagina = 15;
	$registros = 0;
	
	if (isset($_GET["page"]))
		$pagina=$_GET["page"];
	
	$rs = $conexion->Execute($query);
	$registros = $rs->recordCount();
	
	$maxpage = ceil($registros/$registrosporpagina);
	$offset = ($pagina - 1) * $registrosporpagina;
	
	$query = $query . " LIMIT ".$offset.", ".$registrosporpagina;
	$rs = $conexion->Execute($query);	
	
	if(!isset($nav)){
		$nav = "";
	}
	$nav_10 = "";
	
	if(!isset($_SESSION['cont_siguiente'])){
		$_SESSION['cont_siguiente'] = 1;
	}
	
	if(isset($_REQUEST['inc_siguiente'])){
		if($_REQUEST['inc_siguiente'] == "si"){
			$_SESSION['cont_siguiente'] = $_SESSION['cont_siguiente'] + 1;
		}

		if($_REQUEST['inc_siguiente'] == "no"){
			$_SESSION['cont_siguiente'] = $_SESSION['cont_siguiente'] - 1;
		}
	}
	
	for($page = 1; $page <= $maxpage; $page++) {
   	if ($page == $pagina) {
		 // if($page <= 10)
		 if(($page >= $_SESSION['cont_siguiente']) && ($page <= $_SESSION['cont_siguiente']*10))
		  		{ $nav_10 .= " $page "; }
       	$nav .= " $page "; // no need to create a link to current page
   	}
    else  {
     // $nav .= " <a href=\"$self?page=$page\">$page</a> ";
	 if(($page >= $_SESSION['cont_siguiente']*10-9) && ($page <= $_SESSION['cont_siguiente']*10))
		{	if(isset($_REQUEST['CampoOrden'])) 
	   		$nav_10 .= " <a href=\"?page=$page&tabla=".$_GET["tabla"]."\" style='color: #333333'>$page</a> ";
	   	else
	   		$nav_10 .= " <a href=\"?page=$page&cont_siguiente=".$_SESSION['cont_siguiente']."\" style='color: #333333'>$page</a> "; 
		}
			 
        if(isset($_REQUEST["idcategoria"]))
			$nav .= " <a href=\"?page=$page&idcategoria=".$_REQUEST["idcategoria"]."\" style='color: #333333'>$page</a> ";
		else
			$nav .= " <a href=\"?page=$page\">$page</a> ";	
     } 
    }


	echo("<table class=tabladeresultado width='100%' cellpadding=1 cellspacing=0>");
	if($maxpage<=10){	
		echo("<tr>");
		echo("<td class='resultadodelabusqueda' align=left>Pagina: &nbsp;".$nav."</td>");
		
		echo("<td class='resultadodelabusqueda' align=right>Visualizando del registro ".$min." al ".$topeMax." de un total de ".$registros." registros.</td>");
		echo("</tr>");
	}
	else{
		echo("<tr>");
				
		$pagin_ant = (($_SESSION['cont_siguiente'] * 10) + 1) - 20;
		$pagin =  ($_SESSION['cont_siguiente'] * 10) + 1;  // ($pagin == $pagin_sig)
		
		$Maximus = $pagina*15;
		$Minus = $Maximus -14;
		
		if (isset($_SESSION["nombre"])){
			echo("<tr><td colspan=3 class='resultadosdelabusqueda' align=left>Resultados de la búsqueda de:<b><em> ".$_SESSION["nombre"]."</em></b>.</td></tr>");
		}
		echo("<tr><td colspan=3 class='resultadodelabusqueda' align=left>Visualizando del registro ".$Minus." al ".$Maximus." de un total de ".$registros." registros.</td></tr>");
		echo("<tr><td colspan=2 class='resultadodelabusqueda' align=left>Página: &nbsp;".$nav_10."</td></tr>");
		
			
		if($_SESSION['cont_siguiente'] != 1){
			//echo("<td class='titulodelformulariodelistado' align=left><a href=\"?contador_siguiente=$_REQUEST['contador_siguiente']&page=$page&tabla=".$_GET["tabla"]."\" style='color: #333333'>Siguiente --></a></td>");
			echo("<td class='resultadodelabusqueda' align=left><a href=\"?inc_siguiente=no&page=$pagin_ant"."\" style='color: #333333'><-Ant</a></td>");
			echo("<td class='resultadodelabusqueda' align=left><a href=\"?inc_siguiente=si&page=$pagin"."\" style='color: #333333'>Sig-></a></td>");
		}
		else
		{
			//echo("<td class='titulodelformulariodelistado' align=left><a href=\"?contador_siguiente=2&page=$page&tabla=".$_GET["tabla"]."\" style='color: #333333'>Siguiente --></a></td>");
		//	echo("<td class='titulodelformulariodelistado' align=left><a href=\"?inc_siguiente=no&contador_siguiente=$contador_siguiente&page=$pagin&tabla=".$_GET["tabla"]."\" style='color: #333333'><-Ant</a></td>");
			echo("<td colspan=2 class='resultadodelabusqueda' align=left><a href=\"?inc_siguiente=si&page=$pagin"."\" style='color: #333333'>Sig-></a></td>");
			echo("</tr>");
		}

	}	
		
	//echo("<table class=tabladeresultado width='100%' cellpadding=1 cellspacing=0>");
	//echo("<tr><td height=25 colspan=4 class=resultadodelabusqueda align=right>Se encontraron <strong>$registros</strong> productos. || Páginas: $nav</td><tr>");

//	echo("<tr><td width=15% class=titulodelistado>&nbsp;</td><td width=60% class=titulodelistado><a href='resultados.php?orden=nombre' class=titulodelistado>Producto</a></td><td width=25% class=titulodelistado><a href='resultados.php?orden=precio' class=titulodelistado>Precio</a></td>");
	echo("<tr><td width=15% class=titulodelistado>&nbsp;</td><td width=60% class=titulodelistado><a class=titulodelistado>Producto</td><td width=25% class=titulodelistado><a class=titulodelistado>Precio</td>");
	echo("</tr>");
	
	while(!$rs->EOF) {
	
		if (($fila % 2) == 0)
			$clase = "filaderesultado";
//			$clase = "filaderesultado_libros";
		else
			$clase = "filaderesultadoalternado";	
//			$clase = "filaderesultadoalternado_libros";	
		echo("<tr class='$clase'>");

		if ($rs->fields["URLimagen"] != "") 
			echo("<td width=15%><img border=1 src='contenido/".$rs->fields["URLimagen"]."' width=50 height=50></td>");
		else
			echo("<td width=15%>&nbsp;</td>");
			
		echo("<td width=50%>");
		
		// Si el producto es una oferta se agrega el icono de oferta.
		if ($rs->fields["preciooferta"] != "")
			echo("<a href=ofertas.php><img src='Libreria/Imagenes/iconos/ico-oferta16x16.gif' border=0>&nbsp;</a>");
		
		echo("<a href='detalle.php?id=".$rs->fields["id"]."' class='enlacedelproducto'>".$rs->fields["descripcion"]."</a><br><div class=categoriasdeproductos>".$rs->fields["categoria"]."</br></td>");
		
	//	if ($rs->fields["precio"] != "") 
			echo("<td width=25% class=preciodelproducto>".$rs->fields["simbolo"]." ".$rs->fields["precio"]."</td>");
	//		echo("<td width=25% class=preciodelproducto>".$rs->fields["precio"]."</td>");
	//	else
	//		echo("<td width=25% class=preciodelproducto>".$rs->fields["precio"]."</td>");
//		echo("<td width=10%><a href='".$_SESSION["SERVIDOR"]."contenido/cuenta.php?id=".$rs->fields["id"]."'><img border=0 src='../Libreria/Imagenes/iconos/ico-comprar.gif'></a></td>");
		echo("<td width=10%><a href='detalle.php?id=".$rs->fields["id"]."'><img border=0 src='Libreria/Imagenes/iconos/ico-comprar.gif'></a></td>");
		
		echo("</tr>");
		
		$fila++;

		$rs->moveNext();
	}
	//echo("<tr><td height=25 colspan=4 align=center class=resultadodelabusqueda>Páginas: $nav</td><tr>");
	echo("</table>");
}


/*
function crearTablaDeResultados_libros($conexion, $query) {
	$fila = 0;
	$maxpage = 1;
	$pagina = 1;
	$offset = 0;
	$registrosporpagina = 15;
	$registros = 0;
	
	if (isset($_GET["page"]))
		$pagina=$_GET["page"];
	
	$rs = $conexion->Execute($query);
	$registros = $rs->recordCount();
	
	$maxpage = ceil($registros/$registrosporpagina);
	$offset = ($pagina - 1) * $registrosporpagina;
	
	$query = $query . " LIMIT ".$offset.", ".$registrosporpagina;
	$rs = $conexion->Execute($query);	
	
	if(!isset($nav)){
		$nav = "";
	}
	
	for($page = 1; $page <= $maxpage; $page++) {
   	if ($page == $pagina) {
       	$nav .= " $page "; // no need to create a link to current page
   	}
    else  {
     // $nav .= " <a href=\"$self?page=$page\">$page</a> ";
        if(isset($_REQUEST["idcategoria"]))
			$nav .= " <a href=\"?page=$page&idcategoria=".$_REQUEST["idcategoria"]."\" style='color: #333333'>$page</a> ";
		else
			$nav .= " <a href=\"?page=$page\">$page</a> ";	
     } 
    }

	echo("<table class=tabladeresultado width='100%' cellpadding=1 cellspacing=0>");
	echo("<tr><td height=25 colspan=4 class=resultadodelabusqueda align=right>Se encontraron <strong>$registros</strong> productos. || Páginas: $nav</td><tr>");

	echo("<tr><td width=15% class=titulodelistado>&nbsp;</td><td width=60% class=titulodelistado><a href='resultados.php?orden=nombre' class=titulodelistado>Producto</a></td><td width=25% class=titulodelistado><a href='resultados.php?orden=precio' class=titulodelistado>Precio</a></td>");
	echo("</tr>");
	
	while(!$rs->EOF) {
	
		if (($fila % 2) == 0)
			$clase = "filaderesultado_libros";
		else
			$clase = "filaderesultadoalternado_libros";	
		echo("<tr class='$clase'>");

		if ($rs->fields["URLimagen"] != "") 
			echo("<td width=15%><img border=1 src='contenido/".$rs->fields["URLimagen"]."' width=50 height=50></td>");
		else
			echo("<td width=15%>&nbsp;</td>");
			
		echo("<td width=50%>");
		
		// Si el producto es una oferta se agrega el icono de oferta.
		if ($rs->fields["preciooferta"] != "")
			echo("<a href=ofertas.php><img src='Libreria/Imagenes/iconos/ico-oferta16x16.gif' border=0>&nbsp;</a>");
		
		echo("<a href='detalle_libros.php?id=".$rs->fields["id"]."' class='enlacedelproducto'>".$rs->fields["descripcion"]."</a><br><div class=categoriasdeproductos>".$rs->fields["categoria"]."</br></td>");
		
		echo("<td width=25% class=preciodelproducto>".$rs->fields["simbolo"]." ".$rs->fields["precio"]."</td>");
		//echo("<td width=10%><a href='".$_SESSION["SERVIDOR"]."contenido/cuenta.php?id=".$rs->fields["id"]."'><img border=0 src='../Libreria/Imagenes/iconos/ico-comprar.gif'></a></td>");
		echo("<td width=10%><a href='detalle_libros.php?id=".$rs->fields["id"]."'><img border=0 src='Libreria/Imagenes/iconos/ico-comprar.gif'></a></td>");
		
		echo("</tr>");
		
		$fila++;

		$rs->moveNext();
	}
	echo("<tr><td height=25 colspan=4 align=center class=resultadodelabusqueda>Páginas: $nav</td><tr>");
	echo("</table>");
}*/

function obtenerIdDeCompraPendiente($conexion, $usuario) {
	$strSQL = "SELECT MAX(id) AS id FROM tbcompras WHERE usuario = $usuario AND estado = 1";
	$rs = $conexion->Execute($strSQL);
	
	if (($rs->recordCount() > 0) && ($rs->fields["id"] != "")) {
		return($rs->fields["id"]);
		}
	else
		return(-1);
}

function obtenerCantidadDelProductoDeLaCompra($conexion, $compra, $producto) {
	$strSQL = "SELECT cantidad FROM tbproductosdelacompra WHERE compra = $compra AND producto = $producto";

	$rs = $conexion->Execute($strSQL);
	
	if (($rs->recordCount() > 0) && ($rs->fields["cantidad"] != ""))
		return($rs->fields["cantidad"]);
	else
		return(-1);
}

function obtenerPrecioDelProducto($conexion, $p) {
	
	$strSQL_oferta = "SELECT precio FROM tbofertas WHERE producto = $p";
	$rs_oferta = $conexion->Execute($strSQL_oferta);
	
	if ($rs_oferta->fields["precio"] != "")
		return($rs_oferta->fields["precio"]);
	else{
		$strSQL_producto = "SELECT precio FROM tbproductos WHERE id = $p";
		$rs_producto = $conexion->Execute($strSQL_producto);
		
		return($rs_producto->fields["precio"]);
	}
		
}

function agregarCompra($conexion, $usuario) {
	$strSQL = "INSERT INTO tbcompras (usuario, fecha, estado, fechadecompra) VALUES ($usuario, CURRENT_DATE, 1, null)";
	$conexion->Execute($strSQL);
}

function agregarProductoDeLaCompra($conexion, $compra, $producto, $cantidad) {
	
	$pcantidad = $cantidad;
	$pproducto = $producto;

	$cantidadactual = obtenerCantidadDelProductoDeLaCompra($conexion, $compra, $producto);
	$precio = obtenerPrecioDelProducto($conexion, $producto);
	$total = $cantidad * $precio;
		
	if ($cantidadactual > 0)
		$strSQL = "UPDATE tbproductosdelacompra SET cantidad = cantidad + $cantidad WHERE compra = $compra AND producto = $producto";
	else
		$strSQL = "INSERT INTO tbproductosdelacompra (compra, producto, cantidad, descuento, total, precio, iva) VALUES ($compra, $producto, $cantidad, 0, $total, $precio, 0)";

	$conexion->Execute($strSQL);	
}

function eliminarProductoDeLaCompra($conexion, $compra, $producto) {
	
	if ($producto > 0)
		$strSQL = "DELETE FROM tbproductosdelacompra WHERE compra = $compra AND producto = $producto";
		
	$conexion->Execute($strSQL);	
}

//<sumary>
//Retorna un producto por el Id.
//</sumary>
function obtenerProductoPorId($conexion, $id) {
	//echo(vProductos()." WHERE P.id = $id");
	return($conexion->Execute(vProductos()." WHERE P.id = $id"));
}

//<sumary>
//Retorna un producto por el Id.
//</sumary>
function obtenerProductosSimilares($conexion, $categoria, $id) {
	//echo(vProductos()." WHERE P.id = $id");
	return($conexion->Execute(vProductos()." WHERE P.id <> $id AND P.categoria = $categoria LIMIT 0,10"));
}

//<sumary>
//Retorna un producto por el Id.
//</sumary>
function obtenerProductosSimilares_libros($conexion, $categoria, $id) {
	//echo(vProductos()." WHERE P.id = $id");
	return($conexion->Execute(vProductos()." WHERE P.id <> $id AND P.categoria = 18 AND P.categoria = $categoria LIMIT 0,10"));
}


//<sumary>
//Retorna los adicionales de compra que se aplican al producto.
//</sumary>
function obtenerAdicionalesDeCompraPorProductos($conexion) {
	//echo(vProductos()." WHERE P.id = $id");
	return($conexion->Execute("SELECT * FROM tbadicionales WHERE seaplicaalproducto = 'S'"));
}

//<sumary>
//Retorna los adicionales de compra que se aplican al total de la compra.
//</sumary>
function obtenerAdicionalesDeCompraPorTotal($conexion) {
	//echo(vProductos()." WHERE P.id = $id");
	return($conexion->Execute("SELECT * FROM tbadicionales WHERE seaplicaaltotal = 'S'"));
}

//<sumary>
//Retorna las opciones de menu dinamico.
//</sumary>
function obtenerOpcionesDeMenuDinamico($conexion) {
	//echo(vProductos()." WHERE P.id = $id");
	return($conexion->Execute("SELECT * FROM tbopciones ORDER BY orden asc"));
}

function obtenerFotosDeslizantes($conexion) {
	$strSQL = "SELECT * FROM tbfotosdeslizantes ORDER BY ordenaparicion asc LIMIT 0,3";
	return($conexion->Execute($strSQL));
}

function obtenerConfigFotosDeslizantes($conexion) {
	$strSQL = "SELECT * FROM tbconfigurarfotosdeslizantes LIMIT 0,1";
	return($conexion->Execute($strSQL));
}

//<sumary>
//Incrementa en uno la cantidad de visitas a un producto.
//</sumary>
function incrementarVisitasAlProducto($conexion, $id) {
	
	$strSQL = "UPDATE tbproductos SET visitas = visitas + 1 WHERE id = $id";

	$conexion->Execute($strSQL);
}

//<sumary>
//Incrementa en uno la cantidad de visitas a la página.
//</sumary>
function incrementarVisitas($conexion) {
	
	$strSQL = "UPDATE tbvisitas SET cantidad = cantidad + 1";

	$conexion->Execute($strSQL);
}

//<sumary>
//Devuelve la cantidad de visitas a la página.
//</sumary>
function mostrarCantidadDeVisitas($conexion) {

	$strSQL = "SELECT cantidad FROM tbvisitas WHERE id=1";
	
	return($conexion->Execute($strSQL));
}

//<sumary>
//Devuelve la cantidad de PESOS ($) a la que equivale un DOLAR (u$s).
//</sumary>
function CantidadDePesosPorDolar($conexion) {

	$archivo="http://www.midolar.com.ar/dolar.xml";
	
	//abrimos el archivo en lectura
	$fp = fopen($archivo,'r');
	
	$texto = fread($fp, 350);

	$valor=$texto[299].".".$texto[301].$texto[302];
	
	return($valor);
}

function ObtenerCategoriasInternas($conexion, $idcategoria) {

	$strSQL = "SELECT * FROM tbcategorias WHERE padre=".$idcategoria;
	
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return $rs;
	else
		return(0);
}

function ObtenerCategoriasInternas_2($conexion, $idcategoria) {

	$strSQL = "SELECT * FROM tbcategorias WHERE padre=".$idcategoria;
	
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return $rs;
	else
		return(0);
}

function ObtenerProductosSubrubros($conexion, $idcategoria) {
	
	$strSQL = "SELECT DISTINCT(P.categoria) AS categoria, P.id, P.descripcion, P.precio AS precioproducto, P.URLimagen, P.URLimagen2, URLimagen3, P.descripcion, M.simbolo FROM tbproductos P";
	$strSQL = $strSQL . " INNER JOIN tbmonedas M ON M.id = P.moneda";
	$strSQL = $strSQL . " INNER JOIN tbcategorias C ON C.id = P.categoria";
	
	$strSQL = $strSQL." WHERE C.padre=".$idcategoria;	
	$strSQL = $strSQL." GROUP BY categoria";

//	$strSQL = "SELECT * FROM tbcategorias WHERE padre=".$idcategoria;
	
	$rs = $conexion->Execute($strSQL);
	
	if ($rs->recordCount() > 0)
		return $rs;
	else
		return(0);
}

?>