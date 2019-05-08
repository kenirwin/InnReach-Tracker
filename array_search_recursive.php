<?php
function array_search_recursive($needle, $haystack, $a=0, $nodes_temp=array()){
  /* copied from php.net: 
     http://us2.php.net/manual/en/function.array-search.php#79640
     this script improves upon the array_search() function
     by returning an array of results, rather than just one result
  */
  global $nodes_found;
  $a++;
  foreach ($haystack as $key1=>$value1) {
    $nodes_temp[$a] = $key1;
    if (is_array($value1)){   
      array_search_recursive($needle, $value1, $a, $nodes_temp);
    }
    else if ($value1 === $needle){
      $nodes_found[] = $nodes_temp[$a];
    }
  }
  return $nodes_found;
}
?>

