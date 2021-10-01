<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
define('NEWS_URL', 'http://www.jffnms.org/news.php'); //just return some HTML to be included in the start page (no body tags)
$jffnms_init_classes=1;
require_once('auth.php');

$view_stats = $Sanitizer->get_string('view_stats', profile('VIEW_STARTPAGE_STATS'));

$map_id = 1; 
if ($map_profile = profile("MAP")) $map_id = $map_profile; 

if ($_SESSION['auth_type'] == AUTH_TYPE_USER)
  $client_id = 0;
else {
  $client_id = profile('CUSTOMER');
}
print_start_page  ($view_stats, $client_id, $map_id);

function print_start_page($view_stats, $client_id, $map_id)
{
  global $Config;

  adm_header('Start Page');
    
  echo 
    table('startpage').
    table_row('JFFNMS '. JFFNMS_VERSION.' Start Page','title',2,'',false).
    table_row($Config->get('jffnms_site').' Network Management System','subtitle',2,'',false);
  if ($view_stats==1)
  {
    $news = get_news_text(NEWS_URL);
    echo 
      tr_open('data').
      td (table('stats').
      tr_open().
        td('&nbsp;','spacer').
        td('Statistics','sectitle','',2).
        td('&nbsp;','spacer').
      tag_close('tr').
      get_info_text($map_id, $client_id).
      table_close());
    if (!empty($news))
      echo
        td (
          table('news').
          table_row('News','sectitle',2,'',false).
          $news.
          table_close()
        );
    echo tag_close('tr');
  } else 
    table_row(linktext('View Statistics','start.php?view_stats=1'),'view_stats',2);
  table_row('by Javier Szyszlican','author',2);
  echo table_close();
        
  adm_footer();
}

function get_info_text($map_id, $client_id)
{
  global $jffnms;
  $Interfaces = new JffnmsInterfaces();
  $Hosts = new JffnmsHosts();
  $Maps = new JffnmsMaps();
  $Users = new JffnmsUsers();
  $Clients = new JffnmsClients();
  $info_text = '';

  $info[]=array('title'=>'Alarms', 
    'data'=> get_alarms_text($Interfaces, $map_id, $client_id));

  if (($map_id==1) && ($client_id==0)) //only users with no filtered map
  {
    $info['hosts']   = array(  'title'=>'Hosts',  'data'=>$Hosts->count_all());
    $info['interfaces'] = array(  'title'=>'Interfaces',  'data'=>$Interfaces->count_all());
    $info['maps']   = array(  'title'=>'Maps',  'data'=>$Maps->count_all()-1);
    $info['customers']   = array(  'title'=>'Customers',  'data'=>$Clients->count_all()-1);
    $info['users']   = array(  'title'=>'Users',  'data'=>$Users->count_all());
    //$info["journals"]   = array(  "title"=>"Journals",  "data"=>$jffnms->journal->count_all());
  }

  foreach ($info as $info_row)
    $info_text .=
        tr_open().
        td('&nbsp;','spacer').
        td($info_row['title'].': ','cat').
        td($info_row['data'],'data').
        td('&nbsp;','spacer').
        tag_close('tr');
  return $info_text;
}

function get_news_text($news_url)
{
  $news_data = '';

  $news = news_get($news_url);
  if (is_array($news))
  foreach ($news as $item) 
    $news_data .= table_row($item,'','','',false);
  return $news_data;
}
  
function get_alarms_text(&$Interfaces, $map_id, $client_id)
{
  $alarms_text = '';

  if ($map_id == 1)
    $alarms = $Interfaces->status(NULL,array('in_maps'=>1,
      'client'=>$client_id, 'only_visible'=>true)); //all interfaces in rootmap
  else
    $alarms = $Interfaces->status(NULL,array('map'=>$map_id,
      'client'=>$client_id, 'only_visible'=>true)); //all interfaces in map
  
  foreach ($alarms as $key=>$value) 
    if ($key!='total')
      $alarms_text .= '<b>'.$key.':</b> '.$value['qty'].'<br>';
    
  if ($alarms_text=='') $alarms_text = 'All OK';
  return $alarms_text;
}

?>
