<?php
include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';
include_once '../clases/diarioFacturasClientes.class.php';
$diarioFacturasClientes = new diarioFacturasClientes();
$accion = $_POST['accion'];
if ($accion == 'proyectos') {
	$resultado = $diarioFacturasClientes->pintarSelProyectos();
} else if ($accion == 'clientes') {
	$resultado = $diarioFacturasClientes->pintarSelClientes();
} else if ($accion == 'pintarTabla') {
	$filtros = $_POST;
	$resultado = $diarioFacturasClientes->pintarTabla($filtros);
} else if ($accion == 'imprimirExcel') {
	$filtros = $_POST;
	$resultado = $diarioFacturasClientes->pintarTabla($filtros, true); // con true = imprime excel
}
echo $resultado;
?>