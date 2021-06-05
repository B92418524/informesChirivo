<?php

require_once('config.php');

if (check_privileges('5')!=true){die;}

echo get_header('informe-6');
echo get_content('informe-6');
echo get_footer('informe-6');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';


?>