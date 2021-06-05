<?php

require_once('config.php');

if (check_privileges('10')!=true){die;}

echo get_header('informe-10');
echo get_content('informe-10');
echo get_footer('informe-10');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';


?>