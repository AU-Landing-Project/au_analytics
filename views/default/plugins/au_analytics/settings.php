<?php

$options = array(
    'name' => 'params[pagelog]',
    'value' => $vars['entity']->pagelog ? $vars['entity']->pagelog : 'no',
    'options_values' => array(
        'yes' => elgg_echo('option:yes'),
        'no' => elgg_echo('option:no')
    )
);

echo elgg_view('input/dropdown', $options) . ' ' . elgg_echo('au_analytics:log:pages');