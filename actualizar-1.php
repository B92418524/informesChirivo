<?php

require_once('config.php');
require_once('config_i3.php');

if (check_privileges('5')!=true){die;}

require_once(ABSPATH.'/functions/render/i3.php');
require_once(ABSPATH.'/functions/render/common.php');
require_once(ABSPATH.'/inc/mail/mail_functions.php');

/* actualizar los jefes de obra, llamar al procedimiento almacenado */
$jefes_obra = exec_db_data(array('actualizar-jefes-obra'));

?><!DOCTYPE html>
<html>
<head>
    <title>Generacion de informes</title>
	<meta charset="utf-8" />
	<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/bootstrap/css/jumbotron-narrow.css" rel="stylesheet" />
</head>
<body>


<div class="container">
      <div class="header clearfix">        
        <h3 class="text-muted">Generaci&oacute;n de informes de Jefes de Obra</h3>
      </div>

      <div class="jumbotron">
        <h1 style="font-size:30px; color:green; margin-bottom:30px; font-weight:bold;"><?php echo generar_informes(); ?></h1>
        <p class="lead" style="font-size:12px; text-align:left;">Este proceso genera los informes tomando como punto de partida los de meses anteriores y las nuevas obras dadas de alta determinando el resto de par&aacute;metros autom&aacute;ticamente.</p>
		<br/>
		<p style="font-size:12px; text-align:left;">Los informes solo pueden generarse una &uacute;nica vez para el mes en curso a partir del d&iacute;a 10. Una vez generados estan disponibles para revisi&oacute;n desde el panel de control. Para enviarlos a los jefes de obra seleccione la opci&oacute;n correspondiente del men&uacute;.</p>
		<br/>		
        <p><a class="btn btn-lg btn-success" href="informe-4d1.php" role="button">Ver informes</a></p>
      </div>      

      <footer class="footer">
        
      </footer>

    </div>






<?


//send_i3_mail();

?>