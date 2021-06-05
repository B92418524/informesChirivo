<?php

function render_tabla_i5 () {
	/** PARAMETROS DE ENTRADA ******************************************************************************************* */
	
	$obra				=	cG('wk');
	
	/*
	$limites		['YS']='2009';
	$limites		['YE']='2014';
	$limites		['MS']='6';
	$limites		['ME']='9';
	*/
	
	//i5_periodos($obra);
	
	//echo '<pre>'.print_r(i5_periodos($limites),true).'</pre>';
	
	$graphic='';
	
	$last_v1=0;	$last_v2=0;	$last_v3=0;	$last_v4=0;	$last_v5=0;	$last_v6=0;	$last_v7=0;	$last_v8=0;
	
	if ($obra!='') {
		$periodos=i5_periodos($obra);
		
		/** TABLA 1 */
		
		$ths=	'<td>CONCEPTO</td>';
		$f1=	'<td>FACTURADO</td>';
		$f2=	'<td>PF CIERRE HASTA FIN DE MES</td>';
		$f3=	'<td>PF FALTA DE CONTRATO</td>';
		$f4=	'<td>PF FALTA DE PARTIDAS</td>';
		$f5=	'<td>PF OTROS MOTIVOS</td>';
		
		$f6=	'<td>GASTOS CONTABILIZADOS</td>';
		$f7=	'<td>PC SUBCONTRATAS Y PROVEEDORES</td>';
		$f8=	'<td>ACOPIOS O ENTREGAS A CUENTA</td>';
		
		$f9=	'<td>BENEFICIO MENSUAL</td>';
		$f10=	'<td>BENEFICIO ORIGEN</td>';
		$f11=	'<td>% BENEFICIO MENSUAL</td>';
		$f12=	'<td>% BENEFICIO ORIGEN</td>';
		
		$ft1=	'<td>TOTAL IMPORTE</td>';
		$ft2=	'<td>TOTAL IMPORTE</td>';
		
		$vac='<td style="border:0; height:100px;"></td>';
		
		//echo '<pre>'.print_r($periodos,true).'</pre>';
		
		$v1th=0; $v2th=0; $v3th=0; $v4th=0; $v5th=0; $v6th=0; $v7th=0; $v8th=0;
		
		$vv1=array();
		$vv2=array();
				
		$ci=0;
		
		$facturacion_origen=0;
		$gastos_origen=0;
		
		$ingresos_dg	=	array();
		$gastos_dg		=	array();
		$benficio_dg	=	array();
		
		foreach ($periodos as $p) {
			$data=get_db_data(array('i5-linea',$obra,$p['M'],$p['Y']));
			$data=$data['0'];
			
			//echo '<pre>'.print_r($data,true).'</pre>';
			
			$v1=0; $v2=0; $v3=0; $v4=0; $v5=0; $v6=0; $v7=0; $v8=0;
			
			
			$vac.='<td style="border:0; height:50px;"></td>';
			
			$v1=$data['FACTURADO'];
			$v2=$data['PF_CIERRE'];
			$v3=$data['PF_FALTA_CONTRATO'];
			$v4=$data['PF_APROBACION'];
			$v5=$data['PF_OTROS'];
			
			$v6=$data['GASTOSCONTA'];
			$v7=$data['IMPORTE_P_CERTIFICACION'];
			$v8=$data['IMPORTE_ACOPIO_ENTREGA'];
			
			$ingresos_dg[]	=	$data['FACTURADO']+$v1+$v2+$v3+$v4+$v5-$last_v1-$last_v2-$last_v3-$last_v4-$last_v5;
			$gastos_dg[]	=	$data['GASTOSCONTA']-$v6-$v7+$v8+$last_v6+$last_v7-$last_v8;
			$benficio_dg[]	=	$data['FACTURADO']-$data['GASTOSCONTA'];
			
			//$facturacion_origen		=	$facturacion_origen		+		$data['FACTURADO'];
			//$gastos_origen			=	$gastos_origen		 	+		$data['GASTOSCONTA'];
			//$beneficio_origen		=	$facturacion_origen		-		$gastos_origen;
						
			$facturacion_origen		=	$data['FACTURACION_ORIGEN'];
			$gastos_origen			=	$data['GASTOS_ORIGEN'];
			
			
			/*
			if ($v1==''){$v1=0;}
			if ($v2==''){$v2=0;}
			if ($v3==''){$v3=0;}
			if ($v4==''){$v4=0;}
			if ($v5==''){$v5=0;}
			if ($v6==''){$v6=0;}
			if ($v7==''){$v7=0;}
			if ($v8==''){$v8=0;}
			*/
			
			$v1th=$v1th+$v1;
			$v2th=$v2th+$v2;
			$v3th=$v3th+$v3;
			$v4th=$v4th+$v4;
			$v5th=$v5th+$v5;
			
			$v6th=$v6th+$v6;
			$v7th=$v7th+$v7;
			$v8th=$v8th+$v8;
						
			$ths.=	'<td style="font-weight:bold; text-align:center;">'		.$p['M'].'/'.$p['Y']				.'</td>';
			$f1.=	'<td style="text-align:right;">'		.aux_money_format($v1).'</td>';
			$f2.=	'<td style="text-align:right;">'		.aux_money_format($v2).'</td>';
			$f3.=	'<td style="text-align:right;">'		.aux_money_format($v3).'</td>';
			$f4.=	'<td style="text-align:right;">'		.aux_money_format($v4).'</td>';
			$f5.=	'<td style="text-align:right;">'		.aux_money_format($v5).'</td>';
			
			$vv1[$ci]=$v1+$v2+$v3+$v4+$v5;
			
			$f6.=	'<td style="text-align:right;">'		.aux_money_format($v6).'</td>';
			$f7.=	'<td style="text-align:right;">'		.aux_money_format($v7).'</td>';
			$f8.=	'<td style="text-align:right;">'		.aux_money_format($v8).'</td>';
			
			
			$cal_1
			=
				$v1			//		FACTURADO	
				+$v2		//		PF CIERRE HASTA FIN DE MES
				+$v3		//		PF FALTA DE CONTRATO
				+$v4		//		PF FALTA DE PARTIDAS
				+$v5		//		PF OTROS MOTIVOS
				-$v6		//		GASTOS CONTABILIZADOS
				-$v7		//		PC SUBCONTRATAS Y PROVEEDORES
				+$v8		//		ACOPIOS O ENTREGAS A CUENTA
				
				-$last_v2		//	LAST PF CIERRE HASTA FIN DE MES
				-$last_v3		//	LAST 	PF FALTA DE CONTRATO
				-$last_v4		//	LAST PF FALTA DE PARTIDAS
				-$last_v5		//	LAST PF OTROS MOTIVOS			
				+$last_v7		//	LAST PC SUBCONTRATAS Y PROVEEDORES
				-$last_v8		//	LAST ACOPIOS O ENTREGAS A CUENTA
			;
			
			
			$beneficio_origen
			=
				$facturacion_origen
				-$gastos_origen
				+$v2		//		PF CIERRE HASTA FIN DE MES
				+$v3		//		PF FALTA DE CONTRATO
				+$v4		//		PF FALTA DE PARTIDAS
				+$v5		//		PF OTROS MOTIVOS
				
				-$v7		//		PC SUBCONTRATAS Y PROVEEDORES
				+$v8		//		ACOPIOS O ENTREGAS A CUENTA
				
				-$last_v2		//	LAST PF CIERRE HASTA FIN DE MES
				-$last_v3		//	LAST 	PF FALTA DE CONTRATO
				-$last_v4		//	LAST PF FALTA DE PARTIDAS
				-$last_v5		//	LAST PF OTROS MOTIVOS			
				+$last_v7		//	LAST PC SUBCONTRATAS Y PROVEEDORES
				-$last_v8		//	LAST ACOPIOS O ENTREGAS A CUENTA
			;
			
			// $f9.=	'<td style="text-align:right;">'		.aux_money_format($cal_1).'</td>';
			// $f10.=	'<td style="text-align:right;">'		.aux_money_format($beneficio_origen).'</td>';
			// $f11.=	'<td style="text-align:right;">'		.''.'</td>';
			// $f12.=	'<td style="text-align:right;">'		.''.'</td>';
			
			$vv2[$ci]=$v6+$v7-$v8;
			
			$last_v1=$v1;
			$last_v2=$v2;
			$last_v3=$v3;
			$last_v4=$v4;
			$last_v5=$v5;
			$last_v6=$v6;
			$last_v7=$v7;
			$last_v8=$v8;
			
			$ci++;
		}
		
		$sumavertical_t1='<td style="font-weight:bold;">Total</td>';
		$sumavertical_t2='<td style="font-weight:bold;">Total</td>';
		
		$ci = 0;

		/* el total de la primera y segunda tabla estaba mal, y los beneficios no estaban hechos ... */
		$ultimo_periodo = array('m' => $periodos[0]['M'], 'y' => $periodos[0]['Y']);
		$periodos_array		= listar_array_de_periodos_i4($periodos[0]['M'], $periodos[0]['Y'], '12', '2016');
		$obtener_datos_obra = get_db_data(array('listar-obras-i4-filtrado','WHERE D_CODIGO_PROYECTO='.$obra));

		if (is_array($obtener_datos_obra)) {
			foreach ($periodos_array as $p)	{

				$facturacion_origen = 0;
				$facturacion_mes = 0;
				$gastos_contabilizados_origen = 0;
				$gastos_contabilizados_periodo = 0;
				foreach ($obtener_datos_obra as $datos_obra) {
					$codigo_cliente		= $datos_obra['D_CODIGO_CLIENTE'];
					$facturacion_origen	= obtener_facturacion_origen_por_cliente_y_perido_i4($obra,$codigo_cliente,$p);
					$facturacion_mes	= obtener_facturacion_mes($obra,$codigo_cliente,$p,$facturacion_mes);
					$gastos_arr			= obtener_gastos_por_id_obra_y_periodo_i4($obra,$codigo_cliente,$p,$gastos_contabilizados_origen,$gastos_contabilizados_periodo);
					$gastos_contabilizados_origen = $gastos_arr['gastos_contabilizados_origen'];
					$gastos_contabilizados_periodo = $gastos_arr['gastos_contabilizados_periodo'];
					$ultimo_periodo 	= $p;
				}

				$id_informe 	= obtener_id_informe($obra,$ultimo_periodo);
				$last_inf_arr	= make_i3_array($id_informe);
				$total_pendiente_de_cobro 	= fix_sum(array($last_inf_arr[0]['PF_CIERRE'],$last_inf_arr[0]['PF_FALTA_CONTRATO'],$last_inf_arr[0]['PF_APROBACION'],$last_inf_arr[0]['PF_OTROS']));

				$periodo_anterior = $ultimo_periodo['m'].'-'.$ultimo_periodo['y'];
				
				$total_prevision_de_ingresos_periodo = fix_sum(array($total_pendiente_de_cobro,$facturacion_mes))-nf(obtener_total_pendiente_cobro_de_un_perido_anterior_al_indicado($obra,$periodo_anterior));
				$total_prevision_de_ingresos = fix_sum(array($facturacion_origen,$last_inf_arr[0]['PF_CIERRE'],$last_inf_arr[0]['PF_FALTA_CONTRATO'],$last_inf_arr[0]['PF_APROBACION'],$last_inf_arr[0]['PF_OTROS']));

				$prev_fact_periodo = aux_money_format($total_prevision_de_ingresos_periodo);

				if ($vv1[$ci] <= 0) {
					$prev_fact_periodo = '';
				}

				$total_prevision_gastos_periodo	= compatibilidad_coma_punto(obtener_total_prevision_gastos_periodo($id_informe,$gastos_contabilizados_periodo,$obra,$periodo_anterior));
				$prev_gastos_periodo = aux_money_format($total_prevision_gastos_periodo);

				if ($vv2[$ci] <= 0) {
					$prev_gastos_periodo = '';
				}

				//$sumavertical_t1.='<td style="text-align:right;" class="r">'.$prev_fact_periodo.'</td>';
				//$sumavertical_t2.='<td style="text-align:right;" class="s">'.$prev_gastos_periodo.'</td>';
				$ci++;

				/* tabla beneficios */
				$total_prevision_gastos_origen = compatibilidad_coma_punto(obtener_total_prevision_gastos_origen($id_informe,$gastos_contabilizados_origen,$obra,$periodo_anterior));
				$beneficio_origen =	$total_prevision_de_ingresos - $total_prevision_gastos_origen;		
				$beneficio_periodo = $total_prevision_de_ingresos_periodo - $total_prevision_gastos_periodo;

				if ($beneficio_periodo == 0) {
					$beneficio_origen = 0;
				}

				$beneficio_origen_p = '';
				if ($total_prevision_gastos_origen!=0) {							
					$beneficio_origen_p	= (($total_prevision_de_ingresos/$total_prevision_gastos_origen)-1)*100;
					$beneficio_origen_p = round($beneficio_origen_p,2).'&nbsp;%';
				}

				$beneficio_periodo_p = '';
				if ($total_prevision_gastos_periodo!=0) { 
					$beneficio_periodo_p = ( ($total_prevision_de_ingresos_periodo	/	$total_prevision_gastos_periodo	)	-1	)	*	100	;
					$beneficio_periodo_p = round($beneficio_periodo_p,2).'&nbsp;%';
				} elseif ($total_prevision_de_ingresos_periodo>0) {
					$beneficio_periodo_p = 100;
					$beneficio_periodo_p = round($beneficio_periodo_p,2).'&nbsp;%';
				}

				$f9 .= '<td style="text-align:right;">'.aux_money_format($beneficio_periodo).'</td>';
				$f10 .= '<td style="text-align:right;">'.aux_money_format($beneficio_origen).'</td>';
				$f11 .= '<td style="text-align:right;">'.$beneficio_periodo_p.'</td>';
				$f12 .= '<td style="text-align:right;">'.$beneficio_origen_p.'</td>';
			}
		}

		$contador = 0;
		foreach ($vv1 as $c) {
		 	$sumavertical_t1.='<td>'.aux_money_format($vv1[$contador]).'</td>';
		 	$sumavertical_t2.='<td>'.aux_money_format($vv2[$contador]).'</td>';
			$contador++;
		}
		
		$sumavertical_t1.='<td></td>';
		$sumavertical_t2.='<td></td>';
		
		$thso=$ths.'<td style="border:0 !important;"></td>';
		
		$ths.='<td style="font-weight:bold;">Total</td>';
		
		$f1.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v1th).'</td>';
		$f2.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v2th).'</td>';
		$f3.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v3th).'</td>';
		$f4.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v4th).'</td>';
		$f5.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v5th).'</td>';
		
		$f6.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v6th).'</td>';
		$f7.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v7th).'</td>';
		$f8.=	'<td style="font-weight:bold; text-align:right;">'		.aux_money_format($v8th).'</td>';
		
		$f9.=	'<td style="border:0 !important;"></td>';
		$f10.=	'<td style="border:0 !important;"></td>';
		$f11.=	'<td style="border:0 !important;"></td>';
		$f12.=	'<td style="border:0 !important;"></td>';
		
		$t1_content='<tr>'.$ths.'</tr>'.'<tr>'.$f1.'</tr>'.'<tr>'.$f2.'</tr>'.'<tr>'.$f3.'</tr>'.'<tr>'.$f4.'</tr>'.'<tr>'.$f5.'</tr><tr>'.$sumavertical_t1.'</tr>';
		$t2_content='<tr>'.$ths.'</tr>'.'<tr>'.$f6.'</tr>'.'<tr>'.$f7.'</tr>'.'<tr>'.$f8.'</tr><tr>'.$sumavertical_t2.'</tr>';
		
		$t3_content='<tr>'.$thso.'</tr>'.'<tr>'.$f9.'</tr>'.'<tr>'.$f10.'</tr>'.'<tr>'.$f11.'</tr><tr>'.$f12.'</tr>';
		
		$table_1=
			'<table class="table table-bordered" style="border:0 !important;">'.
			$t1_content.
			'<tr style="border:0 !important;">'.$vac.'</tr>'.				
			$t2_content.
			'<tr style="border:0 !important;">'.$vac.'</tr>'.				
			$t3_content.
			'</table><br/><br/><br/><br/><br/>';
		
		$graphic = render_graph_1($ingresos_dg,$gastos_dg,$benficio_dg,$periodos);
	
	} else {		
		$table_1 = '<table class="table table-bordered"><tr><td style="padding:30px; font-size:24px; text-align:center;">Seleccione una obra</td></tr></table><br/><br/><br/><br/><br/>';
	}
	
	/*
	if ($obra!='')
	{
		
		$out=
			'*************<br/>'.
			print_r(i5_periodos($obra),true).
			'*************<br/>';
		
		
	}
	*/
	
	$out=$table_1.$graphic;	
	
	return $out;
}

function render_graph_1 ($ingresos_dg, $gastos_dg, $benficio_dg, $periodos) {
	
	$ingresos_gr	=	'[';
	$gastos_gr		=	'[';
	$benficio_gr	=	'[';
	$ticks			=	'[';
	
	$i=0;
	foreach ($periodos as $p) {
		if ($i==0){$sep='';} else {$sep=',';}
		
		$ticks.=$sep."'".$p['M'].'/'.$p['Y']."'";
		
		if ($ingresos_dg[$i]=='')	{$ingresos_dg[$i]=0;}	else {$ingresos_dg[$i]	=	$ingresos_dg[$i]	+0;}
		if ($gastos_dg[$i]=='')		{$gastos_dg[$i]=0;}		else {$gastos_dg[$i]	=	$gastos_dg[$i]		+0;}
		if ($benficio_dg[$i]=='')	{$benficio_dg[$i]=0;}	else {$benficio_dg[$i]	=	$benficio_dg[$i]	+0;}
		
		$ingresos_gr	.=	$sep.$ingresos_dg[$i];
		$gastos_gr		.=	$sep.$gastos_dg[$i];
		$benficio_gr	.=	$sep.$benficio_dg[$i];
		
		$i++;
	}
	
	$ingresos_gr	.=	']';
	$gastos_gr		.=	']';
	$benficio_gr	.=	']';
	$ticks			.=	']';
	
	
	$out=
"
<div id=\"chart1\" style=\"height:600px;width:1200px;\"></div>
<script>		
	$(document).ready(function(){		
        $.jqplot.config.enablePlugins = true;
        var s1 = ".$ingresos_gr.";
   		var s2 = ".$gastos_gr.";
		var s3 = ".$benficio_gr.";
        var ticks = ".$ticks.";
         
        plot1 = $.jqplot('chart1', [s1,s2,s3], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{               
                pointLabels: { show: false }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: false },
			legend: { show: true },
			series: [{ label: 'Ingresos' },{ label: 'Gastos' },{ label: 'Beneficios' }]
        });
     
        
    });	
</script>	
";

	return $out;
}


function i5_periodo_limites ($obra) {
	$out=array();
	
	$year_start_arr		=	get_db_data(array('min-year-i5',$obra));
	$year_end_arr		=	get_db_data(array('max-year-i5',$obra));
	
	
	$year_start			=	$year_start_arr	['0']['min'];
	$year_end			=	$year_end_arr	['0']['max'];
	
	
	//echo '<pre>'.print_r($year_start,true).'</pre>';
	//echo '<pre>'.print_r($year_end,true).'</pre>';
	
	$month_start_arr	=	get_db_data(array('min-month-i5',$obra,$year_start));
	$month_end_arr		=	get_db_data(array('max-month-i5',$obra,$year_end));
	
	$month_start		=	$month_start_arr['0']['min'];
	$month_end			=	$month_end_arr['0']['max'];
	
	//echo '<pre>'.print_r($month_start,true).'</pre>';
	//echo '<pre>'.print_r($month_end,true).'</pre>';
	
	$out['YS']			=	$year_start;
	$out['YE']			=	$year_end;
	$out['MS']			=	$month_start;
	$out['ME']			=	$month_end;
	
	return $out;
	
}

function i5_periodos ($obra) {
	$limites=i5_periodo_limites($obra);
	
	//echo '<pre>'.print_r($limites,true).'</pre>';
	
	//die;
	
	$out=array();	
	
	$year_start = $limites['YS']; $year_end = $limites['YE']; $month_start	= $limites['MS']; $month_end = $limites['ME'];
	
	//echo '->'.print_r($year_start['0'],true);
	
	if ($year_start==$year_end)	{
		//echo '1A';
		$cursor_m=$month_start;
		
		$i=0;
		while ($cursor_m<=$month_end) {
			$out[$i]['Y']		=	$year_start;
			$out[$i]['M']		=	$cursor_m;
			
			$cursor_m++;
			$i++;
		}
	} elseif ($year_start<$year_end) {
		$cursor_y		=	$year_start;
		
		$i=0;
		
		while ($cursor_y<=$year_end) {			
			if ($cursor_y==$year_start)	{				
				$cursor_m=$month_start;
				
				
				while ($cursor_m<=12) {
					$out[$i]['Y']		=	$cursor_y;
					$out[$i]['M']		=	$cursor_m;
					
					$cursor_m++;
					$i++;
				}
			} elseif ($cursor_y!=$year_end)	{
				$cursor_m=1;
				
				while ($cursor_m<=12) {
					$out[$i]['Y']		=	$cursor_y;
					$out[$i]['M']		=	$cursor_m;
					
					$cursor_m++;
					$i++;
				}
			} elseif ($cursor_y==$year_end)	{				
				$cursor_m=1;
				
				while ($cursor_m<=$month_end) {
					$out[$i]['Y']		=	$cursor_y;
					$out[$i]['M']		=	$cursor_m;
					
					$cursor_m++;
					$i++;
				}
			}
			
			$cursor_y++;						
		}
		
	}
	
	return $out;
}


function make_filter_i5 () {
	$filtro=file_get_contents_utf8(ABSPATH.'/template/filtro-5.html');	
	$years=get_years_options();
	
	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a target="_blank" style="margin-left: 20px;" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';
	} else {		
		$excel_button='<button style="margin-left: 20px; margin-bottom:5px;" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Excel</a>';
	}
	
	$filtro=str_replace('{EXCEL_URL}'	,	$excel_button						,	$filtro);	
	$filtro=str_replace('{WK}'		,	render_listar_obras_i5()				,	$filtro);	
	
	return $filtro;
}


function render_listar_obras_i5 () {
	$data=get_db_data(array('listar-obras-i4'));
	
	$current_value=cG('wk');
	
	$options='';
	
	/*
	if ($current_value=='activas')
	{
		$options='<option selected="selected" value="activas">Obras activas</option>';		
	}
	else
	{
		$options='<option value="activas">Obras activas</option>';
	}
	*/
	
	$options.='<option value="">Seleccione una obras</option>';
	
	foreach ($data as $d) {
		if ($current_value==$d['CODIGOPROYECTO']) {$selected=' selected="selected" ';} else {$selected='';}
		
		$options.='<option '.$selected.' value="'.$d['CODIGOPROYECTO'].'">'.$d['CODIGOPROYECTO'].' - '.ucfirst($d['PROYECTO']).'</option>';
	}
	
	return $options;
}
















?>