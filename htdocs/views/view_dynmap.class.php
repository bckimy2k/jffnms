<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once('view_normal.class.php');

class View_dynmap extends View_normal
{
  private $dynmap_objects = array();

  function __construct()
  {
    parent::__construct();
  }

  function html_init($action)
  {
    if ($action == 'edit' && !profile('ADMIN_HOSTS'))
      $action = 'view';

    if ($action == 'edit')
    {
      adm_header("Dynmap", $this->map_color, "OnContextMenu='javascript: select_object_by_mouse(event); return false;'");
      $url = $Sanitizer->get_url('', 'all', FALSE, array('alarms_last', 'alarms_time'));
    } else
      adm_header('Dynmap', $this->map_color, '');
    echo script("
    var sizex = $this->sizex;
    var sizey = $this->sizey;

    if (window.innerHeight) { //Mozilla
	totalx = window.innerWidth;
	totaly = window.innerHeight;
    } else { //IE
	totalx = document.documentElement.offsetWidth;
	totaly = document.documentElement.offsetHeight;
    }
    
    totalx = totalx - 65;
    totaly = totaly - 20;
    
    var objects = new Array();
    var already_lines = new Array();
    var selected_name = null;
    var link_to_a = null;
    var was_moved = null;
    var	gridx = 10;
    var	gridy = 10;

    var objects_to_save = new Array();
    var objects_to_save_id = 0;
    
    document.write(
    '\<style\> ".
    ".mapbox { width:'+totalx+'; height: '+totaly+'; visibility:visible; position:absolute; left:0; top:0; } ".
    ".infobox { visibility: hidden; position: absolute; } ".
    "\</style\>');

    function debug (data) {
	".(($debug!=1)?"if (1==2)":"")."
	real_debug(data);
    }");
?>
<SCRIPT SRC="views/infobox.js"></SCRIPT>
<SCRIPT SRC="views/view_dynmap.js"></SCRIPT>
<script src="views/toolbox.js"></script>

<div class='mapbox'><table bgcolor="<?php echo $this->map_color ?>" width="100%" height="100%"><tr><td>&nbsp;</td></tr></table></div>
<?php
  } //html_init()

  function interface_process(&$item, $urls)
  {
    if ($this->active_only == 0 || $item['id'] > 1)
    {
      $map_int_id = $item['map_int_id'];
      $this->dynmap_objects[$map_int_id]['int_id'] = $item['id'];
      $this->dynmap_objects[$map_int_id]['x'] = $item['map_x'];
      $this->dynmap_objects[$map_int_id]['y'] = $item['map_y'];
      $this->dynmap_objects[$map_int_id]['a_events'] = $item['a_events'];
      $this->dynmap_objects[$map_int_id]['html'] = $interface_html;
      $this->dynmap_objects[$map_int_id]['image_events'] = $image_events;
      $this->dynmap_objects[$map_int_id]['toolbox'] = $toolbox;
    }
  }// interface_process()

  function no_interfaces($source)
  {
    die('dynmap no interfaces');
  }

  function finish($action)
  {
    if (count($this->dynamp_objects) <= 0 )
      return;
    $js_objects = '';
    foreach ($this->dynmap_objects as $object_id => $object)
    {
      $obj_int_id = $object['int_id'];
      if ($obj_int-id > 1)
        $js_objects .= "
      objects[$obj_int_id]=new Array();
      objects[$obj_int_id][0]=$object_id;
      objects[$obj_int_id][1]=new Array();
      objects[$obj_int_id][2]=$object[x];
      objects[$obj_int_id][3]=$object[y];
      ";
    }
    $connection_count=0;
    foreach ($this->dynmap_objects as $object_id => $object)
    {
      if ($object['int_id'] != 1)
        continue;
      $connection_count++;
      $obj_x = $object['x'];
      $obj_y = $object['y'];
      $js_object .= "
      objects[$obj_y][1][$object_id] = $obj_y;
      objects[$obj_x][1][$object_id] = $obj_x;
      ";
    }
    echo script($js_objects);

    if ($action == 'edit')
    {
      foreach ($this->dynmap_objects as $object_id => $object)
        if ($object['int_id'] == 1)
          echo "<div class='mapbox'><img id='connection$object_id' src='' border='0' /></div>\n";
    } elseif ($connection_count > 0)
      echo "<div class='mapbox'><img id='allconnections' src='' border='0' /></div>\n";
    echo "\n<div class='mapbox' onMouseMove='javascript: follow_object(event);'><table width='100%' height='100%'><tr><td>&nbsp</td></tr></table></div>\n";
    foreach ($this->dynmap_objects as $object_id => $object)
    {
      if ($action == 'edit')
      {
        $object['image_events'] = "
          OnDblClick='javascript: link_to_object(\"$object[int_id]\");'
          OnMouseUp='javascript: select_object(\"$object[int_id]\");'  
          OnMouseMove='javascript: follow_object(event);'";
          $a_init = '';
          unset($object['a_events']);
      } else {
        $a_init = '<'.$object['a_events'].'>';
      }
      $object['html'] = str_replace('<image_events>', $object['image_events'],
        $object['html']);
      if ($object['int_id'] > 1)
      {
        $top = $object['y'] - $this->sizey/2;
        $left = $object['x'] - $this->sizex/2;
        if ($top < 1) $top = 1;
        if ($left < 1) $left = 1;
        echo "\n\t<div id='object{$object['int_id']}' style='position:absolute; top: $top; left: $left'>".
          "\n\t$a_init$object[html]</a></div>\n$object[toolbox]";
      }
    }
    echo "\n<div id='infobox' class='infobox'><table width='' height='' border=0 cellpadding=0 cellspacing=1 bgcolor=orange>".
      "<tr><td bgcolor=yellow valign='top' align='center' nowrap><p id='text'>ERROR</p></td></tr></table></div>\n";
    //Debug Box
    if ($debug == 1) 
    {
      $visbility = '';
      echo "<div style='top:50;left:800;position:absolute;background-color:white'><p id='debugtext'><u>Debug Console</u></p></div>";
    } else
      $visibility = "visibility: hidden;";

    //savebox
    echo "
  <SCRIPT>
  document.write('\<div style=\"top: '+(totaly-30)+';left:'+(totalx-200)+';position:absolute;$visibility\">'+
  '<IFRAME id=savebox width=200 height=30></IFRAME></div>');
  </SCRIPT>";

    //Edit/View Box
    //permission FIXME
    echo script("document.write (\"<div style=\'top:5;left:\"+(totalx+5)+\";position:absolute;\'>\");");
      
    if ($action== 'edit') 
    {
      echo
        linktext(image("refresh2.png"),$REQUEST_URI).
        linktext(image("logoff.png"),$REQUEST_URI."&action=view");
    } else
      if (profile('ADMIN_HOSTS'))
        echo linktext(image('edit.png'),$REQUEST_URI.'&action=edit');
      
    echo tag_close('div');
    echo script('position_new_objects();');
    if ($action=='edit')
    {
      echo script('redraw_all_connections();');
      $norefresh=1;
    } else
      echo script('draw_all_connections();');
  } // finish()

  function save()
  {
    die('dynmap save');
  } // save()

}
