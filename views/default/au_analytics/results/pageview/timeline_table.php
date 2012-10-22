<?php

set_time_limit(0); // we could be at this a while

// we're only allowing admins to do stats at the moment
if (!elgg_is_admin_logged_in()) {
  return;
}

$options = array(
    'annotation_names' => array('au_analytics_page_view'),
    'limit' => 0,
);

if (count($vars['members'])) {
  $options['annotation_owner_guids'] = $vars['members'];
}

if ($vars['pageview_url']) {
  $options['annotation_values'] = array($vars['pageview_url']);
}

if ($vars['time_upper']) {
  $options['annotation_created_time_upper'] = $vars['time_upper'];
}

if ($vars['time_lower']) {
  $options['annotation_created_time_lower'] = $vars['time_lower'];
}

$options['limit'] = 100;
$options['offset'] = $vars['offset'] ? $vars['offset'] : 0;

$options['count'] = true;
$count = elgg_get_annotations($options);
$options['count'] = false;

echo '<div class="au_analytics-pagination">';
echo elgg_view('navigation/pagination', array(
    'offset' => $options['offset'],
    'count' => $count,
    'limit' => $options['limit'],
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

$batch = new ElggBatch('elgg_get_annotations', $options, 'au_analytics_output_pageview_timeline_table', 50);

echo '</tbody></table>';
  
echo <<<HTML
    <script>
    \$(document).ready(function() { 
        \$("#au_analytics_timeline_table").tablesorter({widthFixed: true, widgets: ['zebra'], sortList: [[0, 0],[2,1]],}); 
    });
</script>
HTML;
