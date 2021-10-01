<?php
/* TACACS+ Consoliator is part of JFFNMS
 * Copyright (C) <2002> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */


function consolidate_tacacs(&$Events)
{
  $query_tacacs = "
		SELECT 	acct.id as id_tacacs, acct.date,acct.usern as value_user, acct.c_name as source_ip,
			acct.elapsed_time, acct.type, acct.cmd as command, hosts.id as id_host
		FROM 	acct, hosts 
		WHERE 	(hosts.ip_tacacs = acct.s_name OR hosts.ip = acct.s_name) AND analized = 0 
		ORDER BY acct.id asc";

  $result_tacacs = db_query ($query_tacacs) or die ('Query failed - TAC1');
  logger( 'TACACS+ Events to Process: '.db_num_rows($result_tacacs)."\n");

  $event_id = get_command_event_type();
  while ($tacacs = db_fetch_array($result_tacacs))
  {
    logger( "TACACS+ Event: ID: $tacacs[id_tacacs] // date: $tacacs[date] //".
      " host: $tacacs[id_host] // user: $tacacs[value_user] // ".
      "origin: $tacacs[source_ip] // time: $tacacs[elapsed_time] //".
     " type: $tacacs[type] // cmd: $tacacs[command]\n");
		
    $value_interface = "command";
	
    if ($tacacs['command'] == '')
    {
      switch($tacacs['type'])
      {
      case 'START':
        $value_info = "Session Start from $tacacs[source_ip]";
        break;
      case 'STOP':
        $value_info = "Session Finished $tacacs[elapsed_time] sec, $tacacs[source_ip]";
        break;
      case 'REJECT':
        $value_info = "Rejected Password, connection from $tacacs[source_ip]";
        $value_state = strtolower($type);
        break;
      }//switch
		} else {
      $value_info = addslashes(str_replace(array('limit',"'",'offset'),'',$tacacs['command']));
		 $value_state = "active";
		}
		
    $Events->add($tacacs['date'], $event_id, $tacacs['id_host'],
       $value_interface, $value_state, $tacacs['value_user'],
       $value_info, $tacacs['id_tacacs']);  
    db_update('acct',$tacacs['id_tacacs'],array('analized'=>1));
	} //while
}

function get_command_event_type() {
    $cmd_event_type = 8; // Event type 8 is a command event, as shipped

    $query_types = "SELECT id FROM types WHERE description = 'Command' LIMIT 1";
    if ( ($result_types = db_query($query_types))) {
        if (db_num_rows($result_types)) {
            $types_row = db_fetch_array($result_types);
            $cmd_event_type = $types_row['id'];
        }
    }
    return $cmd_event_type;
}
?>
