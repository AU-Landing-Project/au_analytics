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
  
  if(!$entities || !is_numeric($interval)){
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
  $stop = $options['created_time_upper'];
  
  for($i = $start; $i < $stop; $i += $time_section){

    $time_lower = $i;
    $time_upper = $i + $time_section;
    // iterate through our entities and build our array
    foreach($entities as $key => $entity){
      $type = $entity->type;
      $subtype = get_subtype_from_id($entity->subtype);
      
      // initialize line point with value of 0 or cumulative value, then we'll add to it
      if($group){
        $line_name = 'au_analytics:entities:total';
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
  
  // format the line into javascript arrays
  $jsline = array(
      'titles' => "[",
      'data' => "["
  );
  
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