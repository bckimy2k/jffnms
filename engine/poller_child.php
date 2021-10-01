<?php
/*
 * poller_child : The child process that does the real polling
 */
require_once 'common_child.inc.php';
$poller_buffer = array();

$Child = new JffnmsEngineChild($argv);
poller_child_main_loop();

function poller_child_main_loop()
{
    global $Child;
    $old_time = 0;

    while(TRUE) {
      $read_fds = array($Child->stdin);
      $write_fds = NULL;
      $except_fds = NULL;

      $nr = stream_select($read_fds, $write_fds, $except_fds, 1);
      if ($nr === FALSE) {
          $Child->send_error("** FATAL ** Child process had error on select.");
          die();
      }
      if ($nr == 0) {
        $old_time = $Child->check_heartbeat($old_time);
        continue;
      }

      $line = fgets($Child->stdin,4096);
      $params = unserialize($line);
      if (!is_array($params)) {
          $Child->send_error("Received bad line from parent, not an array");
          continue;
      }
      if (!array_key_exists('cmd', $params)) {
          $Child->send_error("Recieved array from parent with no cmd field.");
          continue;
      }
      switch($params['cmd']) {
      case 'DIE':
          die();
          break;
      case 'POLL':
          if (child_poll_interface($params) === TRUE)
              $Child->send_array('DONE');
          else
              $Child->send_array('NOTDONE');
          break;
      default:
          $Child->send_error("Received unknown command '".$params['cmd']."'");
      }//switch cmd
      $old_time = $Child->check_heartbeat($old_time);
    }
}

function child_poll_interface($params)
{
  global $Config, $Child, $poller_buffer;

  $host_id = $params['host_id'];
  if (!is_numeric($host_id)) {
      $Child->send_error("Host ID must be an integer, given \'$host_id\'");
      return FALSE;
  }

  $interface_id = $params['interface_id'];
  if (!is_numeric($interface_id)) {
      $Child->send_error("Interface ID must be an integer, given \'$interface_id\'");
      return FALSE;
  }
  $poller_group = $params['poller_group'];
  if (!is_numeric($poller_group)) {
      $Child->send_error("Poller Group must be an integer, given \'$poller_group\'");
      return FALSE;
  }
  // Poller Group 1 is no polling so return immediately
  if ($poller_group == 1)
      return TRUE;
  $check_status = $params['check_status'];
  $poller_group_query = 'SELECT
    pollers.name AS poller_name, pollers.command AS poller_command,
    pollers.parameters AS poller_parameters,
    pollers_backend.command AS backend_command,
    pollers_backend.parameters AS backend_parameters,
    pollers_poller_groups.pos AS poller_pos
    FROM
      pollers, pollers_backend, pollers_poller_groups
    WHERE
      pollers_poller_groups.poller_group = '.$poller_group.'
      AND pollers_poller_groups.poller = pollers.id
      AND pollers_poller_groups.backend = pollers_backend.id
      '.($check_status==0?' AND pollers_backend.type != 1':'').'
    ORDER BY
      pollers_poller_groups.pos';
  //fwrite(STDERR, $poller_query);
  if ( ($poller_group_result = db_query($poller_group_query)) === FALSE) {
      $Child->send_error('Query failed - poller_plan - '.db_error());
      return FALSE;
  }
  $jffnms_real_path = $Config->get('jffnms_real_path');
  $poller_buffer=array();
  while ($poller_row = db_fetch_array($poller_group_result)) {
    $poller_command = $poller_row['poller_command'];
    $backend_command = $poller_row['backend_command'];
    $poller_filename = "$jffnms_real_path/engine/pollers/".$poller_row['poller_command'].'.php';
    $backend_filename = "$jffnms_real_path/engine/backends/".$poller_row['backend_command'].'.php';
    if ( $Child->require_file($poller_filename, 'poller_'.$poller_command) === FALSE)
        continue;
    if ( $Child->require_file($backend_filename, 'backend_'.$backend_command) === FALSE)
        continue;

    #  Calls poller, poller returns NULL if there is an error
    #  An errors means don't waste time running backend
    $poller_data = child_poller_data($interface_id, $poller_row, $params);
    $time_poller_query = time_msec();
    $poller_result = call_user_func_array('poller_'.$poller_command, array($poller_data));
    $time_poller_query = time_msec_diff($time_poller_query);

    if ($poller_result === FALSE) {
        $time_backend_query=0;
        $poller_result='(FALSE)';
        $backend_result='(not run)';
    } else {
        $time_backend_query = time_msec();
        $backend_result = call_user_func_array('backend_'.$backend_command, 
            array($poller_data, $poller_result));
        $time_backend_query = time_msec_diff($time_backend_query);
    }
      
    // Output the results
    $poller_param_description = '';
    if (array_key_exists('poller_parameters', $poller_row))
      $poller_param_description = $poller_row['poller_parameters'];
    if ( ($desc_len = strlen($poller_param_description)) > 10)
      $poller_param_description = substr($poller_param_description,0,4).'..'.
       substr($poller_param_description,$desc_len-4,4);
    $Child->logger(
      ' :  H '.str_pad($poller_data['host_id'],3,' ',STR_PAD_LEFT).
      ' :  I '.str_pad($poller_data['interface_id'],3,' ',STR_PAD_LEFT).
      ' :  P '.str_pad($poller_row['poller_pos'],3,' ',STR_PAD_LEFT).
      ' : '.(($backend_command=='buffer')?"$poller_command:":'').
      $poller_row['poller_name'].'('.$poller_param_description.
      "): $poller_result ".
      "-> $backend_command($poller_row[backend_parameters]): $backend_result".
      " (time P: $time_poller_query | B: $time_backend_query) ".
      "\n");
  } //while poller_row
  unset($poller_buffer);
  return TRUE;
}

function child_poller_data($interface_id, &$poller_data, &$host_data)
{
  $Interfaces = new JffnmsInterfaces();
  $host_fields = array('interface', 'show_rootmap', 'rw_community', 'ro_community',
    'host_id', 'host_ip');

  $interface_values = $Interfaces->values($interface_id);
  $values = current($interface_values['values']);
  foreach ($host_fields as $field)
    $values[$field] = $host_data[$field];

  if (strpos($poller_data['poller_parameters'], '<') !== FALSE)
  {
    $replace_values = array_merge($host_data, $poller_data, $values);
    foreach($replace_values as $field => $value)
      $poller_data['poller_parameters'] = str_replace("<$field>", $value, $poller_data['poller_parameters']);
  }
  $values['interface_id'] = $interface_id;
  $values['random'] = rand(10,99);
  return array_merge($values,$poller_data);
}

function poller_child_poll($host_id, $interface_id, $poller_pos, $itype, $output=TRUE)
{
  global $Config, $Child;
  $loaded_pollers = array();
  $loaded_backends = array();
  $old_time = time();
  $time_start = time();
  //debug(array('cmd'=>'START','host'=>$host_id));

  // input validation done at parent
  $poller_plan_filter = array('interface'=>$interface_id,'host'=>$host_id,
    'pos'=>$poller_pos,'type'=>$itype);
  $poller_plan_result = poller_plan ($poller_plan_filter); // Get the Poller Plan (things to poll)

  if ($output)
    $Child->logger(" :  H ".str_pad($host_id,3," ",STR_PAD_LEFT)." : Poller Start : ".$poller_plan_result["items"]." Items.\n"); 
  else
    ob_start();

  // Loop through all the polling for this host
  while ($poller_data = poller_plan_next($poller_plan_result))
  {
    $poller_command = $poller_data['poller_command'];
    $backend_command = $backend_data['backend_command'];
    $poller_filename = $Config->get('jffnms_real_path')."engine/pollers/$poller_command.php";
    $backend_filename = $Config->get('jffnms_real_path')."engine/backends/$backend_command.php";
    $poller_function = "poller_$poller_command";
    $backend_function = "backend_$backend_command";

    if (!array_key_exists($poller_command, $loaded_pollers))
    {
      if ($loaded_pollers[$poller_command] == FALSE) // already failed
        continue;
      if (! is_readable($poller_filename))
      {
        $Child->send_error("Poller plugin '$poller_filename' does not exist or is not readable.");
        $loaded_pollers[$poller_command] = FALSE;
        continue;
      }
      require($poller_filename);
      if (!function_exists($poller_function))
      {
        $Child->send_error("Poller function '$poller()' was not found in poller plugin '$poller_filename'.");
        $loaded_pollers[$poller_command] = FALSE;
      }
      $loaded_pollers[$poller_command] = TRUE;
    }

    // Load and check the backend, if not done already
    if (!array_key_exists($backend_command, $loaded_backends))
    {
      if ($loaded_backends[$backend_command] == FALSE) // already failed
        continue;
      if (! is_readable($backend_filename))
      {
        $Child->send_error("backend plugin '$backend_filename' does not exist or is not readable.");
        $loaded_backends[$backend_command] = FALSE;
        continue;
      }
      require($backend_filename);
      if (!function_exists($backend_function))
      {
        $Child->send_error("backend function '$backend()' was not found in backend plugin '$backend_filename'.");
        $loaded_backends[$backend_command] = FALSE;
      }
      $loaded_backends[$backend_command] = TRUE;
    }

    // Time and run the poller
    $time_poller_query = time_msec();
    $poller_result = call_user_func_array($poller_function, $poller_data);
    $time_poller_query = time_msec_diff($time_poller_query);

    // Time and run the backend
    $time_backend_query = time_msec();
    $backend_result = call_user_func_array($backend_function, array($poller_data, $poller_result));
    $time_backend_query = time_msec_diff($time_backend_query);
    $items_ok++;

    if ($output)
    {
      //Cut the Poller Parameters String
      $poller_param_description = isset($poller_data['poller_parameters'])?$poller_data['poller_parameters']:'';
      if ($aux = strlen($poller_param_description) > 10) 
        $poller_param_description = substr($poller_param_description,0,4)."..".substr($poller_param_description,strlen($aux)-4,4); 

      $Child->logger( " :  H ".str_pad($poller_data["host_id"],3," ",STR_PAD_LEFT).
        " :  I ".str_pad($poller_data["interface_id"],3," ",STR_PAD_LEFT).
        " :  P ".str_pad($poller_data["poller_pos"],3," ",STR_PAD_LEFT).
        " : ".(($backend_command=="buffer")?"$poller_command:":"").$poller_data["poller_name"]."(".$poller_param_description."): $poller_result ".
        "-> $backend_command(".$poller_row["backend_parameters"]."): $backend_result".
        " (time P: $time_poller_query | B: $time_backend_query) ".
        "\n");
    } else {
      ob_end_clean();
      $old_time = $Child->check_heartbeat($old_time);
    }
  } // while
  $polling_time = round(time_msec_diff($time_start));
  if ($output)
    $Child->logger(" :  H ".str_pad($host_id,3," ",STR_PAD_LEFT)." : Poller End, Total Time: $polling_time msec.\n");
  else
    ob_end_clean();

  adm_hosts_update($host_id,array('last_poll_date'=>time(), 'last_poll_time'=>round($polling_time/1000))); 
  echo serialize(array('cmd'=>'OK', 'items'=>$poller_plan_result['items'], 'items_ok'=>$items_ok, 'time'=>$polling_time))."\n";
}

function child_get_interface_args($int_id)
{
  $int_query = '
    SELECT
      interfaces.id AS interface_id, interfaces.interface, 
      interfaces.poll AS poller_group,
      hosts.rwcommunity hosts.rocommunity,
      hosts.id AS host_id, hosts.ip AS host_ip
    FROM 
      interfaces, hosts 
    WHERE 
      interfaces.id = '.$int_id.'
    AND hosts.id = interfaces.host';
}

?>
