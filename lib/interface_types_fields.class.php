<?php
/* This file is part of JFFNMS
 * Copyright (C) 2002-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsInterface_types_fields extends basic
{
  function get_all($ids=NULL,$filters=array())
  {
    $where = array();
    if (is_array($filters))
      foreach ($filters as $field=>$value)
        switch ($field)
        {
        case 'itype':
          $where[]=array('interface_types_fields.itype','=',$value);
          break;
        case 'exclude_types':
          $where[]=array('interface_types_fields.ftype','!=',$value);
          break;
        }

    return get_db_list(
        array("interface_types_fields","interface_types","interface_types_field_types"), $ids,
        array("interface_types_fields.*",
      "itype_description"=>"interface_types.description",
      "ftype_description"=>"interface_types_field_types.description",
      "ftype_handler"=>"interface_types_field_types.handler"
        ) ,
        array_merge(
      array(
          array("interface_types_fields.itype","=","interface_types.id"),
          array("interface_types_fields.ftype","=","interface_types_field_types.id"),
          array("interface_types_fields.id",">",1)),
      $where
        ),
        array(
            array("interface_types_fields.itype","asc"),
            array("interface_types_fields.ftype","asc"),
            array("interface_types_fields.pos","asc"),
            array("interface_types_fields.description","desc"),
            array("interface_types_fields.id","desc")
        ));
  }

  function update($id, $data)
  {
    global $Config;
    //call the update handler and then do a standard update
    $update_function = $data['ftype_handler'];
    $real_function = 'handler_$update_function';
    $function_file = $Config->get('jffnms_real_path')."/engine/handlers/$update_function.inc.php";

    if (in_array($function_file,get_included_files()) ||
      (file_exists($function_file) &&  (include_once($function_file))))
    {
      if (function_exists($real_function))
        call_user_func_array($real_function,array($data['name'],&$data['default_value']));
      else
        logger("ERROR: Calling Function '$real_function' doesn't exists.<br>\n");
    }
    unset ($data['ftype_handler']);
    return parent::update($id,$data);
  }

  function add($itype=NULL)
  { 
    if ($itype=NULL) $itype=1;
    return db_insert('interface_types_fields',array('description'=>'New Field','itype'=>$itype)); 
  }
}
