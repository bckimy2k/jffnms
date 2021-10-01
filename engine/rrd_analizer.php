<?php
/* RRDTool File Analyzer. This file is part of JFFNMS
 * Copyright (C) <2002-2003> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

require_once('../conf/config.php');
$Config = new JffnmsConfig();
config_load_libs('basic', 0);

$opt_interface='';
parse_commandline();
rrd_analizer_main($opt_interface);

function rrd_analizer_main($opt_interface)
{
  global $Config;

  $Interfaces = new JffnmsInterfaces();
  $span = 30; //30 minutes (start time)
  $end = 10; //10 minutes (end time)

  $start_time = (($span-5)+$end)*60;
  $end_time = ($end*60);

  //------------------------------------------> timeline
  //    |(start_time)--------|(end_time)    |(now)

  if (empty($opt_interface))
    $int_where = '> 1';
  else
    $int_where = "= $opt_interface";

  $query_rrd='
  SELECT
    interfaces.id, interfaces.interface, interfaces.host, interfaces.sla,
    interfaces.type, slas.info, slas.event_type,
    alarm_states.description as state
  FROM
    interfaces, slas, alarm_states, hosts
  WHERE
    interfaces.id '.$int_where.' AND
    interfaces.poll > 1 AND interfaces.sla > 1 AND
    interfaces.sla = slas.id AND slas.state = alarm_states.id AND
    interfaces.host = hosts.id AND hosts.poll = 1
  ORDER BY
    interfaces.id';

  $result_analyzer = db_query ($query_rrd) or die ("Query failed - S1 - ".db_error());
  $type_dss = array();
  while ($row = db_fetch_array($result_analyzer))
  {
    if (!$Interfaces->is_up($row['id']))
    {
      logger("I$row[id] : is not UP.\n");
      continue;
    }
    if (!isset($type_dss[$row['type']]))
    {
      $interface_data = $Interfaces->values($row['id'],array('ftype'=>20));
      $type_dss[$row['type']] = $interface_data['values'][$row['id']];
      unset ($interface_data);
    }
    if (!array_key_exists($row['type'], $type_dss)) {
      logger("I$row[id] : has no values.\n");
      continue;
    }

    $dss = &$type_dss[$row['type']];
    $values = analyzer_fetch ($row['id'], $start_time, $end_time, $dss);

    if (!is_array($values))
    {
      logger("I$row[id] : RRD File(s) not found.\n");
      continue;
    }
    foreach ($dss as $ds=>$aux)
      if (isset($values[$ds]))
        $text[]=$ds.'('.$values[$ds].')';

    logger("I$row[id]. : ".str_repeat("=",90)."\n");
    logger("I$row[id]. : Start: ".date('Y-m-d H:i:s',$values['information']['start']).
      "\tStop: ".date("Y-m-d H:i:s",$values['information']['stop']).
      "\tMeasures: ".$values['information']['measures']."\n");
    logger("I$row[id] : ".join(' ',$text)."\n");
    logger("I$row[id] : ".str_repeat('-',90)."\n");

    //Call the Analyzers
    $analyzers = array ('sla');
    $function_data = array ($row,&$values);
    foreach ($analyzers as $analyzer_command)
    {
      $analyzer_function = "analyzer_$analyzer_command";
      $analyzer_file = $Config->get('jffnms_real_path')."/engine/analyzers/$analyzer_command.inc.php";

      if (!in_array($analyzer_file, get_included_files()))
      {
        if (!is_readable($analyzer_file))
        {
          logger("ERROR: Analyzer file '$analyzer_file' is not readable.\n");
          continue;
        }
        require_once($analyzer_file);
      }
      if (!function_exists($analyzer_function))
      {
        logger("ERROR: Analyzer function '$analyzer_function' not found in plugin file '$analyzer_file'.\n");
        continue;
      }
      $result = call_user_func_array($analyzer_function,$function_data);
      //show result
      if (is_array($result))
        foreach ($result as $aux)
          logger("I$row[id] : $analyzer_function : $aux\n");
      unset ($result);
    }//foreach
    unset ($values);
    unset ($text);
  }//while row
  db_close();
}

//FIXME what happens when you need to could all values for the average, event the 0, like for packetloss
function get_average($data)
{
  $data_points = count ($data);
  $values = 0;
  $result = 0;

  for ($i = 0;$i < $data_points ; $i++)
    if ($data[$i]!=0) //avoid counting when there's a 0 there. (Don't know if its ok) FIXME
    {
      $result += $data[$i];
      $values++;
    }
  if ($values==0) $values = 1; //avoid divide by 0
  return round($result/$values);
}

function analyzer_fetch($interface_id,$from,$to,$dss)
{
    jffnms_load_api('rrdtool');
  $dss_names = array_keys($dss);
  $values = rrdtool_fetch_ds($interface_id, $dss_names, "-$from", "-$to");
  if (!is_array($values))
    return FALSE;
  foreach ($values as $value_name=>$value_data)
    if ($value_name!='information')
      $result[$value_name] = get_average($value_data); //FIXME how to handle Last Value Deltas and 95th Percentile
  $result['information']=$values['information'];
  $result['information']['measures'] = count ($values[current(array_keys($values))]);
  unset ($values);
  return $result;
}

function parse_commandline()
{
  global $opt_interface;
  $num_params = $GLOBALS['argc']-1;

  $longopts = array( 'help', 'version', 'interface:');
  if ( ($opts = getopt('i:oVH', $longopts)) === FALSE)
  {
    return;
  }
  foreach ($opts as $opt => $opt_val)
  {
    switch ($opt)
    {
    case 'interface':
    case 'i':
      $num_params-= 2;
      $opt_interface = $opt_val;
      if (!is_numeric($opt_interface))
        print_help('interface ID option must be numeric');
      break;
    case 'help':
    case 'H':
      print_help();
      break;
    case 'version':
    case 'V':
      print_version();
    }// switch
  }//for
  if ($num_params > 0) print_help('Problem with command line options.');
}

function print_help($errmsg = FALSE)
{
  if ($errmsg)
    print "$errmsg\n";
  print $_SERVER['SCRIPT_NAME'] . ' [options]
    -i, --interface <ID>   Analyze interface <ID> only
    -H, --help             Print this help
    -V, --version          Print version information
    ';
die;
}
?>
