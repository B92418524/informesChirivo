<?php

require_once('config.php');

if (check_privileges('14')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-14');
echo get_content('informe-14');
echo get_footer('informe-14');

include_once 'template/footer-comun.php';

?>