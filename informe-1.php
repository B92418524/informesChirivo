<?php

require_once('config.php');

if (check_privileges('1')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-1');
echo get_content('informe-1');
echo get_footer('informe-1');

include_once 'template/footer-comun.php';

?>