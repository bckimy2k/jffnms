<?php
/* SNMP Informant Disk Graph. This file is part of JFFNMS.
 * Copyright (C) <2005> Sebastian van Dijk <relaxteb@hotmail.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
*/

function graph_inf_ldisk_opsrate ($data) {

        $opts_DEF = rrdtool_get_def($data,array("rd_rate"=>"rd_ops","wr_rate"=>"wr_ops"));

        $opts_GRAPH = array(
            "CDEF:ldisk_rate_total=rd_rate,wr_rate,+",
            "CDEF:wr_rate_graph=wr_rate,-1,*",

            "COMMENT:'  Disk Total '",
            "GPRINT:ldisk_rate_total:MAX:'Max\:%8.2lf %s '",
            "GPRINT:ldisk_rate_total:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:ldisk_rate_total:LAST:'Last\:%8.2lf %s \\n'",

            "AREA:rd_rate#0000CC:'RD. Disk rate '",
            "GPRINT:rd_rate:MAX:'Max\:%8.2lf %s '",
            "GPRINT:rd_rate:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:rd_rate:LAST:'Last\:%8.2lf %s \\n'",

            "AREA:wr_rate_graph#FF0000:'WR. Disk rate '",
            "GPRINT:wr_rate:MAX:'Max\:%8.2lf %s '",
            "GPRINT:wr_rate:AVERAGE:'Average\:%8.2lf %s '",
            "GPRINT:wr_rate:LAST:'Last\:%8.2lf %s \\n'"
        );

        $opts_header[] = "--vertical-label='RD/WR Disk rate'";

        return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));
    }

?>



