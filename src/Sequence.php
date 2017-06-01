<?php
namespace bio;

class Sequence implements \ArrayAccess {

public $id;
public $name;
public $data;

const TYPE_UNDEF = 0;
const TYPE_NUCLEOTIDE = 1;
const TYPE_AMINOACID = 2;

function __construct($data = null) {
  if ( is_null($data) ) $this->data = [];
  else if ( is_string($data) ) $this->data = $data;
  else {
    $this->id = $data['id'] ?? null;
    $this->name = $data['name'] ?? null;
    $this->data = $data['data'] ?? [];
  }
}

static function Obj($data) {
  if ( $data instanceof Sequence ) return clone $data;
  else return new Sequence($data);
}

function codon($index) {
  return substr($this->data, $index, 3);
}

function forCodon($offset) {
  $index = 0;
  $size = $this->length() - 3;
  for ( $i = $offset ; $i <= $size  ; $i += 3 )
    yield $index++ => $this->codon($i);
}

function translate($offset = 0, $table = null) {
  $table = $table ?? [
   "TTT"=>'F',  "TCT"=>'S',  "TAT"=>'Y',  "TGT"=>'C',
   "TTC"=>'F',  "TCC"=>'S',  "TAC"=>'Y',  "TGC"=>'C',
   "TTA"=>'L',  "TCA"=>'S',  "TAA"=>'#',  "TGA"=>'#',
   "TTG"=>'L',  "TCG"=>'S',  "TAG"=>'#',  "TGG"=>'W',
   "CTT"=>'L',  "CCT"=>'P',  "CAT"=>'H',  "CGT"=>'R',
   "CTC"=>'L',  "CCC"=>'P',  "CAC"=>'H',  "CGC"=>'R',
   "CTA"=>'L',  "CCA"=>'P',  "CAA"=>'Q',  "CGA"=>'R',
   "CTG"=>'L',  "CCG"=>'P',  "CAG"=>'Q',  "CGG"=>'R',

   "ATT"=>'I',  "ACT"=>'T',  "AAT"=>'N',  "AGT"=>'S',
   "ATC"=>'I',  "ACC"=>'T',  "AAC"=>'N',  "AGC"=>'S',
   "ATA"=>'I',  "ACA"=>'T',  "AAA"=>'K',  "AGA"=>'R',
   "ATG"=>'M',  "ACG"=>'T',  "AAG"=>'K',  "AGG"=>'R',

   "GTT"=>'V',  "GCT"=>'A',  "GAT"=>'D',  "GGT"=>'G',
   "GTC"=>'V',  "GCC"=>'A',  "GAC"=>'D',  "GGC"=>'G',
   "GTA"=>'V',  "GCA"=>'A',  "GAA"=>'E',  "GGA"=>'G',
   "GTG"=>'V',  "GCG"=>'A',  "GAG"=>'E',  "GGG"=>'G'
 ];

  $seq = "";
  foreach ( $this->forCodon($offset) as $codon ) {
    $seq .= ($table[$codon] ?? 'X');
  }
  return $seq;
}

function translates($table = null) {
  $r = ['n' => [], 'r' => []];
  $seq = clone $this;
  $r['n'][] = $seq->translate(0, $table);
  $r['n'][] = $seq->translate(1, $table);
  $r['n'][] = $seq->translate(2, $table);

  $seq->reverse_complement();
  $r['r'][] = $seq->translate(0, $table);
  $r['r'][] = $seq->translate(1, $table);
  $r['r'][] = $seq->translate(2, $table);

  return $r;
}

function has_name() {
  if ( !empty($this->name) ) return true;
  if ( !empty($this->id) ) return true;
  return false;
}

function length() {
  return strlen($this->data);
}

function ungap() {
  $this->data = str_replace('-', '', $this->data);
  return $this;
}

function forElem($upper = false) {
  $len = $this->length();
  for ( $i = 0 ; $i < $len ; $i++ ) {
    $c = $this->data[$i];
    if ( $upper ) $c = strtoupper($c);
    yield $i => $c;
  }
}

function complement() {
  $map = [
    'A' => 'T',
    'T' => 'A',
    'U' => 'A',
    'G' => 'C',
    'C' => 'G'
  ];
 
  foreach ( $this->forElem(true) as $i => $c ) {
    $this->data[$i] = $map[$c] ?? $c;
  }
  
  return $this;
}

function to_lower() {
  $this->data = strtolower($this->data);
  return $this;
}

function to_upper() {
  $this->data = strtoupper($this->data);
  return $this;
}

function reverse() {
  $this->data = strrev($this->data);
  return $this;
}

function reverse_complement() {
  return $this->reverse()->complement();
}

function id_str() {
  if ( !empty($this->id) ) {
    return sprintf('ID%', $this->id);
  }
  return null;
  
}

function offsetGet($offset) { return $this->data[$offset] ?? null; }

function offsetSet($offset, $value) { return $this->data[$offset] = $value; }

function offsetExists($offset) { return isset($this->data[$offset]); }

function offsetUnset($offset) { unset($this->data[$offset]); }

function fasta($mode = 'name') {
  $fasta_name;
  if ( $mode == 'name' ) {
    $fasta_name = $this->name ?? $this->id_str() ?? '';
  } else {
    $fasta_name = $this->id_str() ?? $this->name ?? '';
  }
  return sprintf(">%s\n%s\n", $fasta_name, $this->data);  
}

function type() {
  $aminoacid_exclusive = ['E', 'F', 'I', 'L', 'P', 'Q', 'Z'];

  foreach ( $this->forElem(true) as $i => $c ) {
    if ( in_array($c, $aminoacid_exclusive) )
      return self::TYPE_AMINOACID;
  }
  
  return self::TYPE_NUCLEOTIDE;
}

function remove($pos, $length = 1) {
  $this->data = substr_replace($this->data, '', $pos, $length);
  return $this->data;
}

function fill(int $len) {
  $this->data = str_pad($this->data, $len, '-');
}

function search_name($pattern) {
  return (strpos($this->name, $pattern) !== FALSE);
}

function search_pattern($pattern) {
  if ( preg_match_all($pattern , $this->data , $matches, PREG_OFFSET_CAPTURE) ) {
    return $matches[0];
  } else return [];
}

function combined_type($previous_type = Sequence::TYPE_UNDEF) {
  $type = $this->type();
  if ( $previous_type == Sequence::TYPE_AMINOACID ) return Sequence::TYPE_AMINOACID;
  return $type;
}

function is_compatible($other_type) {
  $t = $this->type();
  if ( $t == Sequence::TYPE_UNDEF ) return false;
  if ( $t == Sequence::TYPE_AMINOACID && $other_type == Sequence::TYPE_NUCLEOTIDE ) return false;
  else return true;
}

//Needleman-Wunsch algorithm
static function scoring_matrix(Sequence $seq1, Sequence $seq2) {
  $rows = $seq1->length();
  $cols = $seq2->length();

  $matrix = array_fill(0, $rows + 1, array_fill(0, $cols + 1, 0));

  //esta función sirve como una matriz de similitud simple
  $similarity = function($c1, $c2) {
    if ( strtoupper($c1) == strtoupper($c2) ) return 2;
    if ( $c1 == '-' || $c2 == '-' ) return 0; //linea adicional para que alinee bien, buscar TEST_LEGACY_BUG_1 
    else return -1;
  };
 
  $global_max = -1;
  $global_max_coords = [];

  for ( $i = 1 ; $i <= $rows ; $i++ ) {
  for ( $j = 1 ; $j <= $cols ; $j++ ) {
    $elem_1 = $seq1[$i - 1];
    $elem_2 = $seq2[$j - 1];

    $match  = $matrix[$i - 1][$j - 1] + $similarity($elem_1, $elem_2);
    $delete = $matrix[$i - 1][$j    ] + $similarity($elem_1, '-');
    $insert = $matrix[$i    ][$j - 1] + $similarity('-'    , $elem_2);

    $local_max = max(0, $match, $delete, $insert);
    $matrix[$i][$j] = $local_max;
    if ( $local_max > $global_max ) {
      $global_max = $local_max;
      $global_max_coords[0] = $i;
      $global_max_coords[1] = $j;   
    }
  }}

  return ['matrix' => $matrix, 'max' => $global_max, 'max_coords' => $global_max_coords];
}

static function align($seq1, $seq2) {
  $seq1 = Sequence::Obj($seq1);
  $seq2 = Sequence::Obj($seq2);
  
  $matrix = self::scoring_matrix($seq1, $seq2);

  $a_seq1 = $seq1->data;
  $a_seq2 = $seq2->data;

  $i = $matrix['max_coords'][0];
  $j = $matrix['max_coords'][1];
  $matrix = $matrix['matrix'];

  while( $i > 0 || $j > 0 ) {
    $next_i = max(0, $i - 1);
    $next_j = max(0, $j - 1);
    $max = $matrix[$next_i][$next_j];


    if ( $j > 0 ) {
      $next_max = $matrix[$i][$j - 1];
      if ( $next_max > $max ) {
        $max = $next_max;
        $next_i = $i;
        $next_j = $j - 1;
      }
    }

    if ( $i > 0 ) {
      $next_max = $matrix[$i - 1][$j];
      if ( $next_max > $max ) {
        $max = $next_max;
        $next_i = $i - 1;
        $next_j = $j;
      }
    }

    if ( $i > 0 && $j > 0 ) {
       $next_max = $matrix[$i - 1][$j - 1];
        if ( $next_max > $max ) {
          $max = $next_max;
          $next_i = $i - 1;
          $next_j = $j - 1;
        }
    }

    if ( $next_i == $i - 1 && $next_j == $j ) {
      $a_seq2 = substr_replace($a_seq2, '-', $j, 0);
    }

    if ( $next_j == $j - 1 && $next_i == $i ) {
      $a_seq1 = substr_replace($a_seq1, '-', $i, 0);
    }

    $i = $next_i;
    $j = $next_j;
  }

  return [$a_seq1, $a_seq2];
}

static function similarity($seq1, $seq2, $gap_weight = -1.0) {
  $seq1 = Sequence::Obj($seq1);
  $seq2 = Sequence::Obj($seq2);
  $minlen = min($seq1->length(), $seq2->length());
  $score = 0;
  for ( $i = 0 ; $i < $minlen ; $i++ ) {
    $c1 = $seq1[$i];
    $c2 = $seq2[$i];
    if ( $c1 == '-' && $c2 == '-' ) continue;
    else if ( $c1 == '-' || $c2 == '-' ) {
      if ( $gap_weight <= 0.0 ) continue;
      else $score += $gap_weight;
    } else if ( strtoupper($c1) == strtoupper($c2) )
      $score++;
  }
  return $score;
}

static function align_similarity($seq1, $seq2) {
  $alignment = self::align($seq1, $seq2);
  return self::similarity(...$alignment);
}

static function is_reversed($seq1, $seq2) {
  $sim_n = self::align_similarity($seq1, $seq2);
  $sim_r = self::align_similarity($seq1, Sequence::Obj($seq2)->reverse_complement());
  return $sim_r > $sim_n;
}

function __toString() { return $this->data; }


}
