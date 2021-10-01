<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * Copyright (c) <2009> Craig Small <csmall@enc.com.au>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

# Start here
#
$file_argv=1;
$new_ids=array();

// Defaults
$opt_x = FALSE;
$opt_output = FALSE;


error_reporting(E_ALL|E_STRICT);
$options = getopt('c:ho:vx');
foreach($options as $opt => $value) {
    switch ($opt) {
    case 'c':
        if (is_array($value))
            usage("Can only use the -c option once'");
        $opt_conf = $value;
        $file_argv+=2;
        break;
    case 'h':
        usage();
        break;
    case 'o':
        if (is_array($value))
            usage("Can only use the -o option once.");
        $opt_output = $value;
        $file_argv+=2;
        break;
    case 'v':
        version();
        break;
    case 'x':
        $opt_x = TRUE;
        break;
    }
}
if (isset($opt_conf))
    $config_dir = $opt_conf;
else
    $config_dir = '../../conf';
$config_file = $config_dir . '/config.php';
if (!is_readable($config_file)) {
    perr("Cannot open configuration file '$config_file'.\n");
    exit(1);
}
require($config_file);
error_reporting(E_ALL|E_STRICT);

if (!array_key_exists($file_argv, $_SERVER['argv']))
    usage('Need to specify input file.');

$input_file = $_SERVER['argv']["$file_argv"];
if (!is_readable($input_file)) {
    perr("Cannot open input file '$input_file'.\n");
    exit(1);
}
if ($opt_output) {
    if (file_exists($opt_output)) {
        perr("Output file '$opt_output' exists, aborting.\n");
        exit(1);
    }
    if ( ($outfp = fopen($opt_output,"w")) === FALSE) {
        perr("Cannot open output file '$opt_output' for writing.\n");
        exit(1);
    }
} else {
    $outfp = STDOUT;
}

$data = file ($input_file);
foreach ($data as $line)
{
    if (preg_match("/INSERT INTO (\S+) \(([^)]+)\) VALUES (\(.+\))/i", $line, $matches))
    {
        list (,$table, $fields, $value_list) = $matches;

	        $table = str_replace("`", "", $table);
	        $fields = sql_values($fields);
            if (preg_match_all("/\(([^)]+)\)/",$value_list,$matches)>0) {
                foreach($matches[1] as $values) {
	        $values = sql_values($values);
		
		for ($i=0; $i < count($fields); $i++)
		    $rec[str_replace("'","",$fields[$i])]=$values[$i]; 

        if (!array_key_exists($table, $new_ids) 
            || !is_numeric($new_ids[$table]))
	    	    $new_ids[$table] = next_value($table);
		$rec["_new_id"] = $new_ids[$table]++;
		
		$records[$table][$rec["id"]] = $rec;
		unset ($rec);
            }
            }

        continue;
    }
    fputs($outfp,$line."\n");
} 

    foreach ($records as $table=>$recs) 
	foreach ($recs as $old_id=>$aux) {
	    $rec = &$records[$table][$old_id];

	    switch ($table) {
		case "interface_types":
		    new_value ($records, $rec, "autodiscovery_default_poller", "pollers_groups");
		    new_value ($records, $rec, "graph_default", "graph_types");
		    new_value ($records, $rec, "sla_default", "slas");
		break;

		case "interface_types_fields":
		    new_value ($records, $rec, "itype", "interface_types");
		break;

		case "graph_types":
		    new_value ($records, $rec, "type", "interface_types");
		break;

		case "syslog_types":
		    new_value ($records, $rec, "type", "types");
		break;

		case "types":
		    new_value ($records, $rec, "alarm_up", "types");
		break;

		case "pollers_groups":
		    new_value ($records, $rec, "interface_type", "interface_types");
		break;

		case "pollers_backend":
		    if ($rec["command"]=="'alarm'")
			new_value ($records, $rec, "parameters", "types");
		break;

		case "pollers_poller_groups":
		    new_value ($records, $rec, "poller", "pollers");
		    new_value ($records, $rec, "backend", "pollers_backend");
		    new_value ($records, $rec, "poller_group", "pollers_groups");
		break;
	    }	    
	    unset($rec);
	} 

    foreach ($records as $table=>$recs)  
	    foreach ($recs as $old_id=>$rec) {
	        $new_id = $rec["_new_id"];
	        unset ($rec["_new_id"]);
	    
	        $rec["id"] = $new_id;
	    fputs($outfp, regen_sql($table, $rec)."\n");
	}

function sql_values ($values) {
	$len = strlen($values);
	$parsed = array();
	$string = false;
	$data = "";
	
	for ($i = 0; $i < $len; $i++) {
	    $char = $values[$i];
	
	    if ($char=="`") $char = "'";
	    
	    if ($char=="'") //string start
		$string = !$string;
	
	    if (($char==",") && !$string) {
		$parsed[]=trim($data);
		$data = "";
		$string = false;
	    } else
		$data.= $char;
	}
	if (!empty($data) || ($data==="0")) $parsed[]=trim($data);
	
	return $parsed;
    }

    function next_value($table) {
        global $opt_x;
        //if ($opt_x == TRUE)
	      //  return(current(db_fetch_array(db_query("SELECT MAX(id) FROM ".$table.' WHERE id < 10000')))+1);
	    return(current(db_fetch_array(db_query("SELECT MAX(id) FROM ".$table)))+1);
    }
    
    function regen_sql ($table, $rec) {
	
	foreach ($rec as $field=>$value) {
	    $fields[]="`".$field."`";
	    $values[]=$value;
	}
	
	$fields = join(", ",$fields);
	$values = join(", ",$values);
	
	$query = "INSERT INTO `".$table."` (".$fields.") VALUES (".$values.");";
	
	return $query;
    }

function new_value (&$records, &$rec, $field, $lookup_table) {
	$orig_value = str_replace("'","",$rec[$field]);
	
	if ($orig_value > 10000) {
	    $new_value = $records[$lookup_table][$orig_value]["_new_id"];
    
        if (!is_numeric($orig_value)) {
            perr("ERROR $field (".$rec["id"]."): orig value = ".$orig_value."\n");
            die;
        }
	    elseif (!is_numeric($new_value))
        {
            perr("Error: could not find new id for field \"$field\" (".$rec["id"]."): orig value = ".$orig_value."\n");
            die;
        }
	    else
		$rec[$field] = $new_value;
	}
}
function perr($msg)
{
    fputs(STDERR,$msg);
}

function usage($message=NULL)
{
    if ($message)
        print $message."\n";
    print("
Usage: import_custom.php [ -vh ] [ -c CFGDIR ] [ -o outfile ]
Translate a custom made interface type.

    -c CFGDIR   Use CFGDIR for configation directory instead of ../../config
    -h          This help
    -o          Output to OUTFILE instead of stdout
    -v          Print tools version
");
    if ($message)
        exit(1);
    exit(0);
}

function version()
{
    print("
import_custom.php (JFFNMS) v2.0
    Copyright (C) 2002-2005 Javier Szyszlican <javier@szysz.com>
    Copyright (c) 2009 Craig Small <csmall@enc.com.au>

    JFFNMS comes with ABSOLUTELY NO WARRANTY.
    This is free software, and you are welcome to redistribute it under
    the terms of the GNU General Public License.
    For more information about these matters, see the files named COPYING.
");
    exit(0);
}
?>
