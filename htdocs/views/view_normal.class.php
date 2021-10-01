<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('toolbox.inc.php');
require_once('view_base.class.php');

class View_normal extends View_base
{
  public $filename;


  protected $break_image;
  protected $break_filename;
  protected $break_bgcolor;
  protected $break_fgcolor;

  // Only for this class
  private $break_url = '';
  private $rnd;
  private $image_events;

  function __construct()
  {
    parent::__construct();

    if (($this->active_only==1) && ($this->host_id < 1)) //add one more line for host name in Alarmed Interfaces
      $this->sizey += 10;

    if ($this->big_graph==1) {
        $this->sizex *= 1.5;
        $this->sizey *= 1.5;
    }
  
    $this->cols_max=round(($this->screen_size/$this->sizex))-1;
    $this->table_width='100%';
    $this->cols_count=$this->cols_max;

    $this->rnd = substr(time(),5,10);

  } // View_normal

  function break_init()
  {
    $this->break_image = ImageCreate ($this->sizex, $this->sizey) or die ("Cannot Initialize new GD image stream");
    $this->break_bgcolor = ImageColorAllocate ($this->break_image, 150 ,150, 150);
    $this->break_fgcolor = ImageColorAllocate ($this->break_image, 255, 255, 255);
    $this->break_filename = '';
  } //break_init

  function break_by_card(&$item)
  {
    if ($item['db_break_by_card']) 
      ImageStringCenter ($this->break_image, $this->break_fgcolor, 0, array("Card",$item['card']),$this->big_graph);
    else
      ImageStringCenter ($this->break_image, $this->break_fgcolor, 0, $item['card'] ,$this->big_graph);
    
    $card_filename = str_replace(array(' ','/'), '_', $item['card']);
    $this->break_filename = "c".$card_filename."-$this->big_graph.png";

    if ($item['have_graph']==1)
      $this->break_url = "javascript:ir_url('','view_performance.php?type_id=$item[type_id]&host_id=$this->host_id')";
  } //break_by_card

  function break_by_host(&$item)
  {
    global $Config;
    $jffnms_real_path = $Config->get('jffnms_real_path');
    if ($item['host'] > 1)
    {
      $zone_image_filename = "$jffnms_real_path/htdocs/images/$item[zone_image]";
      if (!empty($item['zone_image']) && file_exists($zone_image_filename)!=FALSE)
      {
        $im_zone = ImageCreateFromPNG($zone_image_filename);
        list($aux_w,$aux_h) = @getimagesize($zone_image_filename);
        ImageCopy($this->break_image,$im_zone,
          ImageSX($this->break_image)-$aux_w,
          ImageSY($this->break_image)-$aux_h,0,0,$aux_w,$aux_h);
  
        imagedestroy($im_zone);
      }
      ImageStringCenter ($this->break_image, $this->break_fgcolor, 0,
        array($item['host_name'],$item['zone_shortname']),$this->big_graph);
      $this->break_filename = "h$item[host]-$this->big_graph.png";
      $this->break_url =  "javascript:ir_url('events.php?express_filter=host,$item[host],=','')";
    }
  } //break_by_host

  function break_by_zone(&$item)
  {
    global $Config;
    if ($item['zone_id'] > 1)
    {
      $jffnms_real_path = $Config->get('jffnms_real_path');
      $zone_image_filename = "$jffnms_real_path/htdocs/images/$item[zone_image]" ;
      if (!empty($item['zone_image']) && file_exists($zone_image_filename)!=FALSE)
      {
        $im_zone = ImageCreateFromPNG($zone_image_filename);
        list($aux_w,$aux_h) = @getimagesize($zone_image_filename);
        ImageCopy($this->break_image,$im_zone,
          ImageSX($this->break_image)-$aux_w,
          ImageSY($this->break_image)-$aux_h,0,0,$aux_w,$aux_h);
        imagedestroy($im_zone);
      }
      ImageStringCenter ($this->break_image, $this->break_fgcolor, 0, array($item['zone'],"Zone"),$this->big_graph);
        $this->break_filename = "z$item[zone_id]-$this->big_graph.png";
      $this->break_url = "javascript:ir_url('events.php?express_filter=zone,$item[zone_id],=','')";
    }
  } // break_by_zone

  function break_show($urls)
  {
    global $Config;
    $images_real_path = $Config->get('images_real_path');
    $images_rel_path = $Config->get('images_rel_path');
    if ($this->break_filename)
    { 
      ImagePng ($this->break_image,"$images_real_path/$this->break_filename");
      ImageDestroy($this->break_image);  
      $this->break_image = NULL;
      echo td (linktext(image("$images_rel_path/$this->break_filename",$this->sizex,$this->sizey),$this->break_url));
    }
  } //break_show()

  function break_next_line_span($break_by_host, $break_by_zone, $break_by_card)
  {
    echo td('&nbsp;');
  } // break_next_line_span()


  // Interface functions

  function interface_show(&$item, $bgcolor, $fgcolor, $mark_interface, $urls)
  {
    global $Config, $Source;
    $images_real_path = $Config->get('images_real_path');
    $interface_shown = FALSE;

    $int_filename = '';
    $item_id = $item['id'];
          
    if (($item_id > 1) && ($item['host'] > 1))
    { 
      $interface_shown = TRUE;
      $int_image = ImageCreate ($this->sizex, $this->sizey) or die ('interface_show(): Unable to create image.');
      $background_color = ImageColorAllocate ($int_image,
        hexdec(substr($bgcolor, 0, 2)),
        hexdec(substr($bgcolor, 2, 2)),
        hexdec(substr($bgcolor, 4, 2)));
      $text_color = ImageColorAllocate ($int_image,
        hexdec(substr($fgcolor, 0, 2)),
        hexdec(substr($fgcolor, 2, 2)),
        hexdec(substr($fgcolor, 4, 2)));
        
      $mark_interface_filename='0';
      if ($item_id == $mark_interface)
      {
        ImageFilledRectangle($int_image,0,0,$this->sizex,$this->sizey,
          ImageColorAllocate ($int_image, 0, 0, 0));
        $mark_interface_filename='1';
      }
      $int_filename = $Source->source_type.$item_id.'-'.$this->big_graph.'-'.$mark_interface_filename.'.png';
      ImageFilledRectangle($int_image,3,3,$this->sizex-3,$this->sizey-3,
        $background_color);
      $infobox_text = '';
  
      if (($item['show_rootmap'] ==2 ) || ($item['check_status'] ==0)) //if its "Mark Disabled"
      {
        $small_box = $sizex*0.13;
    
        if ($item['show_rootmap'] == 2)
          $small_box_color = ImageColorAllocate ($int_image,
            hexdec(substr($bgcolor_status, 0, 2)),
            hexdec(substr($bgcolor_status, 2, 2)),
            hexdec(substr($bgcolor_status, 4, 2)));
        if ($item['check_status'] == 0)
          $small_box_color = ImageColorAllocate ($int_image, hexdec(77), hexdec(77), hexdec(77));
        ImageFilledRectangle($int_image,$this->sizex-7,0,$this->sizex,7,$small_box_color);
      }
      list($text_to_show, $infobox_text) = $Source->normal($this, $item);  
      ImageStringCenter ($int_image, $text_color, 0, $text_to_show, $this->big_graph);
      ImagePng ($int_image,$images_real_path."/".$int_filename);
      ImageDestroy($int_image);  
  
    if (array_key_exists('events',$urls))
        $events_url = $urls['events'][1];
    else
        $events_url = '';
    if (array_key_exists('map',$urls))
        $maps_url = $urls['map'][1];
    else
        $maps_url = '';
      $a_events = "href=\"javascript:ir_url('$events_url','$maps_url')\"";
  
      if ((strlen($infobox_text) > 1) && $this->popups)
        $this->image_events = "onMouseOver=\"javascript: infobox_show(this,event,'$infobox_text');\" onMouseOut=\"javascript: infobox_hide();\"";
  
      $interface_html = image($Config->get('images_rel_path')."/$int_filename?r=".$this->rnd,$this->sizex,$this->sizey,ucfirst($Source->source_type)." ".$item_id,"","","","<image_events>");
    
    } //id>1 host>1
  
    if (($this->map_id > 1) && ($source=='interfaces')) //switch to dynmap if we are using a map (has x,y) and source is interfaces
    {
      $this->interface_process($item, $urls);
    } else 
      if ($int_filename)
      {
        $interface_html = '<a <a_events>>'.$interface_html.'</a>';
        $interface_html = str_replace ('<a_events>',$a_events,$interface_html);
        $interface_html = str_replace ('<image_events>',$this->image_events,$interface_html);
        $interface_html .= view_toolbox($item['id'], $urls);
        echo 
          tag("td", "","","bgcolor='".$this->map_color."'").
          $interface_html.
          tag_close('td');
      }
    return $interface_shown;
  }//interface_show()

  function finish()
  {
    //if (($map_id > 1) && ($source == 'interfaces'))
    //dynmap_init - but that function does not exist
  } // finish


  function interface_process(&$item, $urls)
  {
    $id = $item['id'];

    if (($this->active_only==0) || ($id > 1))
    {
  $dynmap_objects[$map_int_id][int_id]=$id;
        $dynmap_objects[$map_int_id][x]=$map_x;
  $dynmap_objects[$map_int_id][y]=$map_y;

        $dynmap_objects[$map_int_id][a_events]=$a_events;
        $dynmap_objects[$map_int_id][html]=$interface_html;
        $dynmap_objects[$map_int_id][image_events]=$this->image_events;
        $dynmap_objects[$map_int_id]["toolbox"]=view_toolbox($id, $urls);
    }
  } // interface_process()

}
?>
