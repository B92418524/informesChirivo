<?php

// require_once('config.php');

require_once('config_i3.php');

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header('informe-3');
echo get_content('informe-3');
echo get_footer('informe-3');

include_once 'template/footer-comun.php';

?>