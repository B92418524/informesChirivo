<?php
require_once '../../../config.php';
include_once '../../functions.php';
include_once '../../db_functions.php';

$accion = cP('accion');
if ($accion == '') {
	echo "";
} else if ($accion == 'obtenerProyectosFiltro') {
	$GLOBALS['ADMIN_I9']=true;
	$estado = cP('estado');
	$where = '';

	if ($estado == '1') {
		$where = " AND Activa='0' ";
	} elseif ($estado == '2') {
		$where = " AND Activa='-1' ";
	} elseif ($estado == '0') {
		$where = ' ';
	}

	$options = '';
	
	if (isset($GLOBALS['ADMIN_I9'])) {
		$data=get_db_data(array('listar-proyectos-i9', $where));
	} else {
		$data=get_db_data(array('listar-proyectos-jefes-obra-i9', $where));
	}

	if (is_array($data)) {
		foreach ($data as $d) {
			if ($d['CodigoProyecto']!='') {
				if (isset($GLOBALS['ADMIN_I9'])) {
					$descripcion = ucfirst($d['Descripcion']);
				} else {
					$descripcion = ucfirst($d['DescripcionProyecto']);
				}
				$options.='<option value="'.$d['CodigoProyecto'].'">'.$d['CodigoProyecto'].' - '.$descripcion.'</option>';
			}
		}
	}

	echo $options;
} else if ($accion == 'ejecutarProceso') {
	// $query = 'exec.';
	$data = get_db_data(array('custom-query', $query));
	echo $data;
}

?>