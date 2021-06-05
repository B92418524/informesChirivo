<?php
function make_filter4() {
	$filtro=file_get_contents_utf8(ABSPATH.'/template/filtro-4.html');
	
	$years=get_years_options();
	
	if ($_SERVER['QUERY_STRING']!='') {
		$tmp='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$url=explode('?',$tmp);
		
		$excel_url=$url['0'].'?'.$_SERVER['QUERY_STRING']."&excel=true";
		$excel_button='<a target="_blank" style="margin-left: 20px; margin-bottom:5px;" href="'.$excel_url.'" class="btn btn-default btn-sm">Excel</a>';
	} else {		
		$excel_button='<button style="margin-left: 20px; margin-bottom:5px;" onclick="alert(\'Realice una busqueda primero!\'); return false;" class="btn btn-default btn-sm">Excel</a>';
	}
	
	$filtro=str_replace('{EXCEL_URL}'	,	$excel_button						,	$filtro);
	
	//echo '<pre>---'.cG('start').'---</pre>';
	
	$filtro=str_replace('{IS_DELETED}'	,	render_is_deleted()					,	$filtro);
	
	$filtro=str_replace('{DF_START}'	,	cG('start')							,	$filtro);
	
	$filtro=str_replace('{DF_END}'		,	cG('end')							,	$filtro);
	
	$filtro=str_replace('{WK}'		,	render_listar_obras_i4()				,	$filtro);
	
	$filtro=str_replace('{CL}'		,	render_listar_clientes_i4()				,	$filtro);
	
	$filtro=str_replace('{JO}'		,	render_listar_jefes_de_obra_i4()		,	$filtro);
	
	$filtro=str_replace('{EN}'		,	render_listar_encargados_i4()			,	$filtro);
	
	
	$filtro=str_replace('YEARS',$years,$filtro);
	
	
	return $filtro;
}

function render_tabla_i4_d1() {
	$current_year	=	date('Y');
	$current_month	=	date('n');	
	$current_day	=	date('d');
	
	if (cG('period')!='') {
		$p=explode('-',cG('period'));
		
		$i_mes=str_replace('-','',$p[0]);
		$i_year=str_replace('-','',$p[1]);
		
		$date_period = strtotime($i_year.'-'.$i_mes.'-15 -1 month');	
		
		$date_period = strtotime ( '+1 month' , $date_period ) ;	
	} else {	
		$date_period = strtotime($current_year.'-'.$current_month.'-15 -1 month');
	}
	
	$period_year	=	date('Y',$date_period);
	$period_month	=	date('n',$date_period);
	$period_day		=	date('d',$date_period);
	
	$gi=get_db_data(array('listar-i3-por-fecha',$period_month,$period_year));
	
	//echo '<pre>-'.print_r($gi,true).'-</pre>'."\n";
	
	//echo '<pre>'.print_r(is_array($gi),true).'</pre>';

	if (!is_array($gi))	{
		return '<tr><td colspan="10" style="padding:30px; text-align:center; font-size:20px;">No hay datos disponibles para el periodo indicado</td></tr>';
	}
	else {
		//echo '<pre>'.print_r($gi,true).'</pre>';
		
		$tr='';
		$n=1;
		foreach($gi as $g) {
			//echo '<pre>'.print_r($g,true).'</pre>';
			
			$JEFE_OBRA=$g['ID_JEFE_OBRA'].' - '.$g['NOMBRE_JEFE_OBRA'];
			$OBRA=$g['ID_OBRA'].' - '.$g['NOMBRE_OBRA'];
			
			
			$style='';
			
			$ESTADO='';
			if ($g['ESTADO']=='' and $g['ENVIADO']=='')	{
				$ESTADO='Pendiente envio';
			} elseif ($g['ESTADO']=='ENVIADO' and $g['DATA']!='' ) {
				$ESTADO='COMPLETADO PARCIALMENTE';
				
				$style=' style="color:blue; " ';
			} else {
				$ESTADO=$g['ESTADO'];
			}
			
			if ($ESTADO=='PROCESADO') {$style=' style="color:green; " ';}
			
			$LINK='<a class="btn btn-primary btn-xs" href="http://46.25.213.2/informes/informe-3.php?id1='.$g['ID_INFORME'].'&id2='.$g['ID_JEFE_OBRA'].'&admin=true" target="_blank">Ver</a>';
			
			$tr.='<tr '.$style.' ><td>'.$n.'</td><td style="font-size:8px;">'.$g['ID_INFORME'].'</td><td>'.$JEFE_OBRA.'</td><td>'.$g['EMAIL'].'</td><td>'.$OBRA.'</td><td style="text-align:center;">'.$g['MES'].'</td><td style="text-align:center;">'.$g['YEAR'].'</td><td style="text-align:center;">'.date_normalizer($g['FECHA_CEACION_REGISTRO']).'</td><td style="text-align:center;">'.$ESTADO.'</td><td style="text-align:center;">'.$LINK.'</td></tr>';
			$n++;
		}
		
		return $tr;
	}
	
}

function listar_array_de_periodos_i4_old ($mes_inicio,$year_inicio,$mes_fin,$year_fin) {
	$p=array();
	
	$cm=$mes_inicio;
	
	$i=1;
	if ($year_inicio==$year_fin) {
		while ($cm<=$mes_fin) {
			$p[$i]['m']=$cm;
			$p[$i]['y']=$year_inicio;
			$i++;
			$cm++;
		}
	}
	
	return $p;
}


function listar_array_de_periodos_i4($mes_inicio,$year_inicio,$mes_fin,$year_fin) {
	$p=array();
	
	$cm=$mes_inicio;
	
	$i=1;
	if ($year_inicio==$year_fin) {
		while ($cm<=$mes_fin) {
			$p[$i]['m']=$cm;
			$p[$i]['y']=$year_inicio;
			$i++;
			$cm++;
		}
	} elseif($year_inicio<$year_fin) {
		
		while ($cm<=12)	{
			$p[$i]['m']=$cm;
			$p[$i]['y']=$year_inicio;
			$i++;
			$cm++;
		}
		
		$current_loop_year=$year_inicio+1;
		
		while ($current_loop_year<$year_fin) {
			$pl=1;
			
			while ($pl<=12)	{
				$p[$i]['m']=$pl;
				$p[$i]['y']=$current_loop_year;
				$i++;
				$pl++;
			}
			
			$current_loop_year++;
		}		
		
		$pl=1;
		
		while ($pl<=$mes_fin) {
			$p[$i]['m']=$pl;
			$p[$i]['y']=$year_fin;
			$i++;
			$pl++;
		}
	}
	
	return $p;
}


function make_filter_i4_d1 () {
	$mes_inicio=2;	
	$year_inicio=2015;
	
	$mes_fin=date('m')+0;
	$year_fin=date('Y')+0;
	
	$periods=listar_array_de_periodos_i4($mes_inicio,$year_inicio,$mes_fin-1,$year_fin);
	
	$meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	$options='';
	
	$last=count($periods);
	
	$n=1;
	foreach (array_reverse($periods) as $period) {
		$sel='';
		
		if (cG('period')!='') {
			if ($period['m'].'-'.$period['y']==cG('period')) {
				$sel=' selected="selected" ';
			}
		} else {
			if ($n==1) {
				$sel=' selected="selected" ';
			}
		}
		
		$options.='<option '.$sel.' value="'.$period['m'].'-'.$period['y'].'">'.$meses[$period['m']-1].' - '.$period['y'].'</option>';
		$n++;	
	}
	
	$out=
		'<select class="form-control" name="period" id="period">		
		'.$options.
		'</select>';
	
	
	/*
	http://stackoverflow.com/questions/3535082/return-array-of-months-between-and-inclusive-of-start-and-end-dates
		
	$start = strtotime('2015-04-20');
	$end = date('Y-m-d');
	$month = $start;
	$months[] = date('m-Y', $start);
	while($month <= $end) 
	{
		$month = strtotime("+1 month", $month);
		
		$months[] = date('m-Y', $month);
	}
	*/
	
	return $out;
	
	//return '<pre>'.$mes_inicio.'-'.$year_inicio.'-'.$mes_fin.'-'.$year_fin.'</pre>';
	
	//return '<pre>'.print_r($periods,true).'</pre>';
}

function obtener_id_informe_mes_anterior ($codigo,$periodo) {
	$total_pendiente_de_cobro=0;
	
	$e_perido=explode('-',$periodo);
	
	$m=$e_perido['0'];
	$y=$e_perido['1'];
	
	$pre_date=date_create($y.'-'.$m.'-01 -1 months');
	
	
	$pre_m=date_format($pre_date, "m")+0;
	$pre_y=date_format($pre_date, "Y")+0;
	
	
	// Obtenemos el periodo anterior al indicado
	$d_aux_query="WHERE ID_OBRA='".$codigo."' AND MES='".$pre_m."' AND YEAR='".$pre_y."' ";
	
	$d_aux=get_db_data(array('obtener_informe_por_id_obra_y_periodo_i4',$d_aux_query));
	
	//echo '<pre>Consulta -> '.get_db_query(array('obtener_informe_por_id_obra_y_periodo_i4',$d_aux_query)).'</pre>';
	
	$id_informe=$d_aux['0']['ID_INFORME'];
	
	return $id_informe;
	
}

function obtener_total_pendiente_cobro_de_un_perido_anterior_al_indicado ($codigo,$periodo) {
	$total_pendiente_de_cobro='';
	
	$id_informe=obtener_id_informe_mes_anterior($codigo,$periodo);
	
	if ($id_informe!='') {
		//echo 'Id informe -> '.$id_informe;
		$last_inf_arr=make_i3_array($id_informe);
		
		$total_pendiente_de_cobro=
			fix_sum(
				array(
					$last_inf_arr[0]['PF_CIERRE'],
					$last_inf_arr[0]['PF_FALTA_CONTRATO'],
					$last_inf_arr[0]['PF_APROBACION'],
					$last_inf_arr[0]['PF_OTROS']
					)
				);
	}	
	
	return $total_pendiente_de_cobro;
	
}

function obtener_total_pago_anticipado_realizado_de_un_perido_anterior_al_indicado ($codigo,$periodo) {
	$total_pago_pendiente=0;
	
	$id_informe=obtener_id_informe_mes_anterior($codigo,$periodo);
	
	
	if ($id_informe!='') {
		$last_inf_arr=make_i3_array($id_informe);
		
		if (isset($last_inf_arr[0]['B'])) {
			if (is_array($last_inf_arr[0]['B'])) {
				//foreach ($last_inf_arr[0]['B'] as $a)
				//{
				$n=count($last_inf_arr[0]['B']);
				
				//echo '<pre>';
				
				for ($i = 1; $i <= $n; $i++) {
					$total_pago_pendiente=fix_sum
						(
							array(
								$total_pago_pendiente,
								$last_inf_arr[0]['B'][$i]['IMPORTE_ACOPIO_ENTREGA_'.$i]
								)
							);
					//$last_inf_arr[0]['B'][$i]['IMPORTE_P_CERTIFICACION_'.$i].'\n';
				}
				
				//echo 'Cuenta '.count($last_inf_arr[0]['B']).'\n'.print_r($last_inf_arr[0]['B'],true).'</pre>';
				//$total_pago_anticipado=fix_sum($total_pago_anticipado,$a);			
				//}
			}
		}
	}	
	
	return $total_pago_pendiente;
}

function obtener_total_pendiente_pago_de_un_perido_anterior_al_indicado ($codigo,$periodo) {
	$total_pago_anticipado=0;
	
	$id_informe=obtener_id_informe_mes_anterior($codigo,$periodo);

	if ($id_informe!='') {
		$last_inf_arr=make_i3_array($id_informe);
		
		if (isset($last_inf_arr[0]['A'])) {
			if (is_array($last_inf_arr[0]['A'])) {
				//foreach ($last_inf_arr[0]['A'] as $a)
				//{
				$n=count($last_inf_arr[0]['A']);
				
				//echo '<pre>';
				
				for ($i = 1; $i <= $n; $i++) {
					$total_pago_anticipado=fix_sum
						(
							array(
								$total_pago_anticipado,
								$last_inf_arr[0]['A'][$i]['IMPORTE_P_CERTIFICACION_'.$i]
								)
							);
					//$last_inf_arr[0]['A'][$i]['IMPORTE_P_CERTIFICACION_'.$i].'\n';
				}
				
				//echo 'Cuenta '.count($last_inf_arr[0]['A']).'\n'.print_r($last_inf_arr[0]['A'],true).'</pre>';
				//$total_pago_anticipado=fix_sum($total_pago_anticipado,$a);			
				//}
			}
		}
	}	
	
	return $total_pago_anticipado;
}

function consulta_principal_i4 ($cliente,$obra,$jefe_obra,$encargado) {
	$wq_1='WHERE '; $wq_2='WHERE '; $wq_3='WHERE '; 
	
	$df_1=true;	
	
	if (isset($cliente)) {
		if ($cliente!='') {
			$wq_1.="AND D_CODIGO_CLIENTE='".$cliente."' ";
			$df_1=false;
		}
	}
	
	if (isset($obra)) {
		if ($obra!='') {
			if ($obra=='activas') {
				$wq_1.="AND ACTIVO='1' ";
				$df_1=false;
			} else {
				$wq_1.="AND D_CODIGO_PROYECTO='".$obra."' ";
				$df_1=false;
			}
		}
	}
	
	if (isset($jefe_obra)) {
		if ($jefe_obra!='') {
			$wq_1.="AND D_ID_JEFE_OBRA='".$jefe_obra."' ";
			$df_1=false;
		}
	}
	
	/*
	if (isset($encargado))
	{
		if ($encargado!='')
		{
			$wq_1.="AND XXXXX='".$encargado."' ";
			$df_1=false;
		}
	}
	*/
	
	$wq_1=str_replace('WHERE AND', 'WHERE', $wq_1);
	$wq_2=str_replace('WHERE AND', 'WHERE', $wq_2);
	$wq_3=str_replace('WHERE AND', 'WHERE', $wq_3);
	
	if ($df_1) {
		$wq_1='';
		$wq_2='';
		$wq_3='';
	}
	
	//echo get_db_query(array('listar-obras-i4-filtrado',$wq_1));
	
	//echo '<pre>'.$wq_1.'</pre>';
	//echo '<pre>Cliente: ['.$cliente.']</pre>';
	
	return $wq_1;
}

function fila_con_datos_ocultos($data,$d,$n_i) {
	if (isset($data[$n_i]['D_CODIGO_PROYECTO'])) {
		if ($d['D_CODIGO_PROYECTO']==$data[$n_i]['D_CODIGO_PROYECTO']) {
			return true;	
		}
	}
	
	if (isset($data[$n_i-2]['D_CODIGO_PROYECTO'])) {
		if ($d['D_CODIGO_PROYECTO']==$data[$n_i-2]['D_CODIGO_PROYECTO']) {
			return true;	
		}
	}
	
	return false;
}

function detectar_inicio_de_suma($data,$d,$n_i) {
	if (isset($data[$n_i]['D_CODIGO_PROYECTO'])) {
		if ($d['D_CODIGO_PROYECTO']==$data[$n_i]['D_CODIGO_PROYECTO']) {
			return true;	
		}
	}
	
	return false;
}

function detectar_inicio_de_bloque($data,$d,$n_i) {
	if (isset($data[$n_i-2]['D_CODIGO_PROYECTO'])) {
		if ($d['D_CODIGO_PROYECTO']!=$data[$n_i-2]['D_CODIGO_PROYECTO']) {
			return true;	
		}
	}
	
	return false;
}

function detectar_fin_de_suma($data,$d,$n_i) {
	if (isset($data[$n_i-2]['D_CODIGO_PROYECTO'])) {
		if ($d['D_CODIGO_PROYECTO']==$data[$n_i-2]['D_CODIGO_PROYECTO']) {
			if (isset($data[$n_i]['D_CODIGO_PROYECTO'])) {
				if ($d['D_CODIGO_PROYECTO']!=$data[$n_i]['D_CODIGO_PROYECTO']) {
					return true;	
				}
			}	
		}
	}
	
	return false;
}

function obtener_facturacion_origen_por_cliente_y_perido_i4 ($codigo,$codigo_cliente,$p) {
	
	$d_aux_0a_query="WHERE codigoempresa='1' AND codigoproyecto='".$codigo."' AND codigocliente='".$codigo_cliente."' AND mes='".$p['m']."' AND ejercicio='".$p['y']."'";
	$d_aux_0a=get_db_data(array('facturacion-origen-por-cliente-y-perido-i4',$d_aux_0a_query));
	
	return $d_aux_0a['0']['importeorigen'];
}

function obtener_facturacion_mes($codigo,$codigo_cliente,$p,$facturacion_mes) {
	$d_aux_1_query="WHERE codigoempresa='1' AND codigoproyecto='".$codigo."' AND codigocliente='".$codigo_cliente."' AND mes='".$p['m']."' AND ejercicio='".$p['y']."'";
	$d_aux_1=get_db_data(array('facturacion-mes-i4',$d_aux_1_query));
	
	$facturacion_mes=$facturacion_mes+$d_aux_1['0']['importemes'];
	
	return $facturacion_mes;
}

function obtener_gastos_por_id_obra_y_periodo_i4($codigo,$codigo_cliente,$p,$gastos_contabilizados_origen,$gastos_contabilizados_periodo) {		
	$data_query="WHERE CodigoProyecto='".$codigo."' AND Periodo='".$p['m']."' AND Ejercicio='".$p['y']."' ";
	//echo $data_query;
	$data=get_db_data(array('obtener_gastos_por_id_obra_y_periodo_i4',$data_query));
	
	//echo '<pre>'.print_r($data,true).'</pre>';
	
	if (is_array($data)) {	
		
		if (isset($data['0']['ImporteOrigen']))
		{
			$data['ImporteOrigen']=intval($data['0']['ImporteOrigen']);
			
			if($data['ImporteOrigen']>0)
			{
				$gastos_contabilizados_origen=$data['0']['ImporteOrigen']; // Mostrara el ultimo periodo que tenga valor numerico
			}
			
		}
		if (isset($data['0']['ImporteMes']))
		{
			$data['ImporteMes']=intval($data['0']['ImporteMes']);
			
			if($data['ImporteMes']>0)
			{
				$gastos_contabilizados_periodo=$gastos_contabilizados_periodo+$data['0']['ImporteMes']; // Mostrara el ultimo periodo que tenga valor numerico
			}
			
		}
		
	}
	
	$out=array('gastos_contabilizados_origen'=>$gastos_contabilizados_origen,'gastos_contabilizados_periodo'=>$gastos_contabilizados_periodo);
	
	return $out;
}

function obtener_id_informe($codigo,$ultimo_periodo)
{
	$d_aux_2_query="WHERE ID_OBRA='".$codigo."' AND MES='".$ultimo_periodo['m']."' AND YEAR='".$ultimo_periodo['y']."' ";
	
	$d_aux_2=get_db_data(array('obtener_informe_por_id_obra_y_periodo_i4',$d_aux_2_query));
	
	//echo "\n".'<!--'.$d_aux_2_query.'-->'."\n";
	
	$id_informe=$d_aux_2['0']['ID_INFORME'];
	
	return $id_informe;
}

function obtener_link_informe($codigo,$ultimo_periodo)
{
	$d_aux_2_query="WHERE ID_OBRA='".$codigo."' AND MES='".$ultimo_periodo['m']."' AND YEAR='".$ultimo_periodo['y']."' ";
	
	$d_aux_2=get_db_data(array('obtener_informe_por_id_obra_y_periodo_i4',$d_aux_2_query));
	
	//echo "\n".'<!--'.$d_aux_2_query.'-->'."\n";
	
	$id_informe_1=$d_aux_2['0']['ID_INFORME'];
	$id_informe_2=$d_aux_2['0']['ID_JEFE_OBRA'];
	
	$link='';
	if ($d_aux_2['0']['ID_INFORME']!='' and $d_aux_2['0']['ID_JEFE_OBRA']!='')
	{		
		$link='informe-3.php?id1='.$id_informe_1.'&id2='.$id_informe_2.'&admin=true';
	}
	
	return $link;
}

function obtener_datos_informe($codigo,$ultimo_periodo,$facturacion_mes = 0,$facturacion_origen = 0,$fecha_inicio = 0,$hide_this = 0,$facturacion_pendiente_desde_cierre_hasta_fin_de_mes = 0,$pendiente_facturacion_por_falta_contrato = 0,$pendiente_facturacion_por_aprovacion_de_partidas = 0,$pendiente_de_facturacion_por_otros_motivos = 0,$total_prevision_de_ingresos = 0,$total_prevision_de_ingresos_periodo = 0,$pendiente_certificacion_subcontratas_proveedores = 0,$acopio_o_entrega_a_cuenta_cotabilizados = 0)
{
	$id_informe = obtener_id_informe($codigo,$ultimo_periodo);
	
	if ($id_informe!='')
	{
		$last_inf_arr=make_i3_array($id_informe);
		
		$total_pendiente_de_cobro=fix_sum(array($last_inf_arr[0]['PF_CIERRE'],$last_inf_arr[0]['PF_FALTA_CONTRATO'],$last_inf_arr[0]['PF_APROBACION'],$last_inf_arr[0]['PF_OTROS']));
		
		if ($hide_this)
		{
			$total_prevision_de_ingresos								=	fix_sum(array($facturacion_origen));			
			$total_prevision_de_ingresos_periodo						=	fix_sum(array($facturacion_mes));	
		}
		else		
		{
			$total_prevision_de_ingresos_periodo						=	fix_sum(array($total_pendiente_de_cobro,$facturacion_mes))-nf(obtener_total_pendiente_cobro_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio));
			$total_prevision_de_ingresos								=	fix_sum(array($facturacion_origen,$last_inf_arr[0]['PF_CIERRE'],$last_inf_arr[0]['PF_FALTA_CONTRATO'],$last_inf_arr[0]['PF_APROBACION'],$last_inf_arr[0]['PF_OTROS']));
		}
		
		/* 1 */ $facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	$last_inf_arr[0]['PF_CIERRE'];		
		/* 2 */ $pendiente_facturacion_por_falta_contrato				=	$last_inf_arr[0]['PF_FALTA_CONTRATO'];
		/* 3 */ $pendiente_facturacion_por_aprovacion_de_partidas		=	$last_inf_arr[0]['PF_APROBACION'];
		/* 4 */ $pendiente_de_facturacion_por_otros_motivos				=	$last_inf_arr[0]['PF_OTROS'];
		if (isset($last_inf_arr[0]['TOTAL_PENDIENTE_DE_PAGO']))
		{		
			/* 6 */ $pendiente_certificacion_subcontratas_proveedores		=	$last_inf_arr[0]['TOTAL_PENDIENTE_DE_PAGO'];
		}		
		if (isset($last_inf_arr[0]['PAGO_ANTICIPADO_REALIZADO']))
		{
			/* 7 */ $acopio_o_entrega_a_cuenta_cotabilizados				=	$last_inf_arr[0]['PAGO_ANTICIPADO_REALIZADO'];
		}		
		
	}
	else
	{			
		$total_pendiente_de_cobro=fix_sum(array($facturacion_pendiente_desde_cierre_hasta_fin_de_mes,$pendiente_facturacion_por_falta_contrato,$pendiente_facturacion_por_aprovacion_de_partidas,$pendiente_de_facturacion_por_otros_motivos));
	}
	
	
	$out=array
		(
			'total_pendiente_de_cobro'								=>	$total_pendiente_de_cobro,
			'total_prevision_de_ingresos'							=>	$total_prevision_de_ingresos,
			'total_prevision_de_ingresos_periodo'					=>	$total_prevision_de_ingresos_periodo,
			'facturacion_pendiente_desde_cierre_hasta_fin_de_mes'	=>	$facturacion_pendiente_desde_cierre_hasta_fin_de_mes,
			'pendiente_facturacion_por_falta_contrato'				=>	$pendiente_facturacion_por_falta_contrato,
			'pendiente_facturacion_por_aprovacion_de_partidas'		=>	$pendiente_facturacion_por_aprovacion_de_partidas,
			'pendiente_de_facturacion_por_otros_motivos'			=>	$pendiente_de_facturacion_por_otros_motivos,
			'pendiente_certificacion_subcontratas_proveedores'		=>	$pendiente_certificacion_subcontratas_proveedores,
			'acopio_o_entrega_a_cuenta_cotabilizados'				=>	$acopio_o_entrega_a_cuenta_cotabilizados
			);
	
	
	return $out;
	
}

function obtener_total_prevision_gastos_origen($id_informe,$gastos_contabilizados_origen,$codigo,$fecha_inicio)
{
	$inf_arr=make_i3_array($id_informe);
	
	//echo '<pre>'.print_r($last_inf_arr,true).'</pre>';
	// total_prevision_gastos_origen
	$v_tmp_1=0;	if (isset($inf_arr['0']['TOTAL_PENDIENTE_DE_PAGO']))		{$v_tmp_1=$inf_arr['0']['TOTAL_PENDIENTE_DE_PAGO'];}			
	$v_tmp_2=0; if (isset($inf_arr['0']['PAGO_ANTICIPADO_REALIZADO']))		{$v_tmp_2=$inf_arr['0']['PAGO_ANTICIPADO_REALIZADO'];}
	
	
	$total_prevision_gastos_origen = $gastos_contabilizados_origen + $v_tmp_1 -	$v_tmp_2;
	
	/** Según indicaciones de jesus, el total de previsión de facturación a origen y el total previsión de gastos a origen y no hay que restar el mes anterior y en el periodo si se resta el mes anterior
	$total_prevision_gastos_origen = $gastos_contabilizados_origen + $v_tmp_1 -	$v_tmp_2 - obtener_total_pendiente_pago_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio) + obtener_total_pago_anticipado_realizado_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio);
	*/
	
	return $total_prevision_gastos_origen;
	
}

function obtener_total_prevision_gastos_periodo($id_informe,$gastos_contabilizados_periodo,$codigo,$fecha_inicio)
{
	$inf_arr=make_i3_array($id_informe);
	
	// total_prevision_gastos_periodo
	$v_tmp_1=0;	if (isset($inf_arr['0']['TOTAL_PENDIENTE_DE_PAGO']))		{$v_tmp_1=$inf_arr['0']['TOTAL_PENDIENTE_DE_PAGO'];}			
	$v_tmp_2=0; if (isset($inf_arr['0']['PAGO_ANTICIPADO_REALIZADO']))		{$v_tmp_2=$inf_arr['0']['PAGO_ANTICIPADO_REALIZADO'];}
	$total_prevision_gastos_periodo	= $gastos_contabilizados_periodo + $v_tmp_1	- $v_tmp_2 - obtener_total_pendiente_pago_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio) + obtener_total_pago_anticipado_realizado_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio);	
	
	
	return $total_prevision_gastos_periodo;

}

function detectar_one_id($data,$d,$n_i)
{
	
	foreach ($data as $d)
	{
		if (!isset($last_id)){$last_id=$d['D_CODIGO_PROYECTO'];}
		
		if ($last_id!=$d['D_CODIGO_PROYECTO'])
		{
			$last_id==$d['D_CODIGO_PROYECTO'];
			return false;	
		}		
	}
	
	if ($n_i==count($data) and $n_i>1)
	{
		return true;
	}
	return false;
}


function detectar_sumatorio_final($data,$d,$n_i)
{
	$count=count($data);
	
	if ($n_i==count($data))
	{		
		if($data[$count-1]['D_CODIGO_PROYECTO']==$data[$count-2]['D_CODIGO_PROYECTO'])
		{
			return true;
		}		
	}
	
	return false;
}


?>