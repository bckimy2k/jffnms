<?php
/* Poller 3.0 This file is part of JFFNMS
 * Copyright (C) 2004-2011 JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once('../conf/config.php');
$Config = new JffnmsConfig();
config_load_libs('basic', 0);

class JffnmsEngineChild
{
    public $stdin;
    public $stdout;

    private $heartbeat;
    private $included_files = array();

    function __construct($argv)
    {
        $this->heartbeat = $argv[1];
        if (empty($this->heartbeat))
            die ("No heartbeat given on command line.\n");

        $this->stdin = fopen('php://stdin', 'r');
        $this->stdout = fopen('php://stdout', 'w');
        stream_set_blocking($this->stdin, 0);
        stream_set_blocking($this->stdout, 0);

        if ( ($pid = getmypid()) === FALSE)
            die('getmypid() returned FALSE');
        $this->send_array('START', array('pid'=>$pid));
    }

    public function send_array($cmd, $args=array())
    {
        $args['cmd'] = $cmd;
        $msg = serialize($args)."\n";
        $msglen = strlen($msg);
        if ( fwrite($this->stdout, $msg, $msglen) != $msglen)
            die("Cannot write message to parent.\n");
        fflush($this->stdout);
    }
    public function debug($item)
    {
        $debug_msg = explode("\n", print_r($item, TRUE));
        $this->send_array("DEBUG",array('msg'=>$debug_msg));
    }

    public function logger($msg)
    {
        $this->send_array('LOGGER', array('msg'=>addslashes(rtrim($msg))));
    }
    public function send_error($msg)
    {
        $this->send_array('ERROR', array('msg'=>rtrim($msg)));
    }
    public function check_heartbeat($old_time)
    {
        if ( ($old_time + $this->heartbeat) < time()) {
            $this->send_array('HEARTBEAT');
            $old_time = time();
        }
        return $old_time;
    } // check_heartbeat

    /*
     * returns TRUE if found FALSE if not
     */
    public function require_file($filename, $command)
    {
        if (array_key_exists($filename,$this->included_files))
            return $this->included_files[$filename];

        if (!is_readable($filename)) {
            logger("ERROR: $filename is not readable",0);
            $this->included_files[$filename] = FALSE;
            return FALSE;
        }
        require_once($filename);
        if (!function_exists($command)) {
            $this->included_files[$filename] = FALSE;
            logger("ERROR: Command '$command' not found in file '$filename'.\n",0);
            return FALSE;
        }
        $this->included_files[$filename] = TRUE;
        return TRUE;
    } // require_file
} // class JffmnsEngineChild

function child_debug($item)
{
    global $Child;
    $Child->debug($item);
}


?>
