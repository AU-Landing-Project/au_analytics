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
  
  if (!isset($PAGEVIEW_SUMMARY[$result->value])) {
    $PAGEVIEW_SUMMARY[$result->value] = 1;
  }
  else {
    $PAGEVIEW_SUMMARY[$result->value]++;
  }
}



function au_analytics_timeline_graph_line($result, $getter, $options) {
  global $TIMELINE_LINE, $TIMELINE_SECTION, $TIMELINE_PREVIOUS;
  static $subtypes;
  //echo "<pre>" . print_r($result,1) . "</pre>";
  if (!is_array($subtypes)) {
    $subtypes = array();
  }

  if (!in_array($result->subtype, array_keys($subtypes))) {
    $entity = get_entity($result->guid);
    $subtypes[$result->subtype] = $entity->getSubtype();
  }

  $group = $options['au_analytics']['group'];
  $subtype = $subtypes[$result->subtype];
  $type = $result->type;
  $x_value = $options['au_analytics']['x_value'];
  
  if (!is_array($TIMELINE_LINE)) {
    $TIMELINE_LINE = array();
  }
  
  if (!is_array($TIMELINE_PREVIOUS)) {
    $TIMELINE_PREVIOUS = array();
  }
  
  if($group){
    $line_name = elgg_echo('au_analytics:entities:total');
  }
  else{
    $line_name = "{$type}:{$subtype}";
  }
  
  $TIMELINE_LINE[$line_name][$x_value]++;
}