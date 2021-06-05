<?php

require_once('config.php');

if (check_privileges('20')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-19');
echo get_content('informe-20');
echo get_footer('informe-19');

include_once 'template/footer-comun.php';

?>