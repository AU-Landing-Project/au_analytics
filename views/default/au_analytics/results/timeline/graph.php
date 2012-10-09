<?php
/*
// format our options
$options = array(
    'types' => $vars['types'],
    'subtypes' => $vars['subtypes'],
    'owner_guids' => $vars['members'],
    'created_time_lower' => $vars['time_lower'],
    'created_time_upper' => $vars['time_upper'],
    'reverse_order_by' => TRUE,
    'callback' => NULL,
    'limit'  => 0,
    'au_analytics' => array(
      'group' => $vars['group'],
      'cumulative' => $vars['cumulative'],
      'interval' => $vars['interval']
    )
);


if($vars['access'] != 'any'){
  $access = sanitize_int($vars['access']);
  $options['wheres'] = array("e.access_id = {$access}");
}

$instructions = elgg_echo('au_analytics:graph:instructions');

//
//
// generate our line
global $TIMELINE_LINE, $TIMELINE_SECTION, $TIMELINE_PREVIOUS, $TIMELINE_X;

if (!$TIMELINE_SECTION) {
    $TIMELINE_SECTION = 60*60*24*$interval;
  }

// determine x values as line keys
$TIMELINE_X = array();
$i = $options['created_time_lower'];
while ($i < ($options['created_time_upper'] + $TIMELINE_SECTION)) {
  $TIMELINE_X[] = $i;
  $i += $TIMELINE_SECTION;
}


// loop over each x value and query for the y coordinate of each line type
foreach ($TIMELINE_X as $key => $x) {
  $options['created_time_lower'] = ($key == 0) ? NULL : $TIMELINE_X[$key - 1];
  $options['created_time_upper'] = $x;
  $options['au_analytics']['x_value'] = $x;
  $batch = new ElggBatch('elgg_get_entities', $options, 'au_analytics_timeline_graph_line', 50);
}
*/

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
$options['au_analytics_display'] = 'graph';

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
  
$instructions = elgg_echo('au_analytics:graph:instructions');

echo <<<END
<script>
$(document).ready(function(){
	  var plot1 = $.jqplot('au_analytics_timeline', {$line['data']}, {
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
		      labels: {$line['titles']}
		  }
	  });
	});
</script>


<div id="au_analytics_timeline" style="width:600px; height:400px;">{$message}</div>
<br> {$instructions}
END;
