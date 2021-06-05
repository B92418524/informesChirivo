<?php
include_once '../../../config.php';
include_once '../../aux_functions.php';
include_once '../../db_functions.php';

if (cP('accion')=='') {
	echo 'false';
} else {
	$accion = cP('accion');
	if ($accion == 'cif') {
		$cif = cP('cif');
		$data = get_db_data(array('contrato-obtener-datos-empresa', "WHERE CifDni='".$cif."'"));
		if (is_array($data)) {
			$domicilio = '';
			$cp = '';
			$municipio = '';
			if (!empty($data[0]['Domicilio'])) {
				$domicilio = $data[0]['Domicilio'];
			}
			if (!empty($data[0]['CodigoPostal'])) {
				$cp = $data[0]['CodigoPostal'];
			}
			if (!empty($data[0]['Municipio'])) {
				$municipio = $data[0]['Municipio'];
			}
			echo json_encode(array('empresa' => $data[0]['RazonSocial'], 'domicilio' => $domicilio, 'cp' => $cp, 'municipio' => $municipio, 'telefono' => $data[0]['Telefono']));
		} else {
			echo 'false';
		}

	} else if ($accion == 'obra') {
		$jsonObras = array();
		$data = get_db_data(array('get-todos-proyectos'));
		foreach ($data as $d) {
			$jsonObras[] = array(
                'id' => $d['CodigoProyecto'],
                'value' => utf8_encode(ucfirst($d['Proyecto'])),
                'desc' => utf8_encode(ucfirst($d['Descripcion']))
            );
		}
		$json[] = array('jsonObras' => $jsonObras);
        echo json_encode($json);

	} else if ($accion == 'empresa') {
		$jsonEmpresas = array();
		$data = get_db_data(array('get-todas-empresas'));
		foreach ($data as $d) {
			$jsonEmpresas[] = array(
                'id' => $d['CifDni'],
                'value' => utf8_encode(ucfirst($d['RazonSocial'])),
                'domicilio' => utf8_encode(ucfirst($d['Domicilio'])),
                'cp' => $d['CodigoPostal'],
                'municipio' => utf8_encode(ucfirst($d['Municipio'])),
                'telefono' => $d['Telefono']
            );
		}
		$json[] = array('jsonEmpresas' => $jsonEmpresas);
        echo json_encode($json);

	} else if ($accion == 'admin') {
		$filtro = '';
		if (cP('cifEmpresa') != '') {
			$empresa = cP('cifEmpresa');
			$filtro = " WHERE CifEmpresa='".$empresa."' ";
		}
		$jsonAdministradores = array();
		$data = get_db_data(array('get-todos-admins', $filtro));
		foreach ($data as $d) {
			$jsonAdministradores[] = array(
                'id' => $d['CodigoAdministrador'],
                'value' => utf8_encode(ucfirst($d['Nombre'])),
                'dni' => $d['Dni'],
                'cargo' => utf8_encode(ucfirst($d['Cargo'])),
                'cifEmpresa' => $d['CifEmpresa']
            );
		}
		$json[] = array('jsonAdministradores' => $jsonAdministradores);
        echo json_encode($json);

	} else if ($accion == 'eliminarAdmin') {
		$eliminar = '';
		if (cP('id') != '') {
			$id = cP('id');
			$eliminar = get_db_data(array('eliminar-administrador', $id));
		}
		echo json_encode(array('eliminado' => $eliminar));
	} else if ($accion == 'generar') {
		$tipo = cP('tipo');
		$fechaNumero = cP('fecha');
		$fecha = convertDateToLetter(cP('fecha'));
		$cif = cP('cif');
		$nombre = cP('nombre');
		$domicilio = cP('domicilio');
		$cp = cP('codigo_postal');
		$municipio = cP('municipio');
		$admin = cP('admin');
		$dnis = cP('dni');
		$dni = $dnis[0]; // si vuelve a salir el dni, el primero siempre sera para el primer admin, que sera obligatorio
		$cargo = cP('cargo'); // aunque metan varios administradores, van a tener el mismo cargo!
		$cargoUnico = $cargo[0];
		$obra = cP('obra');
		$forma = cP('forma');
		$dias = cP('dias');
		$codigoExpediente = cP('codigoExpediente');
		$mercantil_admin = nombreMercantilAdmin($tipo, $nombre, $admin);
		$mercantil_admin_mayus = strtoupper($mercantil_admin);
		$sociedad = true; // para subcontratas. Normalmente es sociedad hasta que sea un administrador (si no viene admin pues es autonomo)

		/* pueden traer varios admin 16/06/2017 */
		/* el primero lo voy a guardar tal cual por separado en variables, pero los demas voy a hacer la cadena seguida */
		$otrosAdmins = '';
		$contador = 0;
		if (is_array($admin)) {
			foreach ($admin as $a) {
				if ($contador == 0) {
					$admin = $a;
				} else {
					/* si está este admin, va a tener dni tambien, asi que lo cojo directamente del array dnis[] */
					$otrosAdmins .= ', y <strong>D. '. $a .'</strong>, mayor de edad, con DNI ' . $dnis[$contador];
				}
				$contador++;
			}
		} else {
			// si no viene un administrador, es que el nombre de la empresa que puso antes es el nombre del 'administrador' que es autonomo
			$sociedad = false;
		}

		/* traer el documento y cambiarle los párrafos */
		$doc = modeloSegunTipo($tipo);
		$nombreDoc = '';

		/* DATOS COMUNES */
		$doc = mb_convert_encoding($doc, 'HTML-ENTITIES', "UTF-8");
		$doc = str_replace("{FECHA_NUMERO}", $fechaNumero, $doc);
		$doc = str_replace("{FECHA}", $fecha, $doc);
		$doc = str_replace("{NOMBRE}", $nombre, $doc); // NOMBRE DE LA EMPRESA (MERCANTIL)
		$doc = str_replace("{DOMICILIO}", $domicilio, $doc);
		$doc = str_replace("{CIF}", $cif, $doc);
		$doc = str_replace("{CARGO}", $cargo, $doc);
		$doc = str_replace("{ADMIN}", $admin, $doc); // NOMBRE DEL ADMINISTRADOR
		$doc = str_replace("{DNI}", $dni, $doc);
		$doc = str_replace("{OBRA}", $obra, $doc); // NOMBRE LARGO DE LA OBRA
		$doc = str_replace("{OTROS_ADMINS}", $otrosAdmins, $doc); // DIRECTAMENTE LA FRASE DE LOS NOMBRES + DNI OTROS ADMIN
		
		/* SUBCONTRATA */
		if ($tipo == "subcontrata_con_aval" || $tipo == "subcontrata_cerrado_con_retenciones" || $tipo == "subcontrata_cerrado_sin_retenciones" || $tipo == "subcontrata_abierto_con_retenciones" || $tipo == "subcontrata_abierto_sin_retenciones") {
			
			$trabajos = cP('trabajos'); // DESCRIPCION DE TRABAJOS
			$importe = cP('importe');
			$telefono = cP('telefono');
			$pago_fraccionado = cP('pago_fraccionado');
			$plazo_estipulado = cP('plazo_estipulado');
			$fraccionado_descrip = '';
			$fecha_plazo = '';
			$penalizacion = '';

			if ((int)$pago_fraccionado === 1) {
				$fraccionado_descrip = nl2br(cP('fraccionado_descrip'));
				$array_lineas = explode('<br />', $fraccionado_descrip);
				$pago_fraccionado_parrafo = 'La forma de pago que establecen las partes para este contrato se establece de la siguiente forma:<br/><ul>';

				foreach ($array_lineas as $linea) {
					$pago_fraccionado_parrafo .= '<li>'.$linea.'</li>';
				}

				$pago_fraccionado_parrafo .= '</ul>';
				$doc = str_replace("{FRACCIONADO_DESCRIP}", $pago_fraccionado_parrafo, $doc);
			} else {
				$doc = str_replace("{FRACCIONADO_DESCRIP}", '', $doc);
			}

			// if ($plazo_estipulado == '1') { // si escribe algo en el textarea
			// 	$fecha_plazo = nl2br(utf8_decode(cP('fecha_plazo')));
			// 	$penalizacion = cP('penalizacion');
			// } else { // por defecto
			// 	$fecha_plazo = parrafoFechaPlazo();
			// }
			$doc = str_replace("{SOCIEDAD}", autonomoSociedad($sociedad, $admin, $dni, $otrosAdmins, $nombre, $domicilio, $cif), $doc);
			$doc = str_replace("{CODIGO_POSTAL}", $cp, $doc);
			$doc = str_replace("{MUNICIPIO}", $municipio, $doc);
			$doc = str_replace("{TRABAJOS}", $trabajos, $doc);
			$doc = str_replace("{TELEFONO}", $telefono, $doc);
			// $doc = str_replace("{FECHA_PLAZO}", '<p>'.$fecha_plazo.'</p>', $doc);
			$doc = str_replace("{FORMAS_PRECIO}", formasPrecio($tipo, $importe), $doc);
			$doc = str_replace("{RETENCION}", comprobarRetencion($tipo), $doc);
			$doc = str_replace("{FORMA_PAGO}", '5&#170;-1.-'.parrafoPago($forma,$dias), $doc);
			$doc = str_replace("{CARGO_UNICO}", $cargoUnico, $doc);

			$nombreDoc = 'subcontrata';
		}

		/* LIQUIDACION */
		if ($tipo == "liquidacion_con_retencion" || $tipo == "liquidacion_con_retencion_autonomo" || $tipo == "liquidacion_sin_retencion" || $tipo == "liquidacion_sin_retencion_autonomo") {
			$fecha_ultima_factura = cP('fecha_ultima_factura');
			$numero_ultima_factura = cP('numero_ultima_factura');
			$importe_ultima_factura = cP('importe_ultima_factura');
			$fecha_contrato_original = cP('fecha_contrato_original');
			$importe_retencion = cP('importe_retencion');

			$doc = str_replace("{PARRAFO_ADMIN}", parrafoAdminLiquido($tipo, $admin, $dni, $domicilio, $nombre, $otrosAdmins, $cif), $doc);
			$doc = str_replace("{PARRAFO_TERCERA}", parrafoLiquidacionTercera($tipo, $importe_retencion), $doc);
			$doc = str_replace("{FECHA_ULTIMA_FACTURA}", $fecha_ultima_factura, $doc);
			$doc = str_replace("{NUMERO_ULTIMA_FACTURA}", $numero_ultima_factura, $doc);
			$doc = str_replace("{IMPORTE_ULTIMA_FACTURA}", $importe_ultima_factura, $doc);
			$doc = str_replace("{FECHA_CONTRATO_ORIGINAL}", $fecha_contrato_original, $doc);
			$doc = str_replace("{MERCANTIL}", $mercantil_admin, $doc);

			$nombreDoc = 'liquidación';
		}

		/* SUMINISTRO */
		if ($tipo == "suministro_autonomo" || $tipo == "suministro_sin_autonomo") {
			$fecha_oferta = cP('fecha_oferta');
			$numero_oferta = cP('numero_oferta');
			$fecha_suministro_ini = cP('fecha_suministro_ini');
			$fecha_suministro_fin = cP('fecha_suministro_fin');

			$doc = str_replace("{PARRAFO_ADMIN_SUMINISTRO}", parrafoAdminSuministro($tipo, $admin, $dni, $domicilio, $nombre, $cif, $otrosAdmins), $doc);
			$doc = str_replace("{MERCANTIL}", $mercantil_admin, $doc);
			$doc = str_replace("{MERCANTIL_MAYUS}", $mercantil_admin_mayus, $doc);
			$doc = str_replace("{NUMERO_OFERTA}", $numero_oferta, $doc);
			$doc = str_replace("{FECHA_OFERTA}", $fecha_oferta, $doc);
			$doc = str_replace("{FORMA_PAGO}", parrafoPago($forma,$dias), $doc);
			$doc = str_replace("{FECHA_INICIO}", $fecha_suministro_ini, $doc);
			$doc = str_replace("{FECHA_FIN}", $fecha_suministro_fin, $doc);

			$nombreDoc = 'suministro';
		}

		if ($tipo == "anexo_autonomo" || $tipo == "anexo_sin_autonomo") {
			$fecha_contrato = cP('fecha_anexo_contrato');
			$doc = str_replace("{PARRAFO_ADMIN_ANEXO}", parrafoAdminAnexo($tipo, $admin, $dni, $domicilio, $nombre, $cif, $otrosAdmins), $doc);
			$doc = str_replace("{FECHA_CONTRATO}", $fecha_contrato, $doc);

			$nombreDoc = 'anexo';
		}

		$nombreDoc = 'Contrato de '.$nombreDoc.' '.$admin.'.doc';

		$firma = 'EL SUBCONTRATISTA';
		if ($tipo == "suministro_autonomo" || $tipo == "suministro_sin_autonomo") {
			$firma = 'EL PROVEEDOR';
		}
		
		header('Content-type: application/vnd.ms-word; charset=utf-8');
		header("Content-Disposition: attachment;Filename=\"".$nombreDoc."\""); // solo funciona con comillas dobles!!!
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>";
		echo '<meta http-equiv=\'Content-Type\' content=\'text/html; charset=Windows-1252\'>';
		echo '<style>
				p.MsoFooter, li.MsoFooter, div.MsoFooter {
		            margin: 0cm;
		            margin-bottom: 0cm;
		            mso-pagination:widow-orphan;
		            font-size: 12.0 pt;
		        }
		        p.MsoFooter {
		            text-align: center;
		        }
		        @page Section1 {
		            margin: 2cm 2cm 2cm 2cm;
		            mso-page-orientation: portrait;
		            mso-footer:f1;
		        }
		        div.Section1 { page:Section1; }
				body{
					font-family:Calibri,sans-serif;
					font-size:9pt;
					text-align:justify
				}
				p.sangria{
					text-indent:3em;
				}
				p.doble_sangria{
					text-indent:4em;
				}
				div{
					margin:20px 0
				}
				.listaguion{
					list-style:none
				}
				.listaguion li:before{
					content:"-"
				}
				table{
					border:1px solid black;
					border-collapse:collapse;
					width:100%
				}
				table tr td{
					border:1px solid black
				}
				.tablanoborde, .tablanoborde tr td{
					border:0;
					padding-bottom:7px;
				}
				.centrar{
					text-indent:14em;
				}
			</style>
			<body>
				<div class="Section1">
					'.$doc.'
			        <div style="mso-element:footer" id="f1">
		                <div class="MsoFooter">
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    <strong>CHIRIVO CONSTRUCCIONES, S.L.</strong>
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                    <strong>'.$firma.'</strong>
		                    <br/><br/><br/>
		                    <p class=MsoFooter>
		                        <span style="mso-field-code:\' PAGE \'"></span>
		                    </p>
		                </div>
		            </div>
			    </div>
		    </body>
		</html>'; // tiene que ser con espacios, no se pueden insertar tablas en el footer de un word

		/* crear ese administrador */
		// if ($admin != '') {
		// 	$administrador = store_db_data(array('insertar-administrador', "'".utf8_decode($admin)."','".utf8_decode($dni)."','".utf8_decode($cargo)."','".utf8_decode($cif)."'"));		
		// }

		/* insertar contrato en la bd */
		store_db_data(array('insertar-contrato', "'".$nombre."','".$obra."','".nombreSegunTipo($tipo)."', '" .$codigoExpediente. "' "));
		exit;
	} else if ($accion == 'obtenerContratos') {
		$fechaInicio = cP('fechaInicio');
		$fechaFin = cP('fechaFin');
		$usuario = cP('usuario');
		$mercantil = cP('mercantil');
		$obra = cP('obra');
		$contrato = cP('contrato');
		$expediente = cP('expediente');
		$jsonContratos=array();
		$data = get_db_data(array('obtener-contratos'));
		foreach ($data as $d) {
			$exp = $d['CodigoExpediente'];
			if ($exp == '') {
				$exp = ''; // para que no salgan los null
			}
			$jsonContratos[]= array(
				'id' => $d['IdUnico'],
				'fecha' => $d['FechaRegistro']->format('d/m/Y'),
				'usuarioDescarga' => utf8_encode(ucfirst($d['UsuarioDescarga'])),
				'nombreMercantil' => utf8_encode(ucfirst($d['NombreMercantil'])),
				'obra' => utf8_encode(ucfirst($d['NombreObra'])),
				'tipo' => utf8_encode(ucfirst($d['TipoContrato'])),
				'expediente' => $exp
			);
		}
		$json = array('jsonContratos' => $jsonContratos);
		echo json_encode($json);
	} 
}

function nombreSegunTipo($tipo) {
	switch ($tipo) {
	    case "subcontrata_con_aval": 				return 'Subcontrata con aval';
	    case "subcontrata_cerrado_con_retenciones": return 'Subcontrata precio cerrado con retenciones';
	    case "subcontrata_cerrado_sin_retenciones": return 'Subcontrata precio cerrado sin retenciones';
	    case "subcontrata_abierto_con_retenciones": return 'Subcontrata precio abierto con retenciones';
	    case "subcontrata_abierto_sin_retenciones": return 'Subcontrata precio abierto sin retenciones';
	    case "suministro_autonomo":					return 'Suministro autónomo';
	    case "suministro_sin_autonomo":				return 'Suministro no autónomo';
	    case "liquidacion_con_retencion":			return 'Liquidación con retenciones';
	    case "liquidacion_con_retencion_autonomo":	return 'Liquidación con retenciones autónomo';
	    case "liquidacion_sin_retencion":			return 'Liquidación sin retenciones';
	    case "liquidacion_sin_retencion_autonomo":	return 'Liquidación sin retenciones autónomo';
	    case "anexo_autonomo":						return 'Anexo autónomo';
	    case "anexo_sin_autonomo":					return 'Anexo no autónomo';
	    default:									return $tipo;
	}
}

function modeloSegunTipo($tipo) {
	switch ($tipo) {
	    case "subcontrata_con_aval": 				return modeloSubcontrata();
	    case "subcontrata_cerrado_con_retenciones": return modeloSubcontrata();
	    case "subcontrata_cerrado_sin_retenciones": return modeloSubcontrata();
	    case "subcontrata_abierto_con_retenciones": return modeloSubcontrata();
	    case "subcontrata_abierto_sin_retenciones": return modeloSubcontrata();
	    case "suministro_autonomo":					return modeloSuministro();
	    case "suministro_sin_autonomo":				return modeloSuministro();
	    case "liquidacion_con_retencion":			return modeloLiquidacion();
	    case "liquidacion_con_retencion_autonomo":	return modeloLiquidacion();
	    case "liquidacion_sin_retencion":			return modeloLiquidacion();
	    case "liquidacion_sin_retencion_autonomo":	return modeloLiquidacion();
	    case "anexo_autonomo":						return modeloAnexo();
	    case "anexo_sin_autonomo":					return modeloAnexo();
	    default:									return modeloSubcontrata();
	}
}

function modeloSubcontrata() {
	$doc = file_get_contents("docs/subcontrata-anexos-general.php");
	return $doc;
}

function modeloSuministro() {
	$doc = file_get_contents("docs/suministro.php");
	return $doc;
}

function modeloLiquidacion() {
	$doc = file_get_contents("docs/liquidacion.php");
	return $doc;
}

function modeloAnexo() {
	$doc = file_get_contents("docs/anexo.php");
	return $doc;
}

/* si es autonomo o no, cuando el párrafo dice ENTRE CHIRIVO Y ***** puede ser o el nombre de la mercantil o el nombre del administrador (en caso de autonomo) */
function nombreMercantilAdmin($tipo, $mercantil, $admin) {
	switch ($tipo) {
	    case "suministro_autonomo":					return $admin;
	    case "suministro_sin_autonomo":				return $mercantil;
	    case "liquidacion_con_retencion":			return $mercantil;
	    case "liquidacion_con_retencion_autonomo":	return $admin;
	    case "liquidacion_sin_retencion":			return $mercantil;
	    case "liquidacion_sin_retencion_autonomo":	return $admin;
	    default:									return $mercantil;
	}
}

/* parrafo del anexo */
function parrafoAdminAnexo($tipo, $admin, $dni, $domicilio, $nombre, $otrosAdmins, $cif) {
	$p = '';
	if ($tipo === 'anexo_sin_autonomo') {
		$p = 'Y de otra, <strong>D. '.$admin.'</strong>, mayor de edad, con DNI '.$dni.$otrosAdmins.' y actuando en su calidad de apoderado de la mercantil <strong>'.$nombre.'</strong> con domicilio a efectos de notificaciones en '.$domicilio.' y C.I.F. '.$cif.'. En adelante EL SUBCONTRATISTA.';
	} else if ($tipo === 'anexo_autonomo') {
		$p = 'Y de otra, <strong>D. '.$admin.'</strong>, mayor de edad, con DNI '.$dni.', actuando en su propio nombre y derecho y con domicilio a efectos de notificaciones  en '.$domicilio.'. En adelante EL SUBCONTRATISTA.';
	}
	return $p;
}

/* parrafo del suministro */
function parrafoAdminSuministro($tipo, $admin, $dni, $domicilio, $nombre, $otrosAdmins, $cif) {
	$p = '';
	if ($tipo === 'suministro_sin_autonomo') {
		$p = 'Y de otra, <strong>D. '.$admin.'</strong>, mayor de edad, con DNI '.$dni.$otrosAdmins.' y actuando en su calidad de apoderado de la mercantil <strong>'.$nombre.'</strong> con domicilio a efectos de notificaciones en '.$domicilio.' y C.I.F. '.$cif.'. En adelante EL PROVEEDOR.';
	} else if ($tipo === 'suministro_autonomo') {
		$p = 'Y de otra, <strong>D. '.$admin.'</strong>, mayor de edad, con DNI '.$dni.', actuando en su propio nombre y derecho y con domicilio a efectos de notificaciones  en '.$domicilio.'. En adelante EL PROVEEDOR.';
	}
	return $p;
}

/* parrafos del modelo liquidación */
function parrafoAdminLiquido($tipo, $admin, $dni, $domicilio, $nombre, $otrosAdmins, $cif) { // se modifica el segundo parrafo del administrador
	$p = '';
	if ($tipo === 'liquidacion_sin_retencion' || $tipo === 'liquidacion_con_retencion') {
		$p = 'Y de otra, <strong>D. '.$admin.'</strong>, mayor de edad, con DNI '.$dni.$otrosAdmins.' y actuando en su calidad de apoderado de la mercantil <strong>'.$nombre.'</strong> con domicilio a efectos de notificaciones en '.$domicilio.' y C.I.F. '.$cif.'. En adelante EL SUBCONTRATISTA.';
	} else if ($tipo === 'liquidacion_sin_retencion_autonomo' || $tipo === 'liquidacion_con_retencion_autonomo') {
		// al ser autonomo, el admin viene vacio, por lo que se escribe el nombre de la mercantil (que sera el del admin realmente)
		$p = 'Y de otra, <strong>D. '.$nombre.'</strong>, mayor de edad, con DNI '.$cif.', actuando en su propio nombre y derecho y con domicilio a efectos de notificaciones  en '.$domicilio.'. En adelante EL SUBCONTRATISTA.';
	}
	return $p;
}

function parrafoLiquidacionTercera($tipo, $importe_retencion) { //tanto sin/con como autonomo o sin autonomo
	$p = '';
	if ($tipo === 'liquidacion_sin_retencion' || $tipo === 'liquidacion_sin_retencion_autonomo' ) {
		$p = ' EL SUBCONTRATISTA declara que no existe ninguna retenci&oacute;n en poder de CHIRIVO CONSTRUCCIONES, S.L, referente a los trabajos realizados, en concreto, la relativa a la garant&iacute;a de la perfecta ejecuci&oacute;n de los trabajos realizados durante el per&iacute;odo de garant&iacute;a de la obra, as&iacute; como de otras potenciales responsabilidades imputables al SUBCONTRATISTA.';
	} else if ($tipo === 'liquidacion_con_retencion' || $tipo === 'liquidacion_con_retencion_autonomo') {
		$p = 'Como &uacute;nica excepci&oacute;n a lo indicado, EL SUBCONTRATISTA declara que en poder de CHIRIVO CONSTRUCCIONES, S.L, existe una retenci&oacute;n del 5%, ascendente a '.$importe_retencion.' &euro;, relativo a los trabajos realizados, en garant&iacute;a de la perfecta ejecuci&oacute;n de los trabajos realizados durante el per&iacute;odo de garant&iacute;a de la obra, as&iacute; como de otras potenciales responsabilidades imputables al SUBCONTRATISTA.';
	}
	return $p;
}

/* parrafos del modelo subcontrata segun el tipo (precio abierto/cerrado - con retención o sin) */
function parrafoPago($tipo, $dias) { // formas de pago: confirming, pagaré, trasferencia, contado
	$p = '';
		if ($tipo == 1) {
			$p = 'CHIRIVO CONSTRUCCIONES, S.L abonar&aacute; al '.$nombre.' las '.$objeto.' mensuales de obra ejecutada, mediante confirming a trav&eacute;s de una entidad financiera, con vencimiento el d&iacute;a de pago a los '.$dias.' d&iacute;as de la fecha de recepci&oacute;n factura de conformidad, siendo d&iacute;a de pago el d&iacute;a 25 de cada mes, y si el d&iacute;a fuese s&aacute;bado o festivo, el pr&oacute;ximo d&iacute;a laborable, con excepci&oacute;n del d&iacute;a 25 de Agosto, que pasar&aacute;n al siguiente d&iacute;a de pago.';
		} else if ($tipo == 2) {
			$p = 'CHIRIVO CONSTRUCCIONES, S.L abonar&aacute; al '.$nombre.' las '.$objeto.' mensuales de obra ejecutada, mediante pagar&eacute;s aceptados con vencimientos el d&iacute;a de pago a los '.$dias.' d&iacute;as de la fecha de recepci&oacute;n factura de conformidad, siendo d&iacute;a de pago el 25 de cada mes, y si dicho d&iacute;a fuese s&aacute;bado o festivo, el pr&oacute;ximo d&iacute;a laborable, con excepci&oacute;n del d&iacute;a 25 de Agosto, que pasar&aacute;n al siguiente d&iacute;a de pago.';
		} else if ($tipo == 3) {
			$p = 'CHIRIVO CONSTRUCCIONES, S.L abonar&aacute; al '.$nombre.' las '.$objeto.' mensuales de obra ejecutada, mediante transferencia bancaria al n&uacute;mero de cuenta que facilite el SUBCONTRATISTA y del que sea &eacute;ste titular &uacute;nico; dicha transferencia bancaria se realizar&aacute; a partir de la fecha de recepci&oacute;n factura de conformidad, siendo d&iacute;a de pago el d&iacute;a 25 de cada mes, y si dicho d&iacute;a fuese s&aacute;bado o festivo, el pr&oacute;ximo d&iacute;a laborable, con excepci&oacute;n del d&iacute;a 25 de Agosto, que pasar&aacute;n al siguiente d&iacute;a de pago.';
		} else if ($tipo == 4) {
			$p = 'CHIRIVO CONSTRUCCIONES, S.L abonar&aacute; al '.$nombre.' las '.$objeto.' mensuales de obra ejecutada. Se realizar&aacute; el pago al contado a partir de la fecha de recepci&oacute;n factura de conformidad, siendo d&iacute;a de pago el d&iacute;a 25 de cada mes, y si dicho d&iacute;a fuese s&aacute;bado o festivo, el pr&oacute;ximo d&iacute;a laborable, con excepci&oacute;n del d&iacute;a 25 de Agosto, que pasar&aacute;n al siguiente d&iacute;a de pago.';
		}
		return $p;
}
function autonomoSociedad($sociedad = true, $admin, $dni, $otrosAdmins, $nombreMercantil, $domicilio, $cif) { // o es autonomo o es sociedad en los contratos de subcontrata
	$p = '  <p class="sangria">
				Y de otra, <strong>D. '.$admin.'</strong>, mayor de edad, 
				con DNI '.$dni.''.$otrosAdmins.' y actuando en su calidad de apoderado de la mercantil 
				<strong>'.$nombreMercantil.'</strong> con domicilio a efectos de notificaciones en 
				'.$domicilio.' 
				y C.I.F. '.$cif.'. 
				En adelante EL SUBCONTRATISTA.
			</p>';
	if (!$sociedad) { // es un autonomo
		$p = '  <p class="sangria">
					Y de otra, <strong>D. '.$nombreMercantil.'</strong>, mayor de edad, con DNI '.$cif.', actuando en su propio nombre y derecho y con domicilio a efectos de notificaciones en '.$domicilio.' . En adelante EL SUBCONTRATISTA.
				</p>';
	}	
	return $p;
}

function formasPrecio($tipo, $importe) {
	$p = '';
	// por defecto ponia: DIEZ MIL CUATROCIENTOS NUEVE EUROS CON OCHENTA Y CUATRO C&Eacute;NTIMOS (10.409,84 &euro;)
	if ($tipo === 'subcontrata_cerrado_con_retenciones' || $tipo === 'subcontrata_cerrado_sin_retenciones') {
		// $importeLetras = strtoupper(convertNumberToLetter($importe, true)); // true = es moneda
		// $importeLetras = strtoupper(convert_number_to_words($importe));
		include_once 'NumeroALetras.class.php';
		$numeroLetras = new NumeroALetras();
		$importeLetras = $numeroLetras->convertir($importe, 'EUROS', 'CÉNTIMOS');
		$importeLetras = utf8_decode(strtoupper($importeLetras));
		$fraseImporte = $importeLetras . ' (' . $importe . ' &euro;)';

		$p = '1&#170;-1.- EL SUBCONTRATISTA acepta llevar a cabo los trabajos a que se refiere su oferta redactada en ANEXO I del presente contrato por un importe de <strong>'.$fraseImporte.', IVA incluido</strong>, de acuerdo con el Proyecto de la Obra a que pertenece, que declara conocer, y en las condiciones de su citada oferta, en lo que no resulte modificada por el presente contrato y ANEXO-1.';
	} else if ($tipo === 'subcontrata_abierto_con_retenciones') {
		$p = '1&#170;-1.-El precio del presente contrato resultar&aacute; del producto de multiplicar las mediciones por el precio unitario de cada unidad de obra, de acuerdo con los precios que se adjuntan en el <strong>Anexo n&uacute;m. I</strong> de este contrato. Los trabajos a realizar por el SUBCONTRATISTA deber&aacute;n adecuarse al Proyecto de la Obra a la que pertenece, que declara conocer.';
	}
	return $p;
}

function comprobarRetencion($tipo) {
	switch ($tipo) {
	    case "subcontrata_con_aval": 				return '';
	    case "subcontrata_cerrado_con_retenciones": return '';
	    case "subcontrata_cerrado_sin_retenciones": return 'no ';
	    case "subcontrata_abierto_con_retenciones": return '';
	    case "subcontrata_abierto_sin_retenciones": return 'no ';
	    default:									return '';
	}
}

function parrafoFechaPlazo() { //por defecto
	$p = 'Los trabajos contratados comenzar&aacute;n de inmediato y se finalizar&aacute;n conforme a las necesidades del ritmo de las obras.';
	return $p;
}

function convertDateToLetter($date) {
	$arrayMeses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
	//La fecha se supone en formato dd-mm-yyyy	
	$arrayFecha = explode('/', $date);
	$dia = ltrim($arrayFecha[0], '0'); // Elimino un cero al principio del dia si lo hay
	$mes = ltrim($arrayFecha[1], '0'); // Elimino un cero al principio del mes si lo hay
	$anio = $arrayFecha[2];
	return convertNumberToLetter($dia).' de '.$arrayMeses[$mes].' de '. convertNumberToLetter($anio);
}

function convertNumberToLetter($num, $moneda = false) {
	$arrayUnidades = array('','uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte');
	$arrayDecenas = array('','', 'veinti', 'treinta', 'cuarenta', 'cincuenta', 'sesenta','setenta', 'ochenta', 'noventa');
	$arrayCentenas = array('','ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos');
	$arrayUnidadesMillar = array('', 'mil', 'dos mil', 'tres mil', 'cuatro mil', 'cinco mil', 'seis mil', 'siete mil', 'ocho mil', 'nueve mil');
	
	$resultado = '';
	// $tieneDecimales = false; // solo en el caso de que sea una moneda y tenga comas
	$arrayNum = str_split($num);
	// $arrayOriginal = $arrayNum;

	// if ($moneda) { // si quiere transformar una moneda, puede contener comas
	// 	if (strpos($num, ',') !== false) { // si tiene una coma... 
	// 		// entonces solo transformo la primera parte antes de la coma, después le concateno un 'CON' y hago los decimales otra vez
	// 	    $tieneDecimales = true;
	// 	    // encuentro la coma y obtengo su posicion del array, POR EJEMPLO LA POSICION (KEY): 5
	// 	    $posicionComa = array_search(',', $arrayNum);
	// 	    // formo otro array solo con las keys del array de numeros
	// 	    $n = array_keys($arrayNum);
	// 	    // ahora devuelvo la posicion de la posicion de la coma pero en el array de todas las keys
	// 		$count = array_search($posicionComa, $n);
	// 		// corto desde el index 0 como inicio y la posicion (desde donde quiero eliminar, como tb quiero quitar la coma) como longitud
	// 		$arrayNum = array_slice($arrayNum, 0, $count, true);
	// 		// obtengo ahora otro array solo con los decimales, la posicion de la coma + 1 (+1 para omitir la propia coma) y hasta el final del array original
	// 		$arrayDecimales = array_slice($arrayOriginal, $posicionComa+1, count($arrayOriginal), true);
	// 	}
	// }

	if (count($arrayNum) === 4) { // Número con 4 cifras	
		$resultado .= $arrayUnidadesMillar[$arrayNum[0]];	
	}
	
	if (count($arrayNum) >= 3) { // Número al menos 3 cifras	
		// Compruebo si las centenas es igual a 100
		if (implode(array_slice($arrayNum, count($arrayNum)-3)) === '100') {
			$resultado .= ' cien';
		} else {
			$resultado .= ' '.$arrayCentenas[$arrayNum[count($arrayNum)-3]];
		}
	}
	
	if (count($arrayNum) >= 2) { // Número al menos 2 cifras	
		// Compruebo si las centenas es menor que 21
		if ((int)implode(array_slice($arrayNum, count($arrayNum)-2)) < 21) {
			$resultado .= ' '.$arrayUnidades[implode(array_slice($arrayNum, count($arrayNum)-2))];
			// $unid = implode(array_slice($arrayNum, count($arrayNum)-2));
			// if ($unid[0] == '0') {
			// 	// si escribe ,01 debo coger sólo el UNO!!
			// 	$unid = $unid[1];
			// }
			// $escribir = $arrayUnidades[$unid];
			// if ($unid == '1') {
			// 	// y si encima es solo uno, no quiero que me escriba: CON uno CENTIMOS! debe poner con UN CENTIMO!!
			// 	$escribir = 'un';
			// }
			// $resultado .= ' '.$escribir;
		} else {		
			$resultado .= ' '.$arrayDecenas[$arrayNum[count($arrayNum)-2]];
			$espacio = ' ';	
			if ((int )$arrayNum[count($arrayNum)-2] != 2) {
				$resultado .= ' y';
			} else {
				$espacio = '';
			}
			$resultado .= $espacio.$arrayUnidades[$arrayNum[count($arrayNum)-1]];		
		}
	}
	
	if (count($arrayNum) === 1) { // Número con una cifra
		$resultado .= ' '.$arrayUnidades[$arrayNum[count($arrayNum)-1]];	
	}

	// if (count($arrayNum) >= 4 && $moneda) { // si era un número de 4 cifras o mayor, solo pasa con las monedas
	// 	// no coge el ultimo número, como por ejemplo: 5206,65 -> no coge el 6
	// 	$volverString = implode('',$arrayNum);
	// 	// comprobar si el segundo numero empezando por el final, es un 0, entonces pintar el numero unidades final
	// 	$segundoNumFinal = substr($volverString, -2, 1);
	// 	if ($segundoNumFinal == '0') {
	// 		$resultado .= ' '.$arrayUnidades[$arrayNum[count($arrayNum)-1]];
	// 	}
	// }

	// if ($tieneDecimales && $moneda) {
	// 	$resultado .= ' EUROS CON ';
	// 	$volverString = implode('',$arrayDecimales);
	// 	$resultado .= convertNumberToLetter($volverString, false); // ya no quiero que me compruebe las comas!
	// 	$resultado .= utf8_decode(' CÉNTIMOS');
	// }

	return $resultado;
}

// function convert_number_to_words($number) {

//     $hyphen      = '-';
//     $conjunction = ' y ';
//     $separator   = ' ';
//     $negative    = 'negativo ';
//     $decimal     = ' CON ';
//     $dictionary  = array(
//         0                   => 'cero',
//         1                   => 'uno',
//         2                   => 'dos',
//         3                   => 'tres',
//         4                   => 'cuatro',
//         5                   => 'cinco',
//         6                   => 'seis',
//         7                   => 'siete',
//         8                   => 'ocho',
//         9                   => 'nueve',
//         10                  => 'diez',
//         11                  => 'once',
//         12                  => 'doce',
//         13                  => 'trece',
//         14                  => 'catorce',
//         15                  => 'quince',
//         16                  => 'dieciséis',
//         17                  => 'diecisiete',
//         18                  => 'dieciocho',
//         19                  => 'diecinueve',
//         20                  => 'veinte',
//         21                  => 'veintiun', // qué rico es el lenguaje español
//         29                  => 'veintinueve', // qué rico es el lenguaje español
//         30                  => 'treinta',
//         40                  => 'cuarenta',
//         50                  => 'cincuenta',
//         60                  => 'sesenta',
//         70                  => 'sesenta',
//         80                  => 'ochenta',
//         90                  => 'noventa',
//         100                 => 'cien',
//         '100s'              => 'ciento', // qué rico es el lenguaje español
//         200              	=> 'doscientos',
//         300             	=> 'trescientos',
//         400              	=> 'cuatrocientos',
//         500              	=> 'quinientos', // qué rico es el lenguaje español
//         600              	=> 'seiscientos',
//         700              	=> 'setecientos', // qué rico es el lenguaje español
//         800              	=> 'ochocientos',
//         900              	=> 'novecientos', // qué rico es el lenguaje español
//         1000                => 'mil',
//         1000000             => 'millones',
//         1000000000          => 'billones',
//         1000000000000       => 'trillones',
//         1000000000000000    => 'cuadrillones',
//         1000000000000000000 => 'quintillones'
//     );

//     if (!is_numeric($number)) {
//         return false;
//     }

//     if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
//         // overflow
//         // trigger_error(
//         //     'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
//         //     E_USER_WARNING
//         // );
//         return false;
//     }

//     if ($number < 0) {
//         return $negative . convert_number_to_words(abs($number));
//     }

//     $string = $fraction = null;

//     if (strpos($number, '.') !== false) {
//         list($number, $fraction) = explode('.', $number);
//     }

//     switch (true) {
//         case $number < 21:
//             $string = $dictionary[$number];
//             break;
//         case $number == 21 || $number == 29:
//             $string = $dictionary[$number];
//             break;
//         case $number < 100:
//             $tens   = ((int) ($number / 10)) * 10;
//             $units  = $number % 10;
//             $string = $dictionary[$tens];
//             if ($units) {
//                 $string .= $conjunction . $dictionary[$units];
//             }
//             break;
//         case $number < 1000:
//             $hundreds  = $number / 100;
//             $remainder = $number % 100;
//             $primeraCifra = (int)$hundreds;
//             $string = $dictionary[$primeraCifra.'00'];
//             if ($primeraCifra == '1') {
//             	$string = $dictionary['100s'];
//             }
//             if ($remainder) {
//             	// quitarle la conjuncion 'y' si era ciento y una unica unidad: ejemplo: 1502 -> que no ponga mil quinientos Y dos
//     //         	$segundoNumFinal = substr($hundreds, -2, 1);
// 				// if ($segundoNumFinal != '0') {
// 				// 	$string .= $conjunction;
// 				// } else {
// 				$string .= $separator;
// 				// }
// 				// ciento un EUROS, no ciento uno EUROS
// 				if ($remainder == '1') {
// 					$string .= 'y un';
// 				} else {
// 					$string .= convert_number_to_words($remainder);
// 				}
//             }
//             break;
//         default:
//             $baseUnit = pow(1000, floor(log($number, 1000)));
//             $numBaseUnits = (int) ($number / $baseUnit);
//             $remainder = $number % $baseUnit;
//             $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
//             if ($remainder) {
//                 $string .= $remainder < 100 ? $conjunction : $separator;
//                 $string .= convert_number_to_words($remainder);
//             }
//             break;
//     }

//     if (null !== $fraction && is_numeric($fraction)) {
//         // $string .= $decimal;
//         // $words = array();
//         // foreach (str_split((string) $fraction) as $number) {
//         //     $words[] = $dictionary[$number];
//         // }
//         // $string .= implode(' ', $words);
//         $string .= ' EUROS CON ';
// 		$string .= convertNumberToLetter($fraction, false); // ya no quiero que me compruebe las comas!
// 		$string .= utf8_decode(' CÉNTIMOS');
//     }

//     return $string;
// }

?>