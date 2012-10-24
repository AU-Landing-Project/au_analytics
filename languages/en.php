<?php

$english = array(
    'au_analytics' => "AU Analytics",
    'au_analytics:access:any' => "Any",
    'au_analytics:access:friends' => "Friends",
    'au_analytics:entities:total' => "Results",
    'au_analytics:graph:instructions' => "To zoom in on an area of the graph, click and drag a box around that area.  To reset to the full view, double-click the graph.",
    'au_analytics:label:access' => "Access",
    'au_analytics:label:subtypes' => "Subtypes",
    'au_analytics:label:time_lower' => "Start Time",
    'au_analytics:label:time_upper' => "End Time",
    'au_analytics:label:types' => "Entity Types",
    'au_analytics:result:count' => "Count",
    'au_analytics:option:display:graph' => "Graph",
    'au_analytics:option:display:table' => "Table",
    'au_analytics:no_results' => "No results for the selected options.",
    'au_analytics:timestamp' => "Interval Start",
    'au_analytics:type:subtype' => "Type:Subtype",
    'au_analytics:not_logged_in_user' => "Non-Logged in user",
    'au_analytics:waiting' => 'Loading... (this could take up to 10 min for VERY large sets of results)',
    'au_analytics:timout' => 'Results have not finished being computed after 10 minutes.  Please try to refine your requirements to return a smaller results set.',
    
    // timeline
    'admin:statistics:au_timeline' => 'AU Timeline',
    'au_analytics:form:timeline:notice' => "Please note: this form does not limit your search.  For systems with long timelines and lots of content you may run into memory limits or execution timeouts if there are too many results.  If that happens, try a narrower time frame, larger sample interval, less types/subtypes, etc.",
    'au_analytics:label:cumulative' => "Data Display",
    'au_analytics:label:display' => "Display Type",
    'au_analytics:label:group_container' => "Limit to Groups",
    'au_analytics:label:group_results' => "Line Display",
    'au_analytics:label:interval' => "Sample Interval (days)",
    'au_analytics:label:owner_guid' => "Owners (type name for autocomplete, leave blank for any owner)",
    'au_analytics:option:cumulative:true' => "Show cumulative values",
    'au_analytics:option:cumulative:false' => "Show change per time interval",
    'au_analytics:option:group_results:false' => "Use separate lines for each type/subtype",
    'au_analytics:option:group_results:true' => "Group all results into a single combined line",
    'au_analytics:timeline' => "AU Timeline",
    
    // pageview
    'admin:statistics:au_pageview' => 'AU Page Views',
    'au_analytics:pageview:userpicker' => 'Limit to specific users',
    'au_analytics:pageview:url' => 'Enter a URL to limit to views of that page',
    'au_analytics:label:pageview:viewtype' => 'Select the type of view',
    'au_analytics:option:pageview:timeline_table' => 'Chonological Table',
    'au_analytics:option:pageview:summary_table' => 'Summary Table',
    'au_analytics:pageview:header:url' => 'Page Viewed',
    'au_analytics:pageview:header:timestamp' => 'Timestamp',
    'au_analytics:pageview:header:humandate' => 'Date/Time',
    'au_analytics:pageview:header:user' => 'User',
    'au_analytics:pageview:header:viewcount' => 'View Count'
);
					
add_translation("en",$english);
