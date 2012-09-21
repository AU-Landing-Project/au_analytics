<?php

// returns bool based on whether the current browser is IE < 9
function au_analytics_check_ie_pre9(){
  $match=preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$reg);
  if($match==0){
    return FALSE;
  }

  $version = floatval($reg[1]);
  
  if($reg[1] < 9){
    return TRUE;
  }
  
  return FALSE;
}


// formats our line data from the results of a query
// $entities is an array of e* results
// $group is bool flag - combine all types/subtypes into one line?
// $cumulative is bool flag - does next datapoint include value of previous ones?
function au_analytics_get_timeline($options = array(), $group = FALSE, $cumulative = TRUE, $interval = 7){
  // run our query first
  $entities = elgg_get_entities($options);
  
  if(!$entities || !is_numeric($interval) || $interval <= 0){
    // no results
    return FALSE;
  }
  
  $time_section = 60*60*24*$interval;
  
  // iterate through entities to build the line(s)
  // $line is multidimensional array, keys = type:subtype - line name
  // values array(x => y)
  $lines = array();
  
  // iterate through the intervals
  $previous_time = NULL;
  $start = $options['created_time_lower'];
  $stop = $options['created_time_upper'] + $time_section;
  $x_values = array();
  
  for($i = $start; $i < $stop; $i += $time_section){
    $x_values[] = $time_lower = $i;
    $time_upper = $i + $time_section;
    // iterate through our entities and build our array
    foreach($entities as $key => $entity){
      $type = $entity->type;
      $subtype = get_subtype_from_id($entity->subtype);
      
      // initialize line point with value of 0 or cumulative value, then we'll add to it
      if($group){
        $line_name = elgg_echo('au_analytics:entities:total');
      }
      else{
        $line_name = "{$type}:{$subtype}";
      }
      
      // first point for this line at this time
      // initialize the point with either 0, or cumulative value
      if(!isset($lines[$line_name][$time_lower])){
        if($cumulative){
          if($previous_time === NULL){
            // this is the first point for the line total, need to query db to get previous sum
            if($group){
              // we're grouping everything together, so we can sum the initial query with some modifications
              $value = elgg_get_entities(array_merge($options, array('count' => TRUE, 'created_time_lower' => NULL, 'created_time_upper' => $options['created_time_lower'])));
            }
            else{
              $options_mod = array(
                  'count' => TRUE,
                  'created_time_lower' => NULL,
                  'created_time_upper' => $options['created_time_lower'],
              );
              
              if($type){
                $options_mod['types'] = array($type);
              }
              
              if($subtype){
                $options_mod['subtypes'] = array($subtype);
              }
              
              $value = elgg_get_entities(array_merge($options, $options_mod));
            }
          }
          else{
            $value = $lines[$line_name][$previous_time];
          }
        }
        else{
          $value = 0;
        }
        $lines[$line_name][$time_lower] = $value;
      }
      
      if($entity->time_created >= $time_lower && $entity->time_created < $time_upper){
        // this entity falls in this time interval, lets count it
        $lines[$line_name][$time_lower]++;
        // counted, so we don't need it anymore, save some cycles
        unset($entities[$key]);
      }
    }
    
    $previous_time = $i;
  }
  
  // now we have all of our lines, though not all of them will extend to the end of the graph
  // if an entity was counted and removed from the array, and there were no more to the end of the iterations
  // so now we'll normalize them with our x values
  foreach($lines as $name => $values){
    $linex = array_keys($values);
    
    foreach($x_values as $x){
      if(!in_array($x, $linex)){
        // this line is missing this point
        // so add it in either as 0 or as previous value
        if($cumulative){
          $lines[$name][$x] = end($lines[$name]);
        }
        else{
          $lines[$name][$x] = 0;
        }
      }
    }
  }
  
  //if we're outputting a table, we have all the info
  if($options['au_analytics_display'] == 'data'){
    return $lines;
  }
  
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
  foreach($lines as $title => $line){
    if($count != 0){
      $jsline['titles'] .= ",";
      $jsline['data'] .= ",";
    }
    $jsline['titles'] .= "'{$title}'";
    
    
    $jsline['data'] .= "[";
    $count1 = 0;
    foreach($line as $x => $y){
      if($count1 != 0){
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
  
  return $jsline;
}


function au_analytics_load_graph_js() {
  // IE versions < 9 need special js
  if(au_analytics_check_ie_pre9()){
    elgg_load_js('au_analytics/jqplot/canvas');
  }

  /*
   *  get our css/js
   */
  
  // jqplot
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
  
  // tablesorter
  elgg_load_css('au_analytics/tablesorter');
  elgg_load_js('au_analytics/tablesorter');
  elgg_load_js('au_analytics/tablesorter/pager');
}