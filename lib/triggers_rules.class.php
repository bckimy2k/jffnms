<?php
/* Trigger rules Class
 * This file is part of JFFNMS
 * Copyright (C) <2002-2010> JFFNMS Authors
 * Licensed under GNU GPL v2, full terms in the LICENSE file
 */
class JffnmsTriggers_rules extends basic
{
  public function add($trigger_id)
  {
    $data = array(
      'action_id' => '1',
      'stop' => 0,
      'trigger_id' => $trigger_id);
    return db_insert('triggers_rules',$data);
  } //add_rule()

  public function get_all($ids = NULL, $trigger_id = NULL)
  {
      $where = array(
        array('triggers_rules.id','>',0),
        array('triggers_rules.trigger_id','=','triggers.id'),
        array('triggers_rules.action_id','=','actions.id')
    );
      if (is_numeric($trigger_id))
          $where[] = array('triggers.id', '=', $trigger_id);

    return get_db_list(  
      array('triggers','triggers_rules','actions'),$ids,   
      array(  'triggers_rules.*',
        'action_description'=>'actions.description',
        'action_parameters_def'=>'actions.user_parameters',
        'trigger_description'=>'triggers.description',  
        'trigger_type'=>'triggers.type'),  
      $where,
      array(
        array('triggers.id','asc'),
        array('triggers_rules.pos','asc'),
        array('triggers_rules.id','asc')) ); //order 
  } // list_rules()

  public function del($trigger_rule_id)
  {
    return db_delete('triggers_rules',$trigger_rule_id);
  } //del()

  public function del_trigger($trigger_id)
  {
    if (!is_numeric($trigger_id))
      return FALSE;
    $query = "DELETE FROM triggers_rules WHERE trigger_id = $trigger_id";
    return db_query($query) or die('Query Failed TriggersRules::del_all_truger() - '.db_error());
  } // delete()

}
?>

