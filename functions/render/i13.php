<?php

function render_tabla_i13 () {
	$trs='';
	$custom_query='';
	
	if (cG('ejercicios')) {
		$ejercicio = cG('ejercicios');
		if ($ejercicio != 0) {
			$custom_query = 'WHERE Ejercicio='.$ejercicio;
		} else {
			$custom_query = '';
		}
	}

	if (cG('niveles')) {
		$nivel = cG('niveles');
		if ($nivel == 'anexo') {
			$gastos_generales=get_db_data(array('listar-gastos-mediosm-i17-filtro-anexo',$custom_query));		
		} else if ($nivel == 'contrato') {
			$gastos_generales=get_db_data(array('listar-gastos-mediosm-i17-filtro-contrato',$custom_query));
		} else if ($nivel == 'proyecto') {
			$gastos_generales=get_db_data(array('listar-gastos-mediosm-i17-filtro-proyecto',$custom_query));
		} else {
			$gastos_generales=get_db_data(array('listar-gastos-mediosm-i17','WHERE Ejercicio='.date('Y')));
		}
	}

	if (empty($_GET)) {
		/* este año y por anexos */
		$custom_query = 'WHERE Ejercicio='.date('Y');
		$gastos_generales=get_db_data(array('listar-gastos-mediosm-i17-filtro-anexo',$custom_query));
	}

	if (cG('excel')=='true') {
		$separator=';';
			
		$csv='sep=;'."\n\n";

		$ejercicio = cG('ejercicios');
		$ggbi = 0;
		$data = get_db_data(array('obtener-ggbi-i12',$ejercicio));
		if (is_array($data)) {
			$ggbi = $data[0]['GGBI'];
		}

		$csv.=$separator.$separator.$separator.'Ejercicio'.$separator.$ejercicio.$separator.$separator.$separator.'% Estimacion GG+BI'.$separator.$ggbi."\n\n";
 	 	 	 	 	 	
		$csv.='Codigo'.$separator.'Descripcion'.$separator.'Enero'.$separator.'Febrero'.$separator.'Marzo'.$separator.'Abril'.$separator.'Mayo'.$separator.'Junio'.$separator.'Julio'.$separator.'Agosto'.$separator.'Septiembre'.$separator.'Octubre'.$separator.'Noviembre'.$separator.'Diciembre'.$separator.'Total anual'."\n";

		if(is_array($gastos_generales)) {
			foreach ($gastos_generales as $g) {
				$csv.=
						'"'		.trim(	$g['Codigo']									)		.'"'.$separator.''.
						'"'		.trim(	$g['Descripcion']								)		.'"'.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Enero'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Febrero'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Marzo'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Abril'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Mayo'])				)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Junio'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Julio'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Agosto'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Septiembre'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Octubre'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Noviembre'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['Diciembre'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($g['TotalEjercicio'])	)		.''.$separator.''.
						"\n";
			}
		}

		/* se trae por POST un array con todos los valores de la tabla inferior */
		$csv .= "\n\n";
		foreach($_POST as $fila => $v) {
		    if(strpos($fila, 'aFila') === 0) {
		    	if (is_array($v)) {
		    		$csv.=$separator;
					foreach ($v as $dato) {
						$csv.=
							'"' . trim($dato) . '"' . $separator . '';
					}
					$csv.="\n";
				}		       
		    }
		}

		return $csv;

	} else {
		
		$n=1;
		if(is_array($gastos_generales)) {
			foreach ($gastos_generales as $g) {
				$trs.=
					'
					<tr class="rowTabla">
						<td>'.$n.'</td>
						<td class="columnaFija">'.$g['Codigo'].'</td>
						<td class="columnaFija">'.$g['Descripcion'].'</td>				
						<td class="colMes" name="'.$g['Enero'].'">'.aux_money_format_noeuro($g['Enero']).'</td>
						<td class="colMes" name="'.$g['Febrero'].'">'.aux_money_format_noeuro($g['Febrero']).'</td>
						<td class="colMes" name="'.$g['Marzo'].'">'.aux_money_format_noeuro($g['Marzo']).'</td>
						<td class="colMes" name="'.$g['Abril'].'">'.aux_money_format_noeuro($g['Abril']).'</td>
						<td class="colMes" name="'.$g['Mayo'].'">'.aux_money_format_noeuro($g['Mayo']).'</td>
						<td class="colMes" name="'.$g['Junio'].'">'.aux_money_format_noeuro($g['Junio']).'</td>
						<td class="colMes" name="'.$g['Julio'].'">'.aux_money_format_noeuro($g['Julio']).'</td>
						<td class="colMes" name="'.$g['Agosto'].'">'.aux_money_format_noeuro($g['Agosto']).'</td>
						<td class="colMes" name="'.$g['Septiembre'].'">'.aux_money_format_noeuro($g['Septiembre']).'</td>
						<td class="colMes" name="'.$g['Octubre'].'">'.aux_money_format_noeuro($g['Octubre']).'</td>
						<td class="colMes" name="'.$g['Noviembre'].'">'.aux_money_format_noeuro($g['Noviembre']).'</td>
						<td class="colMes" name="'.$g['Diciembre'].'">'.aux_money_format_noeuro($g['Diciembre']).'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($g['TotalEjercicio']).'</td>						
					</tr>
				';
				$n++;
			}
		}
		
		$table_body=$trs;
		
		return $table_body;
	}
	
}


function make_filter_13() {
	
	$filtro	= file_get_contents_utf8(ABSPATH.'/template/filtro-12.html');

	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a id="btnExcel" target="_blank" style="margin-left: 20px" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';
	} else {		
		/* por defecto el año actual y Todos los niveles */
		$anioActual = date('Y');
		$excel_button='<a id="btnExcel" target="_blank" style="margin-left: 20px" href="?ejercicios='.$anioActual.'&niveles=anexo&excel=true" class="btn btn-default btn-sm">Excel</a>';
	}
	
	//$filtro					=	str_replace('{EJERCICIOS}',$proyectos_venta,$filtro);

	$filtro = str_replace('{EXCEL_URL}',$excel_button,$filtro);
	
	return $filtro;
}


function render_tabla_facturacion_mes_i13() {
	if (cG('ejercicios')) {
		$ejercicio = cG('ejercicios');
		if ($ejercicio != 0) {
			$custom_query = 'WHERE Ejercicio='.$ejercicio;
		} else {
			$custom_query = '';
		}
	} else {
		$custom_query = 'WHERE Ejercicio='.date('Y');
	}
	$facturacion_mes=get_db_data(array('listar-facturacion-mes-mediosm-i17', $custom_query));

	$meses = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);

	if(is_array($facturacion_mes)) {
		foreach ($facturacion_mes as $f) {
			$meses[$f['Mes']] = $f['Importe'];
		}
	}

	$tr_facturacion ='<tr class="rowFooter footerFacturacionMes">	
						<td class="rowFooterEnc" colspan="3">FACTURACION MENSUAL</td>
						<td name="'.$meses['1'].'">'.aux_money_format_noeuro($meses['1']).'</td>
						<td name="'.$meses['2'].'">'.aux_money_format_noeuro($meses['2']).'</td>
						<td name="'.$meses['3'].'">'.aux_money_format_noeuro($meses['3']).'</td>
						<td name="'.$meses['4'].'">'.aux_money_format_noeuro($meses['4']).'</td>
						<td name="'.$meses['5'].'">'.aux_money_format_noeuro($meses['5']).'</td>
						<td name="'.$meses['6'].'">'.aux_money_format_noeuro($meses['6']).'</td>
						<td name="'.$meses['7'].'">'.aux_money_format_noeuro($meses['7']).'</td>
						<td name="'.$meses['8'].'">'.aux_money_format_noeuro($meses['8']).'</td>
						<td name="'.$meses['9'].'">'.aux_money_format_noeuro($meses['9']).'</td>
						<td name="'.$meses['10'].'">'.aux_money_format_noeuro($meses['10']).'</td>
						<td name="'.$meses['11'].'">'.aux_money_format_noeuro($meses['11']).'</td>
						<td name="'.$meses['12'].'">'.aux_money_format_noeuro($meses['12']).'</td>
					</tr>';

	return $tr_facturacion;
}

function render_tabla_gastos_mes_i13() {
	if (cG('ejercicios')) {
		$ejercicio = cG('ejercicios');
		if ($ejercicio != 0) {
			$custom_query = 'WHERE Ejercicio='.$ejercicio;
		} else {
			$custom_query = '';
		}
	} else {
		$custom_query = 'WHERE Ejercicio='.date('Y');
	}
	$gastos_mes=get_db_data(array('listar-gastos-mes-mediosm-i17', $custom_query));

	$aMeses = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0);
	$mesesImporte = $aMeses;
	$mesesImporteVeh = $aMeses;

	if(is_array($gastos_mes)) {
		foreach ($gastos_mes as $g) {
			$mesesImporte[$g['mes']] = $g['importe'];
			$mesesImporteVeh[$g['mes']] = $g['ImporteVeh'];
		}
	}

	$tr_facturacion =	'<tr class="rowFooter footerImporte" style="border-top: 3px solid black">	
							<td class="rowFooterEnc" colspan="3">IMPORTE IMPUTADO A OBRAS</td>
							<td name="'.$mesesImporte['1'].'">'.aux_money_format_noeuro($mesesImporte['1']).'</td>
							<td name="'.$mesesImporte['2'].'">'.aux_money_format_noeuro($mesesImporte['2']).'</td>
							<td name="'.$mesesImporte['3'].'">'.aux_money_format_noeuro($mesesImporte['3']).'</td>
							<td name="'.$mesesImporte['4'].'">'.aux_money_format_noeuro($mesesImporte['4']).'</td>
							<td name="'.$mesesImporte['5'].'">'.aux_money_format_noeuro($mesesImporte['5']).'</td>
							<td name="'.$mesesImporte['6'].'">'.aux_money_format_noeuro($mesesImporte['6']).'</td>
							<td name="'.$mesesImporte['7'].'">'.aux_money_format_noeuro($mesesImporte['7']).'</td>
							<td name="'.$mesesImporte['8'].'">'.aux_money_format_noeuro($mesesImporte['8']).'</td>
							<td name="'.$mesesImporte['9'].'">'.aux_money_format_noeuro($mesesImporte['9']).'</td>
							<td name="'.$mesesImporte['10'].'">'.aux_money_format_noeuro($mesesImporte['10']).'</td>
							<td name="'.$mesesImporte['11'].'">'.aux_money_format_noeuro($mesesImporte['11']).'</td>
							<td name="'.$mesesImporte['12'].'">'.aux_money_format_noeuro($mesesImporte['12']).'</td>
						</tr>';

	$tr_facturacion .=	'<tr class="rowFooter footerImporteVeh">	
							<td class="rowFooterEnc" colspan="3">IMPORTE IMPUTADO A OBRAS (VEH&Iacute;CULOS)</td>
							<td name="'.$mesesImporteVeh['1'].'">'.aux_money_format_noeuro($mesesImporteVeh['1']).'</td>
							<td name="'.$mesesImporteVeh['2'].'">'.aux_money_format_noeuro($mesesImporteVeh['2']).'</td>
							<td name="'.$mesesImporteVeh['3'].'">'.aux_money_format_noeuro($mesesImporteVeh['3']).'</td>
							<td name="'.$mesesImporteVeh['4'].'">'.aux_money_format_noeuro($mesesImporteVeh['4']).'</td>
							<td name="'.$mesesImporteVeh['5'].'">'.aux_money_format_noeuro($mesesImporteVeh['5']).'</td>
							<td name="'.$mesesImporteVeh['6'].'">'.aux_money_format_noeuro($mesesImporteVeh['6']).'</td>
							<td name="'.$mesesImporteVeh['7'].'">'.aux_money_format_noeuro($mesesImporteVeh['7']).'</td>
							<td name="'.$mesesImporteVeh['8'].'">'.aux_money_format_noeuro($mesesImporteVeh['8']).'</td>
							<td name="'.$mesesImporteVeh['9'].'">'.aux_money_format_noeuro($mesesImporteVeh['9']).'</td>
							<td name="'.$mesesImporteVeh['10'].'">'.aux_money_format_noeuro($mesesImporteVeh['10']).'</td>
							<td name="'.$mesesImporteVeh['11'].'">'.aux_money_format_noeuro($mesesImporteVeh['11']).'</td>
							<td name="'.$mesesImporteVeh['12'].'">'.aux_money_format_noeuro($mesesImporteVeh['12']).'</td>
						</tr>';

	return $tr_facturacion;
}

?>