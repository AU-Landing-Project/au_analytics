<?php

namespace AU\Analytics;

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
	if ($vars['types']) {
		$options_tmp = $options;
		$options_tmp['created_time_lower'] = ($key == 0) ? NULL : $TIMELINE_X[$key - 1];
		$options_tmp['created_time_upper'] = $x;
		$options_tmp['au_analytics']['x_value'] = $x;
		$options_tmp['au_analytics']['previous_x'] = $options_tmp['created_time_lower'];
		$batch = new \ElggBatch('elgg_get_entities', $options_tmp, __NAMESPACE__ . '\\timeline_graph_line', 50);
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
		$batch = new \ElggBatch('elgg_get_annotations', $annotation_options, __NAMESPACE__ . '\\timeline_annotation_graph_line', 50);
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
// set the line up for javascript graph output
// continue on to format for the graph
// format the line into javascript arrays
$jsline = array(
	'titles' => "[",
	'data' => "["
);
//echo "<pre>" . print_r($lines,1) . "</pre>";
// iterate through our lines and insert title into form ['title1','title2','title3']
// and data into [[['l1x', l1y],['l1x', l1y]],[['l2x', l2y],['l2x', l2y]]]
$count = 0;
foreach ($TIMELINE_LINE as $title => $line) {
	if ($count != 0) {
		$jsline['titles'] .= ",";
		$jsline['data'] .= ",";
	}
	$jsline['titles'] .= "'{$title}'";


	$jsline['data'] .= "[";
	$count1 = 0;
	foreach ($line as $x => $y) {
		if ($count1 != 0) {
			$jsline['data'] .= ",";
		}
		$jsline['data'] .= "['" . date('j-M-Y', $x) . "', {$y}]";
		$count1++;
	}
	$jsline['data'] .= "]";

	// up our count, so our commas are in order
	$count++;
}

$jsline['titles'] .= "]";
$jsline['data'] .= "]";



$instructions = elgg_echo('au_analytics:graph:instructions');

echo <<<END
<script>
$(document).ready(function(){
	  var plot1 = $.jqplot('au_analytics_timeline', {$jsline['data']}, {
	    title:'Timeline',
	    seriesDefaults: {
	        show: true,
	        showMarker: false  
	    },
	    axes:{
	      xaxis:{
	        renderer:$.jqplot.DateAxisRenderer,
	          tickOptions:{
	            formatString:'%b&nbsp;%#d&nbsp;\'%y',
              textColor: '#000000'
	          },
	    	label:'Time',
        	labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
        	labelOptions: {
	    		textColor: '#000000'
	        }
	      },
	      yaxis:{
          tickOptions:{
            textColor: '#000000'
          },
	    	label:'Number',
	        labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
	        labelOptions: {
	    		textColor: '#000000'
	        }
	       }
	    },
	    highlighter: {
	      show: false
	    },
	    cursor: {
	      show: true,
	      tooltipLocation: 'ne',
	      zoom: true
	    },
	    markerOptions: {
            show: false
	    },
      legend: {
		      show: true,
		      location: 'e',
		      placement: 'outside',
		      showLabels: true,
		      labels: {$jsline['titles']}
		  }
	  });
	});
</script>


<div id="au_analytics_timeline" style="width:600px; height:400px;"></div>
<br> {$instructions}
END;
