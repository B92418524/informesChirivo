<?php

require_once('config.php');

if (check_privileges('13')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-13');
echo get_content('informe-13');
echo get_footer('informe-13');

include_once 'template/footer-comun.php';

?>