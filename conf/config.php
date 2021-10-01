<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2012 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
#error_reporting(E_ALL | E_STRICT);

if (function_exists('snmp_set_output_format'))
    snmp_set_output_format(SNMP_OID_OUTPUT_NUMERIC);
if (function_exists('snmp_set_quick_print'))
    snmp_set_quick_print(1);
define('JFFNMS_VERSION', '0.9.4');

class JffnmsConfig
{
  public $config_dir;
  private $configs = array();
  public $default_configs = array();

  function __construct($config_dir=FALSE)
  {
    $default_filename = 'jffnms.conf.defaults';
    $config_filename = 'jffnms.conf';
    if ( ($this->config_dir = $this->_find_config_dir($default_filename, $config_dir)) === FALSE)
      die("Cannot find configuration default file \"$default_filename\".");
    $this->_parse_defaults_file($this->config_dir.'/'.$default_filename);
    $this->_parse_config_file($this->config_dir.'/'.$config_filename);
    $this->_check_configuration();

  }

  function get($key)
  {
    if (!array_key_exists($key, $this->configs))
      return FALSE;
    return $this->configs[$key];
  }

  function set($key, $value)
  {
    $this->configs[$key] = $value;
  }

  function save()
  {
    $new_config_name = $this->config_dir . '/jffnms.conf.'. date('Y-m-d',time());
    $config_file = $this->config_dir.'/jffnms.conf';

    $new_config = '';
    foreach($this->configs as $key => $value)
    {
        if ($key == 'logging_file')
            continue;
        if (!array_key_exists($key, $this->default_configs) ||
            !array_key_exists('type', $this->default_configs[$key]))
            $config_type='plain';
        else
            $config_type = $this->default_configs[$key]['type'];
        switch($config_type)
        {
        case 'db':
        case 'menu':
        case 'phpmodule':
        case 'phpconf':
            break; # this lot dont get saved
        default:
            if (!array_key_exists($key, $this->default_configs) ||
                !array_key_exists('default', $this->default_configs[$key]) ||
                $value != $this->default_configs[$key]['default'])
                $new_config .= "$key = $value\n";
        }
    }
    if ( ($fp = fopen($new_config_name, 'w+', FALSE)) === FALSE)
      die("Cannot open new config file '$new_config_name'");
    fputs($fp, $new_config);
    fclose($fp);

    if (file_exists($new_config_name))
      copy($new_config_name, $config_file);
  }


  private function _find_config_dir($filename, $force_dir=FALSE)
  {
      if ($force_dir !== FALSE) {
          $config_dirs = array($force_dir);
      } else {
          $config_dirs = array('..','.','../conf','../../../conf','../../conf','/etc/jffnms');
      }
     foreach ($config_dirs as $dir)
       if (file_exists($dir.'/'.$filename))
         return $dir;
     return FALSE;
  }

  private function _parse_defaults_file($filename)
  {
    if ( ($fp = fopen($filename, 'r', FALSE)) === FALSE)
      die ("Cannot open default config file '$filename'");
    while ($fp != feof($fp))
    {
      $line = fgets($fp);
      if (preg_match('/^([a-z0-9_]+):([a-z]+)\s*=\s*(\S.*)$/i', $line, $regs))
      {
        $value = trim($regs[3]);
        $this->default_configs["$regs[1]"]["$regs[2]"] = $value;
        if ($regs[2] == 'default')
          $this->configs["$regs[1]"] = $value;
      }
    }
    fclose($fp);
  } # _parse_defaults_file()

  private function _parse_config_file($filename)
  {
    if (!is_readable($filename))
        return;
    if ( ($fp = fopen($filename, 'r', FALSE)) === FALSE)
        return;
    while ($fp != feof($fp))
    {
      $line = fgets($fp);
      if (preg_match('/^([a-z_]+)\s*=\s*(.*)$/i', $line, $regs))
      {
        $value = trim($regs[2]);
        $this->configs["$regs[1]"] = $value;
      }
    }
    fclose($fp);
  } # _parse_config_file()

  private function _check_configuration()
  {
    $jffnms_setup_page = '/admin/setup.php';

    if ($this->get('access_method') === FALSE)
      $this->set('access_method', 'local');

    if ($this->get('jffnms_configured') !=1)
    {
      // Not yet configured and we are running on a website
      if (PHP_SAPI != 'cli' && strpos($_SERVER['REQUEST_URI'],$jffnms_setup_page) === false)
      {
        $jffnms_rel_path = str_replace($jffnms_setup_page,"",$_SERVER['REQUEST_URI']);
        //we are not in the setup page
        if ($_SERVER['HTTPS']) {
          $jffnms_setup_location = 'https://'.$_SERVER['HTTP_HOST'].str_replace('//','/',$jffnms_rel_path.$jffnms_setup_page);
        } else {
          $jffnms_setup_location = 'http://'.$_SERVER['HTTP_HOST'].str_replace('//','/',$jffnms_rel_path.$jffnms_setup_page);
      }
        header('Location: '.$jffnms_setup_location);			//redirect to setup
        die();    
      } else {
        // Help setup with its real path
        $this->set('jffnms_real_path', str_replace('/conf', '', str_replace('\conf', '', dirname(__FILE__)))); 
        $this->set('jffnms_rel_path', str_replace($jffnms_setup_page, '', $_SERVER['PHP_SELF']));
      }
    }
  } # check_configuration()

}

function config_load_libs($include_type = 'gui', $jffnms_init_classes=0)
{
  global $Config, $Sanitizer;

  $Config->set('logging_file', $_SERVER['SCRIPT_NAME']);
  
  // everything except none gets the following
  $jffnms_includes = array('api', 'api.network', 'api.db', 'api.classes' );
  switch ($include_type)
  {
  case 'none':
    return;
    break;
  case 'basic':
    break;
  case 'gui':
    $jffnms_includes = array_merge($jffnms_includes, array(
      'gui', 'gui.toolkit', 'gui.admin', 'gui.controls'));
  }//case

	//Include the Lib Files.
  #$error_level = error_reporting(E_ERROR | E_WARNING | E_PARSE);
  $jffnms_real_path = $Config->get('jffnms_real_path');
  foreach ($jffnms_includes as $jffnms_include)
  {
    require_once("$jffnms_real_path/lib/$jffnms_include.inc.php");
  }
  #error_reporting($error_level);
	
  if ($include_type == 'gui')
  {
    $Sanitizer = new Sanitizer;
	}
}

?>
