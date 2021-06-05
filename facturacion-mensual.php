<?php
include_once 'functions/comun/cabses.php';
$numeroInforme = '11';
include_once 'functions/comun/autorizacion.php';
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="robots" content="noindex,nofollow">
		<title>Facturaci&oacute;n mensual</title>
		<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	    <link href="assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	    <script src="assets/jquery.min.js"></script>
	    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
		<style>
			#tabla thead th {
				vertical-align: middle;
			}
			.colorLeyenda {
				width: 25px;
				height: 25px;
				border-radius: 15px;
				margin-right: 10px;
			}
		</style>
	</head>
	<?php 
		if ($_SESSION['id_jefe_obra'] != 0) {
			include_once 'template/menu_jefe_obra.html'; 
		} else {
			include_once 'template/menu.php'; 
		}
	?>
	<body class="gris">
		<div id="wrap">
			<div class="row" style="margin: 80px 50px 10px 50px;">
				<table style="width: 100%; margin-bottom:20px;">
			        <tr>
			            <td style="padding: 5px;">
			                <select id="selEstadosProyectos" class="form-control" data-live-search="true" style="width:150px">
			                	<option value="1" selected>Obras Activas</option>
			                	<option value="0">Obras Cerradas</option>
			                    <option value="">Todos los estados</option>            
			                </select>
			            </td>
			            <td style="padding: 5px;">
			                <select id="selProyectos" class="form-control selectpicker" data-live-search="true" style="width:400px">
			                    
			                </select>
			            </td>
			            <td style="padding: 5px;">
			            	 <select id="selEstadosFacturacion" class="form-control" data-live-search="true" style="width:200px">
			                	<option value="0">Pendiente</option>
			                	<option value="1">Facturado</option>
			                	<option value="2">Cerrada facturación</option>
			                	<option value="3">No se factura</option>
			                    <option value="">Todos los estados de facturación</option>            
			                </select>
			            </td>
			            <td style="padding: 5px;">
			                <div class="form-inline">
			                    <div class="input-group">
			                        <div class="input-group-addon">Ejercicio</div>
			                        <input id="selEjercicios" type="text" class="input-date input-sm form-control" value="<?php echo date('Y'); ?>" style="width:60px;font-size:14px" autocomplete="off"/>
			                    </div>
			                </div>
			            </td>
			            <td style="padding: 5px;">
			                <div class="form-inline">
			                    <div class="input-group">
			                        <div class="input-group-addon">Mes</div>
			                        <input id="selMeses" type="text" class="input-date input-sm form-control" value="<?php echo date('m'); ?>" style="width:60px;font-size:14px" autocomplete="off"/>
			                    </div>
			                </div>
			            </td>
			            <td>
			                <button style="margin-left: 20px;" type="submit" class="btn btn-danger btn-sm" onclick="ejecutarProcedimiento();">Actualizar informe</button>
			            </td>
			            <td>
			                <button style="margin-left: 20px;" type="submit" class="btn btn-primary btn-sm" onclick="pintarTabla();">Filtrar</button>
			            </td>
			            <td>
			                <a href="javascript:window.location.reload()" style="margin-left: 20px;" type="submit" class="btn btn-default btn-sm">Borrar</a>
			            </td>
			            <td>
			                <form action="functions/interfaces/interface_facturacionMensual.php" method="post" >
			            	<!-- este formulario imita el ajax de pintarTabla, por eso tiene los mismos "name" que los valores que se mandan en ese ajax, no tiene nada que ver con los name originales del form principal, y el id es solo para meterle los mismos valores que se eligen -->
			            		<input type="hidden" name="accion" value="imprimirExcel" />
			            		<input type="hidden" id="selEstadosProyectos2" name="estadoProyecto" value="" />
			            		<input type="hidden" id="selProyectos2" name="proyecto" value="" />
			            		<input type="hidden" id="selEstadosFacturacion2" name="estadoFacturacion" value="" />
			            		<input type="hidden" id="selEjercicios2" name="ejercicio" value="" />
			            		<input type="hidden" id="selMeses2" name="mes" value="" />
			               		<button id="btnExcel" style="margin-left: 20px" class="btn btn-default btn-sm">Excel</button>
			               	</form>
			            </td>
			        </tr>
			    </table>
			    <div class="row" style="margin-left:50px">
			    	<table>
			    		<tr>
			    			<td>
			    				<div class="colorLeyenda" style="background-color:#F4AAB8"></div>
			    			</td>
			    			<td style="width:200px">
			    				Pendiente
			    			</td>
			    			<td>
			    				<div class="colorLeyenda" style="background-color:#95A9F9"></div> 
			    			</td>
			    			<td style="width:200px">
			    				Facturado
			    			</td>
			    			<td>
			    				<div class="colorLeyenda" style="background-color:#8FFAA3"></div>
			    			</td>
			    			<td style="width:200px">
			    				Cerrada facturación
			    			</td>
			    			<td>
			    				<div class="colorLeyenda" style="background-color:#DB7242"></div>
			    			</td>
			    			<td style="width:100px">
			    				No se factura
			    			</td>
			    		</tr>
			    	</table>
			    </div>
			    <div id="cargandoEjecucionProceso" style="padding:10px 0 10px 20px"></div>
	            <div class="col-md-12">                
	                <table id="tabla" class="table table-striped table-bordered table-hover" style="border:0 !important;">
	                    <thead style="background-color:#fff;">
	                        <tr style="border:solid 1px silver;">
	                            <th style="min-width:20px;">#</th>
	                            <th style="min-width:20px;">Ejercicio</th>
	                            <th style="min-width:20px;">Mes</th>
	                            <th style="min-width:20px;">Tipo</th>
	                            <th style="min-width:30px;">C&oacute;digo</th>
	                            <th style="min-width:300px;">Proyecto</th>
	                            <th style="min-width:40px;">Contrato/Anexo</th>
	                            <th style="min-width:500px;">Descripci&oacute;n</th>
	                            <th style="min-width:100px;">Importe mes</th>
	                            <th style="min-width:30px;">Importe contrato</th>
	                            <th style="min-width:30px;">Importe origen</th>
	                            <th style="min-width:30px;">Importe cartera pendiente</th>
	                            <th style="min-width:30px;">Total proyecto facturado mes</th>
	                            <th style="min-width:30px;">Total importe de TODOS los contratos</th>
	                            <th style="min-width:30px;">Total proyecto facturado a origen</th>
	                            <th style="min-width:30px;">Total proyecto cartera pendiente</th>
	                            <th style="min-width:80px;">Estado</th>
	                            <th style="min-width:300px;">Observaciones</th> 
	                            <th style="min-width:10px;">Activo</th>                                                      
	                        </tr>
	                    </thead>
	                    <tbody id="tablaContenido">
	                                          
	                    </tbody>
	                </table>
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
		<script>
			var loading = "<img style='width:50px;position:absolute;top:150%;left:50%;margin-left:-25px;' src='img/loading.gif' />";
			var loading2 = "<img style='width:38px;margin-left:40px;' src='img/loading.gif' />";
			var selProyectos = "";

			$(document).ready(function() {
				llenarSelectProyectos("", true); // estado vacio y primera vez para cargar las activas

				$("#selEjercicios").datepicker({ 
					clearBtn: true, 
					language: "es", 
					autoclose: true, 
					format: "yyyy", 
					viewMode: "years", 
					minViewMode: "years", 
					orientation: "right top" 
				});

				$("#selMeses").datepicker({ 
					clearBtn: true, 
					language: "es", 
					autoclose: true, 
					format: "mm", 
					viewMode: "months",
					minViewMode: "months", 
					orientation: "right top" 
				});

				$("#selEstadosProyectos").change(function() {
					var estado = this.value;
					llenarSelectProyectos(estado, false);
				});

				$("body").on("change", ".selCambiarEstadoFacturacion", function() {
					var nuevoEstado = this.value;
					var fila = $(this).closest("tr");
					var array = identificadorFila(fila);
					$.ajax({
	                    type: "POST",
	                    url:  "functions/interfaces/interface_facturacionMensual.php",
	                    data: {accion: "cambiarEstadoFacturacion", ejercicio: array[0], mes: array[1], codigoProyecto: array[2], anexo: array[3], nuevoEstado: nuevoEstado},
	                    success: function(data) {
	                    }
	                });
				});

				$("body").on("keyup", ".txtCambiarObservaciones", function() {
					var nuevaObservacion = this.value;
					var fila = $(this).closest("tr");
					var array = identificadorFila(fila);
					$.ajax({
	                    type: "POST",
	                    url:  "functions/interfaces/interface_facturacionMensual.php",
	                    data: {accion: "cambiarObservaciones", ejercicio: array[0], mes: array[1], codigoProyecto: array[2], anexo: array[3], nuevaObservacion: nuevaObservacion},
	                    success: function(data) {
	                    }
	                });
				});

			});

			function identificadorFila(fila) {
				var array = new Array;
				var ejercicio = $(fila).attr("ejercicio");
				var mes = $(fila).attr("mes");
				var codigoProyecto = $(fila).attr("codigoProyecto");
				var anexo = $(fila).attr("anexo");

				array.push(ejercicio);
				array.push(mes);
				array.push(codigoProyecto);
				array.push(anexo);

				// console.log("----");
				// console.log(ejercicio);
				// console.log(mes);
				// console.log(codigoProyecto);
				// console.log(anexo);
				// console.log("----");

				return array;
			}

			function llenarSelectProyectos(estado, primeraVez) {
				$.ajax({
                    type: "POST",
                    url:  "functions/interfaces/interface_facturacionMensual.php",
                    data: {accion: "proyectos", estado: estado, primeraVez: primeraVez},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                        if (selProyectos != "") {
                        	selProyectos.empty();
							selProyectos.append(respuesta.html);
		                    selProyectos.trigger("chosen:updated");
                        } else {
                        	$("#selProyectos").html(respuesta.html);
                        	selProyectos = $("#selProyectos").chosen({ search_contains: true });
                        }
                    }
                });
			}

			function ejecutarProcedimiento() {
				var mes = $("#selMeses").val();
				$.ajax({
	                url: "functions/interfaces/interface_facturacionMensual.php",
	                data: {accion: "ejecutarProcedimiento", mes: mes},
	                method: "post",
	                beforeSend: function() {
	                	$("#cargandoEjecucionProceso").html("Por favor espere " + loading2);
	                },
	                success: function(data) {
	                	$("#cargandoEjecucionProceso").html("");
	                	pintarTabla();
	                }
	            });
			}

			function pintarTabla() {
				var estadoProyecto = $("#selEstadosProyectos").val();
				var proyecto = $("#selProyectos").val();
				var estadoFacturacion = $("#selEstadosFacturacion").val();
				var ejercicio = $("#selEjercicios").val();
				var mes = $("#selMeses").val();

				/* para imprimir excel, es otro form, y se necesitan estos valores como campos ocultos que enviar */
				$("#selEstadosProyectos2").val(estadoProyecto);
				$("#selProyectos2").val(proyecto);
				$("#selEstadosFacturacion2").val(estadoFacturacion);
				$("#selEjercicios2").val(ejercicio);
				$("#selMeses2").val(mes);

				$.ajax({
	                url: "functions/interfaces/interface_facturacionMensual.php",
	                data: {accion: "pintarTabla", estadoProyecto: estadoProyecto, proyecto: proyecto, estadoFacturacion: estadoFacturacion,ejercicio: ejercicio, mes: mes},
	                method: "post",
	                beforeSend: function() {
	                	$("#tablaContenido").html(loading);
	                },
	                success: function(data) {
                		var respuesta = $.parseJSON(data);
                		$("#tablaContenido").html(respuesta.html);
		            	// sticky header
		            	$("#tabla").floatThead({
				            scrollingTop: 50
				        });
	                }
	            });
			}
		</script>
		<?php
			include_once 'template/footer-comun.php';
		?>
		<script src="assets/sticky-header.js"></script>
	</body>
</html>