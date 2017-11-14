<?php

require_once(__DIR__ . '/../include.php');

$filename = $argv[1];

$basename = explode('.',basename($filename))[0];

foreach ( \bio\FastaReader::file($filename) as $name => $seq ) {
  echo  '>', $basename, "\n";
  echo  $seq, "\n\n";
}

