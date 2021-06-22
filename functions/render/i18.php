<?php
// ini_set('error_reporting', E_ALL^E_NOTICE);
// ini_set('display_errors', 'on');
header("Content-Type: text/html; charset=ISO-8859-1");

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;

function render_tabla_i18 () {

	$trs = '';
	$custom_query =	' WHERE CodigoEmpresa=' . $_SESSION['company_id'] . ' ';

	/* limpiar la session por si quiere borrarlo todo */
	$_SESSION['proyectos'] = array();
	$_SESSION['proveedores'] = array();
	$_SESSION['articulos'] = array();
	$_SESSION['contratos'] = array();
	$_SESSION['anexos'] = array();
	
	if (cG('project')) { // se pone asi porque el que hizo la funcion de listar_proyectos es cG('project') para autoseleccionar
		$proyectos = cG('project');
		if ($proyectos[0] != 0) { // si el primero que pone es todos los proyectos
			$contador = 0;
			$where = '';
			foreach ($proyectos as $pr) {
				if ($contador == 0) {
					$where = '('; //empezar la condicion
				}
				if ($contador > 0 && $contador < count($proyectos)) {
					$where .= ' OR ';
				}
				$where .= ' CodigoProyecto=' . $pr;
				$contador++;
			}
			if ($where != '') {
				$where .= ')'; //cerrar la condicion
				$custom_query .= ' AND ' . $where;
				$_SESSION['proyectos'] = $proyectos;
			}
		}
	}

	if (cG('pr')) { // se pone asi porque el que hizo la funcion de listar_proyectos es cG('project') para autoseleccionar
		$proveedores = cG('pr');
		if ($proveedores[0] != 0) { // si el primero que pone es todos los proyectos
			$contador = 0;
			$where = '';
			foreach ($proveedores as $pr) {
				if ($contador == 0) {
					$where = '('; //empezar la condicion
				}
				if ($contador > 0 && $contador < count($proveedores)) {
					$where .= ' OR ';
				}
				$where .= ' codigoproveedor=' . $pr;
				$contador++;
			}
			if ($where != '') {
				$where .= ')'; //cerrar la condicion
				$custom_query .= ' AND ' . $where;
				$_SESSION['proveedores'] = $proveedores;
			}
		}
	}

	if (cG('estados')) {
		$estados = cG('estados');
		if ($estados != 'todos') { // si no quiere ver todas las obras...
			if ($estados == 'activos') {
				$estados = '0'; // es finalizado, es decir, que significa lo contrario, finalizado = 0 >>>> estado = 0 >> ACTIVA
			} else {
				$estados = '-1'; // estan finalizadas, es decir, inactivas
			}
			$custom_query .= ' AND finalizado=' . $estados;
		}
	}

	if (cG('articulos')) { // se pone asi porque el que hizo la funcion de listar_proyectos es cG('project') para autoseleccionar
		$articulos = cG('articulos');
		if ($articulos[0] != 0) { // si el primero que pone es todos los proyectos
			$contador = 0;
			$where = '';
			foreach ($articulos as $ar) {
				if ($contador == 0) {
					$where = '('; //empezar la condicion
				}
				if ($contador > 0 && $contador < count($articulos)) {
					$where .= ' OR ';
				}
				$where .= " codigoarticulo='" . $ar . "' ";
				$contador++;
			}
			if ($where != '') {
				$where .= ')'; //cerrar la condicion
				$custom_query .= ' AND ' . $where;
				$_SESSION['articulos'] = $articulos;
			}
		}
	}

	if (cG('contratos')) { // se pone asi porque el que hizo la funcion de listar_proyectos es cG('project') para autoseleccionar
		$contratos = cG('contratos');
		if ($contratos[0] != 0) { // si el primero que pone es todos los proyectos
			$contador = 0;
			$where = '';
			foreach ($contratos as $ar) {
				if ($contador == 0) {
					$where = '('; //empezar la condicion
				}
				if ($contador > 0 && $contador < count($contratos)) {
					$where .= ' OR ';
				}
				$where .= ' codigoseccion=' . $ar;
				$contador++;
			}
			if ($where != '') {
				$where .= ')'; //cerrar la condicion
				$custom_query .= ' AND ' . $where;
				$_SESSION['contratos'] = $contratos;
			}
		}
	}

	if (cG('anexos')) { // se pone asi porque el que hizo la funcion de listar_proyectos es cG('project') para autoseleccionar
		$anexos = cG('anexos');
		if ($anexos[0] != 0) { // si el primero que pone es todos los proyectos
			$contador = 0;
			$where = '';
			foreach ($anexos as $ar) {
				if ($contador == 0) {
					$where = '('; //empezar la condicion
				}
				if ($contador > 0 && $contador < count($anexos)) {
					$where .= ' OR ';
				}
				$where .= " codigodepartamento='" . $ar . "'";
				$contador++;
			}
			if ($where != '') {
				$where .= ')'; //cerrar la condicion
				$custom_query .= ' AND ' . $where;
				$_SESSION['anexos'] = $anexos;
			}
		}
	}

	if (cG('start')) {
		$fecha_inicio = cG('start');
		if ($fecha_inicio != '') {
			$fi = "FechaAlbaran>='" . sql_date($fecha_inicio,'es') . "' ";

			$custom_query .= ' AND ' . $fi;
		}
	}

	if (cG('end')) {
		$fecha_fin = cG('end');
		if ($fecha_fin != '') {
			$ff = "FechaAlbaran<='" . sql_date($fecha_fin,'es') . "' ";

			$custom_query .= ' AND ' . $ff;
		}
	}

	if (empty($_GET)) {
		$costes_desglosado = '';
	} else {
		$costes_desglosado = get_db_data(array('listar-costes-desglosado-i18', $custom_query));
	}

	if (cG('excel') == 'true') {
		$separator = ';';
			
		$csv = 'sep=;' . "\n\n";

		$hoy = date('d/m/Y');

		$csv .= 'INFORME COSTE DESGLOSADO'.$separator.$separator.$separator.$separator.'FECHA:'.$separator.$hoy."\n";
		$csv .= 'Fecha desde:' . $separator . $fecha_inicio."\n";
		$csv .= 'Fecha hasta:' . $separator . $fecha_fin . "\n\n";

		$csv.= 'Fecha albaran' . $separator . 'N. albaran'.$separator.'Serie albaran'.$separator.'RazonSocial'.$separator .'Fecha factura'.$separator .'Codigo articulo'.$separator
		. 'Descripcion articulo' . $separator . 'Unidades'.$separator .'Importe Unitario Neto'.$separator.'Importe Neto (B.I.)'.$separator . 'N. Su Factura'.$separator
		.'Fecha Su Factura' . $separator . 'Codigo proyecto' . $separator . 'Proyecto' . $separator.'Codigo contrato'.$separator .'Contrato'.$separator.'Codigo anexo'.$separator.'Anexo'.$separator."\n";

		if (is_array($costes_desglosado)) {
			$totalUnidades = 0;
			$totalImporteUnitario = 0;
			$totalImporteBaseImpo = 0;

			foreach ($costes_desglosado as $c) {
				$fechaFactura = '';
				$fechaSuFactura = '';
				if (isset($c['FechaFactura'])) {
					$fechaFactura = $c['FechaFactura']->format('d/m/Y');
				}
				if (isset($c['FechaSuFactura'])) {
					$fechaSuFactura = $c['FechaSuFactura']->format('d/m/Y');
				}

				$csv.=
						'"'		.trim(	$c['FechaAlbaran']->format('d/m/Y')				)		.'"'.$separator.''.
						'"'		.trim(	$c['NumeroAlbaran']								)		.'"'.$separator.''.
						'"'		.trim(	$c['SerieAlbaran']								)		.'"'.$separator.''.
						'"'		.trim(	$c['RazonSocial']								)		.'"'.$separator.''.
						'"'		.trim(	$fechaFactura 									)		.'"'.$separator.''.
						'"'		.trim(	$c['CodigoArticulo']							)		.'"'.$separator.''.
						'"'		.trim(	$c['DescripcionArticulo']						)		.'"'.$separator.''.
						''		.trim(	aux_money_format_noeuro($c['Unidades2_'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($c['PrecioRebaje'])		)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($c['BaseImponible'])	)		.''.$separator.''.
						'"'		.trim(	$c['SuFacturaNo']								)		.'"'.$separator.''.
						'"'		.trim(	$fechasufactura									)		.'"'.$separator.''.
						'"'		.trim(	$c['CodigoProyecto']							)		.'"'.$separator.''.
						'"'		.trim(	$c['Proyecto']									)		.'"'.$separator.''.
						'"'		.trim(	$c['CodigoSeccion']								)		.'"'.$separator.''.
						'"'		.trim(	$c['Seccion']									)		.'"'.$separator.''.
						'"'		.trim(	$c['CodigoDepartamento']						)		.'"'.$separator.''.
						'"'		.trim(	$c['Departamento']								)		.'"'.$separator.''.
						"\n";

				$totalUnidades += $c['Unidades2_'];
				$totalImporteUnitario += $c['PrecioRebaje'];
				$totalImporteBaseImpo += $c['BaseImponible'];
			}
			$csv.=
						'"TOTAL GENERAL"'.$separator.''.
						'""'.$separator.''.
						'""'.$separator.''.
						'""'.$separator.''.
						'""'.$separator.''.
						'""'.$separator.''.
						'""'.$separator.''.
						''		.trim(	aux_money_format_noeuro($totalUnidades)			)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($totalImporteUnitario)	)		.''.$separator.''.
						''		.trim(	aux_money_format_noeuro($totalImporteBaseImpo)	)		.''.$separator.''.
						"\n";
		}

		return $csv;

	} else if (cG('pdf') == 'true') { 
		$hoy = date('d/m/Y');

		$orientacion = 'L'; //Horizontal
		$tamFolio = 'A4';
		$titulo = 'Informe costes materiales desglosado';
		$nombrePDF = 'informe_costes_materiales';
		$modo = 'I'; //Se abre directamente, para abrir ventana de guardar y descargar cambiar por la 'D'

		$htmlFechas = '';
		if (!empty($fecha_inicio)) {
			$htmlFechas .= 'Fecha desde: <span class="fecha">' . $fecha_inicio . '</span><br>';
		}
		if (!empty($fecha_fin)) {
			$htmlFechas .= 'Fecha hasta: <span class="fecha">' . $fecha_fin . '</span><br>';
		}


		$html = '<style>
					td {
						font-size: 10px;
						border: 1px solid #dddddd; 
						padding: 3px;
						word-break:break-all;
					}

					table {
						border: 1px solid #dddddd; 
						border-collapse: collapse; 
						width: 100%; 
						margin-left: 6px;
					}

					.titulo {
						font-size: 20px;
					}

					.fechas {
						font-size: 13px;
						margin-left: 12px;
						margin-bottom: 20px;
					}

					.fecha {
						font-size: 13px;
					}

				</style>
				<p class="titulo" align=center>
					<b>' . $titulo .'</b>
		        </p>
		        <p class="fechas">
			        	Fecha: <span class="fecha">' . $hoy . '</span><br>'
			        	. $htmlFechas .
		        '</p>';

        $html .= '<table>'
			        . '<tr>'
				        . '<td>Fecha<br>albarán</td>'
				        . '<td>Nº<br>albarán</td>'
				        . '<td>Serie<br>albarán</td>'
				        . '<td>Razón social</td>'
				        . '<td>Descripción artículo</td>'
				        . '<td>Unds.</td>'
				        . '<td>Importe<br>unitario<br>neto</td>'
				        . '<td>Importe<br>Neto<br>(B.I.)</td>'
				        . '<td>N.<br>Su<br>Factura</td>'
				        . '<td>Fecha<br>Su<br>Factura</td>'
				        . '<td>Cód.<br>proyecto</td>'
				        . '<td>Proyecto</td>'
				        . '<td>Contrato</td>'
				        . '<td>Anexo</td>'
			        . '</tr>';

		if (is_array($costes_desglosado)) {
			$totalUnidades = 0;
			$totalImporteUnitario = 0;
			$totalImporteBaseImpo = 0;

			foreach ($costes_desglosado as $c) {
				$fechaSuFactura = '';
				if (isset($c['FechaSuFactura'])) {
					$fechaSuFactura = $c['FechaSuFactura']->format('d/m/Y');
				}

				$proyecto = str_replace("  ", " ", utf8_encode($c['Proyecto'])); //Hay un proyecto que al tener dos espacios falla
				$descripcion = str_replace("  ", " ", utf8_encode($c['DescripcionArticulo'])); //Pasa igual con la descripción...

				$html .= '<tr>'
							. '<td>' . trim($c['FechaAlbaran']->format('d/m/Y')) . '</td>'
							. '<td>' . trim($c['NumeroAlbaran']) . '</td>'
							. '<td>' . trim($c['SerieAlbaran']) . '</td>'
							. '<td style="width: 9%;">' . trim($c['RazonSocial']) . '</td>'
							. '<td style="width: 28%;">' . $descripcion . '</td>'
							. '<td>' . aux_money_format_noeuro($c['Unidades2_']) . '</td>'
							. '<td>' . aux_money_format_noeuro($c['PrecioRebaje']) . '</td>'
							. '<td>' . aux_money_format_noeuro($c['BaseImponible']) . '</td>'
							. '<td>' . trim($c['SuFacturaNo']) . '</td>'
							. '<td>' . $fechaSuFactura . '</td>'
							. '<td>' . trim($c['CodigoProyecto']) . '</td>'
							. '<td style="width: 9%;">' . $proyecto . '</td>'
							. '<td style="width: 7%;">' . trim($c['Seccion']) . '</td>'
							. '<td style="width: 7%;">' . trim($c['Departamento']) . '</td>'
						. '</tr>';

				$totalUnidades += $c['Unidades2_'];
				$totalImporteUnitario += $c['PrecioRebaje'];
				$totalImporteBaseImpo += $c['BaseImponible'];
			}

			$html .= '<tr>'
						. '<td style="font-size: 12px;">Total<br>general</td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td>'
						. '<td>' . trim(aux_money_format_noeuro($totalUnidades)) . '</td>'
						. '<td>' . trim(aux_money_format_noeuro($totalImporteUnitario)) . '</td>'
						. '<td>' . trim(aux_money_format_noeuro($totalImporteBaseImpo)) . '</td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td>'
						. '<td></td>'
					. '</tr>';
		}

		$html .= '</table>';

        $html2pdf = new Mpdf([
            "orientation" =>$orientacion,
            "tempDir" => "C:/tmp",
        ]);
        try {
            $html2pdf->SetDisplayMode('fullpage');
            $html2pdf->setMBencoding('UTF-8');
            $html2pdf->SetTitle($titulo);
            $html2pdf->WriteHTML($html);
            ob_end_clean(); //Sin esto no genera correctamente el PDF
            $html2pdf->Output($nombrePDF . '.pdf', $modo);
        } catch (MpdfException $e) {
            echo "Error generando PDF";
            die;
        }

    } else {
		$tbody = '';
		$tfoot = '';
		$n = 1;
		$totalUnidades = 0;
		$totalImporteUnitario = 0;
		$totalImporteBaseImpo = 0;

		if (is_array($costes_desglosado)) {
			foreach ($costes_desglosado as $c) {
				$fechaFactura = '';
				$fechaSuFactura = '';
				$fechaFacturaSinFormato ='';
				$fechaSuFacturaSinFormato = '';

				if (isset($c['FechaFactura'])) {
					$fechaFactura = $c['FechaFactura']->format('d/m/Y');
					$fechaFacturaSinFormato = $c['FechaFactura']->format('Y') . $c['FechaFactura']->format('m') . $c['FechaFactura']->format('d');
				}
				if (isset($c['FechaSuFactura'])) {
					$fechaSuFactura = $c['FechaSuFactura']->format('d/m/Y');
					$fechaSuFacturaSinFormato = $c['FechaSuFactura']->format('Y') . $c['FechaSuFactura']->format('m') . $c['FechaSuFactura']->format('d');
				}

				$tbody .=
					'
					<tr class="rowTabla">
						<td>'.$n.'</td>
						<td><span style="display: none;">'.$c['FechaAlbaran']->format('Y').$c['FechaAlbaran']->format('m').$c['FechaAlbaran']->format('d').'</span>'.$c['FechaAlbaran']->format('d/m/Y').'</td>
						<td>'.$c['NumeroAlbaran'].'</td>
						<td>'.$c['SerieAlbaran'].'</td>
						<td>'.$c['RazonSocial'].'</td>
						<td><span style="display: none;">'.$fechaFacturaSinFormato.'</span>'.$fechaFactura.'</td>
						<td>'.$c['CodigoArticulo'].'</td>
						<td>'.$c['DescripcionArticulo'].'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($c['Unidades2_']).'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($c['PrecioRebaje']).'</td>
						<td style="text-align:right">'.aux_money_format_noeuro($c['BaseImponible']).'</td>
						<td>'.$c['SuFacturaNo'].'</td>
						<td><span style="display: none;">'.$fechaSuFacturaSinFormato.'</span>'.$fechaSuFactura.'</td>
						<td>'.$c['CodigoProyecto'].'</td>
						<td>'.$c['Proyecto'].'</td>
						<td>'.$c['CodigoSeccion'].'</td>
						<td>'.$c['Seccion'].'</td>
						<td>'.$c['CodigoDepartamento'].'</td>
						<td>'.$c['Departamento'].'</td>			
					</tr>
				';
				$n++;
				$totalUnidades += $c['Unidades2_'];
				$totalImporteUnitario += $c['PrecioRebaje'];
				$totalImporteBaseImpo += $c['BaseImponible'];
			}
			$tfoot .= '<tr class="rowTotal">
						<td colspan="3">TOTAL GENERAL</td>
						<td colspan="5"></td>
						<td style="text-align:right">' . aux_money_format_noeuro($totalUnidades) . '</td>
						<td style="text-align:right">' . aux_money_format_noeuro($totalImporteUnitario) . '</td>
						<td style="text-align:right">' . aux_money_format_noeuro($totalImporteBaseImpo) . '</td>
						<td colspan="8"></td>		
					</tr>';
		}
		
		$table = '<tbody>
					' . $tbody . '
				  </tbody>
				  <tfoot>
				  	' . $tfoot . '
				  </tfoot>';
		
		return $table;
	}
	
}


function make_filter_18 () {
	/* he tenido que forzarle un onclick porque no funciona el href, NI IDEA DE POR QUÉ SÓLO PASA AQUÍ */
	
	$filtro	= file_get_contents_utf8(ABSPATH . '/template/filtro-18.html');

	if ($_SERVER['QUERY_STRING'] != '') {
		$tmp = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		$url = explode('?',$tmp);
		
		$excel_url = $url['0'] . '?' . $_SERVER['QUERY_STRING'] . "&excel=true";
		$excel_button = '<a id="btnExcel" target="_blank" style="margin-left: 20px" href="' . $excel_url . '" onclick="window.open(\'' . $excel_url . '\')" class="btn btn-default btn-sm">Excel</a>';

		$pdf_url = $url['0'] . '?' . $_SERVER['QUERY_STRING'] . "&pdf=true";
		$pdf_button = '<a id="btnPdf" target="_blank" style="margin-left: 20px;" href="' . $pdf_url . '" onclick="window.open(\'' . $pdf_url . '\')" class="btn btn-default btn-sm">PDF</a>';
	} else {
		$excel_button = '<a id="btnExcel" target="_blank" style="margin-left: 20px" href="?excel=true" onclick="window.open(\'?excel=true\')" class="btn btn-default btn-sm">Excel</a>';
		$pdf_button = '<a id="btnPdf" target="_blank" style="margin-left: 20px;" href="?pdf=true" onclick="window.open(\'?pdf=true\')" class="btn btn-default btn-sm">PDF</a>';
	}

	$filtro = str_replace('{EXCEL_URL}', $excel_button, $filtro);
	$filtro = str_replace('{PDF_URL}', $pdf_button, $filtro);
	
	return $filtro;
}

?>