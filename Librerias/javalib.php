<?php
if(!defined('JAVALIB'))
{
define('JAVALIB', 102);
?>
<script type="text/javascript" src="/Rectificadora/Js/num2text.js"></script>
<link rel="stylesheet" href="/Rectificadora/Librerias/dhtmlmodal/windowfiles/dhtmlwindow.css" type="text/css" />
<script type="text/javascript" src="/Rectificadora/Librerias/dhtmlmodal/windowfiles/dhtmlwindow.js"></script>
<link rel="stylesheet" href="/Rectificadora/Librerias/dhtmlmodal/modalfiles/modal.css" type="text/css" />
<script type="text/javascript" src="/Rectificadora/Librerias/dhtmlmodal/modalfiles/modal.js"></script>


<!-####################################################################################-->
<!-################################### UTILIDADES #####################################-->
<!-####################################################################################-->

<!-##################### Carga la version del GR completa ###################-->
<script type="text/javascript">
function GRVersionCompleta()
	{
	window.parent.location.href = 'index.php';
	return true;
	}
</script>

<!-##################### Carga la version del GR para el taller ###################-->
<script type="text/javascript">
function GRVersionTaller()
	{
	window.open('Gestion/taller.php');
	return true;
	}
</script>

<!-##################### Reemplaza el caracter c_out por c_in en la cadena str ###################-->
<script type="text/javascript">
function Str_Replace(c_out, c_in, str)
	{
	var cadena=str.split(c_out);	// Lo divido en el caracter que saco (c_out)
	cadena=cadena.join(c_in);		// Reemplazo con el caracter nuevo (c_in)
	return(cadena);
	}
</script>

<!-##################### Actualiza Total de Item ###################-->
<script type="text/javascript">
function ActualizarTotalItem(rub, num, add)
	{
	var rec=0;
	// Busco el valor de cantidad, lo multipiclo por el importe unitario y pongo el total en pagina
	if(rub==0)	var id_cant = 'cant_mob-'+rub+'-'+num+'-'+add;		// MOB
	else			var id_cant = 'cant_mat-'+rub+'-'+num+'-'+add;		// MAT
	var cant = document.getElementById(id_cant).value;
	if(!isNaN(cant))
		{
		if(cant < 0)
			{
			alert("La cantidad debe ser positiva");
			document.getElementById(id_cant).focus();
			}
		else	rec=1;
		}
	else
		{
		alert("Ingrese un Valor Numerico");
		document.getElementById(id_cant).focus();
		}
	
	// Actualizo los valores en la clase
	if(rec){
		parent.myframe.location = '../PopUps/upitem.php?hacer=0&rub='+rub+'&num='+num+'&add='+add+'&cant='+cant;
	}
	return true;
	}
</script>

<!-##################### Actualiza Todos los Totales ###################-->
<script type="text/javascript">
function ActualizarTotales(sub_mob, sub_mat, subtot, sub_desc, total)
	{
	parent.document.getElementById("sub_mob").innerHTML = sub_mob;			// Asigno el sub-total de MOB al span de la pagina
	parent.document.getElementById("sub_mat").innerHTML = sub_mat;			// Asigno el sub-total de MAT al span de la pagina
	if(sub_desc!=0)
		{
		parent.document.getElementById("subtotal").innerHTML = subtot;			// Asigno el sub-total al span de la pagina
		parent.document.getElementById("sub_desc").innerHTML = sub_desc;		// Asigno el sub_descuento al span de la pagina
		}
	parent.document.getElementById("total").innerHTML = total; 				// Asigno el total al span de la pagina
	return true;
	}
</script>

<!-##################### Apertura de ventana para edicion de Item ###################-->
<script type="text/javascript">
function EditItem(iten, num, add, cant, desc, cod, imp)
	{
	cod=Str_Replace('+', '|', cod);			// Reemplazo signos '+' por '|' ya que el mas sirve para concatenar en javascript
	desc=Str_Replace('+', '|', desc);
	
	ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/edititem.php?item='+iten+'&cant='+cant+'&desc='+desc+'&cod='+cod+'&imp='+imp, 'Edicion de Item', 'width=500px, height=200px, center=1, resize=0, scrolling=1')

	ventana.onclose=function()									// Codigo que se ejecuta al cerrar la ventana
		{
		var cant=this.contentDoc.getElementById("cantidad").value		// Tomo el valor de la cantidad
		var desc=this.contentDoc.getElementById("descripcion").value	// Tomo el valor de descripcion
		var imp=this.contentDoc.getElementById("importe").value			// Tomo el valor de importe
		
		desc=Str_Replace('+', '|', desc);		// Reemplazo signos '+' por '|' ya que el mas sirve para concatenar en javascript
		var dir = '../PopUps/edititem.php?guardar&item='+iten+'&num='+num+'&add='+add+'&cant='+cant+'&desc='+desc+'&imp='+imp;

		if(iten==0)		// MOB
			{
			var id_cant = 'cant_mob-'+iten+'-'+num+'-'+add;
			var id_desc = 'desc_mob-'+iten+'-'+num+'-'+add;
			var id_imp = 'imp_mob-'+iten+'-'+num+'-'+add;
			var id_tot = 'tot_mob-'+iten+'-'+num+'-'+add;
			}
		else				// MAT
			{
			var id_cant = 'cant_mat-'+iten+'-'+num+'-'+add;
			var id_desc = 'desc_mat-'+iten+'-'+num+'-'+add;
			var id_imp = 'imp_mat-'+iten+'-'+num+'-'+add;
			var id_tot = 'tot_mat-'+iten+'-'+num+'-'+add;
			
			var cod=this.contentDoc.getElementById("codigo").value	// Tomo el valor del codigo
			var id_cod = 'cod_mat-'+iten+'-'+num+'-'+add;
			document.getElementById(id_cod).innerHTML = cod;
			cod=Str_Replace('+', '|', cod);			// Reemplazo signos '+' por '|' ya que el mas sirve para concatenar en javascript
			dir = dir+'&MAT&cod='+cod;
			}

		document.getElementById(id_cant).value = cant;
		document.getElementById(id_desc).innerHTML = desc;
		document.getElementById(id_imp).innerHTML = imp;
		if(cant!=0 && imp!=0)	document.getElementById(id_tot).innerHTML = cant*imp;
		else							document.getElementById(id_tot).innerHTML = '';

		parent.myframe.location = dir;
		return true;
		}
	}
</script>

<!-##################### Edicion de un Item del Encabezado ###################-->
<script type="text/javascript">
function EditarItemEncabezado(idnum, idtext)
	{
	var texto = document.getElementById(idtext).value;
	parent.myframe.location = '../PopUps/edititem.php?enc&item='+idnum+'&desc='+texto;
	return true;
	}
</script>

<!-####################################################################################-->
<!-########################### ACCIONES DE BOTONES DECISION ###########################-->
<!-####################################################################################-->

<!-##################### Se ejecuta cuando se presiona el boton "Ver OT / Crear OT" ###################-->
<script type="text/javascript">
function PRECLI_ProcesarOT(pres, ot)
	{
	if(ot==0)
		{
		ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/prioridad.php', 'Prioridad de OT', 'width=400px, height=120px, center=1, resize=0, scrolling=1');
		ventana.onclose=function()
			{
			var prioridad = this.contentDoc.getElementById("prioridad").value;
			window.parent.location.href = '../OT/buscar.php?buscado&nro='+ot+'&pres='+pres+'&prioridad='+prioridad;
			return true;
			}
		}
	else
		window.parent.location.href = '../OT/buscar.php?buscado&nro='+ot+'&pres='+pres;
	return true;
	}
</script>

<!-##################### Se ejecuta cuando se presiona el botob "Editar" ###################-->
<script type="text/javascript">
function EditarPresOT(tipo, pres, ot)
	{
	if(tipo==2)	window.parent.location.href = 'editar.php?nro='+ot;
	else			window.parent.location.href = 'editar.php?nro='+pres;
	return true;
	}
</script>

<!-##################### Se ejecuta cuando se presiona el boton "Imprimir" ###################-->
<script type="text/javascript">
function Imprimir()
	{
	ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/impdetalle.php', 'Detalle de Impresion', 'width=500px, height=180px, center=1, resize=0, scrolling=1');

	ventana.onclose=function() {
		var nivel = this.contentDoc.getElementById("nivel").value;
		window.open('../imprimir.php?nivel='+nivel);
		return true;
		};
	}
</script>

<!-##################### Se ejecuta cuando se presiona el boton "Imprimir" ###################-->
<script type="text/javascript">
function ReciboPrint(id_recibo)  {
   window.open('../imprimir.php?nivel=0&id_recibo='+id_recibo);
   return true;
}
function OT_ImprimirTodosLosRecibos(num_ot){
   window.open('../imprimir.php?nivel=0&num_ot='+num_ot);
   return true;
}
</script>

<!-##################### Se ejecuta cuando se presiona el boton "Editar" ###################-->
<script type="text/javascript">
function ReciboEdit(id_recibo)  {

	ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/reciboedit.php?id_recibo='+id_recibo, 'Edición de Recibo OT', 'width=800px, height=600px, center=1, resize=0, scrolling=1');

	ventana.onclose = function() {
      
      ReciboSave(1, this.contentDoc, id_recibo);
      
		return true;
   };
   
}
</script>

<!-##################### Se ejecuta cuando se presiona el boton "Eliminar" ###################-->
<script type="text/javascript">
function ReciboDelete(id_recibo, nro_recibo)  {
   
   var returnval = confirm("Seguro que desea eliminar el Recibo "+nro_recibo+"?");
   
   if(returnval) {
      parent.myframe.location = '../OT/recibo.php?opt=3&id_recibo='+id_recibo;
   }
   
   return true;
}
</script>

<!-##################### Se ejecuta cuando se presiona el boton "Agregar Recibo" en OT ###################-->
<script type="text/javascript">
function ReciboAdd(ot) {

	ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/reciboedit.php', 'Edición de Recibo OT', 'width=800px, height=600px, center=1, resize=0, scrolling=1');

	ventana.onclose = function() {
      
      ReciboSave(0, this.contentDoc, ot);
		return true;
   };
}
</script>

<!-##################### Se ejecuta cuando se presiona el boton "Agregar Recibo" en OT ###################-->
<script type="text/javascript">
function ReciboSave(caso, content, id) {
   
   var i, item, partes, curID;
   var itemValues = {};
   var auxValue;

   itemValues['hdr'] = {};
   itemValues['hdr']['edit'] = caso;                                                   // Se trata de edicion o nuevo recibo
   itemValues['hdr']['id_recibo'] = id;                                                // Edicion, guardo id de recibo
   itemValues['hdr']['nro_ot'] = id;                                                   // Nuevo, guardo id de OT
   itemValues['hdr']['nro_recibo'] = content.getElementById("nro_recibo").innerHTML;   // Numero recibo
   itemValues['hdr']['fecha'] = content.getElementById("date_recibo").innerHTML;       // Fecha recibo
   itemValues['hdr']['debe'] = content.getElementById("totalDebe").innerHTML;          // Debe
   itemValues['hdr']['haber'] = content.getElementById("totalHaber").innerHTML;        // Haber
   itemValues['hdr']['saldo'] = content.getElementById("totalSaldo").innerHTML;        // Saldo
   itemValues['hdr']['nota'] = content.getElementById("entryNota").value;              // Nota

   // Busco todos los items y armo array para guardar
   var items = content.querySelectorAll("div[name='group_pay']");
   for(i = 1; i <= items.length; i++) {
      item = items[i-1];
      partes = item.id.split('_');
      curID = parseInt(partes[partes.length-1]);

      auxValue = content.getElementById("combobox_"+curID).value;
      itemValues[curID] = {};
      itemValues[curID]['tipoPago'] = auxValue;

      if(auxValue == 2 || auxValue == 3) {
         itemValues[curID]['entryFecha'] = content.getElementById("entryFecha_"+curID).value;
         itemValues[curID]['entryBanco'] = content.getElementById("entryBanco_"+curID).value;
         itemValues[curID]['entryLocalidad'] = content.getElementById("entryLocalidad_"+curID).value;
         itemValues[curID]['entryNumero'] = content.getElementById("entryNumero_"+curID).value;
      }
      else if(auxValue == 5) {
         itemValues[curID]['entryOtro'] = content.getElementById("entryOtro_"+curID).value;
      }

      itemValues[curID]['haber'] = content.getElementById("haber_"+curID).value;

   }

   parent.myframe.location = '../OT/recibo.php?opt=1&params='+JSON.stringify(itemValues);
}
</script>

<!-##################### Se ejecuta cuando se selecciona la forma de pago en un item de recibo ###################-->
<script type="text/javascript">
function ReciboItemAdd() {
   
   var lastSaldo;
   
   var last_id = document.getElementById("cant_item").innerHTML;
   if(parseInt(last_id, 10)>0) {
      lastSaldo = document.getElementById("saldo_"+last_id);
   }
   else {
      lastSaldo = document.getElementById("totalDebe");
   }
   
   last_id++;
   var parentItems = document.getElementById('parentItems');
   var groupID = "group_pay_"+last_id;
   var comboID = "combobox_"+last_id;
   var debeID = "debe_"+last_id;
   var haberID = "haber_"+last_id;
   var saldoID = "saldo_"+last_id;
   var removeID = "btnItemRemove_"+last_id;
   var options = {1:'EFECTIVO', 2:'CHEQUE', 3:'DEPOSITO/TRANSFERENCIA', 4:'TARJETA', 5:'OTRO'};
   
   // Grupo de campos
   var divGroup = document.createElement("div");
   divGroup.id = groupID;
   divGroup.setAttribute("name", "group_pay");
   divGroup.className = "divReciboItemGroup";
   // div de select type
   var divType = document.createElement("div");
   divType.className = "divReciboItemType";
   var selectType = document.createElement("select");
   selectType.id = comboID;
   var i=1;
   for(index in options) {
      selectType.options[selectType.options.length] = new Option(options[index], index);
      i++;
   }
   selectType.addEventListener("change", ReciboSelectedTypeItem, false);
   divType.appendChild(selectType);
   
   // div debe
   var divDebe = document.createElement("div");
   divDebe.id = debeID;
   divDebe.className = "divReciboItemDebe";
   divDebe.innerHTML = parseFloat(lastSaldo.innerHTML).toFixed(2);
   // div haber
   var divHaber = document.createElement("div");
   divHaber.className = "divReciboItemHaber";
   var entryHaber = document.createElement("input");
   entryHaber.id = haberID;
   entryHaber.type = "text";
   entryHaber.style = "text-align: right;";
   entryHaber.size = 10;
   entryHaber.maxLength = 50;
   entryHaber.value = 0;
   entryHaber.addEventListener("change", ReciboItemUpdate, false);
   divHaber.appendChild(entryHaber);
   // div saldo
   var divSaldo = document.createElement("div");
   divSaldo.id = saldoID;
   divSaldo.className = "divReciboItemSaldo";
   divSaldo.innerHTML = parseFloat(divDebe.innerHTML - entryHaber.value).toFixed(2);
   // div btn Remove
   var divRemove = document.createElement("div");
   divRemove.className = "divReciboItemRemove";
   var aRemove = document.createElement("a");
   aRemove.href = "#";
   var inRemove = document.createElement("input");
   inRemove.type = "image";
   inRemove.title = "Eliminar Item";
   inRemove.id = removeID;
   inRemove.src = "../Imagenes/deleteB.png";
   inRemove.style = "padding: 3px"; 
   inRemove.width = "15";
   inRemove.height = "15";
   inRemove.addEventListener("click", ReciboItemRemove, false);
   aRemove.appendChild(inRemove);
   divRemove.appendChild(aRemove);
   
   divGroup.appendChild(divType);
   divGroup.appendChild(divDebe);
   divGroup.appendChild(divHaber);
   divGroup.appendChild(divSaldo);
   divGroup.appendChild(divRemove);
   parentItems.appendChild(divGroup);
   
   document.getElementById("cant_item").innerHTML = last_id;
}
</script>

<!-##################### Se ejecuta cuando se selecciona la forma de pago en un item de recibo ###################-->
<script type="text/javascript">
function ReciboSelectedTypeItem(selectItem) {
   
   var group_id, group, combo, groupNode, comboNode, infoNode;
   var partes;
   
   if(selectItem.id) {
      partes = selectItem.id.split('_');
   }
   else {
      partes = this.id.split('_');
   }
   group_id = parseInt(partes[partes.length-1]);
   group = "group_pay_"+group_id;
   combo = "combobox_"+group_id;
   groupNode = document.getElementById(group);
   comboNode = document.getElementById(combo);
   infoNode = document.getElementById('divInfo_'+group_id);
   
   // Elimino bloque de info extra
   if(infoNode) {
      infoNode.parentNode.removeChild(infoNode);
   }
   
   var type = comboNode.value;
   switch(type) {
      case '2':   // Agrego los campos de info de cheque
      case '3':   // Agrego los campos de info de transferencia/deposito
         var divInfo = document.createElement("div");
         divInfo.id = "divInfo_"+group_id;
         divInfo.style = "clear: both;";
         
         var divFecha = document.createElement("div");
         divFecha.style = "width: 100%; height: 22px;";
         var divHdrFecha = document.createElement("div");
         divHdrFecha.innerHTML = "Fecha:";
         divHdrFecha.className = "divReciboItemExtraHdr";
         var divEntryFecha = document.createElement("div");
         divEntryFecha.style = "float: left; width: 80%;";
         var entryFecha = document.createElement("input");
         entryFecha.id = "entryFecha_"+group_id;
         entryFecha.type = "date";
         entryFecha.size = 15;
         entryFecha.maxLength = 12;
         entryFecha.placeholder = "ej: 24-09-1984";
         divEntryFecha.appendChild(entryFecha);
         divFecha.appendChild(divHdrFecha);
         divFecha.appendChild(divEntryFecha);
         
         var divBanco = document.createElement("div");
         divBanco.style = "width: 100%; height: 22px;";
         var divHdrBanco = document.createElement("div");
         divHdrBanco.innerHTML = "Banco:";
         divHdrBanco.className = "divReciboItemExtraHdr";
         var divEntryBanco = document.createElement("div");
         divEntryBanco.style = "float: left; width: 80%;";
         var entryBanco = document.createElement("input");
         entryBanco.id = "entryBanco_"+group_id;
         entryBanco.type = "text";
         entryBanco.size = 25;
         entryBanco.maxLength = 100;
         divEntryBanco.appendChild(entryBanco);
         divBanco.appendChild(divHdrBanco);
         divBanco.appendChild(divEntryBanco);
         
         var divLocalidad = document.createElement("div");
         divLocalidad.style = "width: 100%; height: 22px;";
         var divHdrLocalidad = document.createElement("div");
         divHdrLocalidad.innerHTML = "Localidad:";
         divHdrLocalidad.className = "divReciboItemExtraHdr";
         var divEntryLocalidad = document.createElement("div");
         divEntryLocalidad.style = "display: block; float: left; width: 80%;";
         var entryLocalidad = document.createElement("input");
         entryLocalidad.id = "entryLocalidad_"+group_id;
         entryLocalidad.type = "text";
         entryLocalidad.size = 25;
         entryLocalidad.maxLength = 100;
         divEntryLocalidad.appendChild(entryLocalidad);
         divLocalidad.appendChild(divHdrLocalidad);
         divLocalidad.appendChild(divEntryLocalidad);
         
         var divNumero = document.createElement("div");
         var divHdrNumero = document.createElement("div");
         type === '2' ? divHdrNumero.innerHTML = "N° Cheque:" : divHdrNumero.innerHTML = "N° Operación:";
         divHdrNumero.className = "divReciboItemExtraHdr";
         var divEntryNumero = document.createElement("div");
         divEntryNumero.style = "display: block; float: left; width: 80%;";
         var entryNumero = document.createElement("input");
         entryNumero.id = "entryNumero_"+group_id;
         entryNumero.type = "text";
         entryNumero.size = 25;
         entryNumero.maxLength = 100;
         divEntryNumero.appendChild(entryNumero);
         divNumero.appendChild(divHdrNumero);
         divNumero.appendChild(divEntryNumero);
         
         divInfo.appendChild(divFecha);
         divInfo.appendChild(divBanco);
         divInfo.appendChild(divLocalidad);
         divInfo.appendChild(divNumero);
         groupNode.appendChild(divInfo);
         break;
         
      case '5':  // agrego el campo de "otro"
         var divInfo = document.createElement("div");
         divInfo.id = "divInfo_"+group_id;
         divInfo.style = "clear: both;";
         
         var divOtro = document.createElement("div");
         divOtro.style = "width: 100%; height: 22px;";
         var entryOtro = document.createElement("input");
         entryOtro.id = "entryOtro_"+group_id;
         entryOtro.type = "text";
         entryOtro.size = 30;
         entryOtro.maxLength = 100;
         entryOtro.placeholder = "Descripción...";
         divOtro.appendChild(entryOtro);
         
         divInfo.appendChild(divOtro);
         groupNode.appendChild(divInfo);
         break;
   }
}
</script>

<!-##################### Se ejecuta cuando se modifica el monto de "haber" ###################-->
<script type="text/javascript">
function ReciboItemUpdate(entryItem) {
   
   var group_id, currentHaber, lastSaldo, globalHaber="";
   var i, item, partes, curID;
   
   if(entryItem.id) {
      partes = entryItem.id.split('_');
   }
   else {
      partes = this.id.split('_');
   }
   group_id = parseInt(partes[partes.length-1]);
   
   // Actualizo todos los items desde el modificado y sumo todos los haberes
   var items = document.querySelectorAll("div[name='group_pay']");
   for(i = 1; i <= items.length; i++) {
      item = items[i-1];
      partes = item.id.split('_');
      curID = parseInt(partes[partes.length-1]);
      
      currentHaber = document.getElementById("haber_"+curID).value;
      globalHaber = globalHaber*1.0 + currentHaber*1.0;
      if(i==1) {
         lastSaldo = document.getElementById("totalDebe").innerHTML;
      }

      document.getElementById("debe_"+curID).innerHTML = parseFloat(lastSaldo).toFixed(2);
      document.getElementById("saldo_"+curID).innerHTML = parseFloat(lastSaldo - currentHaber*1.0).toFixed(2);
      lastSaldo = document.getElementById("saldo_"+curID).innerHTML;
   }
   
   // Actualizo totales
   document.getElementById("totalHaber").innerHTML = parseFloat(globalHaber).toFixed(2);
   document.getElementById("totalSaldo").innerHTML = parseFloat(document.getElementById("totalDebe").innerHTML - globalHaber).toFixed(2);
   
   // Texto del 'haber' total
   var texto = numeroALetras(parseFloat(globalHaber).toFixed(2), {
      plural: 'pesos',
      singular: 'peso',
      centPlural: 'centavos',
      centSingular: 'centavo'
   });
   
   document.getElementById("textoHaber").innerHTML = "SON: "+texto;
}
</script>


<!-##################### Se ejecuta cuando se presiona el boton "eliminar item" ###################-->
<script type="text/javascript">
function ReciboItemRemove(button) {
   
   var cantItem = parseInt(document.getElementById("cant_item").innerHTML, 10);
   var group_id, group, groupNode;
   var i, items, item, partes, curID, combo, entryHaber, btnRemove;
   
   // Remuevo nodo del grupo a eliminar
   if(button.id) {
      partes = button.id.split('_');
   }
   else {
      partes = this.id.split('_');
   }
   group_id = parseInt(partes[partes.length-1]);
   group = "group_pay_"+group_id;
   groupNode = document.getElementById(group);
   groupNode.parentNode.removeChild(groupNode);
   
   // Reasigno IDs a los grupos que quedan
   cantItem--;
   if(cantItem > 0) {
      items = document.querySelectorAll("div[name='group_pay']");
      for(i = 1; i <= items.length; i++) {
         item = items[i-1];
         partes = item.id.split('_');
         curID = parseInt(partes[partes.length-1]);
         
         document.getElementById(item.id).setAttribute("id", "group_pay_"+i);
         combo = document.getElementById("combobox_"+curID);
         combo.setAttribute("id", "combobox_"+i);
         document.getElementById("debe_"+curID).setAttribute("id", "debe_"+i);
         document.getElementById("haber_"+curID).setAttribute("id", "haber_"+i);
         document.getElementById("saldo_"+curID).setAttribute("id", "saldo_"+i);
         document.getElementById("btnItemRemove_"+curID).setAttribute("id", "btnItemRemove_"+i);
         if(document.getElementById("divInfo_"+curID)) {
            document.getElementById("divInfo_"+curID).setAttribute("id", "divInfo_"+i);
            switch(combo.value) {
               case '2':
               case '3':
                  document.getElementById("entryFecha_"+curID).setAttribute("id", "entryFecha_"+i);
                  document.getElementById("entryBanco_"+curID).setAttribute("id", "entryBanco_"+i);
                  document.getElementById("entryLocalidad_"+curID).setAttribute("id", "entryLocalidad_"+i);
                  document.getElementById("entryNumero_"+curID).setAttribute("id", "entryNumero_"+i);
                  break;
                  
               case '5':
                  document.getElementById("entryOtro_"+curID).setAttribute("id", "entryOtro_"+i);
                  break;
            }
         }
      }
      ReciboItemUpdate(items[0]);
   }
   else {
      document.getElementById("totalHaber").innerHTML = "0.00";
      document.getElementById("totalSaldo").innerHTML = document.getElementById("totalDebe").innerHTML;
   }
   
   document.getElementById("cant_item").innerHTML = cantItem;
}
</script>

<!-####################################################################################-->
<!-############################ ACCIONES DE BOTONES EDICION ###########################-->
<!-####################################################################################-->

<!-##################### Apertura de ventana mediante boton "Nota" ###################-->
<script type="text/javascript">
function InsertarNota()
	{
	notewin = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/insertnota.php', 'Insertar Nota', 'width=700px,height=150px,center=1,resize=0,scrolling=1');
	notewin.onclose=function()
		{ 
		var nota=this.contentDoc.getElementById("nota").value		// Tomo el valor del textfield del pop-up
		nota=Str_Replace('\n', ' | ', nota);
		parent.myframe.location = '../PopUps/insertnota.php?hacer=1&nota='+nota;

		// Seteo el valor de la nota al span de la pagina
		if(nota != '')			document.getElementById("span_nota").innerHTML = 'NOTA:\n'+nota;
		else						document.getElementById("span_nota").innerHTML = '';
		return true;
		}
	}
</script>

<!-##################### Apertura de ventana mediante boton "Ajustar Total" ###################-->
<script type="text/javascript">
function AjustarTotal()
	{
	win = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/ajustartotal.php?hacer=0', 'Ajuste de Precio', 'width=390px,height=160px,center=1,resize=0,scrolling=1');
	
	win.onclose=function()									// Codigo que se ejecuta al cerrar la ventana
		{
		var desc=this.contentDoc.getElementById("fieldnewdesc").value;					// Tomo el valor del campo descuento
		var total=this.contentDoc.getElementById("fieldnewtotal").value;				// Tomo el valor del campo indice de precio
		parent.myframe.location = '../PopUps/ajustartotal.php?hacer=1&desc='+desc+'&total='+total;
		return true;
		};
	}
</script>

<!-##################### Apertura de ventana mediante boton "Nuevo Item" ###################-->
<script type="text/javascript">
function NuevoItem()
	{
	newitemwin = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/newitem.php', 'Nuevo Item', 'width=500px, height=260px, center=1, resize=0, scrolling=1')

	newitemwin.onclose=function()
		{
		var caso=this.contentDoc.getElementById("fieldcaso").value;		// Tomo el valor del campo oculto para saber si se trata de MOB(1) o MAT(2) 
		var cant=this.contentDoc.getElementById("cantidad").value;
		var desc=this.contentDoc.getElementById("descripcion").value;
		var imp=this.contentDoc.getElementById("importe").value;
		
		var dir='../PopUps/newitem.php?caso=3&cant='+cant+'&desc='+desc+'&imp='+imp;

		if(caso==2)
			{
			var cod=this.contentDoc.getElementById("codigo").value;
			dir=dir+'&MAT&cod='+cod;
			}
		parent.myframe.location = dir;
		return true;
		};
	}
</script>

<!-##################### Boton "Guardar" en Edicion de STD y "Listo" en Edicion de CLI y OT ###################-->
<script type="text/javascript">
function AceptarEdicion()
	{
	window.parent.location.href = 'guardar.php';
	return true;
	}
</script>

<!-##################### Boton "Cancelar" en Edicion de STD ###################-->
<script type="text/javascript">
function CancelarEdicion(nro)
	{
	window.parent.location.href = 'buscar.php?pres='+nro;
	return true;
	}
</script>

<!-####################################################################################-->
<!-################################# PRESUPUESTO STD ##################################-->
<!-####################################################################################-->

<!-##################### Hace Mostrar en Pantalla el Presupuesto STD Seleccionado ###################-->
<script type="text/javascript">
function MostrarPresupuesto()
	{
	var nro = document.getElementById('nro_pres').value;
	window.parent.location.href = 'buscar.php?pres='+nro;
	return true;
	}
</script>

<!-##################### Prepara y lanza un Nuevo Presupuesto STD ###################-->
<script type="text/javascript">
function LanzarNuevoSTD()
	{
	var motor = document.getElementById("motor").value; 
	window.parent.location.href = 'nuevo.php?motor='+motor;
	return true;
	}
</script>

<!-####################################################################################-->
<!-################################# PRESUPUESTO CLI ##################################-->
<!-####################################################################################-->

<!-##################### Hace Mostrar en Pantalla el Presupuesto STD Seleccionado ###################-->
<script type="text/javascript">
function PRECLI_CheckNew()
	{
	var nro = document.getElementById('nro_pres').value;
	if(nro!=0) {
		var clientForm = document.getElementById('form_newcli');
		clientForm.submit();
	}
	else {
		alert("ATENCION: Debe Seleccionar un Presupuesto Estandar");
	}
	return true;
	}
</script>

<!-####################################################################################-->
<!-################################## MANO DE OBRA ####################################-->
<!-####################################################################################-->

<!-##################### Chequea que el importe ingresado en un nuevo item MOB sea posible ###################-->
<script type="text/javascript">
function MOBValidarImporte(i, caso)
	{
	var nombre, precio;

	nombre='importe'+i;
	precio = document.getElementById(nombre).value;
	if(!isNaN(precio))
		{
		if(precio < 0)
			{
			alert("Precio "+i+" Incorrecto");
			document.getElementById(nombre).focus();
			return false;
			}
		}
	else
		{
		alert("Ingrese un Valor Numerico");
		document.getElementById(nombre).focus();
		return false;
		}
	if(caso==1)	// Debo actualizar ademas el item en la base MOB
		{
		var span='span_imp'+i;
		document.getElementById(span).innerHTML = precio;
		var lista = document.getElementById('lista').value;
		parent.myframe.location = '../PopUps/uplist.php?opc=1&numero='+i+'&imp='+precio+'&lista='+lista;
		}
	return true;
	}
</script>

<!-##################### Boton "Guardar" en el alta de Item ###################-->
<script type="text/javascript">
function MOBGuardarNew()
	{
	var desc;
	
	desc=document.getElementById('descripcion').value;
	sec=document.getElementById('seccion').value;
	if(desc=='')		alert("Se debe Ingresar una Descripcion");
	else if(sec==0)	alert("Se debe Selecionar una Seccion");
	else					document.formnew.submit();
	}
</script>

<!-##################### Boton "Nuevo" luego de agregar un item MOB###################-->
<script type="text/javascript">
function MOBNewItem()
	{
	window.parent.location.href = '../Mob/nuevo.php';
	return true;
	}
</script>

<!-##################### Boton "Cancelar" de MOB ###################-->
<script type="text/javascript">
function MOBCancelar()
	{
	window.parent.location.href = '../index.php';
	return true;
	}
</script>

<!-##################### Boton "Aceptar" de MOB-Lista ###################-->
<script type="text/javascript">
function MOBAjusteGlobal(opc)
	{
	// Tomo los datos con que quedaron las listas origen y destino
	var id_origen = document.getElementById("lista1").value;
	var id_destino = document.getElementById("lista2").value;
	var indice = document.getElementById("indice").value;
	
	if(isNaN(indice))	alert("El Indice debe ser Numerico");
	else if(indice<0)	alert("Ingrese un Indice Positivo");
	else if(id_origen==0 || id_destino==0)	alert("Debe Seleccionar Lista Origen y Lista Destino");
	else
		{
		// Aviso el cambio realizado
		alert(' Se realizo la siguiente operacion:\nOrigen: Lista '+id_origen+' -> Indice: '+indice+' -> Destino: Lista '+id_destino);
		// Modifico los datos de la lista
		parent.myframe.location = '../PopUps/uplist.php?opc='+opc+'&origen='+id_origen+'&destino='+id_destino+'&indice='+indice;
		}
	return true;
	}
</script>

<!-##################### Busca la lista deseada en items y la hace cargar en la p\E1gina ###################-->
<script type="text/javascript">
function MOBMostrarLista(opc)
	{
	var lista = document.getElementById("lista").value; 
	window.parent.location.href = '../Mob/lista.php?opc='+opc+'&lista='+lista;
	return true;
	}
</script>

<!-####################################################################################-->
<!-#################################### MATERIALES ####################################-->
<!-####################################################################################-->

<!-##################### Boton "Cancelar" en Nuevo Item ###################-->
<script type="text/javascript">
function MATCancelar()
	{
	MOBCancelar();
	}
</script>

<!-##################### Boton "Guardar" en el alta de Item ###################-->
<script type="text/javascript">
function MATGuardarNew()
	{
	var motor = document.getElementById('motor').value;
	var rubro = document.getElementById('rubro').value;
	
	if(motor==0 || rubro==0)
		{
		alert("Debe Seleccionar Motor y Rubro");
		return false;
		}
	else
		{
		document.newmat.submit();
		return true;
		}
	}
</script>

<!-##################### Boton "Nuevo" luego de agregar un item MAT ###################-->
<script type="text/javascript">
function MATNewItem()
	{
	window.parent.location.href = '../Mat/nuevo.php';
	return true;
	}
</script>

<!-##################### Actuaiza los Items de la Lista en base a los Filtros Seleccionados ###################-->
<script type="text/javascript">
function MATActualizarFiltros()
	{
	var motor = document.getElementById("motor").value;
	var rubro = document.getElementById("rubro").value;
	var numero = document.getElementById("selnum").value;
	
	if(!numero)		numero = 0;
	window.parent.location.href = 'lista.php?listar&motor='+motor+'&rubro='+rubro+'&numero='+numero;
	return true;
	}
</script>

<!-##################### Lista los materiales filtrados ###################-->
<script type="text/javascript">
function MATActualizarPrecio(mot, rub, iten, id_pre)
	{
	var id_string = 'precio'+id_pre+'-'+mot+'-'+rub+'-'+iten;	// Armo el id del cuadro de texto editado
	var pre = document.getElementById(id_string).value;			// Levanto el valor del cuadro de texto editado
	// Seteo el nuevo valor a la base
	parent.myframe.location = 'upimp.php?mot='+mot+'&rub='+rub+'&item='+iten+'&id_pre='+id_pre+'&pre='+pre;
	}
</script>

<!-##################### Apertura de ventana mediante boton "Editar" ###################-->
<script type="text/javascript">
function MATEditItem(iten, num, desc, cod, motor)
	{
	cod=Str_Replace('+', '|', cod);			// Reemplazo signos '+' por '|' ya que el mas sirve para concatenar en javascript
	desc=Str_Replace('+', '|', desc);

	ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/edititem.php?caso=4&desc='+desc+'&cod='+cod, 'Edicion de Item', 'width=500px, height=150px, center=1, resize=0, scrolling=1')

	ventana.onclose=function()
		{ 
		var desc=this.contentDoc.getElementById("descripcion").value	// Tomo el valor de descripcion del popup
		var cod=this.contentDoc.getElementById("codigo").value		// Tomo el valor del codigo del popup
		var id_cod = 'cod-'+motor+'-'+iten+'-'+num;							// Armo el id del codigo en la pagina padre
		var id_desc = 'desc-'+motor+'-'+iten+'-'+num;						// Armo el id de la descripcion en la pagina padre
		// Actualizo los valores de los span de la pagina padre
		document.getElementById(id_cod).innerHTML = cod;
		document.getElementById(id_desc).innerHTML = desc;
		
		cod=Str_Replace('+', '|', cod);			// Reemplazo signos '+' por '|' ya que el mas sirve para concatenar en javascript
		desc=Str_Replace('+', '|', desc);
		// Actualizo el item en la base de datos
		parent.myframe.location = '../PopUps/upitem.php?hacer=1&rub='+iten+'&num='+num+'&desc='+desc+'&cod='+cod+'&motor='+motor;

		return true;
		};
	}
</script>

<!-##################### Muestra un popup para la edicion global de listas MAT ###################-->
<script type="text/javascript">
function MATAjusteGlobal(motor, rubro)
	{
	ventana = dhtmlmodal.open('ListGlobal', 'iframe', '../PopUps/uplist.php?opc=2&mostrar', 'Ajuste Global de Lista', 'width=500px, height=150px, center=1, resize=0, scrolling=1')

	ventana.onclose=function()
		{ 
		var origen=this.contentDoc.getElementById("lista1").value;		// Tomo el valor de descripcion
		var indice=this.contentDoc.getElementById("indice").value;		// Tomo el valor del codigo
		var destino=this.contentDoc.getElementById("lista2").value;		// Tomo el valor del codigo
		
		if(origen<1 || origen>4 || destino<1 || destino>4)
			{
			alert("ATENCION: Se debe seleccionar una lista ORIGEN y otra DESTINO");
			return false;
			}
		else if(indice<0)
			{
			alert("ATENCION: El indice debe ser mayor o igual a cero");
			return false;
			}
		else
			{
			parent.myframe.location = '../PopUps/uplist.php?opc=2&origen='+origen+'&indice='+indice+'&destino='+destino+'&motor='+motor+'&rubro='+rubro;
			alert('Se realizo la siguiente operacion:\nOrigen: Lista '+origen+' -> Indice: '+indice+' -> Destino: Lista '+destino);
			window.parent.location.href = '../Mat/lista.php?listar&motor='+motor+'&rubro='+rubro;
			return true;
			}
		};
	}
</script>

<!-####################################################################################-->
<!-####################################### RUBRO ######################################-->
<!-####################################################################################-->

<!-##################### Boton "Cancelar" en Nuevo Item ###################-->
<script type="text/javascript">
function RUBCancelar()
	{
	MOBCancelar();
	}
</script>

<!-##################### Nuevo Rubro ###################-->
<script type="text/javascript">
function RUBGuardarNew() {
   
	var desc = document.getElementById('desc_rubro').value;
	var seccion = document.getElementById('seccion').value;
	
	if(desc=='' || seccion==0) {
		alert("ATENCION: Debe Ingresar una Descripcion y una Seccion para el Rubro");
   }
	else {
		document.formrubro.submit();
   }
}
</script>

<!-##################### Prepara para nuevo rubro ###################-->
<script type="text/javascript">
function RUBNewItem()
	{
	window.parent.location.href = '../Rubro/nuevo.php';
	return true;
	}
</script>

<!-##################### Carga la descripcion seleccionada al textfield ###################-->
<script type="text/javascript">
function RUBCargarDescripcion()
	{
	var rubro = document.getElementById("rubro").value;
	parent.myframe.location = '../Rubro/updesc.php?rubro='+rubro;
	return true;
	}
</script>

<!-##################### Env\EDa el Formulario ###################-->
<script type="text/javascript">
function RUBAceptarEdit()
	{
	var rubro = document.getElementById("rubro").value;
	if(rubro==0)
		{
		alert("Seleccione un Rubro para Editar");
		return false;
		}
	else
		{
		document.FormEdit.submit();
		return true;
		}
	}
</script>

<!-##################### Prepara para nueva edicion ###################-->
<script type="text/javascript">
function RUBNewEdit()
	{
	window.parent.location.href = '../Rubro/editar.php';
	return true;
	}
</script>

<!-####################################################################################-->
<!-####################################### MOTOR ######################################-->
<!-####################################################################################-->

<!-##################### Env\EDa el Formulario de Items ###################-->
<script type="text/javascript">
function MOTGuardarNew()
	{
	var desc = document.getElementById('desc_motor').value;
	var lista = document.getElementById('lista').value;
	
	if(desc=='' || lista==0)
		{
		alert("ATENCION: Debe Ingresar Descripcion y Lista para el Motor");
		return false;
		}
	else
		{
		document.SelectItems.submit();
		return true;
		}
	}
</script>

<!-##################### Sale y vuelve al inicio del sistema ###################-->
<script type="text/javascript">
function MOTCancelar()
	{
	MOBCancelar();
	}
</script>

<!-##################### Actualiza los precios de los items en base a la lista deseada ###################-->
<script type="text/javascript">
function MOTMostrarPrecios(lista)
	{
	if(lista==0)	var lista = document.getElementById("lista").value;
	parent.myframe.location = 'motprecios.php?lista='+lista;
	return true;
	}
</script>

<!-##################### Actualiza dinamicamente el precio de un item ###################-->
<script type="text/javascript">
function MOTActualizarImporte(iten, imp)
	{
	var span = 'span_precio'+iten;
	parent.document.getElementById(span).innerHTML = imp;
	return true;
	}
</script>

<!-##################### Prepara para nuevo motor ###################-->
<script type="text/javascript">
function MOTNew()
	{
	window.parent.location.href = '../Motor/nuevo.php';
	return true;
	}
</script>

<!-##################### Hace cargar un motor y su lista de precios ###################-->
<script type="text/javascript">
function MOTCargarMotor()
	{
	var motor = document.getElementById("motor").value;
	parent.myframe.location = '../Motor/motcargar.php?motor='+motor;
	return true;
	}
</script>

<!-##################### Actualiza dinamicamente el estado de un item ###################-->
<script type="text/javascript">
function TildarItem(iten)
	{
	var tick_item = 'item'+iten;
	parent.document.SelectItems[tick_item].checked = true;
	}
</script>

<!-##################### Actualiza dinamicamente el estado de un item ###################-->
<script type="text/javascript">
function DesTildarItem(iten)
	{
	var tick_item = 'item'+iten;
	parent.document.SelectItems[tick_item].checked = false;
	}
</script>

<!-##################### Actualiza los precios de los items en base a la lista deseada ###################-->
<script type="text/javascript">
function MOTAceptarEdicion()
	{
	document.formedit.submit();
	return true;
	}
</script>

<!-##################### Actualiza los precios de los items en base a la lista deseada ###################-->
<script type="text/javascript">
function MOTNewEdit()
	{
	window.parent.location.href = '../Motor/Editar.php?hacer=0';
	return true;
	}
</script>

<!-########################################################################################-->
<!-####################################### PROVEEDOR ######################################-->
<!-########################################################################################-->

<!-##################### Lleva al index ###################-->
<script type="text/javascript">
function PROVCancelar()
	{
	MOBCancelar();
	}
</script>

<!-##################### Lleva al index ###################-->
<script type="text/javascript">
function PROVGoBack(prov)
	{
	window.parent.location = 'lista.php?id='+prov;
	return true;
	}
</script>

<!-##################### Lleva a la pagina base de actualizacion de Proveedor ###################-->
<script type="text/javascript">
function PROVNewUpdate()
	{
	window.parent.location = 'update.php';
	return true;
	}
</script>

<!-##################### Busca las listas disponibles del proveedor ###################-->
<script type="text/javascript">
function PROVCargarListas()
	{
	var id=document.getElementById("id").value; 
	window.parent.location = 'lista.php?id='+id;
	return true;
	}
</script>

<!-##################### Recarga la p\E1gina con resultados de busqueda ###################-->
<script type="text/javascript">
function PROVMostrarResultados()
	{
	var id=document.getElementById("id").value;
	var lista=0;
	if(window.sel_prov.lista)
		lista=document.getElementById("lista").value;
	var motor=document.getElementById("motor").value;
	var frase=document.getElementById("palabra").value;
	window.parent.location = 'lista.php?id='+id+'&lista='+lista+'&motor='+motor+'&palabra='+frase;
	return true;
	}
</script>

<!-##################### Recarga la p\E1gina para alta de un item ###################-->
<script type="text/javascript">
function PROVAltaItem(prov, iten)
	{
	window.parent.location = 'altaitem.php?id='+prov+'&item='+iten;
	return true;
	}
</script>

<!-##################### Busca las listas disponibles del proveedor ###################-->
<script type="text/javascript">
function PROVRefrezcarPagina(id, iten)
	{
	var motor = document.getElementById("motor").value; 
	var rubro = document.getElementById("rubro").value;
	
	window.parent.location = 'altaitem.php?id='+id+'&item='+iten+'&motor='+motor+'&rubro='+rubro;
	return true;
	}
</script>

<!-##################### Guarda un Item Nuevo ###################-->
<script type="text/javascript">
function PROVGuardar()
	{
	var prov = document.getElementById("id").value;
	var iten = document.getElementById("item").value;
	var motor = document.getElementById("motor").value;
	var rubro = document.getElementById("rubro").value;
	var codigo = document.getElementById("codigo").value;
	var desc = document.getElementById("descripcion").value;
	var precio1 = document.getElementById("precio1").value;
	var precio2 = document.getElementById("precio2").value;
	var precio3 = document.getElementById("precio3").value;
	var precio4 = document.getElementById("precio4").value;

	window.parent.location = 'altaitem.php?guardar=1&id='+prov+'&item='+iten+'&motor='+motor+'&rubro='+rubro+'&codigo='+codigo+'&descripcion='+desc+'&precio1='+precio1+'&precio2='+precio2+'&precio3='+precio3+'&precio4='+precio4;
	return true;
	}
</script>

<!-##################### Busca las listas disponibles del proveedor ###################-->
<script type="text/javascript">
function AsociarItem(id, iten, num)
	{
	var motor = document.getElementById("motor").value;
	var rubro = document.getElementById("rubro").value;
	
	window.parent.location = 'altaitem.php?asociar=1&id='+id+'&item='+iten+'&motor='+motor+'&rubro='+rubro+'&numero='+num;
	return true;
	}
</script>

<!-##################### Busca las listas disponibles del proveedor ###################-->
<script type="text/javascript">
function PROVEliminarAsociacion(id, iten, motor, rubro)
	{
	window.parent.location = 'altaitem.php?eliminar=1&id='+id+'&item='+iten+'&motor='+motor+'&rubro='+rubro;
	return true;
	}
</script>

<!-########################################################################################-->
<!-######################################### GESTION ######################################-->
<!-########################################################################################-->

<!-####### Se encarga de setear un campo oculto para controlar que haya al menos una seccion seleccionada #######-->
<script type="text/javascript">
function GESContarSecciones(id)
	{
	var secciones = document.getElementById("secciones").value;	// Valor del campo oculto que lleva la cuenta de las secciones
	var checkid='items'+id;													// Id del check que llamo a la funcion
	if(document.getElementById(checkid).checked)			// Si esta marcado sumo
		secciones++;
	else																			// Sino resto
		secciones--;
	document.getElementById("secciones").value = secciones;		// Asigno el nuevo valor
	}
</script>

<!-##################### Verifica que se pueda crear el informe y hace el submit ###################-->
<script type="text/javascript">
function GESGuardarInforme()
	{
	var desc = document.getElementById("descripcion").value;
	if(desc=='')	// Verifico que tenga una descripcion
		alert("Debe Ingresar una Descripcion Para el Informe");
	else
		{
		secciones=document.getElementById("secciones").value
		if(secciones==0)	// Verifico que tenga al menos una seccion
			alert('Debe Seleccionar, al Menos, una Seccion');
		else
			document.newinf.submit();
		}
	return true;
	}
</script>

<!-##################### Lleva al index ###################-->
<script type="text/javascript">
function GESCancelar()
	{
	MOBCancelar();
	}
</script>

<!-##################### Lleva a la pantalla de crear un nuevo informe ###################-->
<script type="text/javascript">
function GESNewInforme()
	{
	window.parent.location.href = '../Gestion/informe.php?new';
	return true;
	}
</script>

<!-##################### Se llama cuando se desea agregar un operario ###################-->
<script type="text/javascript">
function GESNewOperario()
	{
	var nombre = document.getElementById('new_operario').value;
	if(nombre!='')
		{
		var accion = confirm('Seguro que Desea Agregar al Operario '+nombre+'?');
		if(accion==true)	window.parent.location.href = '../Gestion/operario.php?new&nombre='+nombre;
		}
	else
		alert('Debe Ingresar un Nombre Para el Operario');
	return true;
	}
</script>

<!-##################### Se llama cuando se desea borrar un operario ###################-->
<script type="text/javascript">
function GESBorrarOperario(id, nombre)
	{
	var accion = confirm('Seguro que Desea Eliminar al Operario '+nombre+'?');
	if(accion==true)	window.parent.location.href = '../Gestion/operario.php?eliminar&operario='+id;
	return true;
	}
</script>

<!-##################### Se llama cuando se desea borrar un operario ###################-->
<script type="text/javascript">
function GES_OrdenItem(caso, orden)
	{
	if(caso==2)		window.parent.location.href = '../Gestion/material.php?orden='+orden;		// Gestion de MAT
	else				window.parent.location.href = '../Gestion/mob.php?editar&orden='+orden;		// Gestion de MOB
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia el estado de un material ###################-->
<script type="text/javascript">
function GES_SetEstadoMaterial(ot, iten, num, add)
	{
	var id = 'estado'+ot+'-'+iten+'-'+num+'-'+add;	// Armo el id del SELECT que llamo la funcion
	var estado = document.getElementById(id).value;	// Busco el estado seleccionado
	parent.myframe.location = '../PopUps/acciones.php?accion=set_estado_mat&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add+'&estado='+estado;
	return true;
	}
</script>

<!-##################### Muestra la OT de donde proviene un item ###################-->
<script type="text/javascript">
function GES_MostrarOT(ot)
	{
	ventana = dhtmlmodal.open('EditBox', 'iframe', '../Gestion/verot.php?ot='+ot, 'Orden de Trabajo '+ot, 'width=900px, height=600px, center=1, resize=0, scrolling=1')
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia el estado de una MOB ###################-->
<script type="text/javascript">
function GES_VerificarMOB(ot, iten, num, add)
	{
	var id = 'estado'+ot+'-'+iten+'-'+num+'-'+add;					// Armo el id del SELECT que llamo la funcion
	var estado;
	document.getElementById(id).checked ? estado=1 : estado=0;	// Busco el estado del check seleccionado
	if(estado==0)	parent.myframe.location = '../PopUps/acciones.php?accion=ver_estado_mob&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add;
	else				parent.myframe.location = '../PopUps/acciones.php?accion=set_mob&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add;
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia el estado de una MOB ###################-->
<script type="text/javascript">
function GES_SetEstadoMOB(desc, ot, iten, num, add)
	{        
	if(desc!='-')
		{
		var hacer = confirm('El estado actual del Trabajo es: '+desc+'... Seguro que desea Desactivarlo?');
		if(hacer==false)
			{
			var id = 'estado'+ot+'-'+iten+'-'+num+'-'+add;			// Armo el id del SELECT que llamo la funcion
			parent.document.getElementById(id).checked = true;		// Seteo el check como activado
			}
		else
			parent.myframe.location = '../PopUps/acciones.php?accion=unset_mob&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add;
		}
	else
		parent.myframe.location = '../PopUps/acciones.php?accion=unset_mob&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add;
			
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia una asigancion de operario ###################-->
<script type="text/javascript">
function GES_VerificarOperario(ot, iten, num, add)
	{        
	var id = 'operario'+ot+'-'+iten+'-'+num+'-'+add;					// Armo el id del SELECT que llamo la funcion
	var operario = document.getElementById(id).value;
	parent.myframe.location = '../PopUps/acciones.php?accion=ver_operario&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add+'&operario='+operario;
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia una asigancion de operario ###################-->
<script type="text/javascript">
function GES_SetOperario(desc, ot, iten, num, add, operario)
	{
	if(desc!='-')
		{
		var hacer = confirm('El estado actual del trabajo es: '+desc+'... Seguro que desea cambiar de Operario?');
		if(hacer==false)
			window.parent.location.href = '../Gestion/mob.php?operario';
		else
			parent.myframe.location = '../PopUps/acciones.php?accion=set_operario&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add+'&operario='+operario;
		}
	else
		parent.myframe.location = '../PopUps/acciones.php?accion=set_operario&ot='+ot+'&item='+iten+'&numero='+num+'&agregado='+add+'&operario='+operario;
			
	return true;
	}
</script>

<!-##################### Se llama cuando se presiona el boton "Ordenar Tareas" ###################-->
<script type="text/javascript">
function GES_OrdenarTareas(cantidad)
	{
	var i=0;    
	for(i; i<cantidad; i++)
		if(parent.document.asig_operario[i].value == 0)
			{
			alert("ATENCION: Debe Seleccionar un Operario a Cada Tarea")
			break;
			}
	if(i==cantidad)    
    	window.parent.location.href = '../Gestion/mob.php?ordenar';
	return true;
	}
</script>

<!-##################### Se llama cuando se presiona alguna de las flechas que posicionan una tarea MOB ###################-->
<script type="text/javascript">
function GES_PosicionarMOB(operario, linea, accion, nro_ot, tope_inf)
	{
	var pos = linea;	// Almaceno el valor para utilizarlo luego
	if((accion=='subir_mob' && linea!=1) || (accion=='bajar_mob' && linea!=tope_inf))
		{
		// Linea que se pide mover
		var id1 = operario+'-'+pos;
		var ot1 = document.getElementById('ot'+id1).innerHTML;
		var desc_ot1 = document.getElementById('desc_ot'+id1).innerHTML;
		var cantidad1 = document.getElementById('cantidad'+id1).innerHTML;
		var desc1 = document.getElementById('desc'+id1).innerHTML;
		var prioridad1 = document.getElementById('prioridad'+id1).innerHTML;
		var estado1 = document.getElementById('estado'+id1).checked;
		
		// Linea que cede su lugar
		if(accion=='subir_mob')	pos--;
		else							pos++;
		var id2 = operario+'-'+pos;
		var ot2 = document.getElementById('ot'+id2).innerHTML;
		var desc_ot2 = document.getElementById('desc_ot'+id2).innerHTML;
		var cantidad2 = document.getElementById('cantidad'+id2).innerHTML;
		var desc2 = document.getElementById('desc'+id2).innerHTML;
		var prioridad2 = document.getElementById('prioridad'+id2).innerHTML;
		var estado2 = document.getElementById('estado'+id2).checked;
		
		// Ahora hago el intercambio
		// Paso origen a destino
		document.getElementById('ot'+id2).innerHTML = ot1;
		document.getElementById('desc_ot'+id2).innerHTML = desc_ot1;
		document.getElementById('cantidad'+id2).innerHTML = cantidad1;
		document.getElementById('desc'+id2).innerHTML = desc1;
		document.getElementById('prioridad'+id2).innerHTML = prioridad1;
		document.getElementById('estado'+id2).checked = estado1;
		// Paso destino a origen
		document.getElementById('ot'+id1).innerHTML = ot2;
		document.getElementById('desc_ot'+id1).innerHTML = desc_ot2;
		document.getElementById('cantidad'+id1).innerHTML = cantidad2;
		document.getElementById('desc'+id1).innerHTML = desc2;
		document.getElementById('prioridad'+id1).innerHTML = prioridad2;
		document.getElementById('estado'+id1).checked = estado2;
		
		// Por ultimo hago el reposicionamiento de los items en la base de datos
		parent.myframe.location = '../PopUps/acciones.php?accion='+accion+'&operario='+operario+'&linea='+linea;
		}
	else if(accion=='subir_mob' && linea==1)
		alert('La Tarea ya se Encuentra en el Nivel Superior');
	else if(accion=='bajar_mob' && linea==tope_inf)
		alert('La Tarea ya se Encuentra en el Nivel Inferior');
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia el estado de una MOB para reasignar la tarea ###################-->
<script type="text/javascript">
function GES_ReactivarMOB(nro_ot, iten, num, add, operario, linea)
	{
	var id = 'estado'+operario+'-'+linea;
    
	var estado = parent.document.getElementById(id).checked;
	if(estado === true){
        parent.document.getElementById(id).checked = false;
    }	                
	else
		{            
		var hacer = confirm("Seguro que Desea Reactivar la Tarea?");
		if(hacer==true)
			parent.myframe.location = '../PopUps/acciones.php?accion=reset_mob&nro_op='+nro_op+'&nro_ot='+nro_ot+'&item='+iten+'&numero='+num+'&agregado='+add;
		else
			parent.document.getElementById(id).checked = true;
		}
			
	return true;
	}
</script>

<!-##################### Se llama cuando se desea agregar una tarea extra a un operario ###################-->
<script type="text/javascript">
function GES_TareaExtra(operario)
	{
	ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/mobextra.php?operario='+operario, 'Tarea Extra', 'width=500px, height=150px, center=1, resize=0, scrolling=1');
	
	ventana.onclose=function()									// Codigo que se ejecuta al cerrar la ventana
		{
		var desc=this.contentDoc.getElementById("new_mob").value	// Tomo el valor de descripcion
		parent.myframe.location = '../PopUps/acciones.php?accion=mob_extra&operario='+operario+'&descripcion='+desc;
		return true;
		}
	return true;
	}
</script>

<!-##################### Se llama cuando se cambia el estado de una MOB para reasignar la tarea ###################-->
<script type="text/javascript">
function GES_VerOP()
	{
	var id = document.getElementById('orden').value;
	window.parent.location.href = '../gestion/mob.php?ver&id='+id;
	return true;
	}
</script>

<!-########################################################################################-->
<!-########################################## TALLER ######################################-->
<!-########################################################################################-->

<!-##################### Cuando se selecciona un operario, carga sus items MOB ###################-->
<script type="text/javascript">
function TALBuscarItems()
	{
	var operario = document.getElementById('operario').value;
	window.parent.location.href = '../Gestion/taller.php?cargar&operario='+operario;
	return true;
	}
</script>

<!-##################### Muestra la OT de donde proviene un item ###################-->
<script type="text/javascript">
function TALMostrarOT(ot)
	{
	ventana = dhtmlmodal.open('EditBox', 'iframe', '../Gestion/taller.php?verot&ot='+ot, 'Orden de Trabajo '+ot, 'width=900px, height=600px, center=1, resize=0, scrolling=1')
	return true;
	}
</script>

<!-##################### Se ejecuta cuando se quiere iniciar una tarea ###################-->
<script type="text/javascript">
function TAL_TareaIniciar(operario, posicion, init)
	{
	if(init==0)	
        parent.myframe.location = '../PopUps/acciones.php?accion=init_tarea&operario='+operario+'&posicion='+posicion;
	else			
        alert("Hay una tarea en curso, debe finalizarla para poder iniciar otra");
	return true;
	}
</script>

<!-##################### Se ejecuta cuando se quiere pausar una tarea ###################-->
<script type="text/javascript">
function TAL_TareaPausar(operario, tarea, init)
	{
	if(init==1)
		{
		ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/mobextra.php?justificar', 'Justificacion de Pausa', 'width=500px, height=150px, center=1, resize=0, scrolling=1');
		
		ventana.onclose=function()
			{
			var desc=this.contentDoc.getElementById("new_mob").value	// Tomo la justificacion
			if(desc!='')
				parent.myframe.location = '../PopUps/acciones.php?accion=pausa_tarea&operario='+operario+'&tarea='+tarea+'&comentario='+desc;
			else
				alert("Debe Ingresar una Justificacion");
			return true;
			}
		}
	else	
		alert("No hay tareas iniciadas para pausar");
	return true;
	}
</script>

<!-##################### Se ejecuta cuando se quiere suspender una tarea ###################-->
<script type="text/javascript">
function TAL_TareaSuspender(operario, tarea, init)
	{
	if(init==1)
		{
		var asistencia = confirm("Desea Detener la Tarea Y Solicitar Asistencia?");
		if(asistencia==true)
			parent.myframe.location = '../PopUps/acciones.php?accion=suspender_tarea&operario='+operario+'&tarea='+tarea;
		}

	return true;
	}
</script>

<!-##################### Se ejecuta cuando se quiere adelantar una tarea ###################-->
<script type="text/javascript">
function TAL_TareaOmitir(operario, tarea, init)
	{
	if(init==0)
		{
		ventana = dhtmlmodal.open('EditBox', 'iframe', '../PopUps/mobextra.php?justificar', 'Justificacion de Omision', 'width=500px, height=150px, center=1, resize=0, scrolling=1');
		
		ventana.onclose=function()
			{
			var desc=this.contentDoc.getElementById("new_mob").value	// Tomo la justificacion
			if(desc!='')
				parent.myframe.location = '../PopUps/acciones.php?accion=omitir_tarea&operario='+operario+'&tarea='+tarea+'&comentario='+desc;
			else
				alert("Debe Ingresar una Justificacion");
			return true;
			}
		}
	}
</script>

<!-##################### Se ejecuta cuando se quiere finalizar una tarea ###################-->
<script type="text/javascript">
function TAL_TareaRealizada(operario, tarea, init)
	{
	if(init==0)
        alert("No hay tareas iniciadas para finalizar");
    else
        parent.myframe.location = '../PopUps/acciones.php?accion=check_tarea&operario='+operario+'&tarea='+tarea;
        
	return true;
	}
</script>

<!-##################### Popup que indica la necesidad de asistencia ###################-->
<script type="text/javascript">
function POP_Asistencia(tarea)
	{
	alert("Se Requiere Asitencia para la Tarea:\n"+tarea);
	return true;
	}
</script>

<?php
}
