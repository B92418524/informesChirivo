<?php

require_once('config.php');

if (check_privileges('5')!=true){die;}

echo get_header('informe-5');
echo get_content('informe-5');
echo get_footer('informe-5');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';

?>