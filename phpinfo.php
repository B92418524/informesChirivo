<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function sumaArrayClaves($array) {
	$aSuma = array();
	if (is_array($array)) {
		foreach ($array as $k => $subArray) {
		  	foreach ($subArray as $id => $value) {
		  		if (is_numeric($value)) {
		  			$aSuma[$id] += $value;
		  		}
		  	}
		}
	}
	return $aSuma;
}


$array[] = array(
	"importeContrato" => "15059.9900000000" ,
	"facturacionOrigen" => "4459.2400000000",
	"ventaPendiente" => 10600.75,
	"facturacionMes" => 0,
	"aGastos" => array("gastosOrigen" => 0, "gastosPeriodo" => 0),
	"gastosOrigen" => 0,
	"gastosPeriodo" => 0,
	"ultimoPeriodo" => array("mes" => 1,"anio" => 2018),
	"totalPteCobro" => 0,
	"facturacionPteFinMes" => 0
	);


$array[] = array(
	"importeContrato" => "15059.9900000000" ,
	"facturacionOrigen" => "4459.2400000000",
	"ventaPendiente" => 10600.75,
	"facturacionMes" => 0,
	"aGastos" => array("gastosOrigen" => 0, "gastosPeriodo" => 0),
	"gastosOrigen" => 0,
	"gastosPeriodo" => 0,
	"ultimoPeriodo" => array("mes" => 1,"anio" => 2018),
	"totalPteCobro" => 0,
	"facturacionPteFinMes" => 0
	);


echo '<pre>';
var_dump(sumaArrayClaves($array));
echo '</pre>';

?>