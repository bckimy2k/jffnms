<?php

require_once('source_base.class.php');

class Source_hosts extends Source_base
{
  public $source_type = 'hosts';
  private $Hosts;
  private $Interfaces;

  private $permit_host_modification;

  function __construct(&$View)
  {
    parent:: __construct($View);

    $View->sizex *= 1.3;
    $View->sizex += 5;
    $View->sizey *= 1.43;

    if ($View->sizex > 0)
      $View->cols_max = round($View->screen_size/$View->sizex);

    $View->cols_count = $View->cols_max;
    $this->Hosts = new JffnmsHosts();
    $this->Interfaces = new JffnmsInterfaces();
  } //__construct()

  function get(&$View, $client_id)
  {
    $items = array();
    $host_count = $this->Hosts->get();

    if ($host_count < 1)
      return;

    while ($host = $this->Hosts->fetch())
    {
      if ($host['show_host'] < 1) continue; // Dont show this host
      if ($host['id'] < 2) continue;

      $alarms = $this->Hosts->status($this->Interfaces, $host['id'], $View->map_id, $this->only_rootmap, $client_id);
      if (($View->map_id != 1 || $client_id != 0) && $alarms['total']['qty'] == 0)
        continue;
      if ($View->active_only == 1 && count($alarms) <= 1)
        continue;

       list ($status, $bgcolor, $fgcolor, $status_long, $alarm_name) = alarms_get_status($alarms);

      $items[] = array(
        'id' => $host['id'],
        'host' => $host['id'],
        'make_sound'=>1,
        'show_rootmap'=>$host['show_host'],
        'check_status'=>$host['poll'],
        'db_break_by_card'=>0,
        'type'=>'Hosts',
        'interface'=>'Host'.$host['id'],
        
        //alarm info
        'alarm'=>((array_key_exists($alarm_name, $alarms) && $alarms[$alarm_name]['alarm_id']!=ALARM_UP)?$alarms[$alarm_name]['alarm_id']:NULL),
        'fgcolor_aux'=>(array_key_exists($alarm_name, $alarms)?$alarms[$alarm_name]['fgcolor']:''),
        'bgcolor_aux'=>(array_key_exists($alarm_name, $alarms)?$alarms[$alarm_name]['bgcolor']:''),
        'alarm_name'=>(($alarm_name!='total')?$alarm_name:NULL),
        'alarm_type_id'=>0, //fixed, use only for administrative
        
        //internal info
        'host_status'=>$status,
        'host_status_long'=>$status_long,
        'host_ip'=>$host['ip'],
        'host_name'=>$host['name'],
        'host_lat'=>$host['lat'],
        'host_lon'=>$host['lon'],
        'zone'=>$host['zone_description'],
        'zone_image'=>$host['zone_image']
      );
    } //while

    return $items;
  } //get()

  function dhtml(&$View, &$item)
  {
    if ($View->big_graph == 0)
      $View->cols_max = round($View->screen_size/$View->sizex)-1;

    $text_to_show = array($item['host_name'],$item['zone'],$item['host_status'],$item['host_ip']);

    $infobox_text = join(br(),$text_to_show).br().br();

    $text_to_show[] = $View->add_image($item['zone_image']);
    
    foreach ($item['host_status_long'] as $alarm_key=>$qty)
      $infobox_text .= html('b',ucwords($alarm_key),'', '', '', false, true).': '.$qty.br();
    $infobox_text = str_replace("\n","",$infobox_text);

    return array($text_to_show, $infobox_text);
  }

  function normal(&$View, &$item)
  {
    $text_to_show = array($item['host_name'],$item['zone'],$item['host_status'],$item['host_ip']);
    $infobox_text = '';
    foreach ($item['host_status_long'] as $alarm_key=>$qty)
      $infobox_text .= html('b',ucwords($alarm_key),'', '', '', false, true).': '.$qty.br();
    $infobox_text = str_replace("\n","",$infobox_text);
    return array($text_to_show, $infobox_text);
  }

  function text(&$View, &$item)
  {
    $item_text = str_replace("\n", '', str_replace("\t",'',
      html('b',str_pad($item['host_name'].' '.$item['zone'],30),
      '', '', '', false, true).' '.
      str_pad(html('u',$item['host_status'], '', '', '', false, true),20).' '.
      str_pad($item['host_ip'],15)));
    return array('', $item_text);
  } // text()

  function performance(&$View, &$item)
  {
    return array('','');
  } // performance()

  function urls(&$View, &$item)
  {
    $urls = array(
      'events' => array('Events', "events.php?map_id=$View->map_id&express_filter=host,$item[id],=" , 'text.png'),
      'map' => array('View Interfaces', "frame_interfaces.php?host_id=$item[id]&break_by_card=1&break_by_zone=0&active_only=$View->active_only&events_update=0&map_id=$View->map_id&only_rootmap=$this->only_rootmap",'int1.png'),
      'tools' => array('Tools', "admin/tools.php?host_id=$item[id]", 'tool.png')
    );

    if ($this->permit_host_modification == 1)
      $urls['modification'] = 
      array('Edit', "admin/adm/adm_standard.php?admin_structure=hosts&filter=$item[id]&actionid=$item[id]&action=edit", 'edit.png');

    return $urls;
  } // urls()
} // class
