<?php
header("Content-Type: text/html; charset=ISO-8859-1");

function sql_date($date,$format='') {
	
	//echo '<pre>--- '.$date.' ---</pre>';
	
	if (strpos($date, '/')===false and strpos($date, '-')===false) { // fecha unida sin separadores formato 14012015
		$e[]=substr($date,0, 2);
		$e[]=substr($date,2, 2);
		$e[]=substr($date,4, 4);
	} elseif (strpos($date, '-')!==false) { // fecha separada por guiones - Asumimos el modelo chrome para el tipo input date con formato 2015-01-14
		$tmp_date=explode('-',$date);
		
		$e[]=$tmp_date['2']; // year
		$e[]=$tmp_date['1']; // 
		$e[]=$tmp_date['0']; // 
	} else { // fecha tradicional con formato 14/01/2015
		$e=explode('/',$date);
	}
	
	if ($format=='') {
		// --> 03/20/2015
		
		$year	=	$e['2'];
		$month	=	$e['0'];
		$day	=	$e['1'];
		
		//$date=$year.'-'.$month.'-'.$day.' 00:00:00.000';
		$date=$year.'-'.$day.'-'.$month.' 00:00:00.000';
	} elseif ($format=='es') {
		// --> 20/03/2015
		
		$day	=	$e['0'];
		$month	=	$e['1'];
		$year	=	$e['2'];
		
		$date=$day.'-'.$month.'-'.$year.' 00:00:00.000';
		//$date=$year.'-'.$month.'-'.$day.' 00:00:00.000';		
		//$date=$year.'-'.$day.'-'.$month.' 00:00:00.000';
		//$date=$year.'-'.$month.'-'.$day.' 00:00:00.000';
	}
	
	return $date;
}

function sql_date_to_store($date) {
	$e=explode('/',$date);
	
	// --> 03/20/2015
	
	$year=$e['2'];
	$month=$e['0'];
	$day=$e['1'];
	
	//$date=$year.'-'.$month.'-'.$day.' 00:00:00.000';
	$date=$year.'-'.$day.'-'.$month.' 00:00:00.000';
	
	return $date;
}

function get_years_options() {
	$years='';
	
	$min=2000;
	$max=date('Y');
	
	$year_selected=cG('y');
	
	for ($i = $max; $i >= $min; $i--) {
		if ($year_selected==$i) {$selected='selected="selected"';} else {$selected='';}
		
		$years.='<option '.$selected.' value="'.$i.'">Año '.$i.'</option>';
	}
	
	return $years;
}

function render_listar_proveedores() {
	$data=get_db_data(array('listar-proveedores'));
	
	$current_value=cG('pr');

	$haySesion = false;
	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
	if (isset($_SESSION['proveedores'])) {
		$haySesion = true;
	}
	
	$options='';
	foreach ($data as $d) {

		$selected = '';
		if ($current_value==$d['codigoproveedor']) { $selected=' selected="selected" '; }
		if ($haySesion && in_array($d['codigoproveedor'], $_SESSION['proveedores'])) { $selected=' selected="selected" '; }

		$options.='<option '.$selected.' value="'.$d['codigoproveedor'].'">'.$d['codigoproveedor'].' - '.$d['Razonsocial'].'</option>';
	}
	
	return $options;
}

function render_listar_estados() {
	$data=get_db_data(array('listar-estados'));
	
	$current_value=cG('status');
	
	$options='';
	$sel = '';
	foreach ($data as $d) {
		if ($current_value==$d['estado']) {$selected=' selected="selected" ';} else {$selected='';}

		if (empty($current_value) && $d['estado'] == 'Efecto Pendiente') {
			$selected=' selected="selected" ';
		}
		
		$options.='<option '.$selected.' value="'.$d['estado'].'">'.ucfirst($d['estado']).'</option>';
	}

	if (!empty($_GET) && $current_value == '') {
		$sel = ' selected="selected" ';
	}

	$options.='<option '.$sel.' value="">Todos los estados</option>';
	
	return $options;
}

function render_listar_estados_efecto() {
	$data=array('Pendientes'=>'Pendientes','Descontados'=>'Descontados','En Cartera'=>'En Cartera','Retenciones'=>'Retenciones','Impagados'=>'Impagados');
	$data_k=array_keys($data);
	
	$current_value=cG('estado_efecto');
	
	$options='';
	$i=0;
	foreach ($data as $d) {
		if ($current_value==$d) {$selected=' selected="selected" ';} else {$selected='';}

		if (empty($current_value) && $d == 'Pendientes') {
			$selected=' selected="selected" ';
		}
		
		$options.='<option '.$selected.' value="'.$d.'">'.$data_k[$i].'</option>';
		$i++;
	}

	if (!empty($_GET) && $current_value == '') {
		$sel = ' selected="selected" ';
	}

	$options.='<option '.$sel.' value="">Todos los estados de efecto</option>';
	
	return $options;
}

function render_listar_proyectos() {
	$data=get_db_data(array('listar-proyectos'));
	
	$current_value=cG('project');
	$haySesion = false;
	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
	if (isset($_SESSION['proyectos'])) {
		$haySesion = true;
	}
	
	$options='';
	$n = 0;
	foreach ($data as $d) {
		if ($d['codigoproyecto']!='') {
			$selected = '';
			if ($current_value==$d['codigoproyecto']) { $selected=' selected="selected" '; }
			if (isset($_SESSION['proyectos'])) {
				if (in_array($d['codigoproyecto'], $_SESSION['proyectos'])) { $selected=' selected="selected" '; }
			}
			// $proy = $d['Proyecto'];
			// $proy = mb_substr(utf8_encode($proy), 0, 490, 'UTF-8');
			// $proy = mb_convert_encoding($proy, 'UTF-8');
			// $proy = utf8_encode(ucfirst($proy));
			$proy = ucfirst($d['Proyecto']);
			$options.='<option '.$selected.' value="'.$d['codigoproyecto'].'">'.$d['codigoproyecto'].' - '.$proy.'</option>';
			$n++;
		}
	}
	
	return $options;
}

function object_to_date ($d,$f='') {
	if ($f=='') {$f='d/m/y';}
	
	if ($d=='')	{
		return false;
	} else {
		$date=date_format($d,$f);
	}
	
	return $date;	
}

function date_normalizer ($d,$f='') { 		
	
	$date=object_to_date($d,$f='');
	
	$replaces = array(
		'January' => 'de enero de',
		'February' => 'de febrero de',
		'March' => 'de marzo de',
		'April' => 'de abril de',
		'May' => 'de mayo de',
		'June' => 'de junio de',
		'July' => 'julio',
		'August' => 'agosto',
		'September' => 'septiembre',
		'October' => 'octubre',
		'November' => 'noviembre',
		'December' => 'diciembre'
		);
	
	$date=str_replace(array_keys($replaces),$replaces,$date);
	
	return $date;
}

function aux_money_format($n,$show_cero='') {
	if ($n=='')	{
		if ($show_cero!='') {
			$n=0;
		} else {
			return '';
		}		
	}
		
	$n=number_format((float)$n, 2, ',', '.').'&nbsp;&euro;';
	
	return $n;
}

function aux_money_format_noeuro ($n,$show_cero='') {
	if ($n=='') {
		if ($show_cero!='') {
			$n=0;
		} else {
			return '';
		}		
	}
		
	$n=number_format((float)$n, 2, ',', '.');
	
	return $n;
}

function aux_money_format_e ($n,$show_cero='') {
	$n=str_replace(',','.',$n);
	
	if ($n=='')	{
		if ($show_cero!='') {
			$n=0;
		} else {
			return '';
		}		
	}
	
	$n=number_format((float)$n, 2, ',', '.').'&nbsp;&euro;';
	
	return $n;
}

function render_listar_empresas () {
	$data=get_db_data(array('listar-empresas'));
	
	$options='';
	foreach ($data as $d) {
		$options.='<option value="'.$d['codigoempresa'].'">'.$d['codigoempresa'].' - '.ucfirst($d['empresa']).'</option>';
	}
	
	return $options;
}

function get_company_name ($company_id) {
	$data=get_db_data(array('nombre-empresa-por-id',$company_id));
	
	$nombre=$data['0']['empresa'];
	
	return $nombre;
}

function render_is_deleted () {
	$out='';
	$s0='';
	$s1='';
	$s2='';
	
	$current_value=cG('deleted');

	if (isset($current_value)) {
		if ($current_value=='')	    {	$s0=' selected="selected" ';	}		else		{	$s0='';	}
		if ($current_value=='0')	{	$s1=' selected="selected" ';	}		else		{	$s1='';	}
		if ($current_value=='-1')	{	$s2=' selected="selected" ';	}		else		{	$s2='';	}
	}

	if (!empty($_GET) && $current_value == '') {
		$s0 = ' selected="selected" ';
	}

	if (empty($_GET)) {
		/* por defecto poner no borrados */
		$s0 = '';
		$s1 = ' selected="selected" ';
		$s2 = '';
	}
	
	$out.='<option '.$s1.' value="0">No borrados</option>';
	$out.='<option '.$s0.' value="">Todos</option>';
    $out.='<option '.$s2.' value="-1">Borrados</option>';
	
	return $out;
}

function render_listar_obras_i4 () {
	$data=get_db_data(array('listar-obras-i4'));
	
	$current_value=cG('wk');
	
	$options='';
	
	if ($current_value=='activas') {
		$options='<option selected="selected" value="activas">Obras activas</option>';		
	} else {
		$options='<option value="activas">Obras activas</option>';
	}
	
	$options.='<option value="">Todas las obras</option>';
	
	foreach ($data as $d) {
		if ($current_value==$d['CODIGOPROYECTO']) {$selected=' selected="selected" ';} else {$selected='';}
		
		$options.='<option '.$selected.' value="'.$d['CODIGOPROYECTO'].'">'.$d['CODIGOPROYECTO'].' - '.ucfirst($d['PROYECTO']).'</option>';
	}
	
	return $options;

}

function render_listar_jefes_de_obra_i4 () {
	$data=get_db_data(array('listar-jefes-de-obra-i4'));
	
	$current_value=cG('jo');
	
	$options='';
	foreach ($data as $d) {
		if ($current_value==$d['ID_JEFE_OBRA']) {$selected=' selected="selected" ';} else {$selected='';}
		
		$options.='<option '.$selected.' value="'.$d['ID_JEFE_OBRA'].'">'.$d['ID_JEFE_OBRA'].' - '.ucfirst($d['NOMBRE']).'</option>';
	}
	
	return $options;

}

function render_listar_clientes_i4 () {
	$data=get_db_data(array('listar-clientes-i4'));
	
	$current_value=cG('cl');
	
	$options='';
	foreach ($data as $d) {
		if ($current_value==$d['codigocliente']) {$selected=' selected="selected" ';} else {$selected='';}
		
		$options.='<option '.$selected.' value="'.$d['codigocliente'].'">'.$d['codigocliente'].' - '.ucfirst($d['razonsocial']).'</option>';
	}
	
	return $options;
}


function render_listar_encargados_i4 () {
	return;
	
	
	$data=get_db_data(array('listar-jefes-de-obra-i4'));
	
	$current_value=cG('en');
	
	$options='';
	foreach ($data as $d) {
		if ($current_value==$d['ID_JEFE_OBRA']) {$selected=' selected="selected" ';} else {$selected='';}
		
		$options.='<option '.$selected.' value="'.$d['ID_JEFE_OBRA'].'">'.$d['ID_JEFE_OBRA'].' - '.ucfirst($d['NOMBRE']).'</option>';
	}
	
	return $options;
}

function render_listar_articulos() {
	$options = '';
	$seleccionado = cG('articulos');
	$haySesion = false;
	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
	if (isset($_SESSION['articulos'])) {
		$haySesion = true;
	}
	$data = get_db_data(array('listar-articulos'));
	foreach ($data as $d) {
		$selected = '';
		if ($seleccionado == $d['CodigoArticulo']) { $selected = ' selected="selected" '; }
		if ($haySesion && in_array($d['CodigoArticulo'], $_SESSION['articulos'])) { $selected=' selected="selected" '; }
		$options.='<option '.$selected.' value="'.$d['CodigoArticulo'].'">'.$d['CodigoArticulo'].' - '.ucfirst($d['DescripcionArticulo']).'</option>';
		$n++;
	}
	return $options;
}

function render_listar_articulos_i18() {
	$options = '';
	$seleccionado = cG('articulos');
	$haySesion = false;
	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
	if (isset($_SESSION['articulos'])) {
		$haySesion = true;
	}
	$data = get_db_data(array('listar-articulos-i18'));
	foreach ($data as $d) {
		$selected = '';
		if ($seleccionado == $d['CodigoArticulo']) { $selected = ' selected="selected" '; }
		if ($haySesion && in_array($d['CodigoArticulo'], $_SESSION['articulos'])) { $selected=' selected="selected" '; }
		$options.='<option '.$selected.' value="'.$d['CodigoArticulo'].'">'.$d['CodigoArticulo'].' - '.ucfirst($d['DescripcionArticulo']).'</option>';
		$n++;
	}
	return $options;
}

function render_listar_contratos_i18() {
	$options = '';
	$seleccionado = cG('contratos');
	$haySesion = false;
	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
	if (isset($_SESSION['contratos'])) {
		$haySesion = true;
	}
	$data = get_db_data(array('listar-contratos-i18'));
	foreach ($data as $d) {
		$selected = '';
		if ($seleccionado == $d['codigoseccion']) { $selected = ' selected="selected" '; }
		if ($haySesion && in_array($d['codigoseccion'], $_SESSION['contratos'])) { $selected=' selected="selected" '; }
		$options.='<option '.$selected.' value="'.$d['codigoseccion'].'">'.$d['codigoseccion'].' - '.ucfirst($d['Seccion']).'</option>';
		$n++;
	}
	return $options;
}

function render_listar_anexos_i18() {
	$options = '';
	$seleccionado = cG('anexos');
	$haySesion = false;
	/* puede ser un select multiple, asi que se guarda en session el array de todos los que son seleccionados */
	if (isset($_SESSION['anexos'])) {
		$haySesion = true;
	}
	$n=0;
	$data = get_db_data(array('listar-anexos-i18'));
	foreach ($data as $d) {
		$selected = '';
		if ($seleccionado == $d['codigodepartamento']) { $selected = ' selected="selected" '; }
		if ($haySesion && in_array($d['codigodepartamento'], $_SESSION['anexos'])) { $selected=' selected="selected" '; }
		$options.='<option '.$selected.' value="'.$d['codigodepartamento'].'">'.$d['codigodepartamento'].' - '.ucfirst($d['departamento']).'</option>';
		$n++;
	}
	return $options;
}

function render_confirming_tlf() {
    $html=file_get_contents_utf8(ABSPATH.'/template/confirming-tlf.html');
    
    $data=get_db_data(array('tlf-confirming'));
    
    $content='';
    
    foreach($data as $d) {
        $content.='<tr><td>'.$d['ENTIDAD'].'<td>'.$d['TELEFONO_1'].'<td>'.$d['TELEFONO_2'].'</td></tr>';
    }
    
    $out=str_replace('{CONTENT}',$content,$html);
    
    return $out;
}

function compatibilidad_coma_punto($v) {
	$v=str_replace(',','.',$v);
	
	return $v;
}

?>