<?php
include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';
include_once '../clases/facturacionGlobal.class.php';
$facturacionGlobal = new facturacionGlobal();
$accion = $_POST['accion'];
if ($accion == 'proyectos') {
	$resultado = $facturacionGlobal->pintarSelProyectos();
} else if ($accion == 'proveedores') {
	$resultado = $facturacionGlobal->pintarSelProveedores();
} else if ($accion == 'pintarTabla') {
	$filtros = $_POST;
	$resultado = $facturacionGlobal->pintarTabla($filtros);
} else if ($accion == 'imprimirExcel') {
	$filtros = $_POST;
	$resultado = $facturacionGlobal->pintarTabla($filtros, true); // con true = imprime excel
}
echo $resultado;
?>