<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsUsers extends basic
{
  // Override the class name
  function jffnms_class() { return 'auth'; }

  function add ($username=NULL)
  {
    $ProfilesOptions = new JffnmsProfiles_options();
    $user_id = db_insert('auth', array('usern'=>$username));
  
    $query = 'SELECT id, default_value FROM profiles_options WHERE use_default = 1'; //FIXME order
    $result = db_query($query) or die ('Query Filed - user_add() - profile - '.db_error()); 
  
    while ($row = db_fetch_array($result)) //add every default option to the user profile
      $ProfilesOptions->add($user_id,$row['id']);
    return $user_id;
  } // add()
  
  function get_id($username)
  {
    if ($username)
    {
      $query = "select id from auth where usern = '$username'";
      $result = db_query($query) or die ('Query Failed - USER_GET_ID-1 - '.db_error());
    
      if (db_num_rows($result)==1)
      {
        $row = db_fetch_array($result);
        return $row['id'];
      }
    }
    return false;
  }
  
  function get_username($user_id)
  {
    if ($user_id > 0)
    {
      $query = "select usern from auth where id = '$user_id'";
      $result = db_query($query) or die ("Query Failed - USER_GET_USERNAME-1 - ".db_error());
      if (db_num_rows($result) == 1)
      {
        $row = db_fetch_array($result);
        return $row['usern'];
      }
    }
    return FALSE;
  }
  
  function del ($user_id)
  {
    $TriggersUsers = new JffnmsTriggers_users();
    $Triggers= new JffnmsTriggers();
    $ProfilesOptions = new JffnmsProfiles_options();

    if ($user_id)
    {
      $ProfilesOptions->del_user($user_id);
      $TriggersUsers->del_user($user_id);
      if (count($ProfilesOptions->list_users($user_id)) == 0 &&
        count($TriggersUsers->get_all(NULL, $user_id)) == 0)
        return db_delete('auth',$user_id); //if everthing is ok delete the user
    }  
    return FALSE;
  } //del()
  
  function modify($user_id, $usern, $old_password, $new_password, $fullname, $router)
  {
    if ($new_password == $old_password) $passwd = $new_password;
      else if (!empty($new_password)) $passwd = crypt($new_password);
  
    $user_fields = array('fullname' => $fullname);
    if ($router!='') $user_fields['router'] = $router;
    if (!empty($usern)) $user_fields['usern'] = $usern;
    if (!empty($passwd)) $user_fields['passwd'] = $passwd;
    return db_update('auth',$user_id,$user_fields);
  }
  
  function get_all ($ids = NULL,$where_special = NULL)
  { 
    if (!is_array($where_special)) $where_special = array();
  
    return get_db_list(
      'auth',
      $ids, 
      array(
        '*',
        'old_passwd'=>'passwd'),
        array_merge(
          array(array('id','>',1)),
          $where_special), //where
        array(array("usern","asc")) ); //order 
  }
} //class
?>
