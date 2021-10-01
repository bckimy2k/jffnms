<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsProfiles_options extends basic
{
  var $jffnms_insert = array('tag'=>'New Tag','description'=>'New Option','type'=>'text');

  function get_all($ids = NULL,$where_special = NULL)
  {
    return get_db_list('profiles_options',  $ids,
      array('profiles_options.*'), //table,ids,fields  
      array(array('profiles_options.id','>',0)), //where
      array(array('profiles_options.id','desc')) ); //order 
  } // get_options()

  function add($user_id, $option)
  {
    $id = '';
    if (!is_numeric($option))
      $option = $this->get_id ($option);
    if (is_numeric($option) and ($option > 0))
    { 
      $query = "INSERT INTO profiles (userid,profile_option,value) VALUES ($user_id, $option, 1)";
      $result = db_query ($query) or die ('Query Failed - AZ15 - '.db_error());
      $id = db_insert_id();
      $this->update($user_id,$option,$this->get_default_value($option));
      if (!isset($value) or $value=='')
        $value=1;
    }
    return $id;
  }
      
  function del($user_id, $option)
  {
    if (!is_numeric($user_id) || !is_numeric($option))
      return FALSE;
    $query = "DELETE FROM profiles WHERE userid = $user_id AND profile_option = $option";
    return db_query ($query) or die ("Query Failed - AZ14 - ".db_error());
  }

  function del_user($user_id)
  {
    $query = "DELETE FROM profiles WHERE userid = $user_id";
    $result = db_query ($query) or die ("Query Failed - Profiles del_user_options() - ".db_error());
  }


  function get_user( $user_id, $option)
  {
    $query = "SELECT value FROM profiles WHERE userid = $user_id AND profile_option = $option";
    $result = db_query ($query) or die ('Query Failed - AZ17 - '.db_error());
    $num_rows = db_num_rows($result);
    if ($num_rows == 1)
    {
      $row = db_fetch_array($result);
      return $row['value'];
    }
    return 0;
  }

  function list_users( $user_id)
  {
    return get_db_list(
      array('profiles'), NULL,
      array('*'),
      array(
        array('profiles.id','>',1),
        array('profiles.userid','=',$user_id),
      ),
      NULL);
  }

  function get_type ($option)
  {
    //find out the type of the option
    $query = "SELECT type FROM profiles_options WHERE id = $option";
    $result = db_query ($query) or die ('Query Failed - profile_get_option_type() - '.db_error());
    if (db_num_rows($result) == 1)
    { 
      $row = db_fetch_array($result);
      return $row['type'];
    }
    return '';
  } #get_option_type()

  function get_id ($tag)
  {
    $query = "SELECT id FROM profiles_options WHERE tag = '$tag'";
    $result = db_query ($query) or die ('Query Failed - Profiles-> get_option_id() - '.db_error());
    if (db_num_rows($result) == 1)
    {
      $row = db_fetch_array($result);
      return $row['id'];
    }
    return 0;
  } #get_option_id()

  function get_default_value ($option)
  {
    $query = "SELECT default_value FROM profiles_options WHERE id = $option";
    $result = db_query ($query) or die ('Query Failed - Profiles get_option_default_value() - '.db_error());
    if (db_num_rows($result) == 1)
    { 
      $row = db_fetch_array($result);
      return $row['default_value'];
    }
    return '';
  } #get_option_default_value()

  function update($user_id, $option, $value)
  {
	$value_id = 0;
	if (!is_numeric($option)) $option = $this->get_id ($option);

	if ($type = $this->get_type($option)){
	
	    if ($type=="text") { //if its text modify the value
		
		$value_id = $this->get_user($user_id,$option); //get the actual value of this option for this user

		if (($value_id=="0") or ($value_id==1)) { // didnt have that option //we didnt have any value there so add a new one
		    $query = "insert into profiles_values (profile_option) values ($option)";
		    $result = db_query($query) or die ("Query Failed - AZ12 - ".db_error()); 
		    $value_id = db_insert_id();
		}
	
		if ($value_id > 1) db_update("profiles_values",$value_id,array("value"=>$value)); //update the value
	    }
	    
	    if ($type=="select") { //if its select, find out the ID of the value
		$query = " Select id from profiles_values where profile_option = $option and value = '$value'";
		//echo $query;
		$result = db_query ($query) or die ("Query Failed - AZ10 - ".db_error());
		if (db_num_rows($result) == 1) { 
		    $row = db_fetch_array($result);
		    $value_id = $row["id"];
		}
	    }
	}

	if ($value_id > 0) {
	    $query="Update profiles set 
		    value = '$value_id'
		    where profile_option = '$option' and userid = $user_id";
	    //echo "$query\n";
	    $result = db_query ($query) or die ("Query failed - profiles_modify_value() - ".db_error());
	    return true;
	}
	return false;
    }	
}//class
