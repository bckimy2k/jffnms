<?php
/*
 * This file is part of JFFNMS
 * Copyright (C) <2010> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class Sanitizer
{
  function get_string($tag, $default=FALSE, $force_array = FALSE)
  {
    return $this->get_val($tag, $default, $force_array,
      FILTER_SANITIZE_STRING, 
      FILTER_FLAG_ENCODE_LOW|FILTER_FLAG_ENCODE_HIGH);
  }

  function get_special($tag, $default=FALSE, $force_array = FALSE)
  {
    return $this->get_val($tag, $default, $force_array,
      FILTER_UNSAFE_RAW,
      FILTER_FLAG_STRIP_HIGH);
  }
  private function get_val($tag, $default, $force_array, $filter, $filt_opts)
  {
    if (!array_key_exists($tag, $_REQUEST))
      return $default;
    $value = $_REQUEST[$tag];
    if (!is_array($value) && $force_array === FALSE)
    {
      return filter_var($value, $filter, $filt_opts);
    } 
    $retval = array();
    if (!is_array($value))
      $value = array($value);
    foreach($value as $key => $item)
    {
      if (is_array($item))
        $retval[$key] = filter_var_array($item, $filter);
      else
        $retval[$key] = filter_var($item, $filter, $filt_opts);
    }
    return $retval;
  }

  function get_int($tag, $default=FALSE, $force_array=FALSE)
  {
    return $this->get_val($tag, $default, $force_array,
      FILTER_SANITIZE_NUMBER_INT,0);
  }

  function get_url($base_url,$copy_tags=array(),$new_tags=FALSE, $del_tags=FALSE, $overwrite=TRUE)
  {
    $get_vars = array();
    if (empty($base_url))
      $base_url = $_SERVER['SCRIPT_NAME'];
    if (!is_array($copy_tags) && $copy_tags == 'all')
      $copy_tags = array_keys($_REQUEST);

    if (is_array($copy_tags))
    {
      if (is_array($del_tags))
        $copy_tags = @array_diff($copy_tags, $del_tags);
      if ($overwrite == TRUE && is_array($new_tags))
        $copy_tags = array_diff($copy_tags, array_keys($new_tags));
    }

    if (is_array($copy_tags))
    {
      //$copy_tags = array_values($copy_tags);
      foreach($copy_tags as $tag)
      {
          $value = $this->get_string($tag,FALSE,FALSE);
        if ($value != NULL && $value !== FALSE)
            if (is_array($value))
                array_push($get_vars, $tag.'='.implode(',',$value));
            else
                array_push($get_vars, $tag.'='.$value);


      }
    }
    if (is_array($new_tags))
      foreach($new_tags as $key => $value)
      {
        $fvalue = filter_var($value, FILTER_SANITIZE_ENCODED,
          FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
        $fkey = filter_var($key, FILTER_SANITIZE_ENCODED,
          FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
        array_push($get_vars, "$fkey=$fvalue");
      }
    return $base_url.(count($get_vars)>0)?'?'.join('&',$get_vars):'';
  }
};
?>
