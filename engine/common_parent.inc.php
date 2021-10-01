<?php
/* Common functions for parent engine processes
 * Copyright (C) <2002-2011> JFFNMS Authors
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
require_once('../conf/config.php');

define ('ENGINE_READ_TIMEOUT', 3);
define ('ENGINE_MAX_TRIES', 2);
define ('ENGINE_HEARTBEAT', 30);
define ('ENGINE_MAX_POLL_TIME', 180);
define ('ENGINE_DIE_TIME', 5);
define ('ENGINE_MAX_CHILDREN', 5);
define ('ENGINE_SELECT_USEC', 2000); //PHP RECOMMENDATION is 200,000

require_once('../conf/config.php');
$Config = new JffnmsConfig();
config_load_libs('basic', 0);

class JffnmsEngineParent
{
    public $min_children=0;
    public $max_children=ENGINE_MAX_CHILDREN;
    public $polling_items = array();
    public $waiting_items = array();
    public $polling_count = 0;
    public $waiting_count = 0;

    public $child_procs = array();

    function __construct($opt_children)
    {
        if ($opt_children !== FALSE) {
            if (!preg_match('/^(\d+)(?:,(\d+)|)$/', $opt_children, $regs))
                print_help('children option must be MIN,MAX');
            if (count($regs) > 2) {
                $this->min_children = $regs[1];
                $this->max_children = $regs[2];
            } else
                $this->max_children = $regs[2];
            if ($this->max_children < $this->min_children)
                print_help('maximum children must be greater than minimum');
            if ($this->min_children < 0)
                print_help('minimum children must be 0 or more');
        }
    } // __construct

    ################################################################
    #
    # Item functions
    #
    public function items_new($new_items)
    {
        foreach ($new_items as $item_id => &$item) {
            if (!array_key_exists($item_id, $this->waiting_items))
                $this->waiting_count++;
            $this->waiting_items[$item_id] = $item;
        }
    } // items_new

    private function items_recount()
    {
        $new_wait = count($this->waiting_items);
        if ($this->waiting_count != $new_wait) {
            logger("PAR: Waiting count adjusted to $new_wait (was ".$this->waiting_count.")\n");
            $this->waiting_count = $new_wait;
        }
        $new_poll = count($this->polling_items);
        if ($this->polling_count != $new_poll) {
            logger("PAR: Polling count adjusted to $new_poll (was ".$this->polling_count.")\n");
            $this->polling_count = $new_poll;
        }
    }


    private function item_set_done($item_id)
    {
        if (!is_numeric($item_id)) {
            $this->items_recount();
            return;
        }
        if (!array_key_exists($item_id, $this->polling_items)) {
            logger("PAR: ** ERROR ** item_set_done(): Item $item_id does not exist in poller items.\n");
            $this->items_recount();
            return;
        }
        unset($this->polling_items[$item_id]);
        $this->polling_count--;
    } // item_set_done

    public function item_set_waiting($item_id)
    {
        if (!is_numeric($item_id)) {
            $this->items_recount();
            return;
        }
        if (!array_key_exists($item_id, $this->polling_items)) {
            logger("PAR: ** ERROR ** item_set_waiting(): Item $item_id does not exist in poller items.\n");
            $this->items_recount();
            return;
        }
        if ($this->polling_items[$item_id]['tries'] > ENGINE_MAX_TRIES) {
            logger("PAR: Cannot process item $item_id after ".ENGINE_MAX_TRIES." attempts.\n");
        } else {
            $this->waiting_items[$item_id] = $this->polling_items[$item_id];
            $this->waiting_count++;
        }
        unset($this->polling_items[$item_id]);
        $this->polling_count--;
    } // item_set_waiting

    public function item_set_polling($item_id)
    {
        if (!is_numeric($item_id)) {
            $this->items_recount();
            return;
        }
        if (!array_key_exists($item_id, $this->waiting_items)) {
            logger("PAR: ** ERROR ** item_set_polling(): Item $item_id does not exist in waiting items.\n");
            $this->items_recount();
            return;
        }
        $this->polling_items[$item_id] = $this->waiting_items[$item_id];
        unset($this->waiting_items[$item_id]);
        $this->polling_count++;
        $this->waiting_count--;
    } // item_set_polling

    /*
     * item_check_poll_time()
     * Scans the list of items and checks that the poller for these
     * items has not hung. If so, kill that child and retry it later
     */
    public function item_check_poll_time()
    {
        // Time where the poller started, earlier than this is a dead proc
        $min_poll_time = time() - ENGINE_MAX_POLL_TIME;
        foreach ($this->polling_items as $item_id => &$item)
        {
            if ($item['poll_time'] < $min_poll_time) {
                $this->stop_child($item['child_id'], 'item past max poll time');
                $this->item_set_waiting($item_id);
            }
        }
    } // item_check_poll_time()

    ################################################################
    #
    # Children fnuctions
    #

    /*
     * children_start
     * Checks required number of children and starts them if needed
     * returns number of children started
     */
    public function children_start()
    {
        $children_started = 0;
        $children_count = count($this->child_procs);
        if ($children_count >= $this->max_children)
            return 0;
        $required_children = min($this->max_children, ($this->waiting_count + $this->polling_count));
        if ($required_children < $this->min_children)
            $required_children = $this->min_children;

        while ($children_count < $required_children) {
            $new_child = $this->start_child();
            if ($new_child === FALSE)
                return $children_started;
            $this->child_procs[$this->get_child_id()] = $new_child;
            $children_count++;
            $children_started++;
        }
        return $children_started;
    } // children_start()

    public function get_child_id()
    {
        $id=0;
        while(TRUE) {
            if (!array_key_exists($id, $this->child_procs))
                return $id;
            $id++;
        }
    }
    public function stop_all_children($reason)
    {
        foreach ($this->child_procs as $child_id => $child)
            $this->stop_child($child_id, $reason);
    }

    public function stop_child($child_id, $reason)
    {
        if (!array_key_exists($child_id, $this->child_procs)) {
            logger("PAR: ** FATAL ** Child ID $child_id stopped but doesn't exist.\n");
            die();
        }
        logger("CH$child_id (".$this->child_procs[$child_id]['pid']."): stopped: $reason.\n");
        $this->item_set_waiting($this->child_procs[$child_id]['item_id']);
        $this->child_procs[$child_id]['item_id']=FALSE;
        $this->child_procs[$child_id]['state']='DEAD';
        $this->child_procs[$child_id]['killtime'] = time();
        $this->send_to_child($child_id, 'DIE');
    } // stop_child

    public function check_children()
    {
        $child_count = count($this->child_procs);
        $waiting = $this->waiting_count;
        $now = time();
        foreach ($this->child_procs as $child_id => $child) {
            // Check that the child process actually exists
            $child_stat = proc_get_status($child['resource']);
            if ($child_stat === FALSE ) {
                logger("CH:$child_id no status - removing.\n");
                $this->item_set_waiting($child['item_id']);
                unset($this->child_procs[$child_id]);
                $child_count--;
                continue;
            }
            if ($child_stat['running'] === FALSE) {
                if ($child['state'] != 'DEAD')
                    logger("CH:$child_id No longer running - removing.\n");
                $this->item_set_waiting($child['item_id']);
                proc_close($child['resource']);
                unset($this->child_procs[$child_id]);
                $child_count--;
                continue;
            }

            // Make sure we've heard from our kids
            if ($now - $child['lastseen'] > (ENGINE_HEARTBEAT*3)) {
                if ($child['state'] == 'DEAD') {
                    posix_kill($child['pid']);
                    proc_close($child['resource']);
                    unset($this->child_procs[$child_id]);
                } else {
                    $this->stop_child($child_id, 'heartbeat expired');
                }
            }



            switch ($child['state']) {
            case 'IDLE':
                // Kill off any excess
                if ($child_count > $this->min_children && --$waiting <= 0) {
                    $this->stop_child($child_id, 'not needed');
                    $child_count--;
                }
                break;
            case 'DEAD':
                if ($child['killtime'] + ENGINE_DIE_TIME < $now) {
                    logger("CH:$child_id ($child[pid]): took too long to die.\n");
                    proc_close($child['resource']);
                    unset($this->child_procs[$child_id]);
                    $child_count--;
                }
                break;
            }// switch state
            
        } // foreach children
    }

    public function read_children($callback_function)
    {
        $read_fds = array();
        $write_fds = NULL;
        $except_fds = NULL;
        $now = time();

        foreach($this->child_procs as $child_id => &$child_proc)
            if (is_resource($child_proc['output']))
                $read_fds[] = $child_proc['output'];
        if (sizeof($read_fds) == 0)
            return FALSE;

        $child_had_data = FALSE;
        if ( ($nr = stream_select($read_fds, $write_fds, $except_fds, 0)) !== FALSE) {
            if ($nr == 0)
                return FALSE;
            foreach($this->child_procs AS $child_id => &$child_proc) {
                if ( in_array($child_proc['output'], $read_fds)) {
                    if ( ($raw_data = fgets($child_proc['output'])) === FALSE)
                        continue;
                    $child_proc['lastseen'] = $now;
                    $child_data = @unserialize($raw_data);
                    //print_r($child_data);
                    if (!is_array($child_data)) {
                        // If unserialise fails, it needs to be logged
                        logger("CH$child_id :  RAW:".preg_replace('/^\d\d\:\d\d\:\d\d /','',$raw_data));
                        $child_had_data = TRUE;
                        continue;
                    }
                    if (!array_key_exists('cmd', $child_data))
                    {
                        logger("CH#$child_id: ERROR sent data with no cmd.");
                        $child_had_data = TRUE;
                        continue;
                    }
                    switch($child_data['cmd'])
                    {
                    case 'HEARTBEAT':
                        $child_had_data = TRUE;
                        break;
                    case 'START':
                        if ($child_proc['state'] != 'STARTING')
                        {
                            logger("CH$child_id ($child_proc[pid]): sending START after starting.\n");
                        } else {
                            if (array_key_exists('pid', $child_data)) {
                                $child_proc['pid'] = $child_data['pid'];
                                $child_proc['state'] = 'IDLE';
                                logger("CH:$child_id ($child_proc[pid]): START and idle.\n");
                            } else {
                                logger("CH:$child_id (unknown): ERROR: child started and did not return PID.\n");
                            }
                        }
                        $child_had_data = TRUE;
                        break;
                    case 'DEBUG':
                        if (array_key_exists('msg', $child_data)) {
                            logger("CH:$child_id ($child_proc[pid]): DEBUG\n");
                            foreach ($child_data['msg'] as $line)
                            logger("  $line\n");
                        } else
                            logger("CH:$child_id ($child_proc[pid]): got DEBUG with no message\n");
                        $child_had_data = TRUE;
                        break;

                    case 'LOGGER':
                        if (array_key_exists('msg', $child_data)) {
                            $msg = stripslashes($child_data['msg']);
                            logger("CH:$child_id ($child_proc[pid]): $msg\n");
                        } else
                            logger("CH:$child_id ($child_proc[pid]): logger called with no msg.\n");
                        $child_had_data = TRUE;
                        break;
                    case 'ERROR':
                        if (array_key_exists('msg', $child_data))
                            logger("CH$child_id ($child_proc[pid]): ERROR: \"$child_data[msg]\"\n");
                        else
                            logger("CH$child_id ($child_proc[pid]): got error \"unknown error\"\n");
                        $child_had_data = TRUE;
                        break;
                    default:
                        call_user_func_array($callback_function,array(&$this, $child_id, $child_data));
                        $child_had_data = TRUE;
                        break;
                    }// case
                }//child output inarry
            }//foreach
        }//nr not false
        return $child_had_data;
    } // read_children()

    /*
     * work_children
     *
     * Check the current items we have and send jobs to idle children
     */
    function work_children()
    {
        $worked_child=FALSE;
        if ($this->waiting_count == 0)
            return FALSE;

        foreach ($this->waiting_items as $item_id => &$item) {
            $found_idle_child=FALSE;
            foreach($this->child_procs as $child_id => &$child) {
                if ($child['state'] != 'IDLE')
                    continue;
                $found_idle_child=TRUE;
                $worked_child=TRUE;
                $child['state'] = 'BUSY';
                $child['item_id'] = $item_id;
                $item['child_id'] = $child_id;
                $item['tries']++;
                $item['poll_time'] = time();
                $this->item_set_polling($item_id);
                logger("PAR: Child $child_id was idle, working on item $item_id Try $item[tries]\n");
                $this->send_to_child($child_id, 'POLL',$item);
                break;
            }// foreach child
            if ($found_idle_child === FALSE)
                return $worked_child; // no point looping when everyone is busy
        }//foreach waiting items
        return $worked_child;
    }//work_children

    public function print_status()
    {
        $child_count = array('starting'=>0, 'idle'=>0, 'busy'=>0,'dead'=>0,
            'other'=>0, 'total'=>0 );
        logger('PAR: Items Waiting/Polling=Total: '.
            $this->waiting_count.'/'.$this->polling_count."\n");

        foreach($this->child_procs as $child) {
            $child_count['total']++;
            switch($child['state']) {
            case 'STARTING': $child_count['starting']++; break;
            case 'IDLE': $child_count['idle']++; break;
            case 'BUSY': $child_count['busy']++; break;
            case 'DEAD': $child_count['dead']++; break;
            default: $child_count['other']++; break;
            }
        }
        logger('PAR: Childn Min/Max Idl/Bsy/Srt/Ded/Otr=Tot: '.
            $this->min_children.'/'.
            $this->max_children.' '.
            "$child_count[idle]/$child_count[busy]/$child_count[starting]/$child_count[dead]/$child_count[other]=$child_count[total]\n");
    } //children_print_status()

    // Returns the item ID
    public function child_done_job($child_id)
    {
        if (array_key_exists($child_id, $this->child_procs)) {
            $item_id = $this->child_procs[$child_id]['item_id'];
            $this->item_set_done($item_id);
            $this->child_procs[$child_id]['state'] = 'IDLE';
            $this->child_procs[$child_id]['item_id']=FALSE;
            return $item_id;
        }
        logger("** ERROR ** child_done_job(): Child ID $child_id not found.\n");
        return FALSE;
    } // child_done_job

    public function child_notdone_job($child_id)
    {
        if (array_key_exists($child_id, $this->child_procs)) {
            $child = $this->child_procs[$child_id];
            $item_id = $child['item_id'];
            logger("CH:$child_id (): ** ERROR ** failed to process item $item_id.\n");
            $this->item_set_waiting($item_id);
            $this->child_procs[$child_id]['state'] = 'IDLE';
            $this->child_procs[$child_id]['item_id'] = FALSE;
            return $item_id;
        }
        logger("CH: $child_id(): ** ERROR ** child_notdone_job(): Child ID $child_id not found.\n");
        return FALSE;
    } // child_notdone_job()

    ############################################################
    #
    # Private methods
    private function start_child()
    {
        global $Config;
        $command = $Config->get('php_executable').' -q '.CHILD_FILE.' '.ENGINE_HEARTBEAT;
        if ($Config->get('os_type') == 'unix')
            $command = 'exec '. $command; // exec means replace the shell

        $res = proc_open($command, array(0=> array('pipe','r'), 1=>array('pipe','w')), $pipes);
        sleep(1);

        if (is_resource($res))
        {
            stream_set_blocking($pipes[1], false);
            $child = array('resource'=>$res, 'input'=>$pipes[0], 'output'=>$pipes[1], 'state'=>'STARTING','lastseen'=>time(),'pid'=>-1);
            return $child;
        }    
        return FALSE;            
    } //start_child()

    private function send_to_child($child_id, $cmd, $args=array())
    {
        $args['cmd'] = $cmd;
        $msg = serialize($args)."\n";
        $msglen = strlen($msg);
        if ( fwrite($this->child_procs[$child_id]['input'], $msg, $msglen) != $msglen)
            logger("CH:$child_id(): ** ERROR ** Parent unable to send message to child.\n");
        fflush($this->child_procs[$child_id]['input']);
    }
} //class


?>
