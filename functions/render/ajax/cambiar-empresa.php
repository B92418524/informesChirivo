<?php
require_once '../../../config.php';
include_once '../../functions.php';
include_once '../../db_functions.php';

$accion = cP('accion');
if ($accion == 'cambiarEmpresa') {
	$selEmpresa = cP('selEmpresa');
	$_SESSION['company_id'] = $selEmpresa;
}

header("Location: {$_SERVER['HTTP_REFERER']}");
exit;

?>