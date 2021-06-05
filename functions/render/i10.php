<?php

function render_tabla_i10() {
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
	
	//$gi=get_db_data(array('listar-i3-por-fecha',$period_month,$period_year));
	
	$gi = get_db_data(array('listar-i3-por-fecha-y-jefe-obra',$period_month,$period_year,$_SESSION['id_jefe_obra']));
	
	//echo '<pre>-'.print_r($gi,true).'-</pre>'."\n";
	
	//echo '<pre>'.print_r(is_array($gi),true).'</pre>';

	if (!is_array($gi)) {
		return '<tr><td colspan="10" style="padding:30px; text-align:center; font-size:20px;">No hay datos disponibles para el periodo indicado</td></tr>';
	} else {
		//echo '<pre>'.print_r($gi,true).'</pre>';
		
		$tr='';
		$n=1;
		foreach ($gi as $g) {
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
			
			$LINK='<a class="btn btn-primary btn-xs" href="http://95.60.0.190/informes/informe-3.php?id1='.$g['ID_INFORME'].'&id2='.$g['ID_JEFE_OBRA'].'" target="_blank">Ver</a>';
			
			$tr.='<tr '.$style.' ><td>'.$n.'</td><td style="font-size:8px;">'.$g['ID_INFORME'].'</td><td>'.$JEFE_OBRA.'</td><td>'.$g['EMAIL'].'</td><td>'.$OBRA.'</td><td style="text-align:center;">'.$g['MES'].'</td><td style="text-align:center;">'.$g['YEAR'].'</td><td style="text-align:center;">'.date_normalizer($g['FECHA_CEACION_REGISTRO']).'</td><td style="text-align:center;">'.$ESTADO.'</td><td style="text-align:center;">'.$LINK.'</td></tr>';
			$n++;
		}
		
		return $tr;
	}
	
}

?>