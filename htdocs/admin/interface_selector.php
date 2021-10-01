<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (C) 2010 Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

{
  include ('../auth.php'); 
    
  $field = $Sanitizer->get_string('field');
  $field_id = $Sanitizer->get_string('field_id');
  $field_name = $Sanitizer->get_string('field_name');
  $map_id = $Sanitizer->get_int('map_id');
  $client_id = $Sanitizer->get_int('client_id');

  adm_header('Interface Selector'.(!empty($field_name)?' - '.$field_name:''));
  $Int_obj = new JffnmsInterfaces();
  
  if ($field_id != 'all')
    $filters = array($field=>$field_id);

  if (isset($map_id)) $filters['map']=$map_id;
  if (isset($client_id)) $filters['client']=$client_id;

  $cant = $Int_obj->get(NULL,$filters);
  $interfaces = array();
  $max_options = 20;

  if ($cant > 0)
    while ($int = $Int_obj->fetch())
    {
      $description = array();
      switch ($field)
      {
      case 'host':
        $description = array($int['type_description'], $int['interface'], 
          $int['client_shortname']);
        break;
      case "type":
      default:
        $description = array($int['host_name'], $int['zone_shortname'],
          $int['interface'], $int['client_shortname']);
      }
      if (array_key_exists('description', $int))
          $description[] = $int['description'];
      
      $final_description = join(' ',$description);
      $final_description = str_replace(' ', '&nbsp;', $final_description);
      $interfaces[$int['id']] = $final_description;
    }
  asort ($interfaces);
  
  echo 
    "<script language=\"JavaScript\" src=\"../scripts/interface_selector.js\">
    </script>".
      tag("div","popup_selector").
      table("popup_selector").
      tr_open().
      td( $field_name, "title").
      td(  control_button("View Now","_self", "javascript: view_now('".$field."');","world.png")).
      td( control_button("Select All","_self", "javascript: select_all('selector[]');","world.png")).
      td( control_button("Add","_self", "javascript: operation('add'); ","new2.png")).
      td( control_button("Remove","_self", "javascript: operation('del'); ","delete.png")).
      td( control_button("","_self", "javascript: close_popup(); ","logoff.png")).
      tag_close("tr").
      table_close().
      (($cant>0)
    ?select_custom("selector", $interfaces, "", "", $max_options, false, "", "javascript: operation('add'); ")
    :br().html('span','No Interfaces Found','no_interfaces_found')).
      tag_close('div').
      script("document.getElementById('selector[]').focus();");
  
  adm_footer();
}
