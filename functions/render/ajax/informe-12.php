<?php

include_once '../../aux_functions.php';
include_once '../../db_functions.php';

if (cP('accion')=='') {
	echo "false";
} else {
	$accion = cP('accion');
	if ($accion == 'obtenerGGBI') {
		$anio = cP('anio');
		$data = get_db_data(array('obtener-ggbi-i12',$anio));
		if (is_array($data)) {
			echo $data[0]['GGBI'];
		} else {
			echo "false";
		}
	} if ($accion == 'cambiarGGBI') {
		$anio = cP('anio');
		$ggbi = cP('ggbi');
		$update = update_db_data(array('cambiar-ggbi-i12', $anio, $ggbi));
		if ($update > 0) {
			echo "true";
		} else {
			// no existe la linea, por lo que hay que insertar una nueva
			$insert = store_db_data(array('insertar-nuevo-ggbi-i12', $anio, $ggbi));
			echo "true";
		}
	}
}

?>