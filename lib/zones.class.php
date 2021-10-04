<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsZones extends basic
{
  public function get_all($ids = NULL, $fields=[])
  {
    return get_db_list(
      array('zones'),
      $ids,
      'zones.*',
      array(array('zones.id','>','1')),
      array(array('zone','asc')));
  } // get_all()

  public function add($filters=NULL)
  {
    return db_insert('zones',array('zone'=>'a New Zone','image'=>'unknown.png'));
  } // add()

  function update($id,$data)
  {
    return db_update('zones',$id,$data);
  } //update()

  function del($id)
  {
      $Hosts = new JffnmsHosts();
      $zone_hosts = $Hosts->get_all(NULL,$id);
      foreach ($zone_hosts as $host)
          $Hosts->del($host['id']);

      $zone_hosts = $Hosts->get_all(NULL,$id);
      if (count($zone_hosts) == 0)
          return db_delete('zones', $id);
      return FALSE;
  } //del()

  public function status(&$Interfaces, $zone_id = NULL, $only_in_rootmap = 0)
  {
    return $Interfaces->status(NULL,
      array('zone'=>$zone_id,'in_maps'=>$only_in_rootmap));
  }
} //class
  

?>
