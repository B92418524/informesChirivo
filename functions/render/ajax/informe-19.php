<?php
session_start();
include_once '../../aux_functions.php';
include_once '../../db_functions.php';

$accion = cP('accion');
if ($accion == '') {
	echo "";
} else if ($accion == 'obtenerObrasFiltro') {
	$estado = cP('estado');
	$where = '';

	if ($estado != 'todas') { // si no quiere ver todas las obras...
		if ($estado == 'activas' || $estado == 'inactivas') {
			if ($estado == 'activas') {
				$estado = '0'; // es finalizado, es decir, que significa lo contrario, finalizado = 0 >>>> estado = 0 >> ACTIVA
			} else {
				$estado = '-1'; // estan finalizadas, es decir, inactivas
			}
			$where = ' WHERE Finalizado='.$estado;
		} else if ($estado == 'ptes') {
			// pendientes de facturacion, donde el importe es mayor que 0
			// $where = ' WHERE PendienteEjecucion > 0';
			$where = " WHERE ( (Finalizado=0) OR (Finalizado=-1 AND PendienteEjecucion != 0) ) ";
		}
	} 

	$data = get_db_data(array('listar-obras-contratacion-i19',$where));
	if (is_array($data)) {
		$current_value=cG('obras');
		$haySesion = false;
	 	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
		if(isset($_SESSION['obras'])) {
			$haySesion = true;
		}
		$options='';
		foreach ($data as $d) {

			$selected = '';
			if ($current_value==$d['codigoproyecto']) { $selected=' selected="selected" '; }
			if ($haySesion && in_array($d['codigoproyecto'], $_SESSION['obras'])) { $selected=' selected="selected" '; }

			$options.='<option '.$selected.' value="'.$d['codigoproyecto'].'">'.$d['codigoproyecto'].' - '.utf8_encode(ucfirst($d['descripcion'])).'</option>';

		}
		echo $options;
	} else {
		echo "";
	}
} else if ($accion == 'ejecutarProcedimiento') {
	$ejercicio = cP('ejercicio');
	$proceso = 'exec.ReportingChirivo.dbo._CH_Sincronizacion_Importes_contrato_' . $ejercicio;
	$data = get_db_data(array('custom-query',$proceso));
	return json_encode(array('aEjecutar' => $data));
}

?>