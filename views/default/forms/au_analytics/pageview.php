<?php

echo elgg_echo('au_analytics:pageview:userpicker') . "<br>";
echo elgg_view('input/userpicker');

echo "<br><br>";

echo elgg_echo('au_analytics:pageview:url') . "<br>";
echo elgg_view('input/text', array('name' => 'url', 'value' => get_input('url')));

echo "<br><br>";

echo elgg_view('input/submit', array('value' => elgg_echo('search')));