<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'vendor/autoload.php';
    
    // $phpWord = new \PhpOffice\PhpWord\PhpWord();
    // $section = $phpWord->addSection();
    // \PhpOffice\PhpWord\Shared\Html::addHtml($section, $_POST['htmlstring']);
    // header('Content-Type: application/octet-stream');
    // header('Content-Disposition: attachment;filename="test.docx"');
    // $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    // $objWriter->save('php://output');


// require_once 'PHPWord/bootstrap.php';
// include_once 'Sample_Header.php';

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element with font customized using explicitly created font style object...
// $fontStyle = new \PhpOffice\PhpWord\Style\Font();
// $fontStyle->setBold(true);
// $fontStyle->setName('Tahoma');
// $fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
// $myTextElement->setFontStyle($fontStyle);

// $section = $phpWord->addSection();
$html = file_get_contents("../docs/subcontrata-anexos-general.php");
$html = utf8_decode($html);
$html = 
'	<style>
	body{
		font-family:Calibri,sans-serif;
		font-size:9pt;
		text-align:justify
	}
	p.sangria{
		text-indent:3em;
	}
	p.doble_sangria{
		text-indent:4em;
	}
	div{
		margin:20px 0
	}
	.listaguion{
		list-style:none
	}
	.listaguion li:before{
		content:"-"
	}
	table{
		border:1px solid black;
		border-collapse:collapse;
		width:100%
	}
	table tr td{
		border:1px solid black
	}
	.tablanoborde, .tablanoborde tr td{
		border:0;
		padding-bottom:7px;
	}
	.centrar{
		text-indent:14em;
	}
	u {
		text-decoration: underline;
	}
	</style>
	'.$html;
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('prueba.docx');

// Saving the document as ODF file...
// $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
// $objWriter->save('helloWorld.odt');

// // Saving the document as HTML file...
// $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
// $objWriter->save('helloWorld.html');

















// require_once 'PHPWord/PhpWord.php';

// // Create a new PHPWord Object
// $PHPWord = new PHPWord();

// // Every element you want to append to the word document is placed in a section. So you need a section:
// $section = $PHPWord->createSection();

// // After creating a section, you can append elements:
// $section->addText('Hello world!');

// // You can directly style your text by giving the addText function an array:
// $section->addText('Hello world! I am formatted.', array('name'=>'Tahoma', 'size'=>16, 'bold'=>true));

// // If you often need the same style again you can create a user defined style to the word document
// // and give the addText function the name of the style:
// $PHPWord->addFontStyle('myOwnStyle', array('name'=>'Verdana', 'size'=>14, 'color'=>'1B2232'));
// $section->addText('Hello world! I am formatted by a user defined style', 'myOwnStyle');

// // You can also putthe appended element to local object an call functions like this:
// $myTextElement = $section->addText('<span>Hello World!</span><h3>cambiaaraa</h3>');
// // $myTextElement->setBold();
// // $myTextElement->setName('Verdana');
// // $myTextElement->setSize(22);

// // At least write the document to webspace:
// $objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
// $objWriter->save('helloWorld.docx');














// use PhpOffice\PhpWord\PhpWord;
// include_once 'PHPWord/src/PhpWord/PhpWord.php';


// // include_once 'Sample_Header.php';

// // New Word Document
// echo date('H:i:s') , ' Create new PhpWord object';
// $phpWord = new \PhpOffice\PhpWord\PhpWord();

// $section = $phpWord->addSection();
// $html = '<h1>Adding element via HTML</h1>';
// $html .= '<p>Some well formed HTML snippet needs to be used</p>';
// $html .= '<p>With for example <strong>some<sup>1</sup> <em>inline</em> formatting</strong><sub>1</sub></p>';
// $html .= '<p>Unordered (bulleted) list:</p>';
// $html .= '<ul><li>Item 1</li><li>Item 2</li><ul><li>Item 2.1</li><li>Item 2.1</li></ul></ul>';
// $html .= '<p>Ordered (numbered) list:</p>';
// $html .= '<ol><li>Item 1</li><li>Item 2</li></ol>';

// \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

// // Save file
// echo write($phpWord, basename(__FILE__, '.php'), $writers);
// if (!CLI) {
//     include_once 'Sample_Footer.php';
// }