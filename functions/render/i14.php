<?php
if(!isset($_SESSION)) { 
   session_start(); 
}
function render_tabla_i14() {
	$trs			=	'';
	$custom_query	=	' WHERE CodigoEmpresa='.$_SESSION['company_id'].' ';
	$banco 			= 	cG('banco');
	$fecha_inicio	=	cG('start');
	$fecha_fin		=	cG('end');
	$efecto			=	cG('efecto');
	
	if (isset($banco)) {
		if ($banco == 0) { // todos (CHECKBOX SIN BANCO!)
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
			$nombre_banco = obtener_nombre_banco($banco);
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
			$fi = str_replace("/", "-", $fi);
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
			$ff = str_replace("/", "-", $ff);
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
			$nombre_efecto = obtener_nombre_tipo_efecto($ef);
			$nombre_efecto = utf8_encode($nombre_efecto);
			/* quitarle la tilde si es Préstamo !!!!! */
			if (strpos($nombre_banco, 'Ñ') !== false) {
			   	$arr = explode("Ñ", $nombre_banco, 2);
				$nombre_banco = $arr[0];
			}
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

	if (!empty($_GET)) {
		$previsiones_pago = get_db_data(array('listar-previsiones-pago-i14',$custom_query));
	}
		
	if (cG('excel')=='true') {
		$separator=';';
			
		$csv='sep=;'."\n";

		$csv.='Banco'.$separator.'Tipo Efecto'.$separator.'Cuenta'.$separator.'IBAN'.$separator.'Total'.$separator.'Saldo'.$separator.'Diferencia'."\n";

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
									$csv.= 	'"'		.trim(	utf8_decode($v[0])				)		.'"'.$separator.$separator.$separator.$separator.''.
											''		.trim(	$v[1]							)		.''.$separator.''.
											''		.trim(	aux_money_format_noeuro($v[2])	)		.''.$separator.''.
											''		.trim(	aux_money_format_noeuro($v[3])	)		.''.$separator.''.
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
						'"'		.trim(	guionIBAN($p['IBAN'])						)		.'"'.$separator.''.
						''		.trim(	aux_money_format_noeuro($p['ImporteEfecto']))		.''.$separator.''.
						"\n";
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			foreach($_POST as $fila => $v) {
				if(strpos($fila, 'aFila'.$i) === 0) {
			    	if (is_array($v)) {
						$csv.= 	'"'		.trim(	utf8_decode($v[0])				)		.'"'.$separator.$separator.$separator.$separator.''.
								''		.trim(	$v[1]							)		.''.$separator.''.
								''		.trim(	aux_money_format_noeuro($v[2])	)		.''.$separator.''.
								''		.trim(	aux_money_format_noeuro($v[3])	)		.''.$separator.''.
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
			$ibanActual = '-1'; //se da el caso de iban vacio!
			$contrapartidaActual = '-1';
			$inicioTabla = true; //controlar que no sea el primer banco que se está leyendo para que no ponga la fila del sumatorio al inicio de la tabla
			$inicioTabla2 = true;
			foreach ($previsiones_pago as $p) {
				if ($ibanActual != $p['IBAN']) {
					if (!$inicioTabla2) {
						$trs.=
							'
							<tr style="background-color:#F8AB8F">
								<td colspan="5">Total '.$bancoActual.' - IBAN '.$ibanActual.' - '.$contrapartidaActual.'</td>
								<td style="text-align:right">'.aux_money_format_noeuro($sumaImporteIban).'</td>
								<td></td>
								<td></td>
							</tr>
						';
					}
					$sumaImporteIban = 0;
					$ibanActual = $p['IBAN'];
					$contrapartidaActual = $p['contrapartida'];
					$inicioTabla2 = false;
				}

				if ($bancoActual != $p['Banco']) {
					// si son bancos distintos Y NO HA EMPEZADO A SUMAR == 0, es la primera vez que entra aquí, por lo que pintar una fila con la sumatoria
					if ($sumaImporteBanco != 0 && !$inicioTabla) {
						$trs.=
							'
							<tr class="rowTotal" style="background-color:#bbb;font-weight:bold;">
								<td colspan="5">Total '.$bancoActual.'</td>
								<td class="tdImporte" style="text-align:right" name="'.$sumaImporteBanco.'">'.aux_money_format_noeuro($sumaImporteBanco).'</td>
								<td class="tdSaldo" name=""><input type="text" class="txtSaldo form-control" value=""/></td>
								<td class="tdDiferencia" style="text-align:right"><span class="spanDiferencia" name=""></span></td>
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
						<td>'.guionIBAN($p['IBAN']).'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($p['ImporteEfecto']).'</td>		
					</tr>
				';
				$sumaImporteBanco += $p['ImporteEfecto'];
				$sumaImporteIban += $p['ImporteEfecto'];
				$n++;
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			$trs.='<tr style="background-color:#F8AB8F">
						<td colspan="5">Total '.$bancoActual.' - IBAN '.$ibanActual.' - '.$contrapartidaActual.'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($sumaImporteIban).'</td>
						<td></td>
						<td></td>
					</tr>';
			$trs.=
				'
				<tr class="rowTotal" style="background-color:#bbb;font-weight:bold;">
					<td colspan="5">Total '.$bancoActual.'</td>
					<td class="tdImporte" style="text-align:right" name="'.$sumaImporteBanco.'">'.aux_money_format_noeuro($sumaImporteBanco).'</td>
					<td class="tdSaldo" name=""><input type="text" class="txtSaldo form-control" value=""/></td>
					<td class="tdDiferencia" style="text-align:right"><span class="spanDiferencia" name=""></span></td>
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


function make_filter_14 () {
	
	$filtro	= file_get_contents_utf8(ABSPATH.'/template/filtro-14.html');
	
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

	$filtro = str_replace('{EXCEL_URL}', $excel_button,$filtro);
	
	return $filtro;
}

function render_select_bancos () {
	$data=get_db_data(array('listar-bancos'));
	
	$current_value=cG('banco');
	
	$options='';
	$n=1;
	foreach ($data as $d) {
		//if ($current_value==$d['codigoempresa']) {$selected=' selected="selected" ';} else {$selected='';}
		if ($d['banco'] != 'SIN BANCO') {
			$options.='<option '.$selected.' value="'.$n.'">'.ucfirst($d['banco']).'</option>';
			$n++;
		}
	}
	
	return $options;
}

function render_select_tipos_efecto ($porDefecto) { //son checkbox!
	session_start();
	$data = get_db_data(array('listar-tipos-efecto'));
	
	$options = '';
	$n = 1;
	foreach ($data as $d) {
		if ($d['tipoefecto'] != '') {
			$checked = '';
			if ($porDefecto && (
					$d['tipoefecto'] == 'Confirming Sin Recurso' 
				|| $d['tipoefecto'] == 'Contado' 
				|| $d['tipoefecto'] == 'Giros Bancarios' 
				|| $d['tipoefecto'] == 'Prestamo' 
				|| $d['tipoefecto'] == 'Pagares'
				|| $d['tipoefecto'] == 'Transferencia Bancaria') ) {
				$checked = 'checked';
			}
			if (in_array($n, $_SESSION['efecto'])) {
				$checked = 'checked';
			}
			/* SIN TIPO son los que tienen fecha de vencimiento y NO TIENEN CONTRAPARTIDA, pero realmente estos son los que se llaman SIN BANCO .. */
			if ($d['tipoefecto'] != 'Sin Tipo') {
				$options .= '<td style="padding-right:25px"> <input type="checkbox" id="efecto-'.$n.'" name="efecto[]" value="'.$n.'" '.$checked.'><label for="efecto-'.$n.'">'.ucfirst($d['tipoefecto']).'</label><td>';
				$n++;
			}
		}
	}
	/* poner sin banco exterior */
	if (in_array($n, $_SESSION['efecto'])) {
		$checked2 = 'checked';
	}
	$options .= '<td style="padding-right:25px"> <input type="checkbox" id="efecto-'.$n.'" name="efecto[]" value="'.$n.'" '.$checked2.'><label for="efecto-'.$n.'">Sin Banco</label><td>';
	
	return $options;
}

function obtener_nombre_banco ($id) {
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

function obtener_nombre_tipo_efecto ($id) {
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

function guionIBAN ($str) {
    return implode("-", str_split($str, 4));
}

?>