<?php

namespace AU\Analytics;

function record_pageview($hook, $type, $return, $params) {
 $log_pageview = elgg_get_plugin_setting('pagelog', 'au_analytics');

  if ($log_pageview != 'no') {
	  $guid = elgg_get_site_entity()->guid;
	  if (elgg_is_logged_in()) {
		  $guid = elgg_get_logged_in_user_guid();
	  }

    create_annotation($guid, 'au_analytics_page_view', current_page_url());
  } 
}