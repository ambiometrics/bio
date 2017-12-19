<?php
namespace edwrodrig\bio;

class SequenceConsensus implements \ArrayAccess {

public $data = [];

function length() {
  return strlen($this->data);
}

function create_index($index, $elem) {
  $elem = strtoupper($elem);
  if ( !isset($this->data[$index]) ) {
    $this->data[$index] = [];
  }
  if ( !isset($this->data[$index][$elem]) ) {
    $this->data[$index][$elem] = 0;
  }
}

function offsetGet($offset) {
  $index = $offset[0];
  $elem = strtoupper($offset[1]);
  $this->create_index($index, $elem);
  return $this->data[$index][$elem];
}

function offsetSet($offset, $value) {
  $index = $offset[0];
  $elem = strtoupper($offset[1]);
  $this->create_index($index, $elem);
  $this->data[$index][$elem] = $value;
}

function offsetExists($offset) {
  return true;
}

function offsetUnset($offset) {;}

function add($offset, $value) {
  $this[$offset] = $this[$offset] + $value;
}

function add_seq($seq) {
  $seq = Sequence::Obj($seq);
  foreach ( $seq->forElem() as $index => $c ) {
    $this->add([$index, $c], 1);
  }
}

function normalize() {
  foreach ( $this->data as &$elem ) {
    $total = 0;
    foreach ( $elem as &$count ) {
      $total += $count;
    }
    foreach ( $elem as &$count ) {
      $count = $count / $total;
    }
  }
}

function seq() {
  $seq = '';
  foreach ( $this->data as $elem ) {
    $max_index = '';
    $max_value = -1;
    foreach ( $elem as $index => $value ) {
      if ( $value > $max_value ) {
        $max_index = $index;
        $max_value = $value;
      }
    }
    $seq .= $max_index;
  }
  return $seq;
}


}
