<?php
use PHPUnit\Framework\TestCase;

use \edwrodrig\bio\SequenceConsensus;

class SequenceConsensusTest extends TestCase {

function testOffsetGet() {
  $s = new SequenceConsensus();
  $this->assertEquals(0, $s[[0,'A']]);
}

function testOffsetSet() {
  $s = new SequenceConsensus();
  $s[[0, 'A']] = 10;
  $this->assertEquals(10, $s[[0, 'A']]);

  $s->add([0, 'A'], 10);

  $this->assertEquals(20, $s[[0, 'A']]);
}

function testNormalize() {
  $s = new SequenceConsensus();
  $s[[0, 'A']] = 25;
  $s[[0, 'B']] = 50;
  $s[[0, 'C']] = 25;

  $s->normalize();

  $this->assertEquals(['A' => 0.25, 'B' => 0.5, 'C' => 0.25], $s->data[0]);
}

function testSeq() {
  $s = new SequenceConsensus();
  $s[[0, 'A']] = 1;
  $s[[1, 'B']] = 2;
  $s[[2, 'C']] = 3;
  
  $this->assertEquals('ABC', $s->seq());

}

function testSeq2() {
  $s = new SequenceConsensus();
  $s[[0, 'A']] = 1;
  $s[[1, 'B']] = 2; $s[[1, 'D']] = 5;
  $s[[2, 'C']] = 3;

  $this->assertEquals('ADC', $s->seq());
}

function testAddSeq1() {
  $s = new SequenceConsensus();
  $s1 = 'ACTG';
  $s2 = 'GTCA';

  $s->add_seq($s1);  
  $this->assertEquals($s1, $s->seq());

  $s->add_seq($s1);  
  $this->assertEquals($s1, $s->seq());

  $s->add_seq($s2);  
  $this->assertEquals($s1, $s->seq());

  $s->add_seq($s2);  
  $this->assertEquals($s1, $s->seq());

  $s->add_seq($s2);  
  $this->assertEquals($s2, $s->seq());

  $s->add_seq($s2);  
  $this->assertEquals($s2, $s->seq());



}

}

