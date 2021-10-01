<?php

require_once 'common_child.inc.php';
jffnms_load_api('iad');

$Child = new JffnmsEngineChild($argv);
iad_child_main_loop();

function iad_child_main_loop()
{
    global $Child;
  $old_time = 0;

  while(TRUE)
  {
    //child_send_pong();
    $read_fds = array($Child->stdin);
    $write_fds = NULL;
    $except_fds = NULL;

    $nr = stream_select($read_fds, $write_fds, $except_fds, 1);
    if ($nr === FALSE)
    {
      $Child->send_error("Child process had error on select.");
      continue;
    }
    if ($nr == 0)
    {
      $old_time = $Child->check_heartbeat($old_time);
      continue;
    }

    $line = fgets($Child->stdin,4096);
    $params = unserialize($line);
    if (!is_array($params))
    {
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
      if (child_discover_interfaces($params) === TRUE)
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

function child_discover_interfaces($params)
{
  global $Config, $Child;
  $iad_time = time_msec();
  $Interfaces = new JffnmsInterfaces();
  $InterfaceTypesFields = new JffnmsInterface_types_fields();
  $Events = new JffnmsEvents();

  $ad_policy = iad_get_autodiscovery($params['autodiscovery_id']);
  $interface_types = iad_get_interface_types($params['itype']);
  if ($interface_types === FALSE)
    return FALSE;

  if ($params['sysobjectid'] == NULL && $params['rocommunity'] != '')
    $params['sysobjectid'] = update_sysobject_id($params['host_id'], 
    $params['host_ip'], $params['rocommunity']);
  // loop through itypes
  foreach($interface_types as $it_id => $itype)
  {
    discovery_logger($params['host_id'], $it_id, FALSE, 
        'Autodiscovering '.$itype['description']);

    // skip it if the sysobjectids don't match
    if ($itype['snmp_oid'] != NULL && $itype['snmp_oid'] != '')
    {
      if ($params['rocommunity'] == '' )
      {
        discovery_logger($params['host_id'], $it_id, FALSE, 
          "  Skipping: No host RO community to find OID $itype[snmp_oid]");
        continue;
      }
      if (sysobjectid_matches($params['sysobjectid'], $itype['snmp_oid']) == FALSE)
      {
        discovery_logger($params['host_id'], $it_id, FALSE, 
          "  Skipping: Host sysObjectId $params[sysobjectid] != $itype[snmp_oid]");
        continue;
      }
    }
    $db_interfaces = iad_interfaces_from_db($Interfaces, $params['host_id'], $it_id);
    $ad_interfaces = iad_interfaces_from_discovery(
      $itype['autodiscovery_function'], $params['host_ip'], 
      $params['rocommunity'], $params['host_id'],
      $itype['autodiscovery_parameters']);

     $fields = $InterfaceTypesFields->get_all (NULL,
        array('itype'=>$itype['id'],'exclude_types'=>20));
    if (is_array($ad_interfaces) && count($ad_interfaces) > 0)
    {
      $unique_interfaces = array_unique(array_merge(array_keys($ad_interfaces), array_keys($db_interfaces)));
      asort($unique_interfaces);
      reset($unique_interfaces);
      foreach($unique_interfaces as $key)
      {
        if ($key < 0)
          continue;
        $processed = FALSE;
	if (isset($db_interfaces[$key]))
		ksort($db_interfaces[$key]);
	if (isset($ad_interfaces[$key]))
		ksort($ad_interfaces[$key]);

        discovery_logger($params['host_id'], $itype['id'], $key,
          'DB  : '.(isset($db_interfaces[$key])?iad_show_interface($fields,$db_interfaces[$key]):'Not Found'));
        discovery_logger($params['host_id'], $itype['id'], $key,
          'HOST: '.(isset($ad_interfaces[$key])?iad_show_interface($fields,$ad_interfaces[$key]):'Not Found'));

        // Not in DB but in Host = add new
        if (!isset($db_interfaces[$key]) && isset($ad_interfaces[$key]))
        {
          if (iad_permit_new($ad_interfaces[$key], $ad_policy,$itype)) {
            $text = '';
            discovery_logger($params['host_id'], $itype['id'], $key,
              'RES : New Interface Found');
            //if the AD policy permits adding an interface.
            if ($ad_policy['permit_add']==1)
            {
              //if the AD policy says use default poller
              if ($ad_policy['poller_default']==1)
                ad_set_default ($ad_interfaces[$key]['poll'],$itype['autodiscovery_default_poller']); //use the interface type default poller for this new interface
              ad_set_default ($ad_interfaces[$key]['client'],$params['autodiscovery_default_customer']);
              ad_set_default ($ad_interfaces[$key]['sla'],$itype['sla_default']);
              //Find the Index Field
               foreach ($fields as $fdata)
                if ($fdata['ftype']==3)
                {
                  $index_field = $fdata['name'];
                  break;
                }
              //add the index field as data to use when adding a record
              $ad_interfaces[$key][$index_field]=$key;

                //delete the status fields, because they will not be found in the db
                unset ($ad_interfaces[$key]['admin']);
                unset ($ad_interfaces[$key]['oper']);

                $interface_id = $Interfaces->add(array('host'=>$params['host_id'],'type'=>$itype['id'])); //add new record
                $Interfaces->update($interface_id,$ad_interfaces[$key]); //update it with the data
                $text = '- Added';
              } // permit add
                $Events->add(date("Y-m-d H:i:s",time()), $Config->get('jffnms_administrative_type'), $params['host_id'], $ad_interfaces[$key]['interface'], //add informative event
                  'alert','autodiscovery',trim('Found '.$text),0); 
              $processed = 1;
            } // if allow add 
          continue;
        } // In Host not DB

        if (!isset($ad_interfaces[$key]) && isset($db_interfaces[$key])) {
            if ($itype['autodiscovery_validate']==1 && $db_interfaces[$key]['poll'] > 1) {
            discovery_logger($params['host_id'], $itype['id'], $key,
              'RES : Not Found in Host');
            $test='';
            if ($ad_policy['permit_del']==1 && $ad_policy['permit_disable']==0) {
                $Interfaces->del($db_interfaces[$key]['id']);
                $text = '- Deleted';
            }
            if ($ad_policy['permit_del']==0 && $ad_policy['permit_disable']==1) {
                $Interfaces->update($db_interfaces[$key]['id'],
                    array('poll'=>1, 'show_rootmap'=>2));
                $text = '- Disabled';
            }

            if ($ad_policy['alert_del']==1)
                $Events->add(date("Y-m-d H:i:s",time()), $Config->get('jffnms_administrative_type'), $params['host_id'], $db_interfaces[$key]['interface'], //add informative event
                'alert', 'autodiscovery', trim('Not Found in Host '.$text),0);
            }
            continue;
        }

        // Found in both and polling enabled
        if (isset($ad_interfaces[$key]) && isset($db_interfaces[$key])) {
            if ($itype['autodiscovery_validate']==1) {
                $fields_to_modify = array();
                $track_fields = array();

                $track_fields['interface']='Interface Name';
                foreach ($fields as $fdata)
                    if ($fdata['tracked']==1)
                        $track_fields[$fdata['name']] = $fdata['description'];

                foreach ($track_fields as $track_field=>$track_field_descr) {
                    if (!empty($ad_interfaces[$key][$track_field]) &&
                        (strncmp($ad_interfaces[$key][$track_field], $db_interfaces[$key][$track_field], 30) != 0)) {
                            discovery_logger($params['host_id'], $itype['id'], $key,
                                "RES : $track_field_descr changed from ". 
                                $db_interfaces[$key][$track_field].' to '.
                                $ad_interfaces[$key][$track_field]);
                            $fields_to_modify[$track_field]=$ad_interfaces[$key][$track_field];
                        } // fields not equal
                    if (count($fields_to_modify) > 0) {
                        $changed_fields = array();
                        foreach ($fields_to_modify as $field_name=>$field_value)
                            $changed_fields[] = $track_fields[$field_name].' to '.$field_value.' was '.$db_interfaces[$key][$field_name];
                        $changed_fields = join(' and ',$changed_fields);

                        if ($ad_policy['permit_mod']==1) {
                            $Interfaces->update($db_interfaces[$key]['id'],
                                $fields_to_modify);
                            if (array_key_exists('interface', $fields_to_modify))
                                $interface_name = $ad_interfaces[$key]['interface'];
                            else
                                $interface_name = $db_interfaces[$key]['interface'];
                            $event_comment = '- Changed '.$changed_fields;
                        } else {
                            $interface_name = $db_interfaces[$key]['interface'];
                            $event_comment = '- NOT Changed '.$changed_fields;
                        }
                        $Events->add(date("Y-m-d H:i:s",time()), $Config->get('jffnms_administrative_type'), $params['host_id'], $interface_name,
                'alert', 'autodiscovery', trim('detected modification '.$event_comment),0);
                    }
                }
            }

            // Check for interfaces with no client assigned
            if ($db_interfaces[$key]['client'] <= 1) {
                discovery_logger($params['host_id'], $itype['id'], $key,
                    'RES : No Customer Selected');
                $Events->add(date("Y-m-d H:i:s",time()), $Config->get('jffnms_administrative_type'), $params['host_id'], $db_interfaces[$key]['interface'],
                'alert', 'autodiscovery', 'Incomplete interface setup (Customer not selected)', 0);
                continue;
            }
        }
        discovery_logger($params['host_id'], $itype['id'], $key,
            'RES : Nothing done.');
      } // foreach unique interfaces
    } //ad_interfaces > 0
  } //interface types
  $iad_time = round(time_msec_diff($iad_time));
  $Child->logger('H '.str_pad($params['host_id'],3,' ',STR_PAD_LEFT)." : Autodiscovery took ".$iad_time." msec.\n");
  return TRUE;
}


function iad_get_autodiscovery($autodiscovery_id)
{
  static $autodiscovery_cache = array();

  if (array_key_exists($autodiscovery_id, $autodiscovery_cache))
    return $autodiscovery_cache[$autodiscovery_id];

  $iad_query = '
    SELECT
      poller_default, permit_add, permit_del, permit_mod, permit_disable,
      skip_loopback, check_state, check_address, alert_del
    FROM autodiscovery
    WHERE id = '.$autodiscovery_id;

  $iad_result = db_query($iad_query) or die ('Query failed - iad cache - '.db_error());
  if (!($iad_row = db_fetch_array($iad_result)))
    return FALSE;
  $autodiscovery_cache[$autodiscovery_id] = $iad_row;
  return $iad_row;
} //iad_get_autodiscovery()

function iad_get_interface_types($itype_id=FALSE)
{
  static $itypes_cache = FALSE;

  if (!is_array($itypes_cache))
  {
    //fill the cache
    $itype_query = '
      SELECT
        id, autodiscovery_validate, autodiscovery_function,
        autodiscovery_parameters, autodiscovery_default_poller,
        description, sla_default, snmp_oid
      FROM interface_types
      WHERE id > 1 AND
      autodiscovery_enabled = 1
      ORDER BY id';
    $itype_result = db_query($itype_query) or die ('Query failed - iad_get_interface_types() - '.db_error());
    while ($itype_row = db_fetch_array($itype_result))
    {
      $id = $itype_row['id'];
      $itypes_cache[$id] = $itype_row;
    }
  }//cache fill
  if (is_numeric($itype_id))
  {
    if (!array_key_exists($itype_id, $itypes_cache))
      return FALSE;
    return array($itype_id => $itypes_cache[$itype_id]);
  }
  return $itypes_cache;
} // iad_get_interface_types()

function discovery_logger($host, $itype, $intid, $result)
{
    global $Child;

    $Child->logger(
          'H '.str_pad($host,3,' ',STR_PAD_LEFT).' : '.
          'IT '.str_pad($itype,3,' ',STR_PAD_LEFT).' : '.
          ($intid?'I '.str_pad($intid,4,' ',STR_PAD_LEFT).' : ':'').
          "$result.\n");
}

function iad_show_interface ($fields,$data)
{
  $fields = array_merge(array(array('name'=>'interface', 'ftype'=>1, 'ftype_handler'=>'none')),$fields);  

  $text = array();   
  foreach ($fields as $fdata)
    if (isset($fdata['ftype']) && ($fdata['ftype']!=3) && isset($fdata['ftype_handler']) && ($fdata['ftype_handler']!='bool'))
    $text[] = substr($fdata['name'],0,4).': '.(isset($data[$fdata['name']])?$data[$fdata['name']]:'');
  $text = join(" | ",$text);
  return $text;
}

// Not in database but found in host - is it really supposed to be there?
function iad_permit_new(&$host_data, &$ad_policy, &$itype)
{
  if ($itype['autodiscovery_validate'] == 0)
    return TRUE;

  # If we skip looback and have a loopback address - dont add it
  if ( // Possibly check for loopback
	  $ad_policy['skip_loopback'] == 1 &&
	  (
	      ( preg_match('/(loopback.*|null.*|lo\d*)$/i', $host_data['interface']) != 0) ||
	      ( array_key_exists('address', $host_data) && $host_data['address'] == '127.0.0.1')
      )) {
	return FALSE; // Skip loopback and loopback detected
  }

  # Valid address?
  if ( $ad_policy['check_address'] == 1 &&
	  (array_key_exists('address', $host_data) == TRUE) &&
	  ($host_data['address'] == '' || $host_data['address'] == '0.0.0.0')
  ) 
	  return FALSE; // Found address and its invalid

  $Alarms = new JffnmsAlarms();
  if ($ad_policy['check_state'] == 1 &&
    $Alarms->lookup($host_data['oper']) != ALARM_UP)
    return FALSE;

  return TRUE;
}

function update_sysobject_id($host_id, $host_ip, $rocommunity)
{
  $oid = '.1.3.6.1.2.1.1.2.0';
  $sysObjectId = snmp_get($host_ip, $rocommunity, $oid);
  if ($sysObjectId === FALSE)
    return NULL;
  $sysObjectId = str_replace('SNMPv2-SMI::enterprises','ent', $sysObjectId);
  db_update('hosts', $host_id, array('sysobjectid' => $sysObjectId));
  return $sysObjectId;
}

function sysobjectid_matches($host_id, $itype_id)
{
    if ($itype_id == '')
        return TRUE;
    if ($itype_id == '.' && $host_id != '')
        return TRUE;
    $host_id = substr(preg_replace('/^'.ENTERPRISES_OID.'\./','ent.',
        $host_id), 0, strlen($itype_id));
    return ($host_id == $itype_id);
}
