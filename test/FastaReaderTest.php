<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__ . '/../include.php');

use \bio\FastaReader;

class FastaReaderTest extends TestCase {

function testReadStr() {
  $data = <<<EOF
>seq1
ACTGACTG
>seq2
AAACCCTTTGGG
EOF;
  foreach ( FastaReader::str($data) as $k => $v ) {
    echo  "$k = $v\n";
  }

}

}
