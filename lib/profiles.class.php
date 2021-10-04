<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsProfiles extends basic
{

  // Profile base methods
  function get_all($ids = NULL,$where_special = NULL)
  { 
    if (!is_array($where_special))
      $where_special = array();
    
     return get_db_list(  
      array('auth','profiles','profiles_options','profiles_values'),  $ids, 
      array(  'profiles.*',
        'profiles_options.*',
        'auth.*',
        'profiles.id',
        'values_description'=>'profiles_values.description',
        'values_value'=>'profiles_values.value'
      ) ,  
      array_merge(
        array(  
          array('profiles.userid'   ,'=','auth.id'),
          array('profiles.profile_option','=','profiles_options.id'),
          array('profiles.value' ,'=','profiles_values.id'),
          array('profiles.id','>',1)),
        $where_special),
      array(
        array('profiles.userid','desc'), 
        array('profiles.id','desc')
    )); 
  }
  function add_user($user_id, $option) {
      $id = '';
      $ProfilesValues = new JffnmsProfiles_values();
      $ProfilesOptions = new JffnmsProfiles_options();

      if (!is_numeric($option)) {
          $option = $ProfilesOptions->get_id($option);
      }
      if (!is_numeric($option) or $option == 0) 
          return '';

      $query = "insert into profiles (userid,profile_option,value) VALUES ($user_id, $option, 1)";
	  $result = db_query ($query) or die ("Query Failed - AZ15 - ".db_error());
	  $id = db_insert_id();

      $ProfilesValues->modify($user_id, $option, $ProfilesOptions->get_default_value($option));
      return $id;
  }

  function del($id)
  {
    return db_delete('profiles',$id);  
  }


} // class
?>
