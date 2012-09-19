<?php

echo elgg_view_form('au_analytics/pageview', array('action' => current_page_url()));

$guids = get_input('members', array());
$url = get_input('url', false);

$options = array(
    'annotation_names' => array('au_analytics_page_view'),
    'limit' => 0,
);

if (count($guids)) {
  $options['annotation_owner_guids'] = $guids;
}

if ($url) {
  $options['annotation_values'] = array($url);
}

$annotations = elgg_get_annotations($options);

if (!$annotations) {
  $annotations = array();
}

foreach ($annotations as $annotation) {
  $owner = $annotation->getOwnerEntity();
  if (elgg_instanceof($owner, 'user')) {
    $name = $owner->name;
    $href = $owner->getURL();
  }
  else {
    $name = 'Not logged in user';
    $href = elgg_get_site_url();
  }
  
  echo '<div style="margin: 10px; border: 1px solid black; padding: 10px;">';
  echo elgg_view('output/url', array('text' => $name, 'href' => $href));
  echo '<br>';
  echo 'Page View: ' . $annotation->value . '<br>';
  echo 'Time: ' . date('m/d/Y g:ia', $annotation->time_created);
  echo '</div>';
}