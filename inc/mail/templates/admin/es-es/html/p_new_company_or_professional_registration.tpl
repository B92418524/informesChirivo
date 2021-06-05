<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Nuevo mensaje: nuevo registro de empresa o profesional</title>
</head>
	
<body>
    <table style="width: 100%; border: 0; padding: 0; border-collapse: collapse; text-align: center; background-color: #f0f0f0;">
        <tbody>
            <tr>
                <td style="width: 100%; vertical-align: top; text-align: center;">
					<table align="center" style="width: 670px; margin: 40px auto 10px auto;">
						<tr>
							<td style="text-align:left; width:100%">
								<img alt="logo" style="width: 250px;" src="{LOGO_URL}" />
							</td>
						</tr>
					</table>
                    <table align="center" style="max-width: 670px; margin: 10px auto 50px auto; background-color: #ffffff;">
                        <tbody>
                            <tr>
                                <td style="padding: 20px; text-align: left;">

									<h3 style="margin: 10px auto 40px auto; font-size:18px; font-weight:bold; font-size:18px;">Nuevo mensaje: nuevo registro de empresa o profesional</h3>
                                    <h4 style="font-size:16px;">Datos del perfil</h4>                                    
                                    <table style="font-size:14px;">
                                        <tbody>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Nombre:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{PROFILE_TITLE}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Email:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{COMPANY_EMAIL}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Descripción:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{COMAPANY_DESCRIPTION}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Servicios:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{COMPANY_SERVICES_DESCRIPTION}</td>
                                            </tr>	
											<tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Codigo Promocional:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{COUPON}</td>
                                            </tr>										
                                        </tbody>
                                    </table>



                                    <a style="margin:50px auto 10px auto; max-width:200px; display:block; padding:10px 20px 10px 20px; background:rgb(28,184,65); text-decoration:none; color:#ffffff; font-weight:bold; font-family:sans-serif; font-size:18px; text-align:center; border-radius:6px" href="{VALIDATION_LINK}">Validar</a>





                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table align="center" style="width: 670px; margin: 10px auto 50px auto;">
                        <tbody>
                            <tr>
                                <td style="font-size: 12px;">Has recibido este correo electrónico porque es necesario validar este perfil.
                                    <br />
                                    Accede al panel de usuario pulsando aqui: <a href="{CONTROL_PANEL_URL}">{THIS_PAGE_NAME}</a>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>