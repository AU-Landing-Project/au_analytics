<?php
/*
 * Note that we're using custom page handlers instead of the admin views
 * as there are problems with the friendspicker/userpicker inputs in admin
 * theme.
 */

// include our procedural functions
require_once 'lib/functions.php';

// plugin init
function au_analytics_init(){
  
  // set up css
	elgg_extend_view('css/elgg', 'au_analytics/css');
	elgg_register_css('au_analytics/jqplot', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/jquery.jqplot.min.css');
	
	
	// Register our javascript
	elgg_register_js('au_analytics/jqplot/canvas', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/excanvas.min.js', 'head');
	elgg_register_js('au_analytics/jqplot', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/jquery.jqplot.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/highlighter', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.highlighter.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/cursor', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.cursor.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/dateaxis', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.dateAxisRenderer.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/barRender', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.barRenderer.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/categoryAxis', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/pointLabels', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.pointLabels.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/canvasAxisLabel', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js', 'head');
	elgg_register_js('au_analytics/jqplot/canvasText', elgg_get_site_url() . 'mod/au_analytics/js/jqplot/plugins/jqplot.canvasTextRenderer.min.js', 'head');

  
  if(elgg_is_admin_logged_in() && (elgg_get_context() == 'admin' || elgg_get_context() == 'au_analytics')){
    $section = '';
    $parent = '';
    if(elgg_get_context() == 'admin'){
      $section = 'administer';
      $parent = 'statistics';
    }
    
    $url = elgg_get_site_url() . "au_analytics/timeline";
      
    
     elgg_register_menu_item('page', array(
         'name' => 'au_analytics_timeline',
         'href' => $url,
         'text' => elgg_echo('au_analytics:timeline'),
         'parent_name' => $parent,
         'section' => $section,
         'priority' => 1000
     ));
  }
          
  // register page handler
  elgg_register_page_handler('au_analytics', 'au_analytics_page_handler');
}


// route our pages
function au_analytics_page_handler($page){
  switch ($page[0]){
    case "timeline":
    default:
      if(!include(elgg_get_plugins_path() . 'au_analytics/pages/timeline.php')){
        return FALSE;
      }
      break;
  }
  
  return TRUE;
}

elgg_register_event_handler('init', 'system', 'au_analytics_init');
