<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
:w 
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
class JffnmsHosts_configs extends basic
{
  function jffnms_class()
  {
      return 'hosts_config';
  }

  function add($host_id=1) {
      return db_insert('hosts_config', array('config'=>'New Config', 'host'=>$host_id));
  } // add


  function diff ($id1, $id2)
  {
    global $Config;
  
    if ($id1 != $id2)
    {
      $aux = $this->get_all(array($id1,$id2));
      $str1 = $aux[0]['config'];
      $str2 = $aux[1]['config'];
    
      $engine_temp = $Config->get('engine_temp_path');
    
      $name1 = $engine_temp.'/'.uniqid('').'.dat';
      $name2 = $engine_temp.'/'.uniqid('').'.dat';
  
      $pf = fopen($name1,'w');
      fputs($pf,$str1);
      fclose($pf);
  
      $pf = fopen($name2,"w");
      fputs($pf,$str2);
      fclose($pf);
  
      $diff_executable = $Config->get('diff_executable');
  
      if (file_exists($diff_executable))
      {
        $c = exec($diff_executable.' -Nru '.$name1.' '.$name2, $diff);
        unlink($name1);
        unlink($name2);
        
        $diff = join("\n",array_slice($diff, 3));
        return $diff;
      }
    }
    return false;
  }

  function get_all($ids = NULL, $host_id=NULL, $init=NULL, $span=NULL, $where_special=NULL)
  {
      if (!is_array($where_special))
          $where_special = array();

      if (is_numeric($host_id))
          $where_special[]=array('hosts.id','=',$host_id);
      if ($ids!= NULL)
          $order = 'asc';
      else 
          $order = 'desc';

      return get_db_list(
        array('hosts_config','zones','hosts'),
        $ids,
        array('hosts_config.*',
              'zone_description'=>'zones.zone',
              'host_description'=>'hosts.name' ),
        array_merge(
            array(
                array('hosts.zone','=','zones.id'),
                array('hosts_config.host','=','hosts.id'),
                array('hosts_config.id','>','1')),
            $where_special),
        array (
            array('hosts_config.date',$order),
            array('hosts.id','asc')),
        '',NULL,$init,$span
    );
  }
} // class JffnmsHosts_Config
