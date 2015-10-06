<?php

namespace AU\Analytics;

function record_pageview($hook, $type, $return, $params) {
	$log_pageview = elgg_get_plugin_setting('pagelog', 'au_analytics');

	if ($log_pageview == 'yes') {
		$guid = elgg_get_site_entity()->guid;
		if (elgg_is_logged_in()) {
			$guid = elgg_get_logged_in_user_guid();
		}

		$viewtype = elgg_get_viewtype();
		if ($viewtype == 'default') {
			$dbprefix = elgg_get_config('dbprefix');
			$parts = parse_url(current_page_url());
			$timestamp = time();

			$sql = "INSERT INTO {$dbprefix}au_analytics_pageviews"
					. " (guid, scheme, host, path, query, timestamp)"
					. " VALUES ({$guid}, '{$parts['scheme']}', '{$parts['host']}', '{$parts['path']}', '{$parts['query']}', {$timestamp})";

			insert_data($sql);
		}
	}
}
