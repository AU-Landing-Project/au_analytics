<?php

namespace AU\Analytics;

/**
 * start of versioning
 */
function upgrade20151005() {
	$version = (int) elgg_get_plugin_setting('version', PLUGIN_ID);
	if ($version >= PLUGIN_VERSION) {
		return true;
	}
	
	elgg_set_plugin_setting('version', 20151005, PLUGIN_ID);
}
