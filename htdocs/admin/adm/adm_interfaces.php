<?php
/* This file is part of JFFNMS
 * Copyright (C) 2002-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once('../../auth.php');
    
if (!profile('ADMIN_HOSTS')) die (html('H1','You dont have Permission to access this page.'));

$action = $Sanitizer->get_string('action'); 
$init = $Sanitizer->get_int('init',0); 
$span = $Sanitizer->get_int('span',20); 
$host_id = $Sanitizer->get_int('host_id', FALSE); 
$interface_id = $Sanitizer->get_string('interface_id'); 
$actionid = $Sanitizer->get_string('actionid',$interface_id, TRUE); 
$filter = $Sanitizer->get_string('filter', $host_id); 
$add_to_map_id = $Sanitizer->get_int('add_to_map_id', 1);
$use_interfaces = $Sanitizer->get_string('use_interfaces');
$type_id = $Sanitizer->get_int('type_id', FALSE); 
if (!is_array($use_interfaces))
    $use_interfaces = explode(',', $use_interfaces);

adm_header('Interfaces');

echo script ("
function check(field_aux) {
  field=document.forms[0].elements[field_aux];
  for (i = 0; i < field.length; i++) { 
      if (field[i].checked==true) 
    field[i].checked = false; 
      else  
    field[i].checked = true; 
  }
  return true;
}
");


$fields_init = array (
 'action' =>array('description'=>'Action', 'showable'=>1, 'overwritable'=>0),
 'id' =>array('description'=>'ID', 'showable'=>1, 'overwritable'=>0),
 'host' =>array('description'=>'Host', 'default_value'=>1, 'showable'=>1, 'overwritable'=>1),
 'type' =>array('description'=>'Type', 'default_value'=>1, 'showable'=>0, 'overwritable'=>1),
 'interface' =>array('description'=>'Interface Name', 'showable'=>1, 'overwritable'=>1)
);
    
$fields_end = array (
 'row_filler' =>array('description'=>'Row Filler', 'showable'=>1, 'overwritable'=>0),
 'client' =>array('description'=>'Customer', 'default_value'=>1, 'showable'=>1, 'overwritable'=>1),
 'poll' =>array('description'=>'Poller Group', 'default_value'=>1, 'showable'=>1, 'overwritable'=>1),
 'check_status' =>array('description'=>'Check Status', 'default_value'=>1, 'showable'=>1, 'overwritable'=>1),
 'sla' =>array('description'=>'SLA', 'default_value'=>1, 'showable'=>1, 'overwritable'=>1),
 'make_sound' =>array('description'=>'Enable Sound?', 'default_value'=>0, 'showable'=>1, 'overwritable'=>1),
 'show_rootmap' =>array('description'=>'Show', 'default_value'=>0, 'showable'=>1, 'overwritable'=>1),
 'poll_interval' =>array('description'=>'Polling Interval', 'default_value'=>0, 'showable'=>1, 'overwritable'=>1),
 'creation_date' =>array('description'=>'Creation Date', 'showable'=>1, 'overwritable'=>0),
 'modification_date'=>array('description'=>'Modification Date', 'showable'=>1, 'overwritable'=>0),
 'last_poll_date'=>array('description'=>'Last Poll Date', 'showable'=>1, 'overwritable'=>0)
);  

$Interfaces = new JffnmsInterfaces();
$Hosts = new JffnmsHosts();

if (!$action)
  $action_select['update'] = 1;
else {
    $action_select['update'] = '';
  $action_select[$action] = 1;
}

// This doesnt have edit or list, which come later
switch ($action)
{
case 'add':
  if (!$host_id) $host_id = 1; 
  $interface_id = $Interfaces->add($host_id);
  $actionid=array($interface_id);
  $action='edit';
  $old_action = 'add';
  break;

case 'update':
  adm_interfaces_update_local($Interfaces, $actionid);
  $action='list';
  break;

case 'bulkadd':
  list($action, $actionid, $use_interfaces) = adm_interfaces_bulkadd($Interfaces);
  $action_select['update']=1;
  $clean_url = $Sanitizer->get_url('', 'all',
    array('use_interfaces'=> $use_interfaces),
    array('bulk_add_ids', 'bulk_add'));
  break;

case 'delete':
  if (is_array($actionid)) 
    foreach ($actionid as $id) 
      if (is_numeric($id)) 
        $Interfaces->del($id);
  $action='list';
  break;

case 'map':
  if ($add_to_map_id > 1)
  {
      $MapsInterfaces = new JffnmsMaps_interfaces();
      $map_list = $MapsInterfaces->get_all($add_to_map_id);

    if (is_array($actionid)) 
      foreach ($actionid as $id) 
        if (is_numeric($id))
        {
          $ok = 1;
          foreach ($map_list as $aux) 
            if ($aux['interface']==$id) $ok = 0; 
          if ($ok==1) $MapsInterfaces->add($add_to_map_id,$id);
        }
    unset($map);
    $action='list';
  }
  break;
}


$max_fields = 50;

$filters = reports_make_interface_filter($use_interfaces,0);
$filters['with_field_type'] = 1;
if (isset($filter))
  $filters['host'] = $filter;
$interface_count = $Interfaces->get($use_interfaces, $filters);

echo adm_table_header('Interfaces', $init, $span, $max_fields, $interface_count, 'admin_interfaces', false);

if ($interface_count > 0)
{
  $types_fields = $Interfaces->fetch();
  $interface_count--;
  $num_fields = array();

  foreach ($types_fields as $it=>$fds) 
  {
    $num_fields[$it] = 0;
    foreach ($fds as $fd)
      if ($fd['showable'] > 0)
        $num_fields[$it]++;
  }

  if (is_array($num_fields))
    $fields_end['row_filler']['max'] = max($num_fields);
  else
    $fields_end['row_filler']['max'] = 0;

  unset ($it);
  unset ($fds);
  unset ($fd);
  
  $Interfaces->slice($init,$span);
  
  $old_interface_type = 0;
  $number_of_types = 0;
  
  echo adm_form('update', 'POST', '_self', false). reports_pass_options();

  while ($register = $Interfaces->fetch())
  {
    if ($register['type'] != $old_interface_type)
    {
      echo tag ('tbody','itype_'.$register['type']);  
      $type_fields = array_merge($fields_init,
        (is_array($types_fields[$register["type"]])?$types_fields[$register["type"]]:array()),    
        $fields_end);
  
      show_header ($type_fields, $register,$num_fields[$register['type']]);
      $row = 0;
    
      $old_interface_type = $register['type'];
      $number_of_types++;
    }
  
    if ($actionid && in_array($register['id'],$actionid) && $action=='edit') 
      $edit = 1;
    else 
      $edit = 0;

    echo tr_open('row_'.$register['id'],($edit)?'editing':((($row++%2)!=0)?'odd':''));
    show_values ($type_fields,$register,$edit,$actionid,$interface_count,$num_fields[$register['type']]);
    echo tag_close("tr");
  }
  echo tag_close('tbody');
  
  if (($action!='edit') && ($interface_count > 1)) //multiple editor
  {
    if ($number_of_types > 1)
    {
      $multiple_edit_fields = array_merge($fields_init, $fields_end);
      $old_interface_type=''; //to show all field types (sla, poll)
    } else 
      $multiple_edit_fields = $type_fields;

    table_row('&nbsp;','',$max_fields);
    echo tr_open('multi_editor');
    show_values ($multiple_edit_fields,array('type'=>$old_interface_type),1,0,0,$num_fields[$old_interface_type]);

    echo 
      tag_close('tr'). tr_open('multi_editor_checkboxes');
    show_multiple_edit_boxes($multiple_edit_fields,$num_fields[$old_interface_type]);
    echo tag_close('tr');
  }
  
  if ($interface_count > 0) //if theres an interface listed
  {
    if ($action != 'edit') //if we are in edit mode
    {
      $action_map = 0;
      if (array_key_exists('map', $action_select))
        $action_map = $action_select['map'];
      echo 
        tr_open ('options').
        td ((($interface_count == 1) //if the other option arent showing, show a Submit button
          ?adm_form_submit('Add To Map').br().hidden('action','map')
          :radiobutton('action',$action_map,'map').'Add to Map:'.br())
      ,"field","add_to_map").
    
        td(select_maps("add_to_map_id",$add_to_map_id),"field","add_to_map_select",2).
        td(linktext("View Map", $Config->get('jffnms_rel_path')."/admin/adm/adm_standard.php?admin_structure=maps_interfaces&filter=".$add_to_map_id),
      "field","view_map");
        td("&nbsp;","row_filler","",$max_fields-5).
        tag_close("tr");
    }
    interfaces_print_shortcuts($use_interfaces, $max_fields);
  }
} else
  table_row('No Interfaces Found','no_records_found',$max_fields);
    
echo 
  table_close(). form_close();
adm_footer();

function show_header($fields,$sample_data = array(),$number_of_showable_fields = 0) 
{
  global $Sanitizer;

  $clean_url = $Sanitizer->get_url('', 'all', FALSE, array('bulk_add_ids', 'bulk_add'));
  echo tr_open('','header');
  if (is_array($fields))
    foreach ($fields as $field_name=>$data) 
      if ($data['showable'] > 0)
        switch ($field_name) {
        case 'action' :
          echo td ( (is_numeric($GLOBALS["type_id"])
          ?linktext('View all Types',$clean_url.'&type_id=&action=')
          :linktext($sample_data['type_description'],$clean_url.'&action=&type_id='.$sample_data['type']))
          ,'type_filter');
          break;
        case 'row_filler':
          $rows = $data['max']-$number_of_showable_fields;
          if ($rows > 0)
            echo td('&nbsp','row_filler','',$rows);
          break;
        default: 
          echo td($data['description'],'field','field_'.$field_name);
        }//switch
  echo tag_close('tr');
}

function show_values ($fields,$values,$edit = 0, $ids = array(),$interface_count = 1, $number_of_showable_fields = 0)
{
  if (is_array($fields))
    foreach ($fields as $field_name=>$data)
      if ($data['showable'] > 0)
      {
        if (isset($values[$field_name]))
          $value = htmlspecialchars($values[$field_name]);
        else { 
          if (array_key_exists('default_value', $data))
            $value = $data['default_value'];
          else
            $value = '&nbsp;';
        }

        if ($edit)
          $content = get_interface_field_edit($field_name, $ids, $interface_count, $value, $values, $data, $number_of_showable_fields);
        else
          $content = get_interface_field($field_name, $ids, $interface_count, $value, $values, $data ,$number_of_showable_fields);

        if ($content)
          echo td($content, "field", "field_".$field_name);
      
      if (($edit==1) && ($interface_count!=0) && ($data["overwritable"]==1)) //normal one interface edit
    echo hidden("update_fields[]",$field_name);

  }
    
}

function show_multiple_edit_boxes ($fields, $number_of_showable_fields)
{
  global $action_select;

  if (is_array($fields))
  {
    foreach ($fields as $field_name=>$data)
      if ($data["showable"] > 0)
      {
        unset ($content);
        $rows = 1;
        switch ($field_name)
        {
        case 'action' :
          $action_delete = 0;
          if (array_key_exists('delete', $action_select))
            $action_delete == $action_select['delete'];
          $content = radiobutton('action',$action_delete,'delete').'Delete';
        break;
        case 'id': 
          $content = 'Multiple'.br().'Edit';
        break;
        case 'row_filler':
        $rows = $data['max']-$number_of_showable_fields;
    
        case 'creation_date' :
        case 'modification_date' :
        case 'last_poll_date' :
          if ($rows > 0)
            $content = '&nbsp;322'; 
        break;
        default:
          $content = ($data['overwritable']==1)
            ?checkbox_value('update_fields[]',$field_name,0)
            :'&nbsp;';
        }//switch

        if (!empty($content))
          echo td ($content, 'field', 'field_'.$field_name, $rows);
      }
  }
}
    

function get_interface_field_edit($field_name, $ids, $cant, $value, $values, $data, $number_of_showable_fields)
{
  global $action_select;

  switch ($field_name)
  {
  case 'action' :
    if ($cant != 0)
    {
      echo td (adm_standard_submit_cancel('Save','Discard'), 'action');
      return FALSE;
    } else
      return adm_form_submit().br().
       radiobutton('action',$action_select['update'],'update').
       'Update';
    break;
  case 'id':
    if ($cant != 0)
      return checkbox_value('actionid[]',$value,1).' '.$value;
    return checkbox_value('','',false,true, "this.value=check('actionid[]');").' All'.br();
    break;

  case 'host':
    return select_hosts('host', $value);
    break;

  case 'type': 
    return select_interface_types($field_name,$value);
    break;

  case 'poll': 
    return select_pollers_groups($field_name,$value,$values['type']);
    break;

  case 'interface': 
    return textbox($field_name,$value,20);
    break;

  case 'client': 
    return select_clients($field_name,$value);
    break;
    
  case 'sla': 
    return select_slas($field_name,$value,$values['type']);
    break;
    
  case 'check_status': 
  case 'make_sound': 
    return checkbox($field_name,$value);
    break;

  case 'show_rootmap':
    return select_show_rootmap($field_name,$value,FALSE);
    break;

  case 'row_filler':
    $rows = $data['max']-$number_of_showable_fields;
        
    if ($rows > 0)
      echo td('&nbsp;','row_filler','',$rows);
    return '';
      break;
    
  case 'poll_interval': 
    return select_poll_interval($field_name,$value,FALSE);
    break;

  case 'creation_date' :
  case 'modification_date' :
  case 'last_poll_date' :
    if ($cant == 0)
      return '&nbsp;';
    return show_unix_date($values[$field_name]);
    break;
            
  default: 
    switch ($data['type_handler'])
    {
    case 'bool' : //check box
      return checkbox($field_name,$value,1);
      break;
          
    case "text" : //text is the pseudo-default
    default:
      if ($data['overwritable'] == 1)
        return textbox($field_name, $value, 20);
      if (empty($value))
        return ' ';
      return $value;
      break;
    }
  }//switch field_name
  return $value;
}

function get_interface_field($field_name, $ids, $cant, $value, $values, $data, $number_of_showable_fields)
{
    global $filter;
  switch($field_name)
  {
  case 'action' :
    if ($cant != 0)
    {
      echo adm_standard_edit_delete($filter, $values['id'], false);
      return FALSE;
    }
    return adm_form_submit().br().
      radiobutton('action',$GLOBALS['action_select']['update'],'update').
      'Update';
    break;

  case 'id':
    if ($cant != 0)
    { 
      $check = ((is_array($ids) && in_array($value,$ids) || ($cant==1))?1:0);
      return checkbox_value('actionid[]', $value, $check).' '.$value;
    }
    return checkbox_value('','',false,true, "this.value=check('actionid[]');").' All'.br();
    break;

  case 'host':
    return $values['host_name'].' '.$values['zone_shortname'];
    break;

  case 'type': 
    return $values['type_description'];
    break;

  case 'poll': 
    return $values['poller_group_description'];
    break;

  case 'interface': 
    return $values['interface'];
    break;

  case 'client': 
    return $values['client_name'];
    break;
    
  case 'sla': 
    return $values['sla_description'];
    break;
    
  case 'check_status': 
  case 'make_sound': 
    return checkbox($field_name,$value,0);
    break;

  case 'show_rootmap':
    return select_show_rootmap($field_name,$value,TRUE);
    break;

  case 'row_filler':
    $rows = $data['max']-$number_of_showable_fields;
    if ($rows > 0)
      echo td('&nbsp;','row_filler','',$rows);
    return '';
    break;
    
  case 'poll_interval': 
    return select_poll_interval($field_name,$value,TRUE);
    break;

  case 'creation_date' :
  case 'modification_date' :
  case 'last_poll_date' :
    if ($cant == 0)
      return '&nbsp;';
    return show_unix_date($values[$field_name]);
    break;
            
  default: 
    switch ($data['type_handler'])
    {
      case 'bool' : //check box
        return checkbox($field_name,$value,FALSE);
        break;
          
      case 'text': //text is the pseudo-default
      default:
        if (empty($value))
          return ' ';
        return $value;
        break;
    }
  }
  return $value;
}

function interfaces_print_shortcuts($use_interfaces, $max_fields)
{
  global $Config, $Sanitizer;

  $jffnms_rel_path = $Config->get('jffnms_rel_path');
  $copy_tags = array('host_id', 'name', 'actionid');
  if (is_array($use_interfaces) &&  !$_SERVER["QUERY_STRING"])
    $interfaces_url = "&use_interfaces[]=".join("&use_interfaces[]=",$use_interfaces);
  else
    $interfaces_url = '';

  $shortcuts = array(
    array('graph.png', 'Performance', '/view_performance.php'),
    array('text.png',  'Report', '/admin/reports/state_report.php'),
    array('text.png',  'Alarms', '/admin/adm/adm_alarms.php'),
  );

  $output = tr_open('shortcuts');
  foreach($shortcuts as $shortcut)
  {
    $output .=
    td(linktext(image($shortcut[0], '', '', $shortcut[1]) .
                ' '.$shortcut[1],
                $Sanitizer->get_url($jffnms_rel_path.$shortcut[2],$copy_tags).$interfaces_url),
      '', '', 2);
  }
  echo $output.
    td('&nbsp;','row_filler','',$max_fields-4).
    tag_close('tr');
}

function adm_interfaces_update_local(&$Interfaces, $actionid)
{
  global $Sanitizer;

  if (!is_array($actionid))
    return;

  $update_fields = $Sanitizer->get_string('update_fields');

  if (!is_array($update_fields))
    return;

  $data = array();
  foreach ($update_fields as $field) 
    $data[$field]=trim($Sanitizer->get_string($field)); 

  foreach ($actionid as $id) 
    if (is_numeric($id)) 
      $Interfaces->update($id,$data);
}

function adm_interfaces_bulkadd(&$Interfaces)
{
  global $Sanitizer;

  $use_interfaces = array();
  $actionid = array();
  $action = 'list';

  $bulk_add_ids = $Sanitizer->get_int('bulk_add_ids');
  $bulk_add = $Sanitizer->get_special('bulk_add');
  #var_dump($bulk_add);echo "<br>that was sanitized<br>";
  #var_dump($_POST['bulk_add']);

  if (is_array($bulk_add_ids) && is_array($bulk_add))
  { 
    foreach($bulk_add as $key => $data)
     if (in_array($key,$bulk_add_ids))
     {
       $new_id = $Interfaces->add(array('host'=>$data['host'],'type'=>$data['type'])); //add
       $result = $Interfaces->update($new_id,$data); //update
       $use_interfaces[]=$new_id;
       $actionid[]=$new_id;
      } 
      if (count($bulk_add_ids)==1) 
        $action = 'edit';
  }
  return array($action, $actionid, $use_interfaces);
} // adm_interfaces_bulkadd
?>
