<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require('../auth.php'); 

if (!profile('ADMIN_HOSTS')) die ('<H1> You dont have Permission to access this page.</H1></HTML>');

$Tools = new JffnmsTools();
$Interfaces = new JffnmsInterfaces();

$tool_get_data = $Sanitizer->get_string('tool_get_data', FALSE);
$tool_set_data = $Sanitizer->get_string('tool_set_data', FALSE);
$host_id = $Sanitizer->get_int('host_id');
$use_interfaces = $Sanitizer->get_string('use_interfaces', FALSE);
$interface_id = $Sanitizer->get_string('interface_id', FALSE);
if (!is_array($use_interfaces))
    $use_interfaces = explode(',', $use_interfaces);

if ($tool_get_data != FALSE)
{
  tool_get($Tools, $Interfaces, $tool_get_data);
  die();
}

if ($tool_set_data != FALSE)
{
  tool_set($Tools, $Interfaces, $tool_set_data);
  die();
}

$iframe = html('iframe','','NAME','', "name='NAME' OnLoad=\" if (src!='') { 
  frame = (parent.map)?parent.map:((parent.work)?parent.work:((parent.frame2)?parent.frame2:parent)); 
  frame.show(this); }\"");


$refresh=0;
tools_header();
    
if ($use_interfaces && !($interface_id))
  $interface_id = $use_interfaces;

if ($host_id == 0 && !($interface_id)) die ('Interface Not Selected');

$shown_interfaces = 0;

$interface_filters = array('host_fields'=>1);
if ($host_id >0)
  $interface_filters['host']=$host_id;

$ints = $Interfaces->get_all($interface_id,$interface_filters);
if (is_array($ints) && (count($ints)>0))
{
  head();
  echo
      tr_open('','header').
      td('Tool Description').
      td('Value').
      td("Action ".linktext("[all]","javascript: check();")).
      td('Result').
      tag_close('tr');

  $tools_list = array();
  foreach ($ints as $int_id=>$int)
  {
    $tool_answer_ok = true;
    if (!array_key_exists($int['type'], $tools_list))
      $tools_list[$int['type']] = $Tools->get_all(NULL,array('itype'=>$int['type'])); //get this interface type tools
    reset($tools_list[$int['type']]);

    if (count($tools_list[$int['type']]) > 0) //if this interface type has tools
    {
      table_row(
        $int['host_name'].' '.$int['zone_shortname'].' '.$int['interface'].' '.$int['description'],
        'interface', 4);
      foreach($tools_list[$int['type']] as $tool)
      {
        $tool_info = $Tools->info($tool['name']);
        $name = "value-$int_id-".$tool["name"];
        unset ($result);
    
        //$info_render = tool_info_render($name,$tool_info,$tool_values[$int_id][$tool['name']],$tool['allow_set']);
        $info_render = tool_info_render($name,$tool_info,NULL,$tool['allow_set']);
        
        if ($info_render!==false)
        {
          $div_name = 'tool_'.$int_id.'_'.$tool['name'];
          $result_name = 'result_'.$int_id.'_'.$tool['name'];
          $iframe_name = 'buffer_'.$int_id.'_'.$tool['name'];
          $iframe_aux = str_replace("NAME",$iframe_name,$iframe);
          echo 
            tr_open().
            td($tool['description']).
            td(html("div", $info_render, $div_name).$iframe_aux).
            td(checkbox_value("action",$int_id."-".$tool["name"]."-".$tool["allow_set"])).
            td(html('div','&nbsp;', $result_name)).  //result
            tag_close("tr");
        } else
          table_row ($tool['description'],'',4);
        flush();    
      }
      $shown_interfaces++;
    } else
      if (count($ints)==1) //only if we were requested one interface
        table_row ('There are no Tools defined for this Interface Type.','no_records_found',4);
  }
} else
  table_row ('Bad Interface selection.','no_records_found',4);
if ($shown_interfaces > 0)
  head();

echo 
  form_close().
  table_close();
adm_footer();

function tool_get(&$Tools, &$Interfaces, &$tool_get_data)
{
  list ($int_id, $tool_name,$tool_allow_set) = explode(',',$tool_get_data);
  echo tool_info_render("value-$int_id-$tool_name",
    $tools->info($tool_name),
    $tools->get_value($tool_name,
    $Interfaces->get_all($int_id,array('host_fields'=>1))),
    $tool_allow_set);
}

function tool_set(&$Tools, &$Interfaces, &$tool_set_data)
{
  list ($int_id, $tool_name, $value) = explode(",",$tool_set_data);
  list ($result, $value) = 
    $Tools->set_value($tool_name,
    $Interfaces->get_all($int_id,array("host_fields"=>1)),
    $value, $auth_user_name, false);

  echo ($result==true)?"<font color=green>OK</font>":"<font color=red>ERROR</font>";
}
function tool_info_render($name, $info, $value, $allow_set)
{

  switch ($info["type"]) {

      case "text":
        $result = textbox($name,$value,$info['param']['size'],0,($allow_set==0)).' '.$info['param']['label'];
    break;

      case "select":
    if (!empty($value)) 
        $result = select_custom($name,$info['param'],$value);
    else
        $result = select_custom($name,array(),$value);
    break;

      case 'table':
    if (is_array($value)) {
        $result = table();
    
        if (count($value) > 0) {
      $result .= tr_open();
      foreach ($info['param']['fields'] as $fn)
          $result .= td ($fn);

      $result .= td ($info["param"]["action_field"]);
    
      $result .= tag_close("tr");
        }    
    
        foreach ($value as $key=>$data) {
      $result .= tr_open();
      foreach ($data as $fv)
          $result .= td($fv);

      $result .= td(checkbox_value ($name."[]",$key));
      $result .= tag_close("tr");
        }
        
        $result .= table_close();
    } else
        $result = "No Information\n";
    break;

      case "separator":
    $result = false;
    break;
  }
  return $result;
    }

    function head() {
  echo
      tr_open("","top").
      td("&nbsp;","","",2).
      td(
    linktext("Refresh","javascript: tool_execute(true);")."&nbsp; | &nbsp;".
    linktext("Set","javascript: tool_execute(false);")).

      td( linktext("Close","javascript: window.close();")).
      tag_close("tr");
    }

function tools_header()
{
  adm_header('Tools');

  echo 
    script("
    function tool_get (intid,tool,set) {
       iframe_name = 'buffer_'+intid+'_'+tool;
       ifr = document.getElementById(iframe_name);
       url = document.location+'&tool_get_data='+intid+','+tool+','+set;
       document.getElementById('result_'+intid+'_'+tool).innerHTML = '<font color=blue>Fetching</font>';
       ifr.set = false;
       ifr.src = url;
    }

    function tool_set (intid,tool,set) {
      if (set==1) {
        value = document.getElementById('value-'+intid+'-'+tool).value;
        ifr = document.getElementById('buffer_'+intid+'_'+tool);
        ifr.set = true;
        document.getElementById('result_'+intid+'_'+tool).innerHTML = '<font color=blue>Setting</font>';
        ifr.src = document.location+'&tool_set_data='+intid+','+tool+','+value;
      }
    }
  
    function show (iframe) {
       name = iframe.name;
       if (iframe.set == true) { 
         result = 'result'+name.substr(6,name.length);
         document.getElementById(result).innerHTML = this.frames[name].document.body.innerHTML;
       } else {
         div = 'tool'+name.substr(6,name.length);
         document.getElementById(div).innerHTML = this.frames[name].document.body.innerHTML;
         result = 'result'+name.substr(6,name.length);
         document.getElementById(result).innerHTML = '<font color=green>DONE</font>'
       }
    }
    
    function check() {
      field=document.forms[0].elements['action'];
          for (i = 0; i < field.length; i++) { 
        if (field[i].checked==true)
        field[i].checked = false; 
    else
        field[i].checked = true; 
      }
    }
  
    function tool_execute(get) {
      eles = document.forms[0].elements['action'];
      for (i=0; i < eles.length; i++) {
        ele = eles[i];
      if (ele.checked) { 
        vars = ele.value.split('-');
        if (get) 
      tool_get (vars[0],vars[1],vars[2]);
        else
      tool_set (vars[0],vars[1],vars[2]);
        }
      }
    }").
    table('tools').
    form('','','GET');
  table_row('Tools', 'title', 4);
}
?>
