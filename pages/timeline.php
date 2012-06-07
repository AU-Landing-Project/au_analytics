<?php

/*
 * Generates timeline graphs based on input parameters
 */


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

// generate our form
$html = elgg_view_form('au_analytics/timeline', array('action' => current_page_url()));

// get our data formatting options
$group = get_input('group', FALSE);
$cumulative = get_input('cumulative', TRUE);

// format our options
$options = array();
$options['types'] = get_input('types');
$options['subtypes'] = get_input('subtypes');
$options['time_lower'] = get_input('time_lower');
$options['time_upper'] = get_input('time_upper');

// get entities by time asc
$options['reverse_order_by'] = TRUE;

// don't need the entity objects, waste of processing power
$options['callback'] = NULL;

// get all results
$options['limit'] = 0;

//$html .= "<pre>" . print_r($options,1) . "</pre>";

$entities = elgg_get_entities($options);

$line = au_analytics_get_line($entities, $group, $cumulative);

//$html .= "<pre>" . print_r($entities,1) . "</pre>";

$html .= <<<END
<script>
$(document).ready(function(){
	  var line1=[['1-Jan-2012', 54], ['2-Jan-2012', 37], ['3-Jan-2012', 55], ['5-Jan-2012', 44]];
    var line2=[['1-Jan-2012', 34], ['2-Jan-2012', 47], ['3-Jan-2012', 52], ['5-Jan-2012', 49]];
	  var plot1 = $.jqplot('au_analytics_timeline', [line1, line2], {
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
		      labels: ['Mild','Moderate','Extreme']
		  }
	  });
	});
</script>


<div id="au_analytics_timeline" style="width:600px; height:400px;"></div>
END;
// set up selection for subtypes

$body = elgg_view_layout('one_sidebar', array('content' => $html));

echo elgg_view_page('', $body);