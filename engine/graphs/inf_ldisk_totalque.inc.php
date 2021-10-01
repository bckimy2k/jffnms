<?php
/* SNMP Informant Disk Graph. This file is part of JFFNMS.
 * Copyright (C) <2005> Sebastian van Dijk <relaxteb@hotmail.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
*/

function graph_inf_ldisk_totalque ($data) {

        $opts_DEF = rrdtool_get_def($data,array("curr_queue"=>"cur_disk_q","avg_queue"=>"avg_disk_q"));

        $opts_GRAPH = array(
            "CDEF:ldisk_rate_total=curr_queue,avg_queue,+",
            "CDEF:avg_queue_graph=avg_queue,-1,*",

            "COMMENT:'  Disk Total '",
            "GPRINT:ldisk_rate_total:MAX:'Max\:%8.2lf %s '",
            "GPRINT:ldisk_rate_total:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:ldisk_rate_total:LAST:'Last\:%8.2lf %s \\n'",

            "AREA:curr_queue#0000CC:'Curr. Disk Queue '",
            "GPRINT:curr_queue:MAX:'Max\:%8.2lf %s '",
            "GPRINT:curr_queue:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:curr_queue:LAST:'Last\:%8.2lf %s \\n'",

            "AREA:avg_queue_graph#FF0000:'Avg. Disk Queue '",
            "GPRINT:avg_queue:MAX:'Max\:%8.2lf %s '",
            "GPRINT:avg_queue:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:avg_queue:LAST:'Last\:%8.2lf %s \\n'"
        );

        $opts_header[] = "--vertical-label='AVG/Curr Disk Queue'";

        return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));
    }

?>

