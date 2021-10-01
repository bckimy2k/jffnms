<?php
/* Dell OpenManage Fan. This file is part of JFFNMS
 * Copyright (C) 2008  blentz at users.sourceforge.net
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */

function graph_dell_om_fan ($data) {
	$color_array = array('3','C','F');
	foreach ($color_array as $a)
	foreach ($color_array as $c)
	foreach ($color_array as $e)
	$colors[] = $e.'0'.$c.'0'.$a.'0';

	$opts_DEF = array();
	$opts_GRAPH = array();

	for ($id = 1; $id <= 7; $id++) {
		$used_color = $colors[++$i*2];
		$opts_DEF = array_merge($opts_DEF, "dell_om_fan_$id");
		$opts_GRAPH = array_merge($opts_GRAPH,
			"LINE2:dell_om_fan_$id#$used_color:'Fan speed in RPM'",
			"GPRINT:dell_om_fan_$id:MAX:'Max\:%5.0lf'",
			"GPRINT:dell_om_fan_$id:AVERAGE:'Average\:%5.0lf'",
			"GPRINT:dell_om_fan_$id:LAST:'Last\:%5.0lf \\n'"
		);
	}

	$opts_DEF = rrdtool_get_def($data,$opts_DEF);
	$opts_header[] = "--vertical-label='Speed'";

	return array ($opts_header, @array_merge($opts_DEF,$opts_GRAPH));

} 

?>
