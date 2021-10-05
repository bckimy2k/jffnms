<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsMaps extends basic
{
  
  function get_all($ids = NULL, $parent = NULL, $where_special = NULL)
  {
    if (!is_array($where_special))
      $where_special = array();
    if ($parent)
      $where_special[]=array('maps.parent','=',$parent); 
    if ($ids != 1)
      $where_special[]=array('maps.id','>','0');
    
    return get_db_list(
      array('maps','parent'=>'maps'),
      $ids,
      array(  'maps.*',
      'parent_name'=>'parent.name'),
      array_merge(
        array(array('maps.parent','=','parent.id')),
        $where_special),
      array (
        array('maps.name','asc')
        ),
      'maps.id',
      NULL);
  } // get_all()
  
  function status(&$Interfaces, $map_id)
  {
    return $Interfaces->status(NULL,array('map'=>$map_id));
  } // status()
  
  function status_all_down(&$Interfaces, $map_id)
  {
    $result = $this->status($Interfaces, $map_id);
    if (is_array($result) &&
      ($result['down']['qty'] == $result['total']['qty']) &&
      $result['total']['qty'] > 0)
      return TRUE;
  
    return FALSE;
  } // maps_status_all_down()
  
  function add($parent = 1) 
  {
    return db_insert('maps',array('name'=>'New Map '.rand(1,999),'parent'=>1));
  }
  
  function update($map_id,$data,$value=NULL)
  {
    return db_update('maps',$map_id,$data);
  }
  
  function del($map_id)
  {
      $Interfaces = new JffnmsInterfaces();
    $interfaces = $Interfaces->get_all($map_id);
  
    foreach ($interfaces as $int) 
      $Interfaces->del($int[id]);
    $interfaces = $Interfaces->get_all($map_id);
    
    if (count($interfaces) == 0) return db_delete('maps',$map_id);
    else return FALSE;
  }
} //class
?>
