<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */


class JffnmsAlarms extends basic
{

  // was delete
  public function delete($id)
  {
    return db_delete('alarms',$id);
  } //del()

  public function status ($interface,$event_type,$alarm_states)
  {
    $query ="
      SELECT alarms.id as start_id, alarms.referer_start, alarms.date_start as start_date, alarm_states.state  as alarm_state
      FROM alarms, alarm_states 
      WHERE alarms.interface = '$interface' and alarms.type = '$event_type' and alarms.active = alarm_states.id and
      (alarm_states.state = '".$alarm_states[0]."' or alarm_states.state = '".$alarm_states[1]."')";
    $result = db_query ($query) or die ("Query failed - have_other_alarm() - ".db_error());
    $cant = db_num_rows ($result);
  
    $data = array();
    if ($cant > 0) $data = db_fetch_array($result);
    return array('result'=>$result,'count'=>$cant,'alarm'=>$data);
  } //have_other

  public function insert(&$Triggers, $date_start,$date_stop,$interface,$type,$active,$referer_start,$referer_stop)
  {
    $data = array(
      'date_start' => $date_start,
      'interface' => $interface,
      'type' => $type,
      'active' => $active,
      'referer_start' => $referer_start);

    if ($date_stop != '')
      $data['date_stop'] = $date_stop;
    if ($referer_stop != '')
      $data['referer_stop'] = $referer_stop;

    $id = db_insert('alarms',$data);
    logger( "New Alarm: $id := $date_start - $date_stop - $interface - $type - $active - $referer_start - $referer_stop\n");
      
    $Triggers->analyze('alarm',current($this->alarms_list(NULL,array('alarm_id'=>$id)))); //analyze alarm triggers
    return $id;
  } //insert

  // was list
  public function alarms_list ($ids,$filters = NULL,$init = 0,$span = 100, $where_special = NULL)
  {
    if (!is_array($where_special)) $where_special = array();
    //round span values (no decimals in SQL LIMIT)
    $span = round($span);
    $init = round($init);
               
    if (is_array($filters))
      foreach ($filters as $filter_key=>$filter_value) 
          if ( isset($filter_value) )
        switch ($filter_key) {
        case 'type':
          $where_special[]=array('types.id','=',$filter_value);
          break;
        case 'state':
          $where_special[]=array('alarm_states.id','=',$filter_value);
          break;
        case 'alarm_id':
          $where_special[]=array('alarms.id','=',$filter_value);
          break;
        case 'triggered':
          $where_special[]=array('alarms.triggered','=',$filter_value);
          break;
        case 'alarm_state':
          $where_special[]=array('alarm_states.state','=',$filter_value);
          break;
        case 'host':
          $where_special[]=array('interfaces.host','=',$filter_value);
          break;
        } //switch
    $result = get_db_list(
      array('interfaces','alarms','types','alarm_states','clients'),
      $ids,
      array(
        'alarms.*',
        'duration'=>'(alarms.date_stop - alarms.date_start)',
        'interface_host'=>'interfaces.host',
        'interface_interface'=>'interfaces.interface',
        'interface_client'=>'clients.name',
        'interface_client_id'=>'interfaces.client',  //Needed for triggers by Customer
        'interface_type'=>'interfaces.type',  //needed for triggers by interface type
        'type_description'=>'types.description',
        'alarm_state'=>'alarm_states.state',
        'state_description'=>'alarm_states.description'),
    array_merge(
        array(
      array('alarms.interface','=','interfaces.id'),
      array('interfaces.client','=','clients.id'),
      array('alarms.type','=','types.id'),
      array('alarms.active','=','alarm_states.id'),
      array('alarms.id','>','1')),
        $where_special),
    array (
        array('alarms.id','desc')),
    '',NULL,$init,$span);
  
    foreach ($result as $key=>$data)
    {
      $result[$key]['interface_description'] = 
        $data['interface_client'].' '.$data['interface_interface']; 
      if ($result[$key]['alarm_state']==ALARM_DOWN) //Alarm still Active
        $result[$key]['duration'] = time() - strtotime($result[$key]['date_start']); //calculate the ongoing duration
    }
    return $result;
  }

  function lookup ($alarm_description)
  {
    $query = "SELECT state FROM alarm_states where description = '$alarm_description'";    
    $result = db_query ($query) or die ("Query Failed - alarms>lookup($alarm_description) - ".db_error());
    if (db_num_rows($result) == 1)
      return current(db_fetch_array($result));
    return NULL;
  }//lookup

  public function update($id,$data,$value=NULL)
  {
    $Triggers = new JffnmsTriggers();
    $result = FALSE;


    if (is_array($data))
      $result = db_update('alarms',$id,$data);

    $Triggers->analyze('alarm',current($this->alarms_list(NULL,array('alarm_id'=>$id)))); //analyze alarm triggers
    return $result;
  } //update()
} //class






?>
