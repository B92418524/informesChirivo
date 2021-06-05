<?php


function i6v () {
	$errors = false;
	$errors_text = array();	
	
	$cif	=	cP('cif');
	$date	=	cP('date');
	$nfac	=	cP('nfac');
	$bimp	=	cP('bimp');
	$imp	=	cP('imp');
	
	
	if ($cif == '') {
		$errors = true;
		$errors_text[] = 'Debe indicar un CIF';
	} else {
		if (!validate_cif($cif)) {
			$errors = true;
			$errors_text[] = 'El CIF indicado no es correcto';
		}
	}

	if ($date == '') {
		$errors = true;
		$errors_text[] = 'Debe indicar una fecha';
	}
	
	if ($nfac == '') {
		$errors = true;
		$errors_text[] = 'Debe indicar un numero de factura';
	} else {
		if (strlen($nfac)>15) {
			$errors = true;
			$errors_text[] = 'El campo factura no puede tener mas de 15 caracteres';
		}
	}
	
	if ($bimp=='') {
		$errors = true;
		$errors_text[]='Debe indicar una base imponible';
	}

	if (!validar_cifras($bimp))	{
		$errors = true;
		$errors_text[]='Use coma como separador decimal. No incluya puntos.';
	}

	if ($imp=='') {
		$errors = true;
		$errors_text[]='Debe indicar un importe';
	}

	if (!validar_cifras($imp)) {
		$errors = true;
		$errors_text[]='Use coma como separador decimal. No incluya puntos.';
	}

	if (!$_FILES['pdf']['name']) {
		$errors = true;
		$errors_text[]='Debe adjuntar una factura en formato PDF';
	}
	
	if ($errors == false)	{
		if (search_duplicates_i6())	{
			$errors = true;
			$errors_text[] = 'Ya existe una factura similar registrada!';
		}
	}		
	
	if ($errors == false)	{
		return 'ok';
	}
	
	return $errors_text;
}

function i6_errors () {
	$results = i6v();
	$error = '';
	
	if (is_array($results))	{
		foreach ($results as $r) {
			$error .= '<p class="bg-danger" style="font-weight:bold; padding:5px;">' . $r . '</p>';			
		}
	}
	
	return '<div style="margin-top:20px;">' . $error . '</div>';
}


function validate_cif ($cif) {
	$data = get_db_data(array('proveedor-por-cif', $cif, $_SESSION['company_id']));
	
	if (is_array($data)) {
		if (count($data) == 1) {
			return true;	
		}
	}
	
	return false;
}

function obtener_proveedor_por_cif ($cif) {
	$data = get_db_data(array('proveedor-por-cif', $cif, $_SESSION['company_id']));
	return $data['0'];
}


function search_duplicates_i6 () {

	$data_p						=	obtener_proveedor_por_cif(cP('cif'));		
	$codigoproveedor			=	trim($data_p['CodigoProveedor']);
	
	$date						=	sql_date(cP('date'),'es');
	
	$nfac						=	cP('nfac');
	
	
	/**
	 * PREFIJO DE PRUEBAS
	 * */
	//$nfac='BORRAR_'.$nfac;
	
	
	$query = "
		SELECT * FROM [SERVIDOR2].[logicSQ].[dbo].[_CH_Registro_Facturas]

		WHERE 

		[CodigoProveedor]					=	" . $codigoproveedor . "					AND
		[SuFacturaNo]						=	'" . $nfac . "'								AND
		[FechaSuFactura]					=	'" . $date . "'

		";
	
	//BUSCAMOS LA ID ASIGNADA
	$r = get_db_data(array('custom-query', $query));
	
	if (!empty($r))	{
		return true;
	}
	
	return false;
}

function store_i6 () {
	
	$out = array();
	
	$out['RESULT']				=	'error';
		
	$data_p = obtener_proveedor_por_cif(cP('cif'));
	
	$codigo_empresa				=	$_SESSION['company_id'];
	$cif						=	cP('cif');
	$codigoproveedor			=	trim($data_p['CodigoProveedor']);
	$razonsocial				=	$data_p['RazonSocial'];
	$date						=	sql_date(cP('date'),'es');
	$nfac						=	cP('nfac');
	
	$bimp						=	cP('bimp');
	$bimp						=	str_replace(',','.',$bimp);
	
	$imp						=	cP('imp');
	$imp						=	str_replace(',','.',$imp);
	
	$current_date				=	'04/01/2015';
	
	$file_generated_name		=	'0000'.$_SESSION['company_id'].'_'.generate_file_name().'.pdf';

	$file = $file_generated_name;
	
	// Mostrado en el resultado del informe
	//$file						=	$file_generated_name.'.pdf';
	
	$DOCNombreLc				=	'Factura '.$nfac;
	$DOCTituloLc				=	'Fra Prov. '.$razonsocial.' '.$nfac;
	$DOCTemaLc					=	'Factura Proveedor '.$razonsocial;
	
	// YA HEMOS VALIDADO ANTERIORMENTE QUE NO ESTA REGISTRADA UNA FACTURA SIMILAR
	
	/**
	 * PREFIJO DE PRUEBAS
	 * */
	//$nfac='BORRAR_'.$nfac;
	
	
	// EMPEZAMOS POR MOVER EL FICHERO
	if (move_file_i6($file_generated_name) == 'ok') {

		$query_1 = "
	INSERT INTO [SERVIDOR2].[logicSQ].[dbo].[LcDOCPdf]
	(
		[DOCIdArchivoLc], 
		[DOCIdLc], 
		[DOCCodigoUsuarioLc], 		
		[DOCNombreLc], 
		[DOCTituloLc], 
		[DOCTemaLc], 
		[DOCAutorLc], 
		[DOCFechaCreacionLc], 
		[DOCNombreUsuarioLc], 
		[DOCRetenidoLc], 
		[DOCIdCarpetaLc], 
		[DOCIdCarpetaSistemaLc], 
		[DOCNombrePdfLc], 
		[CodigoEmpresa], 
		[CodigoDocumentoLc], 
		[DOCFechaInicioCaducidadLc],
		[CodigoProveedor]
	)
	VALUES
	(
		'904D1B5F-D9D6-4066-A3C0-746731A552AC',
		NEWID(), 
		'1', 
		'".$DOCNombreLc."',
		'".$DOCTituloLc."', 
		'".$DOCTemaLc."',		 
		'administrador',
		GETDATE(),
		'administrador',
		'-1', 
		'5579BDD7-EB9E-46BA-A2EF-4918D340BDA5', 
		'0', 
		'".$file_generated_name."',
		'".$codigo_empresa."', 
		'".$nfac."', 
		'".$date."',
		'".$codigoproveedor."'
		)
";

		$r1 = store_db_data(array('custom-query', $query_1));

		$query_2 =
			"
			SELECT * FROM [SERVIDOR2].[logicSQ].[dbo].[LcDOCPdf]

			WHERE 

			[DOCIdArchivoLc]				=	'904D1B5F-D9D6-4066-A3C0-746731A552AC'		AND
			[DOCCodigoUsuarioLc]			=	'1'											AND
			[DOCNombreLc]					=	'".$DOCNombreLc."'							AND
			[DOCTituloLc]					=	'".$DOCTituloLc."'							AND
			[DOCTemaLc]						=	'".$DOCTemaLc."'							AND
			[DOCAutorLc]					=	'administrador'								AND
			[DOCNombreUsuarioLc]			=	'administrador'								AND
			[DOCRetenidoLc]					=	'-1'										AND
			[DOCIdCarpetaLc]				=	'5579BDD7-EB9E-46BA-A2EF-4918D340BDA5'		AND
			[DOCIdCarpetaSistemaLc]			=	'0'											AND
			[DOCNombrePdfLc]				=	'".$file_generated_name."'					AND
			[CodigoEmpresa]					=	'".$codigo_empresa."'						AND
			[CodigoDocumentoLc]				=	'".$nfac."'									AND
			[DOCFechaInicioCaducidadLc]		=	'".$date."'

			";

		//BUSCAMOS LA ID ASIGNADA
		$r2 = get_db_data(array('custom-query', $query_2));

		$key_id = $r2['0']['DOCPosicionLc'];

		$id_registro = $key_id;

		$query_3 = "
			INSERT INTO [SERVIDOR2].[logicSQ].[dbo].[_CH_Registro_Facturas]
			(
				[CodigoEmpresa],
				[CodigoProveedor],
				[FechaSuFactura],
				[SuFacturaNo],
				[BaseImponible],
				[ImporteLiquido],
				[StatusContabilizado],
				[FechaRegistro],
				[NUMEROREGISTRO]
			)
			VALUES
			(
				".$codigo_empresa.",
				".$codigoproveedor.",
				'".$date."',
				'".$nfac."',
				".$bimp.",
				".$imp.",
				0,
				GETDATE(),
				'".$key_id."'	
			)
			";

		$r3 = store_db_data(array('custom-query',$query_3));

		$out['RESULT']				=	'ok';	
		
		
	} // FIN IF DE SI EL FICHERO SE MUEVE CORRECTAMENTE
	else {
		$out['RESULT']				=	'file-error';		
	}
	
	
	$out['cif']					=	$cif;
	$out['codigoproveedor']		=	$codigoproveedor;
	$out['razonsocial']			=	$razonsocial;
	$out['date']				=	$date;
	$out['nfac']				=	$nfac;
	$out['bimp']				=	$bimp;
	$out['imp']					=	$imp;
	$out['current_date']		=	$current_date;
	$out['file']				=	$file;
	$out['id_registro']			=	$id_registro;

	return $out;	
}

function validar_cifras ($cifra) {
	if ($cifra!='') {
		$pos = strrpos($cifra, ".");
		if ($pos === false)	{
			return true;
		}
	}
	
	return false;
}

function generate_file_name () {
	$time			=	date_create();
	$timestamp		=	date_timestamp_get($time) . rand('1111','99999999') . rand('1111','99999999');
	$name			=	md5($timestamp);
	
	return $name;	
}

function move_file_i6 ($new_name) {
	$ruta_destino = 'D:\DOCSMurano\ARCHIVO_{904D1B5F-D9D6-4066-A3C0-746731A552AC}';
	
	if($_FILES['pdf']['error'] > 0)	{
		return 'Error al subir el fichero ' . $_FILES['pdf']['name'];
	} else {		
		if(!move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_destino . '/' . $new_name)) {
			return 'Error al mover el fichero ' . $_FILES['pdf']['name'] . ' a ' . $ruta_destino . '/' . $new_name;
		} else {
			return 'ok';	
		}
	}	
}
?>