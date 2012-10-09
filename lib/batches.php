<?php

/**
 *  Generates the internal markup of the pageview timeline table
 * @param type $result
 * @param type $getter
 * @param type $options
 */
function au_analytics_output_pageview_timeline_table($result, $getter, $options) {
  $owner = $result->getOwnerEntity();
  
  if (elgg_instanceof($owner, 'user')) {
    $name = $owner->name;
    $owner_url = $owner->getURL();
  }
  else {
    $name = elgg_echo('au_analytics:not_logged_in_user');
    $owner_url = elgg_get_site_url();
  }
  
  echo '<tr><td><a href="' . $owner_url . '">' . $name . '</a></td>';
  echo '<td><a href="' . $result->value . '">' . $result->value . '</a></td>';
  echo '<td>' . $result->time_created . '</td>';
  echo '<td>' . date('m/d/Y, g:ia', $result->time_created) . '</td>';
}


/**
 *  Counts pageviews per person and outputs it to a multidimensional array
 * for use in generating the summary table
 * @global type $PAGEVIEW_SUMMARY
 * @param type $result
 * @param type $getter
 * @param type $options
 */
function au_analytics_output_pageview_summary_table($result, $getter, $options) {
  global $PAGEVIEW_SUMMARY;
  
  if (!is_array($PAGEVIEW_SUMMARY)) {
    $PAGEVIEW_SUMMARY = array();
  }
  
  if (!isset($PAGEVIEW_SUMMARY[$result->owner_guid][$result->value])) {
    $PAGEVIEW_SUMMARY[$result->owner_guid][$result->value] = 1;
  }
  else {
    $PAGEVIEW_SUMMARY[$result->owner_guid][$result->value]++;
  }
}



function au_analytics_output_timeline_graph($result, $getter, $options) {
  global $TIMELINE_LINE, $TIMELINE_SECTION, $TIMELINE_PREVIOUS;
  static $subtypes;
  
  if (!is_array($subtypes)) {
    $subtypes = array();
  }

  if (!in_array($result->subtype, array_keys($subtypes))) {
    $subtypes[$result->subtype] = $result->getSubtype();
  }

  $group = $options['au_analytics']['group'];
  $interval = $options['au_analytics']['interval'];
  $cumulative = $options['au_analytics']['cumulative'];
  
  if (!is_array($TIMELINE_LINE)) {
    $TIMELINE_LINE = array();
  }
  
  if (!$TIMELINE_SECTION) {
    $TIMELINE_SECTION = 60*60*24*$interval;
  }
  
  if (!is_array($TIMELINE_PREVIOUS)) {
    $TIMELINE_PREVIOUS = array();
  }
  
  // iterate through the intervals
  $previous_time = NULL;
  $start = $options['created_time_lower'];
  $stop = $options['created_time_upper'] + $TIMELINE_SECTION;
  $x_values = array();
  
  if($group){
    $line_name = elgg_echo('au_analytics:entities:total');
  }
  else{
    $line_name = "{$type}:{$subtypes[$result->subtype]}";
  }
  
  
  // get first point if line not initiated yet
  // first point for this line at this time
  // initialize the point with either 0, or cumulative value
  if(!isset($TIMELINE_LINE[$line_name][$time_lower])){
    if($cumulative){
      if($previous_time[$line_name] === NULL){
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
  
}