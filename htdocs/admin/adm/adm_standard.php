<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
// Get from _GET
include ('../../auth.php'); 
require ('structures.php');

{
$adm_view_type = $Sanitizer->get_string('adm_view_type','html');
$st_name = $Sanitizer->get_string('admin_structure');
$so = $Sanitizer->get_string('so');
$sf = $Sanitizer->get_string('sf');
$init = round($Sanitizer->get_int('init'),0);
$span = round($Sanitizer->get_int('span'),20);
$action = $Sanitizer->get_string('action');
$filter = $Sanitizer->get_string('filter');
$editid = $Sanitizer->get_string('editid');
$actionid = $Sanitizer->get_string('actionid');
$old_passwd = $Sanitizer->get_string('old_passwd');
$new_passwd = $Sanitizer->get_string('new_passwd');

$st = get_structure($st_name);
    
$admin_users = profile('ADMIN_USERS');
    
$clean_url = $Sanitizer->get_url('','all', FALSE, array_merge(array_keys($st['fields']), array('action','actionid')));
$column_header_url = $Sanitizer->get_url('','all', FALSE, array_merge($st['fields'], array('action','sf','so')));

if ($adm_view_type!='html') $action = 'list';

if (array_key_exists('profile', $st) &&  $st['profile']) //Authorization
{
  if (!array_key_exists('deny', $st) || !$st['deny']) 
    $st['deny']='You dont have Permission to access this page.';

  if (!profile($st["profile"]))
    die ('<H1>'.$st['deny'].'</H1></HTML>');
}
    
if (!array_key_exists('no_records_message', $st))
  $st['no_records_message'] = 'No Records Found';

if (array_key_exists('include',$st))
  include_once('adm_'.$st['include'].'.inc.php');

$object_name = 'Jffnms'.$st['object'];
$api = new  $object_name();

if (function_exists($st_name.'_action_'.$action))
  call_user_func_array($st_name.'_action_'.$action, array($api, $actionid));

switch ($action)
{
case 'list':
  unset ($actionid);
  break;
    
case 'view':
  if ($st['action_type']==2) 
    adm_frame_menu_split($st['split'],$st['split_standard']);
    break;
    
case 'update' :
  if ($actionid=='new')
  {
    if (!array_key_exists('disable_add', $st)) 
    {
      if (array_key_exists('filter_by_user', $st) && $st['filter_by_user'] 
        && !$admin_users)
        $actionid = $api->add($_SESSION['auth_user_id']);
      else
        $actionid = $api->add($filter);
    } else 
      die ('Add not Allowed');
  }
  foreach($st['fields'] as $field_name=>$field_data) 
  {
    if ($field_name == '')
      continue;
    switch ($field_data['type'])
    { 
    case 'checkbox':   
      if (isset($_POST[$field_name]))
        $data[$field_name] = $Sanitizer->get_string($field_name);
      else
        $data[$field_name] = 0;
      break;
    // FIXME This is not right      
    case 'select':   
      if (array_key_exists('params', $field_data) && $field_data['params'] > 1
        && is_array($_POST[$field_name])) //size > 1 and array result
      {
        foreach ($_POST[$field_name] as $key=>$aux_value) 
          if ($aux_value > 1)
          {
            if ($key==0) //Why ???
              $api->update($actionid,array($field_name=>$aux_value));
            else 
              $api->add($filter,$aux_value);
          }
      } else {
        $data[$field_name] = $Sanitizer->get_string($field_name);
        if ($field_name == 'rocommunity' || $field_name == 'rwcommunity')
          $data[$field_name] = snmp_options_parse($Sanitizer->get_string($field_name));
      }
      break;
    default:
      if (isset($_POST[$field_name]))
      {
        $data[$field_name] = $Sanitizer->get_special($field_name);
          if (is_string($data[$field_name]))
            $data[$field_name] = trim($data[$field_name]);
      }
      break;
    }//switch
    if (array_key_exists('filter_by_user',$st)
      && $field_name == $st['filter_by_user'] && !$admin_users) 
      $data[$field_name]=$_SESSION['auth_user_id']; //filter by user

  }//foreach field
    //debug ($data);
    //debug ($actionid);
      
    $api->update($actionid, $data);
    $action='list';
    break;

case 'delete':
  if (isset($st['profile']) && !isset($st['filter_by_user']))
    $api->del($actionid);
  $action='list';
  break;

case 'add':
  $record = $api->get_empty_record();

  if (isset($st['add_filter_field']))
    $record[$st["add_filter_field"]] = $filter;
    
  if (isset($st['filter_field']))
    $record[$st['filter_field']] = $filter;

  $record['id'] = 'new';
      
  foreach ($record as $field_name=>$aux) 
    if (isset($_REQUEST[$field_name]))
      $record[$field_name]=$_REQUEST[$field_name];

  $records = array($record);
  $actionid = $record["id"];
  unset ($filter_field);
  unset ($filter_value);
  unset ($record);

case 'edit':     
  $editid = $actionid;
  break; 
  
default:
  $action = 'list';

} // switch action
    
// Main Code

$rows = 1;

if ($st['show_id'])  //add ID Field
  $st['fields'] = array_merge(array('id'=>array('name'=>'ID', 'type'=>'id')), $st['fields']);

$rows += count($st['fields']);

if (empty($actionid) || $actionid!='new')
{
  // OLD Filter handling 
  if (isset($st['filter_field'])) 
    $records = $api->get_all(NULL,array($st['filter_field']=>$filter));
  else 
    $records = $api->get_all($filter);
}

if (($action=='list') && ($adm_view_type=='html'))
{
  // Filters
    
  // Get Filters Values
  $filter_fields = '';
  while (list ($field, $data) = each ($st['fields']))
    if ($data != NULL && array_key_exists('filter', $data)
      && $data['filter']!==false)
      $filter_fields[$field]=(isset($data['view'])?$data['view']:$field);

  $field_values = $api->field_values($filter_fields);

  // Render Filters HTML
  while (list($field, $values) = each ($field_values))
    if (count($values) > 2)
      switch ($st['fields'][$field]['type'])
      {
      case 'checkbox':
        $filters[$field] = select_custom ('filter_'.$field,
          array(''=>'', '0'=>'Not Set', '1'=>'Set'), 
          '-1',' javascript: filter(this);', 1, false, 'filter');
        break;
  
      case 'colorbox':
        //Disable Color Box Filter
        break;
      
      case 'select':
        //Selects that does not have a View field (ie not from DB, like show_rootmap)
        //$filters[$field] = call_user_func_array($st["fields"][$field]["func"], array($field,$record[$field_name])); 
        if (!isset($st['fields'][$field]['view']))
          break;
        
      default:
        if ($field!='description')
          $filters[$field] = select_custom ('filter_'.$field, $values, 
          (($field==$filter_field)?$filter_value:""),
          ' javascript: filter(this);', 1, false, 'filter');
        break;
      }
} //if its not new record or edit

//Filter the Records
if (!empty($filter_field) && ($filter_value!==""))
  $records = array_record_search($records, $filter_field, $filter_value);

// SORT Records
if (empty($sf) || empty($so))
{
  if (isset($st['default_sort_field'])) 
    $sf = $st['default_sort_field'];

  if (isset($st['default_sort_order'])) 
    $so = $st['default_sort_order'];
}
    
if (!empty($sf) && !empty($so))
  array_key_sort($records, array($sf=>(($so=='asc')?SORT_ASC:SORT_DESC)));

    
$cant = count($records);

$show_add = TRUE;
if (array_key_exists('disable_add', $st) && $st['disable_add'])
  $show_add = FALSE;
$adm_table_header = adm_table_header($st['title'], $init, $span, $rows, $cant, 'admin_'.$st_name, $show_add);

if ($adm_view_type=='html')
{
  //Draw Header
  adm_header($st['title']);
  
  echo $adm_table_header;
    
  if ($st['action_type']==3) 
    echo script ("
  function go_action(select_name, link) {
          field = document.getElementById(link);
      if (field) {
        select = document.getElementById(select_name);
        value = select.options[select.selectedIndex].value;
        field.href = '".$clean_url."'+value;
      }
        }");

  echo script ("
  function filter (select) {
      field = select.name.substr(7);
          value = select.options[select.selectedIndex].value;
      location.href = '".$clean_url."' + '&action=list&init=0&filter_field=' + field + '&filter_value=' + value;
  }

  function toggle_filter(field) {
      filter_field = document.getElementById('filter_'+field);
      
      filter_field.style.visibility = ((filter_field.style.visibility=='visible')?'hidden':'visible');
  }");

  echo 
      tag('tr','','header').
          td ('Action', 'field', 'action');
}
    
//show only the init and span the user asked for
if ($cant > 0 && !empty($actionid) && $actionid!='new')
  $records = array_slice($records,$init,$span);


foreach ($st['fields'] as $field_name=>$field_data) 
{
  if (!is_array($field_data) || ($field_data['type']=='hidden'))
    continue;
  switch ($adm_view_type)
  {
  case 'html': 
    echo td(
      //ordering
      (($action=='list')
        ?linktext($field_data['name'],
        $column_header_url."&action=list&sf=".$field_name."&so=".((($sf==$field_name) && ($so=="asc"))?"desc":"asc")
        ,"","field")
        :$field_data['name']).
    //filter
    (((array_key_exists('filter', $field_data) && $field_data['filter']!==false) && ($field_name!=='id') && 
        ($action=='list') && (isset($filters[$field_name])))
        ?"&nbsp;".linktext( image("filter3.png"),"javascript: toggle_filter('".$field_name."');").
      "&nbsp;".$filters[$field_name]
        :""),
    'field','field_'.$field_name);
    break;

  case 'ascii':
    $ascii['fields'][$field_name]=$field_data['name'];
    break;
  }//switch
}//foreach fields

if ($adm_view_type=='html')
  echo tag_close('tr'). tag('tbody');
    
$shown = 0;
$row=0;
if ($cant > 0)
{
  foreach($records as $record)
  {
    if (array_key_exists('hide_record_one', $st) && $st['hide_record_one'] == 1
      && $record['id'] == 1)
      continue;

    if (isset($st['filter_by_user']))
    {
      if ($record[$st['filter_by_user']] != $_SESSION['auth_user_id'] && !$admin_users)
        continue;
      if ($admin_users && (!empty($filter) && $record[$st['filter_by_user']] != $filter))
        continue;
    }
    //debug ($record);
    $shown++;

    if ($adm_view_type=='html')
      echo tr_open('row_'.$record['id'],(($editid==$record['id'])?'editing':((($row++%2)!=0)?'odd':'')));

    if ($editid!=$record['id'])
    {
      if ($adm_view_type=='html')
      {
        switch($st['action_type'])
        {
        case 2:
          if (array_key_exists('split_view', $st))
            $view_name = $st['split_view'];
          else
            $view_name = '';
          echo adm_standard_edit_delete($filter, $record['id'], $view_name);
          break;
        case 3:
          echo td (action_dropdown('action_host', $record['id'], $st['actions'], $action), 'action');
          break;
        default:
          echo adm_standard_edit_delete($filter ,$record['id'], FALSE);
          break;
        }
      }
      if ($adm_view_type=='ascii')
        $ascii['data']['id'][$record['id']]=$record['id'];
      
        foreach ($st['fields'] as $field_name=>$field_data)
        if ($field_name)
          {
            unset ($control_value);
            $control_value = '';
            switch ($field_data['type'])
            {
            case 'id':
              if ($adm_view_type == 'html')
                $control_value = linktext($record[$field_name], $clean_url."&action=list&init=0&filter=".$record[$field_name],"","field_id");
              else
                $control_value = $record[$field_name];
              break;
      
            case 'memobox': 
            case 'textbox': 
              if ($adm_view_type == 'html')
                $control_value = htmlspecialchars(substr($record[$field_name],0,$field_data["size"]));
              else
                $control_value = $record[$field_name];
              break;
            case 'checkbox': 
              if ($adm_view_type == 'html')
                $control_value = checkbox($field_name,$record[$field_name],0);
              else
                $control_value = (($record[$field_name]=="0")?"O":"X"); 
              break;
            case 'select':
              if (!array_key_exists('params', $field_data))
                $field_data['params'] = array();

              if (!array_key_exists('view_params', $field_data))
                $field_data['view_params'] = array();
      
              if (array_key_exists('params_field', $field_data))
              {
                if (!is_array($record[$field_data['params_field']]))
                  $params_field = array($record[$field_data['params_field']]);
                else
                  $params_field = $record[$field_data['params_field']];
              } else
                $params_field = array();
              if (isset($field_data["view"]))
                $control_value = (isset($record[$field_data["view"]])
                ?(($adm_view_type=="html")
                ?htmlspecialchars(substr($record[$field_data["view"]],0,$field_data["size"]))
                :$record[$field_data["view"]])
                :$field_data["view"]);
              else
              {
                if (isset($field_data['view_func']))
                  $view_function = $field_data['view_func'];
                else
                  $view_function = $field_data['func'];
        
                $params = array_merge(
                  array($field_name,$record[$field_name]),
                  $params_field,
                  $field_data['params'],
                  $field_data['view_params']);

                //debug ($view_function);
                //debug ($params);
               
                $control_value = call_user_func_array ($view_function, $params);
              }
              break;
            case 'colorbox':
              if ($adm_view_type == 'html')
                $control_value = select_color($field_name,$record[$field_name],0);
              else
                $control_value = '#'.$record[$field_name];
              break;
            default:
              if ($field_data['type']!='hidden')
                $control_value = $record[$field_name]; 
              break;
            } //switch

            if ($adm_view_type=='html')
              echo td ($control_value, 'field',"field_$field_name");
            elseif ($adm_view_type=='ascii')
              $ascii['data'][$field_name][$record['id']]=$control_value;
          } //foreach-if
      } else { //edit
        adm_form('update');
        echo td (adm_standard_submit_cancel('Save','Discard'), 'action');
        if (array_key_exists('hidden_fields',$st))
          foreach ($st['hidden_fields'] as $hidden_field)
            echo hidden ($hidden_field,
              (array_key_exists($hidden_field,$record)?$record[$hidden_field]:''));

        foreach ($st["fields"] as $field_name=>$field_data) 
          if ($field_name)
          {
            unset ($control_value);
            switch ($field_data['type'])
            {
            case 'textbox': 
              $control_value = textbox($field_name,$record[$field_name],$field_data["size"]);
              break;
          
            case 'memobox': 
              $control_value = memobox($field_name,$field_data['height'],$field_data['width'],$record[$field_name]);
              break;

            case 'checkbox': 
              $control_value = checkbox($field_name,$record[$field_name],1);
              break;
  
            case "select": 
              // Don't execute Selects with params fields on new records
              if (($record['id']!='new') || !array_key_exists('params_field', $field_data) || !isset($record[$field_data['params_field']]))
              {
                if (!array_key_exists('params', $field_data)
                    || !is_array($field_data['params']))
                  $field_data['params'] = array();

                if (isset($field_data['params_field']) and array_key_exists($field_data['params_field'], $record))
                {
                  if (!is_array($record[$field_data['params_field']])) 
                    $params_field = array($record[$field_data['params_field']]);
                  else
                    $params_field = $record[$field_data["params_field"]];
                } else
                  $params_field = array();
                if (array_key_exists($field_name, $record))
                  $aux_param = array_merge(array($field_name,$record[$field_name]),$field_data['params'],$params_field);
                else
                  $aux_param = array_merge(array($field_name),$field_data['params'],$params_field);
                //debug ($aux_param);
          
                $control_value = call_user_func_array($field_data['func'],$aux_param); 
              } else
                $control_value = 'Not Available';
              break;
    
            case 'colorbox': 
              $control_value = select_color($field_name,$record[$field_name],1);
              break;
        
            case 'hidden': 
              $control_value = hidden($field_name,
                (array_key_exists($field_name, $record)?$record[$field_name]:''));
              break;

            default:
              if ($field_data['type']!='hidden')
                $control_value = $record[$field_name]; 
              break;
            } //switch
            echo td ($control_value, "field_$field_name");
          } //foreach-if
          echo form_close();
      } //edit
      
      if ($adm_view_type=='html')
        echo tag_close('tr');
      
      if (array_key_exists('after_record_function', $st) 
        && function_exists($st['after_record_function']))
        call_user_func_array($st['after_record_function'], array($record));
    } //if
} // cant
    
if ($shown==0)
  if ($adm_view_type=='html')
    table_row($st['no_records_message'],'no_records_found', $rows);
    
if ($adm_view_type=='html')
{
  echo tag_close('tbody'). table_close();
  adm_footer();
}

// ASCII Table Draw    
if (($adm_view_type=='ascii') && (is_array($ascii)))
{
  echo header('Content-Type: text/plain');
  $ascii_table = array(0=>'', 1=>'');
  
  while (list($field, $name) = each ($ascii['fields']))
  {
    $max_size[$field] = 0;
    if (is_array($ascii['data'][$field]))
      foreach ($ascii['data'][$field] as $data)
        $max_size[$field] = (strlen($data) > $max_size[$field])?strlen($data):$max_size[$field];
      
    $max_size[$field] = (strlen($name) > $max_size[$field])?strlen($name):$max_size[$field];
      
    $ascii_table[0] .= "-+-".str_repeat("-",$max_size[$field]);
    $ascii_table[1] .= " | ".str_pad($name, $max_size[$field]," ");
  }
  
  $ascii_table[0] .= "-+";
  $ascii_table[1] .= " |";
  $ascii_table[3] = $ascii_table[0];
  
  if (is_array($ascii['data']))
  {
    $id_field = current(array_keys($ascii['data']));
  
    foreach ($ascii['data'][$id_field] as $id)
    {
      $row++;
      $ascii_table[(3+$row)]='';
      foreach ($ascii['fields'] as $field=>$aux)
        $ascii_table[(3+$row)] .= " | ".str_pad($ascii["data"][$field][$id], $max_size[$field]," ");
  
      $ascii_table[(3+$row)] .= " |";
    }
    $ascii_table[] = $ascii_table[0];
  }
  foreach ($ascii_table as $data)
    echo substr($data,1)."\n";
}
}
?>
