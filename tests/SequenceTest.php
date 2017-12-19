<?php
use PHPUnit\Framework\TestCase;

use \edwrodrig\bio\Sequence;

class SequenceTest extends TestCase {

function ungapProvider() {
  return  [
[
  'AAA----CCCC',
  'AAACCCC'
],

[
  'aa-bb---ccc-cfa',
  'aabbccccfa'
]

];
}

/**
 * @dataProvider ungapProvider
 */
function testUngap($sequence, $expected) {
  $s = new Sequence($sequence);
  $this->assertEquals($expected, strval($s->ungap()));
}

function reverseProvider() {
  return  [
[
  'AAA----CCCC',
  'CCCC----AAA'
],

[
  'aa-bb---ccc-cfa',
  'afc-ccc---bb-aa'
],

[
  'ACTGACTG',
  'GTCAGTCA'
]

];
}

/**
 * @dataProvider reverseProvider
 */
function testReverse($sequence, $expected) {
  $s = new Sequence($sequence);
  $this->assertEquals($expected, strval($s->reverse()));

}

function complementProvider() {
  return  [
[
  'ATUGC',
  'TAACG'
],

[
  'atugc',
  'TAACG'
],

[
  'AATT--U-g-c',
  'TTAA--A-C-G'
]

];

}

/**
 * @dataProvider complementProvider
 */
function testComplement($sequence, $expected) {
  $s = new Sequence($sequence);
  $this->assertEquals($expected, strval($s->complement()));

}

function typeProvider() {
  return [
[ 'ACTG', Sequence::TYPE_NUCLEOTIDE],
[ 'AAAA', Sequence::TYPE_NUCLEOTIDE],
[ 'AAAE', Sequence::TYPE_AMINOACID],
[ 'aaae', Sequence::TYPE_AMINOACID]
];
}

/**
 * @dataProvider typeProvider
 */
function testType($sequence, $expected) {
  $s = new Sequence($sequence);
  $this->assertEquals($expected, $s->type());
}

function combinedTypeProvider() {
  return [
[ 'A', Sequence::TYPE_UNDEF, Sequence::TYPE_NUCLEOTIDE],
[ 'A', Sequence::TYPE_NUCLEOTIDE, Sequence::TYPE_NUCLEOTIDE],
[ 'A', Sequence::TYPE_AMINOACID, Sequence::TYPE_AMINOACID],
[ 'E', Sequence::TYPE_UNDEF, Sequence::TYPE_AMINOACID],
[ 'E', Sequence::TYPE_NUCLEOTIDE, Sequence::TYPE_AMINOACID],
[ 'E', Sequence::TYPE_AMINOACID, Sequence::TYPE_AMINOACID]
];
}

/**
 * @dataProvider combinedTypeProvider
 */
function testCombinedType($sequence, $previous, $expected) {
  $s = new Sequence($sequence);
  $this->assertEquals($expected, $s->combined_type($previous));
}

function fillProvider() {
  return [
['A', 1, 'A'],
['A', 2, 'A-'],
['AA', 5, 'AA---']
];

}

/**
 * @dataProvider fillProvider
 */
function testFill($sequence, $len, $expected) {
  $s = new Sequence($sequence);
  $s->fill($len);
  $this->assertEquals($expected, strval($s));

}

function testObjUsingObj() {
  $s = new Sequence('ACTG');
  $this->assertEquals($s, Sequence::Obj($s));
}

function testObjUsingString() {
  $s = Sequence::Obj('ACTG');
  $this->assertInstanceOf(Sequence::class, $s);
}

function searchPatternProvider() {
  return [
['AACCTTGG', '/AA/', [['AA', 0]]],
['AACCAA', '/AA/', [['AA', 0], ['AA', 4]]],
['AACCAA', '/CC/', [['CC', 2]]]
];
}

/**
 * @dataProvider searchPatternProvider
 */
function testSearchPattern($seq, $pattern, $expected) {
  $s = Sequence::Obj($seq);
  $this->assertEquals($expected, $s->search_pattern($pattern));
}

function testSearchName() {
  $s = new Sequence;
  $s->name = 'hola como te va';
  $this->assertTrue($s->search_name('como'));
  $this->assertFalse($s->search_name('chao'));
}

function alignProvider() {
  return [[
'AACCTG',
'AATTTTCCTG',
'AA----CCTG',
'AATTTTCCTG'
]
];
}

/**
 * @dataProvider alignProvider
 */
function testAlign($seq1, $seq2, $aseq1, $aseq2) {
  $this->assertEquals([$aseq1, $aseq2], Sequence::align($seq1, $seq2));
}

function testAlignLegacyBug1() {
  $this->testAlign(
'GCATGCU',
'GATTACA',
'GCAT-GCU',
'G-ATTACA'
);
/*
  cuando se saca la linea TEST_LEGACY_BUG_1 en la funcion scoring_matrix el alineado queda
  GCATGCU
  G-ATTACA
  que es peor que
  GCAT-GCU
  G-ATTACA
*/
}

function similarityProvider() {
  return [[
'AAA',
'ABA', 2
],
[
'GCAT-GCU',
'G-ATTACA', 4
],
[
'GCATGCU',
'G-ATTACA', 3
],
[
'AAA',
'AAA', 3
],
[
'AA-A',
'AA-A', 3
],
[
'A-A-A',
'A-A-A', 3
]
];
}

/**
 * @dataProvider similarityProvider
 */
function testSimilarity($seq1, $seq2, $similarity) {
  $this->assertEquals($similarity, Sequence::similarity($seq1, $seq2));
}

function alignSimilarityProvider() {
  return [
['AAA', 'AAA', 3],
['AAA', 'CCC', 0]
];
}

/**
 * @dataProvider alignSimilarityProvider
 */
function testAlignSimilarity($seq1, $seq2, $similarity) {
  $this->assertEquals($similarity, Sequence::align_similarity($seq1, $seq2));
}

function isReversedProvider() {
  return [
['AAA', 'AAA', false],
['AAA', 'TTT', true]
];
}

/**
 * @dataProvider isReversedProvider
 */
function testIsReversed($seq1, $seq2, $expected) {
  $this->assertEquals($expected, Sequence::is_reversed($seq1, $seq2));
}

function testCodon() {
  $s = Sequence::Obj('ACTGAAACCCTTTGGG');
  $this->assertEquals('ACT', $s->codon(0));
  $this->assertEquals('CTG', $s->codon(1));
  $this->assertEquals('AAA', $s->codon(4));  
}

function testForCodon() {
  $s = Sequence::Obj('ACTGAAACCCTTTGGG');
  $a = iterator_to_array($s->forCodon(0));
  $this->assertEquals('ACT', $a[0]);
  $this->assertEquals('GAA', $a[1]);
  $this->assertEquals('ACC', $a[2]);
  $this->assertEquals('CTT', $a[3]);
  $this->assertEquals('TGG', $a[4]);
  $this->assertEquals(5, count($a));

  $a = iterator_to_array($s->forCodon(1));
  $this->assertEquals('CTG', $a[0]);
  $this->assertEquals('AAA', $a[1]);
  $this->assertEquals('CCC', $a[2]);
  $this->assertEquals('TTT', $a[3]);
  $this->assertEquals('GGG', $a[4]);
  $this->assertEquals(5, count($a));
}

function testTranslate() {
  $s = Sequence::Obj('GATTAGTCGCGT');
  $this->assertEquals('D#SR', $s->translate());
}

function testTranslates() {
  $s =  Sequence::Obj('GATTAGTCGCGT');
  $this->assertEquals([
'n' => [
'D#SR',
'ISR',
'LVA'
],
'r' => [
'TRLI',
'RD#',
'ATN'
]]
  , $s->translates());


}

}
