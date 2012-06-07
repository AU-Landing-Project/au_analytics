<?php

// returns bool based on whether the current browser is IE < 9
function au_analytics_check_ie_pre9(){
  $match=preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$reg);
  if($match==0){
    return FALSE;
  }

  $version = floatval($reg[1]);
  
  if($reg[1] < 9){
    return TRUE;
  }
  
  return FALSE;
}


// formats our line data from the results of a query
// $entities is an array of e* results
// $group is bool flag - combine all types/subtypes into one line?
// $cumulative is bool flag - does next datapoint include value of previous ones?
function au_analytics_get_line($entities, $group, $cumulative){
  
}