<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsEvents
{

  function set_ack($id,$ack = 1)
  {
    if ($ack == 1) 
      $result = db_query ("update events set ack='$ack' where ack = 0 and id = '$id'"); //dont overwrite the journal
    else 
      $result = db_update("events",$id,array("ack"=>$ack)); //if it's journal, overerite
    return $result;
  } // ack

  public function set_analized($id,$analized = 1)
  {
    return db_update('events',$id, array('analized'=>$analized));
  }

  public function add($date,$type,$host,$interface,$state,$username,$info,$referer,$log_it = 1)
  {
    $Triggers = new JffnmsTriggers();
    $info = substr($info,0,149);
    $data = compact('date','type','host','interface','state','username','info');
    if (!empty($referer))
      $data['referer'] = $referer;
    $id = db_insert('events',$data);
  
    if ($log_it == 1)
      logger("New Event ($id): $date - $type - $host - $interface - $state - $username - $info - $referer\n");

    $Triggers->analyze('event',current($this->get_all($id)),$log_it); //analize event triggers
    return $id;
  }

  public function get_all ($event_id = NULL, $map_id = 1, $have_filter = 1, $filter = NULL, $init = 0, $span = 20, $order_type = "desc" ,$view_all = 0, $show_all = 0, $journal = 0, $client_id = 0)
  { 
    global $Config;
    $params = func_get_args();
    $params = current($params);
  
    $latest_mode = 2; //FIXME make this an option
    $use_latest = 1;
    $time_latest = $Config->get('events_latest_max')*(60);
  
    $query_select = 
      "select /* SQL_BUFFER_RESULT HIGH_PRIORITY SQL_BIG_RESULT */ ".
      "<events_table>.id, <events_table>.date, hosts.name as host_name, <events_table>.host as host_id, ".
      "hosts.ip as host_ip, zones.shortname as zone, zones.image as zone_image, types.description as type_description, ". 
      "<events_table>.type as type_id, severity.severity, zones.id as zone_id, zones.zone as zone_name, ".
      "severity.level as severity_level, severity.fgcolor, severity.bgcolor, <events_table>.interface, ".
      "<events_table>.username as user, <events_table>.state, <events_table>.info, types.text, types.show_host, ".
      "<events_table>.ack, clients.name as interface_customer, interfaces.id as interface_id, interfaces.client as interface_client_id ";
  
    $query_from  = "hosts, types, severity, zones, <events_table> ";

    $query_joins = " LEFT OUTER JOIN interfaces on (<events_table>.host = interfaces.host) and (<events_table>.interface = interfaces.interface) \n".
           " LEFT OUTER JOIN clients on (interfaces.client = clients.id) ";

    $query_where = "where <events_table>.host = hosts.id and <events_table>.type = types.id and hosts.zone = zones.id and 
      types.severity = severity.id $filter ";
  
    $query_order = "order by <events_table>.date $order_type, <events_table>.id $order_type ";
    
    if ($event_id > 1) $query_where.= " and <events_table>.id = $event_id ";

    if (($map_id) && ($map_id !=1 )) //Map Selected and Not RootMap
    {
      $query_from = "maps_interfaces, $query_from";
      $query_where .= 
        " and interfaces.interface = <events_table>.interface and interfaces.host = hosts.id and ". 
        "maps_interfaces.interface = interfaces.id and maps_interfaces.map = $map_id";
      
      if ($have_filter==0)
        $query_where .=" and date >= '".date("Y-m-d",time()-(60*60*24*3))." 00:00:00'"; 
      $use_latest = 0;
    }
  
    if (is_numeric($client_id) && ($client_id > 0)) //Filter by Client ID
      $query_where .= " and interfaces.client = $client_id ";
    
    if (($init+$span) > $Config->get("events_latest_max"))
      $use_latest = 0; 

    if ($have_filter==1)
    {
      $show_all = 1;
      $use_latest = 0;
      $query_where .= ' and types.show_default > 0'; //if its filtered show types 1 and 2 (show and 'only in filter')
    }

    if ($show_all==0)
      $query_where .= ' and types.show_default = 1'; //only show the normal (don't hidden) types
  
    if ($journal > 1)
      $query_where .= " and <events_table>.ack = $journal";
      
    //round span values (no decimals in SQL LIMIT)
    $span = round($span);
    $init = round($init);
  
    if (($view_all==1) && ($have_filter==1))
      $query_limit = '';
    else
      $query_limit = " LIMIT $init,$span";

    if ($latest_mode == 1)
    {
      if ($use_latest==1)
        $events_table = "events_latest";
      else
        $events_table = "events";
    }

    if ($latest_mode == 2)
    {
      //filter by lastest hours on events table
      if ($use_latest==1)
        $query_where .= " and <events_table>.date > '".date("Y-m-d H:i:s",time()-$time_latest)."' ";
      $events_table = "events";
    }
  
    $query = "$query_select \nfrom $query_from \n$query_joins \n$query_where \n$query_order $query_limit;";
    $query = str_replace("<events_table>",$events_table,$query);        
    //debug ($query);

    $res = db_query($query) or die ("Query Failed - events_list(".join(",",$params).") - ".db_error());
    $info = array();
  
    while ($reg = db_fetch_array($res)) {
      $reg["date"] = substr($reg["date"], 0, 19);
      $reg["text"] = $this->replace_vars($reg,$reg["text"]); //replace the variables in < >
      $info[]=$reg;  
    }
    //debug ($info);
    return $info;
  } // fetch

  public function make_latest($max = 0) //for performance reasons, this must be db-specific
  {
    global $Config;
    if ($max == 0) $max = $Config->get('events_latest_max');
    return db_copy_table("events","events_latest",$max);
  } // make_latest()

  public function replace_vars($event,$text_aux)
  {
    $Interfaces = new JffnmsInterfaces();
    $replacer = array ();
    $int_id = $event["interface_id"];
  
    if (is_numeric($int_id)) //if the event matched an interface
    {
      $int_data = $Interfaces->values($int_id); //get values
  
      foreach (current($int_data['fields']) as $fname=>$fdata)
        switch ($fdata['type'])
        {
        case 7:
          if (!isset($event['interface_description']))
            $event['interface_description']='';
          $event['interface_description'].= ' '.$int_data['values'][$int_id][$fname];
          $event[$fname]=$int_data['values'][$int_id][$fname];
          break;
        case 8: 
          $event[$fname]=$int_data['values'][$int_id][$fname];
          break;
        }
      unset ($int_data);
    }
    
    foreach (array_keys($event) as $key)
    {
      $replacer[$key] = $key;
      if (strpos($key,"_") > 1) $replacer[str_replace("_","-",$key)]=$key;
    }

    //exceptions
    $replacer['journal']='ack';
    $replacer['client']='interface_customer';
    $replacer['customer']='interface_customer';

    //debug ($event);
    //debug ($replacer);

    foreach ($replacer as $match=>$field)
      $text_aux = str_replace("<$match>",htmlspecialchars(trim($event[$field])),$text_aux);

     // this replaces not mached < > variables
    $text_aux = preg_replace('(<\S+>)','',$text_aux);

    return $text_aux;
  } //replace vars
} // class


//FILTERS
//--------------

function filters_generate_sql($filter_id = 1) {
    $query_filter_aux = "
      select filters_cond.pos, filters_fields.field, filters_cond.op, filters_cond.value
      from filters_cond, filters_fields
      where filters_cond.filter_id = $filter_id and filters_cond.field_id = filters_fields.id
      order by filters_cond.pos";
    $result_filter_aux = db_query($query_filter_aux) or die ("Query failed - FS1 - ".db_error());
    while ($registro_filter_aux = db_fetch_array($result_filter_aux)) {
  extract($registro_filter_aux);
  
  if ($field) {
      if (($field=="AND") or ($field=="OR"))  //for special fields
    $sql .= " $field ";
      else if (strpos($value,"(") > 0 ) $sql .="($field $op $value)"; //this is for SQL functions like NOW()
        else $sql .="($field $op '$value')"; //for other values
  }
    }    
    $sql = trim($sql);
    if ($sql!="") $sql_aux ="($sql)";
    return $sql_aux;
}



?>
