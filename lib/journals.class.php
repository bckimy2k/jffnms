<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

class JffnmsJournals extends basic
{
  public $jffnms_insert = array('subject'=>'New Journal');

  //overwrite the default DB table mapping
  function jffnms_class() { return 'journal'; }

  function get_all($ids = NULL,$only_active = 1)
  {
    if ($only_active==1)
      $filter = array("journal.active","=",1);
    else
      $filter = array();

    return get_db_list(
      array('journal'), $ids,
      array('journal.*'),
      array($filter,array('journal.id','>',2)),
      array(array('journal.id','desc'))
    );
  }

  function update($id, $data, $comment_update = '')
  {
    $journal_data = current($this->get_all($journal_id));
    $new_comment = $journal_data['comment']."\n".$comment_update; //merge old and new comments
    if (!$data['comment'])
      $data['comment'] = $new_comment;
    return db_update('journal',$id,$data);
  }
}
?>
