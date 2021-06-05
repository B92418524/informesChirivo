<?php

require_once('config.php');

if (check_privileges('15')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-15');
echo get_content('informe-15');
echo get_footer('informe-15');

include_once 'template/footer-comun.php';

?>