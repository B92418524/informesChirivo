<?php

require_once('config.php');

$type=cG('type');

if (!is_numeric($type))
{
	die;
}
else
{
	$query=cG('query');	
}

if ($type=='1')
{
	$query=get_db_data(array('ajax-listar-contratos-por-proyecto',$query));
	
	echo '<option value="">Todos los contratos</option>';
	
	foreach ($query as $q)
	{		
		
		if ($q['codigocontrato']!='')
		{
			echo '<option value="'.$q['codigocontrato'].'">'.$q['codigocontrato'].' - '.$q['contrato'].'</option>';
		}
	}
}
if ($type=='2')
{
	$query=get_db_data(array('ajax-listar-anexos-por-contrato',$query));
	
	echo '<option value="">Todos los anexos</option>';
	
	foreach ($query as $q)
	{
		if ($q['codigoanexo']!='')
		{
			echo '<option value="'.$q['codigoanexo'].'">'.$q['codigoanexo'].' - '.$q['anexo'].'</option>';
		}
	}
	
	
}
if ($type=='3')
{
	
	$params=explode(':',$query);
	
	$p1=$params[0];
	$p2=$params[1];
	
	$result=get_db_data(array('proveedor-por-cif',$p1,$p2));
	
	foreach ($result as $r)
	{
		echo $r['CodigoProveedor'].' - '.$r['RazonSocial'];
	}
	
	
}


?>