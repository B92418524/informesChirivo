<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$nombreDoc = 'prueba.doc';

// $html = file_get_contents("docs/subcontrata-anexos-general.php");
$html = file_get_contents("docs/suministro.php");
$html = utf8_decode($html);
        
header('Content-type: application/vnd.ms-word; charset=utf-8');
header("Content-Disposition: attachment;Filename=\"".$nombreDoc."\""); // solo funciona con comillas dobles!!!
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>";
echo '
    <style>
        p.MsoFooter, li.MsoFooter, div.MsoFooter {
            margin: 0cm;
            margin-bottom: 0cm;
            mso-pagination:widow-orphan;
            font-size: 12.0 pt;
        }
        p.MsoFooter1 {
            text-align: center;
        }
        @page Section1 {
            margin: 2cm 2cm 2cm 2cm;
            mso-page-orientation: portrait;
            mso-footer:f1;
        }
        .MsoFooter1 {
            text-align: center;
        }
        div.Section1 { page:Section1; }
        body {
            font-family:Calibri,sans-serif;
            font-size:9pt;
            text-align:justify;
        }
        p.sangria {
            text-indent:3em;
        }
        p.doble_sangria {
            text-indent:4em;
        }
        div {
            margin:20px 0
        }
        .listaguion {
            list-style:none
        }
        .listaguion li:before {
            content:"-"
        }
        table {
            border:1px solid black;
            border-collapse:collapse;
            width:100%
        }
        table tr td {
            border:1px solid black
        }
        .tablanoborde, .tablanoborde tr td {
            border:0;
            padding-bottom:7px;
        }
        .centrar {
            text-indent:14em;
        }
    </style>
    <body>
        <div class="Section1">
            '.$html.'
            <div style="mso-element:footer" id="f1">
                <div class="MsoFooter">
                    <p class=MsoFooter1>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>CHIRIVO CONSTRUCCIONES, S.L.</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>EL SUBCONTRATISTA</strong>
                        <br/>
                        <span style="mso-field-code:\' PAGE \'"></span>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>';