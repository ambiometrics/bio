<?php
namespace edwrodrig\bio;

class SequenceSet {

public $seqs = [];

function add(...$seqs) {
  foreach ($seqs as $seq ) {
    $s = Sequence::Obj($seq);
    $s->name = 'ALIGN_ ' . count($this->seqs);
    $this->seqs[] = $s;
  }
}

function max_length() {
  $len = 0;
  foreach ( $this->seqs as $seq ) {
    $current_len = $seq->length();
    $len = $current_len > $len ? $current_len : $len;
  }
  return $len;
}

function normalize_length() {
  $len = $this->max_length();
  foreach ( $this->seqs as $seq ) {
    $seq->fill($len);
  }
  return $this;
}

function remove_col($pos, $length = 1) {
  foreach ( $this->seqs as $seq ) {
    $seq->remove($pos, $length);
  }
}

function type() {
  $current_type = Sequence::TYPE_UNDEF;
  foreach ( $this->seqs as $seq ) {
    $current_type = $seq->combined_type($current_type);
  }
  return $current_type;
}

function __toString() {
  $out = '';
  foreach ( $this->seqs as $seq ) {
    $out .= strval($seq) . "\n";
  }
  return $out;
}

function fasta($mode = 'name') {
  $out = '';
  foreach ($this->seqs as $seq ) {
    $out .= $seq->fasta($mode);
  }
  return $out;
}

function search_pattern($pattern) {
  $result = [];
  foreach ( $this->seqs as $index => $seq ) {
    $r = $seq->search_pattern($pattern);
    if ( empty($r) ) continue;
    $result[$index] = $r;
  }
  return $result;
}

function search_name($pattern) {
  $result = [];
  foreach ( $this->seqs as $index => $seq ) {
    if ( $seq->search_name($pattern) ) $result[] = $index;
  }
  return $result;
}

function consensus() {
  $c = new SequenceConsensus();

  foreach ( $this->seqs as $seq )
    $c->add_seq($seq);
  return $c;
}

function normalize_direction() {
  $c = $this->consensus()->seq();
  
  foreach ( $this->seqs as $seq ) {
    if ( Sequence::is_reversed($c, $seq) ) {
      $seq->reverse_complement();
    }
  }
  return $this;
}

}
