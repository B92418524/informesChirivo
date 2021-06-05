<?php

require_once('config.php');
require_once('config_i3.php');

if (check_privileges('5')!=true){die;}

require_once(ABSPATH.'/functions/render/i3.php');
require_once(ABSPATH.'/functions/render/common.php');
require_once(ABSPATH.'/inc/mail/mail_functions.php');


?><!DOCTYPE html>
<html>
<head>
    <title>Envio de informes</title>
	<meta charset="utf-8" />
	<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="assets/bootstrap/css/jumbotron-narrow.css" rel="stylesheet" />
</head>
<body>


<div class="container">
      <div class="header clearfix">        
        <h3 class="text-muted">Env&iacute;o de informes de Jefes de Obra</h3>
      </div>

      <div class="jumbotron">
		<div style="max-height:300px; overflow-x: hidden; overflow-y: auto;">
			<?php echo send_i3_mail(); ?>			
		</div>
        <p class="lead" style="font-size:12px; text-align:left; margin-top:30px;">Este proceso env&iacute;a los informes generados y que a&uacute;n sigan pendientes de enviar.</p>		
		<br/>		
        <p><a class="btn btn-lg btn-success" href="informe-4d1.php" role="button">Ver informes</a></p>
      </div>      

      <footer class="footer">
        
      </footer>

    </div>






<?


//generar_informes();

?>