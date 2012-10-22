<?php

//
// Limit to specific users
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:pageview:userpicker') . "<br>";
echo elgg_view('input/userpicker', array('id' => 'pageview-users'));
echo '</div>';
echo '<br>';

//
//  Limit to specific urls
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:pageview:url') . "<br>";
echo elgg_view('input/text', array('name' => 'url', 'id' => 'pageview-url'));
echo '</div>';


// set up time lower/time upper
// get minimum time (time of oldest logged pageview)
$id = get_metastring_id('au_analytics_page_view');
$result = get_data("SELECT MIN(time_created) as time_created FROM " . elgg_get_config('dbprefix') . "annotations WHERE name_id = " . $id);
$time_lowest = max(array($result[0]->time_created, strtotime('-6 months')));

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:time_lower') . "<br>";
echo elgg_view('input/date', array('name' => 'created_time_lower', 'value' => $time_lowest, 'timestamp' => TRUE, 'style' => 'width: 120px;')) . "<br>";

echo elgg_echo('au_analytics:label:time_upper') . "<br>";
echo elgg_view('input/date', array('name' => 'created_time_upper', 'value' => time(), 'timestamp' => TRUE, 'style' => 'width: 120px;'));
echo '</div>';


//
//  choose type of view
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:pageview:viewtype') . '<br>';
echo elgg_view('input/dropdown', array(
    'name' => 'viewtype',
    'id' => 'pageview-viewtype',
    'options_values' => array(
        'timeline_table' => elgg_echo('au_analytics:option:pageview:timeline_table'),
        'summary_table' => elgg_echo('au_analytics:option:pageview:summary_table')
    )
));
echo '</div>';

//
//  Submit button
echo '<br><br>';
echo '<div class="au_analytics_formelement">';
echo elgg_view('input/submit', array('value' => elgg_echo('search'), 'id' => 'pageview-submit'));
echo '</div>';