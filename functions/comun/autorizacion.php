<?php

/* si todo está correcto, ahora se comprueba si puede acceder al informe que ha entrado, siempre y cuando se sepa cuál es por la variable */
if (isset($numeroInforme) && $numeroInforme != '' && !empty($_SESSION['username'])) {
	include_once __DIR__ . '/bd.class.php';
	$bd = new bd();
	$select = 'TOP 1 id_informe';
	$from = DB_APP.'usuariosinformes';
	$where = "WHERE codigousuario='".$_SESSION['username']."' AND id_informe=".$numeroInforme;
	$aAutorizacion = $bd->consulta($select, $from, $where);
	if (!is_array($aAutorizacion)) {
		header('Location: template/noautorizado.html');
	}
} else {
	header('Location: login.php');
}