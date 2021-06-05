<?php

require_once('config.php');

if (check_privileges('18')!=true){die;}

//echo '<pre>'.print_r($_SESSION,true).'</pre>';
/* este será el informe de costes materiales desglosado */
echo get_header('informe-18');
echo get_content('informe-18');
echo get_footer('informe-18');

include_once 'template/footer-comun.php';

?>