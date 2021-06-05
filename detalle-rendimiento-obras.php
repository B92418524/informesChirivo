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
		<title>Detalle rendimiento obras</title>
		<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	    <link href="assets/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	    <script src="assets/jquery.min.js"></script>
	    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	    <link href="assets/jqplot/jquery.jqplot.min.css" rel="stylesheet">
	    <script src="assets/jqplot/jquery.jqplot.min.js"></script>
	    <script src="assets/jqplot/plugins/jqplot.barRenderer.js"></script>
	    <script src="assets/jqplot/plugins/jqplot.categoryAxisRenderer.js"></script>
	    <script src="assets/jqplot/plugins/jqplot.pointLabels.js"></script>
	    <script src="assets/jqplot/plugins/jqplot.canvasAxisTickRenderer.js"></script>
	    <script src="assets/jqplot/plugins/jqplot.canvasTextRenderer.js"></script>
	    <script src="assets/chosen/chosen.jquery.min.js"></script>
		<style>
			th{
				    border-top: 1px solid #ddd !important;
			}
		</style>
	</head>
	<?php include_once 'template/menu.php'; ?>
	<body class="gris">
		<div id="wrap">
			<div class="row" style="margin: 80px 50px 10px 50px;">
				<table style="width: 100%; margin-bottom:20px;">
			        <tr>
			            <td style="padding: 5px;">
			                <select id="selObras" class="form-control selectpicker" data-live-search="true" style="width:400px"></select>
			            </td>
			        </tr>
			        <tr>
			            <td>
			                <button style="margin-left: 10px;" type="submit" class="btn btn-primary btn-sm" onclick="pintarTabla();">Filtrar</button>
			                <a href="javascript:window.location.reload()" style="margin-left: 10px;" type="submit" class="btn btn-default btn-sm">Borrar</a>
			                <form action="functions/interfaces/interface_rendimientoObrasDetalle.php" method="post" style="display:inline" target="_blank" >
			            	<!-- este formulario imita el ajax de pintarTabla, por eso tiene los mismos "name" que los valores que se mandan en ese ajax, no tiene nada que ver con los name originales del form principal, y el id es solo para meterle los mismos valores que se eligen -->
			            		<input type="hidden" name="accion" value="imprimirExcel" />
			            		<input type="hidden" id="selObras2" name="obra" value="" />
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
                    url:  "functions/interfaces/interface_rendimientoObrasDetalle.php",
                    data: {accion: "obras"},
                    success: function(data) {
                        var respuesta = $.parseJSON(data);
                    	$("#selObras").html(respuesta.html);
                    	$("#selObras").chosen({ search_contains: true });
                    }
                });
			}

			function pintarTabla() {
				var obra = $("#selObras").val();

				/* para imprimir excel, es otro form, y se necesitan estos valores como campos ocultos que enviar */
				$("#selObras2").val(obra);
				
				$.ajax({
	                url: "functions/interfaces/interface_rendimientoObrasDetalle.php",
	                data: {accion: "pintarTabla", obra: obra},
	                method: "post",
	                beforeSend: function() {
	                	$("#divTabla").html(loading);
	                },
	                success: function(data) {
                		var respuesta = $.parseJSON(data);
                		$("#divTabla").html(respuesta.html);
                		pintarGrafico();
	                }
	            });
			}

		</script>
		<?php
			include_once 'template/footer-comun.php';
		?>
	</body>
</html>