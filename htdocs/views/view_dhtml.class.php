<?php

require_once('view_normal.class.php');
require_once('toolbox.inc.php');

class View_dhtml extends View_normal
{
  private $first_time = TRUE;
  private $html_separation_x = 10;
  private $html_separation_y = 7;
  private $html_current_x = 0;
  private $html_current_y = 10;
  private $charsize = 10;

  private $break_text;
  private $break_urls;
  private $break=1;

  function __construct()
  {
    parent::__construct();

    $this->sizey += 3;
    $this->cols_max-=1;
    $this->cols_count = $this->cols_max;
    $this->charsize = 10;
    if ($this->big_graph == 1)
      $this->charsize *= 1.5;
  } //View_dhtml()

  function break_by_card(&$item)
  {
    if ($item['db_break_by_card']) 
      $this->break_text = array("Card",$item['card']);
    else
      $this->break_text = array($item['card']);

    $this->break_urls = array();
    if ($item['have_graph'] == 1)
      $this->break_urls['map'] = array('Performance',"view_performance.php?type_id=$item[type_id]&host_id=$this->host_id",'graph.png');
  } // break_by_card()

  function break_by_zone(&$item)
  {
    if ($item['zone_id'] > 1)
    {
      $this->break_text = array($item['zone'],'Zone',$this->add_image ($item['zone_image'],'br'));
      $this->break_urls = array('events'=>array('Events',"events.php?express_filter=zone,$item[zone_id],=",'text.png'));
    }
  } //break_by_zone()

  function break_finish_row()
  {
      if ($this->first_time)
          $this->first_time=FALSE;
      else
          $this->html_current_y += $this->sizey + $this->html_separation_y;
      $this->html_current_x = 0;
  }
  function break_next_line_span($break_by_host, $break_by_zone, $break_by_card)
  {
    if (($break_by_host==1) or ($break_by_zone==1) or ($break_by_card==1))
      $this->html_current_x += $this->html_separation_x * 2 + $this->sizex;
  } // break_next_line_span()

  function break_show($urls)
  {
    if (is_array($this->break_text))
    {
      $this->html_current_x += $this->html_separation_x;
      $bgcolor = 'rgb(150,150,150)';
      $fgcolor = 'white';
      $text_to_show = join(br(),$this->break_text);
      if (array_key_exists('events', $this->break_urls))
        $event_url = $this->break_urls['events'][1];
      else
        $event_url = $urls['events'][1];
      if (array_key_exists('map', $this->break_urls))
        $map_url = $this->break_urls['map'][1];
      else
        $map_url = $urls['map'][1];
      $events = " onClick=\"javascript:ir_url('$event_url','$map_url')\"";
      $this->show_div ("b".$this->break++, $fgcolor, $bgcolor, $text_to_show, $events);
      $this->html_current_x += $this->sizex + $this->html_separation_x;
    }
  } // break_show()

  function html_init()
  {
    adm_header('Alarm Map', $this->map_color);
    // Popup Delays
    $base_delay = 1000;
    $show_delay = $base_delay*1;
    $hide_delay = $show_delay*4; //take 4 times more time to hide than to show

    echo 
  script("
var shown_objects = new Array();
var lastzIndex = 1;

function show_info(o,text) {
    oid = o.id;
    
    //hide all other boxes. already shown or not
    for (var i in shown_objects)
  if ((shown_objects[i]!=null) && (i!=oid)) {
  
      if (shown_objects[i]['state']==0)   //if it was not shown yet
    shown_objects[i] = null;  //just delete it, to avoid it to be shown
      else
    window.setTimeout (\"real_hide_info('\"+i+\"');\",".$hide_delay."); //hide it normally
  }
  
    if (!shown_objects[oid]) { //if not's already created
      shown_objects[oid] = Array();
      shown_objects[oid]['obj'] = o;
      shown_objects[oid]['state'] = 0;

      window.setTimeout (\"real_show_info('\"+text+\"','\"+oid+\"');\",".$show_delay."); //delay real show
    }
}

function real_show_info(text,oid) { 
    a = shown_objects[oid];
    if (!a) return 1; //if exists
    
    o = a['obj'];
    if (!o) return 2; //if its valid;
    
    if (shown_objects[oid]['state']!=0) return 3; //don't do it again

    shown_objects[oid]['state']=1;

    //save values for restore
    shown_objects[oid]['old_x'] = o.style.left;
    shown_objects[oid]['old_y'] = o.style.top;
    shown_objects[oid]['old_w'] = o.style.width;
    shown_objects[oid]['old_h'] = o.style.height;
    shown_objects[oid]['old_text'] = o.innerHTML;
    shown_objects[oid]['old_size'] = o.style.fontSize;
    shown_objects[oid]['old_border'] = o.style.borderStyle;

    //save current position values
    x = parseInt(o.style.left.replace('px',''));
    y = parseInt(o.style.top.replace('px',''));
    w = parseInt(o.style.width.replace('px',''));
    h = parseInt(o.style.height.replace('px',''));

    o.style.width = 'auto' ;
    o.style.height = 'auto' ;
    o.style.overflow = 'visible';
    o.style.zIndex = lastzIndex++;

    o.innerHTML = text;
    o.style.fontSize = '".round($this->charsize*1.6)."px';
    o.style.lineHeight = o.style.fontSize;

    o.style.borderStyle = 'solid';

    //re-center the box
    o.style.left = 0;
    o.style.top = 0;

    new_w = o.offsetWidth;
    new_h = o.offsetHeight;
    
    new_x = x + (w/2) - (o.offsetWidth/2)
    new_y = y + (h/2) - (o.offsetHeight/2)
    o.style.left = new_x ;
    o.style.top  = new_y;


    //Fix Position
    win_w = window.document.body.clientWidth;
    win_h = window.document.body.clientHeight;
    win_t = window.document.body.scrollTop;
    win_l = window.document.body.scrollLeft;
    dif = 2;
    
    //fix the scroll values
    new_y -= win_t;
    new_x -= win_l;
    
    if ((new_x + new_w) >= win_w)
  o.style.left = win_w - new_w - dif + win_l;

    if ((new_y + new_h) >= win_h)
  o.style.top = win_h - new_h - dif + win_t;
    
    if (new_x <= dif) 
  o.style.left = win_l + dif;

    if (new_y <= dif) 
  o.style.top  = win_t + dif;

    x = parseInt(o.style.left.replace('px',''));
    y = parseInt(o.style.top.replace('px',''));

    toolbox_show(oid,x,new_w,y+new_h);
    return 0;
}

function hide_info(o) {
    window.setTimeout (\"real_hide_info('\"+o.id+\"');\",".$hide_delay."); //delay real hide
}

function real_hide_info(oid) {
    a = shown_objects[oid]
    if (!a) return 1; //if exists

    o = a['obj'];
    if (!o) return 2; //if its valid

    if (shown_objects[oid]['state']!=1) return 3; //if is shown
    
    o.innerHTML = shown_objects[oid]['old_text'];
    o.style.fontSize = shown_objects[oid]['old_size'];
    o.style.lineHeight = o.style.fontSize;
        
    o.style.borderStyle = shown_objects[oid]['old_border'];

    o.style.width = shown_objects[oid]['old_w'];
    o.style.height = shown_objects[oid]['old_h'];
    o.style.left = shown_objects[oid]['old_x']
    o.style.top  = shown_objects[oid]['old_y']

    o.style.overflow = 'hidden';
    o.style.zIndex = 0;
    
    shown_objects[oid] = null;

    toolbox_hide(oid);

    return 0;
}

function ir_url(url,url2){
    if (url!='') {
  if (top.work && top.work.events) 
      top.work.events.location.href = url; //if events frame exists use it
  else 
      if (url2=='') 
    url2=url; //if it doesnt and this is the only url take it to the main frame
//      else
//    window.open(url); //if the events frame doesn't exists, but we have to show 2 urls, show this one in a new window
    }
    
    if (url2!='') {
  if (top.work && top.work.map)
      top.work.map.location.href = url2;
  else
      if (top.work)
    top.work.location.href = url2; //y esto que si no esta map salga en work
      else
    window.open(url2); //esto hace que si no esta la frame salga una window
    }
}").

    html ("script", "", "", "", "src='views/toolbox.js'").

    html ("style","
.interface {
    position:absolute; 
    border-width: 1px; 
    border-color: black;
    border-style: solid;
    font-size: ".$this->charsize."px; 
    line-height: ".$this->charsize."px; 
    font-family: sans-serif,monospace,arial; 
    letter-spacing: 0px; 
    white-space: nowrap;
    margin: 0px 0px 0px 0px; 
    padding: 3px 0px 0px 0px;
    overflow: hidden; 
    float: right;
    align: left;
    word-wrap: none;
    text-align: center;
    cursor: pointer;
}","","","type='text/css'").

    script("
    //FIX IE Things
    if (document.all) {
  document.styleSheets[0].rules[0].style.paddingTop = '0px';
        document.styleSheets[0].rules[0].style.cursor = 'hand';
    }");
  } // html_init()

  // returns boolean if we show it
  function interface_show(&$item, $bgcolor, $fgcolor, $mark_interface, $urls)
  {
    global $Source;

    if ( ($item['id'] <= 1) || ($item['host'] <= 1))
      return FALSE;
    $this->html_current_x += $this->html_separation_x;

    list($text_to_show, $infobox_text) = $Source->dhtml($this, $item);
    if (($item['show_rootmap'] == 2) || ($item['check_status'] == 0)) //if its "Mark Disabled"
    {
      $small_box = $this->sizex*0.13;
  
      if ($item['show_rootmap'] == 2) $small_box_color = $bgcolor;
      if ($item['check_status'] == 0) $small_box_color = "777777";
      
      $text_to_show[] = html('div', '', '', '', "style='position:absolute; top:0; left:".($this->sizex-$small_box).'; '.
    "width: $small_box; height: $small_box; background-color: $small_box_color;'");
    }
    $text_to_show = trim(str_replace("\n","",join(br(),$text_to_show)));
    if ($item['id'] == $mark_interface)
      $border = 'border-style: double;';
    else
      $border = '';

    if (array_key_exists('events',$urls))
        $events_url = $urls['events'][1];
    else
        $events_url = '';
    if (array_key_exists('map',$urls))
        $maps_url = $urls['map'][1];
    else
        $maps_url = '';

    $events = " onClick=\"javascript:ir_url('$events_url','$maps_url')\"";

    if ((strlen($infobox_text) > 1) && $this->popups)
      $events .= " onMouseOver=\"javascript: show_info(this,'$infobox_text');\" onMouseOut=\"javascript: hide_info(this);\"";
    $this->show_div ($item['id'], $fgcolor, $bgcolor, $text_to_show, $events, $border);
    echo view_toolbox($item['id'], $urls);
    $this->html_current_x += $this->sizex;
    return TRUE;
  }

  public function add_image ($image,$pos = "br")
  {
    global $Config;

    $jffnms_real_path = $Config->get('jffnms_real_path');
    $jffnms_rel_path = $Config->get('jffnms_rel_path');
  
    $zone_image_filename = $jffnms_real_path.'/htdocs/images/'.$image;
    if (!empty($image) && file_exists($zone_image_filename)!=FALSE)
    {
      list($aux_w,$aux_h) = @getimagesize($zone_image_filename);
      $zone_image_filename = $jffnms_rel_path.'/images/'.$image;
      if ($pos == 'br') // bottom right
      {
        $top = $this->sizey-$aux_h;
        $left = $this->sizex-$aux_w;
      } else {
        $top=$pos;
        $left = $this->sizex-$aux_w;
      }
      return html('div', '', '', '', "style='position:absolute; top: ".
        $top."px; left:".$left."px; width: ".$aux_w."px; height: ".
        $aux_h."px; ".
        "background-image: url(".$zone_image_filename."); z-index: -10;'");
    }
  } //add_image()

  private function show_div ($id, $fgcolor, $bgcolor, $text_to_show, $events="", $more_styles="")
  {
    echo html('div',$text_to_show, $id, 'interface', "style='".
      "top: $this->html_current_y; left: $this->html_current_x; width: $this->sizex; height: $this->sizey; ".
      "background-color: $bgcolor; color: $fgcolor; $more_styles' ".$events);
  } // show_div()

}

?>
