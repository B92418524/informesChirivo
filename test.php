<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//require_once('config.php');

// require_once('config_i3.php');
// require_once('inc/mail/mail_functions.php');

//echo '<pre>'.print_r($_SESSION,true).'</pre>';

// echo get_header('informe-3');
// echo send_i3_mail();
// echo get_footer('informe-3');

// $importe = '10409.84';
// $importe = '10831.01';
// $importe = '2001';
$importe = '11.03';
echo $importe.'<br/>';
// $letras = convert_number_to_words($importe);
// echo $letras;

// echo '<br/><br/><br/>';
include_once 'functions/render/ajax/NumeroALetras.class.php';
$numeroLetras = new NumeroALetras();
echo $numeroLetras->convertir($importe, 'EUROS', 'CÉNTIMOS');


// function convert_number_to_words($number) {

//     $hyphen      = '-';
//     $conjunction = ' y ';
//     $separator   = ' ';
//     $negative    = 'negativo ';
//     $decimal     = ' CON ';
//     $dictionary  = array(
//         0                   => 'cero',
//         1                   => 'uno',
//         2                   => 'dos',
//         3                   => 'tres',
//         4                   => 'cuatro',
//         5                   => 'cinco',
//         6                   => 'seis',
//         7                   => 'siete',
//         8                   => 'ocho',
//         9                   => 'nueve',
//         10                  => 'diez',
//         11                  => 'once',
//         12                  => 'doce',
//         13                  => 'trece',
//         14                  => 'catorce',
//         15                  => 'quince',
//         16                  => 'dieciséis',
//         17                  => 'diecisiete',
//         18                  => 'dieciocho',
//         19                  => 'diecinueve',
//         20                  => 'veinte',
//         21                  => 'veintiun', // qué rico es el lenguaje español
//         29                  => 'veintinueve', // qué rico es el lenguaje español
//         30                  => 'treinta',
//         40                  => 'cuarenta',
//         50                  => 'cincuenta',
//         60                  => 'sesenta',
//         70                  => 'sesenta',
//         80                  => 'ochenta',
//         90                  => 'noventa',
//         100                 => 'cien',
//         '100s'              => 'ciento', // qué rico es el lenguaje español
//         200              	=> 'doscientos',
//         300             	=> 'trescientos',
//         400              	=> 'cuatrocientos',
//         500              	=> 'quinientos', // qué rico es el lenguaje español
//         600              	=> 'seiscientos',
//         700              	=> 'setecientos', // qué rico es el lenguaje español
//         800              	=> 'ochocientos',
//         900              	=> 'novecientos', // qué rico es el lenguaje español
//         1000                => 'mil',
//         1000000             => 'millones',
//         1000000000          => 'billones',
//         1000000000000       => 'trillones',
//         1000000000000000    => 'cuadrillones',
//         1000000000000000000 => 'quintillones'
//     );

//     if (!is_numeric($number)) {
//         return false;
//     }

//     if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
//         // overflow
//         // trigger_error(
//         //     'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
//         //     E_USER_WARNING
//         // );
//         return false;
//     }

//     if ($number < 0) {
//         return $negative . convert_number_to_words(abs($number));
//     }

//     $string = $fraction = null;

//     if (strpos($number, '.') !== false) {
//         list($number, $fraction) = explode('.', $number);
//     }

//     switch (true) {
//         case $number < 21:
//             $string = $dictionary[$number];
//             break;
//         case $number == 21 || $number == 29:
//             $string = $dictionary[$number];
//             break;
//         case $number < 100:
//             $tens   = ((int) ($number / 10)) * 10;
//             $units  = $number % 10;
//             $string = $dictionary[$tens];
//             if ($units) {
//                 $string .= $conjunction . $dictionary[$units];
//             }
//             break;
//         case $number < 1000:
//             $hundreds  = $number / 100;
//             $remainder = $number % 100;
//             $primeraCifra = (int)$hundreds;
//             $string = $dictionary[$primeraCifra.'00'];
//             if ($primeraCifra == '1') {
//             	$string = $dictionary['100s'];
//             }
//             if ($remainder) {
//             	// quitarle la conjuncion 'y' si era ciento y una unica unidad: ejemplo: 1502 -> que no ponga mil quinientos Y dos
//     //         	$segundoNumFinal = substr($hundreds, -2, 1);
// 				// if ($segundoNumFinal != '0') {
// 				// 	$string .= $conjunction;
// 				// } else {
// 				$string .= $separator;
// 				// }
// 				// ciento un EUROS, no ciento uno EUROS
// 				if ($remainder == '1') {
// 					$string .= 'y un';
// 				} else {
// 					$string .= convert_number_to_words($remainder);
// 				}
//             }
//             break;
//         default:
//             $baseUnit = pow(1000, floor(log($number, 1000)));
//             $numBaseUnits = (int) ($number / $baseUnit);
//             $remainder = $number % $baseUnit;
//             $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
//             if ($remainder) {
//                 $string .= $remainder < 100 ? $conjunction : $separator;
//                 $string .= convert_number_to_words($remainder);
//             }
//             break;
//     }

//     if (null !== $fraction && is_numeric($fraction)) {
//         // $string .= $decimal;
//         // $words = array();
//         // foreach (str_split((string) $fraction) as $number) {
//         //     $words[] = $dictionary[$number];
//         // }
//         // $string .= implode(' ', $words);
//         $string .= ' EUROS CON ';
// 		$string .= convertNumberToLetter($fraction, false); // ya no quiero que me compruebe las comas!
// 		$string .= utf8_decode(' CÉNTIMOS');
//     }

//     return $string;
// }
























// function convert_number_to_words2($number) {

//     $hyphen      = '-';
//     $conjunction = ' y ';
//     $separator   = ' ';
//     $negative    = 'negativo ';
//     $decimal     = ' CON ';
//     $dictionary  = array(
//         0                   => 'cero',
//         1                   => 'uno',
//         2                   => 'dos',
//         3                   => 'tres',
//         4                   => 'cuatro',
//         5                   => 'cinco',
//         6                   => 'seis',
//         7                   => 'siete',
//         8                   => 'ocho',
//         9                   => 'nueve',
//         10                  => 'diez',
//         11                  => 'once',
//         12                  => 'doce',
//         13                  => 'trece',
//         14                  => 'catorce',
//         15                  => 'quince',
//         16                  => 'dieciséis',
//         17                  => 'diecisiete',
//         18                  => 'dieciocho',
//         19                  => 'diecinueve',
//         20                  => 'veinte',
//         21                  => 'veinti',
//         30                  => 'treinta',
//         40                  => 'cuarenta',
//         50                  => 'cincuenta',
//         60                  => 'sesenta',
//         70                  => 'sesenta',
//         80                  => 'ochenta',
//         90                  => 'noventa',
//         100                 => 'cien',
//         '100s'              => 'ciento', // qué rico es el lenguaje español
//         200              	=> 'doscientos',
//         300             	=> 'trescientos',
//         400              	=> 'cuatrocientos',
//         500              	=> 'quinientos', // qué rico es el lenguaje español
//         600              	=> 'seiscientos',
//         700              	=> 'setecientos', // qué rico es el lenguaje español
//         800              	=> 'ochocientos',
//         900              	=> 'novecientos', // qué rico es el lenguaje español
//         1000                => 'mil',
//         1000000             => 'millones',
//         1000000000          => 'billones',
//         1000000000000       => 'trillones',
//         1000000000000000    => 'cuadrillones',
//         1000000000000000000 => 'quintillones'
//     );

//     if (!is_numeric($number)) {
//         return false;
//     }

//     if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
//         // overflow
//         // trigger_error(
//         //     'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
//         //     E_USER_WARNING
//         // );
//         return false;
//     }

//     if ($number < 0) {
//         return $negative . convert_number_to_words(abs($number));
//     }

//     $string = $fraction = null;

//     if (strpos($number, '.') !== false) {
//         list($number, $fraction) = explode('.', $number);
//     }

//     switch (true) {
//         case $number < 21:
//             $string = $dictionary[$number];
//             break;
//         case $number < 100:
//             $tens   = ((int) ($number / 10)) * 10;
//             $units  = $number % 10;
//             $string = $dictionary[$tens];

//             if ($units) {
//                 $string .= $conjunction . $dictionary[$units];

//             }
//             break;
//         case $number < 1000:
//             $hundreds  = $number / 100;
//             $remainder = $number % 100;
//             $primeraCifra = (int)$hundreds;
//             $string = $dictionary[$primeraCifra.'00'];
//             if ($primeraCifra == '1') {
//             	$string = $dictionary['100s'];
//             }
//             if ($remainder) {
//             	// quitarle la conjuncion 'y' si era ciento y una unica unidad: ejemplo: 1502 -> que no ponga mil quinientos Y dos
//             	$segundoNumFinal = substr($hundreds, -2, 1);
// 				// if ($segundoNumFinal != '0') {
// 				// 	$string .= $separator;
// 				// }
// 				//  else {
// 					$string .= $separator;
// 				// }
// 				echo $string.'<br/>';
// 				// ciento un EUROS, no ciento uno EUROS
// 				if ($remainder == '1') {
// 					$string .= 'y un';
// 				} else {
// 					$string .= convert_number_to_words($remainder);
// 				}
// 				echo $string.'<br/>';
//             }
//             break;
//         default:
//             $baseUnit = pow(1000, floor(log($number, 1000)));
//             $numBaseUnits = (int) ($number / $baseUnit);
//             $remainder = $number % $baseUnit;
//             $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
//             if ($remainder) {
//                 $string .= $remainder < 100 ? $conjunction : $separator;
//                 $string .= convert_number_to_words($remainder);
//             }
//             break;
//     }

//     if (null !== $fraction && is_numeric($fraction)) {
//         // $string .= $decimal;
//         // $words = array();
//         // foreach (str_split((string) $fraction) as $number) {
//         //     $words[] = $dictionary[$number];
//         // }
//         // $string .= implode(' ', $words);
//         $string .= ' EUROS CON ';
// 		$string .= convertNumberToLetter($fraction, false); // ya no quiero que me compruebe las comas!
// 		$string .= utf8_decode(' CÉNTIMOS');
//     }

//     return $string;
// }





// $importe = '5206,65';
// echo $importe.'<br/>';
// $importeLetras = convertNumberToLetter($importe, true);
// echo $importeLetras;

// function convertNumberToLetter($num, $moneda = false) {
// 	$arrayUnidades = array('','uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte');
// 	$arrayDecenas = array('','', 'veinti', 'treinta', 'cuarenta', 'cincuenta', 'sesenta','setenta', 'ochenta', 'noventa');
// 	$arrayCentenas = array('','ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos');
// 	$arrayUnidadesMillar = array('', 'mil', 'dos mil', 'tres mil', 'cuatro mil', 'cinco mil', 'seis mil', 'siete mil', 'ocho mil', 'nueve mil');
	
// 	$resultado = '';
// 	$tieneDecimales = false; // solo en el caso de que sea una moneda y tenga comas
// 	$arrayNum = str_split($num);
// 	$arrayOriginal = $arrayNum;

// 	echo '<pre>';
// 	var_dump($arrayNum);
// 	echo '</pre>';

// 	if ($moneda) { // si quiere transformar una moneda, puede contener comas
// 		if (strpos($num, ',') !== false) { // si tiene una coma... 
// 			// entonces solo transformo la primera parte antes de la coma, después le concateno un 'CON' y hago los decimales otra vez
// 		    $tieneDecimales = true;
// 		    // encuentro la coma y obtengo su posicion del array, POR EJEMPLO LA POSICION (KEY): 5
// 		    $posicionComa = array_search(',', $arrayNum);
// 		    // formo otro array solo con las keys del array de numeros
// 		    $n = array_keys($arrayNum);
// 		    // ahora devuelvo la posicion de la posicion de la coma pero en el array de todas las keys
// 			$count = array_search($posicionComa, $n);
// 			// corto desde el index 0 como inicio y la posicion (desde donde quiero eliminar, como tb quiero quitar la coma) como longitud
// 			$arrayNum = array_slice($arrayNum, 0, $count, true);

// 			echo '<pre>';
// 			var_dump($arrayNum);
// 			echo '</pre>';

// 			// obtengo ahora otro array solo con los decimales, la posicion de la coma + 1 (+1 para omitir la propia coma) y hasta el final del array
// 			$arrayDecimales = array_slice($arrayOriginal, $posicionComa+1, count($arrayOriginal), true);

// 			echo '<pre>';
// 			var_dump($arrayDecimales);
// 			echo '</pre>';
// 		}
// 	}

// 	if (count($arrayNum) === 4) { // Número con 4 cifras	
// 		$resultado .= $arrayUnidadesMillar[$arrayNum[0]];
// 	}
	
// 	if (count($arrayNum) >= 3) { // Número al menos 3 cifras	
// 		// Compruebo si las centenas es igual a 100
// 		if (implode(array_slice($arrayNum, count($arrayNum)-3)) === '100') {
// 			$resultado .= ' cien';
// 		} else {
// 			$resultado .= ' '.$arrayCentenas[$arrayNum[count($arrayNum)-3]];
// 		}
// 	}

// 	if (count($arrayNum) >= 2) { // Número al menos 2 cifras	
// 		// Compruebo si las centenas es menor que 21
// 		if ((int)implode(array_slice($arrayNum, count($arrayNum)-2)) < 21) {
// 			$unid = implode(array_slice($arrayNum, count($arrayNum)-2));
// 			if ($unid[0] == '0') {
// 				// si escribe ,01 debo coger sólo el UNO!!
// 				$unid = $unid[1];
// 			}
// 			$escribir = $arrayUnidades[$unid];
// 			if ($unid == '1') {
// 				// y si encima es solo uno, no quiero que me escriba: CON uno CENTIMOS! debe poner con UN CENTIMO!!
// 				$escribir = 'un';
// 			}
// 			$resultado .= ' '.$escribir;
// 		} else {		
// 			$resultado .= ' '.$arrayDecenas[$arrayNum[count($arrayNum)-2]];
// 			$espacio = ' ';	
// 			if ((int )$arrayNum[count($arrayNum)-2] != 2) {
// 				$resultado .= ' y';
// 			} else {
// 				$espacio = '';
// 			}
// 			$resultado .= $espacio.$arrayUnidades[$arrayNum[count($arrayNum)-1]];
// 		}
// 	}
	
// 	if (count($arrayNum) === 1) { // Número con una cifra
// 		$resultado .= ' '.$arrayUnidades[$arrayNum[count($arrayNum)-1]];	
// 	}

// 	if (count($arrayNum) >= 4 && $moneda) { // si era un número de 4 cifras o mayor, solo pasa con las monedas
// 		// no coge el ultimo número, como por ejemplo: 5206,65 -> no coge el 6
// 		$volverString = implode('',$arrayNum);
// 		// comprobar si el segundo numero empezando por el final, es un 0, entonces pintar el numero unidades final
// 		$segundoNumFinal = substr($volverString, -2, 1);
// 		if ($segundoNumFinal == '0') {
// 			$resultado .= ' '.$arrayUnidades[$arrayNum[count($arrayNum)-1]];
// 		}
// 	}

// 	if ($tieneDecimales && $moneda) {
// 		$resultado .= ' EUROS CON ';
// 		$volverString = implode('',$arrayDecimales);
// 		$resultado .= convertNumberToLetter($volverString, false); // ya no quiero que me compruebe las comas!
// 		$resultado .= ' CENTIMOS';
// 	}

// 	return $resultado;
// }


?>













