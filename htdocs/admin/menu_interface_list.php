<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

{
include ('../auth.php'); 
    
$frame = $Sanitizer->get_string('frame');
$field = $Sanitizer->get_string('field');
$field_name = $Sanitizer->get_string('field_name');
$field_id = $Sanitizer->get_string('field_id');
$map_id = $Sanitizer->get_int('map_id');
$client_id = $Sanitizer->get_int('client_id');
$type = $Sanitizer->get_string('type', 'performane');
if (empty($type)) $type = 'performance'; // for urls type=
$request_uri = $Sanitizer->get_url('');
$action = interface_list_action($type);

adm_header("Interface Selector".(!empty($field_name)?" - ".$field_name:""));
    
if ($map_profile = profile('MAP')) $map_id = $map_profile; 
if ($client_profile = profile('CUSTOMER')) $client_id = $client_profile; 

    
echo '<script language="JavaScript" src="../scripts/menu_interface_list.js"></script>'.
  script ("
        function select_all(field_aux) {
      field = document.getElementById(field_aux);
      for (i = 0; i < field.length; i++)
    field[i].selected = ! field[i].selected;
  }");

interface_list_popups($type, $action,$frame, $request_uri);
echo 
  table('interface_selector').
    tr_open('header').
      td(linktext(image('b-left.png','','','Back','','back'),
        "javascript: toggle_menu('back','../images/b-left.png','../images/b-right.png');").
        '&nbsp;'.'&nbsp;'.html('span','Interface Selector', '','title'),'','',2).
      td(linktext(image('refresh.png'),$request_uri),'action').
    tag_close('tr').
    tr_open().
      td('').
      td('').
      td('').
    tag_close('tr');
print_interface_groups($type, $action, $frame, $map_id, $client_id);
    adm_footer(); 
}

function print_interface_groups($type, $action, $frame, $map_id, $client_id)
{
  global $Sanitizer;

  $only_top = $Sanitizer->get_string('only_top');

  if (empty($map_id) && empty($client_id))
    $groups = array(
      'host'=>array('select_hosts', 'Hosts',array()), 
      'client'=>array('select_clients', 'Customers',array()), 
      'map'=>array('select_maps', 'Maps',array()), 
      'type'=>array('select_interface_types', 'Types',array())
    );
  else
    $groups = array(
      'host'=>array('select_hosts_filtered', 'Hosts', array('map'=>$map_id, 'client'=>$client_id))
    );    
      
  foreach ($groups as $group=>$group_data)
  {
    list ($func, $name, $filters) = $group_data;
    $js = "javascript: go_select('".$group."');";
      
     echo 
      tr_open().
       td(
          call_user_func_array($func, array($group, 0, 1, array(0=>$name), $js, $filters)),
          'select', '', 2).
      td(linktext(image('bullet6.png'), '#', '_self', '', $js),'action').
      tag_close('tr');
  }
  echo form('selector_form',$action,'GET',$frame);
 
  table_row("&nbsp","spacer",3);
    
  echo
    hidden("name", "Selected").
    tr_open().
      td('Selected'.br().'Interfaces', 'selected_header','',1).
      td( control_button('Mark All','_self', "javascript: select_all('use_interfaces'); ",'world.png').
      control_button('Del','_self', 'javascript: del_selected(); ','delete.png'),'buttons','',2).
    tag_close('tr');
    
    table_row(select_custom ('use_interfaces', array(), '', '', 10),'selector',3);
    table_row(adm_form_submit('View Selected Interfaces'),'view_selected',3);

    $show_only_top = FALSE;
    if (profile('REPORTS_VIEW_ALL_INTERFACES'))
    {
      table_row(linktext("View All Interfaces",$action."?&view_all=1",$frame),"view_all",3);
      $show_only_top = true;
    }

    if ($show_only_top && ($type=='performance'))
        table_row(
          checkbox_value("only_top",1,($only_top==1)?1:0,1,
          "javascript: addlink('view_all_group','&only_top=',this); addlink('view_all','&only_top=',this);").
          "Show Only the Options&nbsp;","show_only_top",3);

    echo
  form_close().
  table_close();
}

function interface_list_popups($type, $action, $frame, $request_uri)
{
  global $Config, $Sanitizer;

  $popup_w = 550;
  $popup_h = 295;

  $popup_url = $Sanitizer->get_url(
    $Config->get('jffnms_rel_path').'/admin/interface_selector.php',
    array('map_id', 'client_id'));
    

  echo script ("
  popups = new Array();
    
  function ClosePopups()
  {
    for (var name in popups)
      if (popups[name])
        popups[name].close();
  }

  window.onunload = ClosePopups;

  function go_select(field)
  {
    select = document.getElementById(field);
    if (select.selectedIndex == 0) return;
  
    id = select.options[select.selectedIndex].value;
    text = select.options[select.selectedIndex].text;
    name = field+'_'+id;
  
    if (!popups[name]) {
      rand = new Date().getTime();

      url = '${popup_url}field='+field+'&field_id='+id+'&field_name='+text;
      popups[name] = window.open(url, name+'_'+rand, 'toolbar=no,scrollbars=no,location=no,status=no,menubar=no,screenX=250px,width=".$popup_w.",height=".$popup_h."');
      if (!popups[name].opener) popups[name].opener = self;
    } else {
      popups[name].close();
      popups[name]=null;
      url = '$action'+'?'+field+'_id='+id+'&name='+text;
      parent.frames['$frame'].location=url;
    }
    return false;
  }
    
    
  function del_selected()
  {
    select = document.getElementById('use_interfaces');
    size = select.length;
  
    for (i=size; i > -1; i--)
      if (select.options[i] && (select.options[i].selected == true))
        select.options[i]=null;
  }

  function addlink(link, part, source)
  {
    field = document.getElementById(link);
    if (field)
    {
      value = source.checked==true?1:0;
      field.href = field.href+part+value;
    }
  }

  // IE FIX
  if (document.all) 
    parent.document.getElementById('interface_list').cols='275,*';

  old_size = -1;
    
  function toggle_menu(image_name,img_hidden, img_show)
  {
    fs = parent.document.getElementById('interface_list');
    img = document.getElementById(image_name);
  
    if (old_size==-1)
      old_size = fs.cols;
    showed = old_size;
    hidden = '12,*';
    img.src = (fs.cols!=showed)?img_hidden:img_show;
    fs.cols = (fs.cols!=showed)?showed:hidden;
  }");
}

function interface_list_action($type)
{
  global $Config;

  switch ($type)
  {
  case 'alarms':
    $action = '/admin/adm/adm_alarms.php';
    break;
  case 'performance':
    $action = 'view_performance.php';
    break;
  case 'state_report':
    $action = '/admin/reports/state_report.php';
    break;
  case 'interfaces':
    $action = '/admin/adm/adm_interfaces.php';
    break;
  default:
    die ("interface_list_action(): No view_type for \"$type\".");
  }
  $action = $Config->get('jffnms_rel_path').'/'.$action;
  $action = str_replace ('//','/',$action);
  return $action;
}
?>
