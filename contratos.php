<?php
// header('Content-type: text/html; charset=utf-8');
include_once 'functions/comun/cabses.php';
// if (check_privileges("17")!=true){die;}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="robots" content="noindex,nofollow">
		<title>Contratos</title>
		<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	    <link href="assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	    <script src="assets/jquery.min.js"></script>
	    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<link href="assets/bootstrap/css/contratos.css" rel="stylesheet" />
		<link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	</head>
	<?php include_once 'template/menu.php'; ?>
	<body class="gris" onload="javascript:cambiarPestanna(pestanas,pestana1);">
		<div id="pestanas">
        	<ul id="lista" class="nav nav-tabs">
               	<li id="pestana1" class="active pestania"><a href='javascript:cambiarPestanna(pestanas,pestana1);'><i onclick="location.reload()"></i> CONTRATOS</a></li>
               	<li id="pestana2" class="pestania"><a href='javascript:cambiarPestanna(pestanas,pestana2);'><i onclick="location.reload()"></i> CONTROL CONTRATOS</a></li>
            </ul>
        </div>
        <div id="contenidopestanas">
			<div id="cpestana1" class="contenedorContrato">
				<form id="formContrato" action="functions/render/ajax/accion-contratos.php" method="post" class="contenido">
					<div class="formulario">
						<h3 style="font-weight:bold;font-size:22px"><i onclick="location.reload()" class="icon-refresh"></i> Contrato</h3>
						<div class="row">
							<div class="col col-md-3">
		                        <div class="tipos acidjs-css3-treeview">
								    <ul>
								        <li>
								            <input type="checkbox" id="node-0" checked="checked" /><label><input type="checkbox" /><span></span></label>
								            <label class="lblSelec" for="node-0"><span>Seleccione el tipo de contrato</span></label>
								            <ul class="listadoTipos">
								                <li class="subcontrata">
								                    <label for="node-0-0"><strong>Subcontrata</strong></label>
								                    <ul>
								                        <li>
						                                    <label for="node-0-0-1-0">Precio cerrado</label>
						                                    <ul>
								                                <li>
								                                    <label for="subcontrata_cerrado_con">Con retenciones</label> <input type="radio" name="tipo" value="subcontrata_cerrado_con_retenciones" id="subcontrata_cerrado_con" required /> 
								                                </li>
								                                <li>
								                                    <label for="subcontrata_cerrado_sin">Sin retenciones</label> <input type="radio" name="tipo" value="subcontrata_cerrado_sin_retenciones" id="subcontrata_cerrado_sin" style="margin-left:5px" />
								                                </li>
								                            </ul>
						                                </li>
						                                <li>
						                                    <label for="node-0-0-1-1">Precio abierto</label>
						                                    <ul>
								                                <li>
								                                    <label for="subcontrata_abierto_con">Con retenciones</label> <input type="radio" name="tipo" value="subcontrata_abierto_con_retenciones" id="subcontrata_abierto_con" /> 
								                                </li>
								                                <li>
								                                    <label for="subcontrata_abierto_sin">Sin retenciones</label> <input type="radio" name="tipo" value="subcontrata_abierto_sin_retenciones" id="subcontrata_abierto_sin" style="margin-left:5px" /> 
								                                </li>
								                            </ul>
						                                </li>
								                    </ul>
								                </li>
								                <li class="suministro">
								                    <label><strong>Suministro</strong></label>
								                    <ul>
						                        		<li>
						                                    <label for="suministro_autonomo">Aut&oacute;nomo</label> <input type="radio" name="tipo" value="suministro_autonomo" id="suministro_autonomo" style="margin-left:22px" /> 
						                                </li>
						                        		<li>
						                                    <label for="suministro_sin_autonomo">No aut&oacute;nomo</label> <input type="radio" name="tipo" value="suministro_sin_autonomo" id="suministro_sin_autonomo" style="margin-left:5px" />
						                                </li>
						                            </ul>
								                </li>
								                <li class="liquidacion">
								                    <label for="node-0-0"><strong>Liquidaci&oacute;n</strong></label>
								                    <ul>
								                        <li>
								                        	<label>Con retenciones</label>
								                        	<ul>
								                        		<li>
								                                    <label for="liquidacion_con_autonomo">Aut&oacute;nomo</label> <input type="radio" name="tipo" value="liquidacion_con_retencion_autonomo" id="liquidacion_con_autonomo" style="margin-left:22px" /> 
								                                </li>
								                        		<li>
								                                    <label for="liquidacion_con">No aut&oacute;nomo</label> <input type="radio" name="tipo" value="liquidacion_con_retencion" id="liquidacion_con" style="margin-left:5px" />
								                                </li>
								                            </ul>
								                        </li>
								                        <li>
								                        	<label>Sin retenciones</label>
								                        	<ul>
								                                <li>
								                                    <label for="liquidacion_sin_autonomo">Aut&oacute;nomo</label> <input type="radio" name="tipo" value="liquidacion_sin_retencion_autonomo" id="liquidacion_sin_autonomo" style="margin-left:22px" /> 
								                                </li>
								                                <li>
								                                    <label for="liquidacion_sin">No aut&oacute;nomo</label> <input type="radio" name="tipo" value="liquidacion_sin_retencion" id="liquidacion_sin" style="margin-left:5px"/>
								                                </li>
								                            </ul>
								                        </li>
								                    </ul>
								                </li>
								                <li class="anexo">
								                	<label><strong>Anexo Subcontrata</strong></label>
								                    <ul>
						                                <li>
						                                    <label for="anexo_autonomo">Aut&oacute;nomo</label> <input type="radio" name="tipo" value="anexo_autonomo" id="anexo_autonomo" style="margin-left:22px" /> 
						                                </li>
						                                <li>
						                                    <label for="anexo_sin_autonomo">No aut&oacute;nomo</label> <input type="radio" name="tipo" value="anexo_sin_autonomo" id="anexo_sin_autonomo" style="margin-left:5px"/>
						                                </li>
						                            </ul>
								                </li>
								            </ul>
								        </li>
								    </ul>
								</div>
		                    </div>
		                    <div class="col col-md-9">
			                    <div class="col col-md-3">
			                        <label>Fecha (dd/mm/yyyy)</label>
			                        <input id="fecha" name="fecha" placeholder="Fecha" class="input-date text" type="text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask required autocomplete="no" />
			                    </div>
			                    <div class="col col-md-3">
			                        <label>C&oacute;digo expediente</label>
			                        <input id="codigo_expediente" name="codigo_expediente" placeholder="C&oacute;digo expediente" class="text" type="text" autocomplete="no" />
			                    </div>
			                    <div class="col col-md-6">
			                        <label>Nombre</label>
			                        <input id="nombre" name="nombre" placeholder="Nombre" class="text" type="text" required />
			                    </div>
			                    <div class="col col-md-6">
			                        <label>Empresa</label>
			                        <input id="cif" name="cif" placeholder="Empresa CIF" class="text" type="text" required />
			                    </div>
		                        <div class="col col-md-6">
			                        <label>Domicilio</label>
			                        <input id="domicilio" name="domicilio" placeholder="Domicilio" class="text" type="text" readonly="yes" />
			                    </div>
			                    <div class="row">
				                    <div class="col col-md-3">
				                        <input type="button" value="Admins" data-toggle="modal" data-target="#modalAdmins" style="display:block;margin-top:26px 0 0 20px" />
				                    </div>
			                    </div>
			                    <!-- <div class="col col-md-5">
			                        <label>Administrador</label>
			                        <input id="admin" name="admin" placeholder="Administrador" type="text" class="admin" required />
			                    </div>
		                        <div class="col col-md-3">
			                        <label>DNI</label>
			                        <input id="dni" name="dni" placeholder="DNI" type="text" required />
			                    </div>
			                    <div class="col col-md-3">
			                        <label>Cargo</label>
			                        <input id="cargo" name="cargo" placeholder="Cargo" type="text" required />
			                    </div> -->
			                    <div id="otrosAdmins"> </div>
			                    <div class="row"></div>
			                    <div class="col col-md-4">
			                        <label>Nombre de la obra</label>
			                        <input id="cortoObra" placeholder="Nombre corto obra" class="text" type="text" required />
			                    </div>
			                    <div class="col col-md-3">
			                        <label>Obra</label>
			                        <input id="obra" name="obra" placeholder="Nombre largo obra" class="text" type="text" readonly="yes" />
			                    </div>
				                <div id="divForma" class="col col-md-3">
			                        <label>Forma de pago</label>
			                        <select id="forma" name="forma" type="text">
										<option value="1">Confirming</option>
										<option value="2">Pagar&eacute;</option>
										<option value="3">Transferencia bancaria</option>
										<option value="4">Contado</option>
									</select>
			                    </div>
			                    <div id="divDias" class="col col-md-2">
			                        <label>D&iacute;as</label>
			                        <input id="dias" name="dias" placeholder="D&iacute;as" class="text" type="text" />
			                    </div>
		                    </div>
		                </div>
		                <div class="oculto contrato contrato_subcontrata">
			                <div class="row">
			                    <div class="col col-md-12">
		                    		<label>Descripci&oacute;n de trabajos</label>
		                    		<textarea id="trabajos" name="trabajos" placeholder="Descripci&oacute;n de trabajos"></textarea>
		                    	</div>
			                </div>
			                <div class="row">
			                	<div class="col col-md-4">
			                        <label>Importe (s&oacute;lo usar un <u>punto</u> para separar los decimales)</label>
			                        <input id="importe" name="importe" placeholder="Importe" type="text" class="numero text" />
			                    </div>
			                </div>
							<div class="row">
								<div class="col col-md-12">
									<div class="pregunta">
										<label>Pago fraccionado</label>
										<input type="button" id="fraccionado_si" value="S&iacute;" />
										<input type="button" id="fraccionado_no" value="No" />
									</div>
									<textarea id="fraccionado_descrip" name="fraccionado_descrip" class="oculto" placeholder="Descripci&oacute;n del pago fraccionado"></textarea>
			                    </div>
			                </div>
			            </div>
			            <div class="oculto contrato contrato_liquidacion">
				            <div class="row">
				            	<div class="col col-md-12" style="margin-top:30px">
			                        <strong style="font-size:1.2em">&Uacute;ltima factura</strong>
			                    </div>
				            </div>
			                <div class="row">
			                	<div class="col col-md-3">
			                        <label>N&uacute;mero</label>
			                        <input id="numero_ultima_factura" name="numero_ultima_factura" placeholder="N&uacute;mero" type="text" class="text" />
			                    </div>
			                    <div class="col col-md-3">
			                        <label>Fecha (dd/mm/yyyy)</label>
			                        <input id="fecha_ultima_factura" name="fecha_ultima_factura" placeholder="Fecha" class="input-date text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask type="text" />
			                    </div>
			                    <div class="col col-md-3">
			                        <label>Importe</label>
			                        <input id="importe_ultima_factura" name="importe_ultima_factura" placeholder="Importe" class="text" type="text" />
			                    </div>
			                </div>
							<div class="row" style="margin-top:30px">
								<div class="col col-md-3">
			                        <label>Fecha contrato original (dd/mm/yyyy)</label>
			                        <input id="fecha_contrato_original" name="fecha_contrato_original" placeholder="Fecha" class="input-date text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask type="text" />
			                    </div>
			                	<div class="col col-md-3">
			                        <label>Importe retenci&oacute;n</label>
			                        <input id="importe_retencion" name="importe_retencion" placeholder="Importe" class="text" type="text" />
			                    </div>
			                </div>
			            </div>
			            <div class="oculto contrato contrato_suministro">
				            <div class="row">
				            	<div class="col col-md-12" style="margin-top:30px">
			                        <strong style="font-size:1.2em">Oferta / Presupuesto del proveedor</strong>
			                    </div>
				            </div>
			                <div class="row">
			                	<div class="col col-md-3">
			                        <label>N&uacute;mero</label>
			                        <input id="numero_oferta" name="numero_oferta" placeholder="N&uacute;mero" class="text" type="text" />
			                    </div>
			                    <div class="col col-md-3">
			                        <label>Fecha (dd/mm/yyyy)</label>
			                        <input id="fecha_oferta" name="fecha_oferta" placeholder="Fecha" class="input-date text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask type="text" />
			                    </div>
			                </div>
			                <div class="row">
				           		<div class="col col-md-12" style="margin-top:30px">
			                        <strong style="font-size:1.2em">Plazos del suministro</strong>
			                    </div>
				            </div>
							<div class="row">
								<div class="col col-md-3">
			                        <label>Fecha comienzo (dd/mm/yyyy)</label>
			                        <input id="fecha_suministro_ini" name="fecha_suministro_ini" placeholder="Fecha inicial" class="input-date text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask type="text" />
			                    </div>
			                	<div class="col col-md-3">
			                        <label>Fecha fin (dd/mm/yyyy)</label>
			                        <input id="fecha_suministro_fin" name="fecha_suministro_fin" placeholder="Fecha final" class="input-date text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask type="text" />
			                    </div>
			                </div>
			            </div>
			            <div class="oculto contrato contrato_anexo">
			                <div class="row">
			                    <div class="col col-md-3">
		                    		<label>Fecha contrato subcontrataci&oacute;n (dd/mm/yyyy)</label>
		                    		<input id="fecha_anexo_contrato" name="fecha_anexo_contrato" placeholder="Fecha" class="input-date text" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask type="text" />
		                    	</div>
			                </div>
			            </div>
		                <input class="submit" id="btnGenerar" type="submit" value="Generar documento" />
		                <input type="hidden" id="telefono" name="telefono" value="" />
						<input type="hidden" id="pago_fraccionado" name="pago_fraccionado" value="0" />
						<input type="hidden" id="plazo_estipulado" name="plazo_estipulado" value="0" />
						<input type="hidden" id="codigo_postal" name="codigo_postal" value="" />
						<input type="hidden" id="municipio" name="municipio" value="" />
						<input type="hidden" id="codigo_obra" name="codigo_obra" value="" />
						<input type="hidden" id="provincia" name="provincia" value="" />
						<input type="hidden" name="accion" value="generar" />
		            </div>
				</form>
				<div id="modalAdmins" class="modal fade" role="dialog">
				  	<div class="modal-dialog" style="width:69%">
				    	<div class="modal-content">
					      	<div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal">&times;</button>
						        <h3 class="modal-title" style="text-align:center;font-weight:bold">Administradores registrados <span id="cifSeleccionado"></span></h3>
					      	</div>
					      	<div id="divListaAdmins" class="modal-body"></div>
				      		<div class="modal-footer">
				      			<button type="button" class="btn btn-success" id="btnAceptarAdmins">Aceptar</button>
				      			<button type="button" class="btn btn-success" id="btnVerElegidaAdmins">Ver empresa elegida</button>
				      			<button type="button" class="btn btn-warning" id="btnVerTodosAdmins">Ver todos</button>
				        		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				      		</div>
				    	</div>
				  	</div>
				</div>
			</div>
			<div id="cpestana2" class="contenedorContrato">
				<div class="row" style="margin:10px">
					<div class="col-md-12">
						<table style="width: 100%; margin-bottom:20px;">
					        <tr>
					            <td style="padding-left:15px;width: 25%">
					                <div class="form-inline">
					                    <div class="input-group">
					                        <div class="input-group-addon">Desde</div>
					                        <input type="text" class="input-date input-sm form-control" id="fechaInicio" value="" style="height:auto;padding:5px 10px" />
					                        <div class="input-group-addon">Hasta</div>
					                        <input type="text" class="input-date input-sm form-control" id="fechaFin" value="" 
					                        style="height:auto;padding:5px 10px" />
					                    </div>
					                </div>
					            </td>
					            <td style="padding-left:15px;width: 20%">
					            	<label>Usuario</label>
					                <select class="form-control selectpicker" data-live-search="true" id="selUsuarios">
					                    <option value="-1">Todos los usuarios</option>
					                </select>
					            </td>
					            <td style="padding-left:15px;width: 20%">
					            	<label>Empresa</label>
					                <select class="form-control selectpicker" data-live-search="true" id="selEmpresas">
					                    <option value="-1">Todas las empresas</option>
					                </select>
					            </td>
					            <td style="width: 5%">
					                <button onclick="obtenerContratos(false);" style="margin-left: 20px;" class="btn btn-primary btn-sm">Filtrar</button>
					            </td>
					            <td style="width: 5%">
					                <a href="javascript:reiniciarFiltros()" style="margin-left: 20px;" class="btn btn-default btn-sm">Borrar</a>
					            </td>
					            <td style="width: 5%">
					            	<form action="functions/render/ajax/accion-contratos.php" method="post" >
					            	<!-- este formulario imita el ajax de obtenerContratos, por eso tiene los mismos "name" que los valores que se mandan en ese ajax, no tiene nada que ver con los name originales del form principal, y el id es solo para meterle los mismos valores que se eligen -->
					            		<input type="hidden" name="accion" value="imprimirExcel" />
					            		<input type="hidden" id="fechaInicioForm2" name="fechaInicio" value="imprimirExcel" />
					            		<input type="hidden" id="fechaFinForm2" name="fechaFin" value="imprimirExcel" />
					            		<input type="hidden" id="selUsuariosForm2" name="usuario" value="imprimirExcel" />
					            		<input type="hidden" id="selEmpresasForm2" name="mercantil" value="imprimirExcel" />
					            		<input type="hidden" id="selObrasForm2" name="obra" value="imprimirExcel" />
					            		<input type="hidden" id="selContratosForm2" name="contrato" value="imprimirExcel" />
					            		<input type="hidden" id="expedienteForm2" name="expediente" value="imprimirExcel" />
					               		<button id="btnExcel" style="margin-left: 20px" class="btn btn-default btn-sm">Excel</button>
					               	</form>
					            </td>
					        </tr>
					        <tr>
					            <td style="padding-left:15px;width: 20%">
					            	<label>Obra</label>
					                <select class="form-control" data-live-search="true" id="selObras">
					                    <option value="-1">Todas las obras</option>
					                </select>
					            </td>
					            <td style="padding-left:15px;width: 20%">
					            	<label>Tipo de contrato</label>
					                <select class="form-control" data-live-search="true" id="selContratos">
					                    <option value="-1">Todos los contratos</option>
					                    <option value="subcontrata_con_aval">
											Subcontrata con aval
										</option>
										<option value="subcontrata_cerrado_con_retenciones">
											Subcontrata precio cerrado con retenciones
										</option>
										<option value="subcontrata_cerrado_sin_retenciones">
											Subcontrata precio cerrado sin retenciones
										</option>
										<option value="subcontrata_abierto_con_retenciones">
											Subcontrata precio abierto con retenciones
										</option>
										<option value="subcontrata_abierto_sin_retenciones">
											Subcontrata precio abierto sin retenciones
										</option>
										<option value="suministro_autonomo">
											Suministro autónomo
										</option>
										<option value="suministro_sin_autonomo">
											Suministro no autónomo
										</option>
										<option value="liquidacion_con_retencion">
											Liquidación con retenciones
										</option>
										<option value="liquidacion_con_retencion_autonomo">
											Liquidación con retenciones autónomo
										</option>
										<option value="liquidacion_sin_retencion">
											Liquidación sin retenciones
										</option>
										<option value="liquidacion_sin_retencion_autonomo">
											Liquidación sin retenciones autónomo
										</option>
										<option value="anexo_autonomo">
											Anexo autónomo
										</option>
										<option value="anexo_sin_autonomo">
											Anexo no autónomo
										</option>
					                </select>
					            </td>
					            <td style="padding-left:15px;width:20%">
					            	<label>C&oacute;digo de expediente</label>
					                <input type="text" class="form-control" id="expediente" placeholder="Expediente" value="" />
					            </td>
					        </tr>
					    </table>
				    </div>
					<div id="divTabla" class="col-md-12">
						<table id="tablaContratos" class="table table-striped table-bordered">
		                	<thead style="background-color:#fff;" > 
		                		<tr style="border:solid 1px silver;">
		                			<th style="min-width:10px">#</th>
		                			<th style="min-width:105px">Fecha Registro</th>
		                			<th style="min-width:40px">Usuario Descarga</th>
		                			<th style="min-width:100px">Nombre Mercantil</th>
		                			<th style="min-width:100px">C&oacute;digo obra</th>
		                			<th style="min-width:200px">Obra</th>
		                			<th style="min-width:50px">Tipo Contrato</th>
		                			<th style="min-width:10px">Expediente</th>
		                		</tr>
		                	</thead>
		                	<tbody id="tableBody">
		                    </tbody>
		                </table>
		            </div>
				</div>
			</div>
		</div>
		<script src="assets/jquery-ui.min.js"></script>
		<link href="assets/jquery-ui.min.css" rel="stylesheet" />
		<link href="assets/jquery-ui.theme.min.css" rel="stylesheet" />
    	<link href="assets/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<script src="assets/datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
		<script src="assets/datatables/jquery.dataTables.js"></script>
		<script src="assets/datatables/dataTables.bootstrap.js"></script>
		<link href="assets/chosen/chosen.css" rel="stylesheet" />
		<script src="assets/chosen/chosen.jquery.min.js"></script>
	    <script src="assets/inputmask/inputmask.min.js" type="text/javascript"></script>
	    <script src="assets/inputmask/jquery.inputmask.js" type="text/javascript"></script>
	    <script src="assets/inputmask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
	    <script src="assets/inputmask/jquery.inputmask.extensions.js" type="text/javascript"></script>
		<script>			
			var arrayAutoObras;
			var arrayAutoEmpresas;
			var arrayAutoAdministradores;
			var arrayAdminSeleccionados = new Array();
			var cifEmpresaSeleccionado = "";
			var codigoProveedor = ""; // solo es necesario para hacer la consulta que saca la ultima factura proveedor+obra
			var esLiquidacion = false;

			$(document).ready(function() {
				$("body").on("keydown", ".numero", function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

				$(".input-date").datepicker({ clearBtn: true, language: "es", autoclose: true, todayHighlight: true });

				$("[data-mask]").inputmask();

				autocompletarObras();
				autocompletarEmpresas();
				autocompletarAdministradores(""); // obtiene todos, le envio vacio el cif de la empresa
				llenarSelectUsuarios();
				$("#selContratos").chosen({ search_contains: true });

				$("ul.listadoTipos > li").click(function() {
					esLiquidacion = false;

					/* oculto todos los divs primero */
					$(".contrato").hide();
					/* muestro el div que debe verse segun el que se haya clickado */
					var tipo = $(this).attr("class");
					$(".contrato.contrato_"+tipo).show();

					/* solo pueden escribir el importe cuando es subcontrata + precio cerrado */
					var importeReadonly = true;
					if ($("#subcontrata_cerrado_con").prop("checked") || $("#subcontrata_cerrado_sin").prop("checked") ) {
						importeReadonly = false;
            		}

            		/* si es liquidacion, sin retenciones, no pueden escribir el importe de retencion */
            		var importeRetencionReadonly = false;
            		if ($("#liquidacion_sin_autonomo").prop("checked") || $("#liquidacion_sin").prop("checked") ) {
						importeRetencionReadonly = true;
            		}

            		/* cualquier liquidacion activa la variable y ademas oculta ciertos campos principales (formas de pago y dias) */
            		var nombreInput = comprobarCheckTipo();
            		if (nombreInput.includes("liquidacion")) {
	        			esLiquidacion = true;
	        		}
    				formasYdia(!esLiquidacion); // se ocultan solo para las liquidaciones

					$("#importe").attr("readonly", importeReadonly);
					$("#importe_retencion").attr("readonly", importeRetencionReadonly);
				});

				$("#cif").focusout(function() {
					var cif = $(this).val();
					cif = cif.replace(/\-/g, "").replace(/\./g, "");
					$.ajax({
		                url: "functions/render/ajax/accion-contratos.php",
		                data: {cif: cif, accion: 'cif'},
		                method: "post",
		                dataType: "json",
		                success: function(data) {
		                    $("#nombre").val(data.empresa);
		                    $("#domicilio").val(data.domicilio);
		                    $("#codigo_postal").val(data.cp);
		                    $("#municipio").val(data.municipio);
		                    $("#provincia").val(data.provincia);
		                    $("#telefono").val(data.telefono);
		                    autocompletarAdministradores(cif); // dentro se autollena la lista en la ventana modal
		                    autoSeleccionarAdmin();
		                },
		                error: function() {
		                    console.log('No se encuentra el archivo');
		                }
		            });
				});

				$("#fraccionado_si").click(function() {
					$(this).addClass('selected');
					$("#fraccionado_no").removeClass('selected');
					$("#fraccionado_descrip").show();
					$("#pago_fraccionado").val("1");
				});

				$("#fraccionado_no").click(function() {
					$(this).addClass('selected');
					$("#fraccionado_si").removeClass('selected');
					$("#fraccionado_descrip").hide();
					$("#pago_fraccionado").val("0");
				});

				$("#estipulado_si").click(function() {
					$(this).addClass('selected');
					$("#estipulado_no").removeClass('selected');
					$("#estipulado_descrip").show();
					$("#penalizacion").show();
					$("#plazo_estipulado").val("1");
				});

				$("#estipulado_no").click(function() {
					$(this).addClass('selected');
					$("#estipulado_si").removeClass('selected');
					$("#estipulado_descrip").hide();
					$("#penalizacion").hide();
					$("#plazo_estipulado").val("0");
				});

				$("#cortoObra").keyup(function() {
					autocompleteObra($(this));
				});

				$("#nombre").keyup(function() {
					autocompleteEmpresa($(this));
				});

				$("#admin").keyup(function() {
					autocompleteAdmin($(this));
				});

				$("#btnAceptarAdmins").click(function() {
					arrayAdminSeleccionados = new Array();
					$("#tablaAdmins > tbody > tr.filaAdmin").each(function() {
						var td = $(this).children("td.tdCheck");
                		var inputCheck = $(td).children("input");
            			if ($(inputCheck).is(':checked')) {
						   	var id = $(this).attr("name");
							arrayAdminSeleccionados.push(seleccionarAdminInfo(id));
            			}
					});
					$("#modalAdmins").modal("toggle");
					pintarAdminsSeleccionados();
				});
				
				$("#btnVerElegidaAdmins").click(function() {
					cifEmpresaSeleccionado = $("#cif").val();
					$("#cifSeleccionado").text(" - Empresa: " + cifEmpresaSeleccionado);
					autocompletarAdministradores(cifEmpresaSeleccionado);
				});

				$("#btnVerTodosAdmins").click(function() {
					cifEmpresaSeleccionado = "";
					$("#cifSeleccionado").text("");
					autocompletarAdministradores("");
				});

				// $("body").on("click", ".filaAdmin", function(e) {
				// 	if($(e.target).is(".eliminarAdmin")){
			 //            e.preventDefault();
			 //            return;
			 //        }
				// 	var id = $(this).attr("name");
				// 	seleccionarAdmin(id);
				// });

				$("body").on("click", ".eliminarAdmin", function() {
					var id = $(this).attr("name");
					$.ajax({
		                url: "functions/render/ajax/accion-contratos.php",
		                data: {id: id, accion: 'eliminarAdmin'},
		                method: "post",
		                dataType: "json",
		                success: function(data) {
		                    autocompletarAdministradores("");
		                },
		                error: function() {
		                    console.log('No se encuentra el archivo');
		                }
		            });
				});

				$("body").on("click", ".btnEliminarFilaAdmin", function() {
					var id = $(this).attr("name");
					$("#fila-admin-"+id).remove();
				});

				/* evitar que generen el contrato sin administrador */
				$("#btnGenerar").click(function(e) {
					/* solo avisarle de que DEBE asignar un administrador cuando la empresa NO ES UN AUTONOMO, si lo es, ya no hace falta ponerle el admin */
					// var esAutonomo = comprobarAutonomo($("#cif").val());
					// console.log("es autonomo?? " + esAutonomo);
					// if ($(".admin").length == 0 && esAutonomo) {
					// 	e.preventDefault();
					// 	alert("Debe asignar un administrador al contrato.");
					// }
					if (!esLiquidacion && $("#dias").val() == "") { // en las liquidaciones no hace falta añadir los dias
						e.preventDefault();
						alert("Debe indicar los días para la forma de pago.");
					}
				});

				// $("#btnGenerar").click(function() {
				// 	$.ajax({
		  //               url: "functions/render/ajax/accion-contratos.php",
		  //               data: {cif: $("#cif").val(), tipo: $("input[name='tipo']:checked").val(), fecha: $("#fecha").val(), nombre: $("#nombre").val(), 
		  // 						domicilio: $("#domicilio").val(), admin: $("#admin").val(), dni: $("#dni").val(), obra: $("#obra").val(), 
		  // 						trabajos: $("#trabajos").val(), importe: $("#importe").val(), forma: $("#forma").val(), pago_fraccionado: $("#pago_fraccionado").val(), 
		  // 						plazo_estipulado: $("#plazo_estipulado").val(), accion: 'generar'},
		  //               method: "post",
		  //               success: function(data) {
		  //                   console.log(data);
		  //               },
		  //               error: function() {
		  //                   console.log('No se encuentra el archivo.');
		  //               }
		  //           });
				// });

				/* evitar el submit del formulario al darle al enter */
				$(window).keydown(function(event){
				    if(event.keyCode == 13) {
				      	event.preventDefault();
				      	return false;
				    }
			  	});
			});

			function cambiarPestanna(pestannas,pestanna) {
	    
			    // Obtiene los elementos con los identificadores pasados.
			    pestanna = document.getElementById(pestanna.id);
			    listaPestannas = document.getElementById(pestannas.id);
			    
			    // Obtiene las divisiones que tienen el contenido de las pestañas.
			    cpestanna = document.getElementById('c'+pestanna.id);

			    listacPestannas = document.getElementById('contenido'+pestannas.id);
			    
			    i=0;
			    // Recorre la lista ocultando todas las pestañas y restaurando el fondo 
			    // y el padding de las pestañas.
			    while (typeof listacPestannas.getElementsByTagName('div')[i] != 'undefined'){
			        $(document).ready(function(){
			        	//listaPestannas[i].css('display','none');
			            $(listacPestannas.getElementsByTagName('div')[i]).css('display','none');
			            $(listaPestannas.getElementsByTagName('li')[i]).css('background','');
			            $(listaPestannas.getElementsByTagName('li')[i]).css('padding-bottom','');
			        });
			        i += 1;
			    }
			 
			    $(document).ready(function() {
			        // Muestra el contenido de la pestaña pasada como parametro a la funcion,
			        // cambia el color de la pestaña y aumenta el padding para que tape el  
			        // borde superior del contenido que esta justo debajo y se vea de este 
			        // modo que esta seleccionada.
			        $(cpestanna).css('display','');
			        $(cpestanna.getElementsByTagName('div')).css('display','');
			        $(pestanna).css('padding-bottom','2px');
			        // quitar todos los active de las pestañas y ponerselo solo al que este seleccionado
			        $(".pestania").removeClass("active");
			        $(pestanna).addClass("active");
			    });

			   if (cpestanna.id==='cpestana2') {
			    	obtenerContratos(false);
			    }
			 
			}

			function obtenerContratos(excel) {
				var fechaInicio = $("#fechaInicio").val();
				var fechaFin = $("#fechaFin").val();
				var usuario = $("#selUsuarios").val();
				var mercantil = $("#selEmpresas").val();
				var obra = $("#selObras").val();
				var contrato = $("#selContratos").val();
				var expediente = $("#expediente").val();

				/* para imprimir excel, es otro form, y se necesitan estos valores como campos ocultos que enviar */
				$("#fechaInicioForm2").val(fechaInicio);
				$("#fechaFinForm2").val(fechaFin);
				$("#selUsuariosForm2").val(usuario);
				$("#selEmpresasForm2").val(mercantil);
				$("#selObrasForm2").val(obra);
				$("#selContratosForm2").val(contrato);
				$("#expedienteForm2").val(expediente);

				$.ajax({
	                url: "functions/render/ajax/accion-contratos.php",
	                data: {accion: "obtenerContratos", fechaInicio: fechaInicio, fechaFin: fechaFin, usuario: usuario, mercantil: mercantil, obra: obra, contrato: contrato, expediente: expediente},
	                method: "post",
	                success: function(data) {
	                	if (data == 'false') { // no hay datos!
	                		$("#tableBody").html('<tr><td colspan="8" style="text-align:center">No hay registros.</td></tr>');
	                	} else {
	                		var respuesta = $.parseJSON(data);
			            	var contratos = respuesta[0].jsonContratos;
			            	cargarTablaContratos(contratos);
	                	}
	                },
	                error: function() {
	                    console.log('No se encuentra el archivo');
	                }
	            });
			}

			function cargarTablaContratos(contratos) {
				var count = 0;
            	var contLength = contratos.length;
            	var tContratos = '';
            	var idFila = 1;
            	for (count; count<contLength; count++){
            		tContratos+='<tr class="rowTabla">'+
			                		'<td>'+idFila+'</td>'+
			                		'<td>'+contratos[count].fecha+'</td>'+
			                		'<td>'+contratos[count].usuarioDescarga+'</td>'+
			                		'<td>'+contratos[count].nombreMercantil+'</td>'+
			                		'<td>'+contratos[count].codigoObra+'</td>'+
			                		'<td>'+contratos[count].obra+'</td>'+
			                		'<td>'+contratos[count].tipo+'</td>'+
			                		'<td>'+contratos[count].expediente+'</td>'+
		                		'</tr>';
		            idFila++;
            	}
            	$("#tableBody").html(tContratos);
            	// refrescar el sticky header
				$("#tablaContratos").floatThead({
		            scrollingTop: 50
		        });
			}

			function reiniciarFiltros() {
				$("#fechaInicio").val("");
				$("#fechaFin").val("");
				$("#selUsuarios").val(-1).trigger("chosen:updated");
				$("#selEmpresas").val(-1).trigger("chosen:updated");
				$("#selObras").val(-1).trigger("chosen:updated");
				$("#selContratos").val(-1).trigger("chosen:updated");
				$("#expediente").val("");

				$("#fechaInicioForm2").val("");
				$("#fechaFinForm2").val("");
				$("#selUsuariosForm2").val("");
				$("#selEmpresasForm2").val("");
				$("#selObrasForm2").val("");
				$("#selContratosForm2").val("");
				$("#expedienteForm2").val("");

				obtenerContratos(false);
			}

			function encode_utf8(s) {
				return decodeURIComponent(escape(s));
			}

			function llenarSelectUsuarios() {
				$.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratos.php",
                    data: {accion: "usuarios"},
                    success: function(data) {
                    	if (data != 'false') {
	                        var respuesta = $.parseJSON(data);
	                        var usuarios = respuesta[0].jsonUsuarios;
	                        var optionsUsuarios = '<option value="-1">Todos los usuarios</option>';
	                        optionsUsuarios += pintarSelectConJson(usuarios, false);
			            	$("#selUsuarios").html(optionsUsuarios);
			            	$("#selUsuarios").chosen({ search_contains: true });
			            }
                    }
                });
			}

			function pintarSelectConJson(json, mostrarId) {
            	var options = "";
            	var count = 0;
            	var contLength = json.length;
            	
            	for (count; count<contLength; count++) {
            		var texto = json[count].value;

            		if (mostrarId) {
            			texto = json[count].id + " - " + texto; // le pongo delante el id si quiere verlo
            		}

            		options +=
            			'<option value="'+json[count].id+'">'
            				+texto+
		                '</option>';
            	}

            	return options;
            }

			function autocompletarObras() {
                $.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratos.php",
                    data: {accion: "obras"},
                    success: function(data) {
                    	if (data != 'false') {
	                        var respuesta = $.parseJSON(data);
	                        arrayAutoObras = respuesta[0].jsonObras;
	                        var optionsObras = '<option value="-1">Todas las obras</option>';
	                        optionsObras += pintarSelectConJson(arrayAutoObras, true);
			            	$("#selObras").html(optionsObras);
			            	$("#selObras").chosen({ search_contains: true });
			            }
                    }
                });
            }

            function autocompletarEmpresas() {
                $.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratos.php",
                    data: {accion: "empresas"},
                    success: function(data) {
                    	if (data != 'false') {
	                        var respuesta = $.parseJSON(data);
	                        arrayAutoEmpresas = respuesta[0].jsonEmpresas;
	                        var optionsMercantiles = '<option value="-1">Todas las empresas</option>';
	                        optionsMercantiles += pintarSelectConJson(arrayAutoEmpresas, true);
			            	$("#selEmpresas").html(optionsMercantiles);
			            	$("#selEmpresas").chosen({ search_contains: true });
			            }
                    }
                });
            }

            function autocompletarAdministradores(cifEmpresa) {
            	if (cifEmpresa == "" && cifEmpresaSeleccionado != "") {
            		cifEmpresa = cifEmpresaSeleccionado;
            	}
                $.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratos.php",
                    data: {accion: "admins", cifEmpresa: cifEmpresa},
                    async: false,
                    success: function(data) {
                    	if (data != 'false') {
	                        var respuesta = $.parseJSON(data);
	                        arrayAutoAdministradores = respuesta[0].jsonAdministradores;
	                        llenarListaAdmins();
	                    }
                    }
                });
            }

            function autocompleteObra(obj) {
			    var letra = $(obj).val();
			    if (typeof arrayAutoObras !== 'undefined') {
			        $("#cortoObra").autocomplete({
			            source: function(req, response) {
							var results = $.ui.autocomplete.filter(arrayAutoObras, req.term);
						    response(results.slice(0, 10)); //obtener un limite de resultados
						},
			            minLength: 3,
			            select: function(event, ui) {
			            	var id = ui.item.id;
			            	var descripcion = ui.item.desc;
			            	if (descripcion.trim() == "") {
			            		$("#obra").val(ui.item.value);
			            	} else {
			            		$("#obra").val(ui.item.desc);
			            	}
			            	$("#codigo_obra").val(id);
			            	obtenerUltimaFactura();
			            }
			        });
			    }
            }

            function autocompleteEmpresa(obj) {
			    var letra = $(obj).val();
			    if (typeof arrayAutoEmpresas !== 'undefined') {
			        $("#nombre").autocomplete({
			            source: function(req, response) {
							var results = $.ui.autocomplete.filter(arrayAutoEmpresas, req.term);
						    response(results.slice(0, 10)); //obtener un limite de resultados
						},
			            minLength: 3,
			            select: function(event, ui) {
			            	var data = ui.item;
			            	$("#cif").val(data.id);
                			$("#nombre").val(data.value);
		                    $("#domicilio").val(data.domicilio);
		                    $("#codigo_postal").val(data.cp);
		                    $("#municipio").val(data.municipio);
		                    $("#provincia").val(data.provincia);
		                    $("#telefono").val(data.telefono);
		                    cifEmpresaSeleccionado = data.id; // si se borran elementos lo suyo es que se quede con el que estaba buscando
		                    $("#cifSeleccionado").text(" - Empresa: " + cifEmpresaSeleccionado);
		                    autocompletarAdministradores(data.id); // dentro se autollena la lista en la ventana modal
		                    autoSeleccionarAdmin(); // si solo hay uno seleccionarlo
		                    comprobarAutonomo(data.id); // cambiar check de los radiobutton dependiendo de si es autonomo o no
		                    codigoProveedor = data.codigoProveedor; // obtengo tambien su codigo de proveedor para hacer una consulta a la ultima factura
		                    obtenerUltimaFactura();
			            }
			        });
			    }
            }

            function comprobarAutonomo(cif) {
            	esLiquidacion = false;
            	var nombreInput = comprobarCheckTipo();
            	if (nombreInput != "") {
            		var esAutonomo = false;

            		/* si al elegir la empresa, el CIF termina en una letra, es pq es una persona autónoma */
	            	var ultimoCaracter = cif.substr(cif.length - 1);
	            	if (ultimoCaracter.match(/[a-z]/i)) {
	            		esAutonomo = true;
	            	}

            		/* si el nombre del input contiene ciertas palabras clave sabremos de qué tipo es */
	        		if (nombreInput.includes("subcontrata")) {
	        			console.log("subcontrata");
	        		} else if (nombreInput.includes("suministro")) {
	        			console.log("suministro");
	        			if (esAutonomo) {
	        				$("#suministro_autonomo").prop("checked", true);
	        			} else {
							$("#suministro_sin_autonomo").prop("checked", true);
	        			}
	        		} else if (nombreInput.includes("liquidacion_con")) {
	        			esLiquidacion = true;
	        			console.log("liquidacion con retencion");
	        			if (esAutonomo) {
	        				$("#liquidacion_con_autonomo").prop("checked", true);
	        			} else {
							$("#liquidacion_con").prop("checked", true);
	        			}
	        		} else if (nombreInput.includes("liquidacion_sin")) {
	        			esLiquidacion = true;
	        			console.log("liquidacion SIN retencion");
	        			if (esAutonomo) {
	        				$("#liquidacion_sin_autonomo").prop("checked", true);
	        			} else {
							$("#liquidacion_sin").prop("checked", true);
	        			}
	        			
	        		} else if (nombreInput.includes("anexo")) {
	        			console.log("anexo");
	        			if (esAutonomo) {
	        				$("#anexo_autonomo").prop("checked", true);
	        			} else {
							$("#anexo_sin_autonomo").prop("checked", true);
	        			}
	        		}
            	}

    			formasYdia(!esLiquidacion); // se ocultan solo para las liquidaciones
            }

            function formasYdia(ver) {
            	if (ver) {
            		$("#divForma").show();
            		$("#divDias").show();
            	} else {
        			$("#divForma").hide();
            		$("#divDias").hide();
            	}
            }

            function comprobarCheckTipo() {
            	if ($('input[name=tipo]:checked').size() > 0) {
            		var nombreInput = $('input[name=tipo]:checked', '#formContrato').val();

	            	if (nombreInput != "") {
	            		return nombreInput;
	            	}
	            }

	            return "";
            }

            function obtenerUltimaFactura() { // no controlo si unicamente es liquidacion, por si tiene otro marcado y en el ultimo momento cambia a liquidacion, ya tendrá los campos rellenos
            	var obra = $("#codigo_obra").val();

            	if (codigoProveedor != "" && obra != "") {
            		$.ajax({
		                url: "functions/render/ajax/accion-contratos.php",
		                data: {accion: 'obtenerUltimaFactura', codigoProveedor: codigoProveedor, obra: obra},
		                method: "post",
		                dataType: "json",
		                success: function(data) {
		                    console.log(data);
		                    /* marcar si es retencion o no, solo si esta en el apartado de liquidaciones */
		                    if (data.aplicarRetencion == 1) {
		                    	cambiarRetencion(true);
		                    	$("#importe_retencion").val(data.importeRetencion);
		                    } else {
		                    	cambiarRetencion(false);
		                    }
		                    $("#numero_ultima_factura").val(data.numFactura);
		                    $("#fecha_ultima_factura").val(data.fechaFactura);
		                    $("#importe_ultima_factura").val(data.importe);
		                    if (esLiquidacion) {
								$("#fecha").val(data.fechaFactura);
		                    }
		                }
		            });
            	}
            }

            function cambiarRetencion(tieneRetencion) { // solo esta en liquidaciones
            	var nombreInput = comprobarCheckTipo();
            	if (nombreInput != "") {
            		if (nombreInput.includes("liquidacion")) {
	        			if (tieneRetencion) {
	        				$("#liquidacion_con_autonomo").prop("checked", true);
	        			} else {
							$("#liquidacion_sin_autonomo").prop("checked", true);
	        			}
	        			// y justo al terminar, volver a comprobar si es autonomo o no!!
	        			comprobarAutonomo($("#cif").val());
	        			// refrescar estos cambios con el onchange, porque cambian los div inferiores del formulario
	        			$('input[name=tipo]:checked').closest("li").click();
	        		}
            	}
            }

            function autocompleteAdmin(obj) {
			    var letra = $(obj).val();
			    if (typeof arrayAutoAdministradores !== 'undefined') {
			        $("#admin").autocomplete({
			            source: function(req, response) {
							var results = $.ui.autocomplete.filter(arrayAutoAdministradores, req.term);
						    response(results.slice(0, 10)); //obtener un limite de resultados
						},
			            minLength: 3,
			            select: function(event, ui) {
			            	var data = ui.item;
			            	$("#admin").val(data.value);
                			$("#dni").val(data.dni);
		                    $("#cargo").val(data.cargo);
			            }
			        });
			    }
            }

            function autoSeleccionarAdmin() {
            	/* autoseleccionar admin de la empresa elegida si solo hay uno */
            	if (arrayAutoAdministradores.length == 1) {
            		arrayAdminSeleccionados = new Array();
	        		arrayAdminSeleccionados.push(arrayAutoAdministradores[0]);
	        		pintarAdminsSeleccionados();
            	}
            }

            function seleccionarAdminInfo(id) { // traer toda la info y guardarla en el array seleccionados
            	for(var i = 0; i < arrayAutoAdministradores.length; i++) {
					var a = arrayAutoAdministradores[i];
            		if (a.id == id) {
            			return a;
            		}
            	}
            }

            function pintarAdminsSeleccionados() {
            	var divAdmins = "";
            	for(var i = 0; i < arrayAdminSeleccionados.length; i++) {
					var a = arrayAdminSeleccionados[i];
					divAdmins += '<div class="row"></div>' +
								'<div id="fila-admin-'+i+'">' +
				                    '<div class="col col-md-5">' +
				                        '<label>Administrador</label>' +
				                        '<input id="admin-'+i+'" name="admin[]" placeholder="Administrador" type="text" class="text admin" value="'+a.value+'" required />' +
				                    '</div>' +
			                        '<div class="col col-md-2">' +
				                        '<label>DNI</label>' +
				                        '<input id="dni-'+i+'" name="dni[]" placeholder="DNI" type="text" class="text" value="'+a.dni+'" required />' +
				                    '</div>' +
				                    '<div class="col col-md-4">' +
				                        '<label>Cargo</label>' +
				                        '<input id="cargo-'+i+'" name="cargo[]" placeholder="Cargo" type="text" class="text" value="'+a.cargo+'" required />' +
				                    '</div>' +
				                    '<div class="col col-md-1">' +
				                        '<input name="'+i+'" type="button" class="btnEliminarFilaAdmin text" style="display:block;margin:26px 0 0 10px;width:58px" value="x" />' +
				                    '</div>' +
			                    '</div>';
            	}
            	$("#otrosAdmins").html(divAdmins);
            }

            function llenarListaAdmins() {
            	var lista = "No existen administradores registrados.";
            	if (arrayAutoAdministradores.length > 0) {
            		lista = "<table id='tablaAdmins' class='table table-striped'>" +
            					"<thead>" +
            						"<tr>" +
		            					"<th>CIF empresa</th>" +
		            					"<th>Nombre</th>" +
		            					"<th>DNI</th>" +
		            					"<th>Cargo</th>" +
		            					"<th>Seleccionar</th>" +
		            				"</tr>" +
            					"</thead>" +
            					"<tbody>";
					for(var i = 0; i < arrayAutoAdministradores.length; i++) {
						var a = arrayAutoAdministradores[i];
	            		lista += 
	            				"<tr class='filaAdmin' name='"+ a.id +"'>" +
	            					"<td>" + a.cifEmpresa + "</td>" +
	            					"<td>" + a.value + "</td>" +
	            					"<td>" + a.dni + "</td>" +
	            					"<td>" + a.cargo + "</td>" +
	            					//"<td><i class='eliminarAdmin icon-trash' name='" + a.id + "'></i></td>" +
	            					"<td class='tdCheck'><input type='checkbox' name='" + a.id + "' value='' /></td>" +
	            				"</tr>";
	            	}
	            	lista += "</tbody></table>";
            	}
            	$("#divListaAdmins").html(lista);
            	$("#tablaAdmins").DataTable({
				    "bLengthChange": false
				});
            }
		</script>
		<?php
			include_once 'template/footer-comun.php';
		?>
		<script src="assets/sticky-header.js"></script>
	</body>
</html>