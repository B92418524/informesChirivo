<?php

class facturacionGlobal extends bd {

    public function __construct() {
        parent::__construct();
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

    public function pintarSelProveedores() {
    	$html = '<option value="">Todos los proveedores</option>';

    	$select = 'DISTINCT CodigoProveedor, Razonsocial';
    	$from = DB_APP.'proveedores';
    	$where = "WHERE CodigoEmpresa='".$this->empresa."' AND CodigoProveedor <> '' AND Razonsocial is not NULL";
    	$order = 'ORDER BY CodigoProveedor, Razonsocial ASC';
    	$aProveedores = $this->consulta($select, $from, $where, $order);
		if (is_array($aProveedores)) {
			foreach($aProveedores as $a) {
				$html .= '<option value="'.$a['CodigoProveedor'].'">'.$a['CodigoProveedor'].' - '.$a['Razonsocial'].'</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarTabla($filtros, $excel = false) {
    	$detalle = $filtros['checkDetalle'];
    	$filasCabecera = '';
    	$totalColumnas = '5';

    	if ($detalle == '1') {
    		$html = $this->tablaDetalle($filtros, $excel);
    		$filasCabecera = '	<th style="min-width:20px;">Código proyecto</th>
			                	<th style="min-width:500px;">Proyecto</th>';
    		$totalColumnas = '7';
    	} else {
    		$html = $this->tablaSinDetalle($filtros, $excel);
    	}

    	if ($html == '') {
    		$html = '<tr><td colspan="'.$totalColumnas.'" style="text-align:center">No hay registros.</td></tr>';
    	}

    	$tabla = 	'<table id="tabla" class="table table-striped table-bordered table-hover" style="border:0 !important;">
		                <thead style="background-color:#fff;">
				            <tr style="border:solid 1px silver;">
				                <th style="min-width:20px;">#</th>
				                <th style="min-width:20px;">Código proveedor</th>
				                <th style="min-width:200px;">Nombre proveedor</th>
				                '.$filasCabecera.'
				                <th style="min-width:20px;text-align:right;">Importe global facturado</th>
				                <th style="min-width:20px;text-align:right;">Porcentaje</th>                                              
				            </tr>
			        	</thead>
				        <tbody>
				            '.$html.'      
				        </tbody>
			        </table>';

		return json_encode(array('html' => $tabla));
    }

    private function tablaDetalle($filtros, $excel) {
    	$html = '';
    	$total = 0;
    	$contador = 1;

    	/* para no hacer los calculos con javascript y además que estén calculados para el excel, con consulta de sql es imposible pq hay un group by, asi que recorremos dos veces esta tabla */
    	$select = 'SUM(BaseImponible) as Total';
    	$from = DB_APP.DB_PREFIJO.'Facturacion_Global_Proveedores_Detalle';
    	$where = 'WHERE CodigoEmpresa = ' . $this->empresa . " " . $this->obtenerQueryFiltros($filtros);
    	$aTotal = $this->consulta($select, $from, $where);
    	if (is_array($aTotal)) {
    		$total = $aTotal[0]['Total'];
    	}

    	$select = 'CodigoProveedor,RazonSocial,CodigoProyecto,Proyecto,BaseImponible';
    	$order = 'ORDER BY CodigoProveedor, RazonSocial ASC';
    	$aTabla = $this->consulta($select, $from, $where, $order);
		if ($excel) {
    		$this->imprimirExcel($aTabla, $total, true);
    	} else {
    		if (is_array($aTabla)) {
				$proveedorActual = '-1';
				$sumaProveedor = 0;
				$sumaPorcentajeProveedor = 0;
				$inicioTabla = true;

				foreach ($aTabla as $a) {
					$baseImponible = $a['BaseImponible'];
					$porcentaje = $baseImponible * 100 / $total;

					if ($proveedorActual != $a['CodigoProveedor']) {
						// si son distintos Y NO HA EMPEZADO A SUMAR == 0, es la primera vez que entra aquí, por lo que pintar una fila con la sumatoria
						if ($sumaProveedor != 0 && !$inicioTabla) {
							$html .=
								'<tr style="background-color:#bbb;font-weight:bold;">
									<td></td>
									<td>'.$proveedorActual.'</td>
									<td></td>
									<td colspan="2"></td>
									<td style="text-align:right">'.$this->formatoNumero($sumaProveedor, true, false, 2).'</td>
									<td style="text-align:right">'.$this->formatoNumero($sumaPorcentajeProveedor, true, false, 2, false, true).'</td>
								</tr>';
						}
						$sumaProveedor = 0;
						$sumaPorcentajeProveedor = 0;
						$proveedorActual = $a['CodigoProveedor'];
						$inicioTabla = false;
					}

					$html .= 
							'<tr>
								<td style="min-width:20px">'.$contador.'</td>
								<td style="min-width:20px">'.$a['CodigoProveedor'].'</td>
								<td style="min-width:200px">'.$a['RazonSocial'].'</td>
								<td style="min-width:20px">'.$a['CodigoProyecto'].'</td>
								<td style="min-width:500px">'.$a['Proyecto'].'</td>
								<td style="min-width:20px;text-align:right">'.$this->formatoNumero($baseImponible, true, false, 2).'</td>
								<td style="min-width:20px;text-align:right">'.$this->formatoNumero($porcentaje, true, false, 2, false, true).'</td>
							</tr>';

					$sumaProveedor += $baseImponible;
					$sumaPorcentajeProveedor += $porcentaje;
					$contador++;
				}

				/* el ultimo nunca le saldrá la linea de totales */
				$html .=
						'<tr style="background-color:#bbb;font-weight:bold;">
							<td></td>
							<td>'.$proveedorActual.'</td>
							<td>'.$razonSocialActual.'</td>
							<td colspan="2"></td>
							<td style="text-align:right">'.$this->formatoNumero($sumaProveedor, true, false, 2).'</td>
							<td style="text-align:right">'.$this->formatoNumero($sumaPorcentajeProveedor, true, false, 2, false, true).'</td>
						</tr>';

				$html .= 
						'<tr style="background:#96C7CE">
							<td style="min-width:20px"></td>
							<td style="min-width:20px"></td>
							<td style="min-width:200px"></td>
							<td style="min-width:20px"></td>
							<td style="min-width:500px"></td>
							<td style="min-width:20px;text-align:right"><b>'.$this->formatoNumero($total, true, false, 2).'</b></td>
							<td style="min-width:20px"></td>
						</tr>';
			}
		}
		return $html;
    }

    private function tablaSinDetalle($filtros, $excel) {
    	$html = '';
    	$total = 0;
    	$contador = 1;

    	$select = 'SUM(BaseImponible) as Total';
    	$from = DB_APP.DB_PREFIJO.'Facturacion_Global_Proveedores';
    	$where = 'WHERE CodigoEmpresa = ' . $this->empresa . " " . $this->obtenerQueryFiltros($filtros);
    	$aTotal = $this->consulta($select, $from, $where);
    	if (is_array($aTotal)) {
    		$total = $aTotal[0]['Total'];
    	}

    	$select = 'CodigoProveedor,RazonSocial,SUM(Baseimponible) as BaseImponible';
    	$group = 'GROUP BY CodigoProveedor, RazonSocial';
    	$order = 'ORDER BY CodigoProveedor, RazonSocial ASC';
    	$aTabla = $this->consulta($select, $from, $where, $group, $order);
		if ($excel) {
    		$this->imprimirExcel($aTabla, $total, false);
    	} else {
    		if (is_array($aTabla)) {
				foreach ($aTabla as $a) {
					$baseImponible = $a['BaseImponible'];
					$porcentaje = $baseImponible * 100 / $total;

					$html .= 
							'<tr>
								<td style="min-width:20px">'.$contador.'</td>
								<td style="min-width:20px">'.$a['CodigoProveedor'].'</td>
								<td style="min-width:200px">'.$a['RazonSocial'].'</td>
								<td style="min-width:20px;text-align:right">'.$this->formatoNumero($baseImponible, true, false, 2).'</td>
								<td style="min-width:20px;text-align:right">'.$this->formatoNumero($porcentaje, true, false, 2, false, true).'</td>
							</tr>';

					$contador++;
				}

				$html .= 
						'<tr style="background:#96C7CE">
							<td style="min-width:20px"></td>
							<td style="min-width:20px"></td>
							<td style="min-width:200px"></td>
							<td style="min-width:20px;text-align:right"><b>'.$this->formatoNumero($total, true, false, 2).'</b></td>
							<td style="min-width:20px"></td>
						</tr>';
			}
		}
		return $html;
    }

    private function obtenerQueryFiltros($filtros) {
    	$where = '';
    	$fechaInicio = $filtros['fechaInicio'];
    	$fechaFin = $filtros['fechaFin'];
    	$proyecto = $filtros['proyecto'];
    	$proveedor = $filtros['proveedor'];

    	/* fecha inicio */
    	if ($fechaInicio != '') {
    		$aFechaInicio = explode('-', $fechaInicio);
    		$mesIni = $aFechaInicio[0] + 0;
			$anioIni = $aFechaInicio[1] + 0;
			$where .= " AND MONTH(FechaFactura) >= '".$mesIni."' AND YEAR(FechaFactura) >= '".$anioIni."'";
		} 

		/* fecha fin */
    	if ($fechaFin != '') {
			$aFechaFin = explode('-', $fechaFin);
			$mesFin = $aFechaFin[0] + 0;
			$anioFin = $aFechaFin[1] + 0;
			$where .= " AND MONTH(FechaFactura) <= '".$mesFin."' AND YEAR(FechaFactura) <= '".$anioFin."'";
		} 

		/* proyecto */
    	if ($proyecto != '') {
			$where .= " AND CodigoProyecto = '".$proyecto."' ";
		}

		/* proveedor */
    	if ($proveedor != '') {
			$where .= " AND CodigoProveedor = '".$proveedor."' ";
		}

		return $where;
    }

    private function pintarSelCambiarEstadoFacturacion($estadoFacturacion) {
    	$html = '<select class="selCambiarEstadoFacturacion form-control" style="min-width:180px">';

		$ids = array_keys($this->aEstadosFacturacion);
		$i = 0;
		foreach ($this->aEstadosFacturacion as $e) {
			$seleccionado = '';

			if ($estadoFacturacion != '' && $estadoFacturacion == $ids[$i]) {
				$seleccionado = ' selected="selected" ';
			} else {
				$seleccionado = '';
			}
			
			$html .= '<option value="'.$ids[$i].'" '.$seleccionado.'>'. $e .'</option>';
			$i++;
		}

		$html .= '</select>';

		return $html;
    }

    private function pintarTextoActivo($activo) {
    	$texto = '';
    	if ($activo == '-1') {
    		$texto = 'SÍ';
    	} else if ($activo == '0') {
    		$texto = 'NO';
    	}
    	return $texto;
    }

    public function cambiarEstadoFacturacion($ejercicio, $mes, $codigoProyecto, $anexo, $nuevoEstado) {
    	$valores = 'EstadoFacturacion='.$nuevoEstado;
    	$tabla = DB_APP.'facturacionobras';
    	$where = "WHERE Ejercicio=".$ejercicio." AND Mes=".$mes." AND CodigoProyecto='".$codigoProyecto."' AND Proyecto='".$anexo."'";
    	$aModificacion = $this->modificar($valores, $tabla, $where);
    	$flag = $aModificacion['flag'];
    	return json_encode(array('aModificacion' => $aModificacion, 'flag' => $flag));
    }

	public function cambiarObservaciones($ejercicio, $mes, $codigoProyecto, $anexo, $nuevaObservacion) {
		$valores = "ObservacionesProyecto='".$nuevaObservacion."'";
    	$tabla = DB_APP.'facturacionobras';
    	$where = "WHERE Ejercicio=".$ejercicio." AND Mes=".$mes." AND CodigoProyecto='".$codigoProyecto."' AND Proyecto='".$anexo."'";
    	$aModificacion = $this->modificar($valores, $tabla, $where);
    	$flag = $aModificacion['flag'];
    	return json_encode(array('aModificacion' => $aModificacion, 'flag' => $flag));
	}

    public function imprimirExcel($aTabla, $total, $detalle) {
    	header('Content-type: application/vnd.ms-word; charset=utf-8');
		header("Content-Disposition: attachment;Filename=\"Informe facturacion global proveedores.xls\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);

		$columnasProyecto = '';
		if ($detalle) {
			$columnasProyecto = 'Código proyecto'.$separador.'Proyecto'.$separador;
		}

    	$separador = ';';
		$csv  = 'sep=;'."\n\n";
		$csv .= 'Código proveedor'.$separador.'Nombre proveedor'.$separador.$columnasProyecto.'Importe global facturado'.$separador.'Porcentaje'."\n"; 

		if (is_array($aTabla)) {
			foreach ($aTabla as $a) {
				$baseImponible = $a['BaseImponible'];
				$porcentaje = $baseImponible * 100 / $total;

				$pintarProyecto = '';
				if ($detalle) {
					$pintarProyecto = 
						'"'	.trim(	$a['CodigoProyecto'])									.'"'.$separador.''.
						'"'	.trim(	$a['Proyecto'])											.'"'.$separador.'';
				}

				$csv .=
						'"'	.trim(	$a['CodigoProveedor'])									.'"'.$separador.''.
						'"'	.trim(	$a['RazonSocial'])										.'"'.$separador.''.
						$pintarProyecto.
						''	.$this->formatoNumero($baseImponible, true, false, 2, true)		.''.$separador.''.
						''	.$this->formatoNumero($porcentaje, true, false, 2, true)		.''.$separador.''.
						"\n";
			}

			/* pintar la ultima fila de total */
			$pintarProyecto = '';
			if ($detalle) {
				$pintarProyecto = 
					'"'																	.'"'.$separador.''.
					'"'																	.'"'.$separador.'';
			}
			$csv .=
					'"'																	.'"'.$separador.''.
					'"'																	.'"'.$separador.''.
					$pintarProyecto.
					''	.$this->formatoNumero($total, true, false, 2, true)				.''.$separador.'';		
		}

		$csv = utf8_decode($csv);
		echo $csv;
		exit;
    }
}
