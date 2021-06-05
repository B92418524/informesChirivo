<?php

function render_tabla_i7 () {
	
	$cliente		=	cG('cl');
	$fecha_inicio	=	cG('start');
	$fecha_fin		=	cG('end');
	$vfecha_inicio	=	cG('vstart');
	$vfecha_fin		=	cG('vend');
	$estado			=	cG('status');
	$borrado		=	cG('deleted');
	$sufac			=	cG('sufac');
    $serie			=	cG('serie');
	$impf			=	cG('impf');
	$vinculados		=	cG('vinculados');
	$giros			=	cG('giros');
	$estado_efecto	=	cG('estado_efecto');
	
	$query_to_show='';

	$company_id=$_SESSION['company_id'];
	
	$default_query=true;
	$where_query="WHERE CodigoEmpresa="."'".$company_id."'";
	
	if (isset($cliente)) {
		if ($cliente!='') {
			$where_query.="AND CifDni='".$cliente."' ";
			$default_query=false;
		}
	}
	if (isset($fecha_inicio)) {
		if ($fecha_inicio!='') {
			$where_query.="AND FechaEmision>='".sql_date($fecha_inicio,'es')."' ";
			$default_query=false;
		}
	}
	if (isset($fecha_fin)) {
		if ($fecha_fin!='') {
			$where_query.="AND FechaEmision<='".sql_date($fecha_fin,'es')."' ";
			$default_query=false;
		}
	}
	if (isset($vfecha_inicio)) {
		if ($vfecha_inicio!='')	{
			$where_query.="AND FechaVencimiento>='".sql_date($vfecha_inicio,'es')."' ";
			$default_query=false;
		}
	}
	if (isset($vfecha_fin))	{
		if ($vfecha_fin!='') {
			$where_query.="AND FechaVencimiento<='".sql_date($vfecha_fin,'es')."' ";
			$default_query=false;
		}
	}
	if (isset($estado))	{
		if ($estado!='') {
			$where_query.="AND estado LIKE '".$estado."' ";
			$default_query=false;
		}
	}
	if (isset($estado_efecto)) {
		if ($estado_efecto!='')	{
			$where_query.="AND EstadoEfecto LIKE '".$estado_efecto."' ";
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
		if ($sufac!='')	{
			// $where_query.="AND sufacturano LIKE '%".$sufac."%' ";
			$where_query.="AND FRA LIKE '%".$sufac."%' ";
			$default_query=false;
		}
	}
	if (isset($serie)) {
        if ($serie!='') {
            $where_query.="AND SerieFactura = '".$serie."' ";
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
	
	if($default_query) {
		$data=false;
		//$data=get_db_data(array('listado-completo'));
	} else {
		//$query_to_show='<tr><td colspan="12"><pre style="font-size:10px;">'.print_r($where_query,true).'</pre></td></tr>';
		$query_to_show='';
		$data=get_db_data(array('listado-completo-filtrado-clientes', $where_query));
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
	
	
	if ($data === false) {
		$table_body=$query_to_show;
		$table_body.='<tr><td colspan="13" style="text-align:center; padding:40px;">No hay registros</td></tr>';
	} else {
		if (cG('excel')=='true') {
			$separator=';';
			
			$csv='sep=;'."\n";
			
			$csv.='Codigo Proveedor'.$separator.'Razon social'.$separator.'Su factura'.$separator.'Fecha emision'.$separator.'Fecha vencimiento'.$separator.'Tipo efecto'.$separator.'Estado'.$separator.'Importe efecto'.$separator.'Fecha emision pago'.$separator.'Estado efecto'.$separator.'Cuenta'.$separator.'Codigo Proyecto'.$separator.'Proyecto'."\n";
			
			foreach ($data as $row) {
				$detalleProyecto = get_db_data(array('listar-detalle-efecto-i7',$row['NumeroEfecto']));
				$codProy = '';
				$proy = '';

				if (is_array($detalleProyecto)) {
					$codProy = $detalleProyecto[0]['codigoproyecto'];
					$proy = $detalleProyecto[0]['proyecto'];
				}

				$importe=number_format($row['ImporteEfecto'], 2, ',', '');
				$csv.=
					'"'		.trim(	$row['CodigoClienteProveedor']				)		.'"'.$separator.''.
					'"'		.trim(	$row['RazonSocial']							)		.'"'.$separator.''.
					'"'		.trim(	$row['FRA']									)		.'"'.$separator.''.
					'"'		.trim(	date_normalizer($row['FechaEmision'])		)		.'"'.$separator.''.
					'"'		.trim(	date_normalizer($row['FechaVencimiento'])	)		.'"'.$separator.''.
					'"'		.trim(	$row['TipoEfecto']							)		.'"'.$separator.''.
					'"'		.trim(	$row['Estado']								)		.'"'.$separator.''.
					''		.trim(	$importe									)		.''.$separator.''.
					'"'		.trim(	date_normalizer($row['FechaEmision'])		)		.'"'.$separator.''.
					'"'		.trim(	$row['EstadoEfecto']						)		.'"'.$separator.''.
					'"'		.trim(	$row['CodigoCuenta']						)		.'"'.$separator.''.
					'"'		.trim(	$codProy									)		.'"'.$separator.''.
					'"'		.trim(	$proy										)		.'"'.$separator.''.
					
					"\n";					
				
			}
			
			return $csv;
			
		} else if (cG('print')=='true')	{
			$table_body='';
			$table_body=$query_to_show;
			
			$suma=0;
			$codigoActual = '-1';
			$inicioTabla = true;
			foreach ($data as $row) {
				if (trim($codigoActual) != trim($row['CodigoClienteProveedor'])) {
					if (!$inicioTabla) {
						$table_body.=
							'<tr style="background-color:#F8AB8F">
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="3"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte, 'si').'</b></td>
								<td colspan="3"></td>
							</tr>';
					}
					$sumaImporte = 0;
					$codigoActual = $row['CodigoClienteProveedor'];
					$razonSocial = $row['RazonSocial'];
					$inicioTabla = false;
				}

				$detalleProyecto = get_db_data(array('listar-detalle-efecto-i7',$row['NumeroEfecto']));
				$codProy = '';
				$proy = '';

				if (is_array($detalleProyecto)) {
					$codProy = $detalleProyecto[0]['codigoproyecto'];
					$proy = $detalleProyecto[0]['proyecto'];
					if (count($detalleProyecto) > 1) {
						$proy .= ' - Y MÁS';
					}
				}
				
				$table_body.=
					'
				<tr '.$style.' >
					<td>'.$row['CodigoClienteProveedor'].'</td>
					<td>'.$row['RazonSocial'].'</td>
					<td>'.$row['FRA'].'</td>
					<td>'.date_normalizer($row['FechaEmision']).'</td>
					<td>'.date_normalizer($row['FechaVencimiento']).'</td>							
					<td style="text-align: right;">'.aux_money_format($row['ImporteEfecto'], 'si').'</td>
					<td>'.$row['EstadoEfecto'].'</td>
					<td>'.$codProy.'</td>
					<td>'.$proy.'</td>
				</tr>
				';
				$sumaImporte += $row['ImporteEfecto'];
				$suma=$suma+$row['ImporteEfecto'];
				
				$i++;
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			$table_body .=
							'<tr style="background-color:#F8AB8F">
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="3"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte, 'si').'</b></td>
								<td colspan="3"></td>
							</tr>';
			
			$table_body .=
				'
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>										
					<td style="text-align: right;"><b>'.aux_money_format($suma, 'si').'</b></td>
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
							<label class="titulo">INFORME DE EFECTOS PENDIENTES DE CLIENTES</label><br/>
							<label>FECHA DESDE: '.$fecha_inicio.'</label><br/>
							<label>FECHA HASTA: '.$fecha_fin.'</label>
							<table style="background-color: #fff">
			                    <thead>
			                        <tr>
			                        	<th style="width:20px">Codigo Cliente</th>
			                            <th style="min-width:180px">Razon social</th>
			                            <th>Factura</th>
			                            <th style="width:20px">Fecha emision</th>
			                            <th style="width:15px">Fecha vencimiento</th>
			                            <th style="width:15px">Importe efecto</th>
			                            <th style="width:15px">Estado Efecto</th>
			                            <th style="width:15px">Codigo proyecto</th>
			                            <th style="width:260px">Proyecto</th>
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
			
			$i = 1;
			$suma = 0;
			$codigoActual = '-1';
			$inicioTabla = true;
			/* se dan casos en los que los importes son iguales 5€ -5€ y sale 0, pero se controlaba que fuese distinto de 0, entonces esos no salen!! */
			foreach ($data as $row) {
				$link_1='listar-detalle-efecto-i7.php?id='.$row['NumeroEfecto'];
				$link_2='listar-observaciones-efecto-i7.php?id='.$row['NumeroEfecto'];
				$link_3='agregar-observacion-efecto-i7.php?id='.$row['NumeroEfecto'];
				$link_4='listar-detalle-efecto-i7-2.php?id='.$row['NumEfectoAgrupacion_'];
				
				$style=' ';
				if ($row['Borrado']=='-1') {
					$style=' style="color:#cc3300;" ';
				}
				
				$lc_1=''; $lc_2=''; $lc_3=''; $lc_4='';
				
				$lc_3='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_3.'\';" type="button" class="btnFrame btn btn-xs btn-primary">0+</button>';
				
				if ($row['detalle']!='0') {				
					$lc_1='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btnFrame btn btn-xs btn-primary">D</button>';		
				}
				
				if ($row['observaciones']!='0')	{
					$lc_2='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_2.'\';" type="button" class="btnFrame btn btn-xs btn-primary">O</button>';
				}
				
				//if ($row['']!='0')
				//{
				//	$lc_3='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_3.'\';" type="button" class="btn btn-xs btn-primary">0+</button>';
				//}
				
				if ($row['agrupado']!='0') {
					$lc_4='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_4.'\';" type="button" class="btnFrame btn btn-xs btn-primary">A</button>';

					$link_1 .= '&numefectoagrupacion='.$row['NumEfectoAgrupacion_'];
					$lc_1='<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btnFrame btn btn-xs btn-primary">D</button>';
				}
				
				/*
								
				<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_4.'\';" type="button" class="btn btn-xs btn-primary">A</button>
						<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btn btn-xs btn-primary">D</button>
						<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_2.'\';" type="button" class="btn btn-xs btn-primary">O</button>
						<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_3.'\';" type="button" class="btn btn-xs btn-primary">0+</button>
						
						*/

				if (trim($codigoActual) != trim($row['CodigoClienteProveedor'])) {
					if (!$inicioTabla) {
						$table_body.=
							'<tr class="trSuma-'.$codigoActual.'" style="background-color:#F8AB8F">
								<td></td>
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td></td>	
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte, 'si').'</b></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>';
					}
					$sumaImporte = 0;
					$codigoActual = trim($row['CodigoClienteProveedor']);
					$razonSocial = $row['RazonSocial'];
					$inicioTabla = false;
				}

				$detalleProyecto = get_db_data(array('listar-detalle-efecto-i7',$row['NumeroEfecto']));
				$codProy = '';
				$proy = '';

				if (is_array($detalleProyecto)) {
					$codProy = $detalleProyecto[0]['codigoproyecto'];
					$proy = $detalleProyecto[0]['proyecto'];
					if (count($detalleProyecto) > 1) {
						$proy .= '<button onClick="frameStyle(); document.getElementById(\'d1\').src=\''.$link_1.'\';" type="button" class="btnFrame btn btn-xs btn-primary" style="float:right">+</button>';
					}
				}
				
				$table_body .=
					'
				<tr class="trOrden" '.$style.' name="'.trim($row['CodigoClienteProveedor']).'">
					<td>'.$i.'</td>
					<td>'.trim($row['CodigoClienteProveedor']).'</td>
					<td>'.$row['RazonSocial'].'</td>
					<td>'.$row['FRA'].'</td>
					<td>'.date_normalizer($row['FechaEmision']).'</td>
					<td>'.date_normalizer($row['FechaVencimiento']).'</td>
					<td>'.$row['TipoEfecto'].'</td>
					<td>'.$row['Estado'].'</td>										
					<td style="text-align: right;">'.aux_money_format($row['ImporteEfecto'], 'si').'</td>
					<td>'.date_normalizer($row['FechaEmision']).'</td>
					<td>'.$row['EstadoEfecto'].'</td>
					<td>'.$row['CodigoCuenta'].'</td>
					<td style="min-width: 100px; text-align: center;">
						'.$lc_4.$lc_1.$lc_2.$lc_3.'
					</td>
					<td>'.$codProy.'</td>
					<td>'.$proy.'</td>
				</tr>
				';
				$sumaImporte += $row['ImporteEfecto'];
				$suma=$suma+$row['ImporteEfecto'];
				
				$i++;
			}
			//siempre el ultimo no tendra linea porque no pasa a la siguiente row
			$table_body.=
							'<tr class="trSuma-'.$codigoActual.'" style="background-color:#F8AB8F">
								<td></td>
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte, 'si').'</b></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
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
					<td style="text-align: right;"><b>'.aux_money_format($suma, 'si').'</b></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				';
			
		}
		
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}

function render_listar_detalle_efecto_i7 () {
	$eid=cG('id');
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {		
		$data=get_db_data(array('listar-detalle-efecto-i7',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$table_body='';

		$i=1;
		$suma=0;
		foreach ($data as $row)	{
			$link_1='listar-detalle-efecto-i7.php?id='.$row['numeroefecto'];
			$link_2='listar-observaciones-efecto-i7.php?id='.$row['numeroefecto'];
			$link_3='agregar-observacion-efecto-i7.php?id='.$row['numeroefecto'];
			
			$table_body.=
				'
				<tr>
					<td>'.$i.'</td>
					<td>'.$row['codigocliente'].'</td>										
					<td>'.$row['Factura'].'</td>					
					<td>'.$row['codigoproyecto'].'</td>
					<td>'.$row['proyecto'].'</td>					
					<td>'.$row['borrado'].'</td>
					<td>'.$row['numeroefecto'].'</td>
					<td>'.aux_money_format($row['ImporteEfecto'], 'si').'</td>
				</tr>
				';
			
			$suma = $suma + $row['ImporteEfecto'];
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
					<td>' . aux_money_format($suma, 'si') . '</td>
				</tr>
				';
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}

function render_listar_detalle_efecto_i7_2 () {
	$eid=cG('id');
	
	//if (!is_numeric($eid))
	//{
	$data=false;
	//}
	//else
	//{		
	$data=get_db_data(array('listar-detalle-efecto-i7-2',$eid));
	//}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$table_body='';
		
		//echo '<pre>'.print_r($data,true).'</pre>';
		
		$i=1;
		$suma=0;
		foreach ($data as $row)	{		
			
			$table_body.=
				'
				<tr>
					<td>'.$i.'</td>
					<td>'.$row['codigoproveedor'].'</td>										
					<td>'.$row['Factura'].'</td>					
					<td>'.date_normalizer($row['fechaemision']).'</td>
					<td>'.date_normalizer($row['FechaVencimiento']).'</td>					
					<td>'.$row['NumeroEfecto'].'</td>
					<td>'.aux_money_format($row['ImporteEfecto'], 'si').'</td>
				</tr>
				';
			
			$suma = $suma + $row['ImporteEfecto'];
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
					<td>'.aux_money_format($suma, 'si').'</td>
				</tr>
				';
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}

function render_agregar_observacion_i7 () {
	$eid=cG('id');
	
	$out='';
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {		
		$data=get_db_data(array('info-efecto-i7',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		
		$query='';
		
		$content=file_get_contents_utf8(ABSPATH.'/template/agregar-observacion-i7.html');
		
		$d=$data['0'];
		$link='agregar-observacion-efecto-i7.php?id='.$d['NumeroEfecto'];			
		$content=str_replace('{POST_URL}',$link,$content);
		
		$result='';
		if (cP('obs_text')!='') {			
			//$query=get_db_query('agregar-observacion');
			
			// Se acuerda poner en "usuario" => 0 y en "NombreUsuario"  => el username asignado como login
			//				CodigoEmpresa	,		 Nefecto,			Fecha,			Usuario,				NombreUsuario,				Obs,			Prevision,	 CodigoClienteProveedor
			//$where=		"(      '1'         , '".$d['numeroefecto']."', GETDATE(), '".$_SESSION['userid']."', '".$_SESSION['username']."', '".cP('obs_text')."', '".'prevision'."', '".$d['codigoproveedor']."')";
			$where=		    "(      '1'         , '".$d['NumeroEfecto']."', GETDATE(),      '0'                 , '".$_SESSION['username']."', '".cP('obs_text')."', '".'C'."', '".$d['CodigoClienteProveedor']."')";
			
			//$query=str_replace('{OBS}',$where,$query);
			
			//$result='<pre>'.$query.'</pre>';
			
			$result=store_db_data(array('agregar-observacion',$where));
			
			if($result>=1) {
				$content=str_replace('{RESULT}','<div style="text-align:center; padding-bottom:10px; color:green; font-weight:bold;">Observacion grabada correctamente</div>',$content);
			}
			
			
		} else {
			$tmp='<pre>'.print_r($data['0'],true).'</pre>';
			$content=str_replace('{RESULT}','',$content);
		}		
	}

	return $content;
}

function make_filter_7 () {
	$filtro=file_get_contents_utf8(ABSPATH.'/template/filtro-7.html');
	
	$filtro_vinculados=filtro_vinculados();
	
	//$filtro_giros=filtro_giros();
	
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
	
	$filtro=str_replace('{EXCEL_URL}'	,	$excel_button	,	$filtro);

	$filtro=str_replace('{PRINT_URL}'	,	$print_button	,	$filtro);
	
	//echo '<pre>---'.cG('start').'---</pre>';
	
	$filtro=str_replace('{IS_DELETED}'	,	render_is_deleted()	,	$filtro);
	
	$filtro=str_replace('{DF_START}'	,	cG('start')			,	$filtro);
	
	$filtro=str_replace('{DF_END}'		,	cG('end')			,	$filtro);
	
	$filtro=str_replace('{DF_VSTART}'	,	cG('vstart')		,	$filtro);
	
	$filtro=str_replace('{DF_VEND}'		,	cG('vend')			,	$filtro);
	
	$filtro=str_replace('{SUFAC}'		,	cG('sufac')			,	$filtro);

    $filtro=str_replace('{SERIE}'		,	cG('serie')			,	$filtro);
	
	$filtro=str_replace('{IMPF}'		,	cG('impf')			,	$filtro);
	
	$filtro=str_replace('{VINCULADOS}'	,	$filtro_vinculados	,	$filtro);	
	
	$filtro=str_replace('YEARS'			,	$years				,	$filtro);
	
	//$filtro=str_replace('{GIROS}'		,	$filtro_giros		,	$filtro);
	
	
	return $filtro;
}

function render_info_efecto_i7 () {
	$eid=cG('id');
	
	$out='';
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {		
		$data=get_db_data(array('info-efecto-i7',$eid));
	}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$out='Efecto: '.$data[0]['NumeroEfecto'].' - '.$data[0]['RazonSocial'];
	}
	
	return $out;
}

function render_info_efecto_i7_2 () {
	$eid=cG('id');
	
	$out='';
	
	//if (!is_numeric($eid))
	//{
	//	$data=false;
	//}
	//else
	//{		
	$data=get_db_data(array('info-efecto-i7-2',$eid));
	//}
	
	if ($data===false) {
		return 'No hay registros';		
	} else {
		$out=' Efecto: '.$data[0]['NumeroEfecto'].' - Cuenta: '.$data[0]['cuenta'].' - Importe: '.aux_money_format($data[0]['ImporteEfecto'], 'si');
	}
	
	return $out;
}

function render_listar_observaciones_efecto_i7 () {
	$eid=cG('id');
	
	if (!is_numeric($eid)) {
		$data=false;
	} else {
		$data=get_db_data(array('listar-observaciones-efecto-i7',$eid));
	}
	
	if ($data === false) {
		return 'No hay registros';		
	} else {
		$table_body='';
		
		//echo '<pre>'.print_r($data,true).'</pre>';
		
		$i = 1;
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
			
					// <td>'.utf8_decode(str_replace('Âº', 'º', utf8_encode($row['Obs']))).'</td>	
			$i++;
		}
	}
	
	//$template=file_get_contents_utf8(ABSPATH.'/template/pedidos_pendientes.html');
	
	//$out=str_replace(array('{TABLE_CONTENT}'),array($table_body),$template);
	
	//return $out;
	
	return $table_body;
}
?>