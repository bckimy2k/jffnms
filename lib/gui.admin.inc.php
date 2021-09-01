<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

    //This function creates the $filter array for interface_status, interface_list, etc. processed by interface_filter
    //from the URL from menu_reports
function reports_make_interface_filter($use_interfaces,$view_all)
{
    global $Sanitizer;
    $filters = array();
    $filter_names=array('id'=>'interface_id','client'=>'client_id','type'=>'type_id','host'=>'host_id','map'=>'map_id');
    foreach($filter_names as $filter_name => $filter_input)
    {
        $filter_value = $Sanitizer->get_string($filter_input,FALSE);
        if ($filter_value)
            $filters[$filter_name]=$filter_value;
    }
    if ($_SESSION['auth_type'] == 2) { // It is a client
        $filters['client'] = $_SESSION['auth_user_id'];
    } elseif ($client_profile = profile('CUSTOMER')) {
        $filters['client'] = $client_profile;
    }
	if (($view_all==1) || ((count($use_interfaces)==0) && (count($filters)==0))) { 
        if (profile('REPORTS_VIEW_ALL_INTERFACES'))
            $filters=array('custom'=>array(array('1=1'))); //show all
        else
            $filters=array('custom'=>Array('1=2')); //show nothing
	} 
	return $filters;
}

function reports_pass_options()
{
    global $Sanitizer;
    $fields = array('host_id','client_id','interface_id','map_id','type_id','view_all');
    $output='';
    foreach ($fields as $field)
    {
        $field_value = $Sanitizer->get_string($field,FALSE);
        if ($field_value !== FALSE)
            $output .= hidden($field,$field_value);
    }
    $use_interfaces = $Sanitizer->get_string('use_interfaces',FALSE,TRUE);
    if (is_array($use_interfaces)) 
        foreach ($use_interfaces as $value) 
            $output .= hidden('use_interfaces[]',$value);
  return $output;
}

function adm_table_header($title, $init, &$span, $cols, $total_items = -1, $id = "", $show_add = true)
{
    global $Sanitizer;
	global $filter, $host_id, $client_id, $map_id, $use_interfaces, $interface_id, $filter_field;

    if (!$span) $span = 20;
    $url = $Sanitizer->get_url('', array('use_interfaces', 'admin_structure','init','span'),
        array('filter' => $filter));
    $url_init = $Sanitizer->get_url('', array('use_interfaces', 'admin_structure', 'span'),
        array('filter' => $filter));
    $url_span = $Sanitizer->get_url('', array('use_interfaces', 'admin_structure', 'init'),
        array('filter' => $filter));

	return
	    table ($id, "admintable").
	    tag("thead"). 
	    tr_open("navigation").
    	    tag("td", "", "", "colspan='$cols'").
	    html("span", $title, "title").

	    (($init > 0)
		?control_button("First","_self", $url_init."&init=0","a-top.png").
		 control_button("Prev","_self",$url_init."&init=".($init-$span),"a-left.png")
		:control_button("&nbsp","","","none").
		 control_button("&nbsp","","","none")).

	    ((($total_items == -1) || (($init+$span) < $total_items)) 
		?control_button("Next","_self", $url_init."&init=".($init+$span),"a-right.png")
		:control_button("&nbsp","","","none")).

    	    ((($total_items == -1) || (($init+($span*3)) < $total_items))
		?control_button("3rd Next","_self", $url_init."&init=".($init+($span*3)),"a-right.png")
		:control_button("&nbsp","","","none")).

	    control_button("More","_self", $url_span."&span=".($span*2),"plus.png").
	    control_button("Less","_self", $url_span."&span=".round($span/2),"minus.png").
	
	    (($show_add)?control_button("Add","_self", $url."&action=add","new2.png"):"").

	    (($filter || $host_id || $client_id || $map_id || $use_interfaces || $interface_id || $filter_field) 
		?control_button("UnFilter","_self",
      $Sanitizer->get_url('',array('admin_structure')), 'query.png')
		:control_button("&nbsp","","","none")).

	    ((strpos($_SERVER["SCRIPT_NAME"],"adm_standard.php")!==false)
		?control_button("Export","_new",$url."&adm_view_type=ascii", "text.png")
		:control_button("&nbsp","","","none")).
		
	    tag_close("td"). 
	    tag_close("tr"). 
	    tag_close("thead");
}

function adm_standard_edit_delete($filter, $id = NULL, $view_name = '')
{
  global $Sanitizer;

  $url = $Sanitizer->get_url('','all', 
    array('actionid' => $id, 'filter' => $filter), array('action')). '&action=';

  return
    tag('td', "action_$id", 'action').
    control_button ('Edit','_self',$url.'edit','edit.png',"edit_$id").'&nbsp;&nbsp;'.
    control_button ("Del","_self",$url."delete","delete.png","delete_$id", 
    "return confirm('Are you sure you want to Delete record ID ".$id."');").
    
      (($view_name!==false)
    ?control_button ((empty($view_name)?"View":$view_name),"_self",$url."view","query.png"):"").
      tag_close("td");
}

function adm_standard_submit_cancel($submit_value = 'Submit', $cancel_value='Cancel')
{
  return 
    adm_form_submit($submit_value).
    tag ('input', '' ,'', 
    " value='".$cancel_value."' type='button'".
    " OnClick='javascript: location = location+\"&action=list\";'", false);
 }

    function adm_frame_menu_split ($adm_name,$standard = 0) { 

	if ($standard==1) 
	    $url = "adm/adm_standard.php?admin_structure=$adm_name&filter=".$GLOBALS["actionid"];
	else 
	    $url = "adm/adm_$adm_name.php?filter=".$GLOBALS["actionid"];

	adm_frame_menu (
	    "40%", urlencode("adm/".basename($_SERVER["SCRIPT_NAME"])."?".$_SERVER["QUERY_STRING"]."&action="),
	    "60%", urlencode($url));
    }

function adm_frame_menu($size1,$url1,$size2,$url2)
{
  global $Config;

	$url = $Config->get('jffnms_rel_path')."/admin/menu_frame.php?size1=$size1&scroll1=yes&name1=$url1&size2=$size2&scroll2=yes&name2=$url2";

	echo 
	  script("
		if ((parent.frames[1]) && (parent.frames[1].name!='work'))
		    parent.location = '$url';
		else
		    document.location = '$url';
	  ");
	die();
}

    function adm_form($action = "",$method="POST",$target = "_self", $echo = true, $form_id = "") {
	global $SCRIPT_NAME, $id, $actionid;

	if (!isset($id)) $id = $actionid;
	$global_vars = array("init", "span", "filter","admin_structure","filter_field","filter_value","sf","so");

	$result = 
	    form ($form_id, $SCRIPT_NAME, $method, $target).
	    hidden("actionid",$id).
	    (!empty($action)?hidden("action",$action):"");
	
	foreach ($global_vars as $var)
	    if (isset($GLOBALS[$var]))
		$result .= hidden($var, $GLOBALS[$var]);

	if ($echo) 
	    echo $result;

	return $result;
    }

?>
