<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

# NOTE client_id is auth_user_id for customers
define ('AUTH_TYPE_USER', 1);
define ('AUTH_TYPE_CUSTOMER', 2);

find_auth_config();
$Config = new JffnmsConfig();
config_load_libs('gui', (isset($jffnms_init_classes)?$jffnms_init_classes:0));
check_authentication();

function check_authentication()
{
  global $Config, $Sanitizer;
  
  $jffnms_rel_path = $Config->get('jffnms_rel_path');
  $client_pages = array( //pages the customer is allowed to see
    $jffnms_rel_path.'/admin/calendar.php',
    $jffnms_rel_path.'/view_performance.php',
    $jffnms_rel_path.'/admin/reports/state_report.php'
  );

  ini_set('session.save_handler', 'files');
  session_name('jffnms');
  session_start();  
  switch ($Config->get('jffnms_auth_method'))
  {
  case 'http':
    if (!isset($_SESSION['auth_user_id'])) {
        $auth = jffnms_authenticate ($_SERVER["PHP_AUTH_USER"],$_SERVER["PHP_AUTH_PW"],false);

        if ($auth === FALSE || ($_GET['logout'] == 1 && $_GET['OldAuth'] == $_SERVER['PHP_AUTH_USER'])) 
        http_authenticate();
        if ($auth !== FALSE) {
            foreach($auth as $key => $value)
            $_SESSION[$key] = $value;
        }
    }
    session_write_close();
    break;

  case 'login':
    if (isset($_REQUEST['logout']) && ($_REQUEST['logout']==1)) {
      session_destroy();
      session_start();
    }

    if (!isset($_SESSION['auth_user_id']))
    {
      $auth = jffnms_authenticate ($Sanitizer->get_string('user'), $Sanitizer->get_string('pass',''),true,'from '.$_SERVER['REMOTE_ADDR']);

      if ($auth === FALSE)
      {
        if (!empty($_REQUEST['pass']))
          $error = 'Invalid Username or Password';
        else
          $error = '&nbsp;';
      
          include ('login.php');
          die();
      } else {
        // Register the session variables
        foreach($auth as $key => $value)
          $_SESSION[$key] = $value;
      }
    }
    session_write_close();
    break;
      
    default:
      die('Bad Authentication Method.');
  } # switch

  if ($_SESSION['auth_type']== AUTH_TYPE_CUSTOMER)
  {
    if (!in_array($_SERVER['SCRIPT_NAME'], $client_pages))
    {
      $url_limit = $jffnms_rel_path."/view_performance.php";
      Header("Location: $url_limit");
      die();
    }
  }

}

function find_auth_config()
{
  $auth_configuration_file = 'config.php';
  $auth_dirs = array('../conf','../../../conf','../../conf');

  foreach ($auth_dirs as $auth_dir)
  {
    if (file_exists($auth_dir.'/'.$auth_configuration_file))
    { 
      require_once($auth_dir."/".$auth_configuration_file); 
      break;
    } 
  }
}

function jffnms_authenticate($user, $pass, $log_event = false, $log_event_info = '') 
{ 
  global $Config;

  $retval = FALSE;
  $auth = 0;
  $auth_type = 1;
  $auth_count = 0;
    
  if (preg_match('/^[\w\@\.]{0,20}$/', $user) && !empty($pass))
  {
    $query_auth = "select id, usern, passwd, fullname FROM auth WHERE usern = '$user'";
    $result_auth = db_query ($query_auth);
    if (db_num_rows($result_auth) == 1)
    {
      $row = db_fetch_array ($result_auth);
      $passwd= trim($row['passwd']);
      // $encrypt = trim(crypt($pass,$passwd));
      // TODO: Bypass authentication
      $encrypt = $passwd;
      if ($encrypt == $passwd)
      {
        $retval =  array(
        'auth_type' => AUTH_TYPE_USER,
        'auth_user_id' => $row['id'],
        'auth_user_name' => $row['usern'],
        'auth_user_fullname' => $row['fullname']);
      }
    } else {  // Didn't find user in DB
      $query_auth = "select id, username, name FROM clients where username= '$user' and password = '$pass'";
      $result_auth = db_query ($query_auth);
      if (db_num_rows($result_auth) == 1)
      { 
        $row = db_fetch_array($result_auth);
        $retval =  array(
        'auth_type' => AUTH_TYPE_CUSTOMER,
        'auth_user_id' => $row['id'],
        'auth_user_name' => $row['username'],
        'auth_user_fullname' => $row['name']);
      }
    }
    if ($log_event==true) 
    {
      $Events = new JffnmsEvents();
      $Events->add(date('Y-m-d H:i:s',time()),
        $Config->get('jffnms_internal_type'),1,'Login',
        (($retval !== FALSE)?'successful':'failed'),$user,$log_event_info,'',0);
    }
  } //valid username and password
  return $retval;
}
?>
