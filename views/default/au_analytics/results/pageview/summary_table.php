<?php

namespace AU\Analytics;

set_time_limit(0); // we could be at this a while
// we're only allowing admins to do stats at the moment
if (!elgg_is_admin_logged_in()) {
	return;
}

$wheres = array();

if (is_array($vars['members']) && count($vars['members'])) {
	$members = array_map(function($v) {
		return sanitize_int($v);
	}, $vars['members']);
	$wheres[] = " guid IN (" . implode(',', $members) . ")";
}

if ($vars['pageview_url']) {
	$parts = parse_url($vars['pageview_url']);
	if ($parts['path']) {
		$path = sanitize_string($parts['path']);
		$wheres[] = " path = '{$path}'";
	}
	if ($parts['query']) {
		$query = sanitize_string($parts['query']);
		$wheres[] = " query = '{$query}'";
	}
}

if ($vars['time_upper']) {
	$time_upper = sanitize_int($vars['time_upper']);
	$wheres[] = " timestamp < {$time_upper}";
}

if ($vars['time_lower']) {
	$time_lower = sanitize_int($vars['time_lower']);
	$wheres[] = " timestamp >= {$time_lower}";
}

echo '<table id="au_analytics_timeline_table" class="tablesorter">';
echo '<thead><tr>';
//echo '<th>' . elgg_echo('au_analytics:pageview:header:user') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:url') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:viewcount') . '</th>';
echo '</tr></thead>';
echo '<tbody>';

$dbprefix = elgg_get_config('dbprefix');
$sql = "SELECT path, COUNT(path) as count FROM {$dbprefix}au_analytics_pageviews";

if ($wheres) {
	$sql .= " WHERE" . implode(" AND", $wheres);
}

$sql .= " GROUP BY path";

$sql .= " ORDER BY count DESC";

$results = get_data($sql);
//echo '<pre>' . print_r($result,1) . '</pre>';

foreach ($results as $result) {
	$host = parse_url(elgg_get_site_url(), PHP_URL_HOST);
	$scheme = parse_url(elgg_get_site_url(), PHP_URL_SCHEME);
	$url = $scheme . '://' . $host . $result->path;
	
	echo '<td><a href="' . $url .'">' . $url . '</a></td>';
	echo '<td>' . $result->count . '</td></tr>';
}

echo '</tbody></table>';

echo <<<HTML
    <script>
    \$(document).ready(function() { 
        \$("#au_analytics_timeline_table").tablesorter({widthFixed: true, widgets: ['zebra']}); 
    });
</script>
HTML;
