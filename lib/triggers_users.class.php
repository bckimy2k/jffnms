<?php
/* Trigger Users Class
 * This file is part of JFFNMS
 * Copyright (C) <2002-2010> JFFNMS Authors
 * Licensed under GNU GPL v2, full terms in the LICENSE file
 */
class JffnmsTriggers_users extends basic
{
  public function add($user_id=NULL)
  {
    if (!is_numeric($user_id) || $user_id < 1)
      $user_id = 1;
    $data = array(
      'user_id' => $user_id,
      'active' => 0
    );
    return db_insert('triggers_users', $data);
  } // add

  public function get_all($ids = NULL, $user_id = NULL,$trigger_id = NULL,$only_active = 0)
  {
    $where = array();
    if (is_array($user_id))
      $user_id = current($user_id);
  
    if ($user_id)
      $where[]=array('triggers_users.user_id','=',$user_id);
    if ($trigger_id)
      $where[]=array('triggers_users.trigger_id','=',$trigger_id);
    if ($only_active==1)
      $where[]=array('triggers_users.active','=',1);

    return get_db_list( 
      array('triggers_users','auth','triggers'), $ids,   
      array(  'triggers_users.*',
        'user_description'=>'auth.fullname',
        'trigger_description'=>'triggers.description'),   
      array_merge(array(
        array('triggers_users.id','>',1),
        array('triggers_users.user_id','=','auth.id'),
        array('triggers_users.trigger_id','=','triggers.id')),
        $where), 
      array(  array('triggers_users.user_id','desc'),
        array('triggers_users.id','desc')) ); //order 
  } //get_all()

  public function del($trigger_user_id)
  {
      return db_delete('triggers_users', $trigger_user_id);
  }

  public function del_user($user_id)
  {
    if (!is_numeric($user_id))
      return FALSE;
    $query = "DELETE FROM triggers_users WHERE user_id = $user_id";
    return db_query($query) or die('Query Failed TriigersUsers::del() - '.db_error());
  } // delete()

  public function del_trigger($trigger_id)
  {
    if (!is_numeric($trigger_id))
      return FALSE;
    $query = "DELETE FROM triggers_users WHERE trigger_id = $trigger_id";
    return db_query($query) or die('Query Failed TriggersUsers::del_all_truger() - '.db_error());
  } // delete()
}
?>

