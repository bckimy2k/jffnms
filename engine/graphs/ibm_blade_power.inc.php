<?php
/* Blade Power consumption Graph. This file is part of JFFNMS
 * Copyright (C) <2005> David LIMA  <dlima@fr.scc.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

   function graph_ibm_blade_power ($data) {

       $opts_DEF = rrdtool_get_def($data,array("fuelGaugePowerInUse"));

           $opts_GRAPH = array(
           "AREA:fuelGaugePowerInUse#0033AA:'Watts '",
           "GPRINT:fuelGaugePowerInUse:MAX:'Max\: %6.2lf %sWatts'",
           "GPRINT:fuelGaugePowerInUse:AVERAGE:'Average\: %6.2lf %sWatts'",
           "GPRINT:fuelGaugePowerInUse:LAST:'Last\: %6.2lf %sWatts\\n'",
	       );

           $opts_header[] = "--vertical-label='Power'";

       return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));
		       }

?>
