<?php

require_once('config.php');

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('listar-detalle-retencion-i2');
echo get_content('listar-detalle-retencion-i2');
echo get_footer('listar-detalle-retencion-i2');


?>