<?php

$types_subtypes = get_registered_entity_types();

$types = array_keys($types_subtypes);
sort($types);

// set up selection for types
// elgg doesn't have multiple select yet
$value = get_input('types', array());
echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:types') . "<br>";
echo '<select name="types[]" multiple="multiple">';
foreach($types as $type){
  $selected = '';
  if(in_array($type, $value)){
    $selected = ' selected="selected"';
  }
  echo "<option value=\"{$type}\"{$selected}>{$type}</option>";
}
echo '</select>';
echo '</div>';


// set up selection for subtypes
$subtypes = array();
foreach($types_subtypes as $type => $subtypes_array){
  if(is_array($subtypes_array)){
    foreach($subtypes_array as $subtype){
      $subtypes[] = $subtype;
    }
  }
}
$subtypes = array_unique($subtypes);
sort($subtypes);
$value = get_input('subtypes', array());

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:subtypes') . "<br>";
echo "<select name=\"subtypes[]\" multiple=\"multiple\">";
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
$time_lowest = $result[0]->time_created;

echo '<div class="au_analytics_formelement">';
echo elgg_echo('au_analytics:label:time_lower') . "<br>";
echo elgg_view('input/date', array('name' => 'created_time_lower', 'value' => get_input('created_time_lower', $time_lowest), 'timestamp' => TRUE, 'style' => 'width: 120px;')) . "<br><br>";

echo elgg_echo('au_analytics:label:time_upper') . "<br>";
echo elgg_view('input/date', array('name' => 'created_time_upper', 'value' => get_input('created_time_upper', time()), 'timestamp' => TRUE, 'style' => 'width: 120px;'));
echo '</div>';

echo "<br><br>";
echo elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo('Submit')));