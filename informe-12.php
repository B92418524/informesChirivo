<?php

require_once('config.php');

if (check_privileges('12')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-12');
echo get_content('informe-12');
echo get_footer('informe-12');

include_once 'template/footer-comun.php';

?>