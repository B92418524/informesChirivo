<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{SUBJECT}</title>
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

									<h3 style="margin: 10px auto 40px auto; font-size:18px; font-weight:bold; font-size:18px;">{SUBJECT}</h3>
                                    <p style="font-size:14px;">Estimado usuario: {COMPANY_NAME}</p>
									<br/>
									<p style="font-size:14px;">Le enviamos este email desde {SITE_NAME} porque un usuario que se ha identificado como {USER_NAME_FROM} ha cumplimentado el formulario de solicitud de información o presupuesto del perfil de su empresa.</p>
									<p style="font-size:14px;">El usuario ha indicado estos datos en el formulario de contacto:</p>
                                    <br/>
                                    <table style="font-size:14px;">
                                        <tbody>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Nombre:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{USER_NAME_FROM}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Ciudad:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{USER_CITY_FROM}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Email:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{USER_EMAIL_FROM}</td>
                                            </tr>
											<tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Teléfono:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{USER_PHONE_FROM}</td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px; border:solid 1px #eee; padding:10px;">Mensaje:</td>
                                                <td style="width:500px; border:solid 1px #eee; padding:10px;">{USER_MESSAGE_FROM}</td>
                                            </tr>
                                        </tbody>
                                    </table>
									<br/>

									<p style="font-size:14px;">* Debe tener en cuenta que no es posible garantizar que los datos de contacto indicados por el usuario sean correctos.</p>
									<p style="font-size:14px;">Puede ver el perfil de su empresa en: {PROFILE_URL}</p>
									<p style="font-size:14px;">Recuerde que puede modificar su perfil o solicitar la baja desde su panel de usuarios.</p>
									<p style="font-size:14px;">No dude en contactar con nosotros si necesita información adicional.</p>
									<br/>
									<p style="font-size:14px;">Atentamente,</p>
									<p style="font-size:14px;">El equipo de VSE Network</p>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table align="center" style="width: 670px; margin: 10px auto 50px auto;">
                        <tbody>
                            <tr>
                                <td style="font-size: 12px;">
									{AVL}
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