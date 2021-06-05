<?php

include_once '../../comun/bd.class.php';
include_once '../../clases/carteraClientes.class.php';
$carteraClientes = new carteraClientes();
$resultado = $carteraClientes->ejecutarProcedimiento();

?>