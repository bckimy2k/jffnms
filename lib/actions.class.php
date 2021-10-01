<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsActions extends basic
{
  private $jffnms_insert = array('description' => 'New Action', 'command' => 'none');

  public function get_all($ids = NULL)
  {
    return get_db_list('actions', $ids, array('actions.*'),
      array(array('actions.id','>',0)), //where
      array(array('actions.id','desc')) ); //order 
  }
} // class
