<?php
require_once('source_base.class.php');

class Source_maps extends Source_base
{
  private $Maps;

  function __construct(&$View)
  {
    parent:: __construct($View);

    $View->sizex *= 1.3;
    $View->sizey *= 1.3;
    $View->cols_max = round($View->screen_size/$View->sizex)-1;
    if ($View->cols_max < 1) $View->cols_max = 1;
    $View->cols_count = $View->cols_max;
    $this->Maps = new JffnmsMaps();
  } //construct()

  function get(&$View, $client_id)
  {
    $Interfaces = new JffnmsInterfaces();
    $map_count = $this->Maps->get();

    if ($map_count < 1)
      return;

    $items = array();
    while ($map = $this->Maps->fetch())
    {
      if ($map['id'] <= 1 ) continue;
      if ($View->map_id != 1 && ($View->map_id != $map['id'])) continue;

      $alarms = $this->Maps->status($Interfaces, $map['id']);
      list ($status, $bgcolor, $fgcolor, $status_long, $alarm_name) = alarms_get_status ($alarms);
  
      if ($View->active_only == 1 && count($alarms) <= 1)
        continue;

      $item_alarm = NULL;
      $item_fgcolor = NULL;
      $item_bgcolor = NULL;

      if ($alarm_name  && array_key_exists($alarm_name, $alarms))
      {
        if ($alarms[$alarm_name]['alarm_id'] != ALARM_UP)
          $item_alarm = $alarms[$alarm_name]['alarm_id'];
        $item_fgcolor = $alarms[$alarm_name]['fgcolor'];
        $item_bgcolor = $alarms[$alarm_name]['bgcolor'];
      }

      $items[] = array(
        //required info
        'id'=>$map['id'],
        'host'=>$map['id'],
        'make_sound'=>1,
        'show_rootmap'=>1,
        'check_status'=>1,
        'db_break_by_card'=>0,
        'type'=>'Maps',
        'interface'=>'Map'.$map['id'],
        
        'alarm'=> $item_alarm,
        'fgcolor_aux'=> $item_fgcolor,
        'bgcolor_aux'=> $item_bgcolor,
        'alarm_name'=>(($alarm_name!='total')?$alarm_name:NULL),
        'alarm_type_id'=>0, //fixed, use only for administrative
        
        //internal info
        'map_status'=>$status,
        'map_status_long'=>$status_long,
        'map_name'=>$map['name']
      );
    }//while $map

    if (count($items) > 0)
      array_key_sort($items, array('map_name'=>SORT_ASC));
    return $items;
  } //get()

  function dhtml(&$View, &$item)
  {
    $text_to_show = array($item['map_name'], $item['map_status']);
    $infobox_text = join('<br>', $text_to_show).'<br /><br />';
    foreach ($item['map_status_long'] as $alarm_key => $qty)
      $infobox_text = html('b',ucwords($alarm_key), '', '', '', FALSE, TRUE).
      ':'.$qty.br();
    $infobox_text = str_replace("\n",'', $infobox_text);
    return array($text_to_show, $infobox_text);
  } //dhtml()

  function normal(&$View, &$item)
  {
    $text_to_show = array($item['map_name'], $item['map_status']);

    $infobox_text = '';
    foreach ($item['map_status_long'] as $status_key => $qty)
      $infobox_text .= '<b>'.ucwords($alarm_key)."</b>: $qty<br>";
    $infobox_text = str_replace("\n","",$infobox_text);
    return array($text_to_show, $infobox_text);
  } //normal()

  function text(&$View, &$item)
  {
    $item_text ="<b>".str_pad($item['map_name'],30)."</b> ".str_pad("<u>$item[map_status]</u>",20);
    return array('', $item_text);
  } // text()

  function urls(&$View, &$item)
  {
      $id = $item['id'];
      $urls = array(
        'events' => array('Events', "events.php?map_id=$id", 'text.png'),
      'map' => array ('View Interfaces', "frame_interfaces.php?break_by_card=0&break_by_zone=0events_update=0&map_id=$id", 'int1.png')
    );
    return $urls;
  } //urls()
}
