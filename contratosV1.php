<?php
header('Content-type: text/html; charset=utf-8');
require_once("config.php");

if (check_privileges("17")!=true){die;}

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
	<body class="gris">
		<div class="contenedorContrato">
			<form id="formGasto" action="functions/render/ajax/accion-contratosV1.php" method="post" method="post" class="contenido">
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
		                        <input id="fecha" name="fecha" placeholder="Fecha" class="input-date" type="text" required autocomplete="no" />
		                    </div>
		                    <div class="col col-md-3">
		                        <label>C&oacute;digo Expediente</label>
		                        <input id="codigoExpediente" name="codigoExpediente" placeholder="C&oacute;digo Expediente" type="text" autocomplete="no" />
		                    </div>
		                    <div class="col col-md-6">
		                        <label>Nombre</label>
		                        <input id="nombre" name="nombre" placeholder="Nombre" type="text" required />
		                    </div>
		                    <div class="col col-md-6">
		                        <label>Empresa</label>
		                        <input id="cif" name="cif" placeholder="Empresa CIF" type="text" required />
		                    </div>
	                        <div class="col col-md-6">
		                        <label>Domicilio</label>
		                        <input id="domicilio" name="domicilio" placeholder="Domicilio" type="text" readonly="yes" />
		                    </div>
		                    <div class="row">
			                    <div class="col col-md-3">
			                        <input type="button" value="Admins" data-toggle="modal" data-target="#modalAdmins" style="display:block;margin-top:26px 0 0 20px" />
			                    </div>
		                    </div>
		                    <!-- <div class="col col-md-5">
		                        <label>Administrador</label>
		                        <input id="admin" name="admin" placeholder="Administrador" type="text" required />
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
		                        <input id="cortoObra" placeholder="Nombre corto obra" type="text" />
		                    </div>
		                    <div class="col col-md-3">
		                        <label>Obra</label>
		                        <input id="obra" name="obra" placeholder="Nombre largo obra" type="text" readonly="yes" />
		                    </div>
			                <div class="col col-md-3">
		                        <label>Forma de pago</label>
		                        <select id="forma" name="forma" type="text">
									<option value="1">Confirming</option>
									<option value="2">Pagar&eacute;</option>
									<option value="3">Transferencia bancaria</option>
									<option value="4">Contado</option>
								</select>
		                    </div>
		                    <div class="col col-md-2">
		                        <label>D&iacute;as</label>
		                        <input id="dias" name="dias" placeholder="D&iacute;as" type="text" />
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
		                        <input id="importe" name="importe" placeholder="Importe" type="text" class="numero" />
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
		                    <!-- <div class="col col-md-12">
								<div class="pregunta">
									<label style="margin-right:38px">Plazo estipulado</label> 
									<input type="button" id="estipulado_si" value="S&iacute;" />
									<input type="button" id="estipulado_no" value="No" />
								</div>
								<textarea id="estipulado_descrip" name="fecha_plazo" class="oculto" placeholder="Fecha del plazo"></textarea>
								<input id="penalizacion" name="penalizacion" class="oculto" placeholder="Penalizaci&oacute;n" type="text" />
		                    </div> -->
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
		                        <input id="numero_ultima_factura" name="numero_ultima_factura" placeholder="N&uacute;mero" type="text" />
		                    </div>
		                    <div class="col col-md-3">
		                        <label>Fecha (dd/mm/yyyy)</label>
		                        <input id="fecha_ultima_factura" name="fecha_ultima_factura" placeholder="Fecha" class="input-date" type="text" />
		                    </div>
		                    <div class="col col-md-3">
		                        <label>Importe</label>
		                        <input id="importe_ultima_factura" name="importe_ultima_factura" placeholder="Importe" type="text" />
		                    </div>
		                </div>
						<div class="row" style="margin-top:30px">
							<div class="col col-md-3">
		                        <label>Fecha contrato original (dd/mm/yyyy)</label>
		                        <input id="fecha_contrato_original" name="fecha_contrato_original" placeholder="Fecha" class="input-date" type="text" />
		                    </div>
		                	<div class="col col-md-3">
		                        <label>Importe retenci&oacute;n</label>
		                        <input id="importe_retencion" name="importe_retencion" placeholder="Importe" type="text" />
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
		                        <input id="numero_oferta" name="numero_oferta" placeholder="N&uacute;mero" type="text" />
		                    </div>
		                    <div class="col col-md-3">
		                        <label>Fecha (dd/mm/yyyy)</label>
		                        <input id="fecha_oferta" name="fecha_oferta" placeholder="Fecha" class="input-date" type="text" />
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
		                        <input id="fecha_suministro_ini" name="fecha_suministro_ini" placeholder="Fecha inicial" class="input-date" type="text" />
		                    </div>
		                	<div class="col col-md-3">
		                        <label>Fecha fin (dd/mm/yyyy)</label>
		                        <input id="fecha_suministro_fin" name="fecha_suministro_fin" placeholder="Fecha final" class="input-date" type="text" />
		                    </div>
		                </div>
		            </div>
		            <div class="oculto contrato contrato_anexo">
		                <div class="row">
		                    <div class="col col-md-3">
	                    		<label>Fecha contrato subcontrataci&oacute;n (dd/mm/yyyy)</label>
	                    		<input id="fecha_anexo_contrato" name="fecha_anexo_contrato" placeholder="Fecha" class="input-date" type="text" />
	                    	</div>
		                </div>
		            </div>
	                <input class="submit" id="btnGenerar" type="submit" value="Generar documento" />
	                <input type="hidden" id="telefono" name="telefono" value="" />
					<input type="hidden" id="pago_fraccionado" name="pago_fraccionado" value="0" />
					<input type="hidden" id="plazo_estipulado" name="plazo_estipulado" value="0" />
					<input type="hidden" id="codigo_postal" name="codigo_postal" value="" />
					<input type="hidden" id="municipio" name="municipio" value="" />
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
		<script src="assets/jquery-ui.min.js"></script>
		<link href="assets/jquery-ui.min.css" rel="stylesheet" />
		<link href="assets/jquery-ui.theme.min.css" rel="stylesheet" />
    	<link href="assets/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<script src="assets/datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
		<script src="assets/datatables/jquery.dataTables.js"></script>
		<script src="assets/datatables/dataTables.bootstrap.js"></script>
		<script>
			var arrayAutoObras;
			var arrayAutoEmpresas;
			var arrayAutoAdministradores;
			var arrayAdminSeleccionados = new Array();
			var cifEmpresaSeleccionado = "";

			$(document).ready(function() {
				$("body").on("keydown", ".numero", function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,190])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});

				$(".input-date").datepicker({ clearBtn: true, language: "es", autoclose: true, todayHighlight: true });

				autocompletarObras();
				autocompletarEmpresas();
				autocompletarAdministradores(""); // obtiene todos, le envio vacio el cif de la empresa

				$("ul.listadoTipos > li").click(function() {
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

					$("#importe").attr("readonly", importeReadonly);
					$("#importe_retencion").attr("readonly", importeRetencionReadonly);
				});

				$("#cif").focusout(function() {
					var cif = $(this).val();
					cif = cif.replace(/\-/g, "").replace(/\./g, "");
					$.ajax({
		                url: "functions/render/ajax/accion-contratosV1.php",
		                data: {cif: cif, accion: 'cif'},
		                method: "post",
		                dataType: "json",
		                success: function(data) {
		                    $("#nombre").val(data.empresa);
		                    $("#domicilio").val(data.domicilio);
		                    $("#codigo_postal").val(data.cp);
		                    $("#municipio").val(data.municipio);
		                    $("#telefono").val(data.telefono);
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
		                url: "functions/render/ajax/accion-contratosV1.php",
		                data: {id: id, accion: 'eliminarAdmin'},
		                method: "post",
		                dataType: "json",
		                success: function(data) {
		                    autocompletarAdministradores();
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


					

				// $("#btnGenerar").click(function() {
				// 	$.ajax({
		  //               url: "functions/render/ajax/accion-contratosV1.php",
		  //               data: {cif: $("#cif").val(), tipo: $("input[name='tipo']:checked").val(), fecha: $("#fecha").val(), nombre: $("#nombre").val(), domicilio: $("#domicilio").val(), admin: $("#admin").val(), dni: $("#dni").val(), obra: $("#obra").val(), trabajos: $("#trabajos").val(), importe: $("#importe").val(), forma: $("#forma").val(), pago_fraccionado: $("#pago_fraccionado").val(), plazo_estipulado: $("#plazo_estipulado").val(), accion: 'generar'},
		  //               method: "post",
		  //               success: function(data) {
		  //                   console.log(data);
		  //               },
		  //               error: function() {
		  //                   console.log('No se encuentra el archivo.');
		  //               }
		  //           });
				// });
			});

			function autocompletarObras() {
                $.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratosV1.php",
                    data: {accion: "obra"},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                        arrayAutoObras = respuesta[0].jsonObras;
                    }
                });
            }

            function autocompletarEmpresas() {
                $.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratosV1.php",
                    data: {accion: "empresa"},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                        arrayAutoEmpresas = respuesta[0].jsonEmpresas;
                    }
                });
            }

            function autocompletarAdministradores(cifEmpresa) {
            	if (cifEmpresa == "" && cifEmpresaSeleccionado != "") {
            		cifEmpresa = cifEmpresaSeleccionado;
            	}
                $.ajax({
                    type: "POST",
                    url:  "functions/render/ajax/accion-contratosV1.php",
                    data: {accion: "admin", cifEmpresa: cifEmpresa},
                    async: false,
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                        arrayAutoAdministradores = respuesta[0].jsonAdministradores;
                        llenarListaAdmins();
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
			            	var descripcion = ui.item.desc;
			            	if (descripcion.trim() == "") {
			            		$("#obra").val(ui.item.value);
			            	} else {
			            		$("#obra").val(ui.item.desc);
			            	}
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
		                    $("#telefono").val(data.telefono);
		                    cifEmpresaSeleccionado = data.id; // si se borran elementos lo suyo es que se quede con el que estaba buscando
		                    $("#cifSeleccionado").text(" - Empresa: " + cifEmpresaSeleccionado);
		                    autocompletarAdministradores(data.id); // dentro se autollena la lista en la ventana modal
		                    autoSeleccionarAdmin(); // si solo hay uno seleccionarlo
			            }
			        });
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

     //        function seleccionarAdmin(id) {
     //        	for(var i = 0; i < arrayAutoAdministradores.length; i++) {
					// var a = arrayAutoAdministradores[i];
     //        		if (a.id == id) {
     //        			$("#admin").val(a.value);
	    //     			$("#dni").val(a.dni);
	    //                 $("#cargo").val(a.cargo);
	    //                 $("#modalAdmins").modal("toggle");
     //        		}
     //        	}
     //        }

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
            	console.log(arrayAdminSeleccionados);
            	console.log(arrayAdminSeleccionados.length);
            	for(var i = 0; i < arrayAdminSeleccionados.length; i++) {
					var a = arrayAdminSeleccionados[i];
					divAdmins += '<div class="row"></div>' +
								'<div id="fila-admin-'+i+'">' +
				                    '<div class="col col-md-5">' +
				                        '<label>Administrador</label>' +
				                        '<input id="admin-'+i+'" name="admin[]" placeholder="Administrador" type="text" value="'+a.value+'" required />' +
				                    '</div>' +
			                        '<div class="col col-md-2">' +
				                        '<label>DNI</label>' +
				                        '<input id="dni-'+i+'" name="dni[]" placeholder="DNI" type="text" value="'+a.dni+'" required />' +
				                    '</div>' +
				                    '<div class="col col-md-2">' +
				                        '<label>Cargo</label>' +
				                        '<input id="cargo-'+i+'" name="cargo[]" placeholder="Cargo" type="text" value="'+a.cargo+'" required />' +
				                    '</div>' +
				                    '<div class="col col-md-2">' +
				                        '<input name="'+i+'" type="button" class="btnEliminarFilaAdmin" style="display:block;margin:26px 0 0 10px;" value="x" />' +
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
	</body>
</html>