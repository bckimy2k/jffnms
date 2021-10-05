<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2010> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

//hard-coded alarm state IDs
define ('ALARM_DOWN',1);
define ('ALARM_UP',2);
define ('ALARM_ALERT',3);
define ('ALARM_TESTING',4);

define ('ENTERPRISES_OID', '.1.3.6.1.4.1');

//DEBUG
//-----------------------------------------

function logger($text, $show_date = true)
{
  global $Config, $method;

  $old_error_reporting = error_reporting(7);
  $text = (($show_date==true)?date('H:i:s')." ":"").$text;
  error_reporting($old_error_reporting);

  if ($Config->get('jffnms_debug')==1)
  {
      if (array_key_exists('Child', $GLOBALS)) {
          $GLOBALS['Child']->logger($text);
          return;
      }  
      $file = str_replace('.php','',basename($Config->get('logging_file')));
      $filename = $Config->get('log_path').'/'.$file.'-'.date('Y-m-d').'.log';

      if ( ($log = fopen($filename,'a+')) === FALSE) {
          print "Unable to open log file $filename.\n";
          return;
      }
      fputs($log, $text);
      fclose($log);
  }
  echo $text;
}

function debug($text)
{
  echo "\n<PRE>";
  print_r($text);
  echo "</PRE>\n";
  flush();
}

function d($text) {
  echo "\n<PRE>";
  var_dump($text);
  echo "</PRE>\n";
  flush();
}


function vd_tab ($count)
{
  return @str_repeat("\t",$count);
}

function vd($data,$pos = 1)
{
  $result = vd_tab($pos-2).gettype($data)."(";
  if (is_array($data)) {
      $result .= count($data).") { \n";
      foreach ($data as $key=>$value) 
    $result.= vd_tab($pos)."[$key] => ".vd($value,$pos+1)."\n";
      $result .= vd_tab($pos)."} \n";
  } else 
      $result.= strlen(strval($data)).") \"$data\"";
  return $result;
}

// Youre kidding me right? the float and floatvar are not locale
// aware? Really?
// Lucky for us rrdtool exports things as scientific notation so we don't
// need to worry about thousand separators
function str2f($str)
{
    $linfo = localeconv();
    $str = str_replace(',','.',$str);
    return floatval($str);
}


// TIME FUNCTIONS
//-----------------------------------------
//
// Return Unix timestamp in milliseconds
function time_msec()
{
  return microtime(TRUE) * 1000;
}

function time_msec_diff($from, $to=FALSE)
{
  if (!$to)
    $to = time_msec();
  return round($to-$from,2);
}


//MISC
//---------------------------------------------

    function dec2hex($dec) {
  $hex=dechex($dec);
  if (strlen($hex)==1) $hex="0$hex";
  return $hex;
    }

    function str_encode($str,$decode = 0) {
  $from = "ab'cdefghij1:234567890kl=mnozABCDE<FGHI}JKLM;NOPQRSTUV>WXY{Zpqrstuvwxy";
  $to   = "nopq34'56tuvwxyr;sWX2zaTU:VY01789=ZABCDEFbcde<fghijkl{m>NOPQRS}GHIJKLM";

  if ($decode==1)  return strtr($str, $to, $from);
  else return strtr($str, $from, $to);
    }

    /*
  Function:      object2array
  Purpose:       Convert an object to an array recursively: 
                     all object children will be converted to arrays too
    */
    function object2array ( $object ) {
  if ( !is_object ( $object ) && !is_array($object) ) // if $object is not an object nor an array
          return $object;  // return it as is
  
  $ret = array ();  // create return array

  if (is_array($object))
      $v = $object;  //take it as an array
  else
      $v = get_object_vars ( $object ); // retrieve all object properties and values
  
  foreach($v as $prop => $value) // create key=>value pairs for all $prop=>value pairs
          $ret [ $prop ] = object2array ( $value );
  
  return $ret;
    }

    function satellize($data_to_send,$capabilities = NULL) {
  if (isset($data_to_send)) {  

      if (!$capabilities) $capabilities = unsatellize(); //default
      if (strlen($capabilities)==1) $only_one = TRUE; else $only_one = FALSE;
      
      if (strpos($capabilities,"V")!==FALSE) {
    if (!$only_one) $data_header .= "V";
    $data_to_send = vd($data_to_send);
      }

      if (strpos($capabilities,"S")!==FALSE) {
    if (!$only_one) $data_header .= "S";
    $data_to_send = serialize($data_to_send);
      }

      if (strpos($capabilities,"W")!==FALSE) 
    if (strpos($capabilities,"S")===FALSE) { //not using Serialize
        if (!$only_one) $data_header .= "W";  //only if other capabilties are support, add the tag
        $data_to_send = wddx_serialize_value($data_to_send);
    }

      if (strpos($capabilities,"R")!==FALSE)
      if (is_string($data_to_send)) {
    if (!$only_one) $data_header .= "R";
          $data_to_send = str_encode($data_to_send);
      }

      if (strpos($capabilities,"O")!==FALSE) { //soap
    if (!$only_one) $data_header .= "O";

    //create SOAP Response
    require_once 'SOAP/Server.php';
    $server = new SOAP_Server;

        $return_val = $server->buildResult($data_to_send, $a);
    $qn = new QName($GLOBALS["method"].'Response',"urn:JFFNMS");
    $methodValue = new SOAP_Value($qn->fqn(), 'Struct', $return_val);

    header("Content-Type: text/xml; charset=UTF-8");
    $data_to_send = $server->_makeEnvelope($methodValue, $header_results, "UTF-8");

    unset($server);
      }
      
      if ($data_header) $data_ready = $data_header."|";
      
      $data_ready .= $data_to_send;
  }
  return $data_ready;
    }
    
    function unsatellize($data = NULL, $capabilities = NULL) {
  
  if (($data) && (strpos(substr($data,0,10),"|")===FALSE)) { //if no header recived add the default capabilties, probably only one is there like W
      if (!$capabilities) $capabilities = unsatellize();
      $header = $capabilities;
      $data_to_recv = $data;
  } else {
      $pos = strpos($data,"|");
      $header = substr($data,0,$pos);
      $data_to_recv = substr($data,$pos+1,strlen($data)-$pos);
  }

  for ($i=strlen($header); $i >= 0 ; $i--) //des satellize in reverse order 
      switch ($header[$i]) {
    case "S":  
        $data_to_recv = unserialize(stripslashes($data_to_recv));
        break;

    case "W":  
        $data_to_recv = wddx_deserialize(stripslashes($data_to_recv));
        break;

    case "R":  
        $data_to_recv = str_encode($data_to_recv,1);
        break;
    
    case "O":  if (is_string($data_to_recv)) { //because it may be already decoded

            //SOAP Server Request Decoding    
            require_once 'SOAP/Server.php'; 
            $server = new SOAP_Server;
        
            $parser = new SOAP_Parser($data_to_recv,"UTF-8",$attachments);
            $data_to_recv = $server->_decode($parser->getResponse()); 
            $data_to_recv = object2array($data_to_recv);
  
            unset($server);
        }
        break;

      }
  $data_ready = $data_to_recv;

  if (!$data) { //send capabilties
      $data_ready = "S";   
      if (extension_loaded("wddx")) $data_ready.="W";
      //$data_ready .= "R";
  }
  return $data_ready;
    }

    function unsatellize_headers ($raw_headers, $capabilities) {

  $headers = array();
  for ($i=strlen($capabilities); $i >= 0 ; $i--) //des satellize in reverse order 
      switch ($capabilities[$i]) {
    case "O": //SOAP
          $data = unsatellize($raw_headers,$capabilities);
          $raw_headers = preg_replace("/(\r|\n|\(\?\#.*\))/", "", $raw_headers); //take the \r\n's out
          
          $headers["sat_id"] = $data["sat_id"];
          $headers["class"] = $data["class"];
          
          if (preg_match("/<SOAP-ENV:Body>(\s*|)<\S+:(\S+)(>| xmlns)/",$raw_headers,$parts)) 
        $headers["method"] = $parts[2];
          
          $headers["session"] = $data["session"];
          $headers["params"] = $data["params"];

          unset($data);
          break;
  
    case "W": //WDDX
          if ($raw_headers!==NULL) {
        $headers = unsatellize($raw_headers,$capabilities);
        
        $headers["params"] = satellize ($headers["params"],$capabilities); //Re-satellize parameters
          }    
          break;
    default :  
          //FIXME get data from _SERVER
          //OR do Nothing, because the values will be already decoded
          break;
      }

  return $headers;
    }  

    function array_copy_value_to_key($array){ 
  foreach($array as $value) $result[$value]=$value;
  return $result;
    }

    function ad_set_default(&$var,$value) {
  if (!isset($var)) $var = $value;
    }

function is_process_running ($process_name = NULL, $number_of_instances = 1)
{
  global $Config;
  
  if (!isset($process_name))      //if process name is not set
    $process_name = $_SERVER['PHP_SELF'];  //use the current process name

  $process_name_len = strlen($process_name);
  $found = 0;
  
  if ($process_name_len > 1)      //if name is rigth
    if ($Config->get ('os_type')=='unix')  //if we are on a Unix OS
    {
      //exec('ps axo args',$ps_list); //call 'ps'
      exec('ps --no-headers -C php -o args',$ps_list); //call 'ps'
      if (count($ps_list) > 0)
        foreach ($ps_list as $process)
        {
          $pos = strpos($process,$process_name);
          if (($pos!==false) && (($pos+$process_name_len)==strlen($process))) //if proc_name is at the end of the process string
            $found++;
          if ($found >= $number_of_instances) //if we have found enogh instances
            return true;
        }
    }
  return FALSE;
}

function create_command_line ($command_line)
{
  global $Config;

  if ($Config->get('os_type') == 'windows') 
    return "start /MIN $command_line";
  return $command_line.' &';
}
    
function jffnms_shared ($module)
{
  global $Config;
  return $Config->get('jffnms_real_path').'/engine/shared/'.$module.'.inc.php';
}

function spawn ($command = false, $parameters = "", $max_instances = 2)
{
  global $Config;

  if (!$command)
    $command = $Config->get('php_executable').' -q '.$_SERVER['argv'][0];
  $command_line = "$command $parameters";
      
  if (is_process_running($command_line,$max_instances) === false)
  {
    $open = create_command_line($command_line);    
    echo "Executing: $open\n";
    $p = popen($open,'w');
  } else 
    logger ($command_line." is already running $max_instances times.\n");
}

function array_key_sort(&$arr, $keys)
{
  $sort_columns = array();
  reset ($arr);
  $params = array();
  foreach ($arr as $row_key => $row)
    foreach ($keys as $sortk=>$sort_type)
      if (array_key_exists($sortk, $row))
        $sort_columns[$sortk][$row_key] = $row[$sortk];

  foreach ($keys as $sortk=>$sort_type)
  {
    if (array_key_exists($sortk, $sort_columns))
    {
      $params[]=&$sort_columns[$sortk];
      $params[]=&$sort_type;
    }
  }
  $params[]=&$arr;
  call_user_func_array('array_multisort',$params);
}

function convert_sql_sort ($fields, $sorts)
{
  $result = array();

  foreach ($sorts as $sort)
  {
    $sort_field = $sort[0];
    $field_name = array_search($sort_field, $fields);
    if (is_numeric($field_name))
    {
      if (preg_match('/\.([^.]+)$/', $sort_field, $regs))
        $field_name = $regs[1];
    }

    if (array_key_exists(1, $sort) && $sort[1] == 'desc')
      $sort_order = SORT_DESC;
    else
      $sort_order = SORT_ASC;
    if ($field_name=='interface')
      $order = SORT_NUMERIC;

    $result[$field_name]=$sort_order;
  }
  //debug ($result);
  return $result;    
}

    function array_rekey($array, $key, $old_key_name = false) {
    
  reset($array);
  
  foreach($array as $old_key => $data) {
      $new_key = $data[$key];
      $new[$new_key]=$data;

      if ($old_key_name!==false) 
    $new[$new_key][$old_key_name]=$old_key;
  }
  
  return $new;
    }

    function array_search_partial($search, $array_in) {

  foreach ($array_in as $key => $value)
       if (strpos($value, $search) !== false)
    return $key;
    
  return false;
    }              
    
    function arristr($haystack='', $needle=array()) {

        foreach($needle as $n)
      if (stristr($haystack, $n) !== false)
    return true;
   
  return false;
    }

function time_hms ()
{
  //Find the first numeric parameter
  $args = func_get_args();
  if (!is_array($args))
  {
    return '00:00';
  }
  foreach ($args as $arg)
    if (is_numeric($arg))
      return str_pad(floor($arg/(60*60)),2,"0",STR_PAD_LEFT).date(":i:s",$arg);
  return '00:00';
}

function array_record_search ($rows, $needle_field, $needle_value)
{
    $result = array();
  
    if (is_array($rows)) {
        reset($rows);
        foreach($rows as $id => $row)
            if ($row[$needle_field] == $needle_value)
                $result[$id]=$row;
    }
    return $result;
}    
    
    function byte_format($input, $dec = 0) {
  $prefix_arr = array("", "K", "M", "G", "T");
        $value = round($input, $dec);

  while ($value > 1024) {
      $value /= 1024;
      $i++;
  }
  
  $return_str = round($value, $dec)." ".$prefix_arr[$i];
  return $return_str;
    }
    
    function detach() {
  if (function_exists('pcntl_fork') && array_key_exists('start_debug',$GLOBALS) && ($GLOBALS['start_debug']!=1)) {
      if (($launcher_forkpid = pcntl_fork()) == -1) 
        die("could not fork\n");
      else 
    if ($launcher_forkpid) // we are the parent
        exit();
    
      return true;
  }
  
  return false;
    }

function array_item_blank(&$arr, $key)
{
  return array_fetch($arr, $key, '');
}

function array_fetch(&$arr, $key, $default)
{
  if (is_array($arr) and array_key_exists($key, $arr))
    return $arr[$key];
  return $default;
}

/*
 */
spl_autoload_register(function ($class_name)
{
  global $Config;

  if (isset($Config))
    $jffnms_real_path = $Config->get('jffnms_real_path');
  else
    $jffnms_real_path = '..';
  $class_filename = $jffnms_real_path . '/lib/'.strtolower(str_replace('Jffnms', '', $class_name)).'.class.php';
  if (!is_readable($class_filename))
    die ("Class file '$class_filename' is not readable.\n");
  require($class_filename);
});  //__autoload()

function jffnms_load_api($api_names)
{
  global $Config;

  if (isset($Config))
    $jffnms_real_path = $Config->get('jffnms_real_path');
  else
    $jffnms_real_path = '..';

  if (!is_array($api_names))
    $api_names = array($api_names);

  foreach ($api_names as $api_name)
  {
    $api_filename = $jffnms_real_path . "/lib/api.$api_name.inc.php";
    if (!is_readable($api_filename))
      die ("API file '$api_filename' is not readable.\n");
    require_once($api_filename);
  }
}

// {{{ resolve_host()

/**
 * Resolves the given hostname or IP address
 *
 * The function can be given a hostname, IPv4 or IPv6 address and attempts
 * to resolve it to a family,address,port tuple
 *
 * @param string $host  String of a hostname or addres, mayeb include :port
 * @return array (address family, ip address, port)
 *               returns FALSE if cannot resolve
 **/
function resolve_host($host)
{
  # IPV$ address with optional port
  if (preg_match('/^([0-9.]+)(?::(\d+))?$/', $host, $regs))
  {
    if (@inet_pton($regs[1]) === FALSE)
      return FALSE;
    if (array_key_exists(2,$regs))
      $port=$regs[2];
    else
      $port='';
    return array(4,$regs[1],$port);
  }
  if (preg_match('/^([0-9a-f:]+)$/',$host, $regs) 
    || preg_match('/^\[([0-9a-f:]+)\]:(\d+)$/',$host, $regs))
  {
    if (@inet_pton($regs[1]) === FALSE)
      return FALSE;
    if (array_key_exists(2,$regs))
      $port=$regs[2];
    else
      $port='';
    return array(6,$regs[1],$port);
  }
  # Last resort, it is a hostname
  if (preg_match('/^([a-z0-9.-]+)(?::(\d+))?$/i', $host, $regs))
  {
    if (array_key_exists(2,$regs))
      $port=$regs[2];
    else
      $port='';
    $ip = get_dns_host($regs[1]);
    if ($ip === FALSE)
      return FALSE;
    if ( ($in_addr = @inet_pton($ip)) === FALSE)
      return FALSE;
    if (strlen($in_addr) == 16)
      return array(6,$ip,$port);
    if (strlen($in_addr) == 4)
      return array(4,$ip,$port);
    return FALSE;
  }
  return FALSE;
}

function get_dns_host($hostname)
{
  for ($i=0 ; $i<3 ; $i++)
  {
    $dns_records = dns_get_record($hostname);
    if (sizeof($dns_records) == 0)
      return FALSE;
    foreach ($dns_records as $dns)
    {
      switch ($dns['type'])
      {
      case 'CNAME':
        unset($ipv6);
        $hostname = $dns['target'];
        break 2;
      case 'A':
        return $dns['ip']; // prefer IPv4 over IPv6 for now
      case 'AAAA':
        $ipv6 = $dns['ipv6'];
      }
    }
    if (isset($ipv6))
      return $ipv6;
  }
  return FALSE;
}
function is_ipv6($addr)
{
  $net_addr = @inet_pton($addr);
  if ($net_addr === FALSE || strlen($net_addr) < 16)
    return FALSE;
  return TRUE;
}

function print_version()
{
  print 'JFFNMS version '.JFFNMS_VERSION.'
  Copyright (C) 2002-2011 JFFNMS Authors

  JFFNMS comes with ABSOLUTELY NO WARRANTY.
  This is free software, you can redistribute it and/or modify it under
  the terms of the GNU General Public License; either version 2 of the
  License or (at your option) any later version.
  For more information about these matters, see the files named COPYING
  that should be distributed along with this software.
';
  die;
}
?>
