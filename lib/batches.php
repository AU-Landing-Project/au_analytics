<?php

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