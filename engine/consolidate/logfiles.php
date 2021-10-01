<?php
 /* Logfile Consolidator. This is part of JFFNMS
  * Copyright (c) 2008,2010 Craig Small <csmall@enc.com.au>
  * This program is licensed under the GNU GPL, full terms in the LICENSE file
  */

function consolidate_logfiles(&$Events)
{
  $fields = array('interface','state','username','info');

  $query_logfiles = 'SELECT id,filename,last_poll_date,file_offset FROM logfiles';
  $result_logfiles = db_query($query_logfiles) or die("Query failed - LF1 - ".db_error());
  while ($logfile_record = db_fetch_array($result_logfiles))
  {
    $lfid = $logfile_record['id'];
    logger('LF '.$lfid.':='.$logfile_record['filename']."\n");
    if (!is_readable($logfile_record['filename']))
    {
      logger("LF $lfid:= file no readable, skipping.\n");
      continue;
    }
    $stat = @stat($logfile_record['filename']);
    if(!$stat)
    {
      logger("LF $lfid:= Cannot stat logfile, skipping.\n");
      continue;
    }
    $logfile_offset = $logfile_record['file_offset'];
    // Has it changed, ie has the modtime changed
    if ($stat['mtime'] <= $logfile_record['last_poll_date'])
    {
      logger("LF $lfid:= Logfile hasn't change size last poll, skipping.\n");
      continue;
    }
    // New file?
    if ($stat['ctime'] > $logfile_record['last_poll_date'])
    {
      logger("LF $lfid:= Logfile new, resetting offset to 0.\n");
      $logfile_offset = 0;
    } elseif ($stat['size'] == $logfile_offset)
    {
      logger("LF $lfid:= Logfile hasn't grown, skipping.\n");
      continue;
    } elseif ($stat['size'] < $logfile_offset)
    {
      logger("LF $lfid:= Logfile has shrunk!! Resetting offset to 0.\n");
      $logfile_offset = 0;
    }
    if (($fp = fopen($logfile_record['filename'], 'r')) == NULL)
    {
      logger("LF $lfid:= Cannot open logfile for reading.\n");
      continue;
    }
    //Line up the matches
    $match_records = get_db_list(
      array('logfiles_match_items', 'logfiles_match_groups'),
      null,//ids
      array('logfiles_match_items.*'),
      array(
        array('logfiles_match_groups.logfile','=',$logfile_record['id']),
        array('logfiles_match_groups.match_item','=','logfiles_match_items.id'),
      ),
      array(
        array('logfiles_match_groups.pos','asc'),
      )
   );
   fseek($fp, $logfile_offset);
   $lines_read=0;
   while(!feof($fp))
   {
     $line = fread($fp, 4096);
     $lines_read++;
     foreach ($match_records as $match)
     {
       if (preg_match('/'.$match['match_text'].'/',$line,$regs))
       {
         $logger_text = array();
         //logger("LF $lfid:= Matched ".$match['match_text'].".\n");
         foreach ($fields as $field_name)
         {
           if (!empty($match[$field_name]))
           {
             $values[$field_name] = logfile_parse_field($match[$field_name],$line, $regs);
             $logger_text[] = $field_name . '('.$match[$field_name].'): '.$values[$field_name];
           }
         }
         // Get and parse the host
         $host = logfile_parse_field($match[$field_name], $line, $regs);
         $host_id = logfile_get_hostid($host);
         logger("LF $lfid := Host: $host_id - ".join(' - ',$logger_text)."\n");
         $Events->add(date('Y-m-d H:i:s', time()),
           $match['type'], $host_id,
           $values['interface'], strtolower($values['state']),
           $values['username'], $values['info'], 1);
       }//matched
     }
   }
   $logfile_offset = ftell($fp);
   fclose($fp);
   logfile_update($lfid,$logfile_offset);
 }
}

/**
 * logfile_parse_field - Extract a field from a logfile
 * @field_name : selects that the field will be
 * @line:   current logfile line
 * @regs:   matches from regular expressions
 */
function logfile_parse_field($field_name, $line, $regs)
{
    $retval = $field_name; // Default is literal
    switch($field_name)
    {
    case '*':  // complete message
      $retval = $line;
      break;
    case 'D': // Only data
      $retval = join(' ',$regs);
      break;
    default:
      if (is_numeric($field_name))
        if (array_key_exists($field_name,$regs))
          $retval =  $regs["$field_name"];
        else
          $retval = '';
    }
    // No parsing, so its a literal
    return addslashes(trim(str_replace(',','',$retval)));
}

/**
 * logfile_get_hostid - Find the internal ID for a host
 * @host : IP address or hostname of the host
 * 
 */
function logfile_get_hostid($host)
{
  $host_long = ip2long($host);
  if ($host_long === -1 || $host_long === FALSE)
    $host_ip = gethostbyname($host);
  else
    $host_ip = $host;
  $query_routerid = '
    SELECT hosts.id FROM hosts
    WHERE hosts.ip = "'.$host_ip.'" OR hosts.name = "'.$host.'"';
  $result_routerid = db_query($query_routerid);
  if ( ($result_routerid === FALSE) || (db_num_rows($result_routerid) < 1))
    return 1;
  return(current(db_fetch_array($result_routerid)));
}

/**
 * logfile_update - Update offset and last time in database
 * @lfid : Logfile id
 * @offset : File offset
 * 
 */

function  logfile_update($lfid,$logfile_offset)
{
  return db_update('logfiles',$lfid, 
    array('file_offset'=>$logfile_offset, 'last_poll_date' => time()));
}
?>
