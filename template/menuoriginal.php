
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <form method="post" action="functions/render/ajax/cambiar-empresa.php">
                <select id="selEmpresaMenu" name="selEmpresa" style="position:relative;width:122px;top:8px" class="form-control" onchange="this.form.submit()">
                    <option value="1">1 - CHIRIVO CONSTRUCCIONES S.L.</option>
                    <option value="2">2 - CHIMAT, S.L. </option>
                    <option value="3">3 - JOSE MARTIN DOMINGUEZ </option>
                    <option value="4">4 - PROMOCIONES TORCAL, S.C.</option>
                    <option value="5">5 - PROSUR 2005, S.L. </option>
                    <option value="6">6 - CHIRIVO CONSTRUCCIONES</option>
                    <option value="7">7 - INVERSIONES MARTIN DOMINGUEZ S.L.</option>
                    <option value="8">8 - PATRIMONIO JEVA S.L.</option>
                    <option value="9">9 - MINESUR INGENIERIA S.L.</option>
                    <option value="10">10 - DERIVADOS DE HORMIGON LA DEHESA S.L</option>
                </select>
                <input type="hidden" name="accion" value="cambiarEmpresa" />
            </form>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Estado Obra <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">                        
                        <li><a href="informe-4.php" target="_blank">Rendimiento de obras</a></li>
                        <li><a href="informe-5.php" target="_blank">Detalle rendimiento de obra</a></li>
                        <li><a href="informe-4d1.php" target="_blank">Estado informes Jefes de Obra</a></li>
                        <li><a href="informe-11.php" target="_blank">Facturaci&oacute;n mensual</a></li>
                        <li class="divider"></li>
                        <li><a style="font-style:italic; color:#cc3300;" href="actualizar-1.php" target="_blank">Generar Informes</a></li>
                        <li><a style="font-style:italic; color:#cc3300;" href="actualizar-2.php" target="_blank">Enviar Informes</a></li>
                        <!--<li><a href="http://95.60.0.190/informes/informe-3.php?id1=i1&id2=jo1">Demo formulario Jefes de Obra</a></li>-->                     
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Tesorer&iacute;a <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="informe-14.php" target="_blank">Previsiones de pago</a></li>
                        <li><a href="informe-15.php" target="_blank">Previsiones de pago detallado</a></li>
                        <li><a href="#"></a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Anal&iacute;tica <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="informe-12.php" target="_blank">Gastos generales</a></li>
                        <li><a href="informe-13.php" target="_blank">Gastos medios materiales</a></li>
                        <li><a href="informe-18.php" target="_blank">Costes materiales desglosado</a></li>
                        <li><a href="informe-19.php" target="_blank">Importes contrataci&oacute;n</a></li>
                        <li><a href="#"></a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Cartera Efectos <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="informe-6.php" target="_blank">Registro Facturas Proveedores</a></li>
                        <li class="divider"></li>
                        <li><a href="informe-1.php" target="_blank">Proveedores efectos pendientes</a></li>
                        <li><a href="informe-2.php" target="_blank">Proveedores efectos pendientes por proyecto</a></li>
                        <li><a href="javascript;" data-toggle="modal" data-target="#tlf-confirming">Telefonos Confirming</a></li>
                        <li class="divider"></li>
                        <li><a href="informe-7.php" target="_blank">Clientes efectos pendientes</a></li>
                        <li><a href="informe-8.php" target="_blank">Clientes efectos pendientes por proyecto</a></li>
                        <li class="divider"></li>
                        <li><a href="informe-16.php" target="_blank">Facturaci&oacute;n de proveedores y subcontratas</a></li>
                        <li class="divider"></li>
                        <li><a href="contratos.php" target="_blank">Contratos</a></li>                        
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Ratios <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#"></a></li> 
                        <li><a href="#"></a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Veh&iacute;culos <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#"></a></li> 
                        <li><a href="#"></a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <a class="navbar-brand" href="index.php"><img alt="" src="img/logo_s.png" style="height:50px; margin-top:-15px;"/></a>
                <li><a href="login.php?logout=true;">Cerrar</a></li>
            </ul>
        </div>
    </div>
</nav>