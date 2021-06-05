<?php

require_once('render/common.php');
require_once('render/i1.php');
require_once('render/i2.php');
require_once('render/i3.php');
require_once('render/i4.php');
require_once('render/i5.php');
require_once('render/i6.php');
require_once('render/i7.php');
require_once('render/i8.php');
require_once('render/i9.php');
require_once('render/i10.php');
require_once('render/i12.php');
require_once('render/i13.php');
require_once('render/i14.php');
require_once('render/i15.php');
require_once('render/i16.php');
require_once('render/i17.php');
require_once('render/i18.php');
require_once('render/i19.php');
require_once('render/i20.php');
require_once('aux_functions.php');

function get_header($type='default') {
	$aditonal_css='';
	
	if (cG('excel')=='true') {
		$header='';
	} elseif ($type=='default') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
	} elseif ($type=='login') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		$aditonal_css='<link href="assets/bootstrap/css/login.css" id="t_stylesheet" rel="stylesheet">';	
	} elseif ($type=='informe-1') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		
		$aditonal_css='<link href="assets/bootstrap/css/i1b.css" id="t_stylesheet" rel="stylesheet">';	
	} elseif ($type=='listar-detalle-efecto-i1' or $type=='listar-detalle-efecto-i1-2') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_alt1.html');
		
		$aditonal_css='';
	} elseif ($type=='listar-observaciones-efecto-i1') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_alt1.html');
		
		$aditonal_css='';
	} elseif ($type=='agregar-observacion-efecto-i1') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_alt1.html');
		
		$aditonal_css='';
	} elseif ($type=='listar-detalle-efecto-i7' or $type=='listar-detalle-efecto-i7-2') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_alt1.html');
		
		$aditonal_css='';
	} elseif ($type=='listar-observaciones-efecto-i7') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_alt1.html');
		
		$aditonal_css='';
	} elseif ($type=='agregar-observacion-efecto-i7') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_alt1.html');
		
		$aditonal_css='';
	} elseif ($type=='informe-2') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		
		$aditonal_css='<link href="assets/bootstrap/css/i1b.css" id="t_stylesheet" rel="stylesheet">';
	} elseif ($type=='informe-3') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		
		$aditonal_css='<link href="assets/bootstrap/css/i3.css" id="t_stylesheet" rel="stylesheet">';
		$aditonal_css.='<link href="assets/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">';
	} elseif ($type=='informe-4' or $type=='informe-4d1' or $type=='informe-10') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		
		$aditonal_css='<link href="assets/bootstrap/css/i1b.css" id="t_stylesheet" rel="stylesheet">';
	} elseif ($type=='informe-5') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_i5.html');				
	} elseif ($type=='informe-6') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_i6.html');				
	} elseif ($type=='informe-7') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		
		$aditonal_css='<link href="assets/bootstrap/css/i1b.css" id="t_stylesheet" rel="stylesheet">';				
	} elseif ($type=='informe-8') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_default.html');
		
		$aditonal_css='<link href="assets/bootstrap/css/i1b.css" id="t_stylesheet" rel="stylesheet">';				
	} elseif ($type=='informe-9' or $type=='informe-12' or $type=='informe-13' or $type=='informe-14' or $type=='informe-15' or $type=='informe-16' or $type=='informe-17' or $type=='informe-18' or $type=='informe-19') {		
		$header=file_get_contents_utf8(ABSPATH.'/template/header_nuevo.html');					
	}

	$header=str_replace('{ADITIONAL_CSS}',$aditonal_css,$header);
	
	return $header;
}

function get_content($type='default') {	
	
	if ($type=='default') {
		// Base html
		$content=file_get_contents_utf8(ABSPATH.'/template/desktop.html');
		$menu=menu_personalizado();
		
		$main_content=file_get_contents_utf8(ABSPATH.'/template/elements/wellcome.html');
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
	} elseif ($type=='informe-1') {
		
		if (cG('excel')=='true') {

			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i1();
			
			exit;
		} else if (cG('print')=='true') {
		 	echo render_tabla_i1();
			
			exit;
		} else {
			
			// Base html
			
			$content			=	file_get_contents_utf8(ABSPATH.'/template/informe-1.html');		
			$menu				=	menu_personalizado();
			$filtro				=	make_filter();
			$main_content		=	render_tabla_i1();
			$proveedores		=	render_listar_proveedores();
			$estados			=	render_listar_estados();
			
			//echo '<pre>---'.$filtro.'---</pre>';
			
			$content=str_replace('{MAIN_CONTENT}'	,$main_content	,$content);		
			$content=str_replace('{MAIN_MENU}'		,$menu			,$content);
			$content=str_replace('{FILTRO}'			,$filtro		,$content);
			$content=str_replace('{SUPPLIERS}'		,$proveedores	,$content);
			$content=str_replace('{STATES}'			,$estados		,$content);
			
			if (is_array($GLOBALS['DEBUG'])) {
				$debug='';
				foreach($GLOBALS['DEBUG'] as $d) {
					$debug.='<pre>'.$d.'</pre>';
				}
				
				$content=str_replace('{DEBUG}',$debug,$content);
			} else {
				$content=str_replace('{DEBUG}','',$content);
			}
		}
		
	} elseif ($type=='listar-detalle-efecto-i1') {
		// Base html
		
		$content=file_get_contents_utf8(ABSPATH.'/template/listar-detalle-efecto-i1.html');
		$menu=menu_personalizado();
		$main_content=render_listar_detalle_efecto_i1();
		$info_efecto=render_info_efecto_i1();
		
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
		if ($main_content=='No hay registros') {
			return '<div style="widht:100%; padding-top:30px; font-size:18px; text-align:center;">'.$main_content.'</div>';
		}
	} elseif ($type=='listar-detalle-efecto-i1-2') {
		// Base html
		
		$content=file_get_contents_utf8(ABSPATH.'/template/listar-detalle-efecto-i1-2.html');
		$menu=menu_personalizado();
		$main_content=render_listar_detalle_efecto_i1_2();
		$info_efecto=render_info_efecto_i1_2();
		
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
		if ($main_content=='No hay registros') {
			return '<div style="widht:100%; padding-top:30px; font-size:18px; text-align:center;">'.$main_content.'</div>';
		}
	} elseif ($type=='listar-observaciones-efecto-i1') {
		// Base html
		
		$content=file_get_contents_utf8(ABSPATH.'/template/listar-observaciones-efecto-i1.html');
		$menu=menu_personalizado();
		$main_content=render_listar_observaciones_efecto_i1();
		$info_efecto=render_info_efecto_i1();
		
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
		if ($main_content=='No hay registros') {
			return '<div style="widht:100%; padding-top:30px; font-size:18px; text-align:center;">'.$main_content.'</div>';
		}
	} elseif ($type=='agregar-observacion-efecto-i1') {
		// Base html
		
		//$content=file_get_contents_utf8(ABSPATH.'/template/agregar-observacion-i1.html');
		$menu=menu_personalizado();
		$content=render_agregar_observacion_i1();
		$info_efecto=render_info_efecto_i1();
		
		//$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
	} elseif ($type=='listar-detalle-efecto-i7') {
		// Base html
		
		$content=file_get_contents_utf8(ABSPATH.'/template/listar-detalle-efecto-i7.html');
		$menu=menu_personalizado();
		$main_content=render_listar_detalle_efecto_i7();
		$info_efecto=render_info_efecto_i7();
		
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
		if ($main_content=='No hay registros') {
			return '<div style="widht:100%; padding-top:30px; font-size:18px; text-align:center;">'.$main_content.'</div>';
		}
	} elseif ($type=='listar-detalle-efecto-i7-2') {
		// Base html
		
		$content=file_get_contents_utf8(ABSPATH.'/template/listar-detalle-efecto-i7-2.html');
		$menu=menu_personalizado();
		$main_content=render_listar_detalle_efecto_i7_2();
		$info_efecto=render_info_efecto_i7_2();
		
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
		if ($main_content=='No hay registros') {
			return '<div style="widht:100%; padding-top:30px; font-size:18px; text-align:center;">'.$main_content.'</div>';
		}
	} elseif ($type=='listar-observaciones-efecto-i7') {
		// Base html
		
		$content=file_get_contents_utf8(ABSPATH.'/template/listar-observaciones-efecto-i1.html');
		$menu=menu_personalizado();
		$main_content=render_listar_observaciones_efecto_i7();
		$info_efecto=render_info_efecto_i7();
		
		$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
		if ($main_content=='No hay registros') {
			return '<div style="widht:100%; padding-top:30px; font-size:18px; text-align:center;">'.$main_content.'</div>';
		}
	} elseif ($type=='agregar-observacion-efecto-i7') {
		// Base html
		
		//$content=file_get_contents_utf8(ABSPATH.'/template/agregar-observacion-i1.html');
		$menu=menu_personalizado();
		$content=render_agregar_observacion_i7();
		$info_efecto=render_info_efecto_i7();
		
		//$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
		$content=str_replace('{MAIN_MENU}',$menu,$content);
		$content=str_replace('{INFO_EFECTO}',$info_efecto,$content);
		
	} elseif ($type=='informe-2') {
		
		if (cG('excel')=='true') {
			/**/
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i2();
			
			exit;
		} else if (cG('print')=='true')	{
		 	echo render_tabla_i2();
			
			exit;
		} else {
			// Base html
			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-2.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_2();
			$main_content=render_tabla_i2();
			$proveedores=render_listar_proveedores();
			$estados=render_listar_estados();
			$proyectos=render_listar_proyectos();
			
			//echo '<pre>---'.$filtro.'---</pre>';
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{SUPPLIERS}',$proveedores,$content);
			$content=str_replace('{STATES}',$estados,$content);
			$content=str_replace('{PROJECTS}',$proyectos,$content);
		}
		
	} elseif ($type=='informe-3') {
		$content=file_get_contents_utf8(ABSPATH.'/template/informe-3.html');
		
		
		/*
		$aditonal_js=
		'
		<script src="assets/jquery.min.js"></script>
		      <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<script src="assets/datepicker/js/bootstrap-datepicker.min.js">
		</script><script src="assets/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
		';
		*/
		
		$aditonal_js='';	
		
		$aditonal_js.='<script>'."\n".i3_sum_script()."\n".'</script>'."\n";
		
		$content=str_replace('{I3_SCRIPT}',$aditonal_js,$content);
		
		
		
		$content=process_i3($content);
		
	} elseif ($type=='informe-4') {
		
		if (cG('excel')!='') {
			/**/
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i4();
			
			exit;
		} else {
			
			// Base html
			
			$content			=	file_get_contents_utf8(ABSPATH.'/template/informe-4.html');		
			$menu				=	menu_personalizado();
			$filtro				=	make_filter4();
			$main_content		=	render_tabla_i4();
			$proveedores		=	render_listar_proveedores();
			$estados			=	render_listar_estados();
			
			//echo '<pre>---'.$filtro.'---</pre>';
			
			$content=str_replace('{MAIN_CONTENT}'	,$main_content	,$content);		
			$content=str_replace('{MAIN_MENU}'		,$menu			,$content);
			$content=str_replace('{FILTRO}'			,$filtro		,$content);
			$content=str_replace('{SUPPLIERS}'		,$proveedores	,$content);
			$content=str_replace('{STATES}'			,$estados		,$content);
			
			if (is_array($GLOBALS['DEBUG'])) {
				$debug='';
				foreach($GLOBALS['DEBUG'] as $d) {
					$debug.='<pre>'.$d.'</pre>';
				}
				
				$content=str_replace('{DEBUG}',$debug,$content);
			} else {
				$content=str_replace('{DEBUG}','',$content);
			}
		}
		
	} elseif ($type=='informe-4d1') {
		$main_content		=	render_tabla_i4_d1();
		$content            =   file_get_contents_utf8(ABSPATH.'/template/informe-4-d1.html');
		$menu				=	menu_personalizado();
		$filter				=	make_filter_i4_d1();
		
		$content			=	str_replace('{MAIN_CONTENT}'	,$main_content	,$content);		
		$content			=	str_replace('{MAIN_MENU}'		,$menu			,$content);
		$content			=	str_replace('{FILTER}'			,$filter		,$content);
	} elseif ($type=='informe-5') {
		$main_content		=	render_tabla_i5();
		$content            =   file_get_contents_utf8(ABSPATH.'/template/informe-5.html');
		$menu				=	menu_personalizado();
		$filter				=	make_filter_i5();
		
		$content			=	str_replace('{MAIN_CONTENT}'	,$main_content	,$content);		
		$content			=	str_replace('{MAIN_MENU}'		,$menu			,$content);
		$content			=	str_replace('{FILTER}'			,$filter		,$content);
	} elseif ($type=='informe-6') {		
		if (cP('sent')!='ok') { // Mostramos el formulario de alta
			//$main_content		=	render_tabla_i6();
			$content            =   file_get_contents_utf8(ABSPATH.'/template/informe-6.html');
			$menu				=	menu_personalizado();
			//$filter				=	make_filter_i6();
			
			$replaces=array
				(					
					'{MAIN_MENU}'			=>		$menu,					
					'{CIF}'					=>		cP('cif'),
					'{DATE}'				=>		cP('date'),
					'{NFAC}'				=>		cP('nfac'),
					'{BIMP}'				=>		cP('bimp'),
					'{IMP}'					=>		cP('imp'),
					'{PDF}'					=>		cP('pdf'),
					'{ID_EMPRESA}'			=>		$_SESSION['company_id'],
					'{ERROR}'				=>		'',
					);
			
			$content=str_replace(array_keys($replaces),$replaces,$content);			
			
		} elseif (i6v()!='ok') { // Hay errores de modo que mostramos el formulario nuevamente
			//$main_content		=	render_tabla_i6();
			$content            =   file_get_contents_utf8(ABSPATH.'/template/informe-6.html');
			$menu				=	menu_personalizado();
			//$filter				=	make_filter_i6();
			
			$replaces=array
				(					
					'{MAIN_MENU}'			=>		$menu,					
					'{CIF}'					=>		cP('cif'),
					'{DATE}'				=>		cP('date'),
					'{NFAC}'				=>		cP('nfac'),
					'{BIMP}'				=>		cP('bimp'),
					'{IMP}'					=>		cP('imp'),
					'{PDF}'					=>		cP('pdf'),
					'{ERROR}'				=>		i6_errors(),
					'{ID_EMPRESA}'			=>		$_SESSION['company_id']
					);
			
			$content=str_replace(array_keys($replaces),$replaces,$content);	
			
		} else { // Debe ser todo correcto y mostramos la pantalla de registro procesado
			//$main_content		=	render_tabla_i6();
			$content            =   file_get_contents_utf8(ABSPATH.'/template/informe-6b.html');
			$menu				=	menu_personalizado();
			//$filter			=	make_filter_i6();
			
			$result				=	store_i6();
			
			if ($result['RESULT']=='ok') {
				$result_text='<h3 style="text-align:center; color:green;">Factura procesada con ID: '.$result['id_registro'].'</h3>';
			} elseif ($result['RESULT']=='file-error') {
				$result_text='<h3 style="text-align:center; color:red;">Error al procesar el fichero PDF de la factura: '.cP('nfac').'</h3><p style="text-align:center; color:red;">La factura no ha sido registrada en el sistema</p>';
			} else {
				$result_text='<h3 style="text-align:center; color:red;">Error al procesar la factura: '.cP('nfac').'</h3>';
			}
			
			$ID_PROVEEDOR		=	$result['codigoproveedor'];
			$NOMBRE_PROVEEDOR	=	$result['razonsocial'];
			$ID_REGISTRO		=	$result['codigoproveedor'];
			$ID_FICHERO			=	$result['file'];
			
			$replaces=array
				(					
					'{MAIN_MENU}'			=>		$menu,					
					'{ID_REGISTRO}'			=>		$ID_REGISTRO,
					'{ID_FICHERO}'			=>		$ID_FICHERO,
					'{CIF}'					=>		cP('cif'),
					'{ID_PROVEEDOR}'		=>		$ID_PROVEEDOR,
					'{NOMBRE_PROVEEDOR}'	=>		$NOMBRE_PROVEEDOR,
					'{DATE}'				=>		cP('date'),
					'{NFAC}'				=>		cP('nfac'),
					'{BIMP}'				=>		cP('bimp'),
					'{IMP}'					=>		cP('imp'),
					'{PDF}'					=>		cP('pdf'),
					'{ERROR}'				=>		i6_errors(),
					'{ID_EMPRESA}'			=>		$_SESSION['company_id'],
					'{RESULT}'				=>		$result_text
					);
			
			$content=str_replace(array_keys($replaces),$replaces,$content);	
		}		
	} elseif ($type=='informe-7') {
		if (cG('excel')=='true') {
			/**/
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i7();
			
			exit;
		} else if (cG('print')=='true') {
		 	echo render_tabla_i7();
			
			exit;
		} else {
			
			// Base html
			
			$content			=	file_get_contents_utf8(ABSPATH.'/template/informe-7.html');		
			$menu				=	menu_personalizado();
			$filtro				=	make_filter_7();
			$main_content		=	render_tabla_i7();
			$clientes			=	render_listar_clientes_i4(); //reutilizado de i4
			$estados			=	render_listar_estados_efecto();
			
			//echo '<pre>---'.$filtro.'---</pre>';
			
			$content=str_replace('{MAIN_CONTENT}'	,$main_content	,$content);		
			$content=str_replace('{MAIN_MENU}'		,$menu			,$content);
			$content=str_replace('{FILTRO}'			,$filtro		,$content);
			$content=str_replace('{CL}'				,$clientes		,$content);
			$content=str_replace('{ESTADOS_EFECTO}' ,$estados		,$content);
			
			if (is_array($GLOBALS['DEBUG'])) {
				$debug='';
				foreach($GLOBALS['DEBUG'] as $d) {
					$debug.='<pre>'.$d.'</pre>';
				}
				
				$content=str_replace('{DEBUG}',$debug,$content);
			} else {
				$content=str_replace('{DEBUG}','',$content);
			}
		}
	} elseif ($type=='informe-8') {
		if (cG('excel')=='true') {
			/**/
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i8();
			
			exit;
		} else if (cG('print')=='true') {
		 	echo render_tabla_i8();
			
			exit;
		} else {
			// Base html
			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-8.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_8();
			$main_content=render_tabla_i8();
			$clientes=render_listar_clientes_i4(); //reutilizado de i4;
			$estados=render_listar_estados_efecto();
			$proyectos=render_listar_proyectos();
			
			//echo '<pre>---'.$filtro.'---</pre>';
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{CL}',$clientes,$content);
			$content=str_replace('{ESTADOS_EFECTO}',$estados,$content);
			$content=str_replace('{PROJECTS}',$proyectos,$content);
		}
	} elseif ($type=='informe-9') {

		if (cG('excel')=='true') {
			/**/
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");  //File name extension was wrong
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i9();
			
			exit;
		} else {
			// Base html
			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-9.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_9();
			$main_content=render_tabla_i9();

			//echo '<pre>---'.$filtro.'---</pre>';
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);		
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
						
			$date = cG('start');			
			
			if (empty($date)) {
				$date = date('d-m-Y');
			} else {
				/* ponerle el dia para que coja bien la fecha */
				$date = explode('-', $date);
				$y = $date[count($date)-1];
				$m = $date[count($date)-2];
				$date = '01-'.$m.'-'.$y;
			}
			
			$content = months_name_replace($date,$content);
			
		}

	} elseif ($type=='informe-10') {
		$main_content		=	render_tabla_i10();
		$content            =   file_get_contents_utf8(ABSPATH.'/template/informe-4-d1.html');
		$menu				=	menu_personalizado();
		$filter				=	make_filter_i4_d1();
		
		$content			=	str_replace('{MAIN_CONTENT}'	,$main_content	,$content);		
		$content			=	str_replace('{MAIN_MENU}'		,$menu			,$content);
		$content			=	str_replace('{FILTER}'			,$filter		,$content);
	} elseif ($type=='informe-12') { //gastos generales
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i12();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-12.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_12();
			$main_content=render_tabla_i12();
			$facturacion_mes=render_tabla_facturacion_mes();
			$gastos_mes=render_tabla_gastos_mes();
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);	
			$content=str_replace('{FACTURACION_MES}',$facturacion_mes,$content);
			$content=str_replace('{GASTOS_MES}',$gastos_mes,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
						
			$date=cG('start');			
			
			if ($date=='') {$date=date('m-Y');}
						
		}
	} elseif ($type=='informe-13') { //gastos medios materiales
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i13();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-13.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_13();
			$main_content=render_tabla_i13();
			$facturacion_mes=render_tabla_facturacion_mes_i13();
			$gastos_mes=render_tabla_gastos_mes_i13();
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);	
			$content=str_replace('{FACTURACION_MES}',$facturacion_mes,$content);
			$content=str_replace('{GASTOS_MES}',$gastos_mes,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
						
			$date=cG('start');			
			
			if ($date=='') {$date=date('m-Y');}
						
		}
	} elseif ($type=='informe-17') { //gastos medios materiales 2 (cambia el footer)

		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i17();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-17.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_17();
			$main_content=render_tabla_i17();
			$facturacion_mes=render_tabla_facturacion_mes_i17();
			$gastos_mes=render_tabla_gastos_mes_i17();
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);	
			$content=str_replace('{FACTURACION_MES}',$facturacion_mes,$content);
			$content=str_replace('{GASTOS_MES}',$gastos_mes,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
						
			$date=cG('start');			
			
			if ($date=='') {$date=date('m-Y');}
						
		}
	} elseif ($type=='informe-14') { //previsiones pago
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i14();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-14.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_14();
			$main_content=render_tabla_i14();
			$bancos=render_select_bancos();
			if (empty($_GET)) {
				$porDefecto = true;
			} else {
				$porDefecto = false;
			}
			$efectos=render_select_tipos_efecto($porDefecto);
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{BANCOS}',$bancos,$content);
			$content=str_replace('{EFECTOS}',$efectos,$content);
						
			$date=cG('start');			
			
			if ($date=='') {$date=date('m-Y');}
		}
	} elseif ($type=='informe-15') {//previsiones pago detallado
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i15();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-15.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_15();
			$main_content=render_tabla_i15();
			$bancos=render_select_bancos();
			if (empty($_GET)) {
				$porDefecto = true;
			} else {
				$porDefecto = false;
			}
			$efectos=render_select_tipos_efecto($porDefecto);
			$proveedores = render_listar_proveedores();
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{BANCOS}',$bancos,$content);
			$content=str_replace('{EFECTOS}',$efectos,$content);
			$content=str_replace('{PROVEEDORES}',$proveedores,$content);
						
			$date=cG('start');			
			
			if ($date=='') {$date=date('m-Y');}
		}
	} elseif ($type=='informe-16') { //facturacion de proveedores y subcontratas
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i16();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-16.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_16();
			$main_content=render_tabla_i16();
			$proyectos=render_listar_proyectos();
			$proveedores = render_listar_proveedores();
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{PROYECTOS}',$proyectos,$content);
			$content=str_replace('{PROVEEDORES}',$proveedores,$content);
		}
		// informe 17 es Contratos.php
	} elseif ($type=='informe-18') { //costes materiales desglosado
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i18();
			
			exit;
		} else {			
			$content = file_get_contents_utf8(ABSPATH.'/template/informe-18.html');		
			$menu = menu_personalizado();
			$filtro = make_filter_18();
			$main_content = render_tabla_i18();
			$proyectos = render_listar_proyectos();
			$proveedores = render_listar_proveedores();
			$articulos = render_listar_articulos();
			$contratos = render_listar_contratos_i18();
			$anexos = render_listar_anexos_i18();

			$start = cG('start');
			$end = cG('end');
			$estado = cG('estados');
			$checkedActivos = '';
			$checkedFinalizados = '';
			$checkedTodos = '';

			if ($estado == 'activos') {
				$checkedActivos = 'checked';
			} else if ($estado == 'finalizados') {
				$checkedFinalizados= 'checked';
			} else {
				$checkedTodos = 'checked';
			}

			$estados = 
			'<div class="input-group" style="padding-top:6px">
                <input type="radio" name="estados" id="activos" value="activos" style="display:inline;vertical-align:text-bottom" '.$checkedActivos.' />
                    <label for="activos" style="margin-right:40px">Activos</label>
                <input type="radio" name="estados" id="finalizados" value="finalizados" style="display:inline;vertical-align:text-bottom" '.$checkedFinalizados.' /> 
                    <label for="finalizados" style="margin-right:40px">Finalizados</label>
                <input type="radio" name="estados" id="todos" value="todos" style="display:inline;vertical-align:text-bottom" '.$checkedTodos.' /> 
                    <label for="todos">Todos los estados</label>
            </div>';
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{ESTADOS}',$estados,$content);
			$content=str_replace('{PROYECTOS}',$proyectos,$content);
			$content=str_replace('{PROVEEDORES}',$proveedores,$content);
			$content=str_replace('{ARTICULOS}',$articulos,$content);
			$content=str_replace('{CONTRATOS}',$contratos,$content);
			$content=str_replace('{ANEXOS}',$anexos,$content);
			$content=str_replace('{DESDE}',$start,$content);
			$content=str_replace('{HASTA}',$end,$content);						
		}
	} elseif ($type=='informe-19') { //informe de importes de contratación
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i19();
			
			exit;
		} else {			
			$content=file_get_contents_utf8(ABSPATH.'/template/informe-19.html');		
			$menu=menu_personalizado();
			$filtro=make_filter_19();
			$main_content=render_tabla_i19();
			$facturacion_mes=render_tabla_facturacion_mes_i19();
			$obras = render_listar_obras_contratacion();
			
			$content=str_replace('{MAIN_CONTENT}',$main_content,$content);
			$content=str_replace('{MAIN_MENU}',$menu,$content);
			$content=str_replace('{FILTRO}',$filtro,$content);
			$content=str_replace('{OBRAS}',$obras,$content);
						
			$date=cG('start');			
			
			if ($date=='') {$date=date('Y');}						
		}
	} elseif ($type=='informe-20') { //informe de importes de contratación -> segunda parte -> cartera de obra anual
		if (cG('excel')=='true') {
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=informe.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			
			echo render_tabla_i20();
			
			exit;
		} else {			
			$content = file_get_contents_utf8(ABSPATH.'/template/informe-19.html'); // se coge la misma plantilla que el 19	
			$menu = menu_personalizado();
			$filtro = make_filter_20();
			$main_content = render_tabla_i20();
			$facturacion_mes = render_tabla_facturacion_mes_i19(); // los totales siempre serán iguales
			
			$content = str_replace('{MAIN_CONTENT}', $main_content, $content);
			$content = str_replace('{MAIN_MENU}', $menu, $content);
			$content = str_replace('{FILTRO}', $filtro, $content);
						
			$date = cG('start');			
			
			if ($date == '') {$date = date('Y');}
						
		}
	} elseif ($type=='error-1') {
		$content            =   file_get_contents_utf8(ABSPATH.'/template/error_1.html');
		$menu				=	menu_personalizado();
		
		$content=str_replace('{MAIN_MENU}'		,$menu			,$content);        
	}
	
	if ($type!='informe-3') {
		$content=$content.render_confirming_tlf($content);
	}

	$content=c($content);
	
	return $content;
}

function get_login ($type) {
	$content='';
	
	if ($type=='login-mssql') {
		$content.=file_get_contents_utf8(ABSPATH.'/template/login_form.html');			
	} elseif ($type=='login-demo') {
		$content.=file_get_contents_utf8(ABSPATH.'/template/demo/demo_login_form.html');			
	}
	
	$content=str_replace('{COMPANIES}',render_listar_empresas(),$content);
	
	return $content;
}

function get_footer ($type='default') {
	
	if (cG('excel')=='true') {
		return;
	} elseif ($type=='informe-5') {
		$footer=file_get_contents_utf8(ABSPATH.'/template/footer-i5.html');
	} elseif ($type=='informe-6') {
		$footer=file_get_contents_utf8(ABSPATH.'/template/footer-i6.html');
	} else {		
		$footer=file_get_contents_utf8(ABSPATH.'/template/footer.html');
	}	
	
	$aditonal_js='';
	
	$footer=str_replace('{ADITIONAL_JS}',$aditonal_js,$footer);
	
	return $footer;	
}

function file_get_contents_utf8 ($fn) {
	$content = file_get_contents($fn);
	$content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
	$content = trim($content);

	$content = preg_replace('[\xEF\xBB\xBF]', '', $content); // quitar los caracteres extraños de los ficheros como: ï»¿
	
	return $content;
}

function c($text) {
	$replaces=array(
			'á' => '&aacute;',
			'é' => '&eacute;',
			'í' => '&iacute;',
			'ó' => '&oacute;',
			'ú' => '&uacute;',
			'Á' => '&Aacute;',
			'É' => '&Eacute;',
			'Í' => '&Iacute;',
			'Ó' => '&Oacute;',
			'Ú' => '&Uacute;',
			'ñ' => '&ntilde;',
			'Ñ' => '&Ntilde;',
			'º' => '&ordm;',
			'ª' => '&ordf;',			
			);
	
	$text=str_replace(array_keys($replaces),$replaces,$text);
	
	return $text;
}


function i3_sum_script() {
	$script=
		'
			function sum0() {
				var $s0 = parseFloat(0);

				var index;
				var a = ["PF_CIERRE", "PF_FALTA_CONTRATO", "PF_APROBACION", "PF_OTROS"];
				for (index = 0; index < a.length; ++index) {
					var $n = a[index];
					var $v = document.getElementById($n).value;
					
					$v = $v.replace(",", ".");
					
					if (!isNaN($v)) {	
																							
						$v = parseFloat($v) || 0;

						$s0 = parseFloat($s0) + $v;
					}
				}
				
				$s0 = $s0.toFixed(2);
				$s0 = $s0.replace(/\.00/,"");				
				$s0 = $s0.replace(".",",");

				document.getElementById("TOTAL_PENDIENTE_COBRO").value = $s0;

			}
			
			function sum1()
			{				
				var $s1 = parseFloat(0);
				for (i = 1; i < '.MAX_LINES.'; i++) 
				{					
					var $n = \'IMPORTE_P_CERTIFICACION_\' + i;
					var $v = document.getElementById($n).value;
					
					$v = $v.replace(",", ".");
					
					if (!isNaN($v)) 
					{
						$v = parseFloat($v) || 0;
												
						$s1 = parseFloat($s1) + $v;
					}
				}
				
				$s1 = $s1.toFixed(2);
				$s1 = $s1.replace(/\.00/,"");
				$s1 = $s1.replace(".",",");

				document.getElementById(\'TOTAL_PENDIENTE_DE_PAGO\').value = $s1;				
				
			}
			
			function sum2()
			{				
				var $s2 = parseFloat(0);
				for (i = 1; i < '.MAX_LINES.'; i++) 
				{					
					var $n = \'IMPORTE_ACOPIO_ENTREGA_\' + i;
					var $v = document.getElementById($n).value;
					
					$v = $v.replace(",", ".");
					
					if (!isNaN($v)) 
					{	
											
						$v = parseFloat($v) || 0;
												
						$s2 = parseFloat($s2) + $v;
					}
				}
				
				$s2 = $s2.toFixed(2);
				$s2 = $s2.replace(/\.00/,"");
				$s2 = $s2.replace(".",",");


				document.getElementById(\'PAGO_ANTICIPADO_REALIZADO\').value = $s2;				
				
			}
			
			$(document).ready(function() {$(window).keydown(function(event){if(event.keyCode == 13) {event.preventDefault(); return false;}});});
			
            

		
		';
	
	return $script;	
}

function nf ($n) {	
	$n=str_replace(',','.',$n)+0;
	
	return $n;
}	

function fix_sum ($arr) {
	$sum=0;
	foreach ($arr as $a) {		
		if ($a==''){$a=0;}
		$a=nf($a);
		$sum=$a+$sum;			
	}
	
	//$sum=str_replace('.',',',$sum);
	
	return $sum;
}

function nf_csv ($n) {
	if ($n!='') {
		if (is_numeric($n)) {
			$n=number_format($n, 2, ',', '');
		} else {
			//$n='ERROR -> '.$n;	
		}
	}
	
	return $n;
}

function get_ip_address () {
	// check for shared internet/ISP IP
	if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}

	// check for IPs passing through proxies
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		// check if multiple ips exist in var
		if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
			$iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			foreach ($iplist as $ip) {
				if (validate_ip($ip))
				return $ip;
			}
		} else {
			if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	}
	if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
	return $_SERVER['HTTP_X_FORWARDED'];
	if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
	return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
	return $_SERVER['HTTP_FORWARDED_FOR'];
	if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
	return $_SERVER['HTTP_FORWARDED'];

	// return unreliable ip since all else failed
	return $_SERVER['REMOTE_ADDR'];
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function validate_ip($ip) {
	if (strtolower($ip) === 'unknown')
	return false;

	// generate ipv4 network address
	$ip = ip2long($ip);

	// if the ip is set and not equivalent to 255.255.255.255
	if ($ip !== false && $ip !== -1) {
		// make sure to get unsigned long representation of ip
		// due to discrepancies between 32 and 64 bit OSes and
		// signed numbers (ints default to signed in PHP)
		$ip = sprintf('%u', $ip);
		// do private network range checking
		if ($ip >= 0 && $ip <= 50331647) return false;
		if ($ip >= 167772160 && $ip <= 184549375) return false;
		if ($ip >= 2130706432 && $ip <= 2147483647) return false;
		if ($ip >= 2851995648 && $ip <= 2852061183) return false;
		if ($ip >= 2886729728 && $ip <= 2887778303) return false;
		if ($ip >= 3221225984 && $ip <= 3221226239) return false;
		if ($ip >= 3232235520 && $ip <= 3232301055) return false;
		if ($ip >= 4294967040) return false;
	}
	return true;
}

function detectar_tipo_usuario_activo () {
	$info=get_db_data(array('info-usuario-por-id',$_SESSION['userid']));
	
	//echo '<pre>'.print_r($info,true).'</pre>';
	
	if ($info['0']['IDJefeObra']>0)	{
		return 'jefe-obra';
	}
	
	return false;
}

function menu_personalizado () {
	if (detectar_tipo_usuario_activo($_SESSION['userid'])=='jefe-obra')	{	
		return file_get_contents_utf8(ABSPATH.'/template/menu_jefe_obra.html');	
	} else {
		return file_get_contents_utf8(ABSPATH.'/template/menu.php');
	}
}

?>