<?php

admin_gatekeeper();

/*
 * Generates timeline graphs based on input parameters
 */


// generate our form
$html = elgg_view_form('au_analytics/timeline', array('action' => current_page_url()));

// get our data formatting options
$group = get_input('group', FALSE);
$cumulative = get_input('cumulative', TRUE);
$submit = get_input('submit', FALSE);
$interval = (int) get_input('interval', 7);
$display = get_input('display');

// format our options
$options = array();
$options['types'] = get_input('types');
$options['subtypes'] = get_input('subtypes');
$options['owner_guids'] = get_input('members', NULL);
$options['created_time_lower'] = get_input('created_time_lower', FALSE);
$options['created_time_upper'] = get_input('created_time_upper', FALSE);
$options['au_analytics_display'] = get_input('display', 'graph');

$access = get_input('access', 'any');
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

if($submit && $display == 'graph'){
  
  // IE versions < 9 need special js
  if(au_analytics_check_ie_pre9()){
    elgg_load_js('au_analytics/jqplot/canvas');
  }

  // get our css/js
  elgg_load_css('au_analytics/jqplot');
  elgg_load_js('au_analytics/jqplot');
  elgg_load_js('au_analytics/jqplot/highlighter');
  elgg_load_js('au_analytics/jqplot/cursor');
  elgg_load_js('au_analytics/jqplot/dateaxis');
  elgg_load_js('au_analytics/jqplot/barRender');
  elgg_load_js('au_analytics/jqplot/categoryAxis');
  elgg_load_js('au_analytics/jqplot/pointLabels');
  elgg_load_js('au_analytics/jqplot/canvasAxisLabel');
  elgg_load_js('au_analytics/jqplot/canvasText');

  $line = au_analytics_get_timeline($options, $group, $cumulative, $interval);
  
  $message = '';
  if(!$line){
    $message = elgg_echo('au_analytics:no_results');
  }
  
$graph = <<<END
<script>
$(document).ready(function(){
	  //var line1=[['1-Jan-2012', 54], ['2-Jan-2012', 37], ['3-Jan-2012', 55], ['5-Jan-2012', 44]];
    //var line2=[['1-Jan-2012', 34], ['2-Jan-2012', 47], ['3-Jan-2012', 52], ['5-Jan-2012', 49]];
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
END;

$graph .= "<br>" . elgg_echo('au_analytics:graph:instructions');
          

  $html .= $graph;
}
elseif($submit){
  elgg_load_css('au_analytics/tablesorter');
  elgg_load_js('au_analytics/tablesorter');
  elgg_load_js('au_analytics/tablesorter/pager');
  // output the table
  
  $data = au_analytics_get_timeline($options, $group, $cumulative, $interval);
  
  //format data into a table
  
  $html .= '<table id="au_analytics_timeline_table" class="tablesorter">';
  $html .= '<thead><tr>';
  $html .= '<th>' . elgg_echo('au_analytics:type:subtype') . '</th>';
  $html .= '<th>' . elgg_echo('au_analytics:timestamp') . '</th>';
  $html .= '<th>' . elgg_echo('au_analytics:result:count') . '</th>';
  $html .= '</tr></thead>';
  $html .= '<tbody>';
  
  foreach($data as $type_subtype => $values){
    foreach($values as $timestamp => $num){
      $html .= '<tr><td>' . $type_subtype . '</td>';
      $html .= '<td>' . $timestamp . '</td>';
      $html .= '<td>' . $num . '</td>';
    }
  }
  
  $html .= '</tbody></table>';
  
$html .= <<<HTML
    <script>
    \$(document).ready(function() { 
        \$("#au_analytics_timeline_table").tablesorter({widthFixed: true, widgets: ['zebra']}); 
    });
</script>
HTML;

}

$body = elgg_view_layout('one_sidebar', array('content' => $html));

echo elgg_view_page('', $body);