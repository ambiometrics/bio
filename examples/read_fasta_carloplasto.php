<?php

require_once(__DIR__ . '/../include.php');

$dir = $argv[1];

foreach ( glob($dir . '/*.fasta') as $filename ) {
$basename = explode('.',basename($filename))[0];

$i = 0;
foreach ( \bio\FastaReader::file($filename) as $name => $seq ) {
  echo  '>', $basename, "_", ++$i, "\n";
  echo  $seq, "\n\n";
}

}

