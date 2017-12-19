<?php

namespace edwrodrig\bio;

class FastaReader {

public $to_lower = false;
public $ignore_gaps = false;

function stream($stream) {
  $name = null;
  $data = '';
  
  $yield = function() use (&$name, &$data) {
    if ( is_null($name) ) return;
    else yield $name => $data;
    $data = '';
    $name = null;
  };

  while ( $line = fgets($stream) ) {
    if ( strlen($line) <= 0 ) continue;
    $line = substr($line, 0, -1);
    if ( $line[0] == '>' ) {
      yield from $yield();
      $name = substr($line, 1);
    }
    else $data .= $line; 
  }

  yield from $yield();
}

static function str($str) {
   $r = new FastaReader();
   $stream = fopen("php://temp", 'w+');
   fwrite($stream, $str);
   rewind($stream);
   yield from $r->stream($stream);
}

static function file($f) {
  $r = new FastaReader();
  $stream = fopen($f, 'r+');
  yield from $r->stream($stream);
}

}
