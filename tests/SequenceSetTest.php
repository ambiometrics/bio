<?php
use PHPUnit\Framework\TestCase;

use \edwrodrig\bio\SequenceSet;
use \edwrodrig\bio\Sequence;

class SequenceSetTest extends TestCase {

function normalizeLengthProvider() {
  return  [
[ [
    'AAA----CCCC',
    'AAACCCC'
  ],<<<EOF
AAA----CCCC
AAACCCC----

EOF
]
];
}

/**
 * @dataProvider normalizeLengthProvider
 */
function testNormalizeLength($sequences, $expected) {
  $s = new SequenceSet();
  $s->add(...$sequences);
  $this->assertEquals($expected, strval($s->normalize_length()));
}

function testType() {
  $s = new SequenceSet();
  $s->add('ACTG');
  $this->assertEquals(Sequence::TYPE_NUCLEOTIDE, $s->type());

  $s->add('ACTG');
  $this->assertEquals(Sequence::TYPE_NUCLEOTIDE, $s->type());

  $s->add('TTTT');
  $this->assertEquals(Sequence::TYPE_NUCLEOTIDE, $s->type());

  $s->add('EFIL');
  $this->assertEquals(Sequence::TYPE_AMINOACID, $s->type());
}

function testRemoveCol() {
  $s = new SequenceSet();
  $s->add(
    'AACTG',
    'TTGCA',
    'AAFGR');
  $s->remove_col(1);

  $this->assertEquals("ACTG\nTGCA\nAFGR\n", strval($s));

  $s->remove_col(0);

  $this->assertEquals("CTG\nGCA\nFGR\n", strval($s));

  $s->remove_col(1,2);

  $this->assertEquals("C\nG\nF\n", strval($s));
}

function testConsensus() {
  $s = new SequenceSet();
  $s->add(
    'AA',
    'AB',
    'BB'
  );

  $this->assertEquals([['A' => 2, 'B' => 1],['A' => 1, 'B' => 2]], $s->consensus()->data);
}

function testNormalizeDirection1() {
  $s = new SequenceSet();
  $s->add(
    'AA',
    'TT',
    'AA',
    'AA');
  $this->assertEquals("AA\nAA\nAA\nAA\n",strval($s->normalize_direction()));
}

function testNormalizeDirection2() {
  $s = new SequenceSet();
  $s->add(
    'AA',
    'TC',
    'AA',
    'AA');
  $this->assertEquals("AA\nGA\nAA\nAA\n",strval($s->normalize_direction()));
}


}
