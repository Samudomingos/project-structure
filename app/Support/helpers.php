<?php
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;

if (!function_exists('add_mask')) {
  function add_mask($value, $mask, $autocomplete = false, $pad_type = STR_PAD_LEFT) {

    $len_val = strlen($value);
    $len_mask = strlen(preg_replace('/[^9aA#]/', '', $mask));

    if ($len_val === $len_mask) {
      $result = '';

      $arr_mask = str_split($mask);
      $arr_value = str_split($value);

      $x = 0;
      for($i = 0, $j = count($arr_mask); $i < $j; $i++) {

        if ($arr_mask[$i] === '9' && is_numeric($arr_value[$x])) {
          $result .= $arr_value[$x];
          $x++;
        }
        elseif ($arr_mask[$i] === '#' && preg_match('/[0-9a-zA-Z]/', $arr_value[$x])) {
          $result .= $arr_value[$x];
          $x++;
        }
        elseif (($arr_mask[$i] === 'A' || $arr_mask[$i] === 'a') && preg_match('/[a-zA-Z]/', $arr_value[$x])) {
          $result .= $arr_value[$x];
          $x++;
        }
        elseif (!preg_match('/[9aA#]/', $arr_mask[$i])) {
          $result .= $arr_mask[$i];
        }
        else {
          throw new BadMethodCallException('Caracter invalido');
        }
      }
      return $result;
    }
    else {
      if ($autocomplete === false) {
        throw new BadMethodCallException('Tamanho da mascara deve ser igual a do valor');
      }
      else {
        return add_mask(str_pad($value, $len_mask, $autocomplete, $pad_type), $mask);
      }
    }
  }
}

if (!function_exists('remove_mask')) {
  function remove_mask($value, $mask){

    if (strlen($value) === strlen($mask)) {
      $result = '';

      $arr_mask = str_split($mask);
      $arr_value = str_split($value);

      for($i = 0, $j = count($arr_mask); $i < $j; $i++) {

        if ($arr_mask[$i] === '9' && is_numeric($arr_value[$i])) {
          $result .= $arr_value[$i];
        }
        elseif ($arr_mask[$i] === '#' && preg_match('/[0-9a-zA-Z]/', $arr_value[$i])) {
          $result .= $arr_value[$i];
        }
        elseif (($arr_mask[$i] === 'A' || $arr_mask[$i] === 'a') && preg_match('/[a-zA-Z]/', $arr_value[$i])) {
          $result .= $arr_value[$i];
        }
        elseif (!preg_match('/[9aA#]/', $arr_mask[$i])) {
        }
        else {
          throw new BadMethodCallException('Caracter invalido');
        }
      }
      return $result;
    }
    else {
      throw new BadMethodCallException('Tamanho da mascara deve ser igual a do valor');
    }

  }
}

if (!function_exists('add_mask_cnpj')) {
  function add_mask_cnpj($value) {
    return add_mask($value, '99.999.999/9999-99');
  }
}

if(!function_exists('format_plural')){

  function format_plural($count, $singular, $plural, $args = array(), $options = array()) {
    if ($count == 1) {
      return isset($options['return_formated']) ? ('1 '.$singular) : $singular;
    }else{
      return isset($options['return_formated']) ? ($count.' '.$plural) : $plural;
    }
  }
}

function truncate_text($texto, $limit, $sufix='...'){
    if(strlen($texto)<=$limit){
        return $texto;
    } 
    return (substr($texto,0,$limit)).$sufix;
}

/**
* Extrai de um date_interval String(ex.: 1 Day) o nome do intervalo
* @param STRING $str_interval é esperada uma string que represente um formato de intervalo de data. Obs o intervalo tem que ser apenas de uma granulação. não pode ser algo como 1 WEEK 3 DAYS tem que ser 1 week  
* 
* @return STRING caso a str_interval seja valida é retornado uma string ( Ex; Week) se não o retorno é uma string vazia
*/
function extract_interval_name($str_interval){
    $a=explode(' ', $str_interval);
    $r='';
    if(isset($a[1])){
        $b=get_time_scale_by_name($a[1]);
        return ($b>0)? $a[1] : $r;
    }
    return $r;
}
function get_time_scale_by_name($name='sec'){
  switch( strtolower($name)){
    case 'y': case 'year': case 'years': case 'ano':  case 'anos':
     $retorno=31536000;
    break;
    case 'm': case 'month': case 'months': case 'mês':  case 'meses':
     $retorno=2592000;
    break;
    case 'w': case 'week': case 'weeks': case 'semana':  case 'semanas':
     $retorno=604800;
    break;
    case 'd': case 'day': case 'days': case 'dia':  case 'dias':
     $retorno=86400;
    break;
    case 'h': case 'hour': case 'hours': case 'hora':  case 'horas':
     $retorno=3600;
    break;
    case 'i': case 'min': case 'minutes': case 'minuto':  case 'minutos':
     $retorno=60;
    break;
    case 's': case 'sec': case 'seconds': case 'segundo':  case 'segundos':
     $retorno=1;
    break;
    default :
     $retorno=0;
    break; 
  }
  return $retorno;
}
function translate_time_unit(){
    
}
function format_interval($interval, $granularity = 2){
  $units = array(
    ' year| years' => 31536000,
    ' month| months' => 2592000,
    ' week| weeks' => 604800,
    ' day| days' => 86400,
    ' hour| hours' => 3600,
    ' min| min' => 60,
    ' sec| sec' => 1
  );
  $output = '';
  foreach ($units as $key => $value) {
    $key = explode('|', $key);
    if ($interval >= $value) {
      $div=floor($interval / $value);
      $formatado=format_plural($div, $key[0],$key[1], array(), array('return_formated'=>true));
      $output .= ($output ? ' ' : '') .$formatado ;
      $interval %= $value;
      if($granularity==$retorno_em){
         break;
      }
      $granularity--;
    }
    if ($granularity == 0) {
      break;
    }
  }
  return $output ? $output : '0 sec'; //t('0 sec', array(), array('langcode' => $langcode));
}
/**
* Verifica se uma STRING|INT corresponde a um minuto valido 00 á 59.
* @param STRING|INT $minuto  STRING ou INT que represente os minutos. Quando valor for menor que 10 deverá ter o 0(Zero) a esquerda.
* 
* @return TRUE ou FALSE
*/
function is_minuto($minuto){
	if(!preg_match('#^[0-5][0-9]{1,2}?$#i',$minuto)){
		return false;
		
	}
	return true;
}
/**
* Verifica se uma string é uma hora valida do formato 24 com zero a esquerda em numeros menores que 10
* @param INT String $hora  
* 
* @return TRUE ou FALSE
*/
function is_hora($hora){
	if(!preg_match('#^[0-1][0-9]|[2][0-3]{1,2}$#i',$hora)){
		return false;		
	}
	return true;
}
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function pega_mes($mes_atual, $qtd){
		
		if($qtd == 0){
			$mes = $mes_atual;
		}
		elseif($qtd == 1){
			if($mes_atual == '01'){
				$mes = "12";	
			}else{
				$mes = $mes_atual - 1;
			}
		}
		elseif($qtd == 2){
			if($mes_atual == '01'){
				$mes = "11";	
			}elseif($mes_atual == '02'){
				$mes = "12";	
			}else{
				$mes = $mes_atual - 2;
			}
		}
		elseif($qtd == 3){
			if($mes_atual == '01'){
				$mes = "10";	
			}elseif($mes_atual == '02'){
				$mes = "11";	
			}else{
				$mes = $mes_atual - 3;
			}
		}
		elseif($qtd == 4){
			if($mes_atual == '01'){
				$mes = "09";	
			}elseif($mes_atual == '02'){
				$mes = "10";	
			}else{
				$mes = $mes_atual - 4;
			}
		}
		
		switch($mes){
			
			case "01": $mes_es = "JAN";
						break;
			case "02": $mes_es = "FEV";
						break;
			case "03": $mes_es = "MAR";
						break;
			case "04": $mes_es = "ABR";
						break;						
			case "05": $mes_es = "MAI";
						break;
			case "06": $mes_es = "JUN";
						break;
			case "07": $mes_es = "JUL";
						break;
			case "08": $mes_es = "AGO";
						break;
			case "09": $mes_es = "SET";
						break;		
			case "10": $mes_es = "OUT";
						break;			
			case "11": $mes_es = "NOV";
						break;			
			case "12": $mes_es = "DEZ";
						break;												
																										
		}
		
		return $mes_es;
	}
if (!function_exists('dias_uteis')) {
  function dias_uteis(DateTime $data_ini, DateTime $data_fim, $feriados = array(), $sabados = false) {
    $ini = clone $data_ini;
        $uteis = 0;

    while ($ini->getTimestamp() <= $data_fim->getTimestamp()) {

      $dS = $ini->format('w');

      if ($dS != "0" && ($dS != "6" || $sabados)) {

        if (!empty($feriados)) {
          if (array_search($ini->format('d/m/Y'), $feriados) === false) {
            $uteis++;
          }
        }
        else {
          $uteis++;
        }
      }
      $ini->add(new DateInterval('P1D'));
    }

    return $uteis;
  }
}
if (!function_exists('ultimo_dia_mes')) {
  function ultimo_dia_mes($mes, $ano = null) {
    if (empty($ano)) {
      $ano = date('Y');
    }

    return date("t", mktime(0,0,0,$mes,'01',$ano));
  }
}
if(!function_exists('validateEan13')){
 function validateEan13($barcode)
{
    // check to see if barcode is 13 digits long
    if (!preg_match("/^[0-9]{13}$/", $barcode)) {
        return false;
    }

    $digits = $barcode;

    // 1. Add the values of the digits in the 
    // even-numbered positions: 2, 4, 6, etc.
    $even_sum = $digits[1] + $digits[3] + $digits[5] +
                $digits[7] + $digits[9] + $digits[11];

    // 2. Multiply this result by 3.
    $even_sum_three = $even_sum * 3;

    // 3. Add the values of the digits in the 
    // odd-numbered positions: 1, 3, 5, etc.
    $odd_sum = $digits[0] + $digits[2] + $digits[4] +
               $digits[6] + $digits[8] + $digits[10];

    // 4. Sum the results of steps 2 and 3.
    $total_sum = $even_sum_three + $odd_sum;

    // 5. The check character is the smallest number which,
    // when added to the result in step 4, produces a multiple of 10.
    $next_ten = (ceil($total_sum / 10)) * 10;
    $check_digit = $next_ten - $total_sum;

    // if the check digit and the last digit of the 
    // barcode are OK return true;
    if ($check_digit == $digits[12]) {
        return true;
    }

    return false;
}
}
 /*
     * Remove os acentos 
     *  
     */
if(!function_exists('removeAcentos')){
    function removeAcentos($str)
    {
        if (empty($str)) {
            return $str;
        }
        
        $string = trim($str);
        $singleByteString = mb_convert_encoding(mb_strtolower($string, 'UTF-8'), 'ISO-8859-1', 'UTF-8');
        $noAccentsString = strtr($singleByteString, utf8_decode('ãâáàäéèêëíìïîóõòôöúùüûçñ'), 'aaaaaeeeeiiiiooooouuuucn');
        $noAccentsString = preg_replace('/[^\w\s\(\)\[\]\-\.\/\'"`´\@\=\+\*\%\$\!]/i', '', $noAccentsString);
        //return mb_strtoupper(mb_convert_encoding($noAccentsString, 'UTF-8', 'ISO-8859-1'), 'UTF-8');
        return mb_convert_encoding($noAccentsString, 'UTF-8', 'ISO-8859-1');
    }
}  

 function removeAcentosERP($str)
    {
        if (empty($str)) {
            return $str;
        }
        
        $string = trim($str);
        $singleByteString = mb_convert_encoding(mb_strtolower($string, 'UTF-8'), 'ISO-8859-1', 'UTF-8');
        $noAccentsString = strtr($singleByteString, utf8_decode('ãâáàäéèêëíìïîóõòôöúùüûçñ'), 'aaaaaeeeeiiiiooooouuuucn');
        $noAccentsString = preg_replace('/[^\w\s\(\)\[\]\-\.\/\'"`´\@\=\+\*\%\$\!]/i', '', $noAccentsString);
        return $noAccentsString;//, 'UTF-8', 'ISO-8859-1');
    }

    if(!function_exists('find_mult_in_keys')){
        function find_mult_in_keys($query, $ids){
            foreach ($ids as $key=>$value) {
                $query->where($key, '=', $value);
            }
            return $query;
       }
    }
    
    if(!function_exists('response_with_token')){
        function response_with_token($user){
            $token = json_encode($user);
            $hash_token= hash("sha256", time());
            $expire=31536000;
            $accesss_token="lalalalalal";
            $refresh_token="xxaajkhglkhklhl";   
             $objct=["value"=>"",
                "mac"=>"",
                "iv"=>"",
                "token_type"=>"Bearer",
                "expires_in"=> $expire,
                "access_token"=>true,   
                "refresh_token"=>$refresh_token,
                "user"=>$user
            ];
             $response=new \Illuminate\Http\Response(); 
             return $response->withHeaders($objct)->content(json_encode(['uuu'=>'ssas', 'ass'=>'564564654']));
        }
        
    }
    function getSodiumCryptoSecretboxNoncebytes(){
        return SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
       // return 24;
    }
    
    function getSodiumSecretboxKeyBytes(){
       return SODIUM_CRYPTO_SECRETBOX_KEYBYTES;
      //  return 64;
    }
    
   function safeDecrypt(string $encrypted, string $key = null){
            $key = $key ? $key : getSodiumCryptoKey();//hex2bin(getSodiumCryptoKey());
            $decoded    = base64_decode($encrypted);
            $nonce      = mb_substr($decoded, 0, getSodiumCryptoSecretboxNoncebytes(), '8bit');
            $ciphertext = mb_substr($decoded, getSodiumCryptoSecretboxNoncebytes(), null, '8bit');
            $plain = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
             
            if (!is_string($plain)) {
                throw new \RangeException('Falha ao decriptar string. 300820191436');
            }
            sodium_memzero($ciphertext);
            sodium_memzero($key);
            return $plain;
    }         
    function safeEncrypt(string $message, string $key = null){
        
        $key = $key ? $key : getSodiumCryptoKey();
        
        if (($len_key=mb_strlen($key, '8bit')) !== getSodiumSecretboxKeyBytes()) {
                throw new \RangeException('Key is not the correct size (must be 32 bytes).'.$len_key.'--'.getSodiumSecretboxKeyBytes());
        }
        
        $nonce = random_bytes(getSodiumCryptoSecretboxNoncebytes());
        $str_bin=$nonce.sodium_crypto_secretbox($message, $nonce, $key);
        sodium_memzero($message);
        sodium_memzero($key);
        $cipher =base64_encode($str_bin);
        return $cipher;
    }
     function getSodiumCryptoKey(){
     
     $len= getSodiumSecretboxKeyBytes(); //env('SODIUM_CRYPTO_KEY_LENGTH'); 
     $sodium_64=env('SODIUM_CRYPTO_KEY_64','nXSE8OmIE5uzm4XJyDXHTHJllyUtnANfDi6kJV14ZGO2kxCtLCdt0eFB0SINPTJq');
     $sodium_32=env('SODIUM_CRYPTO_KEY_32','E8OmIE5uzm4XJyDXHTHJllyUtnANfDi6');
	       
     return $len==32 ? $sodium_32 : $sodium_64;
    }  

    
if(!function_exists('get_email_config')){
  function get_email_config($field) {
    return DB::table('tbl_config')->where('field',$field)->first()->value;
  }
}