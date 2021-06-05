<?php

require_once('config.php');

if (check_privileges('16')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-16');
echo get_content('informe-16');
echo get_footer('informe-16');

include_once 'template/footer-comun.php';

?>