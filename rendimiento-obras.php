<?php
include_once 'functions/comun/cabses.php';
$numeroInforme = '4';
include_once 'functions/comun/autorizacion.php';
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="robots" content="noindex,nofollow">
		<title>Rendimiento obras</title>
		<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	    <link href="assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	    <script src="assets/jquery.min.js"></script>
	    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	</head>
	<?php include_once 'template/menu.php'; ?>
	<body class="gris">
		<div id="wrap">
			<div class="row" style="margin: 80px 50px 10px 50px;">
				<table style="width: 100%; margin-bottom:20px;">
			        <tr>
			        	<td style="padding: 5px;">
			                <div class="form-inline">
			                    <div class="input-group">
			                        <div class="input-group-addon">Desde (*)</div>
			                        <input type="text" class="input-date input-sm form-control" id="fechaInicio" name="fechaInicio" value="<?php echo date('m-Y'); ?>" autocomplete="off"/>
			                        <div class="input-group-addon">Hasta</div>
			                        <input type="text" class="input-date input-sm form-control" id="fechaFin" name="fechaFin" value="<?php echo date('m-Y'); ?>" autocomplete="off"/>
			                    </div>
			                </div>
			            </td>
			            <td style="padding: 5px;">
			                <select id="selObras" class="form-control selectpicker" data-live-search="true" style="width:400px">
			                    
			                </select>
			            </td>
			            <td style="padding: 5px;">
			                <select id="selClientes" class="form-control selectpicker" data-live-search="true" style="width:400px">
			                    
			                </select>
			            </td>
			        </tr>
			        <tr>
			            <td style="padding: 5px;">
			                <select id="selJefesObra" class="form-control selectpicker" data-live-search="true" style="width:400px">
			                    
			                </select>
			            </td>
			            <!-- <td style="padding: 5px;">
			                <select id="selEncargados" class="form-control selectpicker" data-live-search="true" style="width:400px">
			                    
			                </select>
			            </td> -->
			            <td>
			                <!-- <button style="margin-left: 10px;" type="submit" class="btn btn-danger btn-sm" onclick="btnActualizarInforme();">Actualizar informe</button> -->

			                <button style="margin-left: 10px;" type="submit" class="btn btn-primary btn-sm" onclick="pintarTabla();">Filtrar</button>

			                <a href="javascript:window.location.reload()" style="margin-left: 10px;" type="submit" class="btn btn-default btn-sm">Borrar</a>

			                <form action="functions/interfaces/interface_rendimientoObras.php" method="post" style="display:inline" target="_blank" >
			            	<!-- este formulario imita el ajax de pintarTabla, por eso tiene los mismos "name" que los valores que se mandan en ese ajax, no tiene nada que ver con los name originales del form principal, y el id es solo para meterle los mismos valores que se eligen -->
			            		<input type="hidden" name="accion" value="imprimirExcel" />
			            		<input type="hidden" id="fechaInicio2" name="fechaInicio" value="" />
			            		<input type="hidden" id="fechaFin2" name="fechaFin" value="" />
			            		<input type="hidden" id="selObras2" name="obra" value="" />
			            		<input type="hidden" id="selClientes2" name="cliente" value="" />
			            		<input type="hidden" id="selJefesObra2" name="jefeObra" value="" />
			            		<!-- <input type="hidden" id="selEncargados2" name="encargado" value="" /> -->
			               		<button id="btnExcel" style="margin-left: 10px" class="btn btn-default btn-sm">Excel</button>
			               	</form>
			            </td>
			        </tr>
			    </table>
			    <div id="cargandoEjecucionProceso" style="padding:10px 0 10px 20px"></div>
	            <div id="divTabla" class="col-md-12"></div>
	        </div>
        </div>
		<script src="assets/jquery-ui.min.js"></script>
		<link href="assets/jquery-ui.min.css" rel="stylesheet" />
		<link href="assets/jquery-ui.theme.min.css" rel="stylesheet" />
    	<link href="assets/datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
		<script src="assets/datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
		<link href="assets/chosen/chosen.css" rel="stylesheet" />
		<script src="assets/chosen/chosen.jquery.min.js"></script>
		<script>
			var loading = "<img style='width:50px;position:absolute;top:150%;left:50%;margin-left:-25px;' src='img/loading.gif' />";
			var loading2 = "<img style='width:38px;margin-left:40px;' src='img/loading.gif' />";

			$(document).ready(function() {
				llenarSelectObras();
				llenarSelectClientes();
				llenarSelectJefesObra();
				// llenarSelectEncargados();

				$(".input-date").datepicker({ 
					clearBtn: true, 
					language: "es", 
					autoclose: true, 
					format: "mm-yyyy", 
					viewMode: "months", 
					minViewMode: "months", 
					orientation: "right top"
				});
			});

			function llenarSelectObras() {
				$.ajax({
                    type: "POST",
                    url:  "functions/interfaces/interface_rendimientoObras.php",
                    data: {accion: "obras"},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                    	$("#selObras").html(respuesta.html);
                    	$("#selObras").chosen({ search_contains: true });
                    }
                });
			}

			function llenarSelectClientes() {
				$.ajax({
                    type: "POST",
                    url:  "functions/interfaces/interface_rendimientoObras.php",
                    data: {accion: "clientes"},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                    	$("#selClientes").html(respuesta.html);
                    	$("#selClientes").chosen({ search_contains: true });
                    }
                });
			}

			function llenarSelectJefesObra() {
				$.ajax({
                    type: "POST",
                    url:  "functions/interfaces/interface_rendimientoObras.php",
                    data: {accion: "jefesObra"},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                    	$("#selJefesObra").html(respuesta.html);
                    	$("#selJefesObra").chosen({ search_contains: true });
                    }
                });
			}

			// function llenarSelectEncargados() {
			// 	$.ajax({
   //                  type: "POST",
   //                  url:  "functions/interfaces/interface_rendimientoObras.php",
   //                  data: {accion: "encargados"},
   //                  success: function(data) {
   //                      var respuesta = $.parseJSON(data);
   //                  	$("#selEncargados").html(respuesta.html);
   //                  	$("#selEncargados").chosen({ search_contains: true });
   //                  }
   //              });
			// }

			function ejecutarProcedimiento() {
				$.ajax({
	                url: "functions/interfaces/interface_rendimientoObras.php",
	                data: {accion: "ejecutarProcedimiento"},
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
				var fechaInicio = $("#fechaInicio").val();
				var fechaFin = $("#fechaFin").val();
				var obra = $("#selObras").val();
				var cliente = $("#selClientes").val();
				var jefeObra = $("#selJefesObra").val();
				// var encargado = $("#selEncargados").val();

				/* para imprimir excel, es otro form, y se necesitan estos valores como campos ocultos que enviar */
				$("#fechaInicio2").val(fechaInicio);
				$("#fechaFin2").val(fechaFin);
				$("#selObras2").val(obra);
				$("#selClientes2").val(cliente);
				$("#selJefesObra2").val(jefeObra);
				// $("#selEncargados2").val(encargado);

				if (fechaInicio != '' && fechaFin != '') {
					$.ajax({
		                url: "functions/interfaces/interface_rendimientoObras.php",
		                data: {accion: "pintarTabla", fechaInicio: fechaInicio, fechaFin: fechaFin, obra: obra, cliente: cliente, jefeObra: jefeObra},
		                method: "post",
		                beforeSend: function() {
		                	$("#divTabla").html(loading);
		                },
		                success: function(data) {
	                		var respuesta = $.parseJSON(data);
	                		$("#divTabla").html(respuesta.html);
			            	// sticky header
			            	$("#tabla").floatThead({
					            scrollingTop: 50
					        });
		                }
		            });
				} else {
					alert("Por favor, inserte una fecha de inicio y fin.");
				}				
			}

			function btnActualizarInforme() {
				var respuesta = confirm("La actualización de este informe puede durar 10 minutos, ¿seguro que quiere continuar?");
				if (respuesta) {
				    ejecutarProcedimiento();
				}				
			}
		</script>
		<?php
			include_once 'template/footer-comun.php';
		?>
		<script src="assets/sticky-header.js"></script>
	</body>
</html>