<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

class rendimientoObras extends bd {

	private $separador;
	/* estos son los unicos que realmente se suman entre si en la tabla para formar el footer, los hago variables de clase porque al pintar cada linea, ya se reiniciarian las variables del bucle de obtenerDatosSegunPeriodo */
	private $totalImporteContrato;
	private $totalFacturacionOrigen;
	private $totalVentaPendiente;
	private $totalFacturacionMes;

    public function __construct() {
        parent::__construct();
        $this->separador = ';';
        $this->limpiarVariablesSumatorio();
    }

    private function limpiarVariablesSumatorio () {
    	$this->totalImporteContrato 	= 0;
		$this->totalFacturacionOrigen 	= 0;
		$this->totalVentaPendiente 		= 0;
		$this->totalFacturacionMes 		= 0;
    }

    public function pintarSelObras () {
    	$html = '<option value="-1">Obras activas</option>
    			 <option value="">Todas las obras</option>';

    	$select = 'DISTINCT CodigoProyecto, Proyecto';
    	$from = DB_APP.'jefes_de_obra';
    	$order = 'ORDER BY CodigoProyecto ASC';
    	$aObras = $this->consulta($select, $from, $order);
		if (is_array($aObras)) {
			foreach ($aObras as $a) {
				$html .= '<option value="' . $a['CodigoProyecto'] . '">' . $a['CodigoProyecto'] . ' - ' . $a['Proyecto'] . '</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarSelClientes () {
    	$html = '<option value="">Todos los clientes</option>';

    	$select = 'DISTINCT CodigoCliente, RazonSocial';
    	$from = DB_APP . 'clientes';
    	$where = "WHERE CodigoEmpresa='" . $this->empresa . "'";
    	$order = 'ORDER BY RazonSocial ASC';
    	$aClientes = $this->consulta($select, $from, $where, $order);
		if (is_array($aClientes)) {
			foreach ($aClientes as $a) {
				$html .= '<option value="' . $a['CodigoCliente'] . '">' . $a['CodigoCliente'] . ' - ' . $a['RazonSocial'] . '</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarSelJefesObra () {
    	$html = '<option value="">Todos los jefes de obra</option>';

    	$select = 'DISTINCT ID_JEFE_OBRA, Nombre';
    	$from = DB_APP . 'jefes_de_obra';
    	$order = 'ORDER BY Nombre ASC';
    	$aJefesObra = $this->consulta($select, $from, $order);
		if (is_array($aJefesObra)) {
			foreach ($aJefesObra as $a) {
				$html .= '<option value="' . $a['ID_JEFE_OBRA'] . '">' . $a['ID_JEFE_OBRA'] . ' - ' . $a['Nombre'] . '</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarSelEncargados () {
    	$html = '<option value="">Todos los encargados</option>';

    	$select = 'DISTINCT ID_JEFE_OBRA, Nombre';
    	$from = DB_APP . 'jefes_de_obra';
    	$order = 'ORDER BY Nombre ASC';
    	$aJefesObra = $this->consulta($select, $from, $order);
		if (is_array($aJefesObra)) {
			foreach ($aJefesObra as $a) {
				$html .= '<option value="' . $a['ID_JEFE_OBRA'] . '">' . $a['ID_JEFE_OBRA'] . ' - ' . $a['Nombre'] . '</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarTabla ($filtros, $excel = false) {
    	$html = '';
		$csv = ''; // en el caso de tener que llenar las lineas para el excel
    	$array1 = array();
    	$array2 = array();
    	$aTotalesFinal = array();

    	$select = 'DISTINCT [D_CODIGO_PROYECTO],[D_NOMBRE_JEFE_OBRA],[D_ID_JEFE_OBRA],[EMAIL],[ACTIVO],[D_PROYECTO],[codigoempresa],[D_CODIGO_CLIENTE],[D_RAZON_SOCIAL_CLIENTE],[D_IMPORTE_CONTRATO]';
    	$from = DB_APP . 'I4_CONSOLIDADO';
    	$where = "WHERE CodigoEmpresa='" . $this->empresa . "' " . $this->obtenerQueryFiltros($filtros);
    	$order = 'ORDER BY D_CODIGO_PROYECTO ASC';
    	$aTabla = $this->consulta($select, $from, $where, $order);

    	if (is_array($aTabla)) {
    		$inicioTabla = true;

			/* se obtienen los periodos */
			$aPeriodos = $this->obtenerPeriodos($filtros);

			/* se coge el primer valor que sabemos que se va a pintar */
			// $codigoProyectoActual = $aTabla[0]['D_CODIGO_PROYECTO']; 
			$codigoProyectoActual = '-1';
			$aDatosAnterior = array();

			$numero_contratos = count($aTabla);

			foreach ($aTabla as $a) {
				/* cada aDatos es una linea, y cuando pase al siguiente proyecto será cuando se calcule únicamente la fila de totales */
				$nombreProyecto = $a['D_PROYECTO'];
				$aDatos = $this->obtenerDatosSegunPeriodo($aPeriodos, $a, $filtros['fechaInicio'], $nombreProyecto);

				/* separar en bloques según el código de proyecto */
				$proyecto = $aDatos['proyecto'];
				$bordeFinProyecto = '';
				$lineaTotales = '';

				if ($codigoProyectoActual != $proyecto) {

					if (!$inicioTabla) {
						$bordeFinProyecto = '<tr style="border:0;height:20px;">
												<td colspan="24" style="border:0; height:50px; background-color:#fff !important;"></td>
											</tr>';
						/* aDatos lleva mucha información, de la cual muchos campos son repetidos, simplemente tenemos que hacer cuentas con dichos campos */
	    				$aTotales = $this->calcularLineaFinalBloque($aDatosAnterior, $filtros['fechaInicio']);
	    				/* el array aTotales trae todo el sumatorio, excepto de las variables globales que se calculan a parte... meterlo dentro del mismo array */
						$aTotales['importeContrato'] 	= $this->totalImporteContrato;
						$aTotales['facturacionOrigen']	= $this->totalFacturacionOrigen;
						$aTotales['ventaPendiente'] 	= $this->totalVentaPendiente;
						$aTotales['facturacionMes'] 	= $this->totalFacturacionMes;

						if ($excel) {
							$csv .= $this->pintarSumatorioExcel($aTotales);
						} else {
							$html .= $this->pintarSumatorio($aTotales) . $bordeFinProyecto;
						}

						/* aqui tengo el array final con todos los arrays de totales por agrupacion */
						$aTotalesFinal[] = $aTotales;

					}
					/* reiniciar sumatorio de las 4 variables iniciales */
					$this->limpiarVariablesSumatorio();
					$codigoProyectoActual = $proyecto;
					$aDatosAnterior = $aDatos;
					$inicioTabla = false;
				}

				/* pintar todo */
				if ($excel) {
					$csv .= $this->pintarLineasExcel($aDatos);
				} else {
					if ($numero_contratos > 1) {
						$html .= $this->pintarLineas($aDatos);
					} 
				}

				/* sumatorio de los cuatro primeros valores, en variables globales */
				$this->totalImporteContrato 	+= $aDatos['importeContrato'];
				$this->totalFacturacionOrigen 	+= $aDatos['facturacionOrigen'];
				$this->totalVentaPendiente 		+= $aDatos['ventaPendiente'];
				$this->totalFacturacionMes 		+= $aDatos['facturacionMes'];

				/*Si solo tiene un contrato pinto los totales y beneficios también en la primera y única línea*/
				if (!$excel && $numero_contratos == 1) {
					$aTotalesUnico = $this->calcularLineaFinalBloque($aDatos, $filtros['fechaInicio']);
					$html .= $this->pintarLineas($aDatos, $aTotalesUnico);
				}

				$primeraVez = false; // despues de pintar el primer bloque deja de ser la primera vez que entra
			}

			/* el ultimo nunca llega a tener el sumatorio de su bloque, el array seria aDatos */
			$aTotales = $this->calcularLineaFinalBloque($aDatos, $filtros['fechaInicio']);
			$aTotales['importeContrato'] 	= $this->totalImporteContrato;
			$aTotales['facturacionOrigen']	= $this->totalFacturacionOrigen;
			$aTotales['ventaPendiente'] 	= $this->totalVentaPendiente;
			$aTotales['facturacionMes'] 	= $this->totalFacturacionMes;
			if ($excel) {
				$csv .= $this->pintarSumatorioExcel($aTotales);
			} else {
				if ($numero_contratos > 1) { //Si solo tiene un contrato no se pinta la línea subtotal
					$html .= $this->pintarSumatorio($aTotales);
				}
			}

			/* y el ultimo tambien tengo que concatenarlo al array */
			$aTotalesFinal[] = $aTotales;

			/* ahora se pinta el sumatorio final, pero es posible que cuando se filtre solo muestre una unica linea, y jamás entre en el if de arriba para llenar aTotalesFinal... */
			if (count($aTotalesFinal) > 0) {
				$aSumaTotalesFinal = $this->sumaArrayClaves($aTotalesFinal);
				if ($excel) {
					$csv .= $this->pintarSumatorioExcel($aSumaTotalesFinal, true);
				} else {
					$html .= $this->pintarSumatorio($aSumaTotalesFinal, true);
				}
			}

		} else {
			$html = '<tr><td colspan="25" style="text-align:center">No hay registros.</td></tr>';
		}

		if ($excel) {
			$this->imprimirExcel($csv);
		} else {
			$html = '<table id="tabla" class="table table-striped table-bordered table-hover" style="border:0 !important;">
                    <thead style="background-color:#fff;">
                        <tr style="border:solid 1px silver;">
                            <th rowspan="2" style="min-width:20px;vertical-align:middle;text-align:center;">C&oacute;digo</th>
                            <th rowspan="2" style="min-width:300px;vertical-align:middle;text-align:center;">Obra</th>
                            <th rowspan="2" style="min-width:300px;vertical-align:middle;text-align:center;">Cliente</th>
                            <th rowspan="2" style="min-width:7px;vertical-align:middle;text-align:center;">Vigor</th>
                            <th rowspan="2" style="min-width:20px;vertical-align:middle;text-align:center;">Importe contrato</th>
                            <th rowspan="2" style="min-width:20px;vertical-align:middle;text-align:center;">Facturaci&oacute;n a origen</th>
                            <th title="Importe contrato - Facturaci&oacute;n a origen" rowspan="2" style="min-width:20px;vertical-align:middle;text-align:center;">Venta pendiente</th>
                            <th rowspan="2" style="min-width:20px;vertical-align:middle;text-align:center;">Facturaci&oacute;n periodo</th>
                            <th rowspan="2" style="min-width:50px;vertical-align:middle;text-align:center;">Facturaci&oacute;n pendiente desde día de cierre hasta fin de periodo</th>
                            <th rowspan="2" style="min-width:50px;vertical-align:middle;text-align:center;">Pendiente de facturaci&oacute;n por falta de contrato</th>
                            <th rowspan="2" style="min-width:50px;vertical-align:middle;text-align:center;">Pendiente de facturaci&oacute;n por aprobaci&oacute;n de partidas</th>
                            <th rowspan="2" style="min-width:20px;vertical-align:middle;text-align:center;">Pendiente de facturaci&oacute;n por otros motivos</th>
                            <th rowspan="2" style="min-width:30px;vertical-align:middle;text-align:center;">Total pendiente de facturaci&oacute;n</th>
                            <th colspan="2" style="min-width:80px;vertical-align:middle;text-align:center;">Total previsi&oacute;n de facturaci&oacute;n</th>
                            <th colspan="2" style="min-width:80px;vertical-align:middle;text-align:center;">Gastos contabilizados</th>
                            <th rowspan="2" style="min-width:50px;vertical-align:middle;text-align:center;">Pendiente de certificaci&oacute;n a subcontratas o proveedores</th>
                            <th rowspan="2" style="min-width:30px;vertical-align:middle;text-align:center;">Acopio o entrega a cuenta contabilizados</th>
                            <th colspan="2" style="min-width:80px;vertical-align:middle;text-align:center;">Total previsi&oacute;n de gastos</th>
                            <th colspan="2" style="min-width:80px;vertical-align:middle;text-align:center;">Beneficio</th>
                            <th colspan="2" style="min-width:80px;vertical-align:middle;text-align:center;">Beneficio %</th>
                        </tr>
                        <tr>
                            <th>Origen</th>
                            <th>Periodo</th>
                            <th>Origen</th>
                            <th>Periodo</th>
                            <th>Origen</th>
                            <th>Periodo</th>
                            <th>Origen</th>
                            <th>Periodo</th>
                            <th>Origen</th>
                            <th>Periodo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaContenido">
                        ' . $html . '          
                    </tbody>
                </table>';

			return json_encode(array(
									'html' => $html,
									'periodos' => $aPeriodos,
									'consulta' => 'select ' . $select . ' from ' . $from . ' ' . $where . ' ' . $order
								)
							);
		}
    }

	private function pintarLineas ($l, $aTotales = []) {
		$totalPrevisionFactOrigen = "";
   		$totalPrevisionFactPeriodo = "";
   		$gastosOrigen = "";
   		$gastosPeriodo = "";
   		$pteCertificacionSubProv = "";
   		$totalGastosOrigen = "";
   		$totalGastosPeriodo = "";
   		$beneficioOrigen = "";
   		$beneficioPeriodo = "";
   		$beneficioOrigenPorcentaje = "";
   		$beneficioPeriodoPorcentaje = "";

    	if (count($aTotales) > 0) { //Si tiene un único contrato se muestra en esa única línea todos los datos
    		$totalPrevisionFactOrigen = $this->formatoNumero($aTotales['totalPrevisionFactOrigen'], false, true, 2);
    		$totalPrevisionFactPeriodo = $this->formatoNumero($aTotales['totalPrevisionFactPeriodo'], false, true, 2);
    		$gastosOrigen = $this->formatoNumero($aTotales['gastosOrigen'], false, true, 2);
    		$gastosPeriodo = $this->formatoNumero($aTotales['gastosPeriodo'], false, true, 2);
    		$pteCertificacionSubProv = $this->formatoNumero($aTotales['pteCertificacionSubProv'], false, true, 2);
    		$totalGastosOrigen = $this->formatoNumero($aTotales['totalGastosOrigen'], false, true, 2);
    		$totalGastosPeriodo = $this->formatoNumero($aTotales['totalGastosPeriodo'], false, true, 2);
    		$beneficioOrigen = $this->formatoNumero($aTotales['beneficioOrigen'], false, true, 2);
			$beneficioPeriodo = $this->formatoNumero($aTotales['beneficioPeriodo'], false, true, 2);
			$beneficioOrigenPorcentaje =  $this->formatoNumero($aTotales['beneficioOrigenPorcentaje'], false, false, 2, false, true);
			$beneficioPeriodoPorcentaje = $this->formatoNumero($aTotales['beneficioPeriodoPorcentaje'], false, false, 2, false, true);
    	}

		/* Si es un gasto extra */
		if (substr($l['nombreObra'], 0, 4) === '9019') {
			$gastosOrigen = $this->formatoNumero($l['gastosOrigen'], false, true, 2);
			$gastosPeriodo = $this->formatoNumero($l['gastosPeriodo'], false, true, 2);
		}

    	$linea = 
		'<tr>
			<td style="min-width:20px"><a href="'.$l['enlace'].'" target="_blank">'.$l['proyecto'].'</a></td>
			<td style="min-width:300px">'.$l['nombreObra'].'</td>
			<td style="min-width:300px">'.$l['razonSocialCliente'].'</td>
			<td style="min-width:7px">'.$l['vigor'].'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($l['importeContrato'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($l['facturacionOrigen'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($l['ventaPendiente'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($l['facturacionMes'], false, true, 2).'</td>
			<td style="min-width:50px;text-align:right"></td>
			<td style="min-width:50px;text-align:right"></td>
			<td style="min-width:50px;text-align:right"></td>
			<td style="min-width:50px;text-align:right"></td>
			<td style="min-width:50px;text-align:right"></td>
			<td style="min-width:50px;text-align:right">'.$totalPrevisionFactOrigen.'</td>
			<td style="min-width:50px;text-align:right">'.$totalPrevisionFactPeriodo.'</td>
			<td style="min-width:50px;text-align:right">'.$gastosOrigen.'</td>
			<td style="min-width:50px;text-align:right">'.$gastosPeriodo.'</td>
			<td style="min-width:50px;text-align:right">'.$pteCertificacionSubProv.'</td>
			<td style="min-width:50px;text-align:right"></td>
			<td style="min-width:50px;text-align:right">'.$totalGastosOrigen.'</td>
			<td style="min-width:50px;text-align:right">'.$totalGastosPeriodo.'</td>
			<td style="min-width:50px;text-align:right">'.$beneficioOrigen.'</td>
			<td style="min-width:50px;text-align:right">'.$beneficioPeriodo.'</td>
			<td style="min-width:50px;text-align:right">'.$beneficioOrigenPorcentaje.'</td>
			<td style="min-width:50px;text-align:right">'.$beneficioPeriodoPorcentaje.'</td>
		</tr>';

		return $linea;
    }

    private function pintarLineasExcel ($l) {
    	$csv =
			'"'		.trim($l['proyecto'] )																.'"'.$this->separador.''.
			'"'		.trim($l['nombreObra'])																.'"'.$this->separador.''.
			'"'		.trim($l['razonSocialCliente'])														.'"'.$this->separador.''.
			'"'		.trim($l['vigor'])																	.'"'.$this->separador.''.
			''		.$this->formatoNumero($l['importeContrato'], true, false, 2, true)					.''.$this->separador.''.
			''		.$this->formatoNumero($l['facturacionOrigen'], true, false, 2, true)				.''.$this->separador.''.
			''		.$this->formatoNumero($l['ventaPendiente'], true, false, 2, true)					.''.$this->separador.''.
			''		.$this->formatoNumero($l['facturacionMes'], true, false, 2, true)					.''.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			''		.$this->formatoNumero($l['gastosOrigen'], true, false, 2, true)						.''.$this->separador.''.
			''		.$this->formatoNumero($l['gastosPeriodo'], true, false, 2, true)					.''.$this->separador.''.
			"\n";

		return $csv;
    }

    private function pintarSumatorio ($sumatorio, $final = false) {
    	$fondo = '#eee';
    	$filaVacia = '';
    	$borde = '';

    	if ($final) {
    		$fondo = '#96C7CE';
    		$filaVacia = '	<tr style="border:0;height:20px;">
								<td colspan="24" style="border:0; height:50px; background-color:#fff !important;"></td>
							</tr>';
    		$borde = 'border-top:solid 5px #6c8c90;';
    	}

    	$lineaTotales = 
			$filaVacia .
		'<tr style="background-color:'.$fondo.';font-weight:bold;'.$borde.'">
			<td style="min-width:20px"></td>
			<td style="min-width:300px"></td>
			<td style="min-width:300px"></td>
			<td style="min-width:7px"></td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($sumatorio['importeContrato'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($sumatorio['facturacionOrigen'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($sumatorio['ventaPendiente'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($sumatorio['facturacionMes'], false, true, 2).'</td>
			<td style="min-width:50px;text-align:right">'.$this->formatoNumero($sumatorio['facturacionPteFinMes'], false, true, 2).'</td>
			<td style="min-width:50px;text-align:right">'.$this->formatoNumero($sumatorio['pteFacturacionFaltaContrato'], false, true, 2).'</td>
			<td style="min-width:50px;text-align:right">'.$this->formatoNumero($sumatorio['pteFacturacionPartidas'], false, true, 2).'</td>
			<td style="min-width:20px;text-align:right">'.$this->formatoNumero($sumatorio['pteFacturacionOtros'], false, true, 2).'</td>
			<td style="min-width:30px;text-align:right">'.$this->formatoNumero($sumatorio['totalPteFacturacion'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['totalPrevisionFactOrigen'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['totalPrevisionFactPeriodo'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['gastosOrigen'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['gastosPeriodo'], false, true, 2).'</td>
			<td style="min-width:50px;text-align:right">'.$this->formatoNumero($sumatorio['pteCertificacionSubProv'], false, true, 2).'</td>
			<td style="min-width:30px;text-align:right">'.$this->formatoNumero($sumatorio['acopioEntregaCuenta'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['totalGastosOrigen'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['totalGastosPeriodo'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['beneficioOrigen'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['beneficioPeriodo'], false, true, 2).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['beneficioOrigenPorcentaje'], false, false, 2, false, true).'</td>
			<td style="min-width:40px;text-align:right">'.$this->formatoNumero($sumatorio['beneficioPeriodoPorcentaje'], false, false, 2, false, true).'</td>
		</tr>';

		return $lineaTotales;
    }

    private function pintarSumatorioExcel ($sumatorio, $final = false) {
    	$filaVaciaInicio = '';
    	$filaVacia = '""' . $this->separador . '' . "\n";

    	if ($final) {
    		$filaVaciaInicio = $filaVacia;
    	}

    	$csv =
    		$filaVaciaInicio .
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['importeContrato'], true, false, 2, true)				.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['facturacionOrigen'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['ventaPendiente'], true, false, 2, true)				.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['facturacionMes'], true, false, 2, true)				.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['facturacionPteFinMes'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['pteFacturacionFaltaContrato'], true, false, 2, true)	.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['pteFacturacionPartidas'], true, false, 2, true)		.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['pteFacturacionOtros'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalPteFacturacion'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalPrevisionFactOrigen'], true, false, 2, true)		.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalPrevisionFactPeriodo'], true, false, 2, true)	.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalGastosOrigen'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalGastosPeriodo'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['pteCertificacionSubProv'], true, false, 2, true)		.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['acopioEntregaCuenta'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalGastosOrigen'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['totalGastosPeriodo'], true, false, 2, true)			.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['beneficioOrigen'], true, false, 2, true)				.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['beneficioPeriodo'], true, false, 2, true)				.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['beneficioOrigenPorcentaje'], true, false, 2, true)	.''.$this->separador.''.
			''	.$this->formatoNumero($sumatorio['beneficioPeriodoPorcentaje'], true, false, 2, true)	.''.$this->separador.''.
			"\n".
			$filaVacia;

		return $csv;
    }

    private function obtenerQueryFiltros ($filtros) {
    	$where = '';
    	$obra = $filtros['obra'];
    	$cliente = $filtros['cliente'];
    	$jefeObra = $filtros['jefeObra'];
    	// $encargado = $filtros['encargado']; // no existe el campo encargado... todavia

		if ($obra == '-1') { // si es -1 solo son las obras activas
			$where .= " AND ACTIVO = '1' ";
		} else if ($obra != '') { // si es vacio es todas las obras
			$where .= " AND D_CODIGO_PROYECTO = '" . $obra . "' ";
		}

		if ($cliente != '' && $cliente != '-1') {
			$where .= " AND D_CODIGO_CLIENTE = '" . $cliente . "' ";
		}

		if ($jefeObra != '' && $jefeObra != '-1') {
			$where .= " AND D_ID_JEFE_OBRA = '" . $jefeObra . "' ";
		}

		return $where;
    }

    public function obtenerPeriodos ($filtros) { // se llama tambien desde el informe detalle
    	$fechaInicio = $filtros['fechaInicio'];
    	$fechaFin = $filtros['fechaFin'];

    	if ($fechaInicio == '') {
    		$fechaInicio = date('m-Y');
    	}

    	if ($fechaFin == '') {
    		$fechaFin = date('m-Y');
    	}

    	$aFechaInicio = explode('-', $fechaInicio);
		$aFechaFin = explode('-', $fechaFin);

		$mesIni = $aFechaInicio[0] + 0;
		$anioIni = $aFechaInicio[1] + 0;

		$mesFin = $aFechaFin[0] + 0;
		$anioFin = $aFechaFin[1] + 0;

		$aPeriodos = array();
		$mesActual = $mesIni;
		$i = 1;

		if ($anioIni == $anioFin) {
			while ($mesActual <= $mesFin) {
				$aPeriodos[$i]['mes'] = $mesActual;
				$aPeriodos[$i]['anio'] = $anioIni;
				$i++;
				$mesActual++;
			}
		} elseif ($anioIni < $anioFin) {
			while ($mesActual <= 12) {
				$aPeriodos[$i]['mes'] = $mesActual;
				$aPeriodos[$i]['anio'] = $anioIni;
				$i++;
				$mesActual++;
			}
			
			$anioActual = $anioIni+1;
			
			while ($anioActual < $anioFin) {
				$contador2 = 1;
				
				while ($contador2 <= 12) {
					$aPeriodos[$i]['mes'] = $contador2;
					$aPeriodos[$i]['anio'] = $anioActual;
					$i++;
					$contador2++;
				}
				
				$anioActual++;
			}
			
			$contador2 = 1;
			
			while ($contador2 <= $mesFin) {
				$aPeriodos[$i]['mes'] = $contador2;
				$aPeriodos[$i]['anio'] = $anioFin;
				$i++;
				$contador2++;
			}			
		}
		
		return $aPeriodos;
	}

	public function obtenerDatosSegunPeriodo ($aPeriodos, $d, $fechaInicio, $nombreProyecto, $informeDetalle = false) { // se llama tambien desde el informe detalle
		/* aquí obtengo los datos fijos de la bd que no necesitan cálculos */
		$aDatos = array();
		$aDatosInforme = array();
		$aInfoInforme = array();
		$aInformeMesAnterior = array();

		$facturacionMes = 0;
		$gastosOrigen = 0;
		$gastosPeriodo = 0;

		$facturacionPteFinMes = 0;
		$pteFacturacionFaltaContrato = 0;
		$pteFacturacionPartidas = 0;
		$pteFacturacionOtros = 0;
		$pteCertificacionSubProv = 0;
		$acopioEntregaCuenta = 0;

		foreach ($aPeriodos as $p) {
			$m = $p['mes'];
			$a = $p['anio'];

			$proyecto					= $d['D_CODIGO_PROYECTO'];
			$nombreObra					= $d['D_PROYECTO'];
			$razonSocialCliente			= $d['D_RAZON_SOCIAL_CLIENTE'];
			$importeContrato			= $d['D_IMPORTE_CONTRATO'];
			$cliente					= $d['D_CODIGO_CLIENTE'];				

			$facturacionMes				= $this->obtenerFacturacionMes($proyecto, $cliente, $m, $a, $facturacionMes, $informeDetalle);
			$facturacionOrigen			= $this->obtenerFacturacionOrigen($proyecto, $cliente, $m, $a, $informeDetalle);

			$ventaPendiente				= $d['D_IMPORTE_CONTRATO'] - $facturacionOrigen;

			$aTotalesGastos 			= $this->obtenerTotalGastos($proyecto, $m, $a, $gastosOrigen, $gastosPeriodo);
			$totalGastosOrigen			= $aTotalesGastos['gastosOrigen'];
			$totalGastosPeriodo			= $aTotalesGastos['gastosPeriodo'];
			$aGastos					= $this->obtenerGastos($proyecto, $m, $a, $gastosOrigen, $gastosPeriodo, $nombreProyecto);
			$gastosOrigen				= $aGastos['gastosOrigen'];
			$gastosPeriodo				= $aGastos['gastosPeriodo'];	
			$ultimoPeriodo				= $p;
		}

		/* obtener id del informe y enlace al informe-3 */
		$id = '';
		$enlace = '#';
		$select = 'TOP 1 ID_INFORME, ID_JEFE_OBRA';
    	$from = DB_APP.'InformesCuadreObras';
    	$where = "WHERE ID_OBRA='" . $proyecto . "' AND MES='" . $ultimoPeriodo['mes'] . "' AND YEAR='" . $ultimoPeriodo['anio'] . "' ";
    	$aInforme = $this->consulta($select, $from, $where);

    	if (is_array($aInforme)) {
    		$id = $aInforme[0]['ID_INFORME'];
    		$jefeObra = $aInforme[0]['ID_JEFE_OBRA'];

    		if ($id != '') {
				$aInfoInforme = $this->obtenerInfoInforme($id);

				$facturacionPteFinMes = $aInfoInforme['PF_CIERRE'];	
				$pteFacturacionFaltaContrato = $aInfoInforme['PF_FALTA_CONTRATO'];
				$pteFacturacionPartidas	= $aInfoInforme['PF_APROBACION'];
				$pteFacturacionOtros = $aInfoInforme['PF_OTROS'];
				$pteCertificacionSubProv = 0;
				$acopioEntregaCuenta = 0;

				/* pte de certificación a subcontratas o proveedores */
				if (isset($aInfoInforme['TOTAL_PENDIENTE_DE_PAGO'])) {		
					$pteCertificacionSubProv = $aInfoInforme['TOTAL_PENDIENTE_DE_PAGO'];
				}		

				/* acopio o entrega a cuenta contabilizados */
				if (isset($aInfoInforme['PAGO_ANTICIPADO_REALIZADO'])) {
					$acopioEntregaCuenta = $aInfoInforme['PAGO_ANTICIPADO_REALIZADO'];
				}

				if ($jefeObra != '') {
					$enlace = 'informe-3.php?id1=' . $id . '&id2=' . $jefeObra . '&admin=true';
				}

			}
    	}

		$aDatos['idInforme'] 					= $id;
		$aDatos['enlace']						= $enlace;
		$aDatos['proyecto'] 					= $proyecto;
		$aDatos['nombreObra']					= $nombreObra;
		$aDatos['razonSocialCliente']			= $razonSocialCliente;
		$aDatos['vigor']						= 'Si';
		$aDatos['importeContrato']				= $importeContrato;
		$aDatos['cliente']						= $cliente;				
		$aDatos['facturacionOrigen']			= $facturacionOrigen;
		$aDatos['ventaPendiente']				= $ventaPendiente;			
		$aDatos['facturacionMes']				= $facturacionMes;		
		$aDatos['aGastos']						= $aGastos;			
		$aDatos['gastosOrigen']					= $gastosOrigen;
		$aDatos['gastosPeriodo']				= $gastosPeriodo;
		$aDatos['totalGastosOrigen']			= $totalGastosOrigen;
		$aDatos['totalGastosPeriodo']			= $totalGastosPeriodo;
		$aDatos['ultimoPeriodo']				= $ultimoPeriodo;
		$aDatos['facturacionPteFinMes']			= $facturacionPteFinMes;
		$aDatos['pteFacturacionFaltaContrato']	= $pteFacturacionFaltaContrato;
		$aDatos['pteFacturacionPartidas']		= $pteFacturacionPartidas;
		$aDatos['pteFacturacionOtros']			= $pteFacturacionOtros;
		$aDatos['pteCertificacionSubProv']		= $pteCertificacionSubProv;
		$aDatos['acopioEntregaCuenta']			= $acopioEntregaCuenta;

		return $aDatos;
	}

	private function obtenerFacturacionOrigen ($proyecto, $cliente, $mes, $ejercicio, $informeDetalle = false) {
		$factOrigen = 0;

		$select = 'SUM(importeorigen) as importeorigen';
		$where = "WHERE CodigoEmpresa='" . $this->empresa . "' AND codigoproyecto='" . $proyecto . "' AND mes='" . $mes . "' AND ejercicio='" . $ejercicio . "'";

		if (!$informeDetalle) { // lo planteamos al revés, para solo tener que meterle el codigo de proyecto si NO es el informe Detalle
			$select = 'TOP 1 importeorigen';
			$where .= " AND codigocliente='" . $cliente . "' ";
		}

    	$from = DB_APP.'facturacion_origen';
    	$aFacturacionOrigen = $this->consulta($select, $from, $where);

    	if (is_array($aFacturacionOrigen)) {
    		$factOrigen = $aFacturacionOrigen[0]['importeorigen'];
    	}

    	return $factOrigen;
	}

	private function obtenerFacturacionMes ($proyecto, $cliente, $mes, $ejercicio, $facturacionMes, $informeDetalle = false) {
		$factMes = $facturacionMes; // se hace así para que no devulva cero si no encuentra la consulta

		$select = 'SUM(importemes) as importemes';
		$where = "WHERE CodigoEmpresa='" . $this->empresa . "' AND codigoproyecto='" . $proyecto . "' AND mes='" . $mes . "' AND ejercicio='" . $ejercicio . "'";

		if (!$informeDetalle) { // lo planteamos al revés, para solo tener que meterle el codigo de proyecto si NO es el informe Detalle
			$select = 'TOP 1 importemes';
			$where .= " AND codigocliente='" . $cliente . "' ";
		}

    	$from = DB_APP . 'facturacion_mes';
    	$aFacturacionMes = $this->consulta($select, $from, $where);

    	if (is_array($aFacturacionMes)) {
    		$factMes = $facturacionMes + $aFacturacionMes[0]['importemes'];
    	}

    	return $factMes;
	}

	private function obtenerGastos ($proyecto, $mes, $ejercicio, $gastosOrigen, $gastosPeriodo, $nombreProyecto) {
		$aGastos = array();

		$select = 'TOP 1 ImporteOrigen, ImporteMes';
    	$from = DB_APP . 'Costes_Mensuales';
    	$where = "WHERE CodigoEmpresa='" . $this->empresa . "' AND CodigoProyecto='" . $proyecto . "' AND Periodo='" . $mes . "' AND Ejercicio='" . $ejercicio . "' AND Proyecto = '" . $nombreProyecto . "'";
    	$aCostesMensuales = $this->consulta($select, $from, $where);

    	if (is_array($aCostesMensuales)) {
    		if (isset($aCostesMensuales[0]['ImporteOrigen'])) {
    			$valor = $aCostesMensuales[0]['ImporteOrigen'];
    			if (intval($valor) > 0) {
    				$gastosOrigen = $valor;
    			}
    		}
    		if (isset($aCostesMensuales[0]['ImporteMes'])) {
    			$valor = $aCostesMensuales[0]['ImporteMes'];
    			if (intval($valor) > 0) {
    				$gastosPeriodo = $gastosPeriodo + $valor;
    			}
    		}
    	}

		$aGastos = array('gastosOrigen' => $gastosOrigen, 'gastosPeriodo' => $gastosPeriodo);
		return $aGastos;
	}

	private function obtenerTotalGastos ($proyecto, $mes, $ejercicio, $gastosOrigen, $gastosPeriodo) {
		$aGastos = array();
		$gastosOrigen = 0;
		$gastosPeriodo = 0;

		$select = 'ImporteOrigen, ImporteMes';
    	$from = DB_APP . 'Costes_Mensuales';
    	$where = "WHERE CodigoEmpresa='" . $this->empresa . "' AND CodigoProyecto='" . $proyecto . "' AND Periodo='" . $mes . "' AND Ejercicio='" . $ejercicio . "'";
    	$aCostesMensuales = $this->consulta($select, $from, $where);

    	if (is_array($aCostesMensuales)) {
    		foreach ($aCostesMensuales as $costeMensual) {
    			$valorImporteOrigen = $costeMensual['ImporteOrigen'];
    			$gastosOrigen += $valorImporteOrigen;

    			$valorImporteMes = $costeMensual['ImporteMes'];
    			$gastosPeriodo += $valorImporteMes;
    		}
    	}

		$aGastos = array('gastosOrigen' => $gastosOrigen, 'gastosPeriodo' => $gastosPeriodo);
		return $aGastos;
	}

	public function calcularLineaFinalBloque ($aDatos, $fechaInicio) {
		/* resulta que la tabla no se usa a partir de facturación periodo, si no que se ponen los datos en la fila de "totales" del bloque */
		$totalGastosOrigen = 0;
		$totalGastosPeriodo = 0;
		$totalPrevisionFactOrigen = 0;
		$totalPrevisionFactPeriodo = 0;
		$beneficioOrigenPorcentaje = 0;
		$beneficioPeriodoPorcentaje = 0;

		/* total pte de facturación */
		$arrayAux = array($aDatos['facturacionPteFinMes'], $aDatos['pteFacturacionFaltaContrato'], $aDatos['pteFacturacionPartidas'], $aDatos['pteFacturacionOtros']);
		$totalPteFacturacion = $this->sumaArray($arrayAux);

		/* calcular total ingresos origen = Facturacion origen + Total pte facturacion */
		$arrayAux2 = array($this->totalFacturacionOrigen, $totalPteFacturacion);
		$totalPrevisionFactOrigen = $this->sumaArray($arrayAux2);	
		
		/* calcular total ingresos periodo = Facturacion periodo + Total pte facturacion - Total pte facturacion mes anterior */
		$arrayAux1 = array($this->totalFacturacionMes, $totalPteFacturacion);
		$valorAux1 = $this->sumaArray($arrayAux1);

		$aDatosInformeMesAnterior = $this->obtenerDatosInformePeriodoAnterior($aDatos['proyecto'], $fechaInicio);
		$pteCobroPeriodoAnterior = $aDatosInformeMesAnterior['pteCobroPeriodoAnterior'];
		$pteCertifMesAnterior = $aDatosInformeMesAnterior['pteCertificacionSubProv'];
		$acopioMesAnterior = $aDatosInformeMesAnterior['acopioEntregaCuenta'];
		$aInformeMesAnterior = $aDatosInformeMesAnterior['aInformeMesAnterior'];
		$valorAux2 = str_replace(',', '.', $pteCobroPeriodoAnterior) + 0;

		/* previsiones */

		$totalPrevisionFactPeriodo = $valorAux1 - $valorAux2;

		/* total prevision gastos origen = gastos origen + pte certificación - acopio */
		$totalGastosOrigen = $aDatos['totalGastosOrigen'] +  $aDatos['pteCertificacionSubProv'] - $aDatos['acopioEntregaCuenta'];

		/* total prevision gastos periodo = gastos periodo + pte certificacion - acopio - (total pte certificacion mes anterior - total acopio mes anterior) */
		$totalGastosPeriodo = $aDatos['totalGastosPeriodo'] + $aDatos['pteCertificacionSubProv'] - $aDatos['acopioEntregaCuenta'] - ($pteCertifMesAnterior - $acopioMesAnterior);

		/* beneficios */
		if ($totalGastosOrigen != 0) {
			$beneficioOrigenPorcentaje = (($totalPrevisionFactOrigen/$totalGastosOrigen) - 1) * 100;
		}

		if ($totalPrevisionFactOrigen == 0) {
			$totalPrevisionFactOrigen = $this->totalFacturacionOrigen;
		}

		if ($totalPrevisionFactPeriodo == 0) {
			$totalPrevisionFactPeriodo = $this->totalFacturacionMes;
		}

		if ($totalGastosPeriodo != 0) {
			$beneficioPeriodoPorcentaje = (($totalPrevisionFactPeriodo/$totalGastosPeriodo) - 1) * 100;
		} else if ($totalPrevisionFactPeriodo > 0) {
			$beneficioPeriodoPorcentaje = 100;
		}

		$beneficioOrigen = $totalPrevisionFactOrigen - $totalGastosOrigen;
		$beneficioPeriodo = $totalPrevisionFactPeriodo - $totalGastosPeriodo;

		/*Si el beneficio periodo es negativo su porcentaje también debe serlo,
		por lo que si es positivo le cambiamos el signo, 
		lo mismo si es positivo el beneficio periodo, también el porcentaje debe serlo*/
		if ($beneficioPeriodoPorcentaje > 0 && $beneficioPeriodo < 0) {
			$beneficioPeriodoPorcentaje = $beneficioPeriodoPorcentaje * -1;
		} else if ($beneficioPeriodoPorcentaje < 0 && $beneficioPeriodo > 0) {
			$beneficioPeriodoPorcentaje = $beneficioPeriodoPorcentaje * -1;
		}

		$aResultadosTotales = array(
			'importeContrato'				=>  $aDatos['importeContrato'],
			'facturacionOrigen'				=>  $aDatos['facturacionOrigen'],
			'ventaPendiente'				=>  $aDatos['ventaPendiente'],
			'facturacionMes'				=>  $aDatos['facturacionMes'],
			'gastosOrigen'					=>  $aDatos['totalGastosOrigen'],
			'gastosPeriodo'					=>  $aDatos['totalGastosPeriodo'],
			'facturacionPteFinMes'			=>	$aDatos['facturacionPteFinMes'],
			'pteFacturacionFaltaContrato'   =>	$aDatos['pteFacturacionFaltaContrato'],
			'pteFacturacionPartidas'		=>	$aDatos['pteFacturacionPartidas'],
			'pteFacturacionOtros'			=>	$aDatos['pteFacturacionOtros'],
			'totalPteFacturacion'			=>	$totalPteFacturacion,
			'totalPrevisionFactOrigen'		=>	$totalPrevisionFactOrigen,
			'totalPrevisionFactPeriodo'		=>	$totalPrevisionFactPeriodo,
			'pteCertificacionSubProv'		=>	$aDatos['pteCertificacionSubProv'],
			'acopioEntregaCuenta'			=>	$aDatos['acopioEntregaCuenta'],
			'totalGastosOrigen'				=>	$totalGastosOrigen,
			'totalGastosPeriodo'			=>	$totalGastosPeriodo,
			'beneficioOrigen'				=>	$beneficioOrigen,
			'beneficioPeriodo'				=>	$beneficioPeriodo,
			'beneficioOrigenPorcentaje'		=>	$beneficioOrigenPorcentaje,
			'beneficioPeriodoPorcentaje'	=>	$beneficioPeriodoPorcentaje
		);

		return $aResultadosTotales;
	}

	private function obtenerDatosInformePeriodoAnterior ($proyecto, $fechaInicio) {
		$aDatos = array();
		$pteCobroPeriodoAnterior = 0;
		$pteCertificacionSubProv = 0;
		$acopioEntregaCuenta = 0;
		$aInformesObrasAnterior = $this->obtenerInformeMesAnterior($proyecto, $fechaInicio);

    	if (is_array($aInformesObrasAnterior)) {
    		$id = $aInformesObrasAnterior[0]['ID_INFORME'];
			$aInformeAnterior = $this->obtenerInfoInforme($id);
			$arrayAux = array($aInformeAnterior['PF_CIERRE'], $aInformeAnterior['PF_FALTA_CONTRATO'], $aInformeAnterior['PF_APROBACION'], $aInformeAnterior['PF_OTROS']);
			$pteCobroPeriodoAnterior = $this->sumaArray($arrayAux);
			$pteCertificacionSubProv = $aInformeAnterior['TOTAL_PENDIENTE_DE_PAGO'];
			$acopioEntregaCuenta = $aInformeAnterior['PAGO_ANTICIPADO_REALIZADO'];
    	}

    	$aDatos = array(
    		'pteCobroPeriodoAnterior' => $pteCobroPeriodoAnterior, // total pte de facturacion, pero del periodo anterior
    		'pteCertificacionSubProv' => $pteCertificacionSubProv,
    		'acopioEntregaCuenta' => $acopioEntregaCuenta,
    		'aInformeMesAnterior' => $aInformesObrasAnterior
    	);
		
		return $aDatos;
	}

	private function obtenerInformeMesAnterior ($proyecto, $fechaInicio) {
		$fecha = explode('-', $fechaInicio);
		$mes = $fecha[0];
		$anio = $fecha[1];
		$fechaAnterior = date_create($anio . '-' . $mes . '-01 -1 months');
		$mesAnterior = date_format($fechaAnterior, 'm') + 0;
		$anioAnterior = date_format($fechaAnterior, 'Y') + 0;

		$select = 'TOP 1 ID_INFORME,ID_OBRA,ID_JEFE_OBRA,NOMBRE_OBRA,NOMBRE_JEFE_OBRA';
    	$from = DB_APP . 'InformesCuadreObras';
    	$where = "WHERE ID_OBRA='" . $proyecto . "' AND MES='" . $mesAnterior . "' AND YEAR='" . $anioAnterior . "' ";
    	$aInformesObrasAnterior = $this->consulta($select, $from, $where);

    	return $aInformesObrasAnterior;
	}

	private function obtenerInfoInforme ($id, $jefeObra = '') {
		$aInfo = array();
		// deberia de devolver solo una linea, pero en el caso del id: a0f6bcdfd63c66966888 devuelve 2 lineas iguales
		$select = 'TOP 1 [ID_UNICO],[ID_INFORME],[ID_JEFE_OBRA],[ID_OBRA],[MES],[YEAR],[FECHA_GRABACION],[PF_CIERRE],[PF_FALTA_CONTRATO],[PF_APROBACION],[PF_OTROS],[TOTAL_PENDIENTE_COBRO],[P_CERTIFICACION],[IMPORTE_P_CERTIFICACION],[MOTIVO_P_CERTIFICACION],[TOTAL_PENDIENTE_DE_PAGO],[ACOPIO_ENTREGA],[IMPORTE_ACOPIO_ENTREGA],[MOTIVO_ACOPIO_ENTREGA],[PAGO_ANTICIPADO_REALIZADO]';
    	$from = DB_APP . 'InformesCuadreObrasDatos';
    	$where = "WHERE ID_INFORME='" . $id . "'";
    	$aInformesObrasDatos = $this->consulta($select, $from, $where);

    	if (is_array($aInformesObrasDatos)) {
    		$aInfo['ID_INFORME']					= 	$aInformesObrasDatos[0]['ID_INFORME'];
			$aInfo['ID_JEFE_OBRA']					= 	$aInformesObrasDatos[0]['ID_JEFE_OBRA'];
			$aInfo['ID_OBRA']						= 	$aInformesObrasDatos[0]['ID_OBRA'];
			$aInfo['MES']							= 	$aInformesObrasDatos[0]['MES'];
			$aInfo['YEAR']							= 	$aInformesObrasDatos[0]['YEAR'];
			$aInfo['PF_CIERRE']						= 	$this->convertirNumero($aInformesObrasDatos[0]['PF_CIERRE']);
			$aInfo['PF_FALTA_CONTRATO']				= 	$this->convertirNumero($aInformesObrasDatos[0]['PF_FALTA_CONTRATO']);
			$aInfo['PF_APROBACION']					= 	$this->convertirNumero($aInformesObrasDatos[0]['PF_APROBACION']);
			$aInfo['PF_OTROS']						= 	$this->convertirNumero($aInformesObrasDatos[0]['PF_OTROS']);

			$arrayAux = array($aInfo['PF_CIERRE'], $aInfo['PF_FALTA_CONTRATO'], $aInfo['PF_APROBACION'], $aInfo['PF_OTROS']);
			$aInfo['TOTAL_PENDIENTE_COBRO'] 		= 	$this->sumaArray($arrayAux);
			$aInfo['TOTAL_PENDIENTE_DE_PAGO'] 		= 	0;
			$aInfo['PAGO_ANTICIPADO_REALIZADO'] 	= 	0; 
    	}

    	$select = 'TOP 1 ID_OBRA,ID_JEFE_OBRA,NOMBRE_OBRA,NOMBRE_JEFE_OBRA';
    	$from = DB_APP . 'InformesCuadreObras';
    	$where = "WHERE ID_INFORME='" . $id . "'";
    	if ($jefeObra != '') {
    		$where .= ' AND ID_JEFE_OBRA = ' . $jefeObra;
    	}
    	$aInformesObras = $this->consulta($select, $from, $where);

    	if (is_array($aInformesObras)) {
    		$aInfo['ID_OBRA']						=	$aInformesObras[0]['ID_OBRA'];
			$aInfo['ID_JEFE_OBRA']					=	$aInformesObras[0]['ID_JEFE_OBRA'];
			$aInfo['NOMBRE_OBRA']					=	$aInformesObras[0]['NOMBRE_OBRA'];
			$aInfo['NOMBRE_JEFE_OBRA']				=	$aInformesObras[0]['NOMBRE_JEFE_OBRA'];
    	}

    	if (is_array($aInformesObrasDatos)) {
    		// actualización 23/08/18. queremos devolver todas las lineas que haya con ese mismo id informe y sumar los ptes certificacion y acopio entrega de cada una de ellas
    		$select = '[P_CERTIFICACION],[IMPORTE_P_CERTIFICACION],[MOTIVO_P_CERTIFICACION],[ACOPIO_ENTREGA],[IMPORTE_ACOPIO_ENTREGA],[MOTIVO_ACOPIO_ENTREGA]';
	    	$from = DB_APP . 'InformesCuadreObrasDatos';
	    	$where = "WHERE ID_INFORME='" . $id . "'";
	    	$aInformesObrasDatos = $this->consulta($select, $from, $where);

    		$masInfo = $this->sumaObrasDatos($aInformesObrasDatos);
    		$aInfo['TOTAL_PENDIENTE_DE_PAGO']		= 	$masInfo['TOTAL_PENDIENTE_DE_PAGO'];
			$aInfo['PAGO_ANTICIPADO_REALIZADO']		= 	$masInfo['PAGO_ANTICIPADO_REALIZADO'];
    	}
		
		return $aInfo;		
	}

	private function convertirNumero ($n) {
		if ($n == '') {
			return 0;
		}

		if (strpos($n, ',') !== false) { // si contiene una coma, transformar en punto
            $n = str_replace(',' , '.' , $n);
        }

		return $n;
	}

	private function sumaObrasDatos ($aInformesObrasDatos) {
		$aInfo = array();
		$aInfo['TOTAL_PENDIENTE_DE_PAGO'] = 0;
		$aInfo['PAGO_ANTICIPADO_REALIZADO'] = 0;
		$contador1 = 1;
		$contador2 = 1;
		$sumatorio1 = '';
		$sumatorio2 = '';

		foreach ($aInformesObrasDatos as $datos) {
			$flagContador1 = false;
			$flagContador2 = false;

			if (isset($datos['P_CERTIFICACION'])) { 
				$aInfo['A'][$contador1]['P_CERTIFICACION_' . $contador1 . ''] = $datos['P_CERTIFICACION'];
				$flagContador1 = true;
			}
			if (isset($datos['MOTIVO_P_CERTIFICACION'])) { 
				$aInfo['A'][$contador1]['MOTIVO_P_CERTIFICACION_' . $contador1 . ''] = $datos['MOTIVO_P_CERTIFICACION'];
				$flagContador1 = true;
			}
			if (isset($datos['IMPORTE_P_CERTIFICACION'])) {
				$aInfo['A'][$contador1]['IMPORTE_P_CERTIFICACION_' . $contador1 . ''] = $datos['IMPORTE_P_CERTIFICACION'];
				$sumatorio1[] = $datos['IMPORTE_P_CERTIFICACION'];
				$flagContador1 = true;
			}		
			if (isset($datos['ACOPIO_ENTREGA'])) {
				$aInfo['B'][$contador2]['ACOPIO_ENTREGA_' . $contador2 . ''] = $datos['ACOPIO_ENTREGA'];
				$flagContador2 = true;
			}
			if (isset($datos['MOTIVO_ACOPIO_ENTREGA']))	{
				$aInfo['B'][$contador2]['MOTIVO_ACOPIO_ENTREGA_' . $contador2 . ''] = $datos['MOTIVO_ACOPIO_ENTREGA'];
				$flagContador2 = true;
			}	
			if (isset($datos['IMPORTE_ACOPIO_ENTREGA'])) {
				$aInfo['B'][$contador2]['IMPORTE_ACOPIO_ENTREGA_' . $contador2 . ''] = $datos['IMPORTE_ACOPIO_ENTREGA'];
				$sumatorio2[] = $datos['IMPORTE_ACOPIO_ENTREGA'];
				$flagContador2 = true;
			}
			if ($flagContador1) {
				$contador1++;
			}
			if ($flagContador2) {
				$contador2++;
			}
		}

		if (is_array($sumatorio1)) {
			$aInfo['TOTAL_PENDIENTE_DE_PAGO'] = $this->sumaArray($sumatorio1); // sumatorio de los ptes de certificación 
		}

		if (is_array($sumatorio2)) { 
			$aInfo['PAGO_ANTICIPADO_REALIZADO'] = $this->sumaArray($sumatorio2); // sumatorio de acopio o entrega a cuenta contabilizados
		}

		return $aInfo;
	}

	private function sumaArray ($array) {
		$sum = 0;
		foreach ($array as $a) {		
			if ($a == '') {
				$a = 0;
			}
			$a = str_replace(',', '.', $a) + 0;
			$sum = $a + $sum;			
		}		
		return $sum;
	}

	private function sumaArrayClaves ($array) {
		$aSuma = array();
		if (is_array($array)) {
			foreach ($array as $k => $subArray) {
			  	foreach ($subArray as $id => $value) {
			  		if (is_numeric($value)) {
				    	if (!isset($aSuma[$id])) { // de primeras hay que darle un valor
				    		$aSuma[$id] = 0;
				    	}
				    	$aSuma[$id] += $value;
				    }
			  	}
			}
		}
		return $aSuma;
	}

	// private function anadirArray(&$array, $aLinea) {
	// 	$array[] = array(
	// 					'importeContrato'				=> $aLinea['importeContrato'],		
	// 					'facturacionOrigen'				=> $aLinea['facturacionOrigen'],
	// 					'ventaPendiente'				=> $aLinea['ventaPendiente'],		
	// 					'facturacionMes'				=> $aLinea['facturacionMes']
	// 				);
	// 	return $array;
	// }

    public function imprimirExcel ($lineas) {
    	header('Content-type: application/vnd.ms-word; charset=utf-8');
		header("Content-Disposition: attachment;Filename=\"Informe rendimiento de obras.xls\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);

		$csv  = 'sep=;'."\n\n";

		$csv .= 'Código'.$this->separador.'Obra'.$this->separador.'Cliente'.$this->separador.'Vigor'.$this->separador.
				'Importe contrato'.$this->separador.'Facturación a origen'.$this->separador.'Venta pendiente'.$this->separador.
				'Facturación periodo'.$this->separador.'Facturación pendiente desde día de cierre hasta fin de periodo'.$this->separador.
				'Pendiente de facturación por falta de contrato'.$this->separador.'Pendiente de facturación por aprobación de partidas'.
				$this->separador.'Pendiente de facturación por otros motivos'.$this->separador.'Total pendiente de facturación'.
				$this->separador.'Total previsión de facturación (ORIGEN)'.$this->separador.'Total previsión de facturación (PERIODO)'.$this->separador.'Gastos contabilizados (ORIGEN)'.$this->separador.'Gastos contabilizados (PERIODO)'.$this->separador.
				'Pendiente de certificación a subcontratas o proveedores'.$this->separador.'Acopio o entrega a cuenta contabilizados'.
				$this->separador.'Total previsión de gastos (ORIGEN)'.$this->separador.'Total previsión de gastos (PERIODO)'.$this->separador.
				'Beneficio (ORIGEN)'.$this->separador.'Beneficio (PERIODO)'.$this->separador.'Beneficio % (ORIGEN)'.$this->separador.
				'Beneficio % (PERIODO)'.$this->separador
				."\n"; 
		
		$csv .= $lineas;

		$csv = utf8_decode($csv);
		echo $csv;
		exit;
    }

    public function ejecutarProcedimiento() {
    	$nombre = DB_APP.'_X_Traspaso_Jefes_Obra';
    	$aEjecutar[] = $this->procedimiento($nombre);

    	$nombre = DB_APP.'_X_Traspaso_clientes_obras';
    	$aEjecutar[] = $this->procedimiento($nombre);

    	$nombre = DB_APP.'_X_Traspaso_Importes_Contrato';
    	$aEjecutar[] = $this->procedimiento($nombre);

    	return json_encode(array('aEjecutar' => $aEjecutar));
	}
}