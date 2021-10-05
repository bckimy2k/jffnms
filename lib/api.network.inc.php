<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    // Frontend SNMP Functions for Current code.
    
define ('INCLUDE_OID_NONE', false);
define ('INCLUDE_OID_ALL', true);
define ('INCLUDE_OID_BASE', '10');
define ('INCLUDE_OID_1', '11');
define ('INCLUDE_OID_2', '12');
define ('INCLUDE_OID_3', '13');
define ('SNMP_TIMEOUT', 1000000); #1,000,000 usec = 1sec
    
/*
 * Check to see if snmp2_* functions exist
 * Really, if they don't, don't use snmp2
 */
if (function_exists('snmp2_walk'))
  define('USE_INTERNAL_SNMP2', TRUE);
else
  define('USE_INTERNAL_SNMP2', FALSE);

function parse_community($comm)
{
  if (empty($comm))
    return FALSE;

  if (preg_match('/^v(\d):(\S+)$/', $comm, $regs))
    return array(intval($regs[1]),$regs[2]);
  return (array(1,$comm));
}

function snmp_walk ($host, $comm, $oid, $include_oid = INCLUDE_OID_NONE, $retries = 2)
{
  list($version,$community)= parse_community($comm);
  $oid = parse_oid($oid, $version);

  switch($version)
  {
  case 1:
    if ($include_oid == INCLUDE_OID_NONE)
      $result = @snmpwalk($host, $community, $oid, SNMP_TIMEOUT, $retries);
    else
      $result = @snmprealwalk($host, $community, $oid, SNMP_TIMEOUT, $retries);
    break;
  case 2:
    if (USE_INTERNAL_SNMP2)
    {
      if ($include_oid == INCLUDE_OID_NONE)
        $result = @snmp2_walk($host, $community, $oid, SNMP_TIMEOUT, $retries);
      else
        $result = @snmp2_real_walk($host, $community, $oid, SNMP_TIMEOUT, $retries);
    } else
        $result = jffnms_snmp2_walk($host, $community, $oid, SNMP_TIMEOUT, $retries, $include_oid);
    break;
  case 3:
     list ($sec_name, $sec_level, $auth_proto, $auth_key, $priv_proto, $priv_key) = explode('|', $community);
    if ($include_oid == INCLUDE_OID_NONE)
      $result = @snmp3_walk($host, $sec_name, $sec_level, $auth_proto,
       $auth_key, $priv_proto, $priv_key, $oid, SNMP_TIMEOUT, $retries);
    else
      $result = @snmp3_real_walk($host, $sec_name, $sec_level, $auth_proto,
       $auth_key, $priv_proto, $priv_key, $oid, SNMP_TIMEOUT, $retries);
    break;
  } // switch
  if ($result === FALSE)
    return FALSE;

  if ($include_oid > INCLUDE_OID_BASE)
    $result = snmp_reduce_table_key ($result, $include_oid-INCLUDE_OID_BASE);
  $new_result = array();
  foreach  ($result as $oid => $value) {
      if (
          (strpos($value, 'No Such Instance') !== FALSE) or
          (strpos($value, 'No Such Object') !== FALSE) or
          (strpos($value, 'No more variables left') !== FALSE) )
          continue;
      $new_result[$oid] = snmp_fix_value($value);
  }
  return $new_result;
}

function snmp_get ($host, $comm, $oid, $retries = 2)
{ 
  list($version,$community)= parse_community($comm);
  $oid = parse_oid($oid, $version);
  $result = FALSE;

  switch($version)
  {
  case 1:
    $result = @snmpget($host, $community, $oid, SNMP_TIMEOUT, $retries);
    break;
  case 2:
    if (USE_INTERNAL_SNMP2)
      $result = @snmp2_get($host, $community, $oid, SNMP_TIMEOUT, $retries);
    else
      $result = jffnms_snmp2_get($host, $community, $oid, SNMP_TIMEOUT, $retries);
    break;
  case 3:
     list ($sec_name, $sec_level, $auth_proto, $auth_key, $priv_proto, $priv_key) = explode('|', $community);
    $result = @snmp3_get($host, $sec_name, $sec_level, $auth_proto,
     $auth_key, $priv_proto, $priv_key, $oid, SNMP_TIMEOUT, $retries);
    break;
  } // switch
  if (
      (strpos($result, 'No Such Instance') !== FALSE) or
      (strpos($result, 'No Such Object') !== FALSE) or
      (strpos($result, 'No more variables left') !== FALSE) )
    return FALSE;
  return snmp_fix_value($result);
}
    
function snmp_set ($host, $comm, $oid, $type, $value, $retries = 2)
{ 
  list($version,$community)= parse_community($comm);
  $oid = parse_oid($oid, $version);
  $result = FALSE;

  switch($version)
  {
  case 1:
    $result = @snmpset($host, $community, $oid, $type, $value, SNMP_TIMEOUT, $retries);
    break;
  case 2:
    if (USE_INTERNAL_SNMP2)
      $result = @snmp2_set($host, $community, $oid, $type, $value, SNMP_TIMEOUT, $retries);
    else
      $result = @snmp2_set($host, $community, $oid, $type, $value, SNMP_TIMEOUT, $retries);
    break;
  case 3:
     list ($sec_name, $sec_level, $auth_proto, $auth_key, $priv_proto, $priv_key) = explode('|', $community);
    $result = @snmp3_set($host, $sec_name, $sec_level, $auth_proto,
     $auth_key, $priv_proto, $priv_key, $oid, $type, $value, SNMP_TIMEOUT, $retries);
    break;
  } // switch
  return $result;
}

function get_snmp_counter ($ip,$community,$oid)
{
  $aux = explode(":",@snmp_get($ip,$community,$oid));
  $result = (count($aux)==1)?$aux[0]:$aux[1];
  return $result;
}
    
/*
 * parse_oid()
 * Returns different OID depending if it is v1 or not
 */
function parse_oid ($oid, $version = 1)
{
  if (!preg_match('/^([^,]+),(.+)$/', $oid, $regs))
    return $oid;
  if ($version == 1)
    return $regs[1];
  return $regs[2];
}

function snmp_fix_value ($value)
{
    if ($value === FALSE)
        return FALSE;
    $value = preg_replace('/(counter32|counter64|gauge|gauge32|gauge64|hex|ipaddress|integer|string):/i', '', $value);
    if ($value == '')
        return '';
    if (is_numeric($value))
        return trim($value);

    // Remove quotes and other delimiters
    $value = str_replace(array("\"","'",">","<","\\","\n","\r"),'', $value);
    return trim($value);
}
    
function snmp_reduce_table_key ($table, $important = 1)
{
  if (!is_array($table) || count($table) == 0)
    return $table;

  $new_table = array();
  foreach($table as $oid => $value)
  {
    $new_oid = join('.',array_slice(explode('.', $oid),-($important)));
    $new_table[$new_oid] = $value;
  }
  return $new_table;    
}

function snmp_hex_to_string ($hex)
{
  $value = trim($hex);
  if (substr($value,0,3)=='Hex')
  { 
    $data = substr($value,4,strlen($value)-5);

    for ($i=0; $i < strlen($data) ; $i++) 
      if (ord($data[$i])==10) $data[$i]=' ';
      $data_array = explode(' ',$data);
      $value = '';
      foreach ($data_array as $aux)
        if ($aux!='00')
          $value .= chr(hexdec($aux));
  }
  return $value;
}


//MISC
//--------------------------------------------------------

    function http_post_message ($url,$vars,$raw_data = "", $debug = 0,$comment = NULL, $HTTP_MODE = "1.0") {

  //unset($comment);    
  //if ($comment) $url.="?method=".$vars[method]."&from=$comment";

  $proto = 'http://';
  $host = '';
  $port = 80;
  $path = '';

  if (preg_match('/^(https?:\/\/)(.*)$/i', $url, $regs))
  {
    $proto = $regs[1];
    $url2 = $regs[2];
  } else 
    $url2 = $url;
  if (preg_match('/^([a-z0-9.-]+)(:\d)?(\/.*)/i', $url2, $regs))
  {
    $host = $regs[1];
    if (isset($regs[3]))
    {
      $port = $regs[2];
      $path = $regs[3];
    } else {
      $path = $regs[2];
    }
  }
  if ($port=='') $port=80;
  if ($proto=='https://')
  {
    $host = 'ssl://'.$host;
    $port = 443;
  }
  
  $user_agent = "JFFNMS";

  $urlencoded = "";
  foreach($vars as $key => $value)
      if (!is_array($value))
    $urlencoded.= "$key=$value&";
      else 
    $urlencoded.= "$key=".str_replace("&","%26",satellize($value))."&";


  $urlencoded = substr($urlencoded,0,-1);  

  $content_length = strlen($urlencoded);

  //Changed to HTTP/1.0 without KeepAlive
  $headers =  "POST $path HTTP/".$HTTP_MODE."\r\n".
      "Host: $host\r\n".
      (($HTTP_MODE=="1.1")?"Connection: close\r\n":"").
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "User-Agent: $user_agent\r\n".
      "Content-Length: $content_length\r\n".
      "\r\n$raw_data";
  
  $fp = @fsockopen($host, $port, $errno, $errstr,4);
  if (!$fp) return false;

  $time_send = time_msec();

  $a = fputs($fp, $headers.$urlencoded);
  
  $time_send = time_msec_diff($time_send);
  if ($debug == 1) echo "time send: $time_send \n";
  
  if ($debug == 2) var_dump($headers.$urlencoded);

  $time_recv = time_msec();

  $ret = "";
  while (!feof($fp))
    $ret.= fgets($fp, 1024);
      fclose($fp);

  $time_recv = time_msec_diff($time_recv);
  if ($debug == 1) echo "time recv: $time_recv \n";

  $init = strpos($ret,"\r\n\r\n");
  $data1 = substr($ret,$init+4,strlen($ret)-$init);
  
  if ($HTTP_MODE=="1.1") {
      $init = strpos($data1,"\r\n")+2;
      $len = strrpos($data1,"\r\n")-$init-4;
  } else {
      $init = strpos($data1,"\r\n");
      $len = strlen($data1)-$init;
  }  
  $data = trim(substr($data1,$init,$len));

  return $data;
    }

    function https_post_message ($url,$vars,$raw_data = "", $debug = 0,$comment = NULL) {

        unset($comment);
        if ($comment) $url.="?method=".$vars["method"]."&from=$comment";

        $user_agent = "JFFNMS";

        $urlencoded = "";
        foreach($vars as $key =>  $value)
            if (!is_array($value))
                $urlencoded.= "$key=$value&";
            else
                $urlencoded.= "$key=".str_replace("&","%26",satellize($value))."&";

        $urlencoded = substr($urlencoded,0,-1);

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST,  2);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER,  0);
        curl_setopt ($ch,CURLOPT_POST,1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS,$urlencoded);

        $data = curl_exec ($ch);
        curl_close ($ch);

        return $data;
    }

    function soap_post_message ($url, $vars, $debug = 0,$comment = NULL) {
  
  require_once "SOAP/Client.php";

  $url = str_replace("soap://","http://",$url);
      $options = array('namespace' => 'urn:JFFNMS', 'trace' => 1);
  $soapclient = new SOAP_Client("$url?capabilities=O");
  
  $method = $vars["method"];
  unset($vars["method"]);
  unset($vars["capabilities"]);
  $ret = $soapclient->call($method,$vars,$options);
  unset($soapclient);
  
  $ret = object2array($ret);

  if (is_array($ret["item"])) {
      $aux = $ret["item"];
      unset ($ret["item"]);
      $ret = array_merge ($ret,$aux);
  }
  
  return $ret;
    }

    function wddx_post_message ($url,$vars,$raw_data = "", $debug = 0,$comment = NULL) {

  preg_match("/^(.*:\/\/)?([^:\/]+):?([0-9]+)?(.*)/", $url,$match);
  list(,,$host,$port,$path) = $match;  
  if (!$port) $port = 5000; 

  $send = satellize ($vars,"W");  

  $fp = fsockopen($host, $port, $errno, $errstr);
  if (!$fp) return false;

  fputs($fp, $send);

  $ret = "";
  while (!feof($fp))
    $ret.= fgets($fp, 1024);
      fclose($fp);

  return $ret;
    }

    function satellite_query ($satellite_url,$message,$comment = NULL,$debug = 0) {
  //debug = 1, debug the transport
  //debug = 2, debug the RAW reply
  //debug = 4, debug the reply
  //debug = 8, return Result and Raw Result
    
  if (!$message["capabilities"] && ($message["session"]=="get")) //set capabilities when establishing a session
      $message["capabilities"] = unsatellize();
    
  $message["from_sat_id"]=$GLOBALS["my_sat_id"]; //add my sat id as from
  
  preg_match("/^(.*:\/\/)?([^:\/]+):?([0-9]+)?(.*)/", $satellite_url,$match);
  $proto = $match[1];

  $result_raw = NULL;    

  switch ($proto) {
      
      case "https://" :   $result_raw = https_post_message ($satellite_url,$message,"",$debug,$comment);
        break;

      case "http://" :   $result_raw = http_post_message ($satellite_url,$message,"",$debug,$comment);
        break;

      case "soap://" :   $result = soap_post_message ($satellite_url,$message,$debug,$comment); //no need to unsatellize
        break;

      case "wddx://" :   $result_raw = wddx_post_message ($satellite_url,$message,$debug,$comment);
        break;

  }

  if ($debug==2) var_dump($result_raw);
  
  if ($result_raw) $result = unsatellize($result_raw);

  if ($debug==4) var_dump($result);
  
  if ($debug==8) $result = array($result,$result_raw);
  
  return $result;
    }
    
    function satellite_call ($sat_id, $class, $method, $params = NULL) {

        $sat_url = current(satellite_get ($sat_id));
        $sat_url = $sat_url["url"];
  
  if (!is_array($params)) $params = array ($params);
  
  $message = array(
      "sat_id"=>$sat_id,
      "class"=>$class,
      "method"=>$method,
      "params"=>$params
  );
  
  $result = satellite_query ($sat_url, $message);
  
  return $result;
    }

function get_ip_peer($ip)
{
  $octets = explode('.', $ip);
  if ($octets[3]%2)
    $octets[3]++;
  else
    $octets[3]--;
  return implode('.', $octets);
}

//Process MIB Status Values    
function parse_interface_status($status)
{
  $statuses = array('1' => 'up', '2' => 'down', '3' => 'testing');

  if (array_key_exists($status, $statuses))
    return $statuses[$status];

  if ( ($trim_status = strstr($status, '(', TRUE)) !== FALSE)
    return $trim_status;
  return $status;
}

##############################################################
#
# External snmp functions
#
if (!USE_INTERNAL_SNMP2)
{
function jffnms_snmp2_walk($host, $community, $oid, $timeout, $retries, $include_oid)
{
  if ($include_oid)
    $outopt = '-Oqn';
  else
    $outopt = '-Oqv';

  $command = "snmpwalk -v2c $outopt -c $community -t $timeout -r $retries $host $oid";
    
  @exec($func, $result, $aux);

  if ((count($result)==1) && 
    (preg_match("/No Such (Object|Instance)/i", current($result)) ||
     preg_match("/No more variables left/i", current($result))))
    return FALSE;

  if ($include_oid)
  {
    $new_result = array();
    foreach($result as $key => $line)
    {
      if (preg_match('/^([0-9.]+)\s+(\S.+)/', $line, $regs))
        $new_result[$regs[1]] = $regs[2];
    }
    return $new_result;
  }
  return $result;
}

function jffnms_snmp2_get($host, $community, $oid, $timeout, $retries)
{
  $command = "snmpget -v2c -Oqv -c $community -t $timeout -r $retries $host $oid";
    
  @exec($func, $result, $aux);

  if ((count($result)==1) && 
    (preg_match("/No Such (Object|Instance)/i", current($result)) ||
     preg_match("/No more variables left/i", current($result))))
    return FALSE;
  return $result;
}

function snmp2_set($host, $community, $oid, $type, $value, $timeout, $retries)
{
  $command = "snmpset -v2c -c $community -t $timeout -r $retries $host $oid $type $value";
    
  @exec($func, $result, $aux);

  if ((count($result)==1) && 
    (preg_match("/No Such (Object|Instance)/i", current($result)) ||
     preg_match("/No more variables left/i", current($result))))
    return FALSE;
  return $result;
}
} //use_internal_snmp2
?>
