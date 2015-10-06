<?php

namespace AU\Analytics;

// returns bool based on whether the current browser is IE < 9
function check_ie_pre9() {
	$match = preg_match('/MSIE ([0-9]\.[0-9])/', $_SERVER['HTTP_USER_AGENT'], $reg);
	if ($match == 0) {
		return false;
	}

	$version = floatval($reg[1]);

	if ($reg[1] < 9) {
		return true;
	}

	return false;
}


function load_graph_js() {
	// IE versions < 9 need special js
	if (check_ie_pre9()) {
		elgg_require_js('au_analytics/jqplot/canvas');
	}

	/*
	 *  get our css/js
	 */

	// jqplot
	elgg_load_css('au_analytics/jqplot');
	elgg_load_css('au_analytics/tablesorter');
	
	/*
	elgg_load_js('au_analytics/jqplot');
	elgg_load_js('au_analytics/jqplot/highlighter');
	elgg_load_js('au_analytics/jqplot/cursor');
	elgg_load_js('au_analytics/jqplot/dateaxis');
	elgg_load_js('au_analytics/jqplot/barRender');
	elgg_load_js('au_analytics/jqplot/categoryAxis');
	elgg_load_js('au_analytics/jqplot/pointLabels');
	elgg_load_js('au_analytics/jqplot/canvasAxisLabel');
	elgg_load_js('au_analytics/jqplot/canvasText');

	// tablesorter
	elgg_load_js('au_analytics/tablesorter');
	elgg_load_js('au_analytics/tablesorter/pager');
 * 
 */
}
