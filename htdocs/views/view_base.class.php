<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class View_base
{
  public $cols_count;
  public $cols_max;
  public $screen_size;
  public $sizex = 60;
  public $sizey = 30;
  public $host_id;
  public $map_id;
  public $map_color;
  public $active_only;
  public $big_graph;
  public $table_width = '100%';

  // Private but inheritable
  protected $popups;

  public function __construct()
  {
    global $Sanitizer;

    $this->map_id = $Sanitizer->get_int('map_id', 1);
    if ($map_profile = profile('MAP'))
      $this->map_id = $map_profile; //fixed map
    $this->map_color = $Sanitizer->get_string('map_color');
    $this->host_id = $Sanitizer->get_int('host_id');
    $this->active_only = $Sanitizer->get_int('active_only');
    $this->big_graph = $Sanitizer->get_string('big_graph', 0);
    $this->screen_size = ($Sanitizer->get_int('screen_size', 1020)-39);

    $this->popups = (profile("POPUPS_DISABLED")==1?false:true);
  }

  function html_init()
  {
    adm_header('Alarm Map',$this->map_color);
  ?>
  <style>
  .infobox { visibility: hidden; position: absolute; }
  .img { position: relative; }
  </style>
  <SCRIPT SRC="views/infobox.js"></SCRIPT>
  <script src="views/toolbox.js"></script>
  <?php
      echo script("
      function ir_url(url,url2){
    if (url!='') {
        if (top.work && top.work.events) 
      top.work.events.location.href = url; //if events frame exists use it
        else 
      if (url2=='') 
          url2=url; //if it doesnt and this is the only url take it to the main frame
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
      }");
  ?>    
  <div name="infobox" id="infobox" class="infobox">
  <table width="" height="" border=0 cellpadding=0 cellspacing=1 bgcolor=orange>
  <tr><td bgcolor=yellow valign="top" align="center" nowrap><p id="text">ERROR</p></td></tr></table>
  </div>
<?php
      echo 
    table("view_interfaces").
    tr_open('','',$this->map_color);
  }//html_init

  public function break_init() {}
  public function break_by_card(&$item) {}
  public function break_by_host(&$item) {}
  public function break_by_zone(&$item) {}
  public function break_next_line_span($break_by_host, $break_by_zone, $break_by_card) {}

  public function break_finish_row()
  {
    echo 
      tag_close('tr').
      tr_open('','',$this->map_color);
  }
  function break_show($urls) {}

  public function no_interfaces($source)
  {
    echo td ('No'.(($this->active_only==1)?' Alarmed':'').' '.ucfirst($source).' Found','','no_interfaces_found');
  } // no_interfaces
  function finish() {}
}
