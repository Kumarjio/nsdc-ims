<?php
// Stop from parsing any further PHP code
  function tep_exit() {
   tep_session_close();
   exit();
  }

////
// Redirect to another page or site
  function tep_redirect($url) {
    if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) { 
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false));
    }

    if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on') ) { // We are loading an SSL page
      if (substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) { // NONSSL url
        $url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER)); // Change it to SSL
      }
    }

    header('Location: ' . $url);

    tep_exit();
  }

// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }

  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

  function tep_sanitize_string($string) {
    $patterns = array ('/ +/','/[<>]/');
    $replace = array (' ', '_');
    return preg_replace($patterns, $replace, trim($string));
  }

////
// Return a random row from a database query
  function tep_random_select($query) {
    $random_product = '';
    $random_query = tep_db_query($query);
    $num_rows = tep_db_num_rows($random_query);
    if ($num_rows > 0) {
      $random_row = tep_rand(0, ($num_rows - 1));
      tep_db_data_seek($random_query, $random_row);
      $random_product = tep_db_fetch_array($random_query);
    }

    return $random_product;
  }

// Break a word in a string if it is longer than a specified length ($len)
  function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      $char = substr($string, $i, 1);
      if ($char != ' ') {
        $l++;
      } else {
        $l = 0;
      }
      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }
      $output .= $char;
    }

    return $output;
  }

////
// Return all HTTP GET variables, except those passed as a parameter
  function tep_get_all_get_params($exclude_array = '') {
    global $_GET;

    if (!is_array($exclude_array)) $exclude_array = array();

    $get_url = '';
    if (is_array($_GET) && (sizeof($_GET) > 0)) {
      reset($_GET);
      while (list($key, $value) = each($_GET)) {
        if ( is_string($value) && (strlen($value) > 0) && ($key != tep_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
		  if(tep_not_null($get_url)) $get_url .= '&';
          $get_url .= $key . '=' . rawurlencode(stripslashes($value));
        }
      }
    }

	if($get_url != '')$get_url .= '&';

    return $get_url;
  }

////

// Returns the clients browser
  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

////

// Wrapper function for round()
  function tep_round($number, $precision) {
    if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {
      $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

      if (substr($number, -1) >= 5) {
        if ($precision > 1) {
          $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
        } elseif ($precision == 1) {
          $number = substr($number, 0, -1) + 0.1;
        } else {
          $number = substr($number, 0, -1) + 1;
        }
      } else {
        $number = substr($number, 0, -1);
      }
    }

    return $number;
  }

////

  function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

  function tep_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {
    setcookie($name, $value, $expire, $path, (tep_not_null($domain) ? $domain : ''), $secure);
  }

  function tep_validate_ip_address($ip_address) {
    if (function_exists('filter_var') && defined('FILTER_VALIDATE_IP')) {
      return filter_var($ip_address, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4));
    }

    if (preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $ip_address)) {
      $parts = explode('.', $ip_address);

      foreach ($parts as $ip_parts) {
        if ( (intval($ip_parts) > 255) || (intval($ip_parts) < 0) ) {
          return false; // number is not within 0-255
        }
      }

      return true;
    }

    return false;
  }

  function tep_get_ip_address() {
    global $_SERVER;

    $ip_address = null;
    $ip_addresses = array();

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      foreach ( array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_ip ) {
        $x_ip = trim($x_ip);

        if (tep_validate_ip_address($x_ip)) {
          $ip_addresses[] = $x_ip;
        }
      }
    }

    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip_addresses[] = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && !empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
      $ip_addresses[] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }

    if (isset($_SERVER['HTTP_PROXY_USER']) && !empty($_SERVER['HTTP_PROXY_USER'])) {
      $ip_addresses[] = $_SERVER['HTTP_PROXY_USER'];
    }

    $ip_addresses[] = $_SERVER['REMOTE_ADDR'];

    foreach ( $ip_addresses as $ip ) {
      if (!empty($ip) && tep_validate_ip_address($ip)) {
        $ip_address = $ip;
        break;
      }
    }

    return $ip_address;
  }

  function get_column_value($col, $table, $where = "")
 {
	$query = tep_db_query("select ". $col ." from ". $table ." " . $where );
	$query_values = tep_db_fetch_array($query);
	return $query_values[$col];
 }

function get_extension($name)	{
	$ext = strrchr($name,".");
	return $ext;
}

function send_mail($to, $subject, $message, $cc='', $bcc='') {
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: " . FROM_EMAIL_NAME . "<" . FROM_EMAIL . ">\r\n";

	if($cc != '')$headers .= 'Cc: ' . $cc . "\r\n";
	if($bcc != '')$headers .= 'Bcc: ' . $bcc . "\r\n";

	return mail($to, $subject, $message, $headers);	
}

function get_alpha_pos($alpha){
  //find the posting of alpha in array
  $arrAlpha = range('A', 'Z');
  $alpha_pos = array_keys($arrAlpha, $alpha);
  $alpha_pos = $alpha_pos[0];

  return $alpha_pos;
 }

 function check_apha_end($alpha_array){
  //check whole string for adding new element
  $return_value = true;
  foreach($alpha_array as $kAlpha=>$vAlpha){
   $cur_post = get_alpha_pos($vAlpha);
   if($cur_post != '25'){
    $return_value = false;
   }
  }

  return $return_value;
 }

 function gen_fix_type($fix_type){

  $arrAlpha = range('A', 'Z');

  $return_fix_type = '';

  if($fix_type == ''){
   $return_fix_type .= 'A';
  }else{

   $arr_str = str_split($fix_type);

   $arr_str_org = (array)$arr_str;

   $str_count = count($arr_str);

   if(is_array($arr_str)){
    foreach($arr_str as $kChunk=>$vChunk){
     if(!is_numeric($vChunk)){
      $alpha_pos = get_alpha_pos($vChunk);

      if($arr_str[$kChunk+1] != ''){
       $next_pos = $kChunk+1;
       $next_letter_alpha_pos = get_alpha_pos($arr_str[$next_pos]);

       if($next_letter_alpha_pos == '25'){
        $next_alpha_pos = ($alpha_pos != '25' ? ($alpha_pos+1) : 0);
        $next_apha = $arrAlpha[$next_alpha_pos];

        $arr_str[$kChunk] = $next_apha;
       }
      }else{
       $next_alpha_pos = ($alpha_pos != '25' ? ($alpha_pos+1) : 0);
       $next_apha = $arrAlpha[$next_alpha_pos];

       $arr_str[$kChunk] = $next_apha;
      }
     }
    }

    if(check_apha_end($arr_str_org)){
     $arr_str[$str_count] = $arrAlpha[0];
    }
   }

   $return_fix_type .= implode($arr_str);
  }

  return $return_fix_type;
 }

 function get_rand_fix_type($fix_type = ''){
  $last_str_value = substr($fix_type, -1);

  $retrun_fix_type = '';

  if(is_numeric($last_str_value)){
    $str_rem_value = substr($fix_type, 0, -2);
    $str_num_value = (int) substr($fix_type, -2);
    $str_add_value = ($str_num_value - 1);

    if($str_num_value > -9){
     $retrun_fix_type = $str_rem_value . $str_add_value;
    }else{
     $retrun_fix_type = gen_fix_type($str_rem_value);
    }

  }else{
   $retrun_fix_type = gen_fix_type($fix_type);
  }

  return $retrun_fix_type;
 }

 function check_valid_type($current_admin_type) {
	if($_SESSION['sess_adm_type'] != $current_admin_type){
		tep_redirect(tep_href_link(FILENAME_HOME));
	}
 }

 function display_valid_date($date){
	 $display_date = '-';
	 if($date != '' && $date != '0000-00-00' && $date != '1970-01-01' && $date != '1969-12-31'){
		 $display_date = date("d-m-Y", strtotime($date));
	 }

	 return $display_date;
 }

 function input_valid_date($date){
	 if($date != '' && $date != '0000-00-00'){
		 return date("Y-m-d", strtotime($date));
	 }

	 return '0000-00-00';
 }

 function change_student_status($student_id, $student_status) {
	tep_db_query("update " . TABLE_STUDENTS . " set student_status = '" . tep_db_input($student_status) . "' where student_id = '" . $student_id . "' limit 1");
 }

 function change_student_deact_status($student_id, $student_status) {
	tep_db_query("update " . TABLE_STUDENTS . " set is_deactivated = '" . tep_db_input($student_status) . "' where student_id = '" . $student_id . "' limit 1");
 }

 function display_currency($price, $currency='INR', $place='LEFT'){
	$arr_symbol = array('INR'=>'Rs', 'USD'=>'&#36;', 'POUND'=>'&pound;');
	$formatted_price = number_format($price, 2);

	if($place == 'LEFT'){
		$formatted_price = (isset($arr_symbol[$currency]) && $arr_symbol[$currency] !== '' ? $arr_symbol[$currency] . ' ' : '') . $formatted_price;
	}else{
		 $formatted_price = $formatted_price . (isset($arr_symbol[$currency]) && $arr_symbol[$currency] !== '' ? ' ' . $arr_symbol[$currency] : '');
	}

	return $formatted_price;
}

function generate_deposit_no(){
	$centre_id = $_SESSION['sess_centre_id'];

	$centre_info_query_raw = "SELECT centre_name FROM " . TABLE_CENTRES . " WHERE centre_id = '" .  $centre_id . "'";
	$centre_info_query = tep_db_query($centre_info_query_raw);
	$centre_info = tep_db_fetch_array($centre_info_query);

	$centre_name = str_replace(" ", "", $centre_info['centre_name']);
	$postfix_centre = strtoupper(substr($centre_name, 0, 4));

	$total_deposits_query_raw = "select count(*) as total_deposits from " . TABLE_DEPOSITS . " where centre_id = '" .  $centre_id . "'";
	$total_deposits_query = tep_db_query($total_deposits_query_raw);
	$total_deposits_array = tep_db_fetch_array($total_deposits_query);

	$total_deposits = $total_deposits_array['total_deposits'] + 1;
	$len_total_deposits = strlen($total_deposits);
	$remain_len_deposits = 4 - $len_total_deposits;
	$total_deposits = str_repeat('0', $remain_len_deposits) . $total_deposits;

	return $postfix_centre . $total_deposits;
}

function display_price($price, $currency_symbol='&#8377; '){
	$price = number_format($price, 2, '.', ',');
	$formatted_price = $currency_symbol . $price;

	return $formatted_price;
}
function calculate_tax($price)
{
	$taxRate=18;
	$tax=$price * $taxRate / 100;
	//$total=$price+$tax;
	//$culculated_tex=(($total-$price)/$price)*100;  
	return $tax;
}

function convert_number($number){
	if (($number < 0) || ($number > 999999999)) {
		throw new Exception("Number is out of range");
	}

	$Gn = floor($number / 1000000);
	/* Millions (giga) */
	$number -= $Gn * 1000000;
	$kn = floor($number / 1000);
	/* Thousands (kilo) */
	$number -= $kn * 1000;
	$Hn = floor($number / 100);
	/* Hundreds (hecto) */
	$number -= $Hn * 100;
	$Dn = floor($number / 10);
	/* Tens (deca) */
	$n = $number % 10;
	/* Ones */

	$res = "";

	if ($Gn) {
		$res .= convert_number($Gn) .  "Million";
	}

	if ($kn) {
		$res .= (empty($res) ? "" : " ") .convert_number($kn) . " Thousand";
	}

	if ($Hn) {
		$res .= (empty($res) ? "" : " ") .convert_number($Hn) . " Hundred";
	}

	$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
	$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");

	if ($Dn || $n) {
		if (!empty($res)) {
			$res .= " and ";
		}

		if ($Dn < 2) {
			$res .= $ones[$Dn * 10 + $n];
		} else {
			$res .= $tens[$Dn];

			if ($n) {
				$res .= "-" . $ones[$n];
			}
		}
	}

	if (empty($res)) {
		$res = "zero";
	}

	return $res;
}

function latest_invoice_number(){
	$centre_id = $_SESSION['sess_centre_id'];
	$latestInvoiceNumber = 0;

	$receipt_info_query_raw = "SELECT MAX(receipt_number) AS max_receipt_number FROM " . TABLE_RECEIPTS . " WHERE centre_id = '" .  $centre_id . "'";
	$receipt_info_query = tep_db_query($receipt_info_query_raw);
	$receipt_info = tep_db_fetch_array($receipt_info_query);

	$nextInvoiceNumber = (int)$receipt_info['max_receipt_number'] + 1;

	$latestInvoiceNumber = str_repeat('0', (8-(strlen($nextInvoiceNumber)))) . $nextInvoiceNumber;

	return $latestInvoiceNumber;
}
?>