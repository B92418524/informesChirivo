<?php
include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';
include_once '../clases/carteraObraAnual.class.php';
$carteraObraAnual = new carteraObraAnual();
$accion = $_POST['accion'];
if ($accion == 'proyectos') {
	$estado = $_POST['estado'];
	$primeraVez = $_POST['primeraVez'];

	if ($primeraVez == 'true') {
		$primeraVez = true;
	} else {
		$primeraVez = false;
	}

	$resultado = $carteraObraAnual->pintarSelProyectos($estado, $primeraVez);
} else if ($accion == 'pintarTabla') {
	$filtros = $_POST;
	$resultado = $carteraObraAnual->pintarTabla($filtros);
} else if ($accion == 'imprimirExcel') {
	$filtros = $_POST;
	$resultado = $carteraObraAnual->pintarTabla($filtros, true); // con true = imprime excel
} else if ($accion == 'cambiarEstadoFacturacion') {
	$ejercicio = $_POST['ejercicio'];
	$mes = $_POST['mes'];
	$codigoProyecto = $_POST['codigoProyecto'];
	$anexo = $_POST['anexo'];
	$nuevoEstado = $_POST['nuevoEstado'];
	$resultado = $carteraObraAnual->cambiarEstadoFacturacion($ejercicio, $mes, $codigoProyecto, $anexo, $nuevoEstado);
} else if ($accion == 'cambiarObservaciones') {
	$ejercicio = $_POST['ejercicio'];
	$mes = $_POST['mes'];
	$codigoProyecto = $_POST['codigoProyecto'];
	$anexo = $_POST['anexo'];
	$nuevaObservacion = $_POST['nuevaObservacion'];
	$resultado = $carteraObraAnual->cambiarObservaciones($ejercicio, $mes, $codigoProyecto, $anexo, $nuevaObservacion);
} else if ($accion == 'ejecutarProcedimiento') {
	$mes = $_POST['mes'];
	$resultado = $carteraObraAnual->ejecutarProcedimiento($mes);
}
echo $resultado;
?>