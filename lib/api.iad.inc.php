<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

// AUTODISCOVERY
//===================================================================================

function iad_interfaces_from_db (&$Interfaces, $host_id,$type_id)
{

  $db_interfaces = array();
  $fields_types = array();

  $aux = $Interfaces->get_all(NULL,array('host'=>$host_id,'type'=>$type_id,'with_field_type'=>1));
      
  if (count($aux) > 0)
  {
    if (is_array($aux['field_types'][$type_id]))
      foreach($aux["field_types"][$type_id] as $field => $fdata)
        if ($fdata['type']==3)
        {
          $index=$field;
          break;
        }
    if (!isset($index))
        return $db_interfaces;
    unset ($field);
    unset ($fdata);
    unset ($aux['field_types']);
    reset ($aux);
      
    foreach($aux as $data)
      $db_interfaces[$data[$index]]=$data;

  }
  unset ($aux);
  return $db_interfaces;
}
    
function iad_interfaces_from_discovery ($function,$host_ip,$community,$host_id,$parameters)
{
  global $Config;
  $real_function = "discovery_$function";
  $function_file = $Config->get('jffnms_real_path')."/engine/discovery/$function.inc.php";

  if (! in_array($function_file, get_included_files()))
  {
    if (!is_readable($function_file))
    {
      logger("Discovery plugin file \"$function_file\" is not readable.");
      return FALSE;
    }
    require_once($function_file);
  }
  if (!function_exists($real_function))
  {
    logger("Discovery function \"$real_function\" was not found in discovery plugin \"$function_file\".");
    return FALSE;
  }
  $host = call_user_func_array($real_function,array($host_ip,$community,$host_id,$parameters));
  return $host;
}
?>
