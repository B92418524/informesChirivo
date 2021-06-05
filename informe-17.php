<?php
/* EL 17 FUE EN SU MOMENTO CONTRATOS, PERO ESA PARTE SE HIZO CON CLASES */

require_once('config.php');

if (check_privileges('17')!=true){die;}

echo get_header('informe-17');
echo get_content('informe-17');
echo get_footer('informe-17');

include_once 'template/footer-comun.php';

?>