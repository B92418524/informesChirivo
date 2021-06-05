<?php
if(!isset($_SESSION)) { 
   session_start(); 
}

function render_tabla_i15 () {
	$trs			=	'';
	$custom_query	=	' WHERE CodigoEmpresa='.$_SESSION['company_id'].' ';
	$banco 			= 	cG('banco');
	$fecha_inicio	=	cG('start');
	$fecha_fin		=	cG('end');
	$efecto			=	cG('efecto');
	$proveedor		=	cG('proveedor');

	if (isset($banco)) {
		if ($banco == 0) { // todos
			$sinBanco = false;
			if (isset($efecto)) {
				/* el efecto de sin banco es el numero 8 */
				foreach ($efecto as $ef) {
					if ($ef == 8) {
						$sinBanco = true;
					}
				}
			}
			$custom_query .= '';
			if (!$sinBanco) {
				$custom_query .= " AND BANCO NOT LIKE 'SIN BANCO' ";
			}
		} else {
			$nombre_banco = obtener_nombre_banco_i15($banco);
			$nombre_banco = utf8_encode($nombre_banco);
			/* quitarle las Ñ !!!!! */
			if (strpos($nombre_banco, 'Ñ') !== false) {
			   	$arr = explode("Ñ", $nombre_banco, 2);
				$nombre_banco = $arr[0];
			}
			$custom_query .= " AND Banco LIKE '%".$nombre_banco."%'";
		}
	}

	if (isset($fecha_inicio)) {
		if ($fecha_inicio != '') {
			$fi = "fechaVencimiento>='".sql_date($fecha_inicio,'es')."' ";

			if ($custom_query == '') {
				$custom_query .= " WHERE ".$fi;
			} else {
				$custom_query .= " AND ".$fi;
			}
		}
	}

	if (isset($fecha_fin)) {
		if ($fecha_fin != '') {
			$ff = "fechaVencimiento<='".sql_date($fecha_fin,'es')."' ";

			if ($custom_query == '') {
				$custom_query .= ' WHERE '.$ff;
			} else {
				$custom_query .= ' AND '.$ff;
			}
		}
	}

	if (isset($efecto)) {
		$_SESSION['efecto'] = array();
		$contador = 0;
		foreach ($efecto as $ef) {
			if ($contador == 0) {
				$e = '('; //empezar la condicion
			}
			if ($contador > 0 && $contador < count($efecto)) {
				$e .= ' OR ';
			}
			$nombre_efecto = obtener_nombre_tipo_efecto_i15($ef);
			$nombre_efecto = utf8_encode($nombre_efecto);
			$e .= "TipoEfecto='".$nombre_efecto."'";
			$contador++;
		}
		if ($e != '') {
			$e .= ')'; //cerrar la condicion
			if ($custom_query == '') {
				$custom_query .= ' WHERE '.$e;
			} else {
				$custom_query .= ' AND '.$e;
			}
			$_SESSION['efecto'] = $efecto;
		}
	}

	if (isset($proveedor)) {
		if ($proveedor != '' && $proveedor != '0') {
			$pr = "CodigoProveedor='".$proveedor."' ";

			if ($custom_query == '') {
				$custom_query .= ' WHERE '.$pr;
			} else {
				$custom_query .= ' AND '.$pr;
			}
		}
	}
	
	if (!empty($_GET)) {
		$previsiones_pago = get_db_data(array('listar-previsiones-pago-detallado-i15',$custom_query));
	}

	if (cG('excel')=='true') {
		$separator=';';
			
		$csv='sep=;'."\n";

		$csv.='Banco'.$separator.'Tipo Efecto'.$separator.'Cuenta'.$separator.'Codigo Proveedor'.$separator.'Proveedor'.$separator.'Fecha Vencimiento'.$separator.'Total'.$separator.'Saldo'.$separator.'Diferencia'."\n";

		if(is_array($previsiones_pago)) {
			$i = 0;
			$bancoActual = '';
			$inicioTabla = true;
			foreach ($previsiones_pago as $p) {
				if ($bancoActual != $p['Banco']) {
					if (!$inicioTabla) {
						/* se trae por POST un array con todos los valores de la tabla */
						foreach($_POST as $fila => $v) {
						    if(strpos($fila, 'aFila'.$i) === 0) {
						    	if (is_array($v)) {
									$csv.= 	'"'		.trim(	utf8_decode($v[0])				)		.'"'.$separator.$separator.$separator.$separator.$separator.$separator.''.
											''		.trim(	$v[1]							)		.''.$separator.''.
											"\n";
									break;
								}		       
						    }
						}
					}
					$bancoActual = $p['Banco'];
					$inicioTabla = false;
					$i++; //pasa al siguiente total del array post
				}
				$csv.=
						'"'		.trim(	$p['Banco']									)		.'"'.$separator.''.
						'"'		.trim(	$p['TipoEfecto']							)		.'"'.$separator.''.
						'"'		.trim(	$p['contrapartida']							)		.'"'.$separator.''.
						'"'		.trim(	$p['CodigoProveedor']						)		.'"'.$separator.''.
						'"'		.trim(	$p['Proveedor']								)		.'"'.$separator.''.
						'"'		.trim(	date_normalizer($p['fechaVencimiento'])		)		.'"'.$separator.''.
						''		.trim(	aux_money_format_noeuro($p['ImporteEfecto']))		.''.$separator.''.
						"\n";
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			foreach($_POST as $fila => $v) {
				if(strpos($fila, 'aFila'.$i) === 0) {
			    	if (is_array($v)) {
						$csv.= 	'"'		.trim(	utf8_decode($v[0])				)		.'"'.$separator.$separator.$separator.$separator.$separator.$separator.''.
								''		.trim(	$v[1]							)		.''.$separator.''.
								"\n";
					}		       
			    }
			}
		}

		return $csv;

	} else {

		$n=1;
		if(is_array($previsiones_pago)) {
			$bancoActual = '';
			$inicioTabla = true; //controlar que no sea el primer banco que se está leyendo para que no ponga la fila del sumatorio al inicio de la tabla
			foreach ($previsiones_pago as $p) {
				if ($bancoActual != $p['Banco']) {
					// si son bancos distintos Y NO HA EMPEZADO A SUMAR == 0, es la primera vez que entra aquí, por lo que pintar una fila con la sumatoria
					if ($sumaImporteBanco != 0 && !$inicioTabla) {
						$trs.=
							'
							<tr class="rowTotal" style="background-color:#bbb">
								<td colspan="7"><b>Total '.$bancoActual.'</b></td>
								<td class="tdImporte" style="text-align:right" name="'.$sumaImporteBanco.'"><b>'.aux_money_format_noeuro($sumaImporteBanco).'</b></td>
							</tr>
						';
					}
					$sumaImporteBanco = 0;
					$bancoActual = $p['Banco'];
					$inicioTabla = false;
				}
				$trs.=
					'
					<tr>
						<td>'.$n.'</td>
						<td>'.$p['Banco'].'</td>
						<td>'.$p['TipoEfecto'].'</td>
						<td>'.$p['contrapartida'].'</td>
						<td>'.$p['CodigoProveedor'].'</td>
						<td>'.$p['Proveedor'].'</td>
						<td>'.date_normalizer($p['fechaVencimiento']).'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($p['ImporteEfecto']).'</td>		
					</tr>
				';
				$sumaImporteBanco += $p['ImporteEfecto'];
				$n++;
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			$trs.=
				'
				<tr class="rowTotal" style="background-color:#bbb">
					<td colspan="7"><b>Total '.$bancoActual.'</b></td>
					<td class="tdImporte" style="text-align:right" name="'.$sumaImporteBanco.'"><b>'.aux_money_format_noeuro($sumaImporteBanco).'</b></td>
				</tr>
			';
		} else {
			$trs = '<tr>
						<td colspan="8">No hay registros.</td>
					</tr>';
		}
		
		$table_body=$trs;
		
		return $table_body;

	}
	
}


function make_filter_15 () {
	
	$filtro	= file_get_contents_utf8(ABSPATH.'/template/filtro-15.html');
	
	//$ejercicios				=	render_listar_ejercicios_i12();

	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a id="btnExcel" target="_blank" style="margin-left: 20px" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';
	} else {		
		/* por defecto esta 2016 y Todos los niveles */
		$excel_button='<a id="btnExcel" target="_blank" style="margin-left: 20px" href="?excel=true" class="btn btn-default btn-sm">Excel</a>';
	}
	
	//$filtro					=	str_replace('{EJERCICIOS}',$proyectos_venta,$filtro);

	$filtro = str_replace('{EXCEL_URL}',$excel_button,$filtro);
	
	return $filtro;
}

function obtener_nombre_banco_i15 ($id) {
	$data=get_db_data(array('listar-bancos'));
	$n = 1;
	foreach ($data as $d) {
		if ($id == $n) {
			return $d['banco'];
			break;
		}
		$n++;
	}
}

function obtener_nombre_tipo_efecto_i15 ($id) {
	$data=get_db_data(array('listar-tipos-efecto'));
	$n = 1;
	foreach ($data as $d) {
		if ($d['tipoefecto'] != '' && $d['tipoefecto'] != 'Sin Tipo') {
			if ($id == $n) {
				return $d['tipoefecto'];
				break;
			}
			$n++;
		}
	}
}

?>