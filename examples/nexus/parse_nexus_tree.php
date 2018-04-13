<?php


function get_labels() {

  $labels = [];
  while( $f = fgets(STDIN) ) {
    if ( preg_match("/translate/", $f) )
      break;
  }

  while ( $f = fgets(STDIN) )  {
    $f = trim($f);
    $matches;
    if ( preg_match("/^(\d+) (.*)/", $f, $matches) ) {
      $labels[$matches[1]] = rtrim($matches[2], ",;");
    } else {
     break;
    }

  }

  return $labels;
}


function get_tree() {
  $last;
  while ( $f = fgets(STDIN) ) {
    if ( preg_match("/[&U]/", $f) )
     $last =  $f;
  }
  return $last;
}

$labels = get_labels();


foreach ( $labels  as $index  => $name ) {
  

}

$last = get_tree();

foreach ( $labels  as $index  => $name ) {

  $last = str_replace(",$index:", ",$name:", $last);
  $last = str_replace("($index:", "($name:", $last);

}


echo  $last;

?>

