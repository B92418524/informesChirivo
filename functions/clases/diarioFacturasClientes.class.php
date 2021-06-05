<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class diarioFacturasClientes extends bd {

	private $separador;

    public function __construct() {
        parent::__construct();
        $this->separador = ';';
    }

    public function pintarSelProyectos() {
    	$html = '<option value="">Todos los proyectos</option>';

    	$select = 'DISTINCT CodigoProyecto, Proyecto';
    	$from = DB_APP.'pca';
    	$where = "WHERE CodigoEmpresa='".$this->empresa."' AND CodigoProyecto <> '' AND Proyecto is not NULL AND CodigoProyecto <> '9015'";
    	$order = 'ORDER BY CodigoProyecto, Proyecto ASC';
    	$aProyectos = $this->consulta($select, $from, $where, $order);
		if (is_array($aProyectos)) {
			foreach($aProyectos as $a) {
				$html .= '<option value="'.$a['CodigoProyecto'].'">'.$a['CodigoProyecto'].' - '.$a['Proyecto'].'</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarSelClientes() {
    	$html = '<option value="">Todos los clientes</option>';

    	$select = 'DISTINCT CodCli, RazonSocial';
    	$from = DB_APP.'clientes';
    	$where = "WHERE CodigoEmpresa='".$this->empresa."'";
    	$order = 'ORDER BY RazonSocial ASC';
    	$aClientes = $this->consulta($select, $from, $where, $order);
		if (is_array($aClientes)) {
			foreach($aClientes as $a) {
				$html .= '<option value="'.$a['CodCli'].'">'.$a['CodCli'].' - '.$a['RazonSocial'].'</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarTabla($filtros, $excel = false) {
    	$html = '';
    	$lineas = '<tr><td colspan="15" style="text-align:center">No hay registros.</td></tr>';
		$csv = ''; // en el caso de tener que llenar las lineas para el excel

    	$select = '[CodigoEmpresa],[EjercicioFactura],[SerieFactura],[NumeroFactura],[FechaFactura],[CodigoCliente],[RazonSocial],[BaseImponible],[TotalIva],[ImporteRetencion],[ImporteLiquido],[IRetGar],[CodigoProyecto],[Proyecto],[CodigoSeccion],[CodigoDepartamento],[TipoLineaDoc],[MovPosicion],[NCertificacion]';
    	$from = DB_SAGE.DB_PREFIJO.'TablaDiario';
    	$where = "WHERE CodigoEmpresa='".$this->empresa."' " . $this->obtenerQueryFiltros($filtros);
    	$order = 'ORDER BY FechaFactura ASC';
    	$aTabla = $this->consulta($select, $from, $where, $order);
		
		if (is_array($aTabla)) {
			$lineas = ''; // vaciarlo antes
			$ejercicioActual = '-1';
			$primeraVez = true;

			// sumatorio de cada bloque de ejercicios
			$aTotalEjercicio = array(); 
			$aTotalEjercicio['totalBase'] = 0;
			$aTotalEjercicio['totalIva'] = 0;
			$aTotalEjercicio['totalRetencion'] = 0;
			$aTotalEjercicio['totalLiquido'] = 0;

			// sumatorio final de todas las sumas
			$aTotales = array(); 
			$aTotales['totalBase'] = 0;
			$aTotales['totalIva'] = 0;
			$aTotales['totalRetencion'] = 0;
			$aTotales['totalLiquido'] = 0;

			foreach ($aTabla as $a) {
				$filaSumatorio = '';
				$filaTitulo = '';
				$bordeFinEjercicio = '';

				if ($ejercicioActual != $a['EjercicioFactura']) {

					if (!$primeraVez) {
						$titulo = 'Total ejercicio '.$ejercicioActual;

						/* si es la primera "tabla", no hay que pintar el footer anterior del sumatorio */
						if ($excel) {
							$csv .= $this->pintarSumatorioExcel($titulo, $aTotalEjercicio);
						} else {
							$lineas .= $this->pintarSumatorio($titulo, $aTotalEjercicio);
						}				

						$aTotales['totalBase'] 		+= $aTotalEjercicio['totalBase'];
						$aTotales['totalIva'] 		+= $aTotalEjercicio['totalIva'];
						$aTotales['totalRetencion'] += $aTotalEjercicio['totalRetencion'];
						$aTotales['totalLiquido'] 	+= $aTotalEjercicio['totalLiquido'];
					}

					$ejercicioActual = $a['EjercicioFactura'];
					$filaTitulo = 
						
					$bordeFinEjercicio = 'border-top:3px solid black';

					if ($excel) {
						$csv .= $this->pintarTituloEjercicioExcel($ejercicioActual);
					} else {
						$lineas .= $this->pintarTituloEjercicio($ejercicioActual);
					}

					$aTotalEjercicio['totalBase'] 		= 0;
					$aTotalEjercicio['totalIva']		= 0;
					$aTotalEjercicio['totalRetencion'] 	= 0;
					$aTotalEjercicio['totalLiquido'] 	= 0;
				}

				if ($excel) {
					$csv .= $this->pintarLineasExcel($a);
				} else {
					$lineas .= $this->pintarLineas($a, $bordeFinEjercicio);
				}

				$aTotalEjercicio['totalBase']		+= $a['BaseImponible'];
				$aTotalEjercicio['totalIva']		+= $a['TotalIva'];
				$aTotalEjercicio['totalRetencion'] 	+= $a['IRetGar'];
				$aTotalEjercicio['totalLiquido']	+= $a['ImporteLiquido'];

				$primeraVez = false;
			}

			// el ultimo siempre se queda sin sumatorio del bloque
			$titulo = 'Total ejercicio '.$ejercicioActual;
			if ($excel) {
				$csv .= $this->pintarSumatorioExcel($titulo, $aTotalEjercicio);
			} else {
				$lineas .= $this->pintarSumatorio($titulo, $aTotalEjercicio);
			}

			$aTotales['totalBase'] 		+= $aTotalEjercicio['totalBase'];
			$aTotales['totalIva'] 		+= $aTotalEjercicio['totalIva'];
			$aTotales['totalRetencion'] += $aTotalEjercicio['totalRetencion'];
			$aTotales['totalLiquido'] 	+= $aTotalEjercicio['totalLiquido'];

			// pintar sumatorio final
			$titulo = 'TOTAL IMPORTES';
			if ($excel) {
				$csv .= $this->pintarSumatorioExcel($titulo, $aTotales, true);
			} else {
				$lineas .= $this->pintarSumatorio($titulo, $aTotales, true);
			}
		}

		if ($excel) {
			$this->imprimirExcel($csv);
		} else {
			$tabla = '<table id="tabla" class="table table-striped table-bordered table-hover" style="border:0 !important;">
	                <thead style="background-color:#fff;">
			            <tr style="border:solid 1px silver;">
			                <th style="min-width:20px;">Serie</th>
			                <th style="min-width:10px;">Factura</th>
			                <th style="min-width:100px;">Fecha</th>
			                <th style="min-width:10px;">Código cliente</th>
			                <th style="min-width:200px;">Razón social</th>
			                <th style="min-width:10px;">Certificación</th>
			                <th style="min-width:20px;">Código proyecto</th>
			                <th style="min-width:200px;">Proyecto</th>
			                <th style="min-width:20px;">Contrato</th>
			                <th style="min-width:20px;">Anexo</th>
			                <th style="min-width:20px;text-align:right;">Base IVA</th>
			                <th style="min-width:20px;text-align:right;">Total IVA</th>
			                <th style="min-width:20px;text-align:right;">Ret. garantía</th>
			                <th style="min-width:20px;text-align:right;">Total líquido</th>
			                <th style="min-width:20px;">Tipo</th>
			            </tr>
		        	</thead>
			        <tbody>
			            '.$lineas.'      
			        </tbody>
		        </table>';

			return json_encode(array('html' => $tabla, 'consulta' => 'select '. $select.' from '.$from.' '.$where.$order));
		}
    }

     private function pintarLineas($a, $bordeFinEjercicio) {
     	$fechaFactura = '';
		if (isset($a['FechaFactura']) && $a['FechaFactura'] != '') {
			$fechaFactura = $a['FechaFactura']->format('d-m-Y');
		}

    	$linea = 
		'<tr style="'.$bordeFinEjercicio.'">
			<td style="min-width:20px">'.$a['SerieFactura'].'</td>
			<td style="min-width:20px">'.$a['NumeroFactura'].'</td>
			<td style="min-width:100px">'.$fechaFactura.'</td>
			<td style="min-width:30px">'.$a['CodigoCliente'].'</td>
			<td style="min-width:200px">'.$a['RazonSocial'].'</td>
			<td style="min-width:40px">'.$a['NCertificacion'].'</td>
			<td style="min-width:30px">'.$a['CodigoProyecto'].'</td>
			<td style="min-width:200px">'.$a['Proyecto'].'</td>
			<td style="min-width:50px">'.$a['CodigoSeccion'].'</td>
			<td style="min-width:50px">'.$a['CodigoDepartamento'].'</td>
			<td style="min-width:100px;text-align:right;">'.$this->formatoNumero($a['BaseImponible'], true, false, 2).'</td>
			<td style="min-width:100px;text-align:right;">'.$this->formatoNumero($a['TotalIva'], true, false, 2).'</td>
			<td style="min-width:100px;text-align:right;">'.$this->formatoNumero($a['IRetGar'], true, false, 2).'</td>
			<td style="min-width:100px;text-align:right;">'.$this->formatoNumero($a['ImporteLiquido'], true, false, 2).'</td>
			<td style="min-width:20px">'.$a['TipoLineaDoc'].'</td>
		</tr>';

		return $linea;
    }

    private function pintarLineasExcel($a) {
    	$fechaFactura = '';
		if (isset($a['FechaFactura']) && $a['FechaFactura'] != '') {
			$fechaFactura = $a['FechaFactura']->format('d-m-Y');
		}

    	$csv =
			'"'		.trim($a['SerieFactura'])															.'"'.$this->separador.''.
			'"'		.trim($a['NumeroFactura'])															.'"'.$this->separador.''.
			'"'		.trim($fechaFactura)																.'"'.$this->separador.''.
			'"'		.trim($a['CodigoCliente'])															.'"'.$this->separador.''.
			'"'		.trim($a['RazonSocial'])															.'"'.$this->separador.''.
			'"'		.trim($a['NCertificacion'])															.'"'.$this->separador.''.
			'"'		.trim($a['CodigoProyecto'])															.'"'.$this->separador.''.
			'"'		.trim($a['Proyecto'])																.'"'.$this->separador.''.
			'"'		.trim($a['CodigoSeccion'])															.'"'.$this->separador.''.
			'"'		.trim($a['CodigoDepartamento'])														.'"'.$this->separador.''.
			''		.$this->formatoNumero($a['BaseImponible'], true, false, 2, true)					.''.$this->separador.''.
			''		.$this->formatoNumero($a['TotalIva'], true, false, 2, true)							.''.$this->separador.''.
			''		.$this->formatoNumero($a['IRetGar'], true, false, 2, true)							.''.$this->separador.''.
			''		.$this->formatoNumero($a['ImporteLiquido'], true, false, 2, true)					.''.$this->separador.''.
			'"'		.trim($a['TipoLineaDoc'])															.'"'.$this->separador.''.
			"\n";

		return $csv;
    }

    private function pintarSumatorio($titulo, $aSumatorio, $final = false) {
    	$filaVacia = '';
    	$fondo = '';

    	if ($final) {
    		$filaVacia = '<tr style="border:0;height:20px;">
							<td colspan="15" style="border:0;height:50px;background-color:#fff !important;text-align:left"></td>
					  	</tr>';
    		$fondo = 'background-color:#96C7CE';
    	}

    	$lineaTotales = 
    		$filaVacia .
			'<tr style="border:0;height:20px;'.$fondo.'">
				<td colspan="4"><b>'.$titulo.'</b></td>
				<td colspan="6"></td>
				<td style="text-align:right"><b>'.$this->formatoNumero($aSumatorio['totalBase'], true, false, 2).'</b></td>
				<td style="text-align:right"><b>'.$this->formatoNumero($aSumatorio['totalIva'], true, false, 2).'</b></td>
				<td style="text-align:right"><b>'.$this->formatoNumero($aSumatorio['totalRetencion'], true, false, 2).'</b></td>
				<td style="text-align:right"><b>'.$this->formatoNumero($aSumatorio['totalLiquido'], true, false, 2).'</b></td>
				<td></td>
		  	</tr>';

		return $lineaTotales;
    }

    private function pintarSumatorioExcel($titulo, $aSumatorio, $final = false) {
    	$filaVacia = '';

    	if ($final) {
    		$filaVacia = '""'.$this->separador.''."\n";
    	}

    	$csv =
    		$filaVacia .
			'"'.$titulo.'"'																				.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			'""'																						.$this->separador.''.
			''		.$this->formatoNumero($aSumatorio['totalBase'], true, false, 2, true)				.''.$this->separador.''.
			''		.$this->formatoNumero($aSumatorio['totalIva'], true, false, 2, true)				.''.$this->separador.''.
			''		.$this->formatoNumero($aSumatorio['totalRetencion'], true, false, 2, true)			.''.$this->separador.''.
			''		.$this->formatoNumero($aSumatorio['totalLiquido'], true, false, 2, true)			.''.$this->separador.''.
			"\n";

		return $csv;
    }

    private function pintarTituloEjercicio($ejercicio) {
    	$lineaTitulo = 
		'<tr style="border:0;height:20px;">
			<td colspan="15" style="border:0;height:50px;background-color:#fff !important;text-align:left"></td>
	  	</tr>
	  	<tr style="border:0;height:20px;">
			<td colspan="15" style="border:0;height:50px;background-color:#eee !important;text-align:left">
				<b>EJERCICIO '.$ejercicio.'</b>
			</td>
	  	</tr>';

		return $lineaTitulo;
    }

    private function pintarTituloEjercicioExcel($ejercicio) {
    	$csv =
    		'""'.$this->separador.''."\n".
    		'""'.$this->separador.''."\n".
			'"EJERCICIO '.$ejercicio.'"'																	.$this->separador.''.
			"\n";

		return $csv;
    }

    private function obtenerQueryFiltros($filtros) {
    	$where = '';
    	$fechaInicio = $filtros['fechaInicio'];
    	$fechaFin = $filtros['fechaFin'];
    	$proyecto = $filtros['proyecto'];
    	$cliente = $filtros['cliente'];

    	$nuevaFechaInicio = '01-'.$fechaInicio;
		$nuevaFechaFin = '01-'.$fechaFin;

    	if ($fechaInicio != '' && $fechaFin != '') {
    		$where .= 
    			" AND (
    				FechaFactura 
    					BETWEEN
    						'".$nuevaFechaInicio."'
    						AND 
    						dateadd(day, -1, dateadd(month, 1, dateadd(day, 1 - day('".$nuevaFechaFin."'), '".$nuevaFechaFin."')))
    			) ";

    	} else if ($fechaInicio != '' && $fechaFin == '') {
			$where .= " AND FechaFactura >= '".$nuevaFechaInicio."' ";

    	} else if ($fechaInicio == '' && $fechaFin != '') {
			$where .= " AND FechaFactura <= dateadd(day, -1, dateadd(month, 1, dateadd(day, 1 - day('".$nuevaFechaFin."'), '".$nuevaFechaFin."'))) ";
    	}

		/* proyecto */
    	if ($proyecto != '') {
			$where .= " AND CodigoProyecto = '".$proyecto."' ";
		}

		/* cliente */
    	if ($cliente != '') {
			$where .= " AND CodigoCliente = '".$cliente."' ";
		}

		return $where;
    }

    private function imprimirExcel($lineas) {
    	header('Content-type: application/vnd.ms-word; charset=utf-8');
		header("Content-Disposition: attachment;Filename=\"Informe diario de facturas de clientes.xls\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);

		$csv  = 'sep=;'."\n\n";

		$csv .= 'Serie'.$this->separador.'Factura'.$this->separador.'Fecha'.$this->separador.'Código cliente'.$this->separador.
				'Razón social'.$this->separador.'Certificación'.$this->separador.'Código proyecto'.$this->separador.'Proyecto'.$this->separador.
				'Contrato'.$this->separador.'Anexo'.$this->separador.'Base IVA'.$this->separador.'Total IVA'.$this->separador.'Ret. garantía'.
				$this->separador.'Total líquido'.$this->separador.'Tipo'.$this->separador
				."\n"; 
		
		$csv .= $lineas;

		$csv = utf8_decode($csv);
		echo $csv;
		exit;
    }

    public function ejecutarProcedimiento($mes) {
    	$nombre = DB_APP.'_X_Traspaso_FacturacionObras ' . $mes;
    	$aEjecutar = $this->procedimiento($nombre);
    	return json_encode(array('aEjecutar' => $aEjecutar));
	}
}
