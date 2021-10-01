<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('toolbox.inc.php');
require_once('view_base.class.php');

class View_text extends View_base
{

  function html_init()
  {
    adm_header('Alarms - Text View');
    echo tag('pre');
  } //html_init()

  function interface_show(&$item, $bgcolor, $fgcolor, $mark_interface, $urls)
  {
    global $Source;
    if ($item['id'] <= 1 || $item['host'] <= 1)
      return FALSE;

    // soruce->interfae
    list ($dummy, $item_text) = $Source->text($this, $item);
    $text = str_pad(strtoupper($item['alarm_name']), 12, ' ', STR_PAD_BOTH).
      "\t".$item_text;

    if (is_array($urls))
      foreach ($urls as $frame=> $url)
      {
        if ($frame == 'map') $frame = '_parent';
        $text .= "\t".str_replace(array("\t","\n"), '',
          linktext($url[0], $url[1], $frame));
      }
    $text .= "\n";
    echo $text;
    return TRUE;
  } //interface_show()
        
  function no_interfaces($source_type)
  {
    if ($this->active_only == 1)
      echo 'No Alarmed '.ucfirst($source_type)." Found\n";
    else
      echo 'No '.ucfirst($source_type)." Found\n";
  } // no_interfaces


    

  function finish()
  {
    echo tag_close('pre');
  } //finish()
}
