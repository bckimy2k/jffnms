<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
include ('../auth.php');
{
  global $Sanitizer, $Config;

  $menu    = $Sanitizer->get_string('menu', 'frame1');
  $menu_type = $Sanitizer->get_string('menu_type');
  $type    = $Sanitizer->get_string('type');
  $frame   = $Sanitizer->get_string('frame', 'frame2');
  $scroll1 = $Sanitizer->get_string('scroll1', 'yes');
  $scroll2 = $Sanitizer->get_string('scroll2', 'yes');
  $size1   = $Sanitizer->get_string('size1', '100%');
  $size2   = $Sanitizer->get_string('size2', '*');
  $post_name1  = $Sanitizer->get_string('name1');
  $post_name2  = $Sanitizer->get_string('name2');

  $jffnms_rel_path = $Config->get('jffnms_rel_path');

  if ($post_name1 === FALSE)
    $name1 = "menu_$menu.php?type=$menu_type&frame=$frame";
  else
    $name1 = "$jffnms_rel_path/admin/$post_name1";

  if ($post_name2 === FALSE)
    $name2 = "$jffnms_rel_path/blank.php";
  else
    $name2 = "$jffnms_rel_path/admin/$post_name2";

  $frame_def = ($type=="vertical")?"rows='*' cols='".$size1.",".$size2."'":"cols='*' rows='".$size1.",".$size2."'";

  echo tag("!DOCTYPE", "", "","HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\"", false);

  adm_header("Menu","","",true);
    
  echo 
  tag("frameset", $menu, "", $frame_def." frameborder='no' framespacing='0'").
      tag("frame", "", "", "name='".$menu ."' noresize scrolling='".$scroll1."' src='".$name1."'", false).
      tag("frame", "", "", "name='".$frame."' noresize scrolling='".$scroll2."' src='".$name2."'", false).
  tag_close("frameset").
  tag_close("html");
}

?>
