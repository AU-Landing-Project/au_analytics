<?php

namespace AU\Analytics;

const PLUGIN_ID = 'au_analytics';
const PLUGIN_VERSION = 20151005;

// include our procedural functions
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/events.php';
require_once __DIR__ . '/lib/batches.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

// plugin init
function init() {

	// extend our views
	elgg_extend_view('css/admin', 'css/au_analytics');
	elgg_register_ajax_view('au_analytics/results/pageview');
	elgg_register_ajax_view('au_analytics/results/timeline');

	// register page-specific css
	elgg_register_css('au_analytics/jqplot', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/jquery.jqplot.min.css');
	elgg_register_css('au_analytics/tablesorter', elgg_get_site_url() . 'mod/au_analytics/js/tablesorter/style.css');

	$cache = elgg_get_config('lastcache');
	if (!elgg_get_config('simplecache_enabled')) {
		$cache = time();
	}

	// Register our javascript
	elgg_define_js('au_analytics/jqplot', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/jquery.jqplot.min.js?c='.$cache,
		'deps' => array(
			'jquery'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/canvas', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/excanvas.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/highlighter', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.highlighter.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/cursor', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.cursor.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/dateaxis', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.dateAxisRenderer.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/barRender', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.barRenderer.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/categoryAxis', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/pointLabels', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.pointLabels.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/canvasAxisLabel', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/jqplot/canvasText', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.canvasTextRenderer.min.js?c='.$cache,
		'deps' => array(
			'au_analytics/jqplot'
		)
	));
	
	elgg_define_js('au_analytics/tablesorter', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/tablesorter/jquery.tablesorter.min.js?c='.$cache
	));
	
	elgg_define_js('au_analytics/tablesorter/pager', array(
		'src' => elgg_get_site_url() . 'mod/au_analytics/js/tablesorter/jquery.tablesorter.pager.js?c='.$cache,
		'deps' => array(
			'au_analytics/tablesorter'
		)
	));
	
	
	// navigation
	elgg_register_admin_menu_item('administer', 'au_pageview', 'statistics', 0);
	elgg_register_admin_menu_item('administer', 'au_timeline', 'statistics', 0);


	/*
	 *  plugin hooks
	 */
	// log page views
	elgg_register_plugin_hook_handler('output:before', 'page', __NAMESPACE__ . '\\record_pageview');
	
	elgg_register_event_handler('upgrade', 'system', __NAMESPACE__ . '\\upgrades');
}
