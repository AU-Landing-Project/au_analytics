<?php

$group = elgg_extract('group', $vars, false);
$cumulative = elgg_extract('cumulative', $vars, true);
$interval = (int) elgg_extract('interval', $vars, 7);

// format our options
$options = array();
$options['types'] = $vars['types'];
$options['subtypes'] = $vars['subtypes'];
$options['owner_guids'] = elgg_extract('members', $vars, NULL);
$options['created_time_lower'] = elgg_extract('time_lower', $vars, false);
$options['created_time_upper'] = elgg_extract('time_upper', $vars, false);
$options['au_analytics_display'] = 'data';

$access = elgg_extract('access', $vars, 'any');
if($access != 'any'){
  $access = sanitize_int($access);
  $options['wheres'] = array("e.access_id = {$access}");
}

// get entities by time asc
$options['reverse_order_by'] = TRUE;

// don't need the entity objects, waste of processing power
$options['callback'] = NULL;

// get all results
$options['limit'] = 0;

$line = au_analytics_get_timeline($options, $group, $cumulative, $interval);
  
$message = '';
if(!$line){
  $message = elgg_echo('au_analytics:no_results');
}

// output the table
  
  $data = au_analytics_get_timeline($options, $group, $cumulative, $interval);
  
  //format data into a table
  
  $html .= '<table id="au_analytics_timeline_table" class="tablesorter">';
  $html .= '<thead><tr>';
  $html .= '<th>' . elgg_echo('au_analytics:timestamp') . '</th>';
  $html .= '<th>' . elgg_echo('au_analytics:type:subtype') . '</th>';
  $html .= '<th>' . elgg_echo('au_analytics:result:count') . '</th>';
  $html .= '</tr></thead>';
  $html .= '<tbody>';
  
  foreach($data as $type_subtype => $values){
    foreach($values as $timestamp => $num){
      $html .= '<tr><td>' . date('Y-m-j', $timestamp) . '</td>';
      $html .= '<td>' . $type_subtype . '</td>';
      $html .= '<td>' . $num . '</td></tr>';
    }
  }
  
  $html .= '</tbody></table>';
  
$html .= <<<HTML
    <script>
    \$(document).ready(function() { 
        \$("#au_analytics_timeline_table").tablesorter({widthFixed: true, widgets: ['zebra'], sortList: [[0, 0],[2,1]],}); 
    });
</script>
HTML;

echo $html;