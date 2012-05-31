<?php
/*
 * Note that we're using custom page handlers instead of the admin views
 * as there are problems with the friendspicker/userpicker inputs in admin
 * theme.
 */


// plugin init
function au_analytics_init(){
  
  elgg_register_admin_menu_item('administer',	'timeline', 'statistics', 0);
          
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








