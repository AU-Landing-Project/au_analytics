<?php

namespace AU\Analytics;

set_time_limit(0); // we could be at this a while
// we're only allowing admins to do stats at the moment
if (!elgg_is_admin_logged_in()) {
	return;
}

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

$options['offset'] = $vars['offset'] ? $vars['offset'] : 0;

$options['count'] = true;


$dbprefix = elgg_get_config('dbprefix');
$sql = "SELECT * FROM {$dbprefix}au_analytics_pageviews";
$count_sql = "SELECT COUNT(guid) as count FROM {$dbprefix}au_analytics_pageviews";

if ($wheres) {
	$where_sql = " WHERE" . implode(" AND", $wheres);
	$sql .= $where_sql;
	$count_sql .= $where_sql;
}

$sql .= " ORDER BY timestamp DESC";

$offset = $vars['offset'] ? sanitize_int($vars['offset']) : 0;
$limit = $vars['limit'] ? sanitize_int($vars['limit']) : 100;
$sql .= " LIMIT {$offset}, {$limit}";

$results = get_data($sql);
$count_result = get_data($count_sql);

$count = $count_result[0]->count;

echo '<div class="au_analytics-pagination">';
echo elgg_view('navigation/pagination', array(
	'offset' => $offset,
	'count' => $count,
	'limit' => $limit,
	'base_url' => elgg_get_site_url(),
	'offset_key' => 'offset'
));
echo '<input type="hidden" name="au_pagination_viewtype" id="au_pagination_viewtype" value="timeline_table">';
echo '<input type="hidden" name="au_pagination_pageview_url" id="au_pagination_pageview_url" value="' . $vars['pageview_url'] . '">';
echo '<input type="hidden" name="au_pagination_time_upper" id="au_pagination_time_upper" value="' . $vars['time_upper'] . '">';
echo '<input type="hidden" name="au_pagination_time_lower" id="au_pagination_time_lower" value="' . $vars['time_lower'] . '">';
if (is_array($vars['members']) && count($vars['members'])) {
	foreach ($vars['members'] as $guid) {
		echo '<input type="hidden" name="au_pagination_members[]" value="' . $guid . '">';
	}
}
echo '</div>';

echo '<table id="au_analytics_timeline_table" class="tablesorter">';
echo '<thead><tr>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:user') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:url') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:timestamp') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:humandate') . '</th>';
echo '</tr></thead>';
echo '<tbody>';

foreach ($results as $result) {
	$owner = get_user($result->guid);

	if (elgg_instanceof($owner, 'user')) {
		$name = $owner->name;
		$owner_url = $owner->getURL();
	} else {
		$name = elgg_echo('au_analytics:not_logged_in_user');
		$owner_url = elgg_get_site_url();
	}
	
	$url = $result->scheme . '://' . $result->host . $result->path;
	if ($result->query) {
		$url .= '?' . $result->query;
	}

	echo '<tr><td><a href="' . $owner_url . '">' . $name . '</a></td>';
	echo '<td><a href="' . $url . '">' . $url . '</a></td>';
	echo '<td>' . $result->timestamp . '</td>';
	echo '<td>' . date('m/d/Y, g:ia', $result->timestamp) . '</td>';
}

echo '</tbody></table>';

echo <<<HTML
    <script>
    \$(document).ready(function() { 
        \$("#au_analytics_timeline_table").tablesorter({widthFixed: true, widgets: ['zebra'], sortList: [[0, 0],[2,1]],}); 
    });
</script>
HTML;
