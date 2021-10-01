<?php

require_once('source_base.class.php');

class Source_interfaces extends Source_base
{
  public $source_type = 'interfaces';
  public $Interfaces;

  private $permit_modification = -1;

  function __construct(&$View)
  {
    $this->Interfaces = new JffnmsInterfaces();
    $this->permit_modification = profile('ADMIN_HOSTS');
    parent:: __construct($View);
  }

  function infobox(&$item)
  {
    $formatted_desc = '';
    foreach($item['description'] as $desc => $value)
      $formatted_desc .= "<br /><b>$desc:</b> ".htmlentities($value);
    $description = join(' ',$item['description']);

    $alarm_text = '';
    if ($item['alarm_name'] != 'OK')
      $alarm_text = " <i>($item[alarm_name])</i>";

    $infobox_text = "<b>$item[host_name]</b> $item[zone]<br /><b>$item[interface]</b> ".
      $alarm_text.
      "$formatted_desc<br />$item[client_name] ".
      (($this->permit_modification==1)?"<a target=events href=admin/adm/adm_interfaces.php?interface_id=$item[id]&action=edit>($item[type] $item[index])</a>":"($item[type] $item[index])");
    ///FIXME this has to be more generic
    if (isset($item['bandwidthin']))
      $infobox_text .= "<br>D".($item['bandwidthin']/1000)."k/U".($item['bandwidthout']/1000)."k";
  
  if ($item['alarm_name']!==NULL)
    $infobox_text .= "<br><font size=2><b>Alarm:</b> ".$item['alarm_type_description']." ".ucwords($item['alarm_name']).' since '.$item['alarm_start'].'</font>';
    return array($description, $infobox_text);
  }

  function dhtml(&$View, &$item)
  {
    list ($description, $infobox_text) = $this->infobox($item);

    $text_to_show = array($item['int_sname'], ucfirst($item['shortname']),
      $description);
    
    if (($View->active_only==1) && ($this->host_id < 1)) //if we're in the alarmed interfaces screen, and not filtering by host
      $text_to_show = array_merge(array($item['host_name']), $text_to_show); //add the host name as the first line of the graph
    return array($text_to_show, $infobox_text);
  } //dhtml

  function normal(&$View, &$item)
  {
    list ($description, $infobox_text) = $this->infobox($item);
    $text_to_show = array($item['int_sname'], ucfirst($item['shortname']), $description);
    if (($View->active_only==1) && ($View->host_id < 1)) //if we're in the alarmed interfaces screen, and not filtering by host
      $text_to_show = array_merge(array($item['host_name']), $text_to_show); //add the host name as the first line of the graph
    return array($text_to_show, $infobox_text);
  } // normal()

  function performance(&$View, &$item)
  {
    list ($description, $infobox_text) = $this->infobox($item);
    $text_to_show = array(substr($item['int_sname'].' - '.$item['shortname'],0,20));
    return array($text_to_show, $infobox_text);
  } //performance()

  function text(&$View, &$item)
  {
    $infobox_text = str_pad("<b>$item[host_name] $item[zone_shortname] $item[int_sname]</b> ".ucfirst($item['shortname'])." ".join(' ',$item['description']),80);
    return array('',$infobox_text);
  } // text()

  function get(&$View, $client_id)
  {
    $items = array();
    if ($View->map_id > 1) $this->only_rootmap = 0;
  
    $interfaces_filter = array(
      'map'=>$View->map_id, 'host'=>$View->host_id, 'in_maps'=>$this->only_rootmap,
      'map_order'=>1, 'only_active'=>$View->active_only, 'alarms_summary'=>0,
      'with_field_type'=>1, 'client'=>$client_id);

    $alarms = $this->Interfaces->status(NULL,$interfaces_filter); //get alarm list
    $ids = array_keys($alarms); //get first list of ids
  
    if (count($ids)==0) $ids[]=1; //if no alarms found, forbid the interface list from returning all the interfaces

    $interfaces_filter['graph_fields']=1; //get graph data
    $interfaces_filter['map_cnx']=$View->map_id; //get map conexion information

    if ($View->map_id > 1) $ids[]=1; //allow id = 1 for maps cnx
    $ints = $this->Interfaces->get_all($ids,$interfaces_filter); //get interface data

    if (array_key_exists('field_types', $ints) && is_array($ints['field_types']))
    {
      $all_types_fields = $ints['field_types'];
      unset ($ints['field_types']);
    }
  
    $ids = array();
    foreach ($ints as $pos_id => $tmp_int)
      $ids[] = array('int_id'=>$tmp_int['id'],'pos_id'=>$pos_id); //make a interface id list
    foreach ($ids as $aux_id) 
    {
      $alarm_data = $alarms[$aux_id['int_id']]; //get alarm data
      $item = $ints[$aux_id['pos_id']]; //get interface data

      list ($aux1, $bgcolor, $fgcolor, $aux1, $alarm_name) = alarms_get_status ($alarm_data); //process alarms

      $alarm_name = ($alarm_name=="total")?NULL:$alarm_name;

      $aux=array(
        //required
        "id"=>$item["id"],
        "interface"=>$item["interface"],
        "host"=>$item["host"],
        "make_sound"=>$item["make_sound"],
        "type"=>$item["type_description"],
        "type_id"=>$item["type"],
        //if the host is in show, use the interface show field, if its not, use the host field 
        "show_rootmap"=>($item["zone_show"]==1?(($item["host_show"]==1)?$item["show_rootmap"]:$item["host_show"]):$item["zone_show"]),
        "check_status"=>$item["check_status"],

        //alarm
        "alarm_name"=>$alarm_name,
        "alarm_type_id"=>($alarm_name)?$alarm_data[$alarm_name]["type_id"]:NULL,
        "alarm_type_description"=>($alarm_name)?$alarm_data[$alarm_name]["type"]:NULL,
        "alarm_start"=>($alarm_name)?$alarm_data[$alarm_name]["start"]:NULL,
        "alarm_stop"=>($alarm_name)?$alarm_data[$alarm_name]["stop"]:NULL,
        "alarm"=>($alarm_name)?$alarm_data[$alarm_name]["alarm_id"]:NULL,
        "bgcolor_aux"=>($alarm_name)?$bgcolor:NULL,
        "fgcolor_aux"=>($alarm_name)?$fgcolor:NULL,
        //internal
        //host
        "host_name"=>$item["host_name"],
        "host_ip"=>$item["host_ip"],

        //client
        "client_id"=>$item["client"],
        "client_name"=>$item["client_name"],
        "shortname"=>($item["client_shortname"]?$item["client_shortname"]:$item["client_name"]),

        //zone
        "zone"=>$item["zone_name"],
        "zone_id"=>$item["zone"],
        "zone_shortname"=>$item["zone_shortname"],
        "zone_image"=>$item["zone_image"],

        //type
        "have_graph"=>$item["have_graph"],
        "have_tools"=>$item["have_tools"],
        "db_break_by_card"=>$item["db_break_by_card"],
        "default_graph"=>$item["default_graph"],

        //map
        "map_int_id"=>($View->map_id>1)?$item["map_int_id"]:NULL,
        "map_x"=>($View->map_id>1)?$item["map_x"]:NULL,
        "map_y"=>($View->map_id>1)?$item["map_y"]:NULL
      );

      //Interface Type Specific Fields Management
      $fields = &$all_types_fields[$item["type"]];

      $aux_description = array();
      if (is_array($fields) && ($item['id'] > 1))  
        foreach ($fields as $fname=>$fdata)
          switch ($fdata['type'])
          {
          case 7:
            if (!empty($item[$fname])) 
              $aux_description[$fdata['description']] = //Sanitize the Description
              str_replace("\r\n","",nl2br(htmlspecialchars($item[$fname])));
            break;
          case 3:
            $aux['index'] = $item[$fname];
            break;
          case 8:
            $aux[$fname] = $item[$fname];
            break;
          }
      $aux['description'] = $aux_description;
      ksort($aux);
      $items[]=$aux;
    }//foreach ids
    return $items;
  } // get()

  function urls(&$View, &$item)
  {
    global $Config;
    $jffnms_administrative_type = $Config->get('jffnms_administrative_type');
    $urls = array();

    $id = $item['id'];
    $urls['events'] = array('Events', "events.php?map_id=$View->map_id&express_filter=interface,$item[interface],=^host,$item[host],=", 'text.png');
    if ($item['have_graph']==1)
      $urls['map'] = array('Performance', "view_performance.php?interface_id=$item[id]", 'graph.png');
    if ($item['have_tools']==1)
      $urls['tools'] = array('Tools', "admin/tools.php?interface_id=$item[id]", 'tool.png');
    if ($this->permit_modification == 1)
    {
      $urls['modification'] = array('Edit', "admin/adm/adm_interfaces.php?interface_id=$item[id]&action=edit", 'edit.png');
      if (($item['alarm_type_id'] == $jffnms_administrative_type) && ($item['client_id']==1)) //force interface configuration when is autodiscovered.
        $urls = array ('events'=>array('Edit', "admin/adm/adm_interfaces.php?interface_id=$item[id]&action=edit", 'edit.png'));
    }
    return $urls;
  }
} // class
