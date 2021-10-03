<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2010> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsTriggers extends basic
{
	var $jffnms_insert = array('description'=>'New Trigger'); 

  // Triggers
  public function del($trigger_id)
  {
      //delete all rules  
      $TriggersRules = new JffnmsTriggers_rules();
      $TriggersRules->del_trigger($trigger_id);

      $TriggersUsers = new JffnmsTriggers_users();
      $TriggersUsers->del_trigger($trigger_id);

      //delete the trigger if everthing is ok
      $rules = $TriggersRules->get_all(NULL, $trigger_id);
      $users = $TriggersUsers->get_all(NULL, NULL, $trigger_id);
      if (count($rules) == 0 && count($users) == 0)
          return db_delete('triggers',$trigger_id);
      return FALSE;
  } // del()
    
  public function get_all($ids = NULL, $fields=array())
  {
    return get_db_list('triggers',  $ids,
      array('triggers.*'), //table,ids,fields  
      array(array('triggers.id','>',0)), //where
      array(array('triggers.id','asc')) ); //order 
  }

  public function analyze($data_type, $data, $log_it = 1)
  {
    global $Config;
    $Interface_maps = new JffnmsMaps_interfaces();
    $TriggersUsers = new JffnmsTriggers_users();

    $old_script_name = $Config->get('logging_file');
    #$Config->set('logging_file','triggers');
    $query_rules = " 
    SELECT
      triggers.id as trigger_id, 
      triggers_rules.id as rule_id, triggers_rules.pos as rule_pos,
      triggers_rules.field, triggers_rules.operator, triggers_rules.value,
      triggers_rules.action_parameters, triggers_rules.stop as rule_stop,
      triggers_rules.and_or,
      actions.id as action_id, actions.command as action_command,
      actions.internal_parameters as internal_parameters
    FROM triggers_rules, triggers, triggers_users, actions
    WHERE
      triggers_users.trigger_id = triggers.id AND triggers_users.active = 1
      AND triggers.type = '$data_type' 
      AND triggers_rules.trigger_id = triggers.id 
      AND triggers_rules.action_id = actions.id 
    GROUP BY
      triggers_rules.id, triggers_rules.pos, triggers_rules.field,
      triggers_rules.operator, triggers_rules.value,
      triggers_rules.action_parameters, triggers_rules.stop, actions.id,
      actions.command, actions.internal_parameters, triggers.id,
      triggers_rules.and_or
    ORDER BY triggers.id asc, triggers_rules.pos asc, triggers_rules.id asc
    ";
    //logger($query_rules);

    $result_rules = db_query ($query_rules) or die ('Query failed - trigger_analyze('.$data['id'].') - '.db_error());
    $stopped_triggers = array();
  
    if ($log_it)
      if (db_num_rows($result_rules) > 0) 
        logger(str_repeat('=',71)."\n");
    while ($rule = db_fetch_array($result_rules))
    {
      unset ($rule_field);
      unset ($eval_result);

      if (!array_key_exists($rule['trigger_id'], $stopped_triggers) ||
        $stopped_triggers[$rule['trigger_id']]==0) //trigger rules are not over
      {
        if (($log_it) && (isset($old_trigger)) && ($old_trigger!=$rule['trigger_id']))  //debuging
          logger ("$data_type ".$data['id'].": ".str_repeat("-",80)."\n");
        $rule_field = $rule['field'];
        if ($rule_field == '')
            continue;


        //exceptions
        $test_modifier = NULL;
        if (array_key_exists('field', $rule) && !empty($rule['field'])) {
            if (strpos('date',$rule['field']) > -1)
                $test_modifier = 'time';
            elseif (strpos('map',$rule['field']) > -1)
                $test_modifier = 'map';
        }
        $test_value = $data[$rule_field];
        switch($rule['field'])
        {
        case 'date':
          if (($data_type=='alarm') && ($rule['field']=='date'))
          {
            if (strtotime($data['date_stop']) > strtotime($data['date_start']))
              $data['date']=$data['date_stop'];
            else
              $data['date']=$data['date_start'];
          } 
          break;
        case 'map':
          $test_value = $rule['value'];
          $rule['value'] = join(',',$Interface_maps->maps($data['interface']));
          $rule['operator']='IN'; //force IN
          break;
        case 'none':
          $eval_result = 0;
          break;
        case 'any':
          $eval_result = 1;
          break;
        default:
            #logger("Unknown trigger rule field type '$rule[field]', skipping.\n");
            continue 2;
        } //switch

        if ($rule_field != 'none' && $rule_field != 'any')
          $eval_result = $this->test_operation ($test_value,$rule['operator'],$rule['value'],$test_modifier);

        if ($eval_result==1) //if the eval was true
        {
          if ($rule['rule_stop']==1)
            $stopped_triggers[$rule['trigger_id']]=1;
          if ($rule['action_id'] > 1)
          {
            $action_result = $this->action_execute($rule['action_command'],$rule['internal_parameters'].",".$rule['action_parameters'],

              $TriggersUsers->get_all(NULL,NULL,$rule['trigger_id'],1),$data_type,$data);
            # Update the alarm and set the triggered flag
            # To not sent out triggers for the same alarm twice
            db_update('alarms',$data['id'],$arr = array('triggered' => 1));
          } else {
            $action_result = 0;
          }
        } else //if the eval was false
          if ($rule['and_or']==1) //and rule specified AND
            $stopped_triggers[$rule['trigger_id']]=1; //Stop this trigger
      
        //Logging & Debugging
        $log=array();
        if (($rule_field!='none') && ($rule_field!='any'))
          $log[]="\tIf '".$rule['field']."(".substr($test_value,0,20).") ".$rule['operator']." ".$rule['value']."' ($eval_result)";
        else
          $log[]="\tIf '".$rule['field']."' ($eval_result)";
    
        if (($eval_result==1) && ($rule['action_id'] > 1))
          $log[]="\tThen ".$rule['action_command']." ($action_result)";
            
        if (array_key_exists('trigger_id', $stopped_triggers) &&
          $stopped_triggers[$rule['trigger_id']]==1) 
          $log[]="\tStop"; 

        if ($log_it)
          logger ("$data_type ".$data['id'].": ".
          "\tT ".$rule['trigger_id'].
          " - P ".$rule['rule_pos'].
          " - R ".$rule['rule_id'].
          join("",$log)."\n");
      } // not stopped
      $old_trigger = $rule['trigger_id'];
      $old_rule = $rule['rule_id'];
    }//while
    #$Config->set('logging_file', $old_script_name);
  } // analyze

  // private
  
  private function action_execute ($command, $parameters, $user_ids, $data_type, $data)
  {
    global $Config;
    $Interfaces = new JffnmsInterfaces();
    $Events = new JffnmsEvents();
    $Profiles = new JffnmsProfiles();
    $TriggersUsers = new JffnmsTriggers_users();

    if (!is_array($user_ids)) $user_ids=array($user_ids);

    $action_function = "action_$command";
    $action_file = $Config->get('jffnms_real_path')."/engine/actions/$command.inc.php";
    if (!in_array($action_file, get_included_files()))
    {
      if (!is_readable($action_file))
      {
        logger("ERROR Action plugin '$action_file' is not readable.\n");
        return FALSE;
      }
      require_once($action_file);
    }
    if (!function_exists($action_function))
    {
      logger("ERROR  Function '$action_function' not found in action plugin '$action_file'.\n");
      return FALSE;
    }
    unset($parameters_array);
    unset($function_data);
    if ($data_type=='alarm')
    { 
      if ($data['interface'])
        $function_data['interface']=current($Interfaces->get_all($data['interface']));  
      if ($data['referer_start'])
        $function_data['event']['start']=current($Events->get_all($data['referer_start']));
      if ($data['referer_stop'])
        $function_data['event']['stop']=current($Events->get_all($data['referer_stop']));
      if ($function_data['event'])
        krsort($function_data['event']);
      $function_data['alarm']=$data; //alarm data
    }
    if ($data_type=='event')
    {
      $function_data['event'][]=$data;
      if ($data['interface_id'] > 1)
        $function_data['interface']=current($Interfaces->get_all($data['interface_id']));
    }
    $param_aux = explode(",",$parameters);
    foreach ($param_aux as $aux) 
      if ($aux)
      {
        $pair = explode(':',$aux);
        //EXCEPTION 'from' handling, keep the last one
        if (($pair[0]=='from') && (!empty($pair[1])))
          $parameters_array[$pair[0]]='';
        if (!array_key_exists($pair[0], $parameters_array))
          $parameters_array[$pair[0]] = '';
        $parameters_array[$pair[0]] .=' '.$pair[1];
        $parameters_array[$pair[0]] = trim($parameters_array[$pair[0]]);
      }
    if (is_array($parameters_array))
    {    
      $replacer=array('interface','alarm','event');
      foreach ($replacer as $aux) 
        if ($function_data[$aux]) //replace variables 
        { 
          if ($aux=='event')
            $data=current($function_data[$aux]); //take only last event
          else
            $data = $function_data[$aux];
          if (is_array($data))
            foreach ($data as $var_key=>$var_data) //every piece of data
              foreach ($parameters_array as $key=>$param)  //every parameter
                $parameters_array[$key]=str_replace("<$aux-$var_key>",$var_data,$parameters_array[$key]);
        }
    } // parameters
    //debug ($parameters_array);
    $parameters_array_aux = $parameters_array; //save parameters before users loop
    $result=Array();
    if (is_array($user_ids) && (count ($user_ids) > 0))     
      foreach ($user_ids as $user_id)
        if ($user_id > 1)
        {
          $user_id = $user_id['user_id'];
          $function_data['user'] = current($TriggersUsers->get_all($user_id));   //get user data
          if (is_array($parameters_array))
          {
            $profile_options = $Profiles->get_all($user_id);
            foreach ($profile_options as $profile) //replace profile variables
              foreach ($parameters_array as $key=>$param) 
                $parameters_array[$key]=str_replace("<profile-".strtolower($profile['tag']).">",$profile['values_value'],$param);
          }
          $function_data['parameters']=$parameters_array;
          //debug ($function_data);
          $result[]= call_user_func_array($action_function,array($function_data));
          $parameters_array=$parameters_array_aux; //restore parameters
        }
    return join(',', $result);
  } //action_execute()

  private function test_operation ($value1, $op, $value2, $modifier = NULL)
  {
    $result = 0;
    if ($modifier == 'time')
    {
      $today = strtotime(substr($value1,0,10).' 00:00:00');
      $value1 = strtotime($value1)-$today;  //only take today hour 
    }

    switch ($op)
    {
      case '='  : if ($value1 == $value2) $result = 1; break;
      case '!=' : if ($value1 != $value2) $result = 1; break;
      case '>'  : if ($value1 > $value2) $result = 1; break;
      case '<'  : if ($value1 < $value2) $result = 1; break;
      case '>=' : if ($value1 >= $value2) $result = 1; break;
      case '<=' : if ($value1 <= $value2) $result = 1; break;
      case 'IN' : if (in_array($value1,explode(',',$value2))) $result = 1; break;
      case '!IN': if (!in_array($value1,explode(',',$value2))) $result = 1;break;
      case 'C'  : if (stristr($value1,$value2)!=false) $result = 1; break;
      case '!C' : if (stristr($value1,$value2)==false) $result = 1; break;
    }
    return $result;
  } //test_operation()

} // class 
?>
