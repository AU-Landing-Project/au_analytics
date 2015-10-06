<?php

namespace AU\Analytics;

// make sure we have our js/css
load_graph_js();
elgg_require_js('au_analytics/timeline');

// generate our form
echo elgg_view_form('au_analytics/timeline', array('action' => current_page_url()));

echo '<br><br>';

echo '<div id="timeline-throbber" class="hidden">';
echo elgg_view('graphics/ajax_loader', array('hidden' => false));
echo elgg_echo('au_analytics:waiting');
echo '</div>';
echo '<div id="timeline-results"></div>';
