<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$i = 0;
foreach ( \edwrodrig\bio\FastaReader::file('file.fasta') as $name => $seq ) {
  echo  $name, "\n";
  echo  $seq, "\n\n";
  $i++;
}

echo "cantidad : $i\n";
