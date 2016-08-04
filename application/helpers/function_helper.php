<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -------------------------------------------------------------------------------
INDICE

- formatDatetime 		Unifica el formato de los datetime
- formatDate 			Unifica el formato de los date
- formatDatemin 		Unifica el formato de los date, solo dia y mes
- formatImporte 		Unifica el formato de los importes
- formatFloat 			Unifica el formato de los float
- formatBites 			Unifica el formato de los bites
- dias_transcurridos 	Cantidad de dias transcurridos entre dos fechas
- dates_between 		Devuelve array de fechas entre dos fechas
- encrypt				Encripta la cadena
- decrypt				Desencripta la cadena
- modulo11				para validar cuit
- validarCUIT			valida cuit
- random_string 		Devuelve cadenas aleatorias

  -------------------------------------------------------------------------------*/
  
  

function formatDatetime($fecha){
	if($fecha == '0000-00-00 00:00:00'){
		return '-';
	}else{
		return date('d/m/Y H:i:s', strtotime($fecha));
	}
}



function formatDate($fecha){
	if($fecha == ''){
		return '';
	}else{
		return date('d/m/Y', strtotime($fecha));
	}
}



function formatDatemin($fecha){
	if($fecha == ''){
		return '';
	}else{
		return date('d-m', strtotime($fecha));
	}
}



function formatImporte($importe){
	return '$ '.number_format($importe, 2, ',', '.');
}



function formatFloat($importe){
	$tamano 	= strlen($importe);
	$entero		= substr($importe, 0, $tamano - 2);
	$decimal	= substr($importe, $tamano - 2, 2);	
	$numero 	= $entero.'.'.$decimal;
	
	return $numero;
}



function formatBites($size, $multiplicar = NULL) {
	if($multiplicar == NULL){
		$size = $size * 1024;	
	}else{
		$size = $size * $multiplicar;
	}
	
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}



function dias_transcurridos($fecha_i, $fecha_f, $badle = NULL){
	if(($fecha_i == '0000-00-00' || $fecha_f == '0000-00-00') ||
	   ($fecha_i == '0000-00-00 00:00:00' || $fecha_f == '0000-00-00 00:00:00') ){
		$dias = '-';
	}else{
		$dias	= (strtotime($fecha_i) - strtotime($fecha_f))/86400;
		$dias 	= abs($dias); 
		$dias	= floor($dias);	
	}
	
	
	
	if($badle == NULL){
		return $dias;
	}else{
		return ' <span class="badge">'.$dias.' días</span>';
	}	
}



function dates_between($startdate, $enddate, $format = null){
 
    (is_int($startdate)) ? 1 : $startdate = strtotime($startdate);
    (is_int($enddate)) ? 1 : $enddate = strtotime($enddate);
 
    if($startdate > $enddate){
        return false; 
    }
 
    while($startdate < $enddate){
        $arr[] = ($format) ? date($format, $startdate) : $startdate;
        $startdate += 86400;
    }
    $arr[] = ($format) ? date($format, $enddate) : $enddate;
 
    return $arr;
}



function encrypt($string) {
	$key = _COOKIE_KEY_;	
	$result = '';
	for($i=0; $i < strlen($string); $i++)
	{
		$char		= substr($string, $i, 1);
		$keychar	= substr($key, ($i % strlen($key))-1, 1);
		$char		= chr(ord($char)+ord($keychar));
		$result	.= $char;
		
	}
	return base64_encode($result);
}



function decrypt($string){
	$key	= _COOKIE_KEY_;	
	$result = '';
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++)
	{
		$char		= substr($string, $i, 1);
		$keychar	= substr($key, ($i % strlen($key))-1, 1);
		$char		= chr(ord($char)-ord($keychar));
		$result		.= $char;
	}
	return $result;
}



function modulo11($cuit){
	$digitos = array(
		array('value' => substr($cuit, 0, 1), 	'multiplo' => 5),
		array('value' => substr($cuit, 1, 1), 	'multiplo' => 4),
		array('value' => substr($cuit, 2, 1), 	'multiplo' => 3),
		array('value' => substr($cuit, 3, 1), 	'multiplo' => 2),
		array('value' => substr($cuit, 4, 1), 	'multiplo' => 7),
		array('value' => substr($cuit, 5, 1),	'multiplo' => 6),
		array('value' => substr($cuit, 6, 1),	'multiplo' => 5),
		array('value' => substr($cuit, 7, 1),	'multiplo' => 4),
		array('value' => substr($cuit, 8, 1),	'multiplo' => 3),
		array('value' => substr($cuit, 9, 1),	'multiplo' => 2),
		array('value' => substr($cuit, 10, 1),	'multiplo' => 1),
	);
	$suma = 0;
	
	foreach ($digitos as $valores) {
		$suma = $valores['value'] * $valores['multiplo'] + $suma;
	}
	
	if($suma % 11 == 0) {
		return true;
	} else {
		return false;
	}
             
}



function validarCUIT($cuit){
	$comienzo	= substr($cuit, 0, 2);
	$comienzos	= array(
		20,
		23,
		24,
		27,
		30,
		33,
		34,
	);
	
	$bandera 	= 0;
	if(is_numeric($cuit)){
		foreach ($comienzos as $value) {
			if($comienzo == $value){
				if(modulo11($cuit)){
					$bandera = 1;
				} 
			}
		}
	}
	
	if($bandera == 1) {
		return true; 
	} else {
		return false;
	}
}



function random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));

    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}



 function NameCmp($a, $b){
        if($a[3]){
            return strcmp($a[0], $b[0]);     // directorios por NOMBRE
        } else {
            return ($a[1] < $b[1]) ? 1 : 0;  // archivos por FECHA
        }
    }