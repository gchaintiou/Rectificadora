<?php
    require_once "/Librerias/adodb/adodb.inc.php";
	$conex = ADONewConnection('mysqli');
	$conex->Connect("10.0.0.200", "root", "02954424916", "rectificadora");
	
    if ($conex->ErrorMsg()) {
        Debugger("No se Pudo Conectar a la Base. Eror: ".$link->ErrorMsg());
    }
    else
        Debugger("Conexión Exitosa");
/*
    $res = $conex->Execute("ALTER TABLE recd MODIFY COLUMN haber FLOAT(15,2)");

    $res = $conex->Execute("ALTER TABLE ote MODIFY COLUMN mat FLOAT(15,2)");
    $res = $conex->Execute("ALTER TABLE ote MODIFY COLUMN mob FLOAT(15,2)");
   
    $res = $conex->Execute("UPDATE recd SET haber = 101533.58 WHERE id_recibo = 139");
    $res = $conex->Execute("UPDATE recd SET haber = 120000 WHERE id_recibo = 154");


    $ote = $conex->Execute("SELECT nro_ot FROM ote WHERE 1");
    $ote->MoveFirst();
    while(!$ote->EOF){
        $nro_ot = $ote->fields['nro_ot'];
        $otd = $conex->Execute("SELECT SUM(importe * cantidad) AS mob FROM otd WHERE otd.nro_ot = $nro_ot AND item = 0");        
        if (isset($otd->fields['mob']))
            $mob = $otd->fields['mob'];
        else
            $mob = '0';
        $otd = null;
        $otd = $conex->Execute("SELECT SUM(importe * cantidad) AS mat FROM otd WHERE otd.nro_ot = $nro_ot AND item > 0");
        if (isset($otd->fields['mat']))
            $mat = $otd->fields['mat'];
        else
            $mat = '0';            
        $otd = null;
        $update = "UPDATE ote SET mob = $mob, mat = $mat WHERE nro_ot = $nro_ot";
        Debugger($update);
        $conex->Execute($update);
        $ote->MoveNext();
    }

*/

function Debugger($texto){
    file_put_contents(getcwd().'/actualizar_1_0_3'.'.log', $texto.PHP_EOL, FILE_APPEND);
}
?>