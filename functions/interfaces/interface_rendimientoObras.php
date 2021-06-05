<?php
include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';
include_once '../clases/rendimientoObras.class.php';
$rendimientoObras = new rendimientoObras();
$accion = $_POST['accion'];
if ($accion == 'obras') {
	$resultado = $rendimientoObras->pintarSelObras();
} else if ($accion == 'clientes') {
	$resultado = $rendimientoObras->pintarSelClientes();
} else if ($accion == 'jefesObra') {
	$resultado = $rendimientoObras->pintarSelJefesObra();
} else if ($accion == 'encargados') {
	$resultado = $rendimientoObras->pintarSelEncargados();
} else if ($accion == 'pintarTabla') {
	$filtros = $_POST;
	$resultado = $rendimientoObras->pintarTabla($filtros);
} else if ($accion == 'imprimirExcel') {
	$filtros = $_POST;
	$resultado = $rendimientoObras->pintarTabla($filtros, true); // con true = imprime excel
} else if ($accion == 'ejecutarProcedimiento') {
	$resultado = $rendimientoObras->ejecutarProcedimiento();
}
echo $resultado;
?>