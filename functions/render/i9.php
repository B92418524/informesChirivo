<?php

function render_tabla_i9 () {
	$trs = '';
	$custom_query = '';
	
	$form_date = cG('start');
		
	if ($form_date == '') {
		$form_date = date('d-m-Y');
	} else {
		/* ponerle el dia para que coja bien la fecha */
		$form_date = explode('-', $form_date);
		$y = $form_date[count($form_date)-1];
		$m = $form_date[count($form_date)-2];
		$form_date = '01-'.$m.'-'.$y;
	}

	$custom_query=" AND Activa='-1' ";
	
	if (cG('estado')=='1') {
		$custom_query=" AND Activa='0' ";
	} elseif (cG('estado')=='2') {
		$custom_query=" AND Activa='-1' ";
	} elseif (cG('estado')=='0') {
		$custom_query=" ";
	}
	
	if (cG('prv')!='') {
		$custom_query=" AND CodigoProyecto='".cG('prv')."' ";
	}

	if (!empty($_GET)) {
		if (isset($GLOBALS['ADMIN_I9'])) {
			$main_loop_arr=get_db_data(array('listar-proyectos-facturacion',$custom_query));
		} else {
			$main_loop_arr=get_db_data(array('listar-proyectos-facturacion-jefe-obra',$custom_query));
		}

	$date_arr=six_months_backwards_array($form_date);
	
	//echo '<pre>'.print_r($date_arr,true).'</pre>';

	if (cG('excel')=='true') {
		$separator=';';
			
		$csv='sep=;'."\n";

		$months_names=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');

		$mes = date("m", strtotime($form_date));
		$periodo = date("Y", strtotime($form_date));
							
		for ($i = 0; $i < 6; $i++) {
			$mes = sprintf("%02d", $mes);
			$md[$i] = $months_names[$mes].'_'.$periodo;
			if ($mes - 1 == 0) {
				$mes = 12;
				$periodo = $periodo - 1; //un año atras
			} else {
				$mes--;
			}
		}

		$mes5 = $md[5];
		$mes4 = $md[4];
		$mes3 = $md[3];
		$mes2 = $md[2];
		$mes1 = $md[1];
		$mes0 = $md[0];

		$csv.='Proyecto'.$separator.'Descripcion'.$separator.'Contrato/Anexo'.$separator.'Importe Facturado a origen'.$separator.'Importe Pendiente'.$separator.'Importe Cartera Pendiente'.$separator.'Total proyecto facturado mes'.$separator.'Total importe de todos los contratos'.$separator.'Total proyecto facturado a origen'.$separator.'Total proyecto cartera pendiente'.$separator.$mes5.$separator.$mes4.$separator.$mes3.$separator.$mes2.$separator.$mes1.$separator.$mes0.$separator.'Cerrado'.$separator.'Estado'.$separator.'Fecha'.$separator.'Observaciones'."\n";

		$n=1;
		$last='';
		foreach ($main_loop_arr as $ml)	{
			if ($ml['CodigoProyecto']!='') {
				if (isset($GLOBALS['ADMIN_I9'])) {
					$second_loop_arr=get_db_data(array('listar-desglose-proyectos-facturacion',$ml['CodigoProyecto'],$custom_query));
				} else {
					$second_loop_arr=get_db_data(array('listar-desglose-proyectos-facturacion-jefe-obra',$ml['CodigoProyecto'],$custom_query));
				}
				
				if (is_array($second_loop_arr))	{
					
					foreach ($second_loop_arr as $sl) {						
						//echo '<pre>'.print_r($sl,true).'</pre>';
						$i=0;
						$start=0;
						$codigoProyecto = $ml['CodigoProyecto'];
						$descripcion = '';
						$proyecto = '';
						$descripcionProyecto = '';
						$importeContrato = '';
						$estadoCierre = '';
						$observaciones = '';
						$pendiente = '';
						$importeCarteraPendiente = '';
						$totalProyectoFacturadoMes = '';
						$totalImporteTodosContratos = '';
						$totalProyectoFacturadoOrigen = '';
						$totalProyectoCarteraPendiente = '';
						$mes = date("m", strtotime($form_date));
						$periodo = date("Y", strtotime($form_date));
						//foreach ($date_arr as $d) {
						for($j = 0; $j < 6; $j++) {
							//echo '<pre>'.print_r($d,true).'</pre>';
							//$mes=$d['M']; 
							//$periodo=$d['Y'];
							
							if (isset($GLOBALS['ADMIN_I9'])) {
								$data=get_db_data(array('listar-proyectos-facturacion-filtrado',$sl['Proyecto'],$mes,$periodo,$custom_query));
							} else {
								$data=get_db_data(array('listar-proyectos-facturacion-jefe-obra-filtrado',$sl['Proyecto'],$mes,$periodo,$custom_query));
							}

							if(is_array($data)) {
								//echo '<pre>'.print_r($data, true).'</pre>';
								/* guardo los datos por si en algun caso no encuentra valores en el mes indicado ($data no es array) */
								if ($descripcion == '') {
									$descripcion = $data['0']['Descripcion'];
								}
								if ($proyecto == '') {
									$proyecto = $data['0']['Proyecto'];
								}
								if ($descripcionProyecto == '') {
									$descripcionProyecto = $data['0']['DescripcionProyecto'];
								}
								if ($importeContrato == '') {
									$importeContrato = $data['0']['ImporteContrato'];
								}
								if ($estadoCierre == '') {
									if ($data['0']['Activa'] == '0') {
										$estadoCierre = 'X';
									}
								}
								if ($observaciones == '') {
									$observaciones = $data['0']['ObsProyecto'];
								}
								if ($importeCarteraPendiente == '') {
									$importeCarteraPendiente = $data['0']['ImporteCarteraPendiente'];
								}
								if ($totalProyectoFacturadoMes == '') {
									$totalProyectoFacturadoMes = $data['0']['TotalProyectoFacturadoMes'];
								}
								if ($totalImporteTodosContratos == '') {
									$totalImporteTodosContratos = $data['0']['TotalImporteContratos'];
								}
								if ($totalProyectoFacturadoOrigen == '') {
									$totalProyectoFacturadoOrigen = $data['0']['TotalProyectoFacturadoOrigen'];
								}
								if ($totalProyectoCarteraPendiente == '') {
									$totalProyectoCarteraPendiente = $data['0']['TotalProyectoCarteraPendiente'];
								}
							}

							//echo '<pre>'.print_r($data,true).'</pre>';
							
							if ($start==0) {
								if ($data['0']['ImporteOrigen'] != '') {
									$importe_origen=$data['0']['ImporteOrigen'];
								}
								
								$flag_view=true;
								
								if (cG('estado_facturacion')!='-1') {
									if (cG('estado_facturacion')!=$data['0']['EstadoFacturacion']) {
										$flag_view=false;
									}
								}

								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_0='background-color:#f9b374;';
									$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_0='background-color:#90f974;';
									$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_0='background-color:#90f974';
									$estado_facturacion='Facturado';
								} else {
									$bg_color_0='background-color:#e86f6f;';
									$estado_facturacion='Pendiente';	
								}
							}
							if ($start==1) {										
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_1='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2')	{
									$bg_color_1='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_1='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_1='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==2) {										
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_2='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_2='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3')	{
									$bg_color_2='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_2='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==3) {								
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_3='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2')	{
									$bg_color_3='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_3='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_3='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==4) {												
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_4='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_4='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3')	{
									$bg_color_4='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_4='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==5) {
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_5='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2')	{
									$bg_color_5='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3')	{
									$bg_color_5='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_5='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}	
							
							if($data['0']['ImporteProyecto'] == '') {
								$dm[$i] = '.0000000000';
							} else {
								$dm[$i]=$data['0']['ImporteProyecto'];
							}
							$i++;
							$start++;			
							//echo '<pre>'.print_r($data,true).'</pre>';
							if ($mes - 1 == 0) {
								$mes = 12;
								$periodo = $periodo - 1; //un año atras
							} else {
								$mes--;
							}		
						}							
						
						$pendiente = $importeContrato - $importe_origen;
						
						$style = '';
						
						if ($last!=$data['0']['CodigoProyecto']) {
							$style=' font-weight: bold; border-top: solid 4px #333; ';
						}
						
						$last=$data['0']['CodigoProyecto'];
						$last_item_n=count($data)-1;

						if ($flag_view)	{
							$importeContrato=number_format($importeContrato, 2, ',', '');	
							$importeOrigen=number_format($importe_origen, 2, ',', '');
							$pendiente=number_format($pendiente, 2, ',', '');

							$importeCarteraPendiente = number_format($importeCarteraPendiente, 2, ',', '');
							$totalProyectoFacturadoMes = number_format($totalProyectoFacturadoMes, 2, ',', '');
							$totalImporteTodosContratos = number_format($totalImporteTodosContratos, 2, ',', '');
							$totalProyectoFacturadoOrigen = number_format($totalProyectoFacturadoOrigen, 2, ',', '');
							$totalProyectoCarteraPendiente = number_format($totalProyectoCarteraPendiente, 2, ',', '');

							$m5=number_format($dm['5'], 2, ',', '');	
							$m4=number_format($dm['4'], 2, ',', '');
							$m3=number_format($dm['3'], 2, ',', '');
							$m2=number_format($dm['2'], 2, ',', '');	
							$m1=number_format($dm['1'], 2, ',', '');
							$m0=number_format($dm['0'], 2, ',', '');
							$csv.=
									'"'		.trim(	$codigoProyecto													)		.'"'.$separator.''.
									'"'		.trim(	$descripcion													)		.'"'.$separator.''.
									'"'		.trim(	$proyecto.' - '.$descripcionProyecto							)		.'"'.$separator.''.
									''		.trim(	$importeContrato												)		.''.$separator.''.
									''		.trim(	$importeOrigen													)		.''.$separator.''.
									''		.trim(	$pendiente														)		.''.$separator.''.

									''		.trim(	$importeCarteraPendiente										)		.''.$separator.''.
									''		.trim(	$totalProyectoFacturadoMes										)		.''.$separator.''.
									''		.trim(	$totalImporteTodosContratos										)		.''.$separator.''.
									''		.trim(	$totalProyectoFacturadoOrigen									)		.''.$separator.''.
									''		.trim(	$totalProyectoCarteraPendiente									)		.''.$separator.''.

									''		.trim(	$m5																)		.''.$separator.''.
									''		.trim(	$m4																)		.''.$separator.''.
									''		.trim(	$m3																)		.''.$separator.''.
									''		.trim(	$m2																)		.''.$separator.''.
									''		.trim(	$m1																)		.''.$separator.''.
									''		.trim(	$m0																)		.''.$separator.''.
									'"'		.trim(	$estadoCierre													)		.'"'.$separator.''.
									'"'		.trim(	$estado_facturacion												)		.'"'.$separator.''.
									'"'		.trim(																	)		.'"'.$separator.''.
									'"'		.trim(	$observaciones													)		.'"'.$separator.''.
									"\n";
						}
						
						$n++;
						
						//$trs.='<pre>'.$d.'</pre>';
					}
				}
			}
		}

		return $csv;

	} else {
		
		$n=1;
		$last='';
		foreach ($main_loop_arr as $ml) {
			if ($ml['CodigoProyecto']!='') {
				if (isset($GLOBALS['ADMIN_I9'])) {
					$second_loop_arr=get_db_data(array('listar-desglose-proyectos-facturacion',$ml['CodigoProyecto'],$custom_query));
				} else {
					$second_loop_arr=get_db_data(array('listar-desglose-proyectos-facturacion-jefe-obra',$ml['CodigoProyecto'],$custom_query));
				}
				
				if (is_array($second_loop_arr)) {
					
					foreach ($second_loop_arr as $sl) {						
						//echo '<pre>'.print_r($sl,true).'</pre>';
						$i=0;
						$start=0;
						$codigoProyecto = $ml['CodigoProyecto'];
						$descripcion = '';
						$proyecto = '';
						$descripcionProyecto = '';
						$importeContrato = '';
						$estadoCierre = '';
						$observaciones = '';
						$pendiente = '';
						$importeCarteraPendiente = '';
						$totalProyectoFacturadoMes = '';
						$totalImporteTodosContratos = '';
						$totalProyectoFacturadoOrigen = '';
						$totalProyectoCarteraPendiente = '';
						$mes = date("m", strtotime($form_date));
						$periodo = date("Y", strtotime($form_date));
						//foreach ($date_arr as $d) {
						for($j = 0; $j < 6; $j++) {
							//echo '<pre>'.print_r($d,true).'</pre>';
							//$mes=$d['M']; 
							//$periodo=$d['Y'];
							
							if (isset($GLOBALS['ADMIN_I9'])) {
								$data = get_db_data(array('listar-proyectos-facturacion-filtrado',$sl['Proyecto'],$mes,$periodo,$custom_query));
							} else {
								$data = get_db_data(array('listar-proyectos-facturacion-jefe-obra-filtrado',$sl['Proyecto'],$mes,$periodo,$custom_query));
							}

							if(is_array($data)) {
								//echo '<pre>'.print_r($data, true).'</pre>';
								/* guardo los datos por si en algun caso no encuentra valores en el mes indicado ($data no es array) */
								if ($descripcion == '') {
									$descripcion = $data['0']['Descripcion'];
								}
								if ($proyecto == '') {
									$proyecto = $data['0']['Proyecto'];
								}
								if ($descripcionProyecto == '') {
									$descripcionProyecto = $data['0']['DescripcionProyecto'];
								}
								if ($importeContrato == '') {
									$importeContrato = $data['0']['ImporteContrato'];
								}
								if ($estadoCierre == '') {
									if ($data['0']['Activa'] == '0') {
										$estadoCierre = 'X';
									}
								}
								if ($observaciones == '') {
									$observaciones = $data['0']['ObsProyecto'];
								}
								if ($importeCarteraPendiente == '') {
									$importeCarteraPendiente = $data['0']['ImporteCarteraPendiente'];
								}
								if ($totalProyectoFacturadoMes == '') {
									$totalProyectoFacturadoMes = $data['0']['TotalProyectoFacturadoMes'];
								}
								if ($totalImporteTodosContratos == '') {
									$totalImporteTodosContratos = $data['0']['TotalImporteContratos'];
								}
								if ($totalProyectoFacturadoOrigen == '') {
									$totalProyectoFacturadoOrigen = $data['0']['TotalProyectoFacturadoOrigen'];
								}
								if ($totalProyectoCarteraPendiente == '') {
									$totalProyectoCarteraPendiente = $data['0']['TotalProyectoCarteraPendiente'];
								}
							}

							//echo '<pre>'.print_r($data,true).'</pre>';
							
							if ($start==0) {
								if ($data['0']['ImporteOrigen'] != '') {
									$importe_origen=$data['0']['ImporteOrigen'];
								}
								
								$flag_view=true;
								
								if (cG('estado_facturacion')!='-1')	{
									if (cG('estado_facturacion')!=$data['0']['EstadoFacturacion']) {
										$flag_view=false;
									}
								}
																						
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_0='background-color:#f9b374;';
									$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2')	{
									$bg_color_0='background-color:#90f974;';
									$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3')	{
									$bg_color_0='background-color:#90f974';
									$estado_facturacion='Facturado';
								} else {
									$bg_color_0='background-color:#e86f6f;';
									$estado_facturacion='Pendiente';	
								}
							}
							if ($start==1) {
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_1='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_1='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_1='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_1='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==2) {										
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_2='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_2='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_2='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_2='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==3) {								
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_3='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_3='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_3='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_3='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==4) {
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_4='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_4='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3')	{
									$bg_color_4='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_4='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}
							if ($start==5) {												
								if ($data['0']['EstadoFacturacion']=='1') {
									$bg_color_5='background-color:#f9b374;';
									//$estado_facturacion='No se factura';
								} elseif ($data['0']['EstadoFacturacion']=='2') {
									$bg_color_5='background-color:#90f974;';
									//$estado_facturacion='Cerrada Facturacion';
								} elseif ($data['0']['EstadoFacturacion']=='3') {
									$bg_color_5='background-color:#90f974';
									//$estado_facturacion='Facturado';
								} else {
									$bg_color_5='background-color:#e86f6f;';
									//$estado_facturacion='';	
								}
							}	
							
							if($data['0']['ImporteProyecto'] == '') {
								$dm[$i] = '.0000000000';
							} else {
								$dm[$i]=$data['0']['ImporteProyecto'];
							}
							$i++;
							$start++;			
							//echo '<pre>'.print_r($data,true).'</pre>';
							if ($mes - 1 == 0) {
								$mes = 12;
								$periodo = $periodo - 1; //un año atras
							} else {
								$mes--;
							}		
						}							
						
						$pendiente = $importeContrato - $importe_origen;
						
						$style='';
						
						if ($last!=$data['0']['CodigoProyecto']) {
							$style=' font-weight: bold; border-top: solid 4px #333; ';
						}
						
						$last = $data['0']['CodigoProyecto'];
						$last_item_n = count($data)-1;

						if ($flag_view) {
							
							$trs.=
								'
					<tr name="tr-'.$codigoProyecto.'" class="'.$last.' '.$data['0']['CodigoProyecto'].'">
						<td style="'.$style.'">'.$n.'</td>
						<td style="'.$style.'">'.$codigoProyecto.'</td>
						<td style="'.$style.'">'.utf8_encode($descripcion).'</td>					
						<td style="'.$style.'">'.$proyecto.' - '.$descripcionProyecto.'</td>					
						<td style="'.$style.' text-align:right;">'.aux_money_format($importeContrato).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($importe_origen).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($pendiente).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($importeCarteraPendiente).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($totalProyectoFacturadoMes).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($totalImporteTodosContratos).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($totalProyectoFacturadoOrigen).'</td>
						<td style="'.$style.' text-align:right;">'.aux_money_format($totalProyectoCarteraPendiente).'</td>
						<td style="'.$style.' text-align:right; '.$bg_color_5.'">'.aux_money_format($dm['5']).'</td>
						<td style="'.$style.' text-align:right; '.$bg_color_4.'">'.aux_money_format($dm['4']).'</td>
						<td style="'.$style.' text-align:right; '.$bg_color_3.'">'.aux_money_format($dm['3']).'</td>
						<td style="'.$style.' text-align:right; '.$bg_color_2.'">'.aux_money_format($dm['2']).'</td>
						<td style="'.$style.' text-align:right; '.$bg_color_1.'">'.aux_money_format($dm['1']).'</td>
						<td style="'.$style.' text-align:right; '.$bg_color_0.'">'.aux_money_format($dm['0']).'</td>
						<td style="'.$style.' text-align:center;">'.$estadoCierre.'</td>
						<td style="'.$style.'">'.$estado_facturacion.'</td>										
						<td style="'.$style.'"></td>
						<td style="'.$style.'">'.$observaciones.'</td>																	
					</tr>
				';
						}
						
						$n++;
						
						//$trs.='<pre>'.$d.'</pre>';
					}
				}
			}
		}
	
		$table_body = $trs;

		$table_body .= "<script>$('#start').val('". $form_date ."');</script>";
		
		return $table_body;
	}
	}
}

function make_filter_9 () {
	
	$filtro					=	file_get_contents_utf8(ABSPATH.'/template/filtro-9.html');
	
	$proyectos_venta		=	render_listar_proyectos_i9();
	
	$estados_facturacion	=	render_listar_estados_facturacion_i9();
	
	$estados				=	render_listar_estados_proyectos_i9();

	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a target="_blank" style="margin-left: 20px" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';
	} else {		
		$excel_button='<button style="margin-left: 20px" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Excel</a>';
	}
	
	$filtro					=	str_replace('{PROYECTOS_VENTA}',$proyectos_venta,$filtro);
	
	$filtro					=	str_replace('{ESTADOS_PROYECTO}',$estados,$filtro);
	
	$filtro					=	str_replace('{ESTADOS_FACTURACION}',$estados_facturacion,$filtro);

	$filtro 				=	str_replace('{EXCEL_URL}',$excel_button,$filtro);
	
	return $filtro;
}

function render_listar_proyectos_i9 ($estado = '', $inicio = true) {
	$options = '';

	$id_jefe_obra = $_SESSION['id_jefe_obra'];
	if ($estado == '' && $inicio) {
		// por defecto saco los proyectos activos
		// solo cuando sea de verdad el inicio, pq si se va a cambiar el desplegable al cambiar el filtro de estado no puede venir la variable $inicio a true, pq igualmente vendria el $estado ($where = vacio!)
		$estado = " AND Activa='-1' ";
	}
	
	if (isset($GLOBALS['ADMIN_I9'])) {
		$data = get_db_data(array('listar-proyectos-i9', $estado));
	} else {
		$data = get_db_data(array('listar-proyectos-jefes-obra-i9', $estado));
	}
	
	$current_value=cG('prv');
	foreach ($data as $d) {
		if ($d['CodigoProyecto']!='') {
			if ($current_value==$d['CodigoProyecto']) {$selected=' selected="selected" ';} else {$selected='';}
			
			if (isset($GLOBALS['ADMIN_I9'])) {
				$descripcion = ucfirst($d['Descripcion']);
			} else {
				$descripcion = ucfirst($d['DescripcionProyecto']);
			}
			$options.='<option '.$selected.' value="'.$d['CodigoProyecto'].'">'.$d['CodigoProyecto'].' - '.$descripcion.'</option>';
		}
		
	}
	
	return $options;	
}

function render_listar_estados_facturacion_i9 () {
	$options='';
	
	$id_jefe_obra=$_SESSION['id_jefe_obra'];
	
	$data=array('0'=>'Pendiente','1'=>'No se factura','2'=>'Cerrada Facturacion','3'=>'Facturado');
	$ids=array_keys($data);
	
	$i = 0;
	$current_value = cG('estado_facturacion');
	foreach ($data as $d) {
		$selected = '';
		if ($current_value != '' && $current_value == $ids[$i]) {$selected=' selected="selected" ';} else {$selected='';}
		
		$ids=array_keys($data);
		$options.='<option '.$selected.' value="'.$ids[$i].'">'. $d .'</option>';
		$i++;
	}
	
	return $options;	
}

function render_listar_estados_proyectos_i9 () {
	$options='';
	
	$id_jefe_obra=$_SESSION['id_jefe_obra'];
	
	$data=array('2'=>'Obras Activas','1'=>'Obras Cerradas');
	$ids=array_keys($data);
	
	$i=0;
	$current_value=cG('estado');
	foreach ($data as $d) {
		if ($current_value==$ids[$i]) {$selected=' selected="selected" ';} else {$selected='';}
		
		$ids=array_keys($data);
		$options.='<option '.$selected.' value="'.$ids[$i].'">'.$d.'</option>';
		$i++;
	}
	
	return $options;	
}

function six_months_backwards_array ($date) {
	
	$date_explode = explode('-',$date);
	
	if ($date_explode['0'] < $date_explode['1']){$date = $date_explode['1'].'-'.$date_explode['0'];}
	
	$months_names = array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
	
	$m=date ('m', strtotime($date));
	$m1=date('m', strtotime($date." -1 month"));
	$m2=date('m', strtotime($date." -2 month"));
	$m3=date('m', strtotime($date." -3 month"));
	$m4=date('m', strtotime($date." -4 month"));
	$m5=date('m', strtotime($date." -5 month"));
	
	$y=date ('Y', strtotime($date));
	$y1=date('Y', strtotime($date." -1 month"));
	$y2=date('Y', strtotime($date." -2 month"));
	$y3=date('Y', strtotime($date." -3 month"));
	$y4=date('Y', strtotime($date." -4 month"));
	$y5=date('Y', strtotime($date." -5 month"));


	$ms['0']['M']=$m; $ms['0']['Y']=$y;
	$ms['1']['M']=$m1; $ms['1']['Y']=$y1;
	$ms['2']['M']=$m2; $ms['2']['Y']=$y2;
	$ms['3']['M']=$m3; $ms['3']['Y']=$y3;
	$ms['4']['M']=$m4; $ms['4']['Y']=$y4;
	$ms['5']['M']=$m5; $ms['5']['Y']=$y5;
	
	return $ms;	
}

function six_months_backwards_array_name ($date) {
	
	$date_explode=explode('-',$date);
	
	if ($date_explode['0']<$date_explode['1']){$date=$date_explode['1'].'-'.$date_explode['0'];}
	
	
	$months_names=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
	
	
	$m=date ('m', strtotime($date));
	$m1=date('m', strtotime($date." -1 month"));
	$m2=date('m', strtotime($date." -2 month"));
	$m3=date('m', strtotime($date." -3 month"));
	$m4=date('m', strtotime($date." -4 month"));
	$m5=date('m', strtotime($date." -5 month"));
	
	$y=date ('Y', strtotime($date));
	$y1=date('Y', strtotime($date." -1 month"));
	$y2=date('Y', strtotime($date." -2 month"));
	$y3=date('Y', strtotime($date." -3 month"));
	$y4=date('Y', strtotime($date." -4 month"));
	$y5=date('Y', strtotime($date." -5 month"));

	$ms=array
		(
			'0'	=>	$months_names[$m].' '.$y,
			'1'	=>	$months_names[$m1].' '.$y1,
			'2'	=>	$months_names[$m2].' '.$y2,
			'3'	=>	$months_names[$m3].' '.$y3,
			'4'	=>	$months_names[$m4].' '.$y4,
			'5'	=>	$months_names[$m5].' '.$y5,
			);
	
	return $ms;
}

function months_name_replace ($date,$content) {
	$months_names=array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');

	$mes = date("m", strtotime($date));
	$periodo = date("Y", strtotime($date));
						
	for ($i = 0; $i < 6; $i++) {
		$mes = sprintf("%02d", $mes);
		$md[$i] = $months_names[$mes].' '.$periodo;
		if ($mes - 1 == 0) {
			$mes = 12;
			$periodo = $periodo - 1; //un año atras
		} else {
			$mes--;
		}
	}
	
	$replaces=array(
			'{MES-5}'	=>	$md[5],
			'{MES-4}'	=>	$md[4],
			'{MES-3}'	=>	$md[3],
			'{MES-2}'	=>	$md[2],
			'{MES-1}'	=>	$md[1],
			'{MES}'		=>	$md[0]
		);
	
	$content = str_replace(array_keys($replaces),$replaces,$content);
	
	return $content;	
}

?>