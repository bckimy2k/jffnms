<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsTools extends basic
{
  var $jffnms_class = "tools";  
  var $jffnms_insert = array("description"=>"New Tool","name"=>"none"); 
  var $jffnms_filter_record = 0;
  var $jffnms_order_type = "asc";
  
  function get_all ($ids = NULL, $filters = array())
  {
    $where = array();
    foreach ($filters as $field=>$value)
      switch ($field)
      {
      case 'itype':
        if (!empty($value)) $where[]=array('tools.itype','=',$value);
        break;
      case 'name':
        if (!empty($value))
          $where[]=array('tools.name','=',"'$value'");
        break;
      } //switch
    return get_db_list(  
        array('tools','interface_types'), $ids, 
        array('tools.*',
          'itype_description'=>'interface_types.description'
        ) ,  
        array_merge(
      array(  
          array('tools.itype','=','interface_types.id'),
          array('tools.id','>',1)),
      $where
        ),
        array(
            array('tools.itype','asc'),
            array('tools.pos','asc'),
            array('tools.description','desc'),
            array('tools.id','desc')
        )); 
  }
  
  function info($tool_name)
  {
    global $Config;
    $data = $this->get_all(NULL,array('name'=>$tool_name));  
    $data = current($data);
    $file = (!empty($data['file_group'])?$data['file_group']:$data['name']);
    $real_function = 'tool_'.$data['name'].'_info';
    $function_file = $Config->get('jffnms_real_path')."/engine/tools/$file.inc.php";
    
    if (in_array($function_file,get_included_files()) || (file_exists($function_file) &&  (include_once($function_file))))
    {
      if (function_exists($real_function))
      {
        $result = call_user_func_array($real_function,array());
        return $result;    
      } else
        logger("ERROR: Calling Function '$real_function' doesn't exists.<br>\n");
    } else
      logger ("ERROR Loading file $function_file.<br>\n");
    return false;
  }
  
  function get(&$Interfaces, $tool_name,$id)
  {
    $data = $this->get_all(NULL,array('name'=>$tool_name));  
    $interface_data = ((is_numeric($id))?$Interfaces->get_all($id,array("host_fields"=>1)):$id);
    
    if (is_array($data) and (count($data)==1) && is_array($interface_data) && (count($interface_data)==1))
    {
      $data = current($data);
      $interface_data = current($interface_data);
      $result = $this->get_real ($data,$interface_data);
    } else
      logger("ERROR: Bad Tool Name or Interface\n");
    
    if (isset($result))
      return $result;
    return false;
  }
  
  function tools_set(&$Interfaces, $tool_name,$id,$value,$username = "unknown",$reget = true)
  {
    $Events = new JffnmsEvents();
    $data = $this->get_all(NULL,array('name'=>$tool_name));  
    if (is_array($data) and (count($data)==1))
    {
      $data = current($data);
      if ($data['allow_set']==1)
      {
         $interface_data = ((is_numeric($id))?$Interfaces->get_all($id,array("host_fields"=>1)):$id);
         if (is_array($interface_data) && (count($interface_data)==1))
         {
           $interface_data = current($interface_data);
           $result = tools_set_real ($data,$interface_data,$value);
          } else
            logger("ERROR: Bad Interface\n");
      } else
        logger ("ERROR: Tool does not allow Setting.\n");
    } else 
      logger("ERROR: Bad Tool Name\n");
  
    if (isset($result))
    {
      //Event Creation 
      $tool_event_type = 42; //FIXME Hard-Coded
  
      //Get a meaningful value to show in the event
      $tool_info = $This->info($data['name']);
        
      switch ($tool_info['type'])
      {
      case 'select': 
        $value_info = $tool_info["param"][$value];
        break;
      default:
        $value_info = $value;
      }
  
      $description= (isset($tool_info['description_label'])?$tool_info['description_label']:$data['description']);
      $value_info = (isset($tool_info['state_label'])   ?$tool_info['state_label']:$value_info);
      $value_info .= (isset($tool_info['param']['label'])   ?$tool_info['param']['label']:''); // add label to event value
        
      $Events->add(date('Y-m-d H:i:s',time()),$tool_event_type,
        $interface_data['host'],$interface_data['interface'],$value_info,$username,$description,'',0);
  
       //Return data to the caller
       return array($result,($reget==true)?$this->get($data['name'],array($interface_data)):NULL);    
    }
    return false;
  }
  
  function tools_get_real ($tool_data,$interface_data)
  {
    global $Config;
    $file = (!empty($tool_data['file_group'])?$tool_data['file_group']:$tool_data['name']);
  
    $real_function = "tool_".$tool_data["name"]."_get";
    $function_file = $Config->get('jffnms_real_path')."/engine/tools/$file.inc.php";
    if (in_array($function_file,get_included_files()) || (file_exists($function_file) &&  (include_once($function_file))))
    {
      if (function_exists($real_function))
        $result = call_user_func_array($real_function,array($interface_data));
      else
        logger("ERROR: Calling Function '$real_function' doesn't exists.<br>\n");
    } else
      logger ("ERROR Loading file $function_file.<br>\n");
    return $result;
  }
  
  function tools_set_real ($tool_data,$interface_data,$value)
  {
    $file = (!empty($tool_data['file_group'])?$tool_data['file_group']:$tool_data['name']);
    $real_function = 'tool_'.$tool_data['name'].'_set';
    $function_file = $Config->get("jffnms_real_path")."/engine/tools/$file.inc.php";
    if (in_array($function_file,get_included_files()) || (file_exists($function_file) &&  (include_once($function_file))))
    {
      if (function_exists($real_function))
        $result = call_user_func_array($real_function,array($interface_data,$value));
      else
        logger("ERROR: Calling Function '$real_function' doesn't exists.<br>\n");
    } else
      logger ("ERROR Loading file $function_file.<br>\n");
    return $result;
  }
} //class
?>
