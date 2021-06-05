<?php

function render_tabla_i2 () {
	
	$proveedor=cG('pr');
	$fecha_inicio=cG('start');
	$fecha_fin=cG('end');
	$estado=cG('status');
	$borrado=cG('deleted');
	
	$proyecto=cG('project');
	$contrato=cG('contract');
	$anexo=cG('annex');
	
	$sufac=cG('sufac');
	$impf=cG('impf');
	
	$giros = cG('giros');
	
	$query_to_show='';
	
	/*
	echo '<pre> $proveedor -> '.$proveedor.'</pre>';
	echo '<pre> $fecha_inicio -> '.$fecha_inicio.'</pre>';
	echo '<pre> $fecha_fin -> '.$fecha_fin.'</pre>';
	echo '<pre> $estado -> '.$estado.'</pre>';
	echo '<pre> $borrado -> '.$borrado.'</pre>';
	*/
	
	$company_id=$_SESSION['company_id'];
	
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

	if (isset($proyecto)) {
		if ($proyecto!='') {
			$where_query.="AND codigoproyecto='".$proyecto."' ";
			$default_query=false;
		}
	}

	if (isset($contrato)) {
		if ($contrato!='') {
			$where_query.="AND codigocontrato='".$contrato."' ";
			$default_query=false;
		}
	}

	if (isset($anexo)) {
		if ($anexo!='')	{
			$where_query.="AND codigoanexo='".$anexo."' ";
			$default_query=false;
		}
	}

	if (isset($sufac)) {
		if ($sufac!='')	{
			$where_query.="AND sufacturano LIKE '%".$sufac."%' ";
			$default_query=false;
		}
	}

	if (isset($impf)) {
		if ($impf!='') {
			$impf=str_replace(',','.',$impf);
			$where_query.="AND baseimponible LIKE '".$impf."%' ";
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
		$query_to_show='<tr><td colspan="18"><pre>'.print_r($where_query,true).'</pre></td></tr>';
		$query_to_show='';
		$data=get_db_data(array('listado-completo-filtrado-i2', $where_query));
	}
	
	if ($data===false) {
		$table_body=$query_to_show;
		$table_body.='<tr><td colspan="18" style="text-align:center; padding:40px;">No hay registros</td></tr>';
	} else {
		
		if (cG('excel')=='true') {
			$separator=';';
			
			$csv='sep=;'."\n";
			
			$csv.='Codigo Proveedor'.$separator.'Razon social'.$separator.'Su factura'.$separator.'Fecha emision'.$separator.'Fecha vencimiento'.$separator.'Tipo efecto'.$separator.'Estado'.$separator.'Base imponible'.$separator.'Fecha emision pago'.$separator.'Contrapartida'.$separator.'Cuenta'.$separator.'Codigo proyecto'.$separator.'Proyecto'.$separator.'Codigo contrato'.$separator.'Contrato'.$separator.'Codigo anexo'.$separator.'Anexo'."\n";
			
			foreach ($data as $row) {
				$baseimponible=number_format($row['baseimponible'], 2, ',', '');
				
				$csv.=
					'"'		.trim(	$row['codigoproveedor']						)		.'"'.$separator.''.
					'"'		.trim(	$row['razonsocial']							)		.'"'.$separator.''.
					'"0'		.trim(	$row['sufacturano']							)		.'"'.$separator.''.
					'"'		.trim(	date_normalizer($row['fechaemision'])		)		.'"'.$separator.''.
					'"'		.trim(	date_normalizer($row['fechavencimiento'])	)		.'"'.$separator.''.
					'"'		.trim(	$row['tipoefecto']							)		.'"'.$separator.''.
					'"'		.trim(	$row['estado']								)		.'"'.$separator.''.
					''		.trim(	$baseimponible								)		.''.$separator.''.
					''		.trim(	date_normalizer($row['FechaEmisionPago'])	)		.''.$separator.''.
					'"'		.trim(	$row['contrapartida']						)		.'"'.$separator.''.
					'"'		.trim(	$row['cuenta']								)		.'"'.$separator.''.
					
					'"'		.trim(	$row['codigoproyecto']						)		.'"'.$separator.''.
					'"'		.trim(	$row['proyecto']							)		.'"'.$separator.''.
					'"'		.trim(	$row['codigocontrato']						)		.'"'.$separator.''.
					'"'		.trim(	$row['contrato']							)		.'"'.$separator.''.
					'"'		.trim(	$row['codigoanexo']							)		.'"'.$separator.''.
					'"'		.trim(	$row['anexo']								)		.'"'.$separator.''.
					
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
				
				$style=' ';
				if($row['borrado']=='-1') {
					$style=' style="color:#cc3300;" ';
				}

				if ($codigoActual != $row['codigoproveedor']) {
					if ($sumaImporte != 0 && !$inicioTabla) {
						$table_body.=
							'<tr style="background-color:#F8AB8F">
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.utf8_encode($razonSocial).'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="9"></td>
							</tr>';
					}
					$sumaImporte = 0;
					$codigoActual = $row['codigoproveedor'];
					$razonSocial = $row['razonsocial'];
					$inicioTabla = false;
				}
				
				$table_body.=
					'
				<tr '.$style.'>
					<td>'.$row['codigoproveedor'].'</td>
					<td>'.$row['razonsocial'].'</td>
					<td>0'.$row['sufacturano'].'</td>
					<td>'.date_normalizer($row['fechaemision']).'</td>
					<td>'.date_normalizer($row['fechavencimiento']).'</td>
					<td>'.utf8_encode($row['tipoefecto']).'</td>
					<td>'.$row['estado'].'</td>										
					<td style="text-align:right">'.aux_money_format($row['baseimponible']).'</td>
					<td>'.date_normalizer($row['FechaEmisionPago']).'</td>
					<td>'.$row['contrapartida'].'</td>
					<td>'.utf8_encode($row['cuenta']).'</td>
					<td>'.$row['codigoproyecto'].'</td>
					<td>'.utf8_encode($row['proyecto']).'</td>
					<td>'.$row['codigocontrato'].'</td>
					<td>'.$row['contrato'].'</td>
					<td>'.$row['codigoanexo'].'</td>
					<td>'.$row['anexo'].'</td>						
				</tr>
				';				
				$sumaImporte += $row['baseimponible'];
				$suma=$suma+$row['baseimponible'];
				
				$i++;
			}

			$table_body.=
						'<tr style="background-color:#F8AB8F">
							<td><b>'.$codigoActual.'</b></td>
							<td><b>'.utf8_encode($razonSocial).'</b></td>
							<td colspan="5"></td>	
							<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
							<td colspan="9"></td>
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
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
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
			                            <th>Fecha emision</th>
			                            <th>Fecha vencimiento</th>
			                            <th>Tipo efecto</th>
			                            <th>Estado</th>
			                            <th>Base imponible</th>
			                            <th>Fecha emision pago</th>
			                            <th>Contrapartida</th>
			                            <th style="min-width:250px">Cuenta</th>
			                            <th>Codigo Proyecto</th>
			                            <th style="width:250px">Proyecto</th>
			                            <th>Codigo Contrato</th>
			                            <th>Contrato</th>
			                            <th>Codigo Anexo</th>
			                            <th>Anexo</th>
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
				$link_1='listar-detalle-retencion-i2.php?id='.$row['numeroefecto'];
				//$link_2='listar-observaciones-efecto-i1.php?id='.$row['numeroefecto'];
				//$link_3='agregar-observacion-efecto-i1.php?id='.$row['numeroefecto'];
				
				$style=' ';
				if($row['borrado']=='-1') {
					$style=' style="color:#cc3300;" ';
				}

				if ($codigoActual != $row['codigoproveedor']) {
					if ($sumaImporte != 0 && !$inicioTabla) {
						$table_body.=
							'<tr style="background-color:#F8AB8F">
								<td></td>
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="9"></td>
							</tr>';
					}
					$sumaImporte = 0;
					$codigoActual = $row['codigoproveedor'];
					$razonSocial = $row['razonsocial'];
					$inicioTabla = false;
				}
				
				$table_body.=
					'
				<tr '.$style.'>
					<td>'.$i.'</td>
					<td>'.$row['codigoproveedor'].'</td>
					<td>'.$row['razonsocial'].'</td>
					<td>0'.$row['sufacturano'].'</td>
					<td>'.date_normalizer($row['fechaemision']).'</td>
					<td>'.date_normalizer($row['fechavencimiento']).'</td>
					<td>'.$row['tipoefecto'].'</td>
					<td>'.$row['estado'].'</td>										
					<td style="text-align:right">'.aux_money_format($row['baseimponible']).'</td>
					<td>'.date_normalizer($row['FechaEmisionPago']).'</td>
					<td>'.$row['contrapartida'].'</td>
					<td>'.$row['cuenta'].'</td>
					<td>'.$row['codigoproyecto'].'</td>
					<td>'.$row['proyecto'].'</td>
					<td>'.$row['codigocontrato'].'</td>
					<td>'.$row['contrato'].'</td>
					<td>'.$row['codigoanexo'].'</td>
					<td>'.$row['anexo'].'</td>						
				</tr>
				';				
				$sumaImporte += $row['baseimponible'];
				$suma=$suma+$row['baseimponible'];
				
				$i++;
			}

			$table_body.=
							'<tr style="background-color:#F8AB8F">
								<td></td>
								<td><b>'.$codigoActual.'</b></td>
								<td><b>'.$razonSocial.'</b></td>
								<td colspan="5"></td>	
								<td style="text-align:right"><b>'.aux_money_format($sumaImporte).'</b></td>
								<td colspan="9"></td>
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
					<td></td>
					<td></td>
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


function make_filter_2 () {
	$filtro=file_get_contents_utf8(ABSPATH.'/template/filtro-2.html');
	
	$filtro_giros=filtro_giros();
	
	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		echo $excel_url;
		$excel_button='<a target="_blank" style="margin-left: 20px; margin-bottom:5px;" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';

		$print_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&print=true";
		$print_button='<a target="_blank" style="margin-left: 20px; margin-bottom:5px;" href="'.$print_url.'" class="btn btn-default btn-sm">Imprimir</a>';
	} else {		
		$excel_button='<button style="margin-left: 20px; margin-bottom:5px;" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Excel</a>';

		$print_button='<button style="margin-left: 20px; margin-bottom:5px;" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Imprimir</a>';
	}
	
	$filtro=str_replace('{EXCEL_URL}'	,	$excel_button	,	$filtro);

	$filtro=str_replace('{PRINT_URL}'	,	$print_button		,	$filtro);
	
	//echo '<pre>---'.cG('start').'---</pre>';
	
	$filtro=str_replace('{IS_DELETED}'	,	render_is_deleted()	,	$filtro);
	
	$filtro=str_replace('{DF_START}'	,	cG('start')			,	$filtro);
	
	$filtro=str_replace('{DF_END}'		,	cG('end')			,	$filtro);
	
	$filtro=str_replace('{SUFAC}'		,	cG('sufac')			,	$filtro);
	
	$filtro=str_replace('{IMPF}'		,	cG('impf')			,	$filtro);
	
	$filtro=str_replace('{GIROS}'		,	$filtro_giros		,	$filtro);
	
	
	return $filtro;
}

?>