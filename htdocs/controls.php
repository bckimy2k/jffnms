<?php 
/* This file is part of JFFNMS
 * Copyright (C) 2002-2010 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once ('auth.php');
    
adm_header('Main Menu');

$view_mode = $Sanitizer->get_string('view_mode', profile('VIEW_DEFAULT'));
if (!$view_mode) 
  $view_mode = 'start';
  
$filtered = (profile('CUSTOMER') || profile('MAP'))?true:false;

$view_modes_list = array(
  'start'        => array('url'=>'start.php', 'name'=>'Start Page'),
  'hosts-events' => array('url'=>'frame_interfaces_events.php?source=hosts', 'name'=>'Hosts & Events'),
  'interfaces-events' => array('url'=>'frame_interfaces_events.php?source=interfaces', 'name'=>'Interfaces & Events')
);

if (!$filtered) 
  $view_modes_list['maps-events'] = array('url'=>'frame_interfaces_events.php?source=maps',        'name'=>'Maps & Events');

$view_modes_list['alarmed-hosts-events'] = array(  'url'=>'frame_interfaces_events.php?source=hosts&active_only=1',    'name'=>'Alarmed Hosts & Events');
$view_modes_list['alarmed-events'] = array(  'url'=>'frame_interfaces_events.php?source=interfaces&active_only=1',    'name'=>'Alarmed Interfaces & Events');
$view_modes_list['alarmed'] = array(  'url'=>'frame_interfaces.php?source=interfaces&active_only=1&events_update=0',  'name'=>'Alarmed Interfaces');
$view_modes_list['interfaces'] = array(  'url'=>'frame_interfaces.php?source=interfaces&events_update=0',    'name'=>'Interfaces');
$view_modes_list['hosts'] = array(    'url'=>'frame_interfaces.php?source=hosts&events_update=0',      'name'=>'Hosts');

if (!$filtered) 
  $view_modes_list['maps'] = array(  'url'=>'frame_interfaces.php?source=maps&events_update=0',      'name'=>'Maps');

$view_modes_list['hosts-all-int'] = array(  'url'=>'frame_interfaces.php?source=hosts&only_rootmap=0&events_update=0',  'name'=>'Hosts All Interfaces');
$view_modes_list['events'] = array(    'url'=>'events.php?span=40',              'name'=>'Events');

$view_modes = array();
foreach ($view_modes_list as $view_mode_name=>$aux)
  $view_modes[$view_mode_name]=$aux['name'];

echo 
  script ("

    function change_view_mode(select){
  var url = select.options[select.selectedIndex].value;
        location.href = location.href+\"&from_refresh=0&view_mode=\"+url;
  return true;
    }

    function toggle_menu() {
        fs = parent.document.getElementById('menu_frame');
  
  showed = '*,152';
  hidden = '*,0';

        fs.cols = (fs.cols!=showed)?showed:hidden;
    }
    ").
  table("mainmenu").
  tr_open().
  td(linktext(image("jffnms_small.png", 0, 0, "Small Logo")."JFFNMS",
    'http://www.jffnms.org/about/?version='. JFFNMS_VERSION.
    '&amp;site='.$Config->get('jffnms_site')
    ,'work','logo')).
  td(control_button($_SESSION['auth_user_fullname'],'work','admin/menu.php?menu=users','users.png')).
  td(  control_button("Views:","", $Sanitizer->get_url('',FALSE,
    array('from_refresh'=>0, 'view_mode'=> $view_mode)), "none").
    select_custom("view_mode",$view_modes,$view_mode,"change_view_mode(this)").
    linktext(image("bullet6.png"),$Sanitizer->get_url('',FALSE,
      array('from_refresh'=>0, 'view_mode' =>$view_mode)))).
  td(control_button("Performance","work","admin/menu.php?menu=performance","graph.png")).
  td((profile("ADMIN_ACCESS")?control_button("Administration","","javascript: toggle_menu();","lock.png"):"&nbsp;")).
  td(control_button('Logout','_top',$Config->get('jffnms_rel_path').'/?logout=1&amp;OldAuth='.$_SESSION['auth_user_name'],'logoff.png'),'logout').
  tag_close("tr").
  table_close().
  (($view_mode)?script ("parent.work.location.href = '".$view_modes_list[$view_mode]["url"]."'"):"");

    adm_footer();
?>
