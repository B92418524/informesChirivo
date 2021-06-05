<?php

require_once('config.php');

if (check_privileges('9')!=true){die;}

echo get_header('informe-9');
echo get_content('informe-9');
echo get_footer('informe-9');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';


?>