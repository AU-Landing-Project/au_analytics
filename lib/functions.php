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
	 *  get our css
	 */

	// jqplot
	elgg_load_css('au_analytics/jqplot');
	elgg_load_css('au_analytics/tablesorter');
}

function install_pageview_table() {
	$dbprefix = elgg_get_config('dbprefix');
	$sql = "CREATE TABLE IF NOT EXISTS `{$dbprefix}au_analytics_pageviews` (
  `guid` int(11) NOT NULL,
  `scheme` tinytext NOT NULL,
  `host` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `query` varchar(255) NOT NULL,
  `timestamp` int(32) NOT NULL,
  KEY `guid` (`guid`),
  KEY `host` (`host`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	
	update_data($sql);
}
