<?php

//$GLOBALS['DEBUG_MAIL']=true; --> definido en config

function send_i3_mail()
{
	// Obtener todos los informes pendientes
	$data=get_db_data(array('listar-i3-pendientes'));
	
	//echo '<pre> Pendientes'."\n".print_r($data,true).'</pre>';
	
	$envios=[];
	
	// Contamos dias y decidimos
	$envios_a_realizar=0;
	if (is_array($data))
	{
		foreach ($data as $d)
		{
			$startdate=date('Y/m/d');
			$enddate=object_to_date($d['FECHA_CEACION_REGISTRO'],$f='Y/m/d');
			
			$dif=count_days($startdate,$enddate);
			
			//echo '<pre>Diferencia de dias -> '.$dif.'</pre>';
			
			if ($dif>90)
			{
				// marcar como caducados
				//echo '<pre>Diferencia mayor de 30 dias -> '.$dif.'</pre>';
				update_db_data(array('actualizar-estado-i3',$d['ID_INFORME'],'CADUCADO'));
			}
			else
			{
				//agregamos al array de envios
				//echo '<pre>Diferencia menor de 30 dias -> '.$dif.'</pre>';
				array_push($envios, $d);
				$envios_a_realizar++;
			}		
		}
	}
	else
	{
		echo '<h1 style="font-size:30px; color:green; margin-bottom:30px; font-weight:bold;">No hay informes pendientes</h1>';
	}
	
	echo '<pre>Numero de registros a enviar: '.print_r($envios_a_realizar,true).'</pre>';
	
	// Hacemos los envios
	if (count($envios) > 0)
	{		
		
		$nm=1;
		foreach ($envios as $envio)
		{
			//bd_mail($from_name,$from_email,$to_name,$to_email,$subject,$body_html,$body_text,$smtp_user='',$smtp_pass='');
			//echo '<pre>Envios a rezliazar'.print_r($data,true).'</pre>';
			
			//$jo=get_db_data(array('jefes-de-obra-por-id',$envio['ID_JEFE_OBRA']));
			
			//if ($jo['0']['ACTIVO']=='1')
			//{
				//echo '<pre>'.print_r($jo,true).'</pre>';
				
				//$envio['NOMBRE']=$jo['0']['NOMBRE'];
				$envio['LINK']='http://46.25.213.2/informes/informe-3.php?id1='.$envio['ID_INFORME'].'&id2='.$envio['ID_JEFE_OBRA'];
				
				$from_name		=	'Informes Chirivo';
				$from_email		=	'informes@chirivo.com';
				$to_name		=	$envio['NOMBRE_JEFE_OBRA'];
				$to_email		=	$envio['EMAIL'];
				$subject		=	'I.M. '.$envio['NOMBRE_OBRA'];
				$body_html		=	get_mail_content_sent_html($envio);
				$body_text		=	get_mail_content_sent_txt_i3($envio);
				
			
			//if ($nm==1)
			//{
				if ($envio['ESTADO']=='')
				{
					//echo '<pre> '.$nm.' - '.$to_name.'</pre>';
					
					//echo '<pre>Se solicita el envio de '.$subject.' a '.$to_name.' - '.$to_email.'</pre>';
					
					//$body_text='Prueba que deberia enviarse a --> '.$to_email.$body_text;
					//$to_email='soporte@ticsur.com';
					
					update_db_data(array('actualizar-estado-i3-a-enviado',$envio['ID_INFORME'],'ENVIADO'));					
					bd_mail($from_name,$from_email,$to_name,$to_email,$subject,$body_html,$body_text,$smtp_user='',$smtp_pass='');
					
				}
			//}
			
			$nm++;
			//}
			//else
			//{
			//	echo 'No hay un jefe de obra activo para la obra '.$envio['ID_OBRA'];
			//}
			
					
		}
	}
	
	
	
	// Enviamos los no caducados
	
	
	
	return;
	
}

function bd_mail($from_name,$from_email,$to_name,$to_email,$subject,$body_html,$body_text,$smtp_user='',$smtp_pass='',$side_domain='')
{
	
	if ($smtp_user=='')		{$smtp_user=$from_email;}
	if ($smtp_pass=='')		{$smtp_pass='b2EcrU0a';} //GENERAL_MAIL_PASSWORD
	if ($side_domain=='')	{$side_domain='serviciodecorreo.es';}
	
	
	if ($GLOBALS['DEBUG_MAIL']==true)
	{
		$debug=2; // local
	}
	else
	{
		$debug=0; // modo debug en web operativa
	}
	
	
	if ($debug!=0)
	{
		
		//echo '<br/> Leida funcion de envio <br/>';
				
				
		
		echo 
		'
			<pre>
			$from_name ['.$from_name.']
			<br/>
			$from_email ['.$from_email.']
			<br/>
			$to_name ['.$to_name.']
			<br/>
			$to_email ['.$to_email.']
			<br/>
			$subject ['.$subject.']
			<br/>
			$body_html ['.$body_html.']
			<br/>
			$body_text ['.$body_text.']
			<br/>
			$smtp_user ['.$smtp_user.']
			<br/>
			$smtp_pass ['.$smtp_pass.']
			<br/>
	
			</pre>
	
	
			';	
		
	}
	
	// http://phpmailer.github.io/PHPMailer/index.html
	
	require_once('php_mailer/5.2.9/PHPMailerAutoload.php');
	
	
	
	$mail = new PHPMailer();																	//Create a new PHPMailer instance	
	
	$mail->CharSet										= "UTF-8";
	$mail->isSMTP();																			//Tell PHPMailer to use SMTP	
	$mail->SMTPDebug									= $debug;									//Enable SMTP debugging:	0 = off (for production use)	1 = client messages	2 = client and server messages	
	$mail->Debugoutput									= "html";								//Ask for HTML-friendly debug output	
	$mail->Mailer 										= "smtp";
	$mail->Host											= "smtp.".$side_domain;					//Set the hostname of the mail server
	//$mail->Host										= 'localhost';							//Set the hostname of the mail server	
	
	$mail->Port											= "465";								//Set the SMTP port number - likely to be 25, 465 or 587	
	$mail->SMTPAuth										= true;																		//Whether to use SMTP authentication
	$mail->SMTPSecure									= "ssl";
	
	$mail->Username										= $smtp_user;							//Username to use for SMTP authentication
	$mail->Password										= $smtp_pass;
	$mail->setFrom($from_email, $from_name);
	$mail->addReplyTo($from_email, $from_name);
	
	$mail->addAddress($to_email, $to_name);
	//$mail->addBCC("rafael@ticsur.com", "Debug Informes Chirivo");
		
	$mail->Subject										= $subject;		
	$mail->WordWrap										= 80;
	
	
	$mail->Body											= $body_text;
	
	//$mail->msgHTML($body, dirname(__FILE__), true);			//Create message bodies and embed images
	//$mail->msgHTML($body_html);
	//$mail->addAttachment(TEMPLATEPATH.'/images/ajax_big_1.gif','ajax_big_1.gif');  // optional name
	//$mail->addAttachment('images/phpmailer.png', 'phpmailer.png');  // optional name
	//$mail->AltBody = '"'.$body_text.'"';
	//$mail->AltBody = $body_text;
	
	$send_result=$mail->send();
	
	if (!$send_result) 
	{
		
		if ($debug!=0)
		{
			echo "<pre>Mailer Error: " . $mail->ErrorInfo.'</pre>';			
		}
		else
		{
			//echo "<pre>Mailer Error: " . $mail->ErrorInfo.'</pre>';
			//echo '<pre>'.print_r($mail,true).'</pre>';
			echo 'Error: no se ha podido enviar su mensaje';	
		}
	} 
	else 
	{
		echo "Su mensaje ha sido enviado correctamente\n";
	}
	/**/
	
}

function get_mail_content_sent_txt_i3($data)
{
	$out='';
	
	$template_content=file_get_contents_utf8(ABSPATH . '/template/informe-3_txt_mail.html');
	
	$replaces=array
		(
			'{OBRA}'				=>		$data['ID_OBRA'].' - '.$data['NOMBRE_OBRA'],
			'{JEFE_DE_OBRA}'		=>		$data['NOMBRE_JEFE_OBRA'],
			'{MES}'					=>		$data['MES'],
			'{YEAR}'				=>		$data['YEAR'],
			//'{DIA_DE_CIERRE}'		=>		date_normalizer($data['DIA_DE_CIERRE']),
			'{LINK}'				=>		$data['LINK'],				
			
		);
	
	$out=str_replace(array_keys($replaces),$replaces,$template_content);
	$out=str_replace(array_keys($replaces),$replaces,$out); // DOS PASADAS
	
	return $out;
	
}

function get_mail_content_sent_html($data)
{
	$data=get_mail_content_sent_txt_i3($data);	
	
	return $data;
}

