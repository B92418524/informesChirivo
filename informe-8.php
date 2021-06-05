<?php

require_once('config.php');

if (check_privileges('2')!=true){die;}

echo get_header('informe-8');
echo get_content('informe-8');
echo get_footer('informe-8');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';


?>