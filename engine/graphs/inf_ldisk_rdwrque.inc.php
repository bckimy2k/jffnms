<?php
/* SNMP Informant Disk Graph. This file is part of JFFNMS.
 * Copyright (C) <2005> Sebastian van Dijk <relaxteb@hotmail.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
*/

function graph_inf_ldisk_rdwrque ($data) {

        $opts_DEF = rrdtool_get_def($data,array("rd_queue"=>"avg_disk_rdq","wr_queue"=>"avg_disk_wrq"));

        $opts_GRAPH = array(
            "CDEF:ldisk_rate_total=rd_queue,wr_queue,+",
            "CDEF:wr_queue_graph=wr_queue,-1,*",

            "COMMENT:'  Disk Total '",
            "GPRINT:ldisk_rate_total:MAX:'Max\:%8.2lf %s '",
            "GPRINT:ldisk_rate_total:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:ldisk_rate_total:LAST:'Last\:%8.2lf %s \\n'",

            "AREA:rd_queue#0000CC:'RD. Disk Queue '",
            "GPRINT:rd_queue:MAX:'Max\:%8.2lf %s '",
            "GPRINT:rd_queue:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:rd_queue:LAST:'Last\:%8.2lf %s \\n'",

            "AREA:wr_queue_graph#FF0000:'WR. Disk Queue '",
            "GPRINT:wr_queue:MAX:'Max\:%8.2lf %s '",
            "GPRINT:wr_queue:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:wr_queue:LAST:'Last\:%8.2lf %s \\n'"
        );

        $opts_header[] = "--vertical-label='RD/WR Disk Queue'";

        return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));
    }

?>



