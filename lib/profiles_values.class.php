
<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsProfiles_values extends basic
{
  var $jffnms_insert = array('description'=>'New Value'); 

  function get_all($filter = NULL, $filters=NULL)
  { 
    if (is_numeric($filter)) $filters['option'] = $filter;
    $where_filters = array();
      
    if (isset($filters['option']))
      $where_filters[]=array('profiles_values.profile_option','=',$filters['option']);

    return get_db_list( array('profiles_options','profiles_values'),
      NULL,   
      array('profiles_values.*','option_description'=>'profiles_options.description'), //table,ids,fields  
      array_merge(
        array(
          array('profiles_values.profile_option','=','profiles_options.id'),
          array('profiles_values.id','>',1)
        ),$where_filters
      ), //where
      array(array('profiles_values.id','desc')) //order 
    );
  }//list_values()

  function add($option_id = 1)
  {
    $ProfilesOptions = new JffnmsProfiles_options();
    if ($ProfilesOptions->get_type($option_id)!='text') 
      return db_insert('profiles_values',array(
        'description'=>'New Value',
        'profile_option' => $option_id,
        'value' =>'new_value')); 
  }

  function description($option, $value)
  {
    $query_profile = 
      'SELECT description FROM profiles_values '.
      "WHERE profile_option=$option AND value='$value'";
    $result_profile = db_query($query_profile) or die ('profile_values::description() - '.db_error());
    if (db_num_rows($result_profile)==0)
      return $value;
    $row = db_fetch_array($result_profile);
    return $row['description'];
  }

  function get_value($tag, $user_id = 0)
  {
    if ($user_id == 0)
    {
      if (!array_key_exists('auth_user_id', $_SESSION))
        die("profiles-> get_value(): auth_user_id not in SESSION table\n");
      $user_id = $_SESSION['auth_user_id'];
    }
    $query_auth = "
      SELECT
        profiles_values.value as profile_value 
      FROM   profiles, profiles_options, profiles_values 
      WHERE
        profiles.userid = '$user_id' and 
        profiles_options.tag = '$tag' and 
        profiles_options.id = profiles.profile_option and
        profiles_values.id = profiles.value
      ";
    $result_auth = db_query ($query_auth) or die('Profiles get_value 1 -'.db_error());
    $auth_count = db_num_rows($result_auth);
    if ($auth_count == 1)
    {
      $auth_row = db_fetch_array($result_auth);
      //debug("$tag: $profile_value\n");
      return $auth_row['profile_value'];
    }
    return FALSE;
  } //get_value()

  function modify($user_id, $option, $value)
  {
    $ProfilesOptions = new JffnmsProfiles_options();
    $value_id = 0;
    if (!is_numeric($option))
      $option = $ProfilesOptions->get_id($option);

    if ($type = $ProfilesOptions->get_type($option))
    {
      if ($type  == 'text')
      {
        $value_id = $ProfilesOptions->get($user_id,$option); //get the actual value of this option for this user
        if ($value_id=='0' or $value_id==1) // didnt have that option //we didnt have any value there so add a new one
        {
          $query = "INSERT INTO profiles_values (profile_option) VALUES ($option)";
          $result = db_query($query) or die ("Query Failed - AZ12 - ".db_error()); 
          $value_id = db_insert_id();
        }
        if ($value_id > 1)
          db_update('profiles_values',$value_id,array('value'=>$value)); //update the value
      }
      if ($type=='select') //if its select, find out the ID of the value
      {
        $query = "SELECT id FROM profiles_values WHERE profile_option = $option and value = '$value'";
        $result = db_query ($query) or die ('Query Failed - AZ10 - '.db_error());
        if (db_num_rows($result) == 1)
        { 
          $row = db_fetch_array($result);
          $value_id = $row['id'];
        }
      }
    }
    if ($value_id > 0)
    {
      $query = "UPDATE profiles SET 
        value = '$value_id'
        WHERE profile_option = '$option' and userid = $user_id";
      //echo "$query\n";
      $result = db_query ($query) or die ('Query failed - profiles_modify_value() - '.db_error());
      return true;
    }
    return false;
  } #modify_value()

}
