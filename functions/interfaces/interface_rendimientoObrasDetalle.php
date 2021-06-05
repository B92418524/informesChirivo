<?php
include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';
include_once '../clases/rendimientoObrasDetalle.class.php';
$rendimientoObrasDetalle = new rendimientoObrasDetalle();
$accion = $_POST['accion'];
if ($accion == 'obras') {
	$resultado = $rendimientoObrasDetalle->pintarSelObras();
} else if ($accion == 'pintarTabla') {
	$obra = $_POST['obra'];
	$resultado = $rendimientoObrasDetalle->pintarTabla($obra);
} else if ($accion == 'imprimirExcel') {
	$obra = $_POST['obra'];
	$resultado = $rendimientoObrasDetalle->pintarTabla($obra, true); // con true = imprime excel
}
echo $resultado;
?>