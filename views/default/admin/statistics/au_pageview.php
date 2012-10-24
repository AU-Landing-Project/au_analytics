<?php
elgg_load_js('au_analytics/pageview');
elgg_load_js('au_analytics/tablesorter');
elgg_load_css('au_analytics/tablesorter');

echo elgg_view('forms/au_analytics/pageview');

echo '<br><br>';
echo '<div id="pageview-throbber" class="hidden">';
echo elgg_view('graphics/ajax_loader', array('hidden' => false));
echo elgg_echo('au_analytics:waiting');
echo '</div>';
echo '<div id="pageview-results"></div>';