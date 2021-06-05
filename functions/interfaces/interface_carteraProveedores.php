<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
set_time_limit(0);

include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';
include_once '../clases/carteraProveedores.class.php';
$carteraProveedores = new carteraProveedores();
$accion = $_POST['accion'];
if ($accion == 'ejecutarProcedimiento') {
	$resultado = $carteraProveedores->ejecutarProcedimiento();
}
echo $resultado;
?>