<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
{
include ('auth.php'); 

$active_only = $Sanitizer->get_int('active_only', 0);
$only_rootmap = $Sanitizer->get_int('only_rootmap', 1);
$map_id = $Sanitizer->get_int('map_id', 1);
$client_id = $Sanitizer->get_int('client_id', 0);

$break_by_zone = $Sanitizer->get_int('break_by_zone', 1);
$break_by_card = $Sanitizer->get_int('break_by_card', 0);
$break_by_host = $Sanitizer->get_int('break_by_host', 0);
$events_update = $Sanitizer->get_int('events_update');
$host_id = $Sanitizer->get_int('host_id');
$mark_interface = $Sanitizer->get_int('mark_interface');
$no_refresh = $Sanitizer->get_int('no_refresh', 0);
$source = $Sanitizer->get_string('source', 'interfaces');
$sound = $Sanitizer->get_string('sound');
$view_type = $Sanitizer->get_string('view_type',NULL);
$big_graph = $Sanitizer->get_string('big_graph', 0);

$Maps = new JffnmsMaps();

$REQUEST_URI = $Sanitizer->get_url('', 'all');
    
if ($map_id_profile = profile('MAP')) $map_id = $map_id_profile;
if ($client_id_profile = profile("CUSTOMER")) $client_id = $client_id_profile;
$sound = profile("MAP_SOUND", $sound);

$view_type = cookie('VIEW_TYPE', $view_type);
if (($view_type=='') && ($view_type_profile = profile('VIEW_TYPE_DEFAULT')))
  $view_type = $view_type_profile;
if ($view_type=='')   //If we coudn't get the view_type from the cookie or the profile 
  $view_type = 'dhtml';   //use the 'DHTML' view type
    
$events_update = ($events_update=="")?1:0;
    
switch ($source)
{  
  case 'interfaces':   $view_types = array (
            'normal'=>'Normal',
            'normal-big'=>'Normal Big',
            'text'=>'Text',
            'performance'=>'Performance',
            'graphviz'=>'GraphViz',
            'dhtml'=>'DHTML',
            'dhtml-big'=>'DHTML Big'
        );
        break;

  case  'maps'  :  
  case  'hosts'  :  $view_types = array (
            'normal'=>'Normal',
            'normal-big'=>'Normal Big',
            'text'=>'Text',
            'dhtml'=>'DHTML',
            'dhtml-big'=>'DHTML Big'
        );
        break;
}

$old_view_type = $view_type;  // for Select
    
switch ($view_type)
{
case 'normal':
  $big_graph=0;
  $image = 'normal.png';
  break;

case 'text':     
  $image = "text.png";
  break;
        
case 'performance':
  $break_by_host=0; 
  $break_by_card=0;
  $image = 'graph.png';
  break;

case 'graphviz':
  $break_by_card=0;
  $break_by_host=1; 
  $break_by_zone=0; 
  $image = '';
  break;

case 'normal-big':
  $big_graph=1;
  $view_type = 'normal';
  $image = 'normal.png';
  break;

case 'dhtml':
  $big_graph=0;
  $image = 'normal.png';
  break;

case 'dhtml-big':
  $big_graph=1;
  $image = 'normal.png';
  $view_type='dhtml';
  break;
}

$map = current($Maps->get_all($map_id));

$maps = $Maps->get(NULL,$map_id);
  
$maps_list = array($Sanitizer->get_url('')=>"Choose Map");
    
while ($rmaps = $Maps->fetch())
  if ($rmaps['id'] != 1) 
  {
    $maps_url = $Sanitizer->get_url('', 'all', 
      array('events_update' => $events_update, 'map_id' => $rmaps['id'],
      'break_by_card' => 0, 'map_color' => $rmaps['color']),
      array('host_id'));
    $maps_list[$maps_url] = $rmaps['name'];
  }

if (count($maps_list) > 1)
  $select_maps = select_custom("map", $maps_list, 0, "change_map_url(this)", 1, 0);
else
  $select_maps = "None";

$options = 
  "&map_id=".$map_id."&map_color=".$map["color"]."&mark_interface=".$mark_interface."&active_only=".$active_only.
  "&break_by_card=".$break_by_card."&break_by_host=".$break_by_host."&break_by_zone=".$break_by_zone."&break_by_card=".$break_by_card.
  "&view_type=".$view_type."&host_id=".$host_id."&sound=".$sound."&big_graph=".$big_graph."&only_rootmap=".$only_rootmap."&source=".$source."&client_id=".$client_id;
$url = "view_interfaces.php?$options";
    
adm_header('Interface Map List');

    echo 
  script(
"
    function change_view_type(select){
  var url = select.options[select.selectedIndex].value;
  location.href = location.href+'&view_type='+url;
        return true;
    }

    function change_map_url(select){
  var url = select.options[select.selectedIndex].value;
  location.href = url;
  return true;
    }

    function change_client(select) {
  var client_id = select.options[select.selectedIndex].value;
  location.href = location.href + '&no_refresh=0&events_update=1&client_id='+client_id;
  return true;
    }").
  table("map_list").
  tr_open().
  td(control_button ($map["name"],"_self","$REQUEST_URI&map_id=".$map["parent"]."&host_id=&break_by_card=0&events_update=".$events_update,"world.png")).

  ((($client_id_profile==0) && ($map_id_profile==0))
      ?td(control_button ("Submaps: ".$select_maps,"","","none")).
       td(control_button ("Customers: ".select_clients("", $client_id, 1, array(0=>"All"),"change_client(this)"),"","","none"))
      :"").

  td(control_button ( "Options:".
      linktext(image($image)."&nbsp;",$url,"map_viewer").
      select_custom("view_type",$view_types,$old_view_type,"change_view_type(this)",1,0),"","","none")).
    
  td(($no_refresh==1) 
      ?control_button("","_self","$REQUEST_URI$options&no_refresh=0&events_update=0","refresh.png")
      :control_button("","_self","$REQUEST_URI$options&no_refresh=1&events_update=0","refresh2.png")).

  td(($active_only==1)
      ?control_button("","_self","$REQUEST_URI$options&active_only=0&events_update=0","all.png")
      :control_button("","_self","$REQUEST_URI$options&active_only=1&events_update=0","alert.png")).

  td(control_button("","_new",$url,"popup.png")).

  td(($sound==1)
      ?control_button("","_self","$REQUEST_URI$options&sound=0&events_update=0","sound.png")
      :control_button("","_self","$REQUEST_URI$options&sound=1&events_update=0","nosound.png")).
  
  tag_close("tr").
  table_close().

  script(($no_refresh==1)
    ?"parent.map_viewer.no_refresh = 1;"
    :"parent.map_viewer.location.href = '$url'+'&screen_size='+window.document.body.clientWidth;").
    
  (($events_update==1)
      ?script("parent.parent.events.location.href = 'events.php?map_id=$map_id&refresh=&client_id=$client_id';")
      :"");

    adm_footer();
}
?>
