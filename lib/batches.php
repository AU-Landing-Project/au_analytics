<?php

namespace AU\Analytics;

//@todo - many of these would be more efficient as direct time based queries

function timeline_graph_line($result, $getter, $options) {
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

	if ($group) {
		$line_name = elgg_echo('au_analytics:entities:total');
	} else {
		$line_name = $subtype ? "{$type}:{$subtype}" : "{$type}";
	}

	$TIMELINE_LINE[$line_name][$x_value] ++;
}

function timeline_annotation_graph_line($result, $getter, $options) {
	global $TIMELINE_LINE;

	static $annotation_names;

	if (!is_array($annotation_names)) {
		$annotation_names = array();
	}

	if (!$annotation_names[$result->name_id]) {
		$a = elgg_get_annotation_from_id($result->id);
		$annotation_names[$result->name_id] = $a->name;
	}

	$group = $options['au_analytics']['group'];
	$x_value = $options['au_analytics']['x_value'];

	if (!is_array($TIMELINE_LINE)) {
		$TIMELINE_LINE = array();
	}

	if ($group) {
		$line_name = elgg_echo('au_analytics:entities:total');
	} else {
		$line_name = "annotation:{$annotation_names[$result->name_id]}";
	}

	$TIMELINE_LINE[$line_name][$x_value] ++;
}
