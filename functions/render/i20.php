<?php
if(!isset($_SESSION)) { 
   session_start(); 
}
function render_tabla_i20 () {
	$trs='';
	$custom_query='';
	
	if (cG('ejercicios')) {
		$ejercicio = cG('ejercicios');
		if ($ejercicio != 0) {
			$custom_query = 'WHERE Ejercicio='.$ejercicio;
		}
	}

	if ($custom_query == '') {
		$custom_query .= ' WHERE codigoempresa='.$_SESSION['company_id'];
	} else {
		$custom_query .= ' AND codigoempresa='.$_SESSION['company_id'];
	}

	/* para este informe en especial, se necesita que se muestren SOLO:
			- los contratos activos (finalizado = 0)
			- los contratos inactivos pero sólo aquellos que tengan algo facturado el año que se elige (finalizado = -1)
	 */
	$mesesFacturados = '	(ISNULL(Enero, 0) > 0) 
						OR 	(ISNULL(Febrero, 0) > 0) 
						OR 	(ISNULL(Marzo, 0) > 0) 
						OR 	(ISNULL(Abril, 0) > 0) 
						OR 	(ISNULL(Mayo, 0) > 0) 
						OR 	(ISNULL(Junio, 0) > 0) 
						OR 	(ISNULL(Julio, 0) > 0) 
						OR 	(ISNULL(Agosto, 0) > 0) 
						OR 	(ISNULL(Septiembre, 0) > 0) 
						OR 	(ISNULL(Octubre, 0) > 0) 
						OR 	(ISNULL(Noviembre, 0) > 0) 
						OR 	(ISNULL(Diciembre, 0) > 0) ';
	$activos = 'Finalizado = 0';
	$inactivosFactura = 'Finalizado = -1 AND ( '.$mesesFacturados.' )';

	$custom_query .= ' AND ( '.$activos.' OR ( '.$inactivosFactura.' ) )';

	if (empty($_GET)) {
		$importes_contratos = '';
	} else {
		$importes_contratos = get_db_data(array('listar-importes-contratos-i19',$custom_query));
	}

	if (cG('excel') == 'true') {
		$separator=';';
			
		$csv='sep=;'."\n\n";
	 	 	
		$csv.='Código Proyecto'.$separator.'Descripción'.$separator.'Inicial'.$separator.'Enero'.$separator.'Febrero'.$separator.'Marzo'.$separator.'Abril'.$separator.'Mayo'.$separator.'Junio'.$separator.'Julio'.$separator.'Agosto'.$separator.'Septiembre'.$separator.'Octubre'.$separator.'Noviembre'.$separator.'Diciembre'.$separator.$separator.$separator.'Importe proyecto a origen'.$separator.'Facturado a origen'.$separator.$separator.'Importe proyecto a origen año actual'.$separator.'Facturado a origen año actual'.$separator.'Pendiente de ejecución año actual'.$separator."\n";

		if(is_array($importes_contratos)) {
			$aTotalesOrigenActual = array();
			$n=1;

			foreach ($importes_contratos as $i) {
				$csv.=
						'"'		.trim(	$i['codigoproyecto']							)		.'"'.$separator.''.
						'"'		.trim(	utf8_decode($i['descripcion'])					)		.'"'.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Inicial'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Enero'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Febrero'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Marzo'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Abril'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Mayo'])				)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Junio'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Julio'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Agosto'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Septiembre'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Octubre'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Noviembre'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['Diciembre'])		)		.''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['ProyectoOrigen'])			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['FacturadoOrigen'])			)		.''.$separator.''.
						''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['ProyectoOrigenEjercicio'])	)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['FacturadoOrigenEjercicio'])	)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($i['PendienteEjecucion'])		)		.''.$separator.''.
						"\n";

				$n++;

				$aTotalesOrigenActual['Proyecto'] = $aTotalesOrigenActual['Proyecto'] + $i['ProyectoOrigenEjercicio'];
				$aTotalesOrigenActual['Facturado'] = $aTotalesOrigenActual['Facturado'] + $i['FacturadoOrigenEjercicio'];
				$aTotalesOrigenActual['Pendiente'] = $aTotalesOrigenActual['Pendiente'] + $i['PendienteEjecucion'];
			}

			if (isset($aTotalesOrigenActual['Proyecto'])) {
				$csv .= ''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						''.$separator.''.
						'TOTALES: '.$separator.''.
						''		.trim(	aux_money_format_noeuro($aTotalesOrigenActual['Proyecto'])	)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($aTotalesOrigenActual['Facturado'])	)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($aTotalesOrigenActual['Pendiente'])	)		.''.$separator.''.
						"\n";
			}
		}

		/* filas de totales de adjudicaciones mensuales y carteras de cada año */
		$importes_totales = get_db_data(array('listar-importes-totales-contratos-i19', ' WHERE codigoempresa='.$_SESSION['company_id']));
		if (is_array($importes_totales)) {
			$aTotales = array();

			foreach ($importes_totales as $i) {
				$esFacturacion = false;

				if (substr($i['codigoproyecto'], 0, 2) == 'FM') {
					$esFacturacion = true;
				}

				$csv.=
					''.$separator.''.
					'"'		.trim(	utf8_decode($i['descripcion'])					)		.'"'.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Inicial'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Enero'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Febrero'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Marzo'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Abril'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Mayo'])				)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Junio'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Julio'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Agosto'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Septiembre'])		)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Octubre'])			)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Noviembre'])		)		.''.$separator.''.
					''		.trim(	aux_money_format_noeuro($i['Diciembre'])		)		.''.$separator.'';

					/* si es facturacion mensual tiene pendiente de ejecucion */	
					if ($esFacturacion) {
						$csv .= ''.$separator.''.
								''.$separator.''.
								''.$separator.''.
								''.$separator.''.
								''.$separator.''.
								''.$separator.''.
								''.$separator.''. // en el excel dejo una separacion mas
								''.trim(aux_money_format_noeuro($i['PendienteEjecucion'])).''.$separator.'';

						/* total de facturacion acumulado (se coge el primero y se suma el siguiente) */
						$aTotales['Inicial'] = $i['Inicial'];
						$aTotales['Enero'] = $i['Enero'] + $i['Inicial'];
						$aTotales['Febrero'] = $i['Febrero'] + $aTotales['Enero'];
						$aTotales['Marzo'] = $i['Marzo'] + $aTotales['Febrero'];
						$aTotales['Abril'] = $i['Abril'] + $aTotales['Marzo'];
						$aTotales['Mayo'] = $i['Mayo'] + $aTotales['Abril'];
						$aTotales['Junio'] = $i['Junio'] + $aTotales['Mayo'];
						$aTotales['Julio'] = $i['Julio'] + $aTotales['Junio'];
						$aTotales['Agosto'] = $i['Agosto'] + $aTotales['Julio'];
						$aTotales['Septiembre'] = $i['Septiembre'] + $aTotales['Agosto'];
						$aTotales['Octubre'] = $i['Octubre'] + $aTotales['Septiembre'];
						$aTotales['Noviembre'] = $i['Noviembre'] + $aTotales['Octubre'];
						$aTotales['Diciembre'] = $i['Diciembre'] + $aTotales['Noviembre'];
					}

				$csv .= "\n";
			}

			$csv .=
					''.$separator.''.
					'FACTURACIÓN MENSUAL ACUMULADO'.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Inicial'])	)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Enero'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Febrero'])	)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Marzo'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Abril'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Mayo'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Junio'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Julio'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Agosto'])		)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Septiembre'])	)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Octubre'])	)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Noviembre'])	)		.''.$separator.''.
					''		.trim( aux_money_format_noeuro($aTotales['Diciembre'])	)		.''.$separator.''.
					"\n";
		}

		$csv = utf8_decode($csv);

		return $csv;

	} else {

		$trs1 = '';
		$trs2 = '';
		$trs3 = '';
		$aTotalesOrigenActual = array();
		$n=1;
		if(is_array($importes_contratos)) {
			foreach ($importes_contratos as $i) {
				$colorFila = ' oye ' . $i['Finalizado'];

				// las inactivas tendran color de fila
				if ($i['Finalizado'] == '-1') {
					$colorFila = 'style="background-color:#bbb"';
				}

				$trs1.=
					'
					<tr class="rowTabla" '.$colorFila.'>
						<td>'.$n.'</td>
						<td style="min-width:5px">'.$i['codigoproyecto'].'</td>
						<td>'.$i['descripcion'].'</td>
						<td class="right" name="'.$i['Inicial'].'">'.aux_money_format_noeuro($i['Inicial']).'</td>				
						<td class="colMes" name="'.$i['Enero'].'">'.aux_money_format_noeuro($i['Enero']).'</td>
						<td class="colMes" name="'.$i['Febrero'].'">'.aux_money_format_noeuro($i['Febrero']).'</td>
						<td class="colMes" name="'.$i['Marzo'].'">'.aux_money_format_noeuro($i['Marzo']).'</td>
						<td class="colMes" name="'.$i['Abril'].'">'.aux_money_format_noeuro($i['Abril']).'</td>
						<td class="colMes" name="'.$i['Mayo'].'">'.aux_money_format_noeuro($i['Mayo']).'</td>
						<td class="colMes" name="'.$i['Junio'].'">'.aux_money_format_noeuro($i['Junio']).'</td>
						<td class="colMes" name="'.$i['Julio'].'">'.aux_money_format_noeuro($i['Julio']).'</td>
						<td class="colMes" name="'.$i['Agosto'].'">'.aux_money_format_noeuro($i['Agosto']).'</td>
						<td class="colMes" name="'.$i['Septiembre'].'">'.aux_money_format_noeuro($i['Septiembre']).'</td>
						<td class="colMes" name="'.$i['Octubre'].'">'.aux_money_format_noeuro($i['Octubre']).'</td>
						<td class="colMes" name="'.$i['Noviembre'].'">'.aux_money_format_noeuro($i['Noviembre']).'</td>
						<td class="colMes" name="'.$i['Diciembre'].'">'.aux_money_format_noeuro($i['Diciembre']).'</td>
						<td style="min-width:1px;max-width:1px"></td>
						<td class="right" name="'.$i['ProyectoOrigen'].'">'.aux_money_format_noeuro($i['ProyectoOrigen']).'</td>
						<td class="right" name="'.$i['FacturadoOrigen'].'">'.aux_money_format_noeuro($i['FacturadoOrigen']).'</td>
						<td style="min-width:1px;max-width:1px"></td>
						<td class="right" name="'.$i['ProyectoOrigenEjercicio'].'">'.aux_money_format_noeuro($i['ProyectoOrigenEjercicio']).'</td>
						<td class="right" name="'.$i['FacturadoOrigenEjercicio'].'">'.aux_money_format_noeuro($i['FacturadoOrigenEjercicio']).'</td>
						<td class="right" name="'.$i['PendienteEjecucion'].'">'.aux_money_format_noeuro($i['PendienteEjecucion']).'</td>					
					</tr>
				';
				$n++;

				$aTotalesOrigenActual['Proyecto'] = $aTotalesOrigenActual['Proyecto'] + $i['ProyectoOrigenEjercicio'];
				$aTotalesOrigenActual['Facturado'] = $aTotalesOrigenActual['Facturado'] + $i['FacturadoOrigenEjercicio'];
				$aTotalesOrigenActual['Pendiente'] = $aTotalesOrigenActual['Pendiente'] + $i['PendienteEjecucion'];
			}

			if (isset($aTotalesOrigenActual['Proyecto'])) {
				$trs2 .= '<tr class="borde-abajo">
							<td class="right" colspan="20">TOTALES: </td>
							<td class="right">'.aux_money_format_noeuro($aTotalesOrigenActual['Proyecto']).'</td>
							<td class="right">'.aux_money_format_noeuro($aTotalesOrigenActual['Facturado']).'</td>
							<td class="right">'.aux_money_format_noeuro($aTotalesOrigenActual['Pendiente']).'</td>
						</tr>';
			}

			/* filas de totales de adjudicaciones mensuales y carteras de cada año */
			$importes_totales = get_db_data(array('listar-importes-totales-contratos-i19', ' WHERE codigoempresa='.$_SESSION['company_id']));
			if(is_array($importes_totales)) {
				$ejBase = $ejercicio; // se parte de, como maximo, el ejercicio que ha seleccionado en el desplegable, cuando se llegue a este se vuelve a poner la clase para partir en secciones
				foreach ($importes_totales as $i) {
					/* si ha filtrado por un ejercicio anterior al actual, no debe salir el último, por ejemplo: si pongo 2017, no debe salirme la adjudicacion del 2018 */
					if ($ejercicio < $i['Ejercicio']) {
						continue;
					}

					$esFacturacion = '';
					$borde = '';
					$ejActual = $i['Ejercicio'];
					if ($ejBase == $ejActual) {
						$borde = 'borde';
					}

					if (substr($i['codigoproyecto'], 0, 2) == 'FM') {
						$esFacturacion = ' rowFooter footerTotalFacturacion';
					}

					$trs3 .= 
					'<tr style="text-align:right" class="'.$borde.' '.$esFacturacion.'">
						<td colspan="3">'.$i['descripcion'].'</td>
						<td name="'.$i['Inicial'].'">'.aux_money_format_noeuro($i['Inicial']).'</td>
						<td name="'.$i['Enero'].'">'.aux_money_format_noeuro($i['Enero']).'</td>
						<td name="'.$i['Febrero'].'">'.aux_money_format_noeuro($i['Febrero']).'</td>
						<td name="'.$i['Marzo'].'">'.aux_money_format_noeuro($i['Marzo']).'</td>
						<td name="'.$i['Abril'].'">'.aux_money_format_noeuro($i['Abril']).'</td>
						<td name="'.$i['Mayo'].'">'.aux_money_format_noeuro($i['Mayo']).'</td>
						<td name="'.$i['Junio'].'">'.aux_money_format_noeuro($i['Junio']).'</td>
						<td name="'.$i['Julio'].'">'.aux_money_format_noeuro($i['Julio']).'</td>
						<td name="'.$i['Agosto'].'">'.aux_money_format_noeuro($i['Agosto']).'</td>
						<td name="'.$i['Septiembre'].'">'.aux_money_format_noeuro($i['Septiembre']).'</td>
						<td name="'.$i['Octubre'].'">'.aux_money_format_noeuro($i['Octubre']).'</td>
						<td name="'.$i['Noviembre'].'">'.aux_money_format_noeuro($i['Noviembre']).'</td>
						<td name="'.$i['Diciembre'].'">'.aux_money_format_noeuro($i['Diciembre']).'</td>';
					$trs3 .= '</tr>';
				}
			}
		}

		$trs = $trs1 . $trs2 . $trs3;
		
		$table_body=$trs;
		
		return $table_body;
	}
	
}


function make_filter_20() {
	
	$filtro					=	file_get_contents_utf8(ABSPATH.'/template/filtro-20.html');

	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a id="btnExcel" target="_blank" style="margin-left: 20px" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';
	} else {		
		/* por defecto el año actual y Todos los niveles */
		$anioActual = date('Y');
		$excel_button='<a id="btnExcel" target="_blank" style="margin-left: 20px" href="?ejercicios='.$anioActual.'&excel=true" class="btn btn-default btn-sm">Excel</a>';
	}

	$filtro 				=	str_replace('{EXCEL_URL}',$excel_button,$filtro);
	
	return $filtro;
}

?>