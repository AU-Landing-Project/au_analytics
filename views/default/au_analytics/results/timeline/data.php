<?php

// format our options
$options = array(
	'types' => $vars['types'],
	'subtypes' => $vars['subtypes'],
	'owner_guids' => $vars['members'],
	'created_time_lower' => $vars['time_lower'],
	'created_time_upper' => $vars['time_upper'],
	'reverse_order_by' => TRUE,
	'callback' => NULL,
	'limit' => 0,
	'au_analytics' => array(
		'group' => $vars['group'],
		'cumulative' => $vars['cumulative'],
		'interval' => $vars['interval']
	)
);

//echo "<pre>" . print_r($options, 1) . "</pre>"; return;
if ($vars['access'] != 'any') {
	$access = sanitize_int($vars['access']);
	$options['wheres'] = array("e.access_id = {$access}");
}

//
//
// generate our line
global $TIMELINE_LINE, $TIMELINE_SECTION, $TIMELINE_X;

if (!$TIMELINE_SECTION) {
	$TIMELINE_SECTION = 60 * 60 * 24 * $options['au_analytics']['interval'];
}

// determine x values as line keys
$TIMELINE_X = array();
$i = $options['created_time_lower'];
while ($i < ($options['created_time_upper'] + $TIMELINE_SECTION)) {
	$TIMELINE_X[] = $i;
	$i += $TIMELINE_SECTION;
}
//echo "<pre>" . print_r($TIMELINE_X,1) . "</pre>";
// loop over each x value and query for the y coordinate of each line type
foreach ($TIMELINE_X as $key => $x) {
	if ($vars['type']) {
		$options_tmp = $options;
		$options_tmp['created_time_lower'] = ($key == 0) ? NULL : $TIMELINE_X[$key - 1];
		$options_tmp['created_time_upper'] = $x;
		$options_tmp['au_analytics']['x_value'] = $x;
		$options_tmp['au_analytics']['previous_x'] = $options_tmp['created_time_lower'];
		$batch = new ElggBatch('elgg_get_entities', $options_tmp, 'au_analytics_timeline_graph_line', 50);
	}
	
	if ($vars['annotations']) {
		$annotation_options = array(
			'annotation_names' => $vars['annotations'],
			'annotation_owner_guids' => $vars['members'],
			'annotation_created_time_upper' => $x,
			'annotation_created_time_lower' => ($key == 0) ? NULL : $TIMELINE_X[$key - 1],
			'reverse_order_by' => TRUE,
			'callback' => NULL,
			'limit' => 0,
			'au_analytics' => array(
				'x_value' => $x,
				'previous_x' => ($key == 0) ? NULL : $TIMELINE_X[$key - 1]
			)
		);
		
		if ($vars['access'] != 'any') {
			$access = sanitize_int($vars['access']);
			$annotation_options['wheres'] = array(
				"n_table.access_id = {$access}"
			);
		}
		$batch = new ElggBatch('elgg_get_annotations', $annotation_options, 'au_analytics_timeline_annotation_graph_line', 50);
	}
}


// now we have all of our lines, though not all of them will extend to the end of the graph
// make see if we have any lines to display, if not, short circuit
if (!$TIMELINE_LINE) {
	echo elgg_echo('au_analytics:no_results');
	return;
}

// if an entity was counted and removed from the array, and there were no more to the end of the iterations
// so now we'll normalize them with our x values
foreach ($TIMELINE_LINE as $name => $values) {
	foreach ($TIMELINE_X as $key => $x) {
		if (!isset($TIMELINE_LINE[$name][$x])) {
			$TIMELINE_LINE[$name][$x] = 0;
		}

		if ($vars['cumulative']) {
			if ($key != 0) {
				// set it to the value of the previous x
				$TIMELINE_LINE[$name][$x] += $TIMELINE_LINE[$name][$TIMELINE_X[$key - 1]];
			}
		}
	}
	ksort($TIMELINE_LINE[$name]);
}
//echo "<pre>" . print_r($TIMELINE_LINE,1) . "</pre>";
//format data into a table

$html .= '<table id="au_analytics_timeline_table" class="tablesorter">';
$html .= '<thead><tr>';
$html .= '<th>' . elgg_echo('au_analytics:timestamp') . '</th>';
$html .= '<th>' . elgg_echo('au_analytics:type:subtype') . '</th>';
$html .= '<th>' . elgg_echo('au_analytics:result:count') . '</th>';
$html .= '</tr></thead>';
$html .= '<tbody>';

foreach ($TIMELINE_LINE as $type_subtype => $values) {
	foreach ($values as $timestamp => $num) {
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

