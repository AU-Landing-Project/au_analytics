<?php

echo elgg_echo('au_analytics:form:timeline:notice') . "<br><br>";

// get all subtypes in the database, not just the public facing ones
$result = get_data("SELECT subtype FROM " . elgg_get_config('dbprefix') . "entity_subtypes");
$subtypes = array();
foreach($result as $object){
  $subtypes[] = $object->subtype;
}

$types_subtypes = get_registered_entity_types();

$types = array_keys($types_subtypes);
sort($types);

// set up selection for types
// elgg doesn't have multiple select yet
$value = get_input('types', array());
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:types') . "<br>";
echo '<select name="types[]" multiple="multiple" id="timeline-types">';
foreach($types as $type){
  $selected = '';
  if(in_array($type, $value)){
    $selected = ' selected="selected"';
  }
  echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
}
echo '</select>';
echo '</div>';


// setup selection for subtypes
$value = get_input('subtypes', array());

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:subtypes') . "<br>";
echo "<select name=\"subtypes[]\" multiple=\"multiple\" id=\"timeline-subtypes\">";
foreach($subtypes as $subtype){
  $selected = '';
  if(in_array($subtype, $value)){
    $selected = ' selected="selected"';
  }
  echo "<option value=\"{$subtype}\"{$selected}>{$subtype}</option>";
}
echo "</select>";
echo '</div>';


// set up time lower/time upper
// get minimum time (time of oldest entity)
$result = get_data('SELECT MIN(time_created) as time_created FROM ' . elgg_get_config('dbprefix') . 'entities');
$time_lowest = max(array($result[0]->time_created, strtotime('-6 months')));

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:time_lower') . "<br>";
echo elgg_view('input/date', array('name' => 'created_time_lower', 'value' => get_input('created_time_lower', $time_lowest), 'timestamp' => TRUE, 'style' => 'width: 120px;')) . "<br><br>";

echo elgg_echo('au_analytics:label:time_upper') . "<br>";
echo elgg_view('input/date', array('name' => 'created_time_upper', 'value' => get_input('created_time_upper', time()), 'timestamp' => TRUE, 'style' => 'width: 120px;'));
echo '</div>';


// sort by access
$options = array(
    'name' => 'access',
    'value' => get_input('access', 'any'),
    'id' => 'timeline-access',
    'options_values' => array(
        'any' => elgg_echo('au_analytics:access:any'),
        ACCESS_PRIVATE => elgg_echo('PRIVATE'),
        ACCESS_LOGGED_IN => elgg_echo('LOGGED_IN'),
        ACCESS_FRIENDS => elgg_echo('au_analytics:access:friends'),
        ACCESS_PUBLIC => elgg_echo('PUBLIC')
    )
);
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:access') . "<br>";
echo elgg_view('input/dropdown', $options);
echo '</div>';


// set up owner_guids
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:owner_guid') . "<br>";
echo elgg_view('input/userpicker', array('name' => 'owner_guids', 'value' => get_input('members', array())));
echo '</div>';


// line display options
$options = array(
    'name' => 'group',
    'value' => get_input('group', 0),
    'id' => 'timeline-group',
    'options_values' => array(
        TRUE => elgg_echo('au_analytics:option:group_results:true'),
        FALSE => elgg_echo('au_analytics:option:group_results:false')
    )
);

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:group_results') . "<br>";
echo elgg_view('input/dropdown', $options);
echo '</div>';


// data display options
$options = array(
    'name' => 'cumulative',
    'value' => get_input('cumulative', TRUE),
    'id' => 'timeline-cumulative',
    'options_values' => array(
        TRUE => elgg_echo('au_analytics:option:cumulative:true'),
        FALSE => elgg_echo('au_analytics:option:cumulative:false')
    )
);

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:cumulative') . "<br>";
echo elgg_view('input/dropdown', $options);
echo '</div>';


// setup sample interval
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:interval') . "<br>";
echo elgg_view('input/text', array('name' => 'interval', 'value' => get_input('interval', 7), 'style' => 'width:150px', 'id' => 'timeline-interval'));
echo '</div>';


// setup display type
$options = array(
    'name' => 'display',
    'value' => get_input('display', 'graph'),
    'id' => 'timeline-display',
    'options_values' => array(
        'graph' => elgg_echo('au_analytics:option:display:graph'),
        'data' => elgg_echo('au_analytics:option:display:table')
    )
);
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:display') . "<br>";
echo elgg_view('input/dropdown', $options);
echo '</div>';


echo "<br><br>";
echo elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('Submit'), 'id' => 'timeline-submit')) . "<br><br>";