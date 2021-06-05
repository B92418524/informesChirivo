<?php

function process_i3($content) {
	$id1=cG('id1');
	$id2=cG('id2');
	$id3=cG('admin');
	
	$option_button_be=cP('BE');
	$option_button_bg=cP('BG');
	$option_button_bm=cP('BM');
	
	if (cP('DIA_DE_CIERRE')=='' and cP('process')=='true' and $option_button_be=='send') {	
		// Si trata de enviar sin fecha lo que hacemos es grabar	
		$advert_error='1';
		$option_button_be='';
		$option_button_bg='save';
	}
	
	
	$error=1;
	
	if ($id1!='' and $id2!='') {				
		$data=get_db_data(array('obtener-i3-por-id', $id1, $id2));
		
		if (!is_array($data)) {
			$error=1;
		} else {
			if (count($data)>1)	{
				$error=3;
			} else {
				if ($data['0']['ESTADO']=='PROCESADO') {
					if ($id3=='true') {
						$GLOBALS['ADMIN_MODIFY_I3']=true;
						$content=editar_informe_procesado($content);	
						$error=false;																
					} else {
						return ver_formulario_procesado($content);
					}
				} else {
					
					if(cG('advert_error')=='1' and cP('BG')!='save' and cP('DIA_DE_CIERRE')=='') {
						echo '<div style="background-color:red;padding:10px;margin-bottom:20px;text-align:center;font-weight:bold;color:#fff;">Debe indicar una fecha de cierre para enviar el informe</div>';
					}

					$content=render_a_files($content);
					$content=render_b_files($content);
					
					$error=false;
				}
			}
		}
	}
	
	$meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	if($error===false) {
		//echo 'error false'; die;	
		
		$FECHA_GRABACION			=		'GETDATE()';
		
		$ID_INFORME					=		$id1;
		$ID_JEFE_OBRA				=		$data['0']['ID_JEFE_OBRA'];
		$ID_OBRA					=		$data['0']['ID_OBRA'];
		$MES						=		$data['0']['MES'];
		$YEAR						=		$data['0']['YEAR'];
		//$DIA_DE_CIERRE			=		date_normalizer($data['0']['DIA_DE_CIERRE']); // <=====================
		$DIA_DE_CIERRE				=		cP('DIA_DE_CIERRE'); // <=====================
		
		$PF_CIERRE					=		cP('PF_CIERRE');
		$PF_FALTA_CONTRATO			=		cP('PF_FALTA_CONTRATO');
		$PF_APROBACION				=		cP('PF_APROBACION');
		$PF_OTROS					=		cP('PF_OTROS');
		$MOTIVOS					=		cP('MOTIVOS');
		$TOTAL_PENDIENTE_COBRO		=		cP('TOTAL_PENDIENTE_COBRO');
		
		$TOTAL_PENDIENTE_DE_PAGO	=		cP('TOTAL_PENDIENTE_DE_PAGO');
		
		$PAGO_ANTICIPADO_REALIZADO	=		cP('PAGO_ANTICIPADO_REALIZADO');
		
		$OBSERVACIONES_GENERALES	=		cP('OBSERVACIONES_GENERALES');

		$A=array();	$B=array();
		
		//echo '<pre> Numero maximo de lineas: '.MAX_LINES.'</pre>';
		
		for ($i = 1; $i <= MAX_LINES; $i++) {
			//echo '<pre> Recorrida linea '.$i.' de '.MAX_LINES.'</pre>';
			
			$a1=cP('P_CERTIFICACION_'.$i); $a2=cP('IMPORTE_P_CERTIFICACION_'.$i); $a3=cP('MOTIVO_P_CERTIFICACION_'.$i);
			
			//echo '<pre>'.$a1.' '.$a2.' '.$a3.'</pre>';
			
			if ($a1!='' or $a2!='' or $a3!='') {	
				//echo 'A['.$i.']<br/>';
				
				$A[$i]['P_CERTIFICACION_'.$i]			=	$a1;
				$A[$i]['IMPORTE_P_CERTIFICACION_'.$i]	=	$a2;
				$A[$i]['MOTIVO_P_CERTIFICACION_'.$i]	=	$a3;
				
				$AE[$i]['P_CERTIFICACION_'.$i]			=	urlencode($a1);
				$AE[$i]['IMPORTE_P_CERTIFICACION_'.$i]	=	urlencode($a2);
				$AE[$i]['MOTIVO_P_CERTIFICACION_'.$i]	=	urlencode($a3);
			}
			
			if (count($A)==0) {
				$A[$i]['P_CERTIFICACION_1']				=	'';
				$A[$i]['IMPORTE_P_CERTIFICACION_1']		=	'0';
				$A[$i]['MOTIVO_P_CERTIFICACION_1']		=	'';
			}
			
			$b1=cP('ACOPIO_ENTREGA_'.$i); $b2=cP('IMPORTE_ACOPIO_ENTREGA_'.$i); $b3=cP('MOTIVO_ACOPIO_ENTREGA_'.$i);
			
			//echo '<pre>'.$b1.' '.$b2.' '.$b3.'</pre>';
			
			if ($b1!='' or $b2!='' or $b3!='') {		
				//echo 'B['.$i.']<br/>';
				
				$B[$i]['ACOPIO_ENTREGA_'.$i]			=	$b1;
				$B[$i]['IMPORTE_ACOPIO_ENTREGA_'.$i]	=	$b2;
				$B[$i]['MOTIVO_ACOPIO_ENTREGA_'.$i]		=	$b3;
				
				$BE[$i]['ACOPIO_ENTREGA_'.$i]			=	urlencode($b1);
				$BE[$i]['IMPORTE_ACOPIO_ENTREGA_'.$i]	=	urlencode($b2);
				$BE[$i]['MOTIVO_ACOPIO_ENTREGA_'.$i]	=	urlencode($b3);
			}
			
			if (count($B)==0) {
				$B[$i]['ACOPIO_ENTREGA_1']				=	'';
				$B[$i]['IMPORTE_ACOPIO_ENTREGA_1']		=	'0';
				$B[$i]['MOTIVO_ACOPIO_ENTREGA_1']		=	'';
			}	
		}	
		
		if (cP('process')=='true') {
			
			if ($option_button_be=='send') {
				//echo 'solicitado enviar'; die;
				
				$records=0;
				
				$i=1;
				foreach($A as $row) {				
					$data="'".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."','".sql_date_to_store($DIA_DE_CIERRE)."',GETDATE(),'".$PF_CIERRE."','".$PF_FALTA_CONTRATO."','".$PF_APROBACION."','".$PF_OTROS."','".$MOTIVOS."','".$TOTAL_PENDIENTE_COBRO."','".$row['P_CERTIFICACION_'.$i]."','".$row['IMPORTE_P_CERTIFICACION_'.$i]."','".$row['MOTIVO_P_CERTIFICACION_'.$i]."','".$TOTAL_PENDIENTE_DE_PAGO."','".$PAGO_ANTICIPADO_REALIZADO."','".$OBSERVACIONES_GENERALES."'";
					$r=store_db_data(array('grabar-i3-a',$data));
					$records=$records+$r;
					$i++;
				}
				
				$i=1;
				foreach($B as $row) {
					$data="'".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."','".sql_date_to_store($DIA_DE_CIERRE)."',GETDATE(),'".$PF_CIERRE."','".$PF_FALTA_CONTRATO."','".$PF_APROBACION."','".$PF_OTROS."','".$MOTIVOS."','".$TOTAL_PENDIENTE_COBRO."','".$TOTAL_PENDIENTE_DE_PAGO."','".$row['ACOPIO_ENTREGA_'.$i]."','".$row['IMPORTE_ACOPIO_ENTREGA_'.$i]."','".$row['MOTIVO_ACOPIO_ENTREGA_'.$i]."','".$PAGO_ANTICIPADO_REALIZADO."','".$OBSERVACIONES_GENERALES."'";
					$r=store_db_data(array('grabar-i3-b',$data));
					$records=$records+$r;
					$i++;
				}
				
				if ($records>0) {
					update_db_data(array('actualizar-estado-i3',$ID_INFORME,'PROCESADO'));
					return '<body><div style="margin-top:20%; color:green; font-size:30px; text-align:center;">Informe grabado correctamente</div></body>';
				} else {
					return '<body><div style="margin-top:20%; color:red; font-size:30px; text-align:center;">Se ha producido un error al procesar el formulario</div></body>';
				}
				
				/*
				echo '<pre>'.print_r($FECHA_GRABACION,true).'</pre>';
				echo '<pre>'.print_r($ID_INFORME,true).'</pre>';
				echo '<pre>'.print_r($ID_JEFE_OBRA,true).'</pre>';
				echo '<pre>'.print_r($ID_OBRA,true).'</pre>';
				echo '<pre>'.print_r($MES,true).'</pre>';
				echo '<pre>'.print_r($DIA_DE_CIERRE,true).'</pre>';
				echo '<pre>'.print_r($PF_CIERRE,true).'</pre>';
				echo '<pre>'.print_r($PF_FALTA_CONTRATO,true).'</pre>';
				echo '<pre>'.print_r($PF_APROBACION,true).'</pre>';
				echo '<pre>'.print_r($PF_OTROS,true).'</pre>';
				echo '<pre>'.print_r($MOTIVOS,true).'</pre>';
				echo '<pre>'.print_r($TOTAL_PENDIENTE_COBRO,true).'</pre>';
							
				echo '<pre>'.print_r($TOTAL_PENDIENTE_DE_PAGO,true).'</pre>';
							
				echo '<pre>'.print_r($PAGO_ANTICIPADO_REALIZADO,true).'</pre>';
				echo '<pre>'.print_r($A,true).'</pre>';
				echo '<pre>'.print_r($B,true).'</pre>';	
				*/	
			} elseif ($option_button_bm=='modify') {
				//echo 'solicitado modificar'; die;
				
				//renombramos los registros con esta id en la tabla datos
				$rows=update_db_data(array('renombrar-id-para-modificar-procesados-i3',$id1,'M_'.$id1.'_'.generate_random_hash()));
				
				//echo 'Modificados '.$rows.' registros';
				//die;
				
				$records=0;
				
				$i=1;
				foreach($A as $row)	{				
					$data="'".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."','".sql_date_to_store($DIA_DE_CIERRE)."',GETDATE(),'".$PF_CIERRE."','".$PF_FALTA_CONTRATO."','".$PF_APROBACION."','".$PF_OTROS."','".$MOTIVOS."','".$TOTAL_PENDIENTE_COBRO."','".$row['P_CERTIFICACION_'.$i]."','".$row['IMPORTE_P_CERTIFICACION_'.$i]."','".$row['MOTIVO_P_CERTIFICACION_'.$i]."','".$TOTAL_PENDIENTE_DE_PAGO."','".$PAGO_ANTICIPADO_REALIZADO."','".$OBSERVACIONES_GENERALES."'";
					$r=store_db_data(array('grabar-i3-a',$data));
					$records=$records+$r;
					$i++;
				}
				
				$i=1;
				foreach($B as $row)	{
					$data="'".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."','".sql_date_to_store($DIA_DE_CIERRE)."',GETDATE(),'".$PF_CIERRE."','".$PF_FALTA_CONTRATO."','".$PF_APROBACION."','".$PF_OTROS."','".$MOTIVOS."','".$TOTAL_PENDIENTE_COBRO."','".$TOTAL_PENDIENTE_DE_PAGO."','".$row['ACOPIO_ENTREGA_'.$i]."','".$row['IMPORTE_ACOPIO_ENTREGA_'.$i]."','".$row['MOTIVO_ACOPIO_ENTREGA_'.$i]."','".$PAGO_ANTICIPADO_REALIZADO."','".$OBSERVACIONES_GENERALES."'";
					$r=store_db_data(array('grabar-i3-b',$data));
					$records=$records+$r;
					$i++;
				}
				
				if ($records>0) {
					update_db_data(array('actualizar-estado-i3',$ID_INFORME,'PROCESADO'));
					return '<body><div style="margin-top:20%; color:green; font-size:30px; text-align:center;">Informe modificado correctamente</div></body>';
				} else {
					return '<body><div style="margin-top:20%; color:red; font-size:30px; text-align:center;">Se ha producido un error al modificar el formulario</div></body>';
				}
				
				/*
				echo '<pre>'.print_r($FECHA_GRABACION,true).'</pre>';
				echo '<pre>'.print_r($ID_INFORME,true).'</pre>';
				echo '<pre>'.print_r($ID_JEFE_OBRA,true).'</pre>';
				echo '<pre>'.print_r($ID_OBRA,true).'</pre>';
				echo '<pre>'.print_r($MES,true).'</pre>';
				echo '<pre>'.print_r($DIA_DE_CIERRE,true).'</pre>';
				echo '<pre>'.print_r($PF_CIERRE,true).'</pre>';
				echo '<pre>'.print_r($PF_FALTA_CONTRATO,true).'</pre>';
				echo '<pre>'.print_r($PF_APROBACION,true).'</pre>';
				echo '<pre>'.print_r($PF_OTROS,true).'</pre>';
				echo '<pre>'.print_r($MOTIVOS,true).'</pre>';
				echo '<pre>'.print_r($TOTAL_PENDIENTE_COBRO,true).'</pre>';
							
				echo '<pre>'.print_r($TOTAL_PENDIENTE_DE_PAGO,true).'</pre>';
							
				echo '<pre>'.print_r($PAGO_ANTICIPADO_REALIZADO,true).'</pre>';
				echo '<pre>'.print_r($A,true).'</pre>';
				echo '<pre>'.print_r($B,true).'</pre>';	
				*/	
			} elseif ($option_button_bg=='save') {
				//echo 'solicitado guardar'; die;
				
				//$sa['FECHA_GRABACION']			=	urlencode(date('d/m/Y h:i:s'));
				$sa['ID_INFORME']					=	urlencode($ID_INFORME);
				$sa['ID_JEFE_OBRA']					=	urlencode($ID_JEFE_OBRA);
				$sa['ID_OBRA']						=	urlencode($ID_OBRA);
				$sa['MES']							=	urlencode($MES);
				$sa['DIA_DE_CIERRE']				=	urlencode($DIA_DE_CIERRE);
				$sa['PF_CIERRE']					=	urlencode($PF_CIERRE);
				$sa['PF_FALTA_CONTRATO']			=	urlencode($PF_FALTA_CONTRATO);
				$sa['PF_APROBACION']				=	urlencode($PF_APROBACION);
				$sa['PF_OTROS']						=	urlencode($PF_OTROS);
				$sa['MOTIVOS']						=	urlencode($MOTIVOS);
				$sa['TOTAL_PENDIENTE_COBRO']		=	urlencode($TOTAL_PENDIENTE_COBRO);
				$sa['TOTAL_PENDIENTE_DE_PAGO']		=	urlencode($TOTAL_PENDIENTE_DE_PAGO);
				$sa['PAGO_ANTICIPADO_REALIZADO']	=	urlencode($PAGO_ANTICIPADO_REALIZADO);
				$sa['OBSERVACIONES_GENERALES']		=	urlencode($OBSERVACIONES_GENERALES);
				
				if (isset($AE)){$sa['A']=$AE;}			
				if (isset($BE)){$sa['B']=$BE;}
				
				//echo '<pre>Datos para almacenar: '.print_r($sa,true).'</pre>';
				
				update_db_data(array('guardar-i3',$ID_INFORME,serialize($sa)));
				
				if(isset($advert_error)) {
					header('Location: '.$_SERVER['REQUEST_URI'].'&advert_error=1');
				} else {				
					return '<body><div style="margin-top:20%; color:green; font-size:30px; text-align:center;">Informe grabado correctamente</div></body>';
				}
				
				//echo 'solicitado guardar'; die;
			} else {
				echo 'Error 12312';				
			}
		} else {			
			//echo 'No procesa'; die;
			
			$DIA_DE_CIERRE_V				=	'';
			$PF_CIERRE_V					=	'';
			$PF_FALTA_CONTRATO_V			=	'';
			$PF_APROBACION_V				=	'';
			$PF_OTROS_V						=	'';
			$TOTAL_PENDIENTE_COBRO_V		=	'';
			$TOTAL_PENDIENTE_DE_PAGO_V		=	'';
			$OBSERVACIONES_GENERALES_V		=	'';
			$MOTIVOS_V						=	'';
			$PAGO_ANTICIPADO_REALIZADO_V    =   '';
			
			if (isset($data['0']['DATA'])) {
				$sv=unserialize($data['0']['DATA']);
				
				if (isset($sv['DIA_DE_CIERRE']))				{	$DIA_DE_CIERRE_V			    =	urldecode($sv['DIA_DE_CIERRE']);			    } else { }
				if (isset($sv['PF_CIERRE']))					{	$PF_CIERRE_V				    =	urldecode($sv['PF_CIERRE']);				    } else { }
				if (isset($sv['PF_FALTA_CONTRATO']))			{	$PF_FALTA_CONTRATO_V		    =	urldecode($sv['PF_FALTA_CONTRATO']);		    } else { }
				if (isset($sv['PF_APROBACION']))				{	$PF_APROBACION_V			    =	urldecode($sv['PF_APROBACION']);			    } else { }
				if (isset($sv['PF_OTROS']))						{	$PF_OTROS_V					    =	urldecode($sv['PF_OTROS']);					    } else { }
				if (isset($sv['TOTAL_PENDIENTE_COBRO']))		{	$TOTAL_PENDIENTE_COBRO_V	    =	urldecode($sv['TOTAL_PENDIENTE_COBRO']);	    } else { }
				if (isset($sv['TOTAL_PENDIENTE_DE_PAGO']))		{	$TOTAL_PENDIENTE_DE_PAGO_V	    =	urldecode($sv['TOTAL_PENDIENTE_DE_PAGO']);	    } else { }
				if (isset($sv['OBSERVACIONES_GENERALES']))		{	$OBSERVACIONES_GENERALES_V	    =	urldecode($sv['OBSERVACIONES_GENERALES']);	    } else { }
				if (isset($sv['MOTIVOS']))		                {   $MOTIVOS_V                      =	urldecode($sv['MOTIVOS']);                      } else { }
				if (isset($sv['PAGO_ANTICIPADO_REALIZADO']))    {   $PAGO_ANTICIPADO_REALIZADO_V    =   urldecode($sv['PAGO_ANTICIPADO_REALIZADO']);    } else { }
			}
			
			//echo '<pre>'.print_r($sv,true).'</pre>';
			
			
			
			$replaces=array(
					'{OBRA}'						=>		$ID_OBRA.' - '.$data['0']['NOMBRE_OBRA'],
					'{JEFE_DE_OBRA}'				=>		$ID_JEFE_OBRA.' - '.$data['0']['NOMBRE_JEFE_OBRA'],
					'{MES}'							=>		$meses[$MES-1].' - '.$YEAR,
					'{DIA_DE_CIERRE_V}'				=>		$DIA_DE_CIERRE_V,
					'{PF_CIERRE_V}'					=>		$PF_CIERRE_V,
					'{PF_FALTA_CONTRATO_V}'			=>		$PF_FALTA_CONTRATO_V,
					'{PF_APROBACION_V}'				=>		$PF_APROBACION_V,
					'{PF_OTROS_V}'					=>		$PF_OTROS_V,
					'{TOTAL_PENDIENTE_COBRO_V}'		=>		$TOTAL_PENDIENTE_COBRO_V,
					'{TOTAL_PENDIENTE_DE_PAGO_V}'	=>		$TOTAL_PENDIENTE_DE_PAGO_V,
					'{PAGO_ANTICIPADO_REALIZADO_V}' =>      $PAGO_ANTICIPADO_REALIZADO_V,
					'{OBSERVACIONES_GENERALES_V}'	=>		$OBSERVACIONES_GENERALES_V,
					'{MOTIVOS_V}'                   =>      $MOTIVOS_V
					
					);
			
			$content=str_replace(array_keys($replaces),$replaces,$content);
		}		

	} elseif ($error==1) {
		return '<body><div style="margin-top:20%; color:red; font-size:30px; text-align:center;">Informe no encontrado</div></body>';
	} elseif ($error==2) {
		return '<body><div style="margin-top:20%; color:red; font-size:30px; text-align:center;">Informe ya procesado</div></body>';
	} elseif ($error==3) {
		return '<body><div style="margin-top:20%; color:red; font-size:30px; text-align:center;">Informe duplicado</div></body>';
	} else {
		echo 'Error desconocido!'; die;		
	}

	return $content;
}

function render_a_files ($content, $processed_data='') {
	
	$id1=cG('id1');
	$id2=cG('id2');
	
	$hstyle='';
	if ($processed_data=='')	{		
		$data=get_db_data(array('obtener-i3-por-id', $id1, $id2));
		
		if (is_array($data)) {
			if (isset($data['0']['DATA'])) {
				$sv=unserialize($data['0']['DATA']);
			}
		}
	} else {
		$sv=$processed_data;
		
		if (!isset($GLOBALS['ADMIN_MODIFY_I3'])) {
			$hstyle=' readonly ';
		}		
	}

	$trs='';
	$mnlA=0;
	for ($i = 1; $i <= MAX_LINES; $i++) {
		$style=''; $js=''; $hide='<td></td>';
		
		if (isset($sv['A'][$i]['P_CERTIFICACION_'.$i.'']) or isset($sv['A'][$i]['IMPORTE_P_CERTIFICACION_'.$i.'']) or isset($sv['A'][$i]['MOTIVO_P_CERTIFICACION_'.$i.'']))	{
			$style =' ';
			if (isset($sv['A'][$i]['P_CERTIFICACION_'.$i.'']))			{$value1=$sv['A'][$i]['P_CERTIFICACION_'.$i.''];			} else {$value1='';}
			if (isset($sv['A'][$i]['IMPORTE_P_CERTIFICACION_'.$i.'']))	{$value2=$sv['A'][$i]['IMPORTE_P_CERTIFICACION_'.$i.''];	} else {$value2='';}
			if (isset($sv['A'][$i]['MOTIVO_P_CERTIFICACION_'.$i.'']))	{$value3=$sv['A'][$i]['MOTIVO_P_CERTIFICACION_'.$i.''];		} else {$value3='';}
			
			$value1=urldecode($value1); $value2=urldecode($value2); $value3=urldecode($value3);
			
			//echo ' ok ';
		} else {
			//echo '<pre>'.print_r($sv['A'][$i],true).'</pre>';
			
			if ($i>1 and $mnlA>0) {
				$style=' style="display:none;" '; 
			}
			$mnlA++;
			$value1='';
			$value2='';
			$value3='';
		}
		
		if ($i>1 and $processed_data=='') {			
			$clear="
            document.getElementById('P_CERTIFICACION_".$i."').value=''; 
			document.getElementById('IMPORTE_P_CERTIFICACION_".$i."').value=''; 
			document.getElementById('MOTIVO_P_CERTIFICACION_".$i."').value='';
			sum1();
			";
			$hide='<td style="max-width:40px; text-align:center; vertical-align:middle;"><div style="font-weight:bold; color:red; cursor:pointer;" class="btn btn-default btn-xs" onclick="jQuery(\'#row_a'.($i).'\').hide(); '.$clear.'">X</div></td></tr>';
		}	
		
		if ($i!=MAX_LINES){$js=' onchange="jQuery(\'#row_a'.($i+1).'\').show(); sum1();" ';}		
		
		//$value1=urldecode($value1); $value2=urldecode($value2); $value3=urldecode($value3);
		
		$trs.='<tr '.$style.' id="row_a'.$i.'"><td><input '.$hstyle.' value="'.$value1.'" '.$js.' id="P_CERTIFICACION_'.$i.'" name="P_CERTIFICACION_'.$i.'" class="form-control" /></td><td><input '.$hstyle.' value="'.$value2.'" '.$js.' id="IMPORTE_P_CERTIFICACION_'.$i.'" name="IMPORTE_P_CERTIFICACION_'.$i.'" class="form-control numero" /></td><td><input '.$hstyle.' value="'.$value3.'" '.$js.' id="MOTIVO_P_CERTIFICACION_'.$i.'" name="MOTIVO_P_CERTIFICACION_'.$i.'" class="form-control" /></td>'.$hide.'</tr>';
	}
	
	$content=str_replace('{TRA}',$trs,$content);
	
	return $content;
}

function render_b_files ($content, $processed_data='') {
	$id1=cG('id1');
	$id2=cG('id2');
	
	$hstyle='';
	if($processed_data=='') {
		$data=get_db_data(array('obtener-i3-por-id', $id1, $id2));
		
		if (is_array($data)) {
			if (isset($data['0']['DATA'])) {
				$sv=unserialize($data['0']['DATA']);
			}
		}
	} else {
		$sv=$processed_data;
		
		if (!isset($GLOBALS['ADMIN_MODIFY_I3'])) {
			$hstyle=' readonly ';
		}
	}
	
	$trs='';
	$mnlB=0;
	for ($i = 1; $i <= MAX_LINES; $i++) {
		$style=''; $js=''; $hide='<td></td>';
		
		if (isset($sv['B'][$i]['ACOPIO_ENTREGA_'.$i.'']) or isset($sv['B'][$i]['IMPORTE_ACOPIO_ENTREGA_'.$i.'']) or isset($sv['B'][$i]['MOTIVO_ACOPIO_ENTREGA_'.$i.''])) {
			$style=' ';
			if (isset($sv['B'][$i]['ACOPIO_ENTREGA_'.$i.'']))			{$value1=$sv['B'][$i]['ACOPIO_ENTREGA_'.$i.''];			} else {$value1='';}
			if (isset($sv['B'][$i]['IMPORTE_ACOPIO_ENTREGA_'.$i.'']))	{$value2=$sv['B'][$i]['IMPORTE_ACOPIO_ENTREGA_'.$i.''];	} else {$value2='';}
			if (isset($sv['B'][$i]['MOTIVO_ACOPIO_ENTREGA_'.$i.'']))	{$value3=$sv['B'][$i]['MOTIVO_ACOPIO_ENTREGA_'.$i.''];	} else {$value3='';}
			
			$value1=urldecode($value1); $value2=urldecode($value2); $value3=urldecode($value3);
			
			//echo ' ok ';
		} else {
			//echo '<pre>'.print_r($sv['B'][$i],true).'</pre>';
			
			if ($i>1 and $mnlB>0) {
				$style=' style="display:none;" '; 
			}
			$mnlB++;
			$value1='';
			$value2='';
			$value3='';
		}
		
		if ($i>1 and $processed_data=='') {
			//$style=' style="display:none;" ';
			
			//document.getElementById('ACOPIO_ENTREGA_".$i."').id='ACOPIO_ENTREGA_R".$i."'; cambiamos la id opcionalmete para que esa linea en blanco no vuelva a aparecer
			$clear="
			document.getElementById('ACOPIO_ENTREGA_".$i."').value='';
			document.getElementById('IMPORTE_ACOPIO_ENTREGA_".$i."').value='';
			document.getElementById('MOTIVO_ACOPIO_ENTREGA_".$i."').value='';
			sum2();
			"; 
			$hide='<td style="max-width:40px; text-align:center; vertical-align:middle;"><div style="font-weight:bold; color:red; cursor:pointer;" class="btn btn-default btn-xs" onclick="jQuery(\'#row_b'.($i).'\').hide(); '.$clear.' return false;">X</div></td></tr>';
		}	
		
		if ($i!=MAX_LINES){$js=' onchange="jQuery(\'#row_b'.($i+1).'\').show(); sum2();" ';}	
		
		//$value1=urldecode($value1); $value2=urldecode($value2); $value3=urldecode($value3);
		
		$trs.='<tr '.$style.' id="row_b'.$i.'"><td><input '.$hstyle.' value="'.$value1.'" '.$js.' id="ACOPIO_ENTREGA_'.$i.'" name="ACOPIO_ENTREGA_'.$i.'" class="form-control" /></td><td><input '.$hstyle.' value="'.$value2.'" '.$js.' id="IMPORTE_ACOPIO_ENTREGA_'.$i.'" name="IMPORTE_ACOPIO_ENTREGA_'.$i.'" class="form-control numero" /></td><td><input '.$hstyle.' value="'.$value3.'" '.$js.' id="MOTIVO_ACOPIO_ENTREGA_'.$i.'" name="MOTIVO_ACOPIO_ENTREGA_'.$i.'" class="form-control" /></td>'.$hide.'</tr>';
	}
	
	$content=str_replace('{TRB}',$trs,$content);
	
	return $content;
}

function make_i3_array ($id_inf,$regenerar=false) {
	if ($regenerar) {
		$data	=	get_db_data(array('leer-informe-por-id', $id_inf));
		$data2	=	get_db_data(array('obtener-i3-por-id-2', $id_inf));
	} else {	
		$id1=cG('id1');
		$id2=cG('id2');
		
		$data	=	get_db_data(array('leer-informe-por-id', $id_inf));
		$data2	=	get_db_data(array('obtener-i3-por-id', $id1, $id2));
	}
	
	//echo count($data);
	
	$sa['ID_INFORME']					=	$data['0']['ID_INFORME'];
	$sa['ID_JEFE_OBRA']					=	$data['0']['ID_JEFE_OBRA'];
	$sa['ID_OBRA']						=	$data['0']['ID_OBRA'];
	$sa['MES']							=	$data['0']['MES'];
	$sa['YEAR']							=	$data['0']['YEAR'];	
	$sa['DIA_DE_CIERRE']				=	$data['0']['DIA_DE_CIERRE'];
	$sa['PF_CIERRE']					=	$data['0']['PF_CIERRE'];
	$sa['PF_FALTA_CONTRATO']			=	$data['0']['PF_FALTA_CONTRATO'];
	$sa['PF_APROBACION']				=	$data['0']['PF_APROBACION'];
	$sa['PF_OTROS']						=	$data['0']['PF_OTROS'];
	$sa['MOTIVOS']						=	$data['0']['MOTIVOS'];
	
	//$sa['TOTAL_PENDIENTE_COBRO']		=	$data['0']['TOTAL_PENDIENTE_COBRO'];
	$sa['TOTAL_PENDIENTE_COBRO']=fix_sum(array($sa['PF_CIERRE'],$sa['PF_FALTA_CONTRATO'],$sa['PF_APROBACION'],$sa['PF_OTROS']));
	

	//$sa['TOTAL_PENDIENTE_DE_PAGO']	=	$data['0']['TOTAL_PENDIENTE_DE_PAGO'];
	//$sa['PAGO_ANTICIPADO_REALIZADO']	=	$data['0']['PAGO_ANTICIPADO_REALIZADO'];
	$sa['OBSERVACIONES_GENERALES']		=	$data['0']['OBSERVACIONES_GENERALES'];
	
		
	
	$sa['ID_OBRA']						=	$data2['0']['ID_OBRA'];
	$sa['ID_JEFE_OBRA']					=	$data2['0']['ID_JEFE_OBRA'];
	$sa['NOMBRE_OBRA']					=	$data2['0']['NOMBRE_OBRA'];
	$sa['NOMBRE_JEFE_OBRA']				=	$data2['0']['NOMBRE_JEFE_OBRA'];
	
	$ia=1;
	$ib=1;
	if (is_array($data)) {
		$val_sum_a='';
		$val_sum_b='';
		
		foreach ($data as $sv) {			
			
			//echo '<pre>'.print_r($sv,true).'</pre>';			
			
			if (isset($sv['P_CERTIFICACION']) or isset($sv['IMPORTE_P_CERTIFICACION']) or isset($sv['MOTIVO_P_CERTIFICACION']))	{
				if (isset($sv['P_CERTIFICACION']))			{$sa['A'][$ia]['P_CERTIFICACION_'.$ia.'']           =   $sv['P_CERTIFICACION'];			    }
				
				if (isset($sv['MOTIVO_P_CERTIFICACION']))	{$sa['A'][$ia]['MOTIVO_P_CERTIFICACION_'.$ia.'']    =   $sv['MOTIVO_P_CERTIFICACION'];		}
				
				if (isset($sv['IMPORTE_P_CERTIFICACION'])) {
					$sa['A'][$ia]['IMPORTE_P_CERTIFICACION_'.$ia.'']   =   $sv['IMPORTE_P_CERTIFICACION'];
					$val_sum_a[]=$sv['IMPORTE_P_CERTIFICACION'];   
				}
								
				$ia++;
			}
						
			
			if (isset($sv['ACOPIO_ENTREGA']) or isset($sv['IMPORTE_ACOPIO_ENTREGA']) or isset($sv['MOTIVO_ACOPIO_ENTREGA'])) {
				if (isset($sv['ACOPIO_ENTREGA']))			{$sa['B'][$ib]['ACOPIO_ENTREGA_'.$ib.'']             =   $sv['ACOPIO_ENTREGA'];			}				
				if (isset($sv['MOTIVO_ACOPIO_ENTREGA']))	{$sa['B'][$ib]['MOTIVO_ACOPIO_ENTREGA_'.$ib.'']      =   $sv['MOTIVO_ACOPIO_ENTREGA'];	}
					
				if (isset($sv['IMPORTE_ACOPIO_ENTREGA'])) {
					$sa['B'][$ib]['IMPORTE_ACOPIO_ENTREGA_'.$ib.''] = $sv['IMPORTE_ACOPIO_ENTREGA'];
					$val_sum_b[]=$sv['IMPORTE_ACOPIO_ENTREGA'];
				}								
				
				$ib++;
			}
		}
		
		if (is_array($val_sum_a)){ $sa['TOTAL_PENDIENTE_DE_PAGO']=fix_sum($val_sum_a); } else { $sa['TOTAL_PENDIENTE_DE_PAGO']=0; }
		if (is_array($val_sum_b)){ $sa['PAGO_ANTICIPADO_REALIZADO']=fix_sum($val_sum_b); } else { $sa['PAGO_ANTICIPADO_REALIZADO']=0; }
		
	}
	
	//return $sa;
	return array($sa);
	//echo '<pre>'.print_r($sa,true).'</pre>';
}

function generar_informes () {
	//proceso detenido manualmente
	//echo '<pre>Proceso detenido manualmente</pre>';
	//return;
	//die;
	
	$current_year	=	date('Y');
	$current_month	=	date('n');	
	$current_day	=	date('d');
	
	$date_period = strtotime($current_year.'-'.$current_month.'-15 -1 month');
	
	$period_year	=	date('Y',$date_period);
	$period_month	=	date('n',$date_period);
	$period_day		=	date('d',$date_period);
	
	// COMPROBAMOS SI YA ESTAN GENERADOS
	
	$gi=get_db_data(array('listar-i3-por-fecha',$period_month,$period_year));
	
	//echo '<pre>-'.print_r($gi,true).'-</pre>'."\n";
	
	//echo '<pre>'.print_r(is_array($gi),true).'</pre>';

	if (!is_array($gi)) {
		if ($current_day<12) {
			echo '<pre>No es posible generar el informe hasta el dia 12</pre>';
		} else {
			$di=get_db_data(array('obras-jefe-de-obra',$current_month,$current_year));
			
			$n=0;
			foreach ($di as $d)	{
				if ($d['ACTIVO']=='1') {
					$ID_INFORME					=		generate_random_hash();
					$ID_JEFE_OBRA				=		$d['ID_JEFE_OBRA'];
					$ID_OBRA					=		$d['CODIGOPROYECTO'];
					$MES						=		$period_month;
					$YEAR						=		$period_year;
					$FECHA_CEACION_REGISTRO		=		'GETDATE()';
					$NOMBRE_JEFE_OBRA			=		$d['NOMBRE'];
					$NOMBRE_OBRA				=		$d['PROYECTO'];
					$EMAIL						=		$d['EMAIL'];
					
					$default_email='jesus@chirivo.com';
					
					if ($EMAIL==''){$EMAIL=$default_email;}
					if ($EMAIL==$default_email and $ID_JEFE_OBRA=='' ){$ID_JEFE_OBRA='91'; $NOMBRE_JEFE_OBRA='Jefe de obra por defecto'; }
					
					//$data="'".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."',".$FECHA_CEACION_REGISTRO.",'".$NOMBRE_JEFE_OBRA."','".$NOMBRE_OBRA."','".$EMAIL."'";
					//$r=store_db_data(array('generar-maestro-i3',$data));
					
					$DATOS_POR_DEFECTO=generar_datos_por_defecto_i3($ID_OBRA);
					
					if ($DATOS_POR_DEFECTO!='')	{
						$data="'".$DATOS_POR_DEFECTO."','".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."',".$FECHA_CEACION_REGISTRO.",'".$NOMBRE_JEFE_OBRA."','".$NOMBRE_OBRA."','".$EMAIL."'";
						
						//echo '<pre>'.get_db_query(array('generar-maestro-i3-regenerado',$data)).'</pre>';
						
						$r=store_db_data(array('generar-maestro-i3-regenerado',$data));						
					} else {
						$data="'".$ID_INFORME."','".$ID_JEFE_OBRA."','".$ID_OBRA."','".$MES."','".$YEAR."',".$FECHA_CEACION_REGISTRO.",'".$NOMBRE_JEFE_OBRA."','".$NOMBRE_OBRA."','".$EMAIL."'";
						//$r=store_db_data(array('generar-maestro-i3',$data));
					}
					
					//echo '<pre>'.$data.'</pre>';
					
					$n++;
				}
				//break;
			}
			
			return 'Generados '.$n.' informes'."\n";
			//echo '<pre>'.print_r($di,true).'</pre>';
		}
	} else {
		return 'Informe ya generados.'."\n";	
	}	
}

function ver_formulario_procesado ($content) {
	
	$content=file_get_contents_utf8(ABSPATH.'/template/informe-3-cerrado.html');
	
	$id1=cG('id1');
	$id2=cG('id2');
	$id3=cG('admin');
	
	$data=make_i3_array($id1);
	$sv=$data['0'];
	
	//echo '<pre>'.print_r($data['0'],true).'</pre>';
	
	$content=render_a_files($content,$data['0']);
	$content=render_b_files($content,$data['0']);	
	
	
	if (isset($sv['DIA_DE_CIERRE']))				{	$DIA_DE_CIERRE_V			    =	date_normalizer($sv['DIA_DE_CIERRE']);		} else { $DIA_DE_CIERRE_V='';				}
	if (isset($sv['PF_CIERRE']))					{	$PF_CIERRE_V				    =	$sv['PF_CIERRE'];							} else { $PF_CIERRE_V='';					}
	if (isset($sv['PF_FALTA_CONTRATO']))			{	$PF_FALTA_CONTRATO_V		    =	$sv['PF_FALTA_CONTRATO'];					} else { $PF_FALTA_CONTRATO_V='';			}
	if (isset($sv['PF_APROBACION']))				{	$PF_APROBACION_V			    =	$sv['PF_APROBACION'];						} else { $PF_APROBACION_V='';				}
	if (isset($sv['PF_OTROS']))						{	$PF_OTROS_V					    =	$sv['PF_OTROS'];							} else { $PF_OTROS_V='';					}
	
	if (isset($sv['TOTAL_PENDIENTE_COBRO']))		{	$TOTAL_PENDIENTE_COBRO_V	    =	$sv['TOTAL_PENDIENTE_COBRO'];				} else { $TOTAL_PENDIENTE_COBRO_V='';		}
		
	if (isset($sv['TOTAL_PENDIENTE_DE_PAGO']))		{	$TOTAL_PENDIENTE_DE_PAGO_V	    =	$sv['TOTAL_PENDIENTE_DE_PAGO'];				} else { $TOTAL_PENDIENTE_DE_PAGO_V='';		}
	if (isset($sv['OBSERVACIONES_GENERALES']))		{	$OBSERVACIONES_GENERALES_V	    =	$sv['OBSERVACIONES_GENERALES'];				} else { $OBSERVACIONES_GENERALES_V='';		}
	if (isset($sv['MOTIVOS']))		                {   $MOTIVOS_V                      =	$sv['MOTIVOS'];								} else { $MOTIVOS_V='';						}
	if (isset($sv['PAGO_ANTICIPADO_REALIZADO']))    {   $PAGO_ANTICIPADO_REALIZADO_V    =   $sv['PAGO_ANTICIPADO_REALIZADO'];			} else { $PAGO_ANTICIPADO_REALIZADO_V='';	}
	
	
	if (isset($sv['ID_OBRA']))						{   $ID_OBRA						=   $sv['ID_OBRA'];								} else { $ID_OBRA='';						}
	if (isset($sv['ID_JEFE_OBRA']))					{   $ID_JEFE_OBRA					=   $sv['ID_JEFE_OBRA'];						} else { $ID_JEFE_OBRA='';					}
	
	if (isset($sv['MES']))							{   $MES							=   $sv['MES'];									} else { $MES='';							}
	if (isset($sv['YEAR']))							{   $YEAR							=   $sv['YEAR'];								} else { $YEAR='';							}
	
	$meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	
	$replaces=array(
			'{OBRA}'						=>		$ID_OBRA.' - '.$sv['NOMBRE_OBRA'],
			'{JEFE_DE_OBRA}'				=>		$ID_JEFE_OBRA.' - '.$sv['NOMBRE_JEFE_OBRA'],
			'{MES}'							=>		$meses[$MES-1].' - '.$YEAR,
			'{DIA_DE_CIERRE_V}'				=>		$DIA_DE_CIERRE_V,
			'{PF_CIERRE_V}'					=>		$PF_CIERRE_V,
			'{PF_FALTA_CONTRATO_V}'			=>		$PF_FALTA_CONTRATO_V,
			'{PF_APROBACION_V}'				=>		$PF_APROBACION_V,
			'{PF_OTROS_V}'					=>		$PF_OTROS_V,
			'{TOTAL_PENDIENTE_COBRO_V}'		=>		$TOTAL_PENDIENTE_COBRO_V,
			'{TOTAL_PENDIENTE_DE_PAGO_V}'	=>		$TOTAL_PENDIENTE_DE_PAGO_V,
			'{PAGO_ANTICIPADO_REALIZADO_V}' =>      $PAGO_ANTICIPADO_REALIZADO_V,
			'{OBSERVACIONES_GENERALES_V}'	=>		$OBSERVACIONES_GENERALES_V,
			'{MOTIVOS_V}'                   =>      $MOTIVOS_V
			
			);
	
	$content=str_replace(array_keys($replaces),$replaces,$content);
	
	return $content;
}

function generar_datos_por_defecto_i3 ($id_obra) {
	$current_year			=	date('Y');
	$current_month			=	date('n');	
	$current_day			=	date('d');
	
	$date_period			=	strtotime($current_year.'-'.$current_month.'-15 -2 month');
	
	$pre_period_year		=	date('Y',$date_period);
	$pre_period_month		=	date('n',$date_period);
	$pre_period_day			=	date('d',$date_period);
	
	$id_inf				=	get_db_data(array('obtener_info_informe_por_id_obra_y_periodo_i3',$id_obra,$pre_period_month,$pre_period_year));
	//$id_inf_d				=	get_db_data(array('obtener_id_informe_por_id_obra_y_periodo_i3_detalle',$id_obra,$pre_period_month,$pre_period_year));
	
	//echo '<pre>ID OLD: '.print_r($id_inf,true).'</pre>';
	
	$id=$id_inf['0']['ID_INFORME'];	
	
	$arr1=make_i3_array($id,true);
	
	$sa=$arr1['0'];
	
	
	$sa['ID_INFORME']					=	urlencode($sa['ID_INFORME']);
	$sa['ID_JEFE_OBRA']					=	urlencode($sa['ID_JEFE_OBRA']);
	$sa['ID_OBRA']						=	urlencode($sa['ID_OBRA']);
	$sa['NOMBRE_OBRA']					=	urlencode($sa['NOMBRE_OBRA']);
	$sa['MES']							=	urlencode($sa['MES']);
	$sa['DIA_DE_CIERRE']				=	'';
	$sa['PF_CIERRE']					=	urlencode($sa['PF_CIERRE']);
	$sa['PF_FALTA_CONTRATO']			=	urlencode($sa['PF_FALTA_CONTRATO']);
	$sa['PF_APROBACION']				=	urlencode($sa['PF_APROBACION']);
	$sa['PF_OTROS']						=	urlencode($sa['PF_OTROS']);
	$sa['MOTIVOS']						=	urlencode($sa['MOTIVOS']);
	$sa['TOTAL_PENDIENTE_COBRO']		=	urlencode($sa['TOTAL_PENDIENTE_COBRO']);
	$sa['TOTAL_PENDIENTE_DE_PAGO']		=	urlencode($sa['TOTAL_PENDIENTE_DE_PAGO']);
	$sa['PAGO_ANTICIPADO_REALIZADO']	=	urlencode($sa['PAGO_ANTICIPADO_REALIZADO']);
	$sa['OBSERVACIONES_GENERALES']		=	urlencode($sa['OBSERVACIONES_GENERALES']);
	
	
	//echo '<pre>'.print_r($sa).'</pre>';
	
	//die;
	
	if (isset($sa['A'])) {
		if (is_array($sa['A']))	{
			$AR=array_keys($sa['A']);
			
			foreach($AR as $av)	{
				if (is_array($sa['A'][$av])) {
					$AR2=array_keys($sa['A'][$av]);
					
					foreach($AR2 as $av2) {
						//echo 'Busco-> A['.$av.']['.$av2.']';
						if ($sa['A'][$av][$av2]!='') {
							//echo '<pre>A-> '.$av.'</pre>';
							$sa['A'][$av][$av2]=urlencode($sa['A'][$av][$av2]);
						}
					}					
				}
				
			}
		}
	}
	if (isset($sa['B'])) {
		if (is_array($sa['B']))	{
			$BR=array_keys($sa['B']);
			
			foreach($BR as $bv)	{
				if (is_array($sa['B'][$bv])) {
					$BR2=array_keys($sa['B'][$bv]);
					
					foreach($BR2 as $bv2) {
						//echo 'Busco-> B['.$av.']['.$av2.']';
						if ($sa['B'][$bv][$bv2]!='') {
							//echo '<pre>B-> '.$bv.'</pre>';
							$sa['B'][$bv][$bv2]=urlencode($sa['B'][$bv][$bv2]);
						}
					}					
				}
				
			}
		}
	}
	
	//echo '<pre>'.print_r($sa).'</pre>';
	
	return serialize($sa);
	
	//if (isset($AE)){$sa['A']=$AE;}			
	//if (isset($BE)){$sa['B']=$BE;}
	
	//echo '<pre>Datos para almacenar: '.print_r($sa,true).'</pre>';
	
	//update_db_data(array('guardar-i3',$ID_INFORME,serialize($sa)));
}

function editar_informe_procesado ($content) {
	$content=file_get_contents_utf8(ABSPATH.'/template/informe-3-modificar.html');
	
	$aditonal_js='';	
	
	$aditonal_js.='<script>'."\n".i3_sum_script()."\n".'</script>'."\n";
	
	$content=str_replace('{I3_SCRIPT}',$aditonal_js,$content);
	
	$id1=cG('id1');
	$id2=cG('id2');
	$id3=cG('admin');
	
	$data=make_i3_array($id1);
	$sv=$data['0'];
	
	//echo '<pre>'.print_r($data['0'],true).'</pre>';
	
	$content=render_a_files($content,$data['0']);
	$content=render_b_files($content,$data['0']);	
	
	
	if (isset($sv['DIA_DE_CIERRE']))				{	$DIA_DE_CIERRE_V			    =	date_normalizer($sv['DIA_DE_CIERRE']);		} else { $DIA_DE_CIERRE_V='';				}
	if (isset($sv['PF_CIERRE']))					{	$PF_CIERRE_V				    =	$sv['PF_CIERRE'];							} else { $PF_CIERRE_V='';					}
	if (isset($sv['PF_FALTA_CONTRATO']))			{	$PF_FALTA_CONTRATO_V		    =	$sv['PF_FALTA_CONTRATO'];					} else { $PF_FALTA_CONTRATO_V='';			}
	if (isset($sv['PF_APROBACION']))				{	$PF_APROBACION_V			    =	$sv['PF_APROBACION'];						} else { $PF_APROBACION_V='';				}
	if (isset($sv['PF_OTROS']))						{	$PF_OTROS_V					    =	$sv['PF_OTROS'];							} else { $PF_OTROS_V='';					}
	if (isset($sv['TOTAL_PENDIENTE_COBRO']))		{	$TOTAL_PENDIENTE_COBRO_V	    =	$sv['TOTAL_PENDIENTE_COBRO'];				} else { $TOTAL_PENDIENTE_COBRO_V='';		}
	if (isset($sv['TOTAL_PENDIENTE_DE_PAGO']))		{	$TOTAL_PENDIENTE_DE_PAGO_V	    =	$sv['TOTAL_PENDIENTE_DE_PAGO'];				} else { $TOTAL_PENDIENTE_DE_PAGO_V='';		}
	if (isset($sv['OBSERVACIONES_GENERALES']))		{	$OBSERVACIONES_GENERALES_V	    =	$sv['OBSERVACIONES_GENERALES'];				} else { $OBSERVACIONES_GENERALES_V='';		}
	if (isset($sv['MOTIVOS']))		                {   $MOTIVOS_V                      =	$sv['MOTIVOS'];								} else { $MOTIVOS_V='';						}
	if (isset($sv['PAGO_ANTICIPADO_REALIZADO']))    {   $PAGO_ANTICIPADO_REALIZADO_V    =   $sv['PAGO_ANTICIPADO_REALIZADO'];			} else { $PAGO_ANTICIPADO_REALIZADO_V='';	}
	
	
	if (isset($sv['ID_OBRA']))						{   $ID_OBRA						=   $sv['ID_OBRA'];								} else { $ID_OBRA='';						}
	if (isset($sv['ID_JEFE_OBRA']))					{   $ID_JEFE_OBRA					=   $sv['ID_JEFE_OBRA'];						} else { $ID_JEFE_OBRA='';					}
	
	if (isset($sv['MES']))							{   $MES							=   $sv['MES'];									} else { $MES='';							}
	if (isset($sv['YEAR']))							{   $YEAR							=   $sv['YEAR'];								} else { $YEAR='';							}
	
	$meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	
	$replaces=array(
			'{OBRA}'						=>		$ID_OBRA.' - '.$sv['NOMBRE_OBRA'],
			'{JEFE_DE_OBRA}'				=>		$ID_JEFE_OBRA.' - '.$sv['NOMBRE_JEFE_OBRA'],
			'{MES}'							=>		$meses[$MES-1].' - '.$YEAR,
			'{DIA_DE_CIERRE_V}'				=>		$DIA_DE_CIERRE_V,
			'{PF_CIERRE_V}'					=>		$PF_CIERRE_V,
			'{PF_FALTA_CONTRATO_V}'			=>		$PF_FALTA_CONTRATO_V,
			'{PF_APROBACION_V}'				=>		$PF_APROBACION_V,
			'{PF_OTROS_V}'					=>		$PF_OTROS_V,
			'{TOTAL_PENDIENTE_COBRO_V}'		=>		$TOTAL_PENDIENTE_COBRO_V,
			'{TOTAL_PENDIENTE_DE_PAGO_V}'	=>		$TOTAL_PENDIENTE_DE_PAGO_V,
			'{PAGO_ANTICIPADO_REALIZADO_V}' =>      $PAGO_ANTICIPADO_REALIZADO_V,
			'{OBSERVACIONES_GENERALES_V}'	=>		$OBSERVACIONES_GENERALES_V,
			'{MOTIVOS_V}'                   =>      $MOTIVOS_V
			
			);
	
	$content=str_replace(array_keys($replaces),$replaces,$content);
	
	return $content;
}
?>