<?php

require_once('config.php');

if (check_privileges('x')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-7');
echo get_content('informe-7');
echo get_footer('informe-7');

include_once 'template/footer-comun.php';


?>