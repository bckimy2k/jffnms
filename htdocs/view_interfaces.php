<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require ('auth.php');  

{

# Get the following from the request
$action = $Sanitizer->get_string('action', 'view');
$break_by_card = $Sanitizer->get_int('break_by_card');
$break_by_host = $Sanitizer->get_int('break_by_host');
$break_by_zone = $Sanitizer->get_int('break_by_zone');
$client_id = $Sanitizer->get_int('client_id', 0);
$dhtml = $Sanitizer->get_string('dhtml');
$mark_interface = $Sanitizer->get_string('mark_interface');
$map_id = $Sanitizer->get_int('map_id',1);
$view_type = $Sanitizer->get_string('view_type', 'normal');
$source_type = $Sanitizer->get_string('source', 'interfaces');

$client_id = 0;
if ($client_id_profile = profile("CUSTOMER")) $client_id = $client_id_profile; //fixed customer

if ($source_type == 'hosts' || $source_type == 'maps')
{
  $break_by_card = 0;
  $break_by_host = 0;
  $break_by_zone = 0;
}

if ($map_id > 1 && $source_type == 'interfaces' && $view_type == 'normal')
  $view_type = 'dynmap';

require_once("views/view_$view_type.class.php");
$view_class_name = "View_$view_type";
$View = new $view_class_name();

// Setup source
require_once("views/source_$source_type.class.php");
$source_class_name = "Source_$source_type";
$Source = new $source_class_name($View);

$interfaces_shown = 0; //number of interfaces shown

if ($action=='save')
  $View->save();

$View->html_init();
    
//load items source
$items = $Source->get($View, $client_id);

$alarms_actual = array();
$old_zone = '';
$old_host = '';
$old_card = '';
if (count($items) > 0) //if there were items returned
  foreach($items as $item)
  {

//Array ( [alarm] => [alarm_name] => [alarm_start] => [alarm_stop] => [alarm_type_description] => [alarm_type_id] => [bgcolor_aux] => [check_status] => 1 [client_id] => 1 [client_name] => Unknown Customer [db_break_by_card] => 0 [default_graph] => rtt [description] => Array ( ) [fgcolor_aux] => [have_graph] => 1 [have_tools] => 0 [host] => 2 [host_ip] => 172.16.242.2 [host_name] => cisco [id] => 2 [index] => [interface] => [interval] => 300 [make_sound] => 1 [map_int_id] => [map_x] => [map_y] => [pings] => 50 [shortname] => Unknown [show_rootmap] => 1 [threshold] => 70 [type] => Reachable [type_id] => 20 [zone] => sdfsd [zone_id] => 3 [zone_image] => unknown.png [zone_shortname] => UNK )

    if ($item['show_rootmap'] > 0) //if its meant to be shown
    {
      unset($alarm);
      unset($alarm_name);
      //debug ($item);

      //clean the interface name
      $interface = str_replace(array('"',"'"),'',$item['interface']);

      list ($item['int_sname'], $item['card']) = interface_shortname_and_card ($interface, $item['type'], $item['db_break_by_card']); //get short names for interface and card
      $urls = $Source->urls($View, $item);
    
      if (property_exists($View, 'cols_count'))
      {
        //break the current row because something has changed
        if ( (($break_by_zone==1)  && ($old_zone != $item['zone_id'])) 
          || (($break_by_host==1)  && ($old_host != $item['host']))
          || (($break_by_card==1)  && ($old_card != $item['card'])))
          $View->cols_count = $View->cols_max;

        if ($View->cols_count==$View->cols_max) //when we get to the end of the row
        {
          $View->break_finish_row();
          $View->cols_count=1; //set current column count to 1
        }

        if ($View->cols_count==1) //if this is the first column in a new row
        {
          if (
            (($break_by_zone==1)  && ($old_zone!=$item['zone_id'])) ||
            (($break_by_host==1)  && ($old_host!=$item['host'])) || 
            (($break_by_card==1)  && ($old_card!=$item['card'])) )  
          {
            $View->break_init();
    
            //include the new row header
            if ($break_by_zone==1)
              $View->break_by_zone($item);
            if ($break_by_card==1)
              $View->break_by_card($item);
            if ($break_by_host==1)
             $View->break_by_host($item);
            $View->break_show($urls);
          } else 
            $View->break_next_line_span($break_by_host, $break_by_zone, $break_by_card); //if new row but not because of a break
        } //cols = 1
      }
      if ($item['alarm_name'] == '')
        $item['alarm_name'] = 'OK';
      if ($item['alarm'] != NULL) //interface is alarmed
      {
        $bgcolor = $item['bgcolor_aux'];   //take colors from the item
        $fgcolor = $item['fgcolor_aux'];
        if ($item['make_sound'] == 1)
        {
          $tmp_alarm = $item['alarm'];
          $alarms_actual[$tmp_alarm][]=$item['id']; //if make_sound active for this interface, put the id in the alarms list
        }
      } else {
        $bgcolor='64FF64'; //use standard colors for non alarmed interfaces (green)
        $fgcolor='000000';
      }
      if ($item['show_rootmap'] == 2) //if its "Mark Disabled"
      {
        $bgcolor_status = $bgcolor; //set small box color to the real bgcolor
        $bgcolor='777777'; //set disabled colors (gray)
        $fgcolor='222222';
      }
      if ($View->interface_show($item, $bgcolor, $fgcolor, $mark_interface, $urls)) //show the interface
        $interfaces_shown++;
  
      if (property_exists($View, 'cols_count'))
      {
        //save current zone, host or card to compare it to a new one
        if ($break_by_zone==1) $old_zone = $item['zone_id'];
        if ($break_by_host==1) $old_host = $item['host'];
        if ($break_by_card==1) $old_card = $item['card'];
        $View->cols_count++;
      }
    } // show rootmap
  }//while items
    
//view_type_finish($view_type);

$View->finish();
if ($interfaces_shown == 0)     //if no interface were shown
  $View->no_interfaces($source_type);
    
$url = view_interfaces_alarm($action, $alarms_actual);

if ($Sanitizer->get_string('norefresh') !=1) //dont refresh if we're ask not to
  echo javascript_refresh("if (self.no_refresh!=1) location.href=\"$url\";",$Config->get('map_refresh')); 

adm_footer();
}
    
function view_interfaces_alarm($action, $alarms_actual)
{
  global $Sanitizer, $Config;

  $sound = $Sanitizer->get_int('sound');
  $alarms_last = unserialize(stripslashes($Sanitizer->get_special('alarms_last','')));
  $alarms_time = $Sanitizer->get_string('alarms_time','');
  $map_sound_renew_time = $Config->get('map_sound_renew_time');
  if ($map_refresh_profile = profile('MAP_REFRESH'))
    $map_refresh = $map_refresh_profile;
  else
    $map_refresh='';

  if ($action != 'view' || $sound != 1)
    return;

  $alarms_diff = array();
  if ((time() > $alarms_time+($map_sound_renew_time*60)) && ($map_sound_renew_time > 0)) //if the alarms are expired 
    unset($alarms_last); //delete the last alarms so we play the currents again
  if (!isset($alarms_last))   $alarms_last=array();

  $array_aux = array_merge(array_keys($alarms_actual),array_keys($alarms_last)); //merge both keys

  foreach ($array_aux as $key) //every different alarm_state id (new or old)
  {
    if (!isset($alarms_last[$key]))
      $alarms_last[$key]=array();   //if not set, set it empty
    if (!isset($alarms_actual[$key]))
      $alarms_actual[$key]=array();   //if not set, set it empty
  
    //get the diff of both alarms list (in = exists in actual, and not in last), (out = exists in last but not in actual)
    $alarms_diff[$key]['out'] = array_diff($alarms_last[$key],$alarms_actual[$key]);
    $alarms_diff[$key]['in'] = array_diff($alarms_actual[$key],$alarms_last[$key]);
  }
  unset($alarm_api);
  if ((count($alarms_diff) > 0) && ($alarms_time)) //if there is a diff and this is not the first call 
    foreach ($alarms_diff as $alarm_state_id=>$diff_items) //go thru all diffs
      if ((count($diff_items['in']) > 0) || (count($diff_items['out']) > 0)) //if there's something to do
      {
        if (!isset($alarm_api)) $alarm_api = $jffnms->get('alarm_states'); //get the api handler
          $sounds = current($alarm_api->get_all($alarm_state_id)); //get the record for this alarm state id

        if (count($diff_items['in']) > 0) //if there are IN items in this alarm state
          echo "<!-- Alarm: IN $alarm_state_id -->\n".play_sound($sounds["sound_in"])."\n"; //play the sound

        if (count($diff_items['out']) > 0) //if there are OUT items in this alarm state
          echo "<!-- Alarm: OUT $alarm_state_id -->\n".play_sound($sounds["sound_out"])."\n"; //play the sound
    
          $alarms_time=time(); //set alarm last time to now
      }
  if (!isset($alarms_time)) $alarms_time=time(); //if this is the first time we've been called set the alarm last time to now
  //get the new values in the url for the refresh
  $url = $Sanitizer->get_url('', 'all',
    array('alarms_last' => serialize($alarms_actual), 'alarms_time' => $alarms_time), 
  array('alarms_last', 'alarms_time'));
  return $url;
}//alarm sound processing
?>
