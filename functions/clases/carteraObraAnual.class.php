<?php

class carteraObraAnual extends bd {

	private $aEstadosFacturacion;

    public function __construct() {
        parent::__construct();
        $this->aEstadosFacturacion = array('0' => 'Pendiente', '1' => 'Facturado', '2' => 'Cerrada facturación', '3' => 'No se factura');
    }

    public function pintarSelProyectos($estado, $primeraVez) {
    	$html = '<option value="">Todos los proyectos</option>';
    	$filtroEstado = ''; // todos los estados

    	if ($estado == '0') {
			$filtroEstado = " AND Activa='0' ";
		} else if ($estado == '1') {
			$filtroEstado = " AND Activa='-1' ";
		}

		if ($primeraVez) { // si es la primera vez pues muestra los activos
			$filtroEstado = " AND Activa='-1' ";
		}

		/* si es un jefe de obra */
		if ($this->jefeObra != 0) {
			$filtroEstado .= " AND ID_JEFE_OBRA=".$this->jefeObra;
		}

    	$select = 'DISTINCT CodigoProyecto, DescripcionProyecto';
    	$from = DB_APP.'facturacionobras';
    	$where = "WHERE CodigoEmpresa='".$this->empresa."' AND CodigoProyecto<9999 ".$filtroEstado;
    	$order = 'GROUP BY CodigoProyecto, DescripcionProyecto ORDER BY CodigoProyecto ASC';
    	$aProyectos = $this->consulta($select, $from, $where, $order);
		if (is_array($aProyectos)) {
			foreach($aProyectos as $a) {
				$html .= '<option value="'.$a['CodigoProyecto'].'">'.$a['CodigoProyecto'].' - '.$a['DescripcionProyecto'].'</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

    public function pintarTabla($filtros, $excel = false) {
    	$html = '<tr><td colspan="14" style="text-align:center">No hay registros.</td></tr>';
    	$contador = 1;

    	$select = 'Ejercicio, Mes, TipoDocum, CodigoProyecto, DescripcionProyecto, Proyecto, Descripcion, FacturadoMes, ImporteContrato, FacturadoOrigen, CarteraPendiente, TotalProyectoFacturadoMes, TotalImporteContratos, TotalProyectoFacturadoOrigen, TotalProyectoCarteraPendiente, EstadoFacturacion, ObservacionesProyecto, Activa';
    	$from = DB_APP.'facturacionobras';
    	$where = "WHERE CodigoEmpresa='".$this->empresa."' " . $this->obtenerQueryFiltros($filtros);
    	$order = 'ORDER BY CodigoProyecto, Proyecto ASC';
    	$aTabla = $this->consulta($select, $from, $where, $order);
		if ($excel) {
    		$this->imprimirExcel($aTabla);
    	} else {
    		if (is_array($aTabla)) {
				$html = ''; // vaciarlo antes
				$codigoProyectoActual = '-1';

				foreach ($aTabla as $a) {
					$bordeFinProyecto = '';
					$colorFila = 'none';
					$estadoFacturacion = $a['EstadoFacturacion'];

					if ($estadoFacturacion == '0') { // pendiente
						$colorFila = '#F4AAB8'; // rosa
					} else if ($estadoFacturacion == '1') { // facturado
						$colorFila = '#95A9F9'; // azul
					} else if ($estadoFacturacion == '2') { // cerrada facturacion
						$colorFila = '#8FFAA3'; // verde
					} else if ($estadoFacturacion == '3') { // no se factura
						$colorFila = '#DB7242'; // naranja
					}

					if ($codigoProyectoActual != $a['CodigoProyecto']) {
						$codigoProyectoActual = $a['CodigoProyecto'];
						$bordeFinProyecto = 'border-top:3px solid black';
					}

					if ($this->jefeObra != 0) { // los jefes de obra no pueden editar el select de estados
						$campoEstadoFacturacion = $this->aEstadosFacturacion[$estadoFacturacion];
						$campoObservaciones = $a['ObservacionesProyecto'];
					} else {
						$campoEstadoFacturacion = $this->pintarSelCambiarEstadoFacturacion($estadoFacturacion);
						$campoObservaciones = '<textarea class="txtCambiarObservaciones" style="min-width:280px">'.$a['ObservacionesProyecto'].'</textarea>';
					}

					$html .= 
							'<tr style="background-color:'.$colorFila.';'.$bordeFinProyecto.'" ejercicio="'.$a['Ejercicio'].'" mes="'.$a['Mes'].'" codigoProyecto="'.$a['CodigoProyecto'].'" anexo="'.$a['Proyecto'].'">
								<td style="min-width:20px">'.$contador.'</td>
								<td style="min-width:20px">'.$a['Ejercicio'].'</td>
								<td style="min-width:20px">'.$a['Mes'].'</td>
								<td style="min-width:20px">'.$a['TipoDocum'].'</td>
								<td style="min-width:30px">'.$a['CodigoProyecto'].'</td>
								<td style="min-width:300px">'.$a['DescripcionProyecto'].'</td>
								<td style="min-width:40px">'.$a['Proyecto'].'</td>
								<td style="min-width:500px">'.$a['Descripcion'].'</td>
								<td style="min-width:100px">'.$this->formatoNumero($a['FacturadoMes'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['ImporteContrato'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['FacturadoOrigen'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['CarteraPendiente'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['TotalProyectoFacturadoMes'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['TotalImporteContratos'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['TotalProyectoFacturadoOrigen'], true, false, 2).'</td>
								<td style="min-width:30px">'.$this->formatoNumero($a['TotalProyectoCarteraPendiente'], true, false, 2).'</td>
								<td style="min-width:80px">'.$campoEstadoFacturacion.'</td>
								<td style="min-width:300px">'.$campoObservaciones.'</td>
								<td style="min-width:10px">'.$this->pintarTextoActivo($a['Activa']).'</td>
							</tr>';
					$contador++;
				}
			}
		}
		return json_encode(array('html' => $html, 'empresa' => $this->empresa, 'consulta' => $select.$from.$where.$order));
    }

    private function obtenerQueryFiltros($filtros) {
    	$where = '';
    	$estadoProyecto = $filtros['estadoProyecto'];
    	$proyecto = $filtros['proyecto'];
    	$estadoFacturacion = $filtros['estadoFacturacion'];
    	$ejercicio = $filtros['ejercicio'];
    	$mes = $filtros['mes'];

    	/* estado proyecto */
    	if ($estadoProyecto == '0') {
			$where .= " AND Activa='0' ";
		} else if ($estadoProyecto == '1') {
			$where .= " AND Activa='-1' ";
		}

		/* proyecto */
    	if ($proyecto != '') {
			$where .= " AND CodigoProyecto='".$proyecto."' ";
		}

		/* estado facturacion */
    	if ($estadoFacturacion != '') {
			$where .= " AND EstadoFacturacion='".$estadoFacturacion."' ";
		}

		/* ejercicio */
    	if ($ejercicio != '') {
			$where .= " AND Ejercicio='".$ejercicio."' ";
		}

		/* mes */
    	if ($mes != '') {
			$where .= " AND Mes='".$mes."' ";
		}

		/* si es un jefe de obra */
		if ($this->jefeObra != 0) {
			$where .= " AND ID_JEFE_OBRA=".$this->jefeObra;
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

    public function imprimirExcel($aTabla) {
    	header('Content-type: application/vnd.ms-word; charset=utf-8');
		header("Content-Disposition: attachment;Filename=\"Informe facturacion mensual.xls\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);

    	$separador = ';';
		$csv  = 'sep=;'."\n\n";
		$csv .= 'Ejercicio'.$separador.'Mes'.$separador.'Tipo'.$separador.'Código'.$separador.'Proyecto'.$separador.
				'Contrato/Anexo'.$separador.'Descripción'.$separador.'Importe mes'.$separador.'Importe contrato'.$separador.
				'Importe origen'.$separador.'Importe cartera pendiente'.$separador.'Total proyecto facturado mes'.$separador.
				'Total importe de TODOS los contratos'.$separador.'Total proyecto facturado a origen'.$separador.
				'Total proyecto cartera pendiente'.$separador.'Estado'.$separador.'Observaciones'.$separador.'Activo'."\n"; 

		if (is_array($aTabla)) {
			foreach ($aTabla as $a) {
				$csv .=
						'"'		.trim(	$a['Ejercicio'])															.'"'.$separador.''.
						'"'		.trim(	$a['Mes'])																	.'"'.$separador.''.
						'"'		.trim(	$a['TipoDocum'])															.'"'.$separador.''.
						'"'		.trim(	$a['CodigoProyecto'])														.'"'.$separador.''.
						'"'		.trim(	$a['DescripcionProyecto'])													.'"'.$separador.''.
						'"'		.trim(	$a['Proyecto'])																.'"'.$separador.''.
						'"'		.trim(	$a['Descripcion'])															.'"'.$separador.''.
						''		.$this->formatoNumero($a['FacturadoMes'], true, false, 2, true)						.''.$separador.''.
						''		.$this->formatoNumero($a['ImporteContrato'], true, false, 2, true)					.''.$separador.''.
						''		.$this->formatoNumero($a['FacturadoOrigen'], true, false, 2, true)					.''.$separador.''.
						''		.$this->formatoNumero($a['CarteraPendiente'], true, false, 2, true)					.''.$separador.''.
						''		.$this->formatoNumero($a['TotalProyectoFacturadoMes'], true, false, 2, true)		.''.$separador.''.
						''		.$this->formatoNumero($a['TotalImporteContratos'], true, false, 2, true)			.''.$separador.''.
						''		.$this->formatoNumero($a['TotalProyectoFacturadoOrigen'], true, false, 2, true)		.''.$separador.''.
						''		.$this->formatoNumero($a['TotalProyectoCarteraPendiente'], true, false, 2, true)	.''.$separador.''.
						'"'		.$this->aEstadosFacturacion[$a['EstadoFacturacion']]								.'"'.$separador.''.
						'"'		.trim($a['ObservacionesProyecto'])													.'"'.$separador.''.
						'"'		.trim($this->pintarTextoActivo($a['Activa']))										.'"'.$separador.''.
						"\n";
			}			
		}

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
