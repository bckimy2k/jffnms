<?php
/* Setup & Autoconfiguration. This file is part of JFFNMS
 * Copyright (C) <2002-2004> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) <2002> Robert Bogdon
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
    
define ('RESULT_OK', 1);
define ('RESULT_ERROR', 2);
define ('RESULT_YES', 3);
define ('RESULT_NO', 4);

function searchPath($cmd)
{  
  global $Config;

  $dirs = explode(":", $_SERVER["PATH"]);

  if ($Config->get('os_type') =='windows')
  {
    $cmd.='.exe';
    $dirs[]='c:/php';
    $dirs[]='c:/rrdtool';
    $dirs[]=$Config->get('jffnms_real_path');
  }

  foreach ($dirs as $thisdir)
    if (is_file($thisdir .'/'. $cmd))
      return $thisdir .'/'. $cmd;
  return FALSE;
}
   
function autoConfig($option, $value)
{
  global $Config;
  $real_path = '/opt/jffnms';
  if (preg_match('/^(.*)\/admin\/setup.php/', $_SERVER['SCRIPT_FILENAME'], $regs))
    $real_path = $regs[1];
  $rel_path = '/jffnms';
  if (preg_match('/^(.*)\/admin\/setup.php/', $_SERVER['REQUEST_URI'], $regs))
    $rel_path = $regs[1];

  switch($option)
  {
  case 'jffnms_real_path':
    $value = $real_path;
    break;
  case 'tftp_real_path':
    $value = $real_path . '/tftpd';
    break;
  case 'rrd_real_path':
    $value = $real_path . '/rrd';
    break;
  case 'engine_temp_path':
    $value = $real_path . '/engine/temp';
    break;
  case 'images_real_path':
    $value = $real_path . '/htdocs/images/temp';
    break;
  case 'images_rel_path':
    $value = $rel_path.'/images/temp';
    break;
  case 'log_path':
    $value = $real_path . '/logs';
    break;
  case 'jffnms_rel_path':
    $value = $rel_path;
    break;
  case 'jffnms_satellite_uri':
    $value = current_host().$_SERVER['REQUEST_URI'];
          $value = str_replace('?', '', $value);
          $value = str_replace('/admin/setup.php', '/admin/satellite.php', $value);
    break;
  case 'php_executable':
    $value = searchPath('php');
    if($value == false) $value = searchPath('php4');
    break;

  case 'neato_executable':
  case 'rrdtool_executable':
  case 'diff_executable':
  case 'nmap_executable':
  case 'fping_executable':
  case 'smsclient_executable':
    list ($file) = explode('_',$option);
    $value = searchPath($file);
    break;
  case 'os_type':
    $value = (strpos($_SERVER['SERVER_SOFTWARE'],'Win32') > 1)?'windows':'unix';
    break;
    
  case 'logo_image':
    $value = $rel_path.'images/jffnms.png';
    break;

  case 'rrdtool_version':
    $value = 'unknown';
    $rrd_exec = $Config->get('rrdtool_executable');
    if (is_executable($rrd_exec))
    {
      exec($rrd_exec.' -v', $output);
      if (preg_match('/rrdtool (\d+\.\d+)/i', $output[0], $regs))
      {
        $value = $regs[1];
      }
    }
    break;

  case 'rrdtool_font':
    $value = $real_path . '/engine/fonts/'.basename($value);
    break;
  }
    
  //TEST Fix for Windows Path \\\\\ escaping problem
  if ($value) $value = str_replace('\\','/',$value);

  return $value;  
}

function check_phpconf($value)
{
  return (ini_get($value)==1)?RESULT_YES:RESULT_NO;
}

function check_enum($value)
{
  return false;  //force Auto Config
}

function check_phpmodule($value)
{
  return (extension_loaded($value)?RESULT_YES:RESULT_NO);
}

function check_db($value)
{
  global $Config;

  return ($Config->get('jffnms_access_method') == 'local')
    ?($conexion = @db_test())?true:false
    :true;
}

    function check_disable($value) {
  return true;
    }

    function check_text($value) {
  return true;
    }

    function check_menu($value) {
  return true;
    }

    function check_bool($value) {
  return true;
    }

    function check_hidden($value) {
  return true;
    }

function check_relative_directory($value)
{
  return (@fopen(current_host() . $value . "/.check","r"))?true:false;
}

    function check_uri($test_url) {
  $new_test_url = (strpos($test_url,"://")===false)
    ?current_host()."/".$test_url."?from=/admin/setup.php"
    :$test_url;

      return (($test_url=="none") || (@fopen($new_test_url,"r")))?true:false;
    }

    function check_file($value) {
  return (is_file($value)?true:false);
    }

function check_directory($value)
{
  return (($value!="../..") && is_dir($value))?true:false;
}

    function check_satellite() {
        return FALSE;
    }
   
function verifyConfig($type, $key, $value)
{
  $old_value = $value;
  
  $state=0;
  if ($type == 'phpmodule' || $type == 'phpconf')
    return array($value, call_user_func('check_'.$type, $key));
  if ($type != 'label')
  {
    $result = call_user_func("check_".$type, $value);
    if ($result)
      $state = 1;
    else {
      $auto_config_value = autoConfig($key, $value);
      $result = call_user_func("check_".$type, $auto_config_value);
      if ($result || ($key=='os_type') || ($key=='rrdtool_version'))
      {
        $value = $auto_config_value;
        $state = 1;
      } else 
        $state = 2;
    }
  }
  return array($value, $state);
}

{
  $no_db=1;
  #require_once('../auth.php'); 
  require_once('../../conf/config.php');
  $Config = new JffnmsConfig();
  config_load_libs('gui',0);

  if ($Config->get('jffnms_initial_config_finished') ==1)
  { 
    require_once('../auth.php');
    if (($Config->get('jffnms_access_method') == 'local') && (db_test()))
      $no_db = 0; 
    if (!profile('ADMIN_SYSTEM')) die ('<H1> You dont have Permission to access this page.</H1></HTML>');
  }

  $action = $Sanitizer->get_string('action');
  $config_file = $Config->config_dir.'/jffnms.conf';

  if (!empty($action))
  {
    $new_config = array();
    
    foreach ($Config->default_configs as $config_key=>$default_data)
    {
        if (array_key_exists('type', $default_data))
            $default_data_type = $default_data['type'];
        else
            $default_data_type = 'plain';
        $new = $Sanitizer->get_string('new_'.$config_key);
        if (($default_data_type=='bool') && $new===FALSE)
            $new = 0;
      if (($new==="") && ($default_data_type!="relative_directory")) //Only Relative directories can be empty
        $new = $default_data['default'];
      $Config->set($config_key,$new);
    }
    $Config->set('jffnms_configured',1);
    $Config->save ();
    unset ($new_config);
    unset ($key);
    unset ($new);
    unset ($data);
  
    //force configuration re-read
    #include('../../conf/config.php');
  }
  $setup_options = '';
  adm_header('Setup');
  foreach ($Config->default_configs as $key=>$data)
  {
    if (!isset($data['type'])) $data['type'] = 'text';
    $config_type = $data['type'];
  
    list($value, $state) = verifyConfig($data["type"], $key, $Config->get($key));
    $input = '&nbsp;';
    $new_key = 'new_'.$key;

    switch ($config_type)
    {
    case 'bool':
      $input = checkbox($new_key,$value);
      $state = 0;
      break;
    case 'enum':
      $options = array ();
      $enum_values = explode(';', $data['values']);
      foreach ($enum_values as $evalue)
      {
        list ($option_name, $option_value) = explode (':', $evalue);
        $options[$option_value]=$option_name;
      }
      $input = select_custom($new_key,$options,$value);
      $state = 0;
      break;
    case 'file':
      $state = (is_file($value) && is_readable($value))?1:2;
      $input = textbox ($new_key, $value,40);
      break;
    case 'hidden':
      $input = hidden($new_key, $value);
      break;
    case 'label':
      $input = $value .hidden($new_key, $value);
      $state = 0;
      break;
    case 'menu':
      $state = 0;
      break;
    case 'db':
      $state += 2;
      break;
    case 'text':
      $state = 0;
      /* fall through */
    case 'directory':
    case 'relative_directory':
    case 'uri':
    case 'satellite':
      $input = textbox ($new_key, $value,40);
      break;
    }
    switch ($state)
    {
    case RESULT_OK:
      $result[$key] = 'ok';
      break;
    case RESULT_ERROR:
      $result[$key] = 'error';
      break;
    case RESULT_YES:
      $result[$key] = 'yes';
      break;
    case RESULT_NO:
      $result[$key] = 'no';
      break;
    }
    if (empty($result[$key]))
      $output_result = '';
    else
      $output_result = td($result[$key], "result_".$result[$key]);
    switch ($config_type)
    {
    case 'hidden':
      $setup_options .= $input;
      break;
    case 'menu':
      $setup_options .= tr_open().
        td($data['description'], 'field_'.$config_type, 'field_'.$key, 3).
        $output_result.
        tag_close('tr');
      break;
    default:
      $setup_options .= tr_open().
        td($data['description'], 'field_'.$config_type, 'field_'.$key, 1).
        td($input, "value", "value_".$key, (empty($result[$key])?2:1)).
        $output_result.
        tag_close('tr');
      break;
    }
  }//foreach default_configs
  echo form(). table('setup');
    
  table_row('JFFNMS Setup','title');
  table_row(linktext('Main',$Config->get('jffnms_rel_path').'/').'&nbsp;'.linktext('Help',$Config->get('jffnms_site_help')),'help');
  table_row('Using '.(file_exists($Config->config_dir.'/jffnms.conf')?realpath($Config->config_dir.'/jffnms.conf'):'defaults'),'config_file');

  echo tr_open(). 
    td( table('options'). $setup_options. table_close() ,'setup_options').
  tag_close("tr");

  table_row(adm_form_submit('Save Changes','action'));

  echo table_close(). form_close();
  adm_footer();
}
?>
