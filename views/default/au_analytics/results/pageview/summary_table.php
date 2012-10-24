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

echo '<table id="au_analytics_timeline_table" class="tablesorter">';
echo '<thead><tr>';
//echo '<th>' . elgg_echo('au_analytics:pageview:header:user') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:url') . '</th>';
echo '<th>' . elgg_echo('au_analytics:pageview:header:viewcount') . '</th>';
echo '</tr></thead>';
echo '<tbody>';

// pagination stuff
// need to count unique values within our parameters

$batch = new ElggBatch('elgg_get_annotations', $options, 'au_analytics_output_pageview_summary_table', 50);

global $PAGEVIEW_SUMMARY;
foreach ($PAGEVIEW_SUMMARY as $url => $count) {
  echo '<td><a href="' . $url . '">' . $url . '</a></td>';
  echo '<td>' . $count . '</td></tr>';
}

echo '</tbody></table>';
  
echo <<<HTML
    <script>
    \$(document).ready(function() { 
        \$("#au_analytics_timeline_table").tablesorter({widthFixed: true, widgets: ['zebra']}); 
    });
</script>
HTML;
