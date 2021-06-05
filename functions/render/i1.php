<?php

function render_tabla_i1 () {
	
	$proveedor		=	cG('pr');
	$fecha_inicio	=	cG('start');
	$fecha_fin		=	cG('end');
	$estado			=	cG('status');
	$borrado		=	cG('deleted');
    $sufac			=	cG('sufac');
    $impf			=	cG('impf');
	$vinculados		=	cG('vinculados');
	$giros			=	cG('giros');
	
	$query_to_show='';
	
	/*
	echo '<pre> $proveedor -> '.$proveedor.'</pre>';
	echo '<pre> $fecha_inicio -> '.$fecha_inicio.'</pre>';
	echo '<pre> $fecha_fin -> '.$fecha_fin.'</pre>';
	echo '<pre> $estado -> '.$estado.'</pre>';
	echo '<pre> $borrado -> '.$borrado.'</pre>';
	*/
	
	$company_id = $_SESSION['company_id'];
	
	$default_query=true;
	$where_query="WHERE CodigoEmpresa="."'".$company_id."'";
	
	if (isset($proveedor)) {
		if ($proveedor!='')	{
			$where_query.="AND codigoproveedor='".$proveedor."' ";
			$default_query=false;
		}
	}

	if (isset($fecha_inicio)) {
		if ($fecha_inicio!='') {
			$where_query.="AND fechaemision>='".sql_date($fecha_inicio,'es')."' ";
			$default_query=false;
		}
	}

	if (isset($fecha_fin)) {
		if ($fecha_fin!='')	{
			$where_query.="AND fechaemision<='".sql_date($fecha_fin,'es')."' ";
			$default_query=false;
		}
	}

	if (isset($estado))	{
		if ($estado!='') {
			$where_query.="AND estado LIKE '".$estado."' ";
			$default_query=false;
		}
	}

	if (isset($borrado)) {
		if ($borrado!='') {
			$where_query.="AND borrado='".$borrado."' ";
			$default_query=false;
		}
	}

    if (isset($sufac)) {
        if ($sufac!='') {
            $where_query.="AND sufacturano LIKE '%".$sufac."%' ";
			$default_query=false;
        }
    }

    if (isset($impf)) {
        if ($impf!='') {
            $impf=str_replace(',','.',$impf);
            $where_query.="AND importeefecto LIKE '".$impf."%' ";
			$default_query=false;
        }
    }

	if (isset($vinculados))	{
		if ($vinculados=='-1') {			
			$where_query.="AND vinculado <> '-1' ";
			$default_query=false;
		}
	}

	if (isset($giros)) {
		if ($giros=='0') {			
			$where_query.="AND tipoefecto <> 'Giros Bancarios' ";
			$default_query=false;
		}
	}
	
	$where_query=str_replace('WHERE AND', 'WHERE', $where_query);
	
	$where_query=str_replace(utf8_encode("Retención de Garantía"), "Retenci%", $where_query);
	
	if ($default_query) {
		$data=false;
		//$data=get_db_data(array('listado-completo'));
	} else {
		//$query_to_show='<tr><td colspan="12"><pre style="font-size:10px;">'.print_r($where_query,true).'</pre></td></tr>';
		$query_to_show='';
		$data=get_db_data(array('listado-completo-filtrado', $where_query));
	}

	/*
	$year_selected=cG('y');
		
	if ($year_selected!=='')
	{
		if (is_numeric($year_selected))
		{
			$default_from_date=$year_selected.'-01-01 00:00:00.000';
			$default_to_date=$year_selected.'-10-01 00:00:00.000';
			
			$where="WHERE fechaemision>='".$default_from_date."' AND fechaemision<='".$default_to_date."'";
			$data=get_db_data(array('listado-completo-filtrado', $where));
			//die('0');
		}
		//die('1');
	}
	else
	{
		$data=get_db_data(array('listado-completo'));
		//die('2');
	}
	*/

	
	if ($data===false) {
		$table_body=$query_to_show;
		$table_body.='<tr><td colspan="12" style="text-align:center; padding:40px;">No hay registros</td></tr>';
	} else {
		if (cG('excel')=='true') {
			$separator=';';
			
			$csv='sep=;'."\n";
			
			$csv.='Codigo Proveedor'.$separator.'Razon social'.$separator.'Su factura'.$separator.'Fecha emision'.$separator.'Fecha vencimiento'.$separator.'Tipo efecto'.$separator.'Estado'.$separator.'Importe efecto'.$separator.'Fecha emision pago'.$separator.'Contrapartida'.$separator.'Cuenta'.$separator.'ComunicadoSII'."\n";
						
			foreach ($data as $row)	{
				$importe=number_format($row['importeefecto'], 2, ',', '');

				$comunicadoSII = '';
				if ($row['ComunicadoSII'] == 0) {
					$comunicadoSII = 'No';
				} else if ($row['ComunicadoSII'] == -1) {
					$comunicadoSII = 'Sí';
				}
				
				$csv.=
					'"'		.trim(	$row['codigoproveedor']						)		.'"'.$separator.''.
					'"'		.trim(	$row['Razonsocial']							)		.'"'.$separator.''.
					'"0'		.trim(	$row['sufacturano']							)		.'"'.$separator.''.
					'"'		.trim(	date_normalizer($row['fechaemision'])		)		.'"'.$separator.''.
					'"'		.trim(	date_normalizer($row['fechavencimiento'])	)		.'"'.$separator.''.
					'"'		.trim(	$row['tipoefecto']							)		.'"'.$separator.''.
					'"'		.trim(	$row['estado']								)		.'"'.$separator.''.
					''		.trim(	$importe									)		.''.$separator.''.
					'"'		.trim(	date_normalizer($row['FechaEmisionPago'])	)		.'"'.$separator.''.
					'"'		.trim(	$row['contrapartida']						)		.'"'.$separator.''.
					'"'		.trim(	$row['cuenta']								)		.'"'.$separator.''.
					'"'		.trim(	$comunicadoSII								)		.'"'.$separator.''.
					
					"\n";					
				
			}
			
			return $csv;
			
		} else if (cG('print')=='true') {
			$table_body='';
			$table_body=$query_to_show;
			
			$suma=0;
			$codigoActual = '-1';
			$inicioTabla = true;
			foreach ($data as $row) {

				if ($codigoActual != $row['codigoproveedor']) {
					if ($sumaImporte != 0 && !$inicioTabla) {
						$table_body.=
							'<tr style="background-color:#F8AB8F !important;">
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="3"></td>
							</tr>';
					}
					$sumaImporte = 0;
					$codigoActual = $row['codigoproveedor'];
					$razonSocial = $row['Razonsocial'];
					$inicioTabla = false;
				}
                $comunicadoSII = '';
				if ($row['ComunicadoSII'] == 0) {
					$comunicadoSII = 'No';
				} else if ($row['ComunicadoSII'] == -1) {
					$comunicadoSII = 'Sí';
				}

				$table_body.=
				'
				<tr '.$style.' >
					<td>'.$row['codigoproveedor'].'</td>
					<td>'.$row['Razonsocial'].'</td>
					<td>0'.$row['sufacturano'].'</td>
					<td>'.date_normalizer($row['fechaemision']).'</td>
					<td>'.date_normalizer($row['fechavencimiento']).'</td>
					<td>'.utf8_encode($row['tipoefecto']).'</td>
					<td>'.$row['estado'].'</td>										
					<td style="text-align: right;">'.aux_money_format($row['importeefecto']).'</td>
					<td>'.date_normalizer($row['FechaEmisionPago']).'</td>
					<!-- <td>'.$row['contrapartida'].'</td> -->
					<td>'.utf8_encode($row['cuenta']).'</td>
					<td>'.$comunicadoSII.'</td>
				</tr>
				';
				$sumaImporte += $row['importeefecto'];
				$suma=$suma+$row['importeefecto'];
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			$table_body.=
							'<tr style="background-color:#F8AB8F !important;">
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="3"></td>
							</tr>';
			
			$table_body.=
				'
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>										
					<td style="text-align: right;"><b>'.aux_money_format($suma).'</b></td>
					<!-- <td></td> -->
					<td></td>
					<td></td>
					<td></td>
				</tr>
				';

            $pagina = '	<!DOCTYPE html>
						<html lang="es">
						<head>
						    <meta charset="utf-8">
						    <meta http-equiv="X-UA-Compatible" content="IE=edge">
						    <meta name="viewport" content="width=device-width, initial-scale=1">
						    <meta name="description" content="">
						    <meta name="author" content="">
						    <link rel="icon" href="../../favicon.ico">
						    <title>Informes</title>
	            			<style>
						        /*body {
						            min-height: 842px;
						            width: 595px;
						            margin-left: auto;
						            margin-right: auto;
						        }*/
						        .page {
						            height: 900px;
						        }
						        @page { 
						        	size: landscape; 
						        }
						        table {
						        	font-size: 10px;
						        	border: collapse;
						        }
						        table thead {
						        	background-color: #ccc !important;
						        }
						        table thead tr th {
						        	padding:5px;
						        }
						        table tbody tr {
						        	border: 1px solid black;
						        }
								table tbody tr td {
									padding:5px;
								}
								label {
									font-size:11px;
								}
								.titulo {
									font-size:18px;
									font-weight: normal;
								}
						    </style>
					    </head>
						<body onload="window.print()">
							<label class="titulo">INFORME DE EFECTOS PENDIENTES DE PROVEEDORES</label><br/>
							<label>FECHA DESDE: '.$fecha_inicio.'</label><br/>
							<label>FECHA HASTA: '.$fecha_fin.'</label>
							<table style="background-color: #fff">
			                    <thead>
			                        <tr>
			                            <th style="width:20px">Codigo Proveedor</th>
			                            <th style="min-width:220px">Razon social</th>
			                            <th>Su factura</th>
			                            <th style="width:20px">Fecha emision</th>
			                            <th style="width:15px">Fecha vencimiento</th>
			                            <th style="width:150px">Tipo efecto</th>
			                            <th style="width:170px">Estado</th>
			                            <th>Importe efecto</th>
			                            <th style="width:20px">Fecha emision pago</th>
			                            <th style="width:70px">Cuenta</th>
			                            <th style="min-width:120px">ComunicadoSII</th>
			                        </tr>
			                    </thead>
			                    <tbody>'.$table_body.'</tbody>
		               		</table>
		               	</body>
	               	</html>';
			
			return $pagina;
			
		} else {
			
			$table_body='';
			$table_body=$query_to_show;
			
			//echo '<pre>'.print_r($data,true).'</pre>';
			
			$i=1;
			$suma=0;
			$codigoActual = '-1';
			$inicioTabla = true;
			foreach ($data as $row)	{
				$link_1='listar-detalle-efecto-i1.php?id='.$row['numeroefecto'];
				$link_2='listar-observaciones-efecto-i1.php?id='.$row['numeroefecto'];
				$link_3='agregar-observacion-efecto-i1.php?id='.$row['numeroefecto'];
				$link_4='listar-detalle-efecto-i1-2.php?id='.$row['numefectoagrupacion_'];

                $style=' ';
                if ($row['borrado']=='-1') {
                    $style=' style="color:#cc3300;" ';
                }
				
				$lc_1=''; $lc_2=''; $lc_3=''; $lc_4='';
				
				$lc_3='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_3.'\';" type="button" class="btnFrame btn btn-xs btn-primary">0+</button>';
				
				if ($row['detalle']!='0') {				
					$lc_1='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btnFrame btn btn-xs btn-primary">D</button>';				
				}
				
				if ($row['observaciones']!='0') {
					$lc_2='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_2.'\';" type="button" class="btnFrame btn btn-xs btn-primary">O</button>';
				}
				
				//if ($row['']!='0')
				//{
				//	$lc_3='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_3.'\';" type="button" class="btn btn-xs btn-primary">0+</button>';
				//}
				
				if ($row['agrupado']!='0') {
					$lc_4='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_4.'\';" type="button" class="btnFrame btn btn-xs btn-primary">A</button>';

					$link_1 .= '&numefectoagrupacion='.$row['numefectoagrupacion_'];
					$lc_1='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btnFrame btn btn-xs btn-primary">D</button>';
				}
				
				/*
				
				<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_4.'\';" type="button" class="btn btn-xs btn-primary">A</button>
						<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btn btn-xs btn-primary">D</button>
						<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_2.'\';" type="button" class="btn btn-xs btn-primary">O</button>
						<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_3.'\';" type="button" class="btn btn-xs btn-primary">0+</button>
						
						*/
				if ($codigoActual != $row['codigoproveedor']) {
					if ($sumaImporte != 0 && !$inicioTabla) {
						$table_body.=
							'<tr style="background-color:#F8AB8F">
								<td></td>
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="4"></td>
							</tr>';
					}
					$sumaImporte = 0;
					$codigoActual = $row['codigoproveedor'];
					$razonSocial = $row['Razonsocial'];
					$inicioTabla = false;
				}

				$comunicadoSII = '';
				if ($row['ComunicadoSII'] == 0) {
					$comunicadoSII = 'No';
				} else if ($row['ComunicadoSII'] == -1) {
					$comunicadoSII = 'Sí';
				}
                
				$table_body.=
					'
				<tr '.$style.' >
					<td>'.$i.'</td>
					<td>'.$row['codigoproveedor'].'</td>
					<td>'.$row['Razonsocial'].'</td>
					<td>0'.$row['sufacturano'].'</td>
					<td>'.date_normalizer($row['fechaemision']).'</td>
					<td>'.date_normalizer($row['fechavencimiento']).'</td>
					<td>'.$row['tipoefecto'].'</td>
					<td>'.$row['estado'].'</td>										
					<td style="text-align: right;">'.aux_money_format($row['importeefecto']).'</td>
					<td>'.date_normalizer($row['FechaEmisionPago']).'</td>
					<!-- <td>'.$row['contrapartida'].'</td> -->
					<td>'.$row['cuenta'].'</td>
					<td style="text-align: center;">'.$comunicadoSII.'</td>
					<td style="min-width: 100px; text-align: center;">
						'.$lc_4.$lc_1.$lc_2.$lc_3.'
					</td>
				</tr>
				';
				$sumaImporte += $row['importeefecto'];
				$suma=$suma+$row['importeefecto'];
				
				$i++;
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			$table_body.=
							'<tr style="background-color:#F8AB8F">
								<td></td>
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="4"></td>
							</tr>';
			
			$table_body.=
				'
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>										
					<td style="text-align: right;"><b>'.aux_money_format($suma).'</b></td>
					<!-- <td></td> -->
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				';
		}
	}

	return $table_body;
}

function render_listar_detalle_efecto_i1 () {
	$eid=cG('id');
	$numefectoagrupacion=cG('numefectoagrupacion');
	
	if (!is_numeric($eid)) {
		$data=false;
	} else if ($numefectoagrupacion != '') {
		$data=get_db_data(array('listar-detalle-efecto-i1-agrupado',$numefectoagrupacion));
	} else {		
		$data=get_db_data(array('listar-detalle-efecto-i1',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$table_body='';
		
		//echo '<pre>'.print_r($data,true).'</pre>';
		
		$i=1;
		$suma=0;
		foreach ($data as $row) {
			$table_body.=
				'
				<tr>
					<td>'.$i.'</td>
					<td>'.$row['codigoproveedor'].'</td>										
					<td>0'.$row['sufacturano'].'</td>					
					<td>'.$row['codigoproyecto'].'</td>
					<td>'.$row['proyecto'].'</td>					
					<td>'.$row['borrado'].'</td>
					<td>'.$row['numeroefecto'].'</td>
					<td>'.aux_money_format($row['baseimponible']).'</td>
				</tr>
				';
			
			$suma=$suma+$row['baseimponible'];
			$i++;
		}
		
		$table_body.=
			'
				<tr>
					<td></td>					
					<td></td>					
					<td></td>					
					<td></td>
					<td></td>					
					<td></td>
					<td></td>
					<td>'.aux_money_format($suma).'</td>
				</tr>
				';
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}

function render_listar_detalle_efecto_i1_2 () {
	$eid=cG('id');
	
	//if (!is_numeric($eid))
	//{
		$data=false;
	//}
	//else
	//{		
		$data=get_db_data(array('listar-detalle-efecto-i1-2',$eid));
	//}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$table_body='';
		
		//echo '<pre>'.print_r($data,true).'</pre>';
		
		$i=1;
		$suma=0;
		foreach ($data as $row) {		
			
			$table_body .=
				'
				<tr>
					<td>'.$i.'</td>
					<td>'.$row['codigoproveedor'].'</td>										
					<td>0'.$row['sufacturano'].'</td>					
					<td>'.date_normalizer($row['fechaemision']).'</td>
					<td>'.date_normalizer($row['FechaVencimiento']).'</td>					
					<td>'.$row['NumeroEfecto'].'</td>
					<td>'.aux_money_format($row['ImporteEfecto']).'</td>
				</tr>
				';
			
			$suma=$suma+$row['ImporteEfecto'];
			$i++;
		}
		
		$table_body .=
			'
				<tr>
					<td></td>					
					<td></td>					
					<td></td>					
					<td></td>
					<td></td>					
					<td></td>					
					<td>'.aux_money_format($suma).'</td>
				</tr>
				';
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}

function render_agregar_observacion_i1() {
	$eid=cG('id');
	
	$out='';
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {		
		$data=get_db_data(array('info-efecto-i1',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		
		$query='';
		
		$content=file_get_contents_utf8(ABSPATH.'/template/agregar-observacion-i1.html');

		
		$d=$data['0'];
		$link='agregar-observacion-efecto-i1.php?id='.$d['numeroefecto'];			
		$content=str_replace('{POST_URL}',$link,$content);
		
		$result='';
		if (cP('obs_text')!='') {			
			//$query=get_db_query('agregar-observacion');
			
            // Se acuerda poner en "usuario" => 0 y en "NombreUsuario"  => el username asignado como login
			//				CodigoEmpresa	,		 Nefecto,			Fecha,			Usuario,				NombreUsuario,				Obs,			Prevision,	 CodigoClienteProveedor
			//$where=		"(      '1'         , '".$d['numeroefecto']."', GETDATE(), '".$_SESSION['userid']."', '".$_SESSION['username']."', '".cP('obs_text')."', '".'prevision'."', '".$d['codigoproveedor']."')";
            $where=		    "(      '1'         , '".$d['numeroefecto']."', GETDATE(),      '0'                 , '".$_SESSION['username']."', '".cP('obs_text')."', '".'P'."', '".$d['codigoproveedor']."')";
			
			//$query=str_replace('{OBS}',$where,$query);
			
			//$result='<pre>'.$query.'</pre>';
            
            $result=store_db_data(array('agregar-observacion',$where));
			
            if ($result>=1) {
                $content=str_replace('{RESULT}','<div style="text-align:center; padding-bottom:10px; color:green; font-weight:bold;">Observacion grabada correctamente</div>',$content);
            }
            
			
		} else {
			$tmp='<pre>'.print_r($data['0'],true).'</pre>';
			$content=str_replace('{RESULT}','',$content);
		}		
	}

	return $content;
}

function make_filter () {
	$filtro=file_get_contents_utf8(ABSPATH.'/template/filtro.html');
	
	$filtro_vinculados=filtro_vinculados();
	
	$filtro_giros=filtro_giros();
	
	$years=get_years_options();
	
	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a target="_blank" style="margin-left: 20px; margin-bottom:5px;" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';

		$print_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&print=true";
		$print_button='<a target="_blank" style="margin-left: 20px; margin-bottom:5px;" href="'.$print_url.'" class="btn btn-default btn-sm">Imprimir</a>';
	} else {		
		$excel_button='<button style="margin-left: 20px; margin-bottom:5px;" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Excel</a>';

		$print_button='<button style="margin-left: 20px; margin-bottom:5px;" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Imprimir</a>';
	}
	
	$filtro=str_replace('{EXCEL_URL}'	,	$excel_button		,	$filtro);

	$filtro=str_replace('{PRINT_URL}'	,	$print_button		,	$filtro);
	
	//echo '<pre>---'.cG('start').'---</pre>';
	
	$filtro=str_replace('{IS_DELETED}'	,	render_is_deleted()	,	$filtro);
	
	$filtro=str_replace('{DF_START}'	,	cG('start')			,	$filtro);
	
	$filtro=str_replace('{DF_END}'		,	cG('end')			,	$filtro);
    
    $filtro=str_replace('{SUFAC}'		,	cG('sufac')			,	$filtro);
    
    $filtro=str_replace('{IMPF}'		,	cG('impf')			,	$filtro);
	
	$filtro=str_replace('{VINCULADOS}'	,	$filtro_vinculados	,	$filtro);	
	
	$filtro=str_replace('YEARS'			,	$years				,	$filtro);
	
	$filtro=str_replace('{GIROS}'		,	$filtro_giros		,	$filtro);
	
	
	return $filtro;
}

function render_info_efecto_i1 () {
	$eid=cG('id');
	
	$out='';
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {		
		$data=get_db_data(array('info-efecto-i1',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$out='Efecto: '.$data[0]['numeroefecto'].' - '.$data[0]['Razonsocial'];
	}
	
	return $out;
}

function render_info_efecto_i1_2 () {
    $eid=cG('id');
	
	$out='';
	
	//if (!is_numeric($eid))
	//{
	//	$data=false;
	//}
	//else
	//{		
		$data=get_db_data(array('info-efecto-i1-2',$eid));
	//}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$out=' Efecto: '.$data[0]['NumeroEfecto'].' - Cuenta: '.$data[0]['cuenta'].' - Importe: '.aux_money_format($data[0]['ImporteEfecto']);
	}
	
	return $out;
}

function render_listar_observaciones_efecto_i1 () {
	$eid=cG('id');
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {
		$data=get_db_data(array('listar-observaciones-efecto-i1',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$table_body='';
		
		//echo '<pre>'.print_r($data,true).'</pre>';
		
		$i=1;
		foreach ($data as $row)	{
			$table_body.=
				'
				<tr>
					<td>'.$i.'</td>
					<td>'.date_normalizer($row['Fecha']).'</td>
					<td>'.$row['NombreUsuario'].'</td>					
					<td>'.str_replace('Âº', 'º', $row['Obs']).'</td>					
				</tr>
				';
			$i++;
		}
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}

function filtro_vinculados() {
	
	$sel='';
	
	if (cG('vinculados')=='0') {
		$sel=' selected="selected" ';
	}
	
	$options=
		'<option value="-1">No incluir vinculados</option>
    	<option value="0" '.$sel.'>Incluir vinculados</option>';

	return $options;
	
}



function filtro_giros() {
	
	$sel='';
	
	if (cG('giros')=='0') {
		$sel=' selected="selected" ';
	}
	
	$options=
		'
	<option value="-1">Incluir Giros</option>
    <option value="0" '.$sel.'>No Incluir Giros</option>
';

	return $options;
}

?>