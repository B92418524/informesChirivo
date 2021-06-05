<?php

require_once('config.php');

if (check_privileges('11')!=true){die;}

$GLOBALS['ADMIN_I9']=true;

echo get_header('informe-9');
echo get_content('informe-9');
echo get_footer('informe-9');


//echo '<pre>'.print_r($_SESSION,true).'</pre>';

include_once 'template/footer-comun.php';


?>