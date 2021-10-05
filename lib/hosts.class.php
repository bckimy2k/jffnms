<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
class JffnmsHosts extends basic
  {
  
    public function status (&$Interfaces, $host_id = NULL, $map_id = NULL, $only_in_rootmap = 0, $client_id = NULL)
  {
    return $Interfaces->status(NULL,array('host'=>$host_id,'map'=>$map_id,'in_maps'=>$only_in_rootmap, 'show_disabled'=>0, 'client'=>$client_id));
  }
  
  function get_all($ids = NULL, $zone_id = NULL, $where_special = NULL, $count = 0)
  {
    if (is_array($zone_id))
    {
      global $Interfaces;
      return $this->list_filtered($Interfaces, $zone_id);
    }
  
    $index = 'hosts.id';
      
    if (!is_array($where_special))
      $where_special = array();
    if (is_numeric($zone_id))
      $where_special[]=array('hosts.zone','=',$zone_id);
  
    $order = array (
      array('hosts.name','asc'),
      array('zones.zone','asc'));
  
    if (!$count)
    {
      $fields = array(
        'hosts.*',
        'zone_description'=>'zones.zone', 
        'autodiscovery_description'=>'autodiscovery.description',
        'default_customer_description'=>"clients.name",
        'satellite_description'=>'satellites.description',
        'zone_image'=>'zones.image',
        'config_type_description'=>'hosts_config_types.description'
      );
    } else {
      $fields = array("count($index) as cant");
      $index = NULL;
      $order = NULL;
    }
    
    return get_db_list(
      array('zones','hosts','autodiscovery','clients','satellites','hosts_config_types'),
        $ids,$fields,
        array_merge(
        array(
      array('hosts.zone','=','zones.id'),
      array('autodiscovery.id','=','hosts.autodiscovery'),
      array('clients.id','=','hosts.autodiscovery_default_customer'),
      array('satellites.id','=','hosts.satellite'),
      array('hosts_config_types.id','=','hosts.config_type'),
      array('hosts.id','>','0')),
      $where_special),
        $order,
        $index);
  }
  
  private function list_filtered (&$Interfaces, $filters = array())
  {
    if (!isset($Interfaces)) {
      $Interfaces = new JffnmsInterfaces();
    }
    $interfaces = $Interfaces->get_all(NULL,$filters);
    
    $hosts = array();
    foreach ($interfaces as $interface)
      $hosts[] = $interface['host'];
    
    $hosts = array_unique($hosts);
    return $this->get_all($hosts);
  }
  
  function count($ids = NULL, $zone_id = NULL, $where_special = NULL)
  {
    return current(current($this->get_all($ids, $zone_id, $where_special, 1)))-1;
  }
      
  function add($zone_id=NULL)
  {
    if (!is_numeric($zone_id)) $zone_id = 1;
    
    return db_insert('hosts', array('name'=>'New Host', 'zone'=>$zone_id, 'creation_date'=>time()));
  }
  
  function update($host_id,$host_data)
  {
    $host_data['rocommunity'] = snmp_options_parse($host_data['rocommunity']);
    $host_data['rwcommunity'] = snmp_options_parse($host_data['rwcommunity']);
    // if the only fields to update are the last_poll_* ones don't update the modification_date
    if ((count($host_data)==2) && isset($host_data['last_poll_date']) && isset($host_data['last_poll_time'])) 
      ; //nothing
    else
      $host_data['modification_date']=time(); //update the modification date
  
    if (isset($host_data['dmii']) && (substr($host_data['dmii'],0,1)!='M') && (substr($host_data['dmii'],0,1)!='I')) 
      $host_data["dmii"] = 1;
    
    $result = db_update('hosts',$host_id,$host_data);
    return $result;
  }
  
  function del($host_id,$IntObj=FALSE)
  {
    if (!$IntObj)
      $IntObj = new JffnmsInterfaces();
    $interfaces = $IntObj->get_all (NULL,array('host'=>$host_id));
    
    foreach ($interfaces as $int) 
      $IntObj->del($int['id']);
  
    $interfaces = $IntObj->get_all (NULL,array('host'=>$host_id));
    if (count($interfaces) == 0)
      return db_delete('hosts',$host_id);
    return FALSE;
  }
  
  function dmii_interfaces_list (&$Interfaces, $host_id)
  {
    $host_data = current($this->get_all($host_id));
    $dmii = $host_data['dmii'];
  
    if ($dmii[0]=='M')
      return array_keys($Interfaces->get_all(NULL,array('map'=>substr($dmii,1))));
    if ($dmii[0]=='I')
      return array(substr($dmii,1)); //return an array with only the interface id
    return 1; //force not to do nothing in poller plan
  }
  
  function dmii_if_all_down_list(&$Interfaces, $host_id,$dmii_map)
  {
    $dmii_down = maps_status_all_down($dmii_map);
    if ($dmii_down)  //if they are all down, dont poll the others
    {
      $result = array_keys($Interfaces->get_all(NULL,array('map'=>$dmii_map,'host'=>$host_id))); //only poll the Designated Main Interfaces
      return $result;
    }
    return FALSE;
  }
  
  function status_dmii(&$Interfaces, $host_id)
  {
    $host_data = current($this->get_all($host_id));
    $dmii = $host_data['dmii'];
  
    if ($dmii[0]=='M')
      return $this->dmii_if_all_down_list($Interfaces, $host_id,substr($dmii,1)); //set to a map
    if ($dmii[0]=='I') //set to an interface
      if (!$Interfaces->is_up(substr($dmii,1))) //is down
        return array(substr($dmii,1)); //return an array with only the interface id
    return FALSE; //not down
  }
  
  // New DMII functions
  function dmii_set($host_id, $new_dmii)
  {
      return db_update('hosts',$host_id, array('dmii_up' => $new_dmii));
  }
  function dmii_get($host_id)
  {
      $dmii_query = "SELECT dmii,dmii_up FROM hosts WHERE id='$host_id'";
      $result=db_query($dmii_query) or die("Query failed ($dmii_query) - Hosts -> dmii_get: ".db_error());
      if (db_num_rows($result) == 0)
          return 0;
      $row = db_fetch_array($result);
      if ($row['dmii'] == '1')
          return '1'; // Always up if no DMII set
      return $row['dmii_up'];
  }
  
}//class
?>
