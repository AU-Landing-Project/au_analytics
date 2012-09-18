<?php

$options = array(
    'name' => 'params[pagelog]',
    'value' => $vars['entity']->pagelog,
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no')
    )
);

echo elgg_view('input/dropdown', $options) . ' ' . elgg_echo('au_analytics:log:pages');