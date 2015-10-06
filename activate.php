<?php

namespace AU\Analytics;

$version = elgg_get_plugin_setting('version', PLUGIN_ID);
if (!$version) {
	elgg_set_plugin_setting('version', PLUGIN_VERSION, PLUGIN_ID);
}

$pagelog = elgg_get_plugin_setting('pagelog', PLUGIN_ID);
if (!$pagelog) {
	elgg_set_plugin_setting('pagelog', 'no', PLUGIN_ID);
}

install_pageview_table();
