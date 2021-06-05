<?php

require_once('config.php');

if (check_privileges('2')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-2');
echo get_content('informe-2');
echo get_footer('informe-2');

include_once 'template/footer-comun.php';

?>