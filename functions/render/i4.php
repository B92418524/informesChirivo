<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once('i4_aux/i4_functions.php');

function render_tabla_i4() {
	/** PARAMETROS DE ENTRADA ******************************************************************************************* */
	
	$DEBUG_VALORES_SUMADOS=array();
	
	$fecha_inicio		=	cG('start');
	$fecha_fin			=	cG('end');
	
	if ($fecha_inicio=='' or $fecha_fin=='') {
		return '<tr><td colspan="24" style="text-align:center;">Debe indicar un periodo</td></tr>';
	}
	
	$cliente			=	cG('cl');
	$obra				=	cG('wk');
	$jefe_obra			=	cG('jo');
	$encargado			=	cG('en');

	/** Calculos de fecha */
	$fecha_inicio_arr	=	explode('-',$fecha_inicio);
	$fecha_fin_arr		=	explode('-',$fecha_fin);	
	
	$mes_inicio			=	$fecha_inicio_arr['0']	+0;
	$year_inicio		=	$fecha_inicio_arr['1']	+0;
	
	$mes_fin			=	$fecha_fin_arr['0']		+0;
	$year_fin			=	$fecha_fin_arr['1']		+0;
	
	// Calculamos los peridos comprendidos entre las dos fechas para iterar posteriormente
	$periodos_array		=	listar_array_de_periodos_i4($mes_inicio,$year_inicio,$mes_fin,$year_fin);	
	/** Fin calculos de fecha */
	
	$company_id			=	$_SESSION['company_id'];
	
	
	/** INICIALIZACION DE VARIABLES */
	
	$last_codigo=''; $last_fecha_inicio='';	$codigo='';	$nombre_obra=''; $vigor=''; $importe_contrato=0; $facturacion_origen=0; $venta_pendiente=0; $facturacion_mes=0; $facturacion_pendiente_desde_cierre_hasta_fin_de_mes=0; $pendiente_facturacion_por_falta_contrato=0; $pendiente_facturacion_por_aprovacion_de_partidas=0; $pendiente_de_facturacion_por_otros_motivos=0; $total_pendiente_de_cobro=0; $total_prevision_de_ingresos=0; $total_prevision_de_ingresos_periodo=0; $gastos_contabilizados_origen=0;	$gastos_contabilizados_periodo=0; $pendiente_certificacion_subcontratas_proveedores=0; $acopio_o_entrega_a_cuenta_cotabilizados=0; $total_prevision_gastos_origen=0; $total_prevision_gastos_periodo='0'; $beneficio_periodo=0;	$beneficio_origen=0; $beneficio_periodo_p=0; $beneficio_origen_p=0;	$_SP_codigo='';	$_SP_codigo_m=''; $_SP_nombre_obra=''; $_SP_cliente='';	$_SP_vigor=''; $_SP_importe_contrato=0;	$_SP_facturacion_origen=0; $_SP_venta_pendiente=0; $_SP_facturacion_mes=0; $_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes='';	$_SP_pendiente_facturacion_por_falta_contrato=''; $_SP_pendiente_facturacion_por_aprovacion_de_partidas='';	$_SP_pendiente_de_facturacion_por_otros_motivos='';	$_SP_total_pendiente_de_cobro=''; $_SP_total_prevision_de_ingresos=0; $_SP_total_prevision_de_ingresos_periodo=0; $_SP_gastos_contabilizados_origen='';	$_SP_gastos_contabilizados_periodo=''; $_SP_pendiente_certificacion_subcontratas_proveedores=''; $_SP_acopio_o_entrega_a_cuenta_cotabilizados=''; $_SP_total_prevision_gastos_origen=''; $_SP_total_prevision_gastos_periodo=''; $_SP_beneficio_periodo='';	$_SP_beneficio_origen=''; $_SP_beneficio_periodo_p=''; $_SP_beneficio_origen_p='';
	
	
	$_SUMA_TOTAL_importe_contrato=0; $_SUMA_TOTAL_facturacion_origen=0; $_SUMA_TOTAL_venta_pendiente=0; $_SUMA_TOTAL_facturacion_mes=0; $_SUMA_TOTAL_facturacion_pendiente_desde_cierre_hasta_fin_de_mes=0; $_SUMA_TOTAL_pendiente_facturacion_por_falta_contrato=0; $_SUMA_TOTAL_pendiente_facturacion_por_aprovacion_de_partidas=0; $_SUMA_TOTAL_pendiente_de_facturacion_por_otros_motivos=0; $_SUMA_TOTAL_total_pendiente_de_cobro=0; $_SUMA_TOTAL_total_prevision_de_ingresos=0; $_SUMA_TOTAL_total_prevision_de_ingresos_periodo=0; $_SUMA_TOTAL_gastos_contabilizados_origen=0; $_SUMA_TOTAL_gastos_contabilizados_periodo=0; $_SUMA_TOTAL_pendiente_certificacion_subcontratas_proveedores=0; $_SUMA_TOTAL_acopio_o_entrega_a_cuenta_cotabilizados=0; $_SUMA_TOTAL_total_prevision_gastos_origen=0; $_SUMA_TOTAL_total_prevision_gastos_periodo=0; $_SUMA_TOTAL_beneficio_origen=0; $_SUMA_TOTAL_beneficio_periodo=0; $_SUMA_TOTAL_beneficio_origen_p=0; $_SUMA_TOTAL_beneficio_periodo_p=0; 
	
	
	/** CONSULTA BUCLE PRINCIPAL ******************************************************************************************* */
	
	$main_query=consulta_principal_i4($cliente,$obra,$jefe_obra,$encargado);
	
	//echo '<pre>'.print_r($main_query,true).'</pre>';
	
	$data=get_db_data(array('listar-obras-i4-filtrado',$main_query));
	
	//echo '<pre>'.print_r($data,true).'</pre>';
	if (!is_array($data)) {
		return '<tr><td colspan="22" style="text-align:center;">No hay registros</td></tr>';
	}
	
	
	
	/** BUCLE PRINCIPAL ******************************************************************************************* */
	
	$trs=''; // filas de la tabla
	
	//$i=1;	
	
	$n_e=count($data); // numero de resgistros en el bucle principal
	$n_i=1; // Se ultiliza para referenciar el proximo y anterior elemento del bucle principal
	
	$separator=';';	$csv='sep=;'."\n";
	
	$csv.='"Código";"Obra";"Cliente";"Vigor";"Importe contrato";"Facturación a origen";"Venta pendiente";"Facturación periodo";"Facturación pendiente desde de cierre hasta fin de perido";"Pendiente de facturación por falta de contrato";"Pendiente de facturación por aprovación de partidas";"Pendiente de facturación por otros motivos";"Total pendiente de facturación";"Total previsión de facturación origen";"Total previsión de facturación perido";"Gastos contabilizados origen";"Gastos contabilizados perido";"Pendiente de certificación a subcontratas o proveedores";"Acopio o entrega a cuenta cotabilizados";"Total previsión de gastos origen";"Total previsión de gastos periodo";"Beneficio origen";"Beneficio perido";"Beneficio % origen";"Beneficio % periodo";'."\n";
	
	foreach ($data as $d) {
		
		//echo '<pre>'.print_r($d['D_CODIGO_PROYECTO'],true).'</pre>';
		
		// Decidimos que registros contienen datos que no se muestran
		$hide_this				=	fila_con_datos_ocultos		($data,$d,$n_i);
		$group_start			=	detectar_inicio_de_suma		($data,$d,$n_i);
		$group_end				=	detectar_fin_de_suma		($data,$d,$n_i);
		$block_start			=	detectar_inicio_de_bloque	($data,$d,$n_i);
		$only_one_id_and_last	=	detectar_one_id				($data,$d,$n_i);
		$group_at_last			=	detectar_sumatorio_final	($data,$d,$n_i);
		
		/** Inicializacion de variables */
		$codigo=''; $nombre_obra=''; $cliente=''; $vigor=''; $importe_contrato=0; $facturacion_origen=0; $venta_pendiente=0; $facturacion_mes=0; $facturacion_pendiente_desde_cierre_hasta_fin_de_mes=0; $pendiente_facturacion_por_falta_contrato=0; $pendiente_facturacion_por_aprovacion_de_partidas=0; $pendiente_de_facturacion_por_otros_motivos=0; $total_pendiente_de_cobro=0; $total_prevision_de_ingresos=0; $total_prevision_de_ingresos_periodo=0; $gastos_contabilizados_origen=0;	$gastos_contabilizados_periodo=0; $pendiente_certificacion_subcontratas_proveedores=0; $acopio_o_entrega_a_cuenta_cotabilizados=0; $total_prevision_gastos_origen=0; $total_prevision_gastos_periodo=0;	$beneficio_periodo=0; $beneficio_origen=0; $beneficio_periodo_p=0; $beneficio_origen_p=0; $options=''; 
		
		foreach ($periodos_array as $p)	{
			$codigo												=		$d['D_CODIGO_PROYECTO'];
			$nombre_obra										=		$d['D_PROYECTO'];
			$cliente											=		$d['D_RAZON_SOCIAL_CLIENTE'];
			$vigor												=		'Si';
			$importe_contrato									=		$d['D_IMPORTE_CONTRATO'];
			$codigo_cliente										=		$d['D_CODIGO_CLIENTE'];				
			$facturacion_origen									=		obtener_facturacion_origen_por_cliente_y_perido_i4($codigo,$codigo_cliente,$p);
			$venta_pendiente									=		$d['D_IMPORTE_CONTRATO']-$facturacion_origen;			
			$facturacion_mes									=		obtener_facturacion_mes($codigo,$codigo_cliente,$p,$facturacion_mes);			
			
			$gastos_arr											=		obtener_gastos_por_id_obra_y_periodo_i4($codigo,$codigo_cliente,$p,$gastos_contabilizados_origen,$gastos_contabilizados_periodo);				
			$gastos_contabilizados_origen						=		$gastos_arr['gastos_contabilizados_origen'];
			$gastos_contabilizados_periodo						=		$gastos_arr['gastos_contabilizados_periodo'];
			
			$ultimo_periodo										=		$p;	
		}
		
		$id_informe		=	obtener_id_informe($codigo,$ultimo_periodo);
		$link_informe	=	obtener_link_informe($codigo,$ultimo_periodo);
		
		if ($id_informe!='')	{$style_a1=' style="color:green" ';} else {$style_a1=' style="color:red" ';}
		
		$datos_informe_arr										=	obtener_datos_informe($codigo,$ultimo_periodo,$facturacion_mes,$facturacion_origen,$fecha_inicio,$hide_this,$facturacion_pendiente_desde_cierre_hasta_fin_de_mes,$pendiente_facturacion_por_falta_contrato,$pendiente_facturacion_por_aprovacion_de_partidas,$pendiente_de_facturacion_por_otros_motivos,$total_prevision_de_ingresos,$total_prevision_de_ingresos_periodo,$pendiente_certificacion_subcontratas_proveedores,$acopio_o_entrega_a_cuenta_cotabilizados);
		//echo '<pre>'.print_r($datos_informe_arr,true).'</pre>';
		$total_pendiente_de_cobro								=	compatibilidad_coma_punto($datos_informe_arr['total_pendiente_de_cobro']);
		$total_prevision_de_ingresos							=	compatibilidad_coma_punto($datos_informe_arr['total_prevision_de_ingresos']);
		$total_prevision_de_ingresos_periodo					=	compatibilidad_coma_punto($datos_informe_arr['total_prevision_de_ingresos_periodo']);
		$facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	compatibilidad_coma_punto($datos_informe_arr['facturacion_pendiente_desde_cierre_hasta_fin_de_mes']);
		$pendiente_facturacion_por_falta_contrato				=	compatibilidad_coma_punto($datos_informe_arr['pendiente_facturacion_por_falta_contrato']);
		$pendiente_facturacion_por_aprovacion_de_partidas		=	compatibilidad_coma_punto($datos_informe_arr['pendiente_facturacion_por_aprovacion_de_partidas']);
		$pendiente_de_facturacion_por_otros_motivos				=	compatibilidad_coma_punto($datos_informe_arr['pendiente_de_facturacion_por_otros_motivos']);
		$pendiente_certificacion_subcontratas_proveedores		=	compatibilidad_coma_punto($datos_informe_arr['pendiente_certificacion_subcontratas_proveedores']);
		$acopio_o_entrega_a_cuenta_cotabilizados				=	compatibilidad_coma_punto($datos_informe_arr['acopio_o_entrega_a_cuenta_cotabilizados']);
		$total_prevision_gastos_origen							=	compatibilidad_coma_punto(obtener_total_prevision_gastos_origen ($id_informe,$gastos_contabilizados_origen,$codigo,$fecha_inicio));


		$total_prevision_gastos_periodo							=	compatibilidad_coma_punto(obtener_total_prevision_gastos_periodo($id_informe,$gastos_contabilizados_periodo,$codigo,$fecha_inicio));
		$beneficio_origen										=	$total_prevision_de_ingresos			-	$total_prevision_gastos_origen;		
		$beneficio_periodo										=	$total_prevision_de_ingresos_periodo	-	$total_prevision_gastos_periodo;
		
		if ($total_prevision_gastos_origen!=0) {							
			$beneficio_origen_p				= ( ($total_prevision_de_ingresos			/	$total_prevision_gastos_origen	)	-1	)	*	100	;
		} if ($total_prevision_de_ingresos == 0) { //si = 0, debe tener el valor de la columna facturacion a origen
			$total_prevision_de_ingresos = $facturacion_origen;
		} if ($total_prevision_de_ingresos_periodo == 0) { //si = 0, debe tener el valor de la columna facturacion periodo
			$total_prevision_de_ingresos_periodo = $facturacion_mes;
		}

		if ($total_prevision_gastos_periodo!=0) { 
			$beneficio_periodo_p			= ( ($total_prevision_de_ingresos_periodo	/	$total_prevision_gastos_periodo	)	-1	)	*	100	;
		} elseif ($total_prevision_de_ingresos_periodo>0) {
			$beneficio_periodo_p			= 100;			
		}
		
		if ($group_start)
		{}

		if($block_start) {	
			if (cG('excel')!='') {
				$csv.=";;;;;;;;;;;;;;;;;;;;;;;;;\n";
			} else {
				$trs.='<tr style="border:0; height:20px;"><td colspan=24 style="border:0; height:50px; background-color:#fff !important;"></td></tr>';			
			}
		}
		
		//$hide_flag='';
		if ($hide_this)	{
			$_SP_importe_contrato										=	$_SP_importe_contrato											+	$importe_contrato;
			$_SP_facturacion_origen										=	$_SP_facturacion_origen											+	$facturacion_origen;
			$_SP_venta_pendiente										=	$_SP_venta_pendiente											+	$venta_pendiente;	
			$_SP_facturacion_mes										=	$_SP_facturacion_mes											+	$facturacion_mes;
			
			$_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	$facturacion_pendiente_desde_cierre_hasta_fin_de_mes;
			$_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	$facturacion_pendiente_desde_cierre_hasta_fin_de_mes;
			$_SP_pendiente_facturacion_por_falta_contrato				=	$pendiente_facturacion_por_falta_contrato;
			$_SP_pendiente_facturacion_por_aprovacion_de_partidas		=	$pendiente_facturacion_por_aprovacion_de_partidas;
			$_SP_pendiente_de_facturacion_por_otros_motivos				=	$pendiente_de_facturacion_por_otros_motivos;
			
			$_SP_total_pendiente_de_cobro								=	$total_pendiente_de_cobro;
			
			/* posteriormente sumamos pendiente de facturacion*/ $_SP_total_prevision_de_ingresos							=	$_SP_total_prevision_de_ingresos								+	$total_prevision_de_ingresos;
			/* posteriormente sumamos pendiente de facturacion*/ $_SP_total_prevision_de_ingresos_periodo					=	$_SP_total_prevision_de_ingresos_periodo						+	$total_prevision_de_ingresos_periodo;
			
			$_SP_total_prevision_gastos_origen							=	$total_prevision_gastos_origen;
			$_SP_total_prevision_gastos_periodo							=	$total_prevision_gastos_periodo;
						
			$_SP_pendiente_certificacion_subcontratas_proveedores		=	$pendiente_certificacion_subcontratas_proveedores;
			$_SP_acopio_o_entrega_a_cuenta_cotabilizados				=	$acopio_o_entrega_a_cuenta_cotabilizados;
			
			$_SP_gastos_contabilizados_origen							=	$gastos_contabilizados_origen;
			$_SP_gastos_contabilizados_periodo							=	$gastos_contabilizados_periodo;
			
						
			// Campos ocultos en las proyectos agrupados
			$facturacion_pendiente_desde_cierre_hasta_fin_de_mes		=	'';
			$pendiente_facturacion_por_falta_contrato					=	'';
			$pendiente_facturacion_por_aprovacion_de_partidas			=	'';
			$pendiente_de_facturacion_por_otros_motivos					=	'';
			$total_pendiente_de_cobro									=	'';
			$gastos_contabilizados_origen								=	'';
			$gastos_contabilizados_periodo								=	'';
			$pendiente_certificacion_subcontratas_proveedores			=	'';
			$acopio_o_entrega_a_cuenta_cotabilizados					=	'';
			$total_prevision_gastos_origen								=	'';
			$total_prevision_gastos_periodo								=	'';
			$beneficio_origen											=	'';
			$beneficio_periodo											=	'';
			$beneficio_origen_p											=	'';
			$beneficio_periodo_p										=	'';
		} else { // NO OCULTO
			if (cG('excel') =='') {
				
				/* PARA SUMA */
				$facturacion_pendiente_desde_cierre_hasta_fin_de_mes_v	=	$facturacion_pendiente_desde_cierre_hasta_fin_de_mes;
				$pendiente_facturacion_por_falta_contrato_v				=	$pendiente_facturacion_por_falta_contrato;
				$pendiente_facturacion_por_aprovacion_de_partidas_v		=	$pendiente_facturacion_por_aprovacion_de_partidas;
				$pendiente_de_facturacion_por_otros_motivos_v			=	$pendiente_de_facturacion_por_otros_motivos;
				
				$pendiente_certificacion_subcontratas_proveedores_v		=	$pendiente_certificacion_subcontratas_proveedores;
				$acopio_o_entrega_a_cuenta_cotabilizados_v				=	$acopio_o_entrega_a_cuenta_cotabilizados;
				/* */
				
				$facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	'<span '.$style_a1.'>'.aux_money_format_e($facturacion_pendiente_desde_cierre_hasta_fin_de_mes,true).'</span>';
				$pendiente_facturacion_por_falta_contrato				=	'<span '.$style_a1.'>'.aux_money_format_e($pendiente_facturacion_por_falta_contrato,true).'</span>';
				$pendiente_facturacion_por_aprovacion_de_partidas		=	'<span '.$style_a1.'>'.aux_money_format_e($pendiente_facturacion_por_aprovacion_de_partidas,true).'</span>';
				$pendiente_de_facturacion_por_otros_motivos				=	'<span '.$style_a1.'>'.aux_money_format_e($pendiente_de_facturacion_por_otros_motivos,true).'</span>';
				
				$pendiente_certificacion_subcontratas_proveedores		=	'<span '.$style_a1.'>'.aux_money_format_e($pendiente_certificacion_subcontratas_proveedores,true).'</span>';
				$acopio_o_entrega_a_cuenta_cotabilizados				=	'<span '.$style_a1.'>'.aux_money_format_e($acopio_o_entrega_a_cuenta_cotabilizados,true).'</span>';
				//Modificados para que haga la división en el momento, ya que no llegaban los datos antes de hacer la división algunas veces y el origen se mostraba como -100.
				if($total_prevision_gastos_origen>0) $beneficio_origen_p=	(($total_prevision_de_ingresos			/	$total_prevision_gastos_origen	)	-1	)	*	100;
				$beneficio_origen_p										=	round($beneficio_origen_p,2).'&nbsp;%';
				$beneficio_periodo_p									=	round($beneficio_periodo_p,2).'&nbsp;%';
			}
		}
		
		if (cG('excel')!='') {
			$csv.=
				'"'		.trim(	$codigo																		)		.'"'.$separator.''.
				'"'		.trim(	$nombre_obra																)		.'"'.$separator.''.
				'"'		.trim(	$cliente																	)		.'"'.$separator.''.
				'"'		.trim(	$vigor																		)		.'"'.$separator.''.
				''		.trim(	nf_csv($importe_contrato)													)		.''.$separator.''.
				''		.trim(	nf_csv($facturacion_origen)													)		.''.$separator.''.
				''		.trim(	nf_csv($venta_pendiente)													)		.''.$separator.''.
				''		.trim(	nf_csv($facturacion_mes)													)		.''.$separator.''.
				''		.trim(	nf_csv($facturacion_pendiente_desde_cierre_hasta_fin_de_mes)				)		.''.$separator.''.
				''		.trim(	nf_csv($pendiente_facturacion_por_falta_contrato)							)		.''.$separator.''.
				''		.trim(	nf_csv($pendiente_facturacion_por_aprovacion_de_partidas)					)		.''.$separator.''.
				''		.trim(	nf_csv($pendiente_de_facturacion_por_otros_motivos)							)		.''.$separator.''.
				''		.trim(	nf_csv($total_pendiente_de_cobro)											)		.''.$separator.''.
				''		.trim(	nf_csv($total_prevision_de_ingresos)										)		.''.$separator.''.
				''		.trim(	nf_csv($total_prevision_de_ingresos_periodo)								)		.''.$separator.''.
				''		.trim(	nf_csv($gastos_contabilizados_origen)										)		.''.$separator.''.
				''		.trim(	nf_csv($gastos_contabilizados_periodo)										)		.''.$separator.''.
				''		.trim(	nf_csv($pendiente_certificacion_subcontratas_proveedores)					)		.''.$separator.''.
				''		.trim(	nf_csv($acopio_o_entrega_a_cuenta_cotabilizados)							)		.''.$separator.''.
				''		.trim(	nf_csv($total_prevision_gastos_origen)										)		.''.$separator.''.
				''		.trim(	nf_csv($total_prevision_gastos_periodo)										)		.''.$separator.''.
				''		.trim(	nf_csv($beneficio_origen)													)		.''.$separator.''.
				''		.trim(	nf_csv($beneficio_periodo)													)		.''.$separator.''.
				''		.trim(	nf_csv(round($beneficio_origen_p,2))										)		.''.$separator.''.
				''		.trim(	nf_csv(round($beneficio_periodo_p,2))										)		.''.$separator.''.
				
				
				"\n";
		} else {
			
			if (!$hide_this) {
				
				$DEBUG_VALORES_SUMADOS[]=$facturacion_pendiente_desde_cierre_hasta_fin_de_mes_v;
				
				$_SUMA_TOTAL_importe_contrato										=	$_SUMA_TOTAL_importe_contrato											+	$importe_contrato;
				$_SUMA_TOTAL_facturacion_origen										=	$_SUMA_TOTAL_facturacion_origen											+	$facturacion_origen;
				$_SUMA_TOTAL_venta_pendiente										=	$_SUMA_TOTAL_venta_pendiente											+	$venta_pendiente;
				$_SUMA_TOTAL_facturacion_mes										=	$_SUMA_TOTAL_facturacion_mes											+	$facturacion_mes;
				
				$_SUMA_TOTAL_facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	$_SUMA_TOTAL_facturacion_pendiente_desde_cierre_hasta_fin_de_mes		+	$facturacion_pendiente_desde_cierre_hasta_fin_de_mes_v;
				$_SUMA_TOTAL_pendiente_facturacion_por_falta_contrato				=	$_SUMA_TOTAL_pendiente_facturacion_por_falta_contrato					+	$pendiente_facturacion_por_falta_contrato_v;
				$_SUMA_TOTAL_pendiente_facturacion_por_aprovacion_de_partidas		=	$_SUMA_TOTAL_pendiente_facturacion_por_aprovacion_de_partidas			+	$pendiente_facturacion_por_aprovacion_de_partidas_v;
				$_SUMA_TOTAL_pendiente_de_facturacion_por_otros_motivos				=	$_SUMA_TOTAL_pendiente_de_facturacion_por_otros_motivos					+	$pendiente_de_facturacion_por_otros_motivos_v;
				
				$_SUMA_TOTAL_total_pendiente_de_cobro								=	$_SUMA_TOTAL_total_pendiente_de_cobro									+	$total_pendiente_de_cobro;
				$_SUMA_TOTAL_total_prevision_de_ingresos							=	$_SUMA_TOTAL_total_prevision_de_ingresos								+	$total_prevision_de_ingresos;
				$_SUMA_TOTAL_total_prevision_de_ingresos_periodo					=	$_SUMA_TOTAL_total_prevision_de_ingresos_periodo						+	$total_prevision_de_ingresos_periodo;	
				$_SUMA_TOTAL_gastos_contabilizados_origen							=	$_SUMA_TOTAL_gastos_contabilizados_origen								+	$gastos_contabilizados_origen;
				$_SUMA_TOTAL_gastos_contabilizados_periodo							=	$_SUMA_TOTAL_gastos_contabilizados_periodo								+	$gastos_contabilizados_periodo;	
				
				$_SUMA_TOTAL_pendiente_certificacion_subcontratas_proveedores		=	$_SUMA_TOTAL_pendiente_certificacion_subcontratas_proveedores			+	$pendiente_certificacion_subcontratas_proveedores_v;
				$_SUMA_TOTAL_acopio_o_entrega_a_cuenta_cotabilizados				=	$_SUMA_TOTAL_acopio_o_entrega_a_cuenta_cotabilizados					+	$acopio_o_entrega_a_cuenta_cotabilizados_v;
				
				$_SUMA_TOTAL_total_prevision_gastos_origen							=	$_SUMA_TOTAL_total_prevision_gastos_origen								+	$total_prevision_gastos_origen;
				$_SUMA_TOTAL_total_prevision_gastos_periodo							=	$_SUMA_TOTAL_total_prevision_gastos_periodo								+	$total_prevision_gastos_periodo;
				
				$_SUMA_TOTAL_beneficio_origen										=	$_SUMA_TOTAL_beneficio_origen											+	$beneficio_origen;
				$_SUMA_TOTAL_beneficio_periodo										=	$_SUMA_TOTAL_beneficio_periodo											+	$beneficio_periodo;
			}
			
			//if (!$hide_this){$codigo='s '.$codigo;} // SUMAMOS LINEAS INDIVIDUALES
			$beneficio_origen=$total_prevision_de_ingresos-$total_prevision_gastos_origen;
			$trs.=
				'<tr>'.
				'<td><a href="'.$link_informe.'" target="_blank"><span '.$style_a1.' title="'.$id_informe.'">'.$codigo.'</span></a></td>'.
				'<td>'.$nombre_obra.'</td>'.
				'<td>'.$cliente.'</td>'.
				'<td>'.$vigor.'</td>'.
				'<td>'.aux_money_format($importe_contrato).'</td>'.
				'<td>'.aux_money_format($facturacion_origen).'</td>'.
				'<td>'.aux_money_format($venta_pendiente,true).'</td>'.
				'<td>'.aux_money_format($facturacion_mes,true).'</td>'.
				'<td>'.$facturacion_pendiente_desde_cierre_hasta_fin_de_mes.'</td>'.
				'<td>'.$pendiente_facturacion_por_falta_contrato.'</td>'.
				'<td>'.$pendiente_facturacion_por_aprovacion_de_partidas.'</td>'.
				'<td>'.$pendiente_de_facturacion_por_otros_motivos.'</td>'.
				'<td>'.aux_money_format($total_pendiente_de_cobro).'</td>'.
				
				'<td>'.aux_money_format($total_prevision_de_ingresos,true).'</td>'. // equivale a origen
				'<td>'.aux_money_format($total_prevision_de_ingresos_periodo,true).'</td>'. // la diferencia de lo dos origenes es el periodo; se calcula como resta restando el origen de este mes menos el del anterior
				
				'<td>'.aux_money_format($gastos_contabilizados_origen).'</td>'.
				'<td>'.aux_money_format($gastos_contabilizados_periodo).'</td>'.
				
				'<td>'.$pendiente_certificacion_subcontratas_proveedores.'</td>'.
				'<td>'.$acopio_o_entrega_a_cuenta_cotabilizados.'</td>'.
				
				'<td>'.aux_money_format($total_prevision_gastos_origen).'</td>'.
				'<td>'.aux_money_format($total_prevision_gastos_periodo).'</td>'.
				
				'<td>'.aux_money_format($beneficio_origen).'</td>'.
				//'<td>'.aux_money_format($total_prevision_de_ingresos-$total_prevision_gastos_origen).'</td>'.
				'<td>'.aux_money_format($beneficio_periodo).'</td>'.			
				
				'<td>'.$beneficio_origen_p.'</td>'.
				'<td>'.$beneficio_periodo_p.'</td>'.
				'</tr>';			
		}
		
		
		
		if ($group_end or $only_one_id_and_last or $group_at_last) {
			
			/** Según indicaciones de jesus, el total de previsión de facturación a origen y el total previsión de gastos a origen y no hay que restar el mes anterior y en el periodo si se resta el mes anterior.
			/* anteriormente no sumamos pendiente de facturacion ni restamos el del mes anterior al periodo
			$_SP_total_prevision_de_ingresos			
			=	
			$_SP_total_prevision_de_ingresos			
			+
			$_SP_total_pendiente_de_cobro
			-
			nf(obtener_total_pendiente_cobro_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio))
			;
			*/
			$_SP_total_prevision_de_ingresos			
			=	
			$_SP_total_prevision_de_ingresos			
			+
			$_SP_total_pendiente_de_cobro;			
			
			
			/* anteriormente no sumamos pendiente de facturacion*/  
			$_SP_total_prevision_de_ingresos_periodo	
			=	
			$_SP_total_prevision_de_ingresos_periodo	
			+	
			$_SP_total_pendiente_de_cobro
			-
			nf(obtener_total_pendiente_cobro_de_un_perido_anterior_al_indicado($codigo,$fecha_inicio))
			;
			
			
			$_SP_beneficio_origen										=	$_SP_total_prevision_de_ingresos								-	$_SP_total_prevision_gastos_origen;		
			$_SP_beneficio_periodo										=	$_SP_total_prevision_de_ingresos_periodo						-	$_SP_total_prevision_gastos_periodo;
			
			
			if($_SP_total_prevision_gastos_origen!=0) {							
				$_SP_beneficio_origen_p									= ( ($_SP_total_prevision_de_ingresos								/	$_SP_total_prevision_gastos_origen	)	-1	)	*	100	;
			}
			
			if($_SP_total_prevision_gastos_periodo!=0) { 
				$_SP_beneficio_periodo_p								= ( ($_SP_total_prevision_de_ingresos_periodo						/	$_SP_total_prevision_gastos_periodo	)	-1	)	*	100	;

				/*Si el beneficio periodo es negativo su porcentaje también debe serlo,
				por lo que si es positivo le cambiamos el signo, 
				lo mismo si es positivo el beneficio periodo, también el porcentaje debe serlo*/
				if($_SP_beneficio_periodo_p > 0 && $_SP_beneficio_periodo < 0) {
					$_SP_beneficio_periodo_p = $_SP_beneficio_periodo_p * -1;
				} else if($_SP_beneficio_periodo_p < 0 && $_SP_beneficio_periodo > 0) {
					$_SP_beneficio_periodo_p = $_SP_beneficio_periodo_p * -1;
				}
			}
			
			
			// SUMAMOS TOTALIZADORES PARCIALES
			$DEBUG_VALORES_SUMADOS[]=$_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes;
			
			$_SUMA_TOTAL_importe_contrato										=	$_SUMA_TOTAL_importe_contrato											+	$_SP_importe_contrato;
			$_SUMA_TOTAL_facturacion_origen										=	$_SUMA_TOTAL_facturacion_origen											+	$_SP_facturacion_origen;
			$_SUMA_TOTAL_venta_pendiente										=	$_SUMA_TOTAL_venta_pendiente											+	$_SP_venta_pendiente;
			$_SUMA_TOTAL_facturacion_mes										=	$_SUMA_TOTAL_facturacion_mes											+	$_SP_facturacion_mes;
			
			$_SUMA_TOTAL_facturacion_pendiente_desde_cierre_hasta_fin_de_mes	=	$_SUMA_TOTAL_facturacion_pendiente_desde_cierre_hasta_fin_de_mes		+	$_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes;
			$_SUMA_TOTAL_pendiente_facturacion_por_falta_contrato				=	$_SUMA_TOTAL_pendiente_facturacion_por_falta_contrato					+	$_SP_pendiente_facturacion_por_falta_contrato;
			$_SUMA_TOTAL_pendiente_facturacion_por_aprovacion_de_partidas		=	$_SUMA_TOTAL_pendiente_facturacion_por_aprovacion_de_partidas			+	$_SP_pendiente_facturacion_por_aprovacion_de_partidas;
			$_SUMA_TOTAL_pendiente_de_facturacion_por_otros_motivos				=	$_SUMA_TOTAL_pendiente_de_facturacion_por_otros_motivos					+	$_SP_pendiente_de_facturacion_por_otros_motivos;
			
			$_SUMA_TOTAL_total_pendiente_de_cobro								=	$_SUMA_TOTAL_total_pendiente_de_cobro									+	$_SP_total_pendiente_de_cobro;
			$_SUMA_TOTAL_total_prevision_de_ingresos							=	$_SUMA_TOTAL_total_prevision_de_ingresos								+	$_SP_total_prevision_de_ingresos;
			$_SUMA_TOTAL_total_prevision_de_ingresos_periodo					=	$_SUMA_TOTAL_total_prevision_de_ingresos_periodo						+	$_SP_total_prevision_de_ingresos_periodo;	
			$_SUMA_TOTAL_gastos_contabilizados_origen							=	$_SUMA_TOTAL_gastos_contabilizados_origen								+	$_SP_gastos_contabilizados_origen;
			$_SUMA_TOTAL_gastos_contabilizados_periodo							=	$_SUMA_TOTAL_gastos_contabilizados_periodo								+	$_SP_gastos_contabilizados_periodo;	
			$_SUMA_TOTAL_pendiente_certificacion_subcontratas_proveedores		=	$_SUMA_TOTAL_pendiente_certificacion_subcontratas_proveedores			+	$_SP_pendiente_certificacion_subcontratas_proveedores;
			$_SUMA_TOTAL_acopio_o_entrega_a_cuenta_cotabilizados				=	$_SUMA_TOTAL_acopio_o_entrega_a_cuenta_cotabilizados					+	$_SP_acopio_o_entrega_a_cuenta_cotabilizados;
			$_SUMA_TOTAL_total_prevision_gastos_origen							=	$_SUMA_TOTAL_total_prevision_gastos_origen								+	$_SP_total_prevision_gastos_origen;
			$_SUMA_TOTAL_total_prevision_gastos_periodo							=	$_SUMA_TOTAL_total_prevision_gastos_periodo								+	$_SP_total_prevision_gastos_periodo;
									
			$_SUMA_TOTAL_beneficio_origen										=	$_SUMA_TOTAL_beneficio_origen											+	$_SP_beneficio_origen;
			$_SUMA_TOTAL_beneficio_periodo										=	$_SUMA_TOTAL_beneficio_periodo											+	$_SP_beneficio_periodo;
				
			
			if (cG('excel')!='') {
				$csv.=
					'"'		.trim(	$_SP_codigo_m																	)		.'"'.$separator.''.	
					'"'		.trim(	$_SP_nombre_obra																)		.'"'.$separator.''.	
					'"'		.trim(	$_SP_cliente																	)		.'"'.$separator.''.	
					'"'		.trim(	$_SP_vigor																		)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_importe_contrato)													)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_facturacion_origen)													)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_venta_pendiente)													)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_facturacion_mes)													)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes)				)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_pendiente_facturacion_por_falta_contrato)							)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_pendiente_facturacion_por_aprovacion_de_partidas)					)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_pendiente_de_facturacion_por_otros_motivos)							)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_total_pendiente_de_cobro)											)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_total_prevision_de_ingresos)										)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_total_prevision_de_ingresos_periodo)								)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_gastos_contabilizados_origen)										)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_gastos_contabilizados_periodo)										)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_pendiente_certificacion_subcontratas_proveedores)					)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_acopio_o_entrega_a_cuenta_cotabilizados)							)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_total_prevision_gastos_origen)										)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_total_prevision_gastos_periodo)										)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_beneficio_origen)													)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv($_SP_beneficio_periodo)													)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv(round($_SP_beneficio_origen_p,2))										)		.'"'.$separator.''.	
					'"'		.trim(	nf_csv(round($_SP_beneficio_periodo_p,2))										)		.'"'.$separator.''.	
					
					"\n";		
			} else {
				
				$trs.=
					'<tr style="border-top:solid 2px silver; background-color:#eee; font-weight:bold;">'.
					'<td>'.$_SP_codigo_m.'</td>'.
					'<td>'.$_SP_nombre_obra.'</td>'.
					'<td>'.$_SP_cliente.'</td>'.
					'<td>'.$_SP_vigor.'</td>'.
					'<td>'.aux_money_format($_SP_importe_contrato).'</td>'.
					'<td>'.aux_money_format($_SP_facturacion_origen).'</td>'.
					'<td>'.aux_money_format($_SP_venta_pendiente).'</td>'.
					'<td>'.aux_money_format($_SP_facturacion_mes).'</td>'.
					'<td>'.'<span '.$style_a1.'>'.aux_money_format($_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes).'</span>'.'</td>'.
					'<td>'.'<span '.$style_a1.'>'.aux_money_format($_SP_pendiente_facturacion_por_falta_contrato).'</span>'.'</td>'.
					'<td>'.'<span '.$style_a1.'>'.aux_money_format($_SP_pendiente_facturacion_por_aprovacion_de_partidas).'</span>'.'</td>'.
					'<td>'.'<span '.$style_a1.'>'.aux_money_format($_SP_pendiente_de_facturacion_por_otros_motivos).'</span>'.'</td>'.
					'<td>'.aux_money_format($_SP_total_pendiente_de_cobro).'</td>'.
					
					'<td>'.aux_money_format($_SP_total_prevision_de_ingresos).'</td>'. // equivale a origen
					'<td>'.aux_money_format($_SP_total_prevision_de_ingresos_periodo).'</td>'. // la diferencia de lo dos origenes es el periodo; se calcula como resta restando el origen de este mes menos el del anterior
					
					'<td>'.aux_money_format($_SP_gastos_contabilizados_origen).'</td>'.
					'<td>'.aux_money_format($_SP_gastos_contabilizados_periodo).'</td>'.
					
					'<td><span '.$style_a1.'>'.aux_money_format($_SP_pendiente_certificacion_subcontratas_proveedores,true).'</span></td>'.
					'<td><span '.$style_a1.'>'.aux_money_format($_SP_acopio_o_entrega_a_cuenta_cotabilizados,true).'</span></td>'.
					
					'<td>'.aux_money_format($_SP_total_prevision_gastos_origen).'</td>'.
					'<td>'.aux_money_format($_SP_total_prevision_gastos_periodo).'</td>'.
					
					'<td>'.aux_money_format($_SP_beneficio_origen).'</td>'.
					'<td>'.aux_money_format($_SP_beneficio_periodo).'</td>'.			
					
					'<td>'.round($_SP_beneficio_origen_p,2).'&nbsp;%</td>'.
					'<td>'.round($_SP_beneficio_periodo_p,2).'&nbsp;%</td>'.
					'</tr>';				
			}
			
			$_SP_codigo_m='';
			$_SP_nombre_obra='';
			$_SP_cliente='';
			$_SP_vigor='';
			$_SP_importe_contrato='';
			$_SP_facturacion_origen='';
			$_SP_venta_pendiente='';
			$_SP_facturacion_mes='';
			$_SP_facturacion_pendiente_desde_cierre_hasta_fin_de_mes='';
			$_SP_pendiente_facturacion_por_falta_contrato='';
			$_SP_pendiente_facturacion_por_aprovacion_de_partidas='';
			$_SP_pendiente_de_facturacion_por_otros_motivos='';
			$_SP_total_pendiente_de_cobro='';
				
			$_SP_total_prevision_de_ingresos='';
			$_SP_total_prevision_de_ingresos_periodo='';
				
			$_SP_gastos_contabilizados_origen='';
			$_SP_gastos_contabilizados_periodo='';
				
			$_SP_pendiente_certificacion_subcontratas_proveedores='';
			$_SP_acopio_o_entrega_a_cuenta_cotabilizados='';
				
			$_SP_total_prevision_gastos_origen='';
			$_SP_total_prevision_gastos_periodo='';
				
			$_SP_beneficio_origen='';
			$_SP_beneficio_periodo='';			
				
			$_SP_beneficio_origen_p='';
			$_SP_beneficio_periodo_p='';
		}		
		
		$n_i++;
	}
	
	if (cG('excel')!='') {
	} else {
		$trs.='<tr style="border:0; height:20px;"><td colspan=24 style="border:0; height:50px; background-color:#fff !important;"></td></tr>';
		
		
		if($_SUMA_TOTAL_total_prevision_gastos_origen!=0) {							
			$_SUMA_TOTAL_beneficio_origen_p									= ( ($_SUMA_TOTAL_total_prevision_de_ingresos								/	$_SUMA_TOTAL_total_prevision_gastos_origen	)	-1	)	*	100	;
		}
		
		if($_SUMA_TOTAL_total_prevision_gastos_periodo!=0) { 
			$_SUMA_TOTAL_beneficio_periodo_p								= ( ($_SUMA_TOTAL_total_prevision_de_ingresos_periodo						/	$_SUMA_TOTAL_total_prevision_gastos_periodo	)	-1	)	*	100	;

			/*Si el beneficio periodo es negativo su porcentaje también debe serlo,
			por lo que si es positivo le cambiamos el signo, 
			lo mismo si es positivo el beneficio periodo, también el porcentaje debe serlo*/
			if($_SUMA_TOTAL_beneficio_periodo_p > 0 && $_SUMA_TOTAL_beneficio_periodo < 0) {
				$_SUMA_TOTAL_beneficio_periodo_p = $_SUMA_TOTAL_beneficio_periodo_p * -1;
			} else if($_SUMA_TOTAL_beneficio_periodo_p < 0 && $_SUMA_TOTAL_beneficio_periodo > 0) {
				$_SUMA_TOTAL_beneficio_periodo_p = $_SUMA_TOTAL_beneficio_periodo_p * -1;
			}
		}
		
		
		$trs.=
			'<tr style="border-top:solid 5px silver; background-color:#eee; font-weight:bold;">'.
			'<td></td>'.
			'<td></td>'.
			'<td></td>'.
			'<td></td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_importe_contrato).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_facturacion_origen).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_venta_pendiente).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_facturacion_mes).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_facturacion_pendiente_desde_cierre_hasta_fin_de_mes).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_pendiente_facturacion_por_falta_contrato).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_pendiente_facturacion_por_aprovacion_de_partidas).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_pendiente_de_facturacion_por_otros_motivos).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_total_pendiente_de_cobro).'</td>'.
			
			'<td>'.aux_money_format($_SUMA_TOTAL_total_prevision_de_ingresos).'</td>'. // equivale a origen
			'<td>'.aux_money_format($_SUMA_TOTAL_total_prevision_de_ingresos_periodo).'</td>'. // la diferencia de lo dos origenes es el periodo; se calcula como resta restando el origen de este mes menos el del anterior
			
			'<td>'.aux_money_format($_SUMA_TOTAL_gastos_contabilizados_origen).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_gastos_contabilizados_periodo).'</td>'.
			
			'<td>'.aux_money_format($_SUMA_TOTAL_pendiente_certificacion_subcontratas_proveedores,true).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_acopio_o_entrega_a_cuenta_cotabilizados,true).'</td>'.
			
			'<td>'.aux_money_format($_SUMA_TOTAL_total_prevision_gastos_origen).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_total_prevision_gastos_periodo).'</td>'.
			
			'<td>'.aux_money_format($_SUMA_TOTAL_beneficio_origen).'</td>'.
			'<td>'.aux_money_format($_SUMA_TOTAL_beneficio_periodo).'</td>'.			
			
			'<td>'.round($_SUMA_TOTAL_beneficio_origen_p,2).'&nbsp;%</td>'.
			'<td>'.round($_SUMA_TOTAL_beneficio_periodo_p,2).'&nbsp;%</td>'.
			'</tr>';		
	}
	
	$default_query = true;
	$wq_1 = "WHERE CodigoEmpresa=" . "'" . $company_id . "'";
	
	if (cG('excel')!='') {		
		echo $csv;
	} else {
		$table_body=$trs;
		
		echo '<!-- <pre>'.print_r($DEBUG_VALORES_SUMADOS,true).'</pre> -->';
		
		return $table_body;
	}
	
	//$table_body.=$suma;	
}
?>