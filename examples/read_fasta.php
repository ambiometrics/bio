<?php

require_once(__DIR__ . '/../include.php');

$i = 0;
foreach ( \bio\FastaReader::file('file.fasta') as $name => $seq ) {
  echo  $name, "\n";
  echo  $seq, "\n\n";
  $i++;
}

echo "cantidad : $i\n";
