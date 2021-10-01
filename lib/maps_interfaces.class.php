<?php
/* maps_interface class : This file is part of JFFNMS
 * Copyright (C) 2004-2011 JFFNMS authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsMaps_interfaces extends basic
{
  function get_all($ids = NULL, $where_special = NULL)
  {
    if (!is_array($where_special)) $where_special = array();
  
    $result = get_db_list(
        array('maps','maps_interfaces','interfaces','clients','hosts','zones'),
        $ids,
        array(  'maps_interfaces.*',
        'map_name'=>'maps.name',
        'aux_interface'=>'interfaces.interface',
        'aux_customer'=>'clients.shortname',
        'aux_host'=>'hosts.name',
        'aux_zone'=>'zones.zone'
        ),
        array_merge(
        array(
      array('maps_interfaces.map','>','1'),
      array('maps_interfaces.map','=','maps.id'),
      array('maps_interfaces.interface','=','interfaces.id'),
      array('interfaces.client','=','clients.id'),
      array('interfaces.host','=','hosts.id'),
      array('hosts.zone','=','zones.id')),
      $where_special),
        array (
      array('maps.id','asc'),
      array('maps_interfaces.id','desc')
      ),
        '');
        
    foreach ($result as $key=>$data)
        $result[$key]['interface_description'] = $data['aux_host'].' '.$data['aux_zone'].' - '.$data['aux_customer'].' - '.$data['aux_interface'];
  
    return $result;
  }
  
  function update($id,$data)
  {
    return db_update('maps_interfaces',$id,$data);
  }
  
  function add($map = 1,$interface = 1)
  { 
    return db_insert('maps_interfaces',array('map'=>$map,'interface'=>$interface));
  }
  
  function del($id)
  {
    return db_delete("maps_interfaces",$id);
  }
  
  function del_from_all($int_id)
  {
    $query = "delete from maps_interfaces where interface='$int_id'";
    $result = db_query($query) or die ("Query Failed - maps_interface_del_from_all() - ".db_error());
    return $result;
  }

  function status($map_id,$interface_id) //alias of interface_in_map
  {
    return interface_in_map($map_id,$interface_id);
  }
  
  function delete_links ($map_id,$interface_id)
  {
    $query = " DELETE from maps_interfaces where (interface = 1 and map = $map_id) and
          (x = $interface_id or y = $interface_id);";
    return db_query($query) or die ("Query Failed maps_interfaces_delete_links($map_id,$interface_id) - ".db_error()); 
  }

  function interface_in_map($map_id,$interface_id)
  {
    $query = "select id from maps_interfaces 
      where interface = '$interface_id' and map = '$map_id'";
    $result = db_query($query) or die ('Query Failed - ISM1 - '.db_error());
    $cant = db_num_rows($result); 
    if ($cant >= 1)
      return 1;
    return 0; 
  }

  function maps($interface_id)
  {
    $maps = array();
    $query = "select map from maps_interfaces where interface = '$interface_id'";
    $result = db_query($query) or die ("Query Failed - ISM1 - ".db_error());
    while ($rs = db_fetch_array($result))
      $maps[]=$rs['map'];
    return $maps;
  }
} //class
