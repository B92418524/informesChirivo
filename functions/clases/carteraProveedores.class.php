<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');
set_time_limit(0);

class carteraProveedores extends bd {

    public function __construct() {
        parent::__construct();
    }

    public function ejecutarProcedimiento() {
    	$nombre = DB_APP.'_CH_EjecutarSincronizacionProveedores';
    	$aEjecutar = $this->procedimiento($nombre);

    	return json_encode(array('aEjecutar' => $aEjecutar));
	}
}
