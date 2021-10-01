<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsLogfiles extends basic
{
    public $jffnms_insert = array('filename'=>'New Logfile');
    public $jffnms_filter_record = 0;

  function add_match_group($filter)
  {
    return db_insert('logfiles_match_groups', array('logfile'=>$filter));
  }

  function list_match_groups($ids = NULL)
  {
    return get_db_list(  
      array('logfiles', 'logfiles_match_groups', 'logfiles_match_items'),  $ids, 
      array('logfiles_match_groups.*',
        'logfile_match_description'=>'logfiles_match_items.description',
      ),  
      array(  
        array('logfiles_match_groups.logfile','=','logfiles.id'),
        array('logfiles_match_groups.match_item','=','logfiles_match_items.id'),
        array('logfiles_match_groups.id','>',1)
      ),
      array(
        array('logfiles.id','asc'), 
        array('logfiles_match_groups.pos','asc'), 
      )
    );
  } //list_match_groups()
} //class
