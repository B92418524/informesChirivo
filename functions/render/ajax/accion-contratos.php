<?php
include_once '../../comun/cabses.php';
include_once '../../comun/bd.class.php';
include_once 'contratos.class.php';
$contratos = new contratos();
$accion = $_POST['accion'];
if ($accion == 'cif') {
	$cif = $_POST['cif'];
	$resultado = $contratos->obtenerDatosEmpresa($cif);
} else if ($accion == 'obras') {
	$resultado = $contratos->obtenerTodosProyectos();
} else if ($accion == 'empresas') {
	$resultado = $contratos->obtenerTodasEmpresas();
} else if ($accion == 'admins') {
	$empresa = $_POST['cifEmpresa'];
	$resultado = $contratos->obtenerTodosAdmins($empresa);
} else if ($accion == 'usuarios') {
	$resultado = $contratos->obtenerTodosUsuarios();
} else if ($accion == 'obtenerUltimaFactura') {
	$codigoProveedor = $_POST['codigoProveedor'];
	$obra = $_POST['obra'];
	$resultado = $contratos->obtenerUltimaFactura($codigoProveedor, $obra);
} else if ($accion == 'eliminarAdmin') {
	$id = $_POST['id'];
	$resultado = $contratos->eliminarAdmin($id);
} else if ($accion == 'generar') {
	$form = $_POST;
	$contratos->generarDocumento($form);
} else if ($accion == 'obtenerContratos') {
	$form = $_POST;
	$resultado = $contratos->obtenerContratos($form);
} else if ($accion == 'imprimirExcel') {
	$form = $_POST;
	$resultado = $contratos->obtenerContratos($form, true); // muestra los contratos pero con true = imprime excel
}
echo $resultado;
?>