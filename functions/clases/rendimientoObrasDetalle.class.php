<?php

include_once '../comun/cabses.php';
include_once '../comun/bd.class.php';

class rendimientoObrasDetalle extends bd {

	private $proyecto;

    public function __construct() {
        parent::__construct();
        $this->proyecto = '';
    }

    public function pintarTabla ($obra, $excel = false) {

    	$this->proyecto = $obra;

    	/* se obtienen los periodos, siempre dos años atrás */
    	$filtros['fechaInicio'] = '01-2016';
    	$filtros['fechaFin'] = '12-' . date('Y');

		$aPeriodos = $this->obtenerPeriodos($filtros);

    	$contador = 1;
    	$select = '[ID_OBRA]
			      ,[YEAR]
			      ,[MES]
			      ,[FACTURADO]
			      ,[PF_CIERRE]
			      ,[PF_FALTA_CONTRATO]
			      ,[PF_APROBACION]
			      ,[PF_OTROS]
			      ,[GASTOSCONTA]
			      ,[IMPORTE_P_CERTIFICACION]
			      ,[IMPORTE_ACOPIO_ENTREGA]
			      ,[FACTURACION_ORIGEN]
			      ,[GASTOS_ORIGEN]';
    	$from = DB_APP . 'DetalleRendimientoObra';
    	$where = "WHERE ID_OBRA = '" . $obra . "' AND YEAR <= YEAR(getdate()) AND YEAR >= 2016";
    	$order = 'ORDER BY YEAR ASC, len(MES) ASC, MES ASC';

    	$aTabla = $this->consulta($select, $from, $where, $order);

    	$htmlCabecera = '';
    	$htmlCabecera2 = '';
    	$htmlCabecera3 = '';
    	$htmlCuerpoCierre = '';
		$htmlCuerpoFacturado = '';
    	$htmlCuerpoFaltaContrato = '';
		$htmlCuerpoFaltaPartidas = '';
		$htmlCuerpoOtrosMotivos  = '';
		$htmlCuerpoGastosContabilizados = '';
		$htmlCuerpoSubcontratas = '';
		$htmlCuerpoAcopios = '';
		$htmlCuerpoBeneficioOrigen = '';
		$htmlCuerpoBeneficioPeriodo = '';
		$htmlCuerpoBeneficioOrigenPorcentaje = '';
		$htmlCuerpoBeneficioPeriodoPorcentaje = '';

		$total = array();
		$total2 = array();

		$totalFacturado = 0;
		$totalCierre = 0;
		$totalFaltaContrato = 0;
		$totalFaltaPartidas = 0;
		$totalOtrosMotivos = 0;
		$totalGastosContabilizados = 0;
		$totalGastosOrigen = 0;
		$totalSubcontratas = 0;
		$totalAcopios = 0;
		$totalFacturacionOrigen = 0;

		$facturacionOrigen_mes_anterior = 0;
		$pf_cierre_mes_anterior = 0;
		$pf_faltaContrato_mes_anterior = 0;
		$pf_aprobacion_mes_anterior = 0;
		$pf_otros_mes_anterior = 0;

		$gastosOrigen_mes_anterior = 0;
		$pc_subcontratas_mes_anterior = 0;
		$acopios_mes_anterior = 0;

		$periodos = array();

		$total_pte_facturacion_mes_anterior = 0;
		$pte_certificacion_mes_anterior = 0;
		$pte_acopio_mes_anterior = 0;

		$ingresos_grafica = array();
		$gastos_grafica = array();
		$beneficios_grafica = array();

		$separador = ';';
        $csv  = 'sep=;' . "\n\n";
        $cabecera_csv = 'CONCEPTO' . $separador;

		if (is_array($aTabla)) {
			foreach ($aTabla as $a) {
				$mes = $a['MES'];
				$anio = $a['YEAR'];
				$periodos[] = ["M"=>$mes, "Y"=>$anio];
				$cabecera_csv .= '"' . $mes . '/' . $anio . '"' . $separador . '';
			}
		}

  		$linea1_tabla1_csv = '"Facturado"' . $separador . '';
        $linea2_tabla1_csv = '"PF Cierre hasta fin mes"' . $separador . '';
        $linea3_tabla1_csv = '"PF Falta de contrato"' . $separador . '';
        $linea4_tabla1_csv = '"PF Falta de partidas"' . $separador . '';
        $linea5_tabla1_csv = '"PF Otros motivos"' . $separador . '';

        $linea1_tabla2_csv = '"Gastos contabilizados"' . $separador . '';
        $linea2_tabla2_csv = '"PC Subcontratas y proveedores"' . $separador . '';
        $linea3_tabla2_csv = '"Acopios o entregas a cuenta"' . $separador . '';

		$linea1_tabla3_csv = '"Beneficio mensual"' . $separador . '';
        $linea2_tabla3_csv = '"Beneficio origen"' . $separador . '';
        $linea3_tabla3_csv = '"% Beneficio mensual"' . $separador . '';
        $linea4_tabla3_csv = '"% Beneficio origen"' . $separador . '';
		
		if (is_array($aTabla)) {
			$html = ''; // vaciarlo antes
			$contador = 0;
			foreach ($aTabla as $a) {

				$mes = $a['MES'];
				$anio = $a['YEAR'];

				/*** TABLA 1 ***/
				$facturado = $a['FACTURADO'];
				$pf_cierre = $a['PF_CIERRE'];
				$pf_faltaContrato = $a['PF_FALTA_CONTRATO'];
				$pf_aprobacion = $a['PF_APROBACION'];
				$pf_otros = $a['PF_OTROS'];

				$htmlCabecera .= '<th style="text-align:center">' . $mes . '/' . $anio . '</th>';

				$htmlCuerpoFacturado .= '<td style="text-align:right">' . $this->formatoNumero($facturado, false, true, 2) . '</td>';
				$htmlCuerpoCierre .= '<td style="text-align:right">' . $this->formatoNumero($pf_cierre, false, true, 2) . '</td>';
				$htmlCuerpoFaltaContrato .= '<td style="text-align:right">' . $this->formatoNumero($pf_faltaContrato, false, true, 2) . '</td>';
				$htmlCuerpoFaltaPartidas .= '<td style="text-align:right">' . $this->formatoNumero($pf_aprobacion, false, true, 2) . '</td>';
				$htmlCuerpoOtrosMotivos .= '<td style="text-align:right">' . $this->formatoNumero($pf_otros, false, true, 2) . '</td>';

				$linea1_tabla1_csv .= '"' . $this->formatoNumero($facturado, false, true, 2, true) . '"' . $separador . '';
				$linea2_tabla1_csv .= '"' . $this->formatoNumero($pf_cierre, false, true, 2, true) . '"' . $separador . '';
                $linea3_tabla1_csv .= '"' . $this->formatoNumero($pf_faltaContrato, false, true, 2, true) . '"' . $separador . '';
                $linea4_tabla1_csv .= '"' . $this->formatoNumero($pf_aprobacion, false, true, 2, true) . '"' . $separador . '';
                $linea5_tabla1_csv .= '"' . $this->formatoNumero($pf_otros, false, true, 2, true) . '"' . $separador .'';

				$totalFacturado += $facturado;
				$totalCierre += $pf_cierre;
				$totalFaltaContrato += $pf_faltaContrato;
				$totalFaltaPartidas += $pf_aprobacion;
				$totalOtrosMotivos += $pf_otros;

				if (!isset($total[$contador])) {
					$total[$contador] = 0;
				}
				$total[$contador] .= $facturado + $pf_cierre + $pf_faltaContrato + $pf_aprobacion + $pf_otros;


				/*** TABLA 2 ***/
				$gastosContabilizados = $a['GASTOSCONTA'];
				$pc_subcontratas = $a['IMPORTE_P_CERTIFICACION'];
				$acopios = $a['IMPORTE_ACOPIO_ENTREGA'];


				$htmlCabecera2 .= '<th style="text-align:center">' . $mes . '/' . $anio . '</th>';

				$htmlCuerpoGastosContabilizados .= '<td style="text-align:right">' . $this->formatoNumero($gastosContabilizados, false, true, 2) . '</td>';
				$htmlCuerpoSubcontratas .= '<td style="text-align:right">' . $this->formatoNumero($pc_subcontratas, false, true, 2) . '</td>';
				$htmlCuerpoAcopios .= '<td style="text-align:right">' . $this->formatoNumero($acopios, false, true, 2) . '</td>';

				$linea1_tabla2_csv .= '"' . $this->formatoNumero($gastosContabilizados, false, true, 2, true) . '"' . $separador . '';
                $linea2_tabla2_csv .= '"' . $this->formatoNumero($pc_subcontratas, false, true, 2, true) . '"' . $separador . '';
                $linea3_tabla2_csv .= '"' . $this->formatoNumero($acopios, false, true, 2, true) . '"' . $separador . '';

				$totalGastosContabilizados += $gastosContabilizados;
				$totalSubcontratas += $pc_subcontratas;
				$totalAcopios += $acopios;

				if (!isset($total2[$contador])) {
					$total2[$contador] = 0;
				}
				$total2[$contador] .= $gastosContabilizados + $pc_subcontratas + $acopios;


				/*** TABLA 3 ***/
				$gastosOrigen = $a['GASTOS_ORIGEN'];
				$facturacionOrigen = $a['FACTURACION_ORIGEN'];

				$htmlCabecera3 .= '<th style="text-align:center">' . $mes . '/' . $anio . '</th>';

				$totalGastosOrigen += $gastosOrigen;
				$totalFacturacionOrigen += $facturacionOrigen;


				/** Beneficio origen **/
				$total_pte_facturacion = $pf_cierre + $pf_faltaContrato + $pf_aprobacion + $pf_otros;
				$total_prev_facturacion_origen = $facturacionOrigen + $total_pte_facturacion;
				$total_prev_gastos_origen = $gastosOrigen + $pc_subcontratas + $acopios;

				//Si no tiene FACTURADO ni GASTOS_ORIGEN y tiene FACTURACION_ORIGEN no muestro beneficio origen (¿es siempre el mes actual?)
				if (is_null($a['GASTOS_ORIGEN']) && is_null($a['FACTURADO']) && !is_null($a['FACTURACION_ORIGEN'])) {
					$beneficioOrigen = '';
				} else {
					$beneficioOrigen = $total_prev_facturacion_origen - $total_prev_gastos_origen;
				}

				$htmlCuerpoBeneficioOrigen .= '<td style="text-align:right">' . $this->formatoNumero($beneficioOrigen, false, true, 2) . '</td>';
				$linea1_tabla3_csv .= '"' . $this->formatoNumero($beneficioOrigen, false, true, 2, true) . '"' . $separador . '';

				/** Beneficio periodo **/
				$total_prev_facturacion_periodo = $facturado + $total_pte_facturacion - $total_pte_facturacion_mes_anterior;
				$total_prev_gastos_periodo = $gastosContabilizados + $pc_subcontratas + $acopios - $pte_certificacion_mes_anterior - $pte_acopio_mes_anterior;

				$beneficioPeriodo = $total_prev_facturacion_periodo - $total_prev_gastos_periodo;
				$htmlCuerpoBeneficioPeriodo .= '<td style="text-align:right">' . $this->formatoNumero($beneficioPeriodo, false, true, 2) . '</td>';
				$linea2_tabla3_csv .= '"' . $this->formatoNumero($beneficioPeriodo, false, true, 2, true) . '"' . $separador . '';

				/** Beneficio periodo porcentaje **/
				$beneficioPeriodoPorcentaje = 0;

				if ($total_prev_gastos_periodo != 0 && $total_prev_facturacion_periodo != 0) {
					$beneficioPeriodoPorcentaje = (($total_prev_facturacion_periodo / $total_prev_gastos_periodo) -1 ) * 100;
				}
				
				$htmlCuerpoBeneficioPeriodoPorcentaje .= '<td style="text-align:right">' . $this->formatoNumero($beneficioPeriodoPorcentaje, false, false, 2, false, true) . '</td>';
				$linea3_tabla3_csv .= '"' . $this->formatoNumero($beneficioPeriodoPorcentaje, false, false, 2, false, true) . '"' . $separador . '';

				/** Beneficio origen porcentaje **/
				$beneficioOrigenPorcentaje = 0;
				if ($total_prev_gastos_origen != 0 && $total_prev_facturacion_origen != 0) {
					$beneficioOrigenPorcentaje = (($total_prev_facturacion_origen / $total_prev_gastos_origen) -1 ) * 100;
				}
				
				$htmlCuerpoBeneficioOrigenPorcentaje .= '<td style="text-align:right">' . $this->formatoNumero($beneficioOrigenPorcentaje, false, false, 2, false, true) . '</td>';
				$linea4_tabla3_csv .= '"' . $this->formatoNumero($beneficioOrigenPorcentaje, false, false, 2, false, true) . '"' . $separador . '';

				$total_pte_facturacion_mes_anterior = $total_pte_facturacion;
				$pte_certificacion_mes_anterior = $pc_subcontratas;
				$pte_acopio_mes_anterior = $acopios;
				$contador++;


				/** GRAFICA **/
				$ingresos_grafica[] = $facturacionOrigen;					

				$gastos_grafica[] = $gastosOrigen;

				$beneficios_grafica[] = $facturacionOrigen - $gastosOrigen;

				$facturacionOrigen_mes_anterior = $facturado;
				$pf_cierre_mes_anterior = $pf_cierre;
				$pf_faltaContrato_mes_anterior = $pf_faltaContrato;
				$pf_aprobacion_mes_anterior = $pf_aprobacion;
				$pf_otros_mes_anterior = $pf_otros;

				$gastosOrigen_mes_anterior = $gastosOrigen;
				$pc_subcontratas_mes_anterior = $pc_subcontratas;
				$acopios_mes_anterior = $acopios;
			}
		}
	
		if ($excel) {
			/*TABLA 1*/
			$csv .= $cabecera_csv . '"TOTAL"' . "\n";

			$linea1_tabla1_csv .= '"' . $this->formatoNumero($totalFacturado, false, true, 2, true) . '"' . $separador . '';
			$linea2_tabla1_csv .= '"' . $this->formatoNumero($totalCierre, false, true, 2, true) . '"' . $separador . '';
			$linea3_tabla1_csv .= '"' . $this->formatoNumero($totalFaltaContrato, false, true, 2, true) . '"' . $separador . '';
			$linea4_tabla1_csv .= '"' . $this->formatoNumero($totalFaltaPartidas, false, true, 2, true) . '"' . $separador . '';
			$linea5_tabla1_csv .= '"' . $this->formatoNumero($totalOtrosMotivos, false, true, 2, true) . '"' . $separador . '';

			//Línea inferior totales
			$lineaTotales_tabla1_csv = '"Total"' . $separador. '';
			foreach ($total as $key => $value) {
				$lineaTotales_tabla1_csv .= '"' . $this->formatoNumero($value, false, true, 2, true) . '"' . $separador . '';
			}

			$csv .= $linea1_tabla1_csv . "\n" . $linea2_tabla1_csv . "\n" . $linea3_tabla1_csv . "\n" . $linea4_tabla1_csv . "\n" . $linea5_tabla1_csv . "\n" . $lineaTotales_tabla1_csv . "\n" .  "\n" . "\n" . "\n";
	
	        /*TABLA 2*/
	        $csv .= $cabecera_csv . '"TOTAL"' . "\n";

			$linea1_tabla2_csv .= '"' . $this->formatoNumero($totalGastosContabilizados, false, true, 2, true) . '"' . $separador . '';
			$linea2_tabla2_csv .= '"' . $this->formatoNumero($totalSubcontratas, false, true, 2, true) . '"' . $separador . '';
			$linea3_tabla2_csv .= '"' . $this->formatoNumero($totalAcopios, false, true, 2, true) . '"' . $separador . '';

			//Línea inferior totales
			$lineaTotales_tabla2_csv = '"Total"' . $separador. '';
			foreach ($total2 as $key => $value) {
				$lineaTotales_tabla2_csv .= '"' . $this->formatoNumero($value, false, true, 2, true) . '"' . $separador . '';
			}

	        $csv .= $linea1_tabla2_csv . "\n" . $linea2_tabla2_csv . "\n" . $linea3_tabla2_csv  . "\n" . $lineaTotales_tabla2_csv . "\n" .  "\n" . "\n" . "\n";

	        /*TABLA 3*/
	        $csv .= $cabecera_csv . "\n";
	        $csv .= $linea1_tabla3_csv . "\n" . $linea2_tabla3_csv . "\n" . $linea3_tabla3_csv . "\n" .  $linea4_tabla3_csv . "\n" . "\n" . "\n";


			$this->imprimirExcel($csv);
		} else {
			/* TABLA 1 */
			$html = '<table id="tabla" class="table table-bordered table-hover" style="border:0 !important;">';

			if ($htmlCuerpoCierre == '') { //Cambiar. Controlar que esté vacío el cuerpo.
				$html .= '<tr><td colspan="14" style="text-align:center">No hay registros.</td></tr>';
			} else {
				$html .= '<tr style="background-color:#fff">
							<th>CONCEPTO</th>' . $htmlCabecera . '
							<th style="text-align:right">Total</th>
						</tr>';

				$html .= '<tbody>
							<tr>
								<td>FACTURADO</td>' . $htmlCuerpoFacturado . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalFacturado, false, true, 2) . '</td>
							</tr>
							<tr>
								<td>PF CIERRE HASTA FIN DE MES</td>' . $htmlCuerpoCierre . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalCierre, false, true, 2) . '</td>
							</tr>
							<tr>
								<td>PF FALTA DE CONTRATO</td>' . $htmlCuerpoFaltaContrato . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalFaltaContrato, false, true, 2) . '</td>
							</tr>
							<tr>
								<td>PF FALTA DE PARTIDAS</td>' . $htmlCuerpoFaltaPartidas . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalFaltaPartidas, false, true, 2) . '</td>
							</tr>
							<tr>
								<td>PF OTROS MOTIVOS</td>' . $htmlCuerpoOtrosMotivos . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalOtrosMotivos, false, true, 2) . '</td>
							</tr>
						</tbody>';

				//Pinta el pie de la tabla con los sumatorios totales
				$html .= '<tr><td><b>Total</b></td>';
				foreach ($total as $key => $value) {
					$html .= '<td style="text-align:right">' . $this->formatoNumero($value, false, true, 2) . '</td>';
				}
				$html .= '<td></td>';

				$html .= '</tr>';
						      
			}
			$html .= '<tr style="border:0 !important;"><td style="border:0"></td></tr>';

			/* TABLA 2 */

			if ($htmlCuerpoGastosContabilizados == '') { //Cambiar. Controlar que esté vacío el cuerpo.
				$html .= '<tr><td colspan="14" style="text-align:center">No hay registros.</td></tr>';
			} else {

				$html .= '<tr style="background-color:#fff">
							<th>CONCEPTO</th>' . $htmlCabecera2 . '
							<th style="text-align:right">Total</th>
						</tr>';

				$html .= '<tbody>
							<tr>
								<td>GASTOS CONTABILIZADOS</td>' . $htmlCuerpoGastosContabilizados . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalGastosContabilizados, false, true, 2) . '</td>
							</tr>
							<tr>
								<td>PC SUBCONTRATAS Y PROVEEDORES</td>' . $htmlCuerpoSubcontratas . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalSubcontratas, false, true, 2) . '</td>
							</tr>
							<tr>
								<td>ACOPIOS O ENTREGAS A CUENTA</td>' . $htmlCuerpoAcopios . '
								<td style="font-weight:bold;text-align:right">' . $this->formatoNumero($totalAcopios, false, true, 2) . '</td>
							</tr>
						</tbody>';

				//Pinta el pie de la tabla con los sumatorios totales
				$html .='<tr><td><b>Total</b></td>';
				foreach ($total2 as $key => $value) {
					$html.= '<td style="text-align:right">' . $this->formatoNumero($value, false, true, 2) . '</td>';
				}
				$html .= '<td></td>';

				$html .= '</tr>';
			}

			$html .= '<tr style="border:0 !important;"><td style="border:0"></td></tr>';

			/* TABLA 3 */

			if ($htmlCuerpoGastosContabilizados == '') { //Cambiar. Controlar que esté vacío el cuerpo.
				$html .= '<tr><td colspan="14" style="text-align:center">No hay registros.</td></tr>';
			} else {

				$html .= '<tr style="background-color:#fff">
								<th>CONCEPTO</th>' . $htmlCabecera3 . '
							</tr>';

				$html .= '<tbody>
							<tr>
								<td>BENEFICIO MENSUAL</td>' . $htmlCuerpoBeneficioPeriodo . '
							</tr>
							<tr>
								<td>BENEFICIO ORIGEN</td>' . $htmlCuerpoBeneficioOrigen . '
							</tr>
							<tr>
								<td>% BENEFICIO MENSUAL</td>' . $htmlCuerpoBeneficioPeriodoPorcentaje . '
							</tr>
							<tr>
								<td>% BENEFICIO ORIGEN</td>' . $htmlCuerpoBeneficioOrigenPorcentaje . '
							</tr>
						</tbody>';

				$html .= '</tr>';

			}

			$html .= '</table>';

			$grafica = $this->pintar_grafica($ingresos_grafica,$gastos_grafica,$beneficios_grafica,$periodos);
			$html = $html . $grafica;		
		}

		return json_encode(array(
			'html' => $html, 
			'empresa' => $this->empresa, 
			'consulta' => 'select ' . $select . ' from ' . $from . ' ' . $where . $order
		));
    }

    public function imprimirExcel ($csv) {
    	header('Content-type: application/vnd.ms-word; charset=utf-8');
		header("Content-Disposition: attachment;Filename=\"Informe facturacion mensual.xls\"");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);

		$csv = utf8_decode($csv);
		echo $csv;
		exit;
    }

    public function obtenerPeriodos ($filtros) {
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

	public function pintar_grafica ($ingresos_grafica, $gastos_grafica, $beneficios_grafica, $periodos) {
		$ingresos_gr	=	'[';
		$gastos_gr		=	'[';
		$benficio_gr	=	'[';
		$ticks			=	'[';
		
		$i=0;
		
		foreach ($periodos as $p) {
			if ($i==0) {$sep='';} else {$sep=',';}
			
			$ticks .= $sep . "'" . $p['M'] . '/' . $p['Y'] . "'";
			
			if ($ingresos_grafica[$i]=='')	{$ingresos_grafica[$i]=0;}	else {$ingresos_grafica[$i]	=	$ingresos_grafica[$i]	+0;}
			if ($gastos_grafica[$i]=='')		{$gastos_grafica[$i]=0;}		else {$gastos_grafica[$i]	=	$gastos_grafica[$i]		+0;}
			if ($beneficios_grafica[$i]=='')	{$beneficios_grafica[$i]=0;}	else {$beneficios_grafica[$i]	=	$beneficios_grafica[$i]	+0;}
			
			$ingresos_gr	.=	$sep . $ingresos_grafica[$i];
			$gastos_gr		.=	$sep . $gastos_grafica[$i];
			$benficio_gr	.=	$sep . $beneficios_grafica[$i];
			
			$i++;
		}
		
		$ingresos_gr	.=	']';
		$gastos_gr		.=	']';
		$benficio_gr	.=	']';
		$ticks			.=	']';		

		$out =
			"
			<button id =\"btnAbrirGrafico\" class=\"btn btn-default btn-sm\" style=\"margin-left: 10px;margin-bottom: 12px;\" type=\"submit\">Mostrar gráfico</button>
			<div id=\"chart1\" style=\"height:600px;width:1200px;display:none;\"></div>
			<script>

				$(document).ready(function () {
			      $('#btnAbrirGrafico').click(function () {
					if ($(\"#btnAbrirGrafico\").html() == 'Mostrar gráfico') {
						$(\"#chart1\").show();
						pintarGrafico();
						$(\"#btnAbrirGrafico\").html('Ocultar gráfico');
					} else {
						$(\"#btnAbrirGrafico\").html('Mostrar gráfico');
						$(\"#chart1\").hide();
					}
			      });
			    });
				
				function pintarGrafico() {
			        $.jqplot.config.enablePlugins = true;
			        var s1 = " . $ingresos_gr . ";
			   		var s2 = " . $gastos_gr . ";
					var s3 = " . $benficio_gr . ";
			        var ticks = " . $ticks . ";
			         
			        plot1 = $.jqplot('chart1', [s1,s2,s3], {
			            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
			            animate: !$.jqplot.use_excanvas,
			            seriesDefaults:{               
			                pointLabels: { show: false }
			            },
			            axes: {
			                xaxis: {
			                    renderer: $.jqplot.CategoryAxisRenderer,
			                    ticks: ticks
			                }
			            },
			            highlighter: { show: false },
						legend: { show: true },
						series: [{ label: 'Ingresos' },{ label: 'Gastos' },{ label: 'Beneficios' }]
			        });
			     }
			</script>	
			";

		return $out;
	}

	public function pintarSelObras() {
    	$html = '<option value="-1">Obras activas</option>';

    	$select = 'DISTINCT CodigoProyecto, Proyecto';
    	$from = DB_APP.'jefes_de_obra';
    	$order = 'ORDER BY CodigoProyecto ASC';
    	$aObras = $this->consulta($select, $from, $order);
		if (is_array($aObras)) {
			foreach ($aObras as $a) {
				$html .= '<option value="'.$a['CodigoProyecto'].'">'.$a['CodigoProyecto'].' - '.$a['Proyecto'].'</option>';
			}
		}

		return json_encode(array('html' => $html));
    }

}