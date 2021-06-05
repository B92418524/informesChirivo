<?php

require_once('config.php');

if (check_privileges('4')!=true){die;}

echo get_header('informe-4');
echo get_content('informe-4');
echo get_footer('informe-4');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';


?>