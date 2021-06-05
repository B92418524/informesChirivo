<?php
function render_tabla_i16() {
	$trs			=	'';
	$custom_query	=	'';
	$proyecto		=	cG('project');
	$proveedor		=	cG('proveedor');

	if (isset($proyecto)) {
		if ($proyecto != '' && $proyecto != '0') {
			$proy = "codigoproyecto='".$proyecto."' ";

			if ($custom_query == '') {
				$custom_query .= ' WHERE '.$proy;
			} else {
				$custom_query .= ' AND '.$proy;
			}
		}
	}

	if (isset($proveedor)) {
		if ($proveedor != '' && $proveedor != '0') {
			$prov = "codigoproveedor='".$proveedor."' ";

			if ($custom_query == '') {
				$custom_query .= ' WHERE '.$prov;
			} else {
				$custom_query .= ' AND '.$prov;
			}
		}
	}
	
	if (!empty($_GET)) {
		$facturacion_proveedores = get_db_data(array('listar-facturacion-proveedores-i16',$custom_query));
	}

	if (cG('excel')=='true') {
		$separator=';';
			
		$csv='sep=;'."\n";

		$csv.='Codigo Proveedor'.$separator.'Razon Social'.$separator.'Ejercicio'.$separator.'Serie'.$separator.'N Factura'.$separator.'Su Factura'.$separator.'Fecha emision'.$separator.'Codigo Proyecto'.$separator.'Proyecto'.$separator.'Base Imponible'.$separator.'Prontopago'.$separator.'Neto'.$separator.'Retencion'.$separator.'PPRetencion'.$separator.'RetencionPP'."\n";

		if(is_array($facturacion_proveedores)) {
			$sumaBI = 0;
			$sumaPronto = 0;
			$sumaNeto = 0;
			$sumaReten = 0;
			$sumaPPReten = 0;
			$sumaRetenPP = 0;
			$sumaTotalCuotaIva = 0;
			$sumaImporteLiquido = 0;
			foreach ($facturacion_proveedores as $p) {
				$csv.=
						'"'		.trim(	$p['codigoproveedor']							)		.'"'.$separator.''.
						'"'		.trim(	$p['razonsocial']								)		.'"'.$separator.''.
						'"'		.trim(	$p['EjercicioFactura']							)		.'"'.$separator.''.
						'"'		.trim(	$p['SerieFactura']								)		.'"'.$separator.''.
						'"'		.trim(	$p['numerofactura']								)		.'"'.$separator.''.
						'"'		.trim(	$p['sufacturano']								)		.'"'.$separator.''.
						'"'		.trim(	date_normalizer($p['fechaemision'])				)		.'"'.$separator.''.
						'"'		.trim(	$p['codigoproyecto']							)		.'"'.$separator.''.
						'"'		.trim(	$p['Proyecto']									)		.'"'.$separator.''.
						''		.trim(	number_format($p['BaseImponible'], 2, ',', '')	)		.''.$separator.''.
						''		.trim(	number_format($p['Prontopago'], 2, ',', '')		)		.''.$separator.''.
						''		.trim(	number_format($p['Neto'], 2, ',', '')			)		.''.$separator.''.
						''		.trim(	number_format($p['Retencion'], 2, ',', '')		)		.''.$separator.''.
						''		.trim(	number_format($p['PPRetencion'], 2, ',', '')	)		.''.$separator.''.
						''		.trim(	number_format($p['RetencionPP'], 2, ',', '')	)		.''.$separator.''.
						''		.trim(	number_format($p['totalcuotaiva'], 2, ',', '')	)		.''.$separator.''.
						''		.trim(	number_format($p['ImporteLiquido'], 2, ',', '')	)		.''.$separator.''.
						"\n";
				$sumaBI += $p['BaseImponible'];
				$sumaPronto += $p['Prontopago'];
				$sumaNeto += $p['Neto'];
				$sumaReten += $p['Retencion'];
				$sumaPPReten += $p['PPRetencion'];
				$sumaRetenPP += $p['RetencionPP'];
				$sumaTotalCuotaIva += $p['totalcuotaiva'];
				$sumaImporteLiquido += $p['ImporteLiquido'];
			}
			$csv.= 	'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					'"'		.trim(											)		.'"'.$separator.''.
					''		.trim(	number_format($sumaBI, 2, ',', '')		)		.''.$separator.''.
					''		.trim(	number_format($sumaPronto, 2, ',', '')	)		.''.$separator.''.
					''		.trim(	number_format($sumaNeto, 2, ',', '')	)		.''.$separator.''.
					''		.trim(	number_format($sumaReten, 2, ',', '')	)		.''.$separator.''.
					''		.trim(	number_format($sumaPPReten, 2, ',', '')	)		.''.$separator.''.
					''		.trim(	number_format($sumaRetenPP, 2, ',', '')	)		.''.$separator.''.
					''		.trim(	number_format($sumaTotalCuotaIva, 2, ',', '')	)		.''.$separator.''.
					''		.trim(	number_format($sumaImporteLiquido, 2, ',', '')	)		.''.$separator.''.
					"\n";
		}

		return $csv;

	} else {
		$n=1;
		if(is_array($facturacion_proveedores)) {
			$sumaBI = 0;
			$sumaPronto = 0;
			$sumaNeto = 0;
			$sumaReten = 0;
			$sumaPPReten = 0;
			$sumaRetenPP = 0;
			$sumaTotalCuotaIva = 0;
			$sumaImporteLiquido = 0;
			foreach ($facturacion_proveedores as $p) {
				$trs.=
					'
					<tr>
						<td>'.$n.'</td>
						<td>'.$p['codigoproveedor'].'</td>
						<td>'.$p['razonsocial'].'</td>
						<td>'.$p['EjercicioFactura'].'</td>
						<td>'.$p['SerieFactura'].'</td>
						<td>'.$p['numerofactura'].'</td>
						<td>'.$p['sufacturano'].'</td>
						<td>'.date_normalizer($p['fechaemision']).'</td>
						<td>'.$p['codigoproyecto'].'</td>
						<td>'.$p['Proyecto'].'</td>
						<td style="text-align:right">'.aux_money_format($p['BaseImponible']).'</td>
						<td style="text-align:right">'.aux_money_format($p['Prontopago']).'</td>
						<td style="text-align:right">'.aux_money_format($p['Neto']).'</td>
						<td style="text-align:right">'.aux_money_format($p['Retencion']).'</td>
						<td style="text-align:right">'.aux_money_format($p['PPRetencion']).'</td>
						<td style="text-align:right">'.aux_money_format($p['RetencionPP']).'</td>
						<td style="text-align:right">'.aux_money_format($p['totalcuotaiva']).'</td>
						<td style="text-align:right">'.aux_money_format($p['ImporteLiquido']).'</td>	
					</tr>
				';
				$n++;
				$sumaBI += $p['BaseImponible'];
				$sumaPronto += $p['Prontopago'];
				$sumaNeto += $p['Neto'];
				$sumaReten += $p['Retencion'];
				$sumaPPReten += $p['PPRetencion'];
				$sumaRetenPP += $p['RetencionPP'];
				$sumaTotalCuotaIva += $p['totalcuotaiva'];
				$sumaImporteLiquido += $p['ImporteLiquido'];
			}
			$trs.=
					'
					<tr style="font-weight:bold;border-top: 3px solid black;background-color:#96C7CE">
						<td colspan="10"></td>
						<td style="text-align:right">'.aux_money_format($sumaBI).'</td>
						<td style="text-align:right">'.aux_money_format($sumaPronto).'</td>
						<td style="text-align:right">'.aux_money_format($sumaNeto).'</td>
						<td style="text-align:right">'.aux_money_format($sumaReten).'</td>
						<td style="text-align:right">'.aux_money_format($sumaPPReten).'</td>
						<td style="text-align:right">'.aux_money_format($sumaRetenPP).'</td>
						<td style="text-align:right">'.aux_money_format($sumaTotalCuotaIva).'</td>
						<td style="text-align:right">'.aux_money_format($sumaImporteLiquido).'</td>	
					</tr>
				';
		} else {
			$trs = '<tr>
						<td colspan="18">No hay registros.</td>
					</tr>';
		}
		
		$table_body=$trs;
		
		return $table_body;

	}
	
}


function make_filter_16 () {
	
	$filtro	= file_get_contents_utf8(ABSPATH.'/template/filtro-16.html');
	
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

	$filtro	= str_replace('{EXCEL_URL}',$excel_button,$filtro);
	
	return $filtro;
}

?>