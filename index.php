<?php

require_once('config.php');

//header('Location: informe-1.php');

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

echo get_header();
echo get_content();
echo get_footer();

include_once 'template/footer-comun.php';
?>