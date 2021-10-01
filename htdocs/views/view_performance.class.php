<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('toolbox.inc.php');
require_once('view_base.class.php');

class View_performance extends View_base
{
  function __construct()
  {
    parent::__construct();
    $this->sizex = 65;
    $this->sizey = 50;

    $this->new_sizex = 120;
    $this->new_sizey = 85;

    $this->map_refresh = 3 * 60; // 3 minutes

    $this->cols_max = round($this->screen_size / $this->new_sizex);
    $this->cols_count = $this->cols_max;
    $this->table_width = '100%';

  }

  function interface_show(&$item, $bgcolor, $fgcolor, $mark_interface, $urls)
  {
    global $Config, $Source;

    $interface_shown = FALSE;
    if ($item['have_graph'] == 1 && $Source->source_type == 'interfaces')
    {
      $interface_shown = TRUE;
      $filename = substr($Source->source_type,0,1).$item['id'].'graph.png';
      $real_filename = $Config->get('images_real_path').'/'.$filename;
      $result = performance_graph($item['id'],$real_filename,
        $item['default_graph'], $this->sizex, $this->sizey, '',
        (-60*60*2),0,'MINI');
      if ($result !== FALSE)
      {
        $im_orig = imagecreatefrompng($real_filename);
        $im_new = imagecreate($this->new_sizex, $this->new_sizey);
        $back_color = ImageColorAllocate ($im_new, 245, 245, 245);
        $text_color = ImageColorAllocate ($im_new, 0,0,0);
      
        imagecopy ($im_new, $im_orig, 0, 10, 20 ,5,imagesx($im_orig)-29,300);
        imagedestroy ($im_orig);

        // calls source view type
        list ($text_to_show, $infobox_text) = $Source->performance($this, $item);
        $size_text = strlen ($text_to_show[0])*6;
        imagestring ($im_new, 2, ($this->new_sizex-$size_text)/2, 0, $text_to_show[0], $text_color);
        if ($item['alarm_name'] != 'OK')
        {
          $alarm_color = ImageColorAllocate ($im_new,
            hexdec(substr($bgcolor, 0, 2)),
            hexdec(substr($bgcolor, 2, 2)),
            hexdec(substr($bgcolor, 4, 2)));
          imagefilledrectangle ($im_new, $this->new_sizex-10, 0, $this->new_sizex, 10, $alarm_color);
          imagerectangle ($im_new, $this->new_sizex-10, 0, $this->new_sizex, 10, $text_color);
        }
        imagepng($im_new, $real_filename);
        imagedestroy ($im_new);

        $a_events = "href=\"javascript:ir_url('".$urls['events'][1]."','".$urls['map'][1]."')\"";
        $image_events = "onMouseOver=\"javascript: infobox_show(this,event,'$infobox_text');\" onMouseOut=\"javascript: infobox_hide();\"";
        $interface_html = image($Config->get('images_rel_path')."/".$filename,$this->new_sizex,$this->new_sizey,ucfirst($Source->source_type)." ".$item['id'], '', '', '', '<image_events>');
        if ($this->map_id <= 1)
        { 
          $interface_html = html("a", $interface_html, "", "", $a_events);
          $interface_html = str_replace ("<a_events>",$a_events,$interface_html);
          $interface_html = str_replace ("<image_events>",$image_events,$interface_html);

          echo td($interface_html);
        }
      }
    } elseif ($this->map_id > 1) //get image from the normal interface show
    {
      //$old_view = $view_type;
      //$view_type = "normal";
      //include(call_view("interface_show"));
      die('call viw interfasce show');
      //$view_type = $old_view;
    } else
      $this->cols_count--;  //only when not using map (dynmap)
    
    if (($Source->source_type == "interfaces") and (($this->map_id > 1) or ($item['id'] == 1))) { 
        $view_type="dynmap";
        //include (call_view("interface_process"));
        die ('call view interface process');
    }
    return $interface_shown;
  } //interface_show()
      
}
