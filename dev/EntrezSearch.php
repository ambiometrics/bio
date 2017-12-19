<?php

class EntrezSearch {

const BASE_URL = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils';
public $db;

function search($term, $max = 100) {
  $data = file_get_contents(sprintf(self::BASE_URL . "/esearch.fcgi?db=%s&term=%s&retmax=%d&retmode=json&tool=bosque&email=sram@profc.udec.cl", $this->db, $term, $max));
  $data = json_decode($data, true);
  implode(',',$data['esearchresult']['idlist']);

  file_get_contents(sprintf(self::BASE_URL . "/esummary.fcgi?db=%s&id=
}

function summary(...$id) {

}


}

$base_url = 'https://eutils.ncbi.nlm.nih.gov/entrez/eutils';
$base_params = 'retmode=json&tool=bosque&email=sram@profc.udec.cl';
$db = 'nucleotide';
$term = 'coli';
$data = file_get_contents(sprintf("$base_url/esearch.fcgi?db=%s&term=%s&retmax=%d&$base_params", $db, $term,100));

$data = json_decode($data, true);

$ids = implode(",", $data['esearchresult']['idlist']);

$data = file_get_contents(sprintf("$base_url/esummary.fcgi?db=%s&id=%s&$base_params", $db, $ids));

$data = json_decode($data, true);

$result = [];

foreach ( $data['result'] as $index => $item ) {
  if ( $index == 'uids' ) continue;
  $result[] = [
    'id' => $index,
    'accession' => $item['caption'],
    'gi' => $item['gi'],
    'definition' => $item['title'],
    'lenght' => $item['slen']
  ];
}

var_dump($result);

$data = file_get_contents(sprintf("$base_url/efetch.fcgi?db=%s&id=%s&rettype=gb&retmode=text&tool=bosque&email=sram@profc.udec.cl", $db, $result[0]['id']));

echo $data;

