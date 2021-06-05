<?php

$server_name=gethostname();

//echo '<pre>'.$server_name.'</pre>';

if ($server_name=='PC') {
	//ECHO 'HOLA';
	define('DB_SERVER_IP','192.168.1.102');
	define('DB_SERVER_NAME','192.168.1.102');
	define('DB_NAME','ReportingChirivo');
	define('DB_USER','sarep');
	define('DB_PASS','Rep*2015');
	define('DB_CONECTION_TYPE','connect_by_ip');
	define('PREFIX','ReportingChirivo.dbo');

	$db_connection_info=array('Database'=>DB_NAME,'UID'=>DB_USER,'PWD'=>DB_PASS);

	// $db_connection_info=array('Database'=>DB_NAME,'UID'=>DB_USER,'PWD'=>DB_PASS,'CharacterSet' => 'UTF-8');
	
	//$sn=DB_SERVER_IP.'\\'.DB_SERVER_NAME.',1433';
	
	//$conn=sqlsrv_connect($sn,$db_connection_info);
	/* DESACTIVAR */ //if( $conn ) {} else { echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>'; die; }
	
} else {
	define('DB_SERVER_IP','127.0.0.1');
	define('DB_SERVER_NAME','SRVPUBLICO');
	define('DB_NAME','ReportingChirivo');
	define('DB_USER','sarep');
	define('DB_PASS','Rep*2015');
	define('DB_CONECTION_TYPE','connect_by_ip');
	define('PREFIX','ReportingChirivo.dbo');

	$db_connection_info=array('Database'=>DB_NAME,'UID'=>DB_USER,'PWD'=>DB_PASS);
}


// ***************************************************
if (isset($_SESSION['userid'])){$user_id=$_SESSION['userid'];}

function login($username,$password,$company_id) {
	global $db_connection_info;
	
	#searches for user and password in the database
	//$query = "SELECT * FROM ".PREFIX.".usuarios WHERE User_Name='".$username."' AND User_Pass='".$password."' AND active='-1'";
	$query = "SELECT * FROM ".PREFIX.".usuarios WHERE CodigoUsuario='".$username."' AND Password='".$password."'";
    $query_2 = "SELECT * FROM ".PREFIX.".usuariosempresas WHERE codigousuario='".$username."' AND codigoempresa='".$company_id."'";
	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);
	
	/* DESACTIVAR */ if( $conn ) {} else { echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>'; die; }
	
	
	$result = sqlsrv_query($conn, $query);
    $result_2 = sqlsrv_query($conn, $query_2);

	#checks if the search was made
	if($result === false) {
		echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>';
		die;
	}
	
	//echo '<pre>'.print_r($result,true).'</pre>';

	#checks if the search brought some row and if it is one only row
	if(!sqlsrv_has_rows($result)) {
		echo "Usuario / contrase&ntilde;a no encontrados";
	} else {
        if (sqlsrv_has_rows($result_2)) {
            
            #creates sessions
            while($row = sqlsrv_fetch_array($result)) {
                $_SESSION['userid']				=		$row['IDUsuario'];
                $_SESSION['username']			=		$row['CodigoUsuario'];
                $_SESSION['display_name']		=		$row['CodigoUsuario'];	
                $_SESSION['company_id']			=		$company_id;
				$_SESSION['id_jefe_obra']		=		$row['IDJefeObra'];
                $_SESSION['company_name']		=		get_company_name($company_id);                
            }
            
            return true;
        } else {
            echo "No esta autorizado a acceder a la empresa indicada";
            //echo '<pre>'.$query_2.'</pre>';
        }
		
	}
	
}

function get_db_data($arr) {
	$type=$arr[0];
	
	global $db_connection_info;
	$query=get_db_query($type);
	
	if ($type=='listar-detalle-efecto-i1' or $type=='listar-detalle-efecto-i1-agrupado' or $type=='listar-observaciones-efecto-i1' or $type=='info-efecto-i1' or $type=="info-efecto-i1-2" or $type=='listar-detalle-efecto-i1-2') {
		$query=str_replace('{EID}',$arr[1],$query);
	}
	if ($type=='listar-detalle-efecto-i7' or $type=='listar-observaciones-efecto-i7' or $type=='info-efecto-i7' or $type=="info-efecto-i7-2" or $type=='listar-detalle-efecto-i7-2') {
		$query=str_replace('{EID}',$arr[1],$query);
		//echo '<pre>'.$query.'</pre>';
	} elseif($type=='listado-completo-filtrado' or $type=='listado-completo-filtrado-i2' or $type=='listado-completo-filtrado-i8') {
		$query=str_replace('{FILTRO}',$arr[1],$query);
	} elseif($type=='listado-completo-filtrado-clientes') {
		$query=str_replace('{FILTRO}',$arr[1],$query);

		//echo '<pre>'.$query.'</pre>';
	} elseif($type=='listar-gastos-generales-i12' or $type=='listar-facturacion-mes-i12' or $type=='listar-gastos-mes-i12' or $type=='listar-gastos-extra-personal-i12' or $type=='listar-gastos-financiacion-i12' or $type=='listar-gastos-generales-i12-filtro-proyecto' or $type=='listar-gastos-generales-i12-filtro-contrato' or $type=='listar-gastos-generales-i12-filtro-anexo'
		 or $type=='listar-gastos-mediosm-i17' 
		 or $type=='listar-facturacion-mes-mediosm-i17' 
		 or $type=='listar-gastos-mes-mediosm-i17' 
		 or $type=='listar-gastos-mediosm-i17-filtro-proyecto' 
		 or $type=='listar-gastos-mediosm-i17-filtro-contrato' 
		 or $type=='listar-gastos-mediosm-i17-filtro-anexo' 

		 or $type=='listar-gastos-mediosm-i17-2'
		 or $type=='listar-gastos-mediosm-i17-2-filtro-proyecto' 
		 or $type=='listar-gastos-mediosm-i17-2-filtro-contrato' 
		 or $type=='listar-gastos-mediosm-i17-2-filtro-anexo' 

		 or $type=='contrato-obtener-datos-empresa' or $type=='listar-costes-desglosado-i18' or $type=='listar-importes-contratos-i19' or $type=='get-todos-admins' or $type=='eliminar-administrador' or $type=='listar-obras-contratacion-i19' or $type=='listar-proyectos-i9' or $type=='listar-proyectos-jefes-obra-i9' or $type == 'listar-importes-totales-contratos-i19' or $type == 'listar-obras-contratacion-i19')
	{
		$query=str_replace('{FILTRO}',$arr[1],$query);
	} elseif($type=='listar-previsiones-pago-i14' or $type=='listar-previsiones-pago-detallado-i15' or $type=='listar-facturacion-proveedores-i16') {
		$query=str_replace('{FILTRO}',$arr[1],$query);
	} elseif($type=='obtener-ggbi-i12') {
		$query=str_replace('{EJERCICIO}',$arr[1],$query);
	} elseif($type=='ajax-listar-contratos-por-proyecto') {
		$query=str_replace('{PROYECTO}',$arr[1],$query);
	} elseif($type=='ajax-listar-anexos-por-contrato') {
		$query=str_replace('{CONTRATO}',$arr[1],$query);
	} elseif($type=='nombre-empresa-por-id') {
		$query=str_replace('{ID_EMPRESA}',$arr[1],$query);
	} elseif($type=='obtener-i3-por-id') {
		$query=str_replace('{ID1}',$arr[1],$query);
		$query=str_replace('{ID2}',$arr[2],$query);
	} elseif($type=='obtener-i3-por-id-2') {
		$query=str_replace('{ID1}',$arr[1],$query);
	} elseif ($type=='jefes-de-obra-por-id') {
		$query=str_replace('{ID_JEFE_OBRA}',$arr[1],$query);
	} elseif ($type=='leer-informe-por-id') {
        $query=str_replace('{ID}',$arr[1],$query);
    } elseif ($type=='listar-i3-por-fecha') {
		$query=str_replace('{MES}',$arr[1],$query);
		$query=str_replace('{YEAR}',$arr[2],$query);
	} elseif ($type=='listar-i3-por-fecha-y-jefe-obra') {
		$query=str_replace('{MES}',$arr[1],$query);
		$query=str_replace('{YEAR}',$arr[2],$query);
		$query=str_replace('{JO}',$arr[3],$query);
	} elseif ($type=='listar-obras-i4-filtrado') {
		$query=str_replace('{QUERY}',$arr[1],$query);
		
		//echo '<!-- '.$query.' -->';		
	} elseif ($type=='facturacion-mes-i4') {
		$query=str_replace('{QUERY}',$arr[1],$query);		
	} elseif ($type=='obtener_informe_por_id_obra_y_periodo_i4') {
		$query=str_replace('{QUERY}',$arr[1],$query);
		
		//echo '<pre>'.$query.'</pre>';
	} elseif ($type=='obtener_gastos_por_id_obra_y_periodo_i4') {
		$query=str_replace('{QUERY}',$arr[1],$query);
	} elseif($type=='facturacion-origen-por-cliente-y-perido-i4') {
		$query=str_replace('{QUERY}',$arr[1],$query);
	} elseif($type=='obtener_info_informe_por_id_obra_y_periodo_i3' or $type=='obtener_info_informe_por_id_obra_y_periodo_i3_detalle') {
		$query=str_replace('{ID_OBRA}',$arr[1],$query);
		$query=str_replace('{MES}',$arr[2],$query);
		$query=str_replace('{YEAR}',$arr[3],$query);
	} elseif ($type=='min-year-i5' or $type=='max-year-i5') {
		$query=str_replace('{ID_OBRA}',$arr[1],$query);
		//echo '<pre>['.$arr[1].']'.$query.'</pre>';
	} elseif ($type=='min-month-i5' or $type=='max-month-i5') {
		$query=str_replace('{ID_OBRA}',$arr[1],$query);
		$query=str_replace('{YEAR}',$arr[2],$query);
		//echo '<pre>['.$arr[1].']'.$query.'</pre>';
	} elseif ($type=='i5-linea') {
		$query=str_replace('{ID_OBRA}'	,	$arr[1],$query);
		$query=str_replace('{MES}'		,	$arr[2],$query);
		$query=str_replace('{YEAR}'		,	$arr[3],$query);
		//echo '<pre>'.print_r($query,true).'</pre>';
	} elseif ($type=='proveedor-por-cif') {
		$query=str_replace('{CIF}',$arr[1],$query);
		$query=str_replace('{ID_EMPRESA}',$arr[2],$query);
		//echo '<pre>['.$arr[1].']'.$query.'</pre>';
	} elseif ($type=='custom-query') {
		$query=str_replace('{CUSTOM_QUERY}',$arr[1],$query);
	} elseif($type=='info-usuario-por-id') {
		$query=str_replace('{ID_USUARIO}',$arr[1],$query);
	} elseif($type=='listar-proyectos-facturacion' or $type=='listar-proyectos-facturacion-jefe-obra') {
		$query=str_replace('{CUSTOM_QUERY}',$arr[1],$query);
		//echo '<pre>'.$type.' -->'.$query.'</pre>';
	} elseif($type=='listar-desglose-proyectos-facturacion-jefe-obra' or $type=='listar-desglose-proyectos-facturacion') {
		$query=str_replace('{CODIGO_PROYECTO}',$arr[1],$query);
		$query=str_replace('{CUSTOM_QUERY}',$arr[2],$query);
		//echo '<pre>'.$type.' -->'.$query.'</pre>';
	} elseif($type=='listar-proyectos-facturacion-jefe-obra-filtrado' or $type=='listar-proyectos-facturacion-filtrado') {
		$query=str_replace('{CODIGO_PROYECTO}',$arr[1],$query);
		$query=str_replace('{MES}',$arr[2],$query);
		$query=str_replace('{PERIODO}',$arr[3],$query);
		$query=str_replace('{CUSTOM_QUERY}',$arr[4],$query);
		//echo '<pre>'.$type.' -->'.$query.'</pre>';
	} elseif($type=='listar-estados-facturacion-i9') {
		//echo '<pre>'.$type.' -->'.$query.'</pre>';
	} elseif($type=='obtener-contratos') {

	}
	
	//echo '<pre>'.$type.' -->'.$query.'</pre>';

	// 1
	$msc1 = microtime(true);	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);	
	$msc1 = microtime(true)-$msc1;
	
	// 2
	$msc2 = microtime(true);	
	$result = sqlsrv_query($conn, $query);	
	$msc2 = microtime(true)-$msc2;
	
	//$GLOBALS['DEBUG'][]='';	
	$GLOBALS['DEBUG'][]='['.$msc1.'] ['.$msc2.'] ['.$query.']';
	

// error_log("primer result ". $query . " \n\n", 3, "./my-errors.log");
	
	if($result === false) {
		echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>';
		die;
	}

	if(sqlsrv_has_rows($result)) {
		if ($type == 'listar-proyectos-facturacion-filtrado') {
			// error_log("\n" . $query . " \n\n", 3, "./my-errors.log");
		}
		
		$data = array();

		while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			$data[]=$row;
		}
		
		return $data;
	} else {
		// error_log("no tiene datos... " . $query, 3, "./my-errors.log");
		return false;
	}
}

function exec_db_data ($arr) {
	$type=$arr[0];
	
	global $db_connection_info;
	$query=get_db_query($type);
	
	if ($type=='sp-actualizar-facturas-ventas')	{
		$query=str_replace('{FECHA}',$arr[1],$query);
	}	
	
	//echo '<pre>'.$type.' -->'.$query.'</pre>';
	
	// 1
	$msc1 = microtime(true);	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);	
	$msc1 = microtime(true)-$msc1;
	
	// 2
	$msc2 = microtime(true);	
	$result = sqlsrv_query($conn, $query);	
	$msc2 = microtime(true)-$msc2;
	
	//$GLOBALS['DEBUG'][]='';	
	$GLOBALS['DEBUG'][]='['.$msc1.'] ['.$msc2.'] ['.$query.']';
	

	if($result === false) {
		echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>';
		die;
	}

	if(sqlsrv_has_rows($result)) {
		$data = array();

		while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			$data[]=$row;
		}
		
		return $data;
	} else {
		return false;
	}
}

function update_db_data ($arr) {
	$type=$arr[0];
	
	global $db_connection_info;	
	$query=get_db_query($type);	
	
	if ($type=='actualizar-estado-i3' or $type=='actualizar-estado-i3-a-enviado') {
		$query=str_replace('{ID}',$arr[1],$query);
		$query=str_replace('{ESTADO}',$arr[2],$query);
	} elseif ($type=='guardar-i3') {
		$query=str_replace('{ID}',$arr[1],$query);
		$query=str_replace('{DATA}',$arr[2],$query);
	} elseif ($type=='renombrar-id-para-modificar-procesados-i3') {
		$query=str_replace('{CURRENT_ID}',$arr[1],$query);
		$query=str_replace('{NEW_ID}',$arr[2],$query);
	} elseif ($type=='custom-query') {
		$query=str_replace('{CUSTOM_QUERY}',$arr[1],$query);
	} elseif($type=='cambiar-ggbi-i12')	{
		$query=str_replace('{EJERCICIO}',$arr[1],$query);
		$query=str_replace('{GGBI}',$arr[2],$query);
	}
	
	
	//echo '<pre>'.$query.'</pre>';
	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);
	$result = sqlsrv_query($conn, $query);

	if($result === false) {
		echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>';
		die;
	}

	return sqlsrv_rows_affected($result);
}

function store_db_data ($arr) {
	$type=$arr[0];
	
	global $db_connection_info;	
	$query=get_db_query($type);	
	
	if ($type=='grabar-i3-a' or $type=='grabar-i3-b') {
		$query=str_replace('{DATA}',$arr[1],$query);
	} elseif ($type=='agregar-observacion') {
		$query=str_replace('{OBS}',$arr[1],$query);
	} elseif ($type=='custom-query') {
		$query=str_replace('{CUSTOM_QUERY}',$arr[1],$query);
	} elseif ($type=='grabar-observacion-i9') {
		$query=str_replace('{I}',$arr[1],$query);
		$query=str_replace('{M}',$arr[2],$query);
		$query=str_replace('{T}',$arr[3],$query);
	} elseif($type == 'insertar-nuevo-ggbi-i12') {
		$query=str_replace('{ANIO}',$arr[1],$query);
		$query=str_replace('{GGBI}',$arr[2],$query);
	}
	
	
	if ($type=='generar-maestro-i3' or $type=='generar-maestro-i3-regenerado' or $type=='insertar-administrador' or $type=='insertar-contrato') {
		$query=str_replace('{DATA}',$arr[1],$query);
	}
		
	//echo '<pre>'.$query.'</pre>';
	
	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);
	$result = sqlsrv_query($conn, $query);

	if($result === false) {
		echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>';
		die;
	}

	return sqlsrv_rows_affected($result);
	/**/
}

function count_db_data($arr) {
	$type=$arr[0];
	
	global $db_connection_info;	
	$query=get_db_query($type);	
	
	//echo '<pre>'.$query.'</pre>';
	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);
	$result = sqlsrv_query($conn, $query);

	if($result === false) {
		echo '<pre>'.print_r( sqlsrv_errors(), true).'</pre>';
		die;
	}
	
	$arr=sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);

	return $arr[0];
}

function get_sql_current_datetime () {
	global $db_connection_info;
	
	$query=get_db_query('sql_current_datetime');
	
	$conn=sqlsrv_connect(DB_SERVER_NAME,$db_connection_info);
	$result = sqlsrv_query($conn, $query);  //$conn is your connection in 'connection.php'	
		
	$arr=sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);

	$date=$arr[0];
	
	return date_format($date, 'Y-m-d H:i:s');
	//return $result;
}

function get_db_query($type,$user_id='') {
	if ($user_id=='') {global $user_id;}
		
	$current_year=date('Y');
	$default_from_date=$current_year.'-01-01 00:00:00.000';
	$default_to_date=$current_year.'-10-01 00:00:00.000';
	
	if (isset($_SESSION['company_id'])) {$company=$_SESSION['company_id'];}
	
	switch ($type) {
		case 'custom-query'											: return "{CUSTOM_QUERY}"; break;
		case 'sql_current_datetime'									: return "SELECT GETDATE() AS SQLCurrentDateTime"; break;
		//case 'listado-completo'									: return "SELECT * FROM ".PREFIX.".carteraproveedor WHERE fechaemision>='".$default_from_date."' AND fechaemision<='".$default_to_date."'"; break;
		case 'listar-proyectos-i9'									: return "SELECT DISTINCT CodigoProyecto, Descripcion FROM ".PREFIX.".facturacionobras WHERE CodigoEmpresa='".$company."' AND CodigoProyecto<9999 {FILTRO} ORDER BY CodigoProyecto ASC"; /*"SELECT DISTINCT CodigoProyecto, DescripcionProyecto FROM ".PREFIX.".facturacionobras WHERE codigoempresa='".$company."' AND CodigoProyecto<9999 ORDER  BY CodigoProyecto"; */ break;
		case 'listar-proyectos-jefes-obra-i9'						: return "SELECT DISTINCT CodigoProyecto, DescripcionProyecto FROM ".PREFIX.".facturacionobras WHERE ID_JEFE_OBRA='".$_SESSION['id_jefe_obra']."' AND codigoempresa='".$company."' AND CodigoProyecto<9999 {FILTRO} ORDER BY CodigoProyecto"; break;
		case 'listar-estados-facturacion-i9'						: return "SELECT DISTINCT CodigoProyecto, DescripcionProyecto FROM ".PREFIX.".facturacionobras WHERE ID_JEFE_OBRA='".$_SESSION['id_jefe_obra']."' AND codigoempresa='".$company."' AND CodigoProyecto<9999 {FILTRO} ORDER BY CodigoProyecto"; break;
		case 'listado-completo'										: return "SELECT TOP 30 * FROM ".PREFIX.".carteraproveedor codigoempresa='".$company."' ORDER BY fechaemision DESC"; break;
		case 'listado-completo-filtrado'							: return "SELECT * FROM ".PREFIX.".carteraproveedor {FILTRO} ORDER BY codigoproveedor ASC,FechaEmision ASC"; break;
		case 'listado-completo-filtrado-clientes'					: return "SELECT * FROM ".PREFIX.".carteraclientes {FILTRO} ORDER BY CodigoClienteProveedor ASC,FechaEmision ASC"; break;
		case 'listado-completo-filtrado-i2'							: return "SELECT * FROM ".PREFIX.".carteraproveedordetalle2 {FILTRO} ORDER BY codigoproveedor ASC,FechaEmision ASC"; break;
		case 'listado-completo-filtrado-i8'							: return "SELECT * FROM ".PREFIX.".carteraclientesdetalle2 {FILTRO} ORDER BY codigocliente ASC,fechaemision ASC"; break;
		case 'info-efecto-i1'										: return "SELECT * FROM ".PREFIX.".carteraproveedor WHERE codigoempresa='".$company."' AND numeroefecto='{EID}'"; break;	// usada en encabezados	
        case 'info-efecto-i1-2'										: return "SELECT * FROM ".PREFIX.".carteraproveedordetalle4 WHERE codigoempresa='".$company."' AND numefectoagrupacion_='{EID}'"; break;	// usada en encabezados	
		case 'listar-detalle-efecto-i1'								: return "SELECT * FROM ".PREFIX.".carteraproveedordetalle WHERE codigoempresa='".$company."' AND numeroefecto='{EID}'"; break;
		case 'listar-detalle-efecto-i1-agrupado'					: return "SELECT * FROM ".PREFIX.".carteraproveedordetalle WHERE codigoempresa='".$company."' AND numefectoagrupacion_='{EID}'"; break;
		case 'listar-detalle-efecto-i1-2'							: return "SELECT * FROM ".PREFIX.".carteraproveedordetalle3 WHERE codigoempresa='".$company."' AND numefectoagrupacion_='{EID}'"; break;
		case 'listar-observaciones-efecto-i1'						: return "SELECT * FROM ".PREFIX.".SeguimientoEfectos WHERE CodigoEmpresa='".$company."' AND Nefecto='{EID}' ORDER BY Fecha DESC"; break;
		
		case 'info-efecto-i7'										: return "SELECT * FROM ".PREFIX.".carteraclientes WHERE CodigoEmpresa='".$company."' AND NumeroEfecto='{EID}'"; break;	// usada en encabezados	
		case 'info-efecto-i7-2'										: return "SELECT * FROM ".PREFIX.".carteraclientedetalle4 WHERE CodigoEmpresa='".$company."' AND NumEfectoAgrupacion_='{EID}'"; break;	// usada en encabezados	
		case 'listar-detalle-efecto-i7'								: return "SELECT * FROM ".PREFIX.".carteraclientesdetalle WHERE CodigoEmpresa='".$company."' AND NumeroEfecto='{EID}'"; break;
		case 'listar-detalle-efecto-i7-2'							: return "SELECT * FROM ".PREFIX.".carteraclientedetalle3 WHERE CodigoEmpresa='".$company."' AND NumEfectoAgrupacion_='{EID}'"; break;
		case 'listar-observaciones-efecto-i7'						: return "SELECT * FROM ".PREFIX.".SeguimientoEfectos WHERE CodigoEmpresa='".$company."' AND Nefecto='{EID}' ORDER BY Fecha DESC"; break;
		
		case 'listar-proveedores'									: return "SELECT codigoproveedor, Razonsocial FROM ".PREFIX.".proveedores WHERE CodigoEmpresa='".$company."' ORDER BY codigoproveedor ASC"; break;
		case 'listar-estados'										: return "SELECT estado FROM ".PREFIX.".estados"; break;
		case 'agregar-observacion'									: return "INSERT INTO ".PREFIX.".SeguimientoEfectos (CodigoEmpresa, Nefecto, Fecha, Usuario, NombreUsuario, Obs, Prevision, CodigoClienteProveedor) VALUES {OBS}";
		case 'listar-proyectos-nocompany'							: return "SELECT DISTINCT codigoproyecto, Proyecto FROM ".PREFIX.".Proyectos_rep WHERE codigoproyecto <> 'NULL' AND Proyecto <> '' AND Proyecto <> 'NULL' ORDER BY Proyecto ASC"; break;
		case 'listar-proyectos'										: return "SELECT DISTINCT codigoproyecto, Proyecto FROM ".PREFIX.".Proyectos_rep WHERE CodigoEmpresa='".$company."' AND Proyecto is not NULL ORDER BY codigoproyecto ASC"; break;
		case 'ajax-listar-contratos-por-proyecto'					: return "SELECT DISTINCT codigocontrato, contrato FROM ".PREFIX.".Proyectos_rep WHERE codigoproyecto='{PROYECTO}' AND CodigoEmpresa='".$company."' ORDER BY codigocontrato ASC"; break;
		case 'ajax-listar-anexos-por-contrato'						: return "SELECT DISTINCT codigoanexo, anexo FROM ".PREFIX.".Proyectos_rep WHERE codigocontrato='{CONTRATO}' AND CodigoEmpresa='".$company."' ORDER BY codigoanexo ASC"; break;
		case 'listar-empresas'										: return "SELECT * FROM ".PREFIX.".empresas ORDER BY codigoempresa ASC"; break;
		case 'nombre-empresa-por-id'								: return "SELECT * FROM ".PREFIX.".empresas WHERE codigoempresa='{ID_EMPRESA}' ORDER BY codigoempresa ASC"; break;
		case 'listar-i3-pendientes'									: return "SELECT * FROM ".PREFIX.".InformesCuadreObras WHERE ESTADO IS NULL"; break;
		case 'obtener-i3-por-id'									: return "SELECT * FROM ".PREFIX.".InformesCuadreObras WHERE ID_INFORME='{ID1}' AND ID_JEFE_OBRA='{ID2}'"; break;
		case 'obtener-i3-por-id-2'									: return "SELECT * FROM ".PREFIX.".InformesCuadreObras WHERE ID_INFORME='{ID1}'"; break;
		case 'grabar-i3-a'											: return "INSERT INTO ".PREFIX.".InformesCuadreObrasDatos (ID_INFORME,ID_JEFE_OBRA,ID_OBRA,MES,YEAR,DIA_DE_CIERRE,FECHA_GRABACION,PF_CIERRE,PF_FALTA_CONTRATO,PF_APROBACION,PF_OTROS,MOTIVOS,TOTAL_PENDIENTE_COBRO,P_CERTIFICACION,IMPORTE_P_CERTIFICACION,MOTIVO_P_CERTIFICACION,TOTAL_PENDIENTE_DE_PAGO,PAGO_ANTICIPADO_REALIZADO,OBSERVACIONES_GENERALES) VALUES ({DATA})";
		case 'grabar-i3-b'											: return "INSERT INTO ".PREFIX.".InformesCuadreObrasDatos (ID_INFORME,ID_JEFE_OBRA,ID_OBRA,MES,YEAR,DIA_DE_CIERRE,FECHA_GRABACION,PF_CIERRE,PF_FALTA_CONTRATO,PF_APROBACION,PF_OTROS,MOTIVOS,TOTAL_PENDIENTE_COBRO,TOTAL_PENDIENTE_DE_PAGO,ACOPIO_ENTREGA,IMPORTE_ACOPIO_ENTREGA,MOTIVO_ACOPIO_ENTREGA,PAGO_ANTICIPADO_REALIZADO,OBSERVACIONES_GENERALES) VALUES ({DATA})";
		case 'actualizar-estado-i3'									: return "UPDATE ".PREFIX.".InformesCuadreObras SET ESTADO='{ESTADO}' WHERE ID_INFORME='{ID}'"; break;
		case 'actualizar-estado-i3-a-enviado'						: return "UPDATE ".PREFIX.".InformesCuadreObras SET ESTADO='{ESTADO}', FECHA_ENVIO=GETDATE() WHERE ID_INFORME='{ID}'"; break;
		case 'jefes-de-obra-por-id'									: return "SELECT * FROM ".PREFIX.".jefes_de_obra WHERE ID_JEFE_OBRA='{ID_JEFE_OBRA}'"; break;
		//case 'guardar-i3'											: return "UPDATE ".PREFIX.".InformesCuadreObras SET DATA='{DATA}', DATA_S='{DATA}' WHERE ID_INFORME='{ID}'"; break;
		case 'guardar-i3'											: return "UPDATE ".PREFIX.".InformesCuadreObras SET DATA='{DATA}' WHERE ID_INFORME='{ID}'"; break;
        case 'tlf-confirming'										: return "SELECT * FROM ".PREFIX.".Telefonos_Entidades_Financieras"; break;
        
        case 'leer-informe-por-id'									: return "SELECT * FROM ".PREFIX.".InformesCuadreObrasDatos WHERE ID_INFORME='{ID}'";
        
        case 'informes-autorizados-por-usuario-id'					: return "SELECT * FROM ".PREFIX.".usuariosinformes WHERE codigousuario='".$_SESSION['username']."'";
        
		case 'listar-i3-por-fecha'									: return "SELECT * FROM ".PREFIX.".InformesCuadreObras WHERE MES='{MES}' AND YEAR='{YEAR}' ORDER BY ID_OBRA"; break;
		
		case 'listar-i3-por-fecha-y-jefe-obra'						: return "SELECT * FROM ".PREFIX.".InformesCuadreObras WHERE MES='{MES}' AND YEAR='{YEAR}' AND ID_JEFE_OBRA='{JO}' ORDER BY NOMBRE_OBRA"; break;
		
		case 'obras-jefe-de-obra'									: return "SELECT * FROM ".PREFIX.".jefes_de_obra WHERE ACTIVO='1'"; break;
		
		case 'generar-maestro-i3'									: return "INSERT INTO ".PREFIX.".InformesCuadreObras (ID_INFORME,ID_JEFE_OBRA,ID_OBRA,MES,YEAR,FECHA_CEACION_REGISTRO,NOMBRE_JEFE_OBRA,NOMBRE_OBRA,EMAIL) VALUES ({DATA})";
	
		case 'generar-maestro-i3-regenerado'						: return "INSERT INTO ".PREFIX.".InformesCuadreObras (DATA,ID_INFORME,ID_JEFE_OBRA,ID_OBRA,MES,YEAR,FECHA_CEACION_REGISTRO,NOMBRE_JEFE_OBRA,NOMBRE_OBRA,EMAIL) VALUES ({DATA})";
	
		case 'listar-obras-i4'										: return "SELECT DISTINCT CODIGOPROYECTO, PROYECTO FROM ".PREFIX.".jefes_de_obra"; break;
		case 'listar-jefes-de-obra-i4'								: return "SELECT DISTINCT ID_JEFE_OBRA, NOMBRE FROM ".PREFIX.".jefes_de_obra"; break;
		case 'listar-clientes-i4'									: return "SELECT * FROM ".PREFIX.".clientes WHERE codigoempresa='".$company."' ORDER BY razonsocial ASC"; break;
		
		case 'listar-obras-i4-filtrado'								: return "SELECT DISTINCT * FROM ".PREFIX.".I4_CONSOLIDADO {QUERY} ORDER BY D_CODIGO_PROYECTO ASC"; break;
	
		case 'facturacion-mes-i4'									: return "SELECT * FROM ".PREFIX.".facturacion_mes {QUERY} "; break;
		
		case 'obtener_informe_por_id_obra_y_periodo_i4'				: return "SELECT * FROM ".PREFIX.".InformesCuadreObras {QUERY} "; break;
	
		case 'obtener_gastos_por_id_obra_y_periodo_i4'				: return "SELECT * FROM ".PREFIX.".Costes_Mensuales {QUERY} "; break;
		
		case 'renombrar-id-para-modificar-procesados-i3'			: return "UPDATE ".PREFIX.".InformesCuadreObrasDatos SET ID_INFORME='{NEW_ID}' WHERE ID_INFORME='{CURRENT_ID}'"; break;
		
		case 'facturacion-origen-por-cliente-y-perido-i4'			: return "SELECT * FROM ".PREFIX.".facturacion_origen {QUERY} "; break;
		
		case 'obtener_info_informe_por_id_obra_y_periodo_i3'		: return "SELECT * FROM ".PREFIX.".InformesCuadreObras WHERE MES='{MES}' AND YEAR='{YEAR}' AND ID_OBRA='{ID_OBRA}' "; break;
	
		case 'obtener_info_informe_por_id_obra_y_periodo_i3_detalle': return "SELECT * FROM ".PREFIX.".InformesCuadreObrasDatos WHERE MES='{MES}' AND YEAR='{YEAR}' AND ID_OBRA='{ID_OBRA}' "; break;
	
		case 'min-year-i5'											: return "SELECT MIN(CAST(YEAR AS INT)) as min FROM ".PREFIX.".DetalleRendimientoObra WHERE ID_OBRA='{ID_OBRA}' "; break;
		
		case 'max-year-i5'											: return "SELECT MAX(CAST(YEAR AS INT)) as max FROM ".PREFIX.".DetalleRendimientoObra WHERE ID_OBRA='{ID_OBRA}' "; break;
	
		case 'min-month-i5'											: return "SELECT MIN(CAST(MES AS INT)) as min FROM ".PREFIX.".DetalleRendimientoObra WHERE ID_OBRA='{ID_OBRA}' AND	YEAR='{YEAR}' "; break;
		
		case 'max-month-i5'											: return "SELECT MAX(CAST(MES AS INT)) as max FROM ".PREFIX.".DetalleRendimientoObra WHERE ID_OBRA='{ID_OBRA}' AND YEAR='{YEAR}' "; break;
		
		case 'i5-linea'												: return "SELECT * FROM ".PREFIX.".DetalleRendimientoObra WHERE ID_OBRA='{ID_OBRA}' AND YEAR='{YEAR}' AND MES='{MES}' and PF_CIERRE IS NOT NULL"; break; /* LE HE AÑADIDO IS NOT NULL PQ EN ALGUNOS CASOS DE 2015 EN ESTA TABLA SALEN MUCHOS RESULTADOS NULOS */
		
		case 'proveedor-por-cif'									: return "SELECT * FROM ".PREFIX."._X_Proveedores_registro WHERE CifDni='{CIF}' and CodigoEmpresa='{ID_EMPRESA}' "; break;
		
		case 'info-usuario-por-id'									: return "SELECT * FROM ".PREFIX.".Usuarios WHERE IDUsuario='{ID_USUARIO}'"; break;
		

		
		
		case 'listar-proyectos-facturacion'							: return "SELECT DISTINCT CodigoProyecto FROM ".PREFIX.".facturacionobras WHERE CodigoEmpresa='".$company."' {CUSTOM_QUERY} ORDER  BY CodigoProyecto"; break;		
		case 'listar-proyectos-facturacion-jefe-obra'				: return "SELECT DISTINCT CodigoProyecto FROM ".PREFIX.".facturacionobras WHERE ID_JEFE_OBRA='".$_SESSION['id_jefe_obra']."' AND CodigoEmpresa='".$company."' {CUSTOM_QUERY} ORDER  BY CodigoProyecto"; break;
		
		
		case 'listar-desglose-proyectos-facturacion'				: return "SELECT DISTINCT Proyecto FROM ".PREFIX.".facturacionobras WHERE CodigoEmpresa='".$company."' AND CodigoProyecto='{CODIGO_PROYECTO}' {CUSTOM_QUERY} ORDER  BY Proyecto"; break;
		case 'listar-desglose-proyectos-facturacion-jefe-obra'		: return "SELECT DISTINCT Proyecto FROM ".PREFIX.".facturacionobras WHERE ID_JEFE_OBRA='".$_SESSION['id_jefe_obra']."' AND CodigoEmpresa='".$company."' AND CodigoProyecto='{CODIGO_PROYECTO}' {CUSTOM_QUERY} ORDER  BY Proyecto"; break;
				
		
		case 'listar-proyectos-facturacion-filtrado'				: return "SELECT * FROM ".PREFIX.".facturacionobras WHERE CodigoEmpresa='".$company."' AND Proyecto='{CODIGO_PROYECTO}' AND Mes='{MES}' AND Ejercicio='{PERIODO}' {CUSTOM_QUERY}"; break;
		case 'listar-proyectos-facturacion-jefe-obra-filtrado'		: return "SELECT * FROM ".PREFIX.".facturacionobras WHERE ID_JEFE_OBRA='".$_SESSION['id_jefe_obra']."' AND CodigoEmpresa='".$company."' AND Proyecto='{CODIGO_PROYECTO}' AND Mes='{MES}' AND Ejercicio='{PERIODO}' {CUSTOM_QUERY}"; break;
		
		
		case 'sp-actualizar-facturas-ventas'						: return "exec _CH_Facturacion_mensual @Fecha='{FECHA}' "; break;
		case 'actualizar-jefes-obra'								: return "exec ".PREFIX."._X_Traspaso_Jefes_Obra "; break;	
		case 'grabar-observacion-i9'								: return "INSERT INTO ".PREFIX.".Observaciones_Ventas (ID_PROYECTO,MES,FECHA,TEXTO) VALUES ('{I}','{M}',GETDATE(),'{T}')"; break;
		
		case 'leer-observacion-i9'									: return "SELECT * FROM ".PREFIX.".Observaciones_Ventas WHERE ID_PROYECTO='{ID_PROYECTO}' AND MES='{MES}' ORDER BY FECHA DESC"; break;
		case 'listar-gastos-generales-i12'							: return "SELECT * FROM ".PREFIX.".GastosGenerales {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-generales-i12-filtro-proyecto'			: return "SELECT * FROM ".PREFIX.".GastosGenerales_Proyecto {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-generales-i12-filtro-contrato'			: return "SELECT * FROM ".PREFIX.".GastosGenerales_Contrato {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-generales-i12-filtro-anexo'				: return "SELECT * FROM ".PREFIX.".GastosGenerales_Anexos {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-facturacion-mes-i12'							: return "SELECT * FROM ".PREFIX.".facturacionmes_GG {FILTRO}"; break;
		case 'listar-gastos-mes-i12'								: return "SELECT * FROM ".PREFIX.".gastosmes_GG {FILTRO}"; break;
		case 'listar-gastos-extra-personal-i12'						: return "SELECT TOP (1) * FROM ".PREFIX.".GastosGenerales_2 {FILTRO}"; break;
//		case 'listar-gastos-financiacion-i12'						: return "SELECT TOP (1) * FROM ".PREFIX.".GastosGenerales_3 {FILTRO}"; break;
		case 'obtener-ggbi-i12'										: return "SELECT * FROM ".PREFIX.".EjerciciosGGBI WHERE Ejercicio={EJERCICIO}"; break;
		case 'cambiar-ggbi-i12'										: return "UPDATE ".PREFIX.".EjerciciosGGBI SET GGBI={GGBI} WHERE Ejercicio={EJERCICIO}"; break;
		case 'insertar-nuevo-ggbi-i12'								: return "INSERT INTO ".PREFIX.".EjerciciosGGBI (Ejercicio, GGBI) VALUES ({ANIO}, {GGBI})"; break;
		case 'listar-gastos-mediosm-i17'							: return "SELECT * FROM ".PREFIX.".MediosMateriales {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-mediosm-i17-filtro-proyecto'			: return "SELECT * FROM ".PREFIX.".MediosMateriales_Proyecto {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-mediosm-i17-filtro-contrato'			: return "SELECT * FROM ".PREFIX.".MediosMateriales_Contrato {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-mediosm-i17-filtro-anexo'				: return "SELECT * FROM ".PREFIX.".MediosMateriales_Anexos {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-facturacion-mes-mediosm-i17'					: return "SELECT * FROM ".PREFIX.".facturacionmes_MM {FILTRO}"; break;
		case 'listar-gastos-mes-mediosm-i17'						: return "SELECT * FROM ".PREFIX.".gastosmes_MM {FILTRO}"; break;
		/* ahora gastos medios materiales simple (que este sí es el informe 17) */
		case 'listar-gastos-mediosm-i17-2'							: return "SELECT * FROM ".PREFIX.".MediosMateriales {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-mediosm-i17-2-filtro-proyecto'			: return "SELECT * FROM ".PREFIX.".MediosMateriales_Proyecto_MM {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-mediosm-i17-2-filtro-contrato'			: return "SELECT * FROM ".PREFIX.".MediosMateriales_Contrato_MM {FILTRO} ORDER BY Codigo ASC"; break;
		case 'listar-gastos-mediosm-i17-2-filtro-anexo'				: return "SELECT * FROM ".PREFIX.".MediosMateriales_Anexos_MM {FILTRO} ORDER BY Codigo ASC"; break;

		case 'listar-bancos'										: return "SELECT DISTINCT(banco) FROM ".PREFIX.".prevision_bancos WHERE Banco <> 'SIN BANCO'"; break;
		case 'listar-tipos-efecto'									: return "SELECT DISTINCT(tipoefecto) FROM ".PREFIX.".TipoEfecto_prevision"; break;
		case 'listar-previsiones-pago-i14'							: return "SELECT Banco,contrapartida,TipoEfecto,SUM(ImporteEfecto) AS ImporteEfecto,CodigoEmpresa,CodigoBanco,CodigoOficina,CuentaCorriente,IBAN FROM ".PREFIX.".PrevisionPago_Agrupado {FILTRO} GROUP BY Banco,contrapartida,TipoEfecto,CodigoEmpresa,CodigoBanco,CodigoOficina,CuentaCorriente,IBAN"; break;
		case 'listar-previsiones-pago-detallado-i15'				: return "SELECT * FROM ".PREFIX.".PrevisionPago_Desglosado {FILTRO} ORDER BY Banco ASC"; break;
		case 'listar-facturacion-proveedores-i16'					: return "SELECT * FROM ".PREFIX.".facturacionproveedores {FILTRO} ORDER BY fechaemision ASC,codigoproveedor ASC, EjercicioFactura DESC"; break;
		case 'contrato-obtener-datos-empresa'						: return "SELECT TOP (1) * FROM SERVIDOR3.[logicSQ].[dbo].[Proveedores] {FILTRO}"; break;
		case 'get-todos-proyectos'									: return "SELECT distinct(CodigoProyecto), Proyecto, Descripcion from SERVIDOR3.logicSQ.dbo.Proyectos"; break;
		case 'get-todas-empresas'									: return "SELECT distinct CifDni,Domicilio,CodigoPostal,Municipio,RazonSocial,Telefono,CodigoEmpresa from SERVIDOR3.[logicSQ].[dbo].[Proveedores] where CodigoEmpresa='".$company."' group by CifDni,Domicilio,CodigoPostal,Municipio,RazonSocial,Telefono,CodigoEmpresa order by RazonSocial ASC"; break;
		case 'get-todos-admins'										: return "SELECT CodigoAdministrador,Nombre,Dni,Cargo,CifEmpresa from [ReportingChirivo].[dbo].[_CH_AdministradoresContrato] {FILTRO} order by Nombre ASC"; break;
		
		//case 'get-todas-formas-pago'								: return "SELECT [CodigoCondiciones],[Condiciones],DiasPrimerPlazo FROM SERVIDOR3.[logicSQ].[dbo].[CondicionesPlazos] group by codigocondiciones,Condiciones,DiasPrimerPlazo order by condiciones asc"; break;

		case 'insertar-administrador'								: return "INSERT INTO [ReportingChirivo].[dbo].[_CH_AdministradoresContrato] (Nombre,Dni,Cargo,CifEmpresa) VALUES ({DATA})";
		case 'eliminar-administrador'								: return "DELETE [ReportingChirivo].[dbo].[_CH_AdministradoresContrato] WHERE CodigoAdministrador = {FILTRO}";

		case 'insertar-contrato'									: return "INSERT INTO [ReportingChirivo].[dbo].[_CH_DescargasContratos] (CodigoEmpresa, UsuarioDescarga, NombreMercantil, NombreObra, TipoContrato, CodigoExpediente) VALUES ('".$_SESSION['company_id']."', '".$_SESSION['username']."', {DATA})";
		case 'obtener-contratos'									: return "SELECT * FROM [ReportingChirivo].[dbo].[_CH_DescargasContratos] WHERE CodigoEmpresa='".$company."'"; break;

		case 'listar-costes-desglosado-i18'							: return "SELECT * FROM [ReportingChirivo].[dbo].[Coste_desglosado] {FILTRO} ORDER BY CodigoProyecto ASC"; break;
		case 'listar-articulos'										: return "SELECT DISTINCT CodigoArticulo,DescripcionArticulo FROM SERVIDOR3.[logicSQ].[dbo].[Articulos] WHERE DescripcionArticulo <> '' ORDER BY DescripcionArticulo ASC"; break;
		case 'listar-articulos-i18'									: return "SELECT DISTINCT CodigoArticulo,DescripcionArticulo FROM [ReportingChirivo].[dbo].[Coste_desglosado] WHERE CodigoEmpresa='".$company."' AND CodigoArticulo <> '' AND  DescripcionArticulo <> '' ORDER BY DescripcionArticulo ASC"; break;
		case 'listar-contratos-i18'									: return "SELECT DISTINCT codigoseccion,Seccion FROM [ReportingChirivo].[dbo].[Coste_desglosado] WHERE CodigoEmpresa='".$company."' AND Seccion <> '' ORDER BY Seccion ASC"; break;
		case 'listar-anexos-i18'									: return "SELECT DISTINCT codigodepartamento,departamento FROM [ReportingChirivo].[dbo].[Coste_desglosado] WHERE CodigoEmpresa='".$company."' AND departamento <> '' ORDER BY departamento ASC"; break;
		case 'listar-importes-contratos-i19'						: return "SELECT * FROM [ReportingChirivo].[dbo].[_CH_ImportesContratos_rep] {FILTRO} ORDER BY CodigoProyecto ASC"; break;
		case 'listar-importes-totales-contratos-i19'				: return "SELECT * FROM [ReportingChirivo].[dbo].[_CH_ImportesContratosTotales_rep] {FILTRO} ORDER BY CASE SUBSTRING(codigoproyecto, 1, 2)
			      WHEN 'AM' THEN 1
			      WHEN 'A2' THEN 2
			      WHEN 'C2' THEN 3
			      ELSE 4
			  END
			  ASC, Ejercicio DESC"; break; // es necesario ordenarse así pq no se puede alfabeticamente
		case 'listar-obras-contratacion-i19'						: return "SELECT distinct codigoproyecto, descripcion FROM [ReportingChirivo].[dbo].[_CH_ImportesContratos_rep] {FILTRO} ORDER BY Descripcion ASC"; break;
		
	}
	
	//echo '<pre>'.$query.'</pre>';
}

function connect_db () {
	global $server_ip;
	global $server_name;
	global $user;
	global $conection_type;

	$connection_info=array('Database'=>'tde1');

	if ($conection_type=='connect_by_ip') {
		$conn=sqlsrv_connect($server_name,$connection_info);
	}

	if ($conn) {
		return $conn;	
	} else {
		echo '<pre>Error: '.print_r(sqlsrv_errors(), true).'</pre>';
	}
}

function generate_random_hash () {
	$string=date('D, d M Y H:i:s').rand(0,9999999999);
	
	$hash=sha1(sha1(md5($string)));
	
	return substr($hash,0,20);
}


function count_days($startdate,$enddate) {
	$startdate = strtotime($startdate);
	$enddate = strtotime($enddate);
	$datediff = $startdate-$enddate;
	
	return floor($datediff/(60*60*24));	
}


function check_privileges($id_informe) {   
	
	if ($id_informe=='x') {
		return true;
	}
    
    $data=get_db_data(array('informes-autorizados-por-usuario-id', $id_informe));
    
    if (is_array($data)) {
        foreach($data as $d) {
            $auth[]=$d['id_informe'];
        }
        
        if (in_array($id_informe,$auth)) {
            return true;
        }        
    }   
    
    //echo '<pre>'.print_r($data,true).'</pre>';
    
    echo get_header('default');    
    echo get_content('error-1');
    //echo '<pre>'.print_r($data,true).'</pre>';
    //echo '<pre>'.print_r($auth,true).'</pre>';
    echo get_footer('default');
    
    return false;
}

//Para jefes de obra
function check_privileges_2 ($id_informe) {   
	if ($id_informe=='x') {
		return true;
	}
	
	$data=get_db_data(array('informes-autorizados-por-usuario-id', $id_informe));
	
	if (is_array($data)) {
		foreach($data as $d) {
			$auth[]=$d['id_informe'];
		}
		
		if (in_array($id_informe,$auth)) {
			return true;
		}
	}
	
	//echo '<pre>'.print_r($data,true).'</pre>';
	
	echo get_header('default');    
	echo get_content('error-1');
	//echo '<pre>'.print_r($data,true).'</pre>';
	//echo '<pre>'.print_r($auth,true).'</pre>';
	echo get_footer('default');
	
	return false;
}
?>