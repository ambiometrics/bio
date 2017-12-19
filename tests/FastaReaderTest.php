<?php
use PHPUnit\Framework\TestCase;

use \edwrodrig\bio\FastaReader;

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
