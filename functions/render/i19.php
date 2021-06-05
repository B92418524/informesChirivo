<?php
if(!isset($_SESSION)) { 
   session_start(); 
}
function render_tabla_i19()
{
	$trs='';
	$custom_query='';
	
	if (cG('ejercicios')) {
		$ejercicio = cG('ejercicios');
		if ($ejercicio != 0) {
			$custom_query = 'WHERE Ejercicio='.$ejercicio;
		}
	}

	if (cG('obras')) {
		$obras = cG('obras');
		if ($obras[0] != 0) { // si el primero que pone es todas las obras
			$contador = 0;
			$where = '';
			foreach ($obras as $o) {
				if ($contador == 0) {
					$where = '('; //empezar la condicion
				}
				if ($contador > 0 && $contador < count($obras)) {
					$where .= ' OR ';
				}
				$where .= ' codigoproyecto='.$o;
				$contador++;
			}
			if ($where != '') {
				$where .= ')'; //cerrar la condicion
				if ($custom_query == '') {
					$custom_query .= ' WHERE '.$where;
				} else {
					$custom_query .= ' AND '.$where;
				}
				$_SESSION['obras'] = $obras;
			}
		}
	}

	if (cG('estados')) {
		$estados = cG('estados');
		if ($estados != 'todas') { // si no quiere ver todas las obras...
			if ($estados == 'activas' || $estados == 'inactivas') {
				if ($estados == 'activas') {
					$estados = '0'; // es finalizado, es decir, que significa lo contrario, finalizado = 0 >>>> estado = 0 >> ACTIVA
				} else {
					$estados = '-1'; // estan finalizadas, es decir, inactivas
				}
				if ($custom_query == '') {
					$custom_query .= ' WHERE Finalizado='.$estados;
				} else {
					$custom_query .= ' AND Finalizado='.$estados;
				}
			} else if ($estados == 'ptes') { // pendientes de facturacion, donde el importe es mayor que 0
				// 28/12/2017 los pendientes ahora son: las activas o las inactivas con pteFacturacion distinto de cero

				if ($custom_query == '') {
					$custom_query .= ' WHERE ';
				} else {
					$custom_query .= ' AND ';
				}

				$w = " ( (Finalizado=0) OR (Finalizado=-1 AND PendienteEjecucion != 0) ) ";
				
				// if ($custom_query == '') {
				// 	$custom_query .= ' WHERE PendienteEjecucion > 0';
				// } else {
				// 	$custom_query .= ' AND PendienteEjecucion > 0';
				// }

				$custom_query .= $w;
			}			
		}
	}

	if ($custom_query == '') {
		$custom_query .= ' WHERE codigoempresa='.$_SESSION['company_id'];
	} else {
		$custom_query .= ' AND codigoempresa='.$_SESSION['company_id'];
	}

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

			/* fila de totales de cada mes */
			// $csv.=
			// 			''.$separator.''.
			// 			''.$separator.''.
			// 			'TOTAL'.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Inicial'])	)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Enero'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Febrero'])	)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Marzo'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Abril'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Mayo'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Junio'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Julio'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Agosto'])		)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Septiembre'])	)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Octubre'])	)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Noviembre'])	)		.''.$separator.''.
			// 			''		.trim( aux_money_format_noeuro($aTotales['Diciembre'])	)		.''.$separator.''.
			// 			"\n";
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

			/* fila de totales de cada mes */
			// $trs .= 
			// 	'<tr style="border-top:3px solid black;background-color:#96C7CE;text-align:right">
			// 		<td colspan="3">TOTAL</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Inicial']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Enero']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Febrero']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Marzo']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Abril']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Mayo']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Junio']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Julio']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Agosto']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Septiembre']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Octubre']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Noviembre']).'</td>
			// 		<td>'.aux_money_format_noeuro($aTotales['Diciembre']).'</td>				
			// 	</tr>';

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


function make_filter_19 () {
	
	$filtro					=	file_get_contents_utf8(ABSPATH.'/template/filtro-19.html');

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
	
	//$filtro					=	str_replace('{EJERCICIOS}',$proyectos_venta,$filtro);

	$filtro 				=	str_replace('{EXCEL_URL}',$excel_button,$filtro);
	
	return $filtro;
}

function render_listar_obras_contratacion () {
	$data=get_db_data(array('listar-obras-contratacion-i19', ' WHERE codigoempresa='.$_SESSION['company_id']));
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

		$options.='<option '.$selected.' value="'.$d['codigoproyecto'].'">'.$d['codigoproyecto'].' - '.ucfirst($d['descripcion']).'</option>';
	}
	return $options;
}

function render_tabla_facturacion_mes_i19() {
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

?>