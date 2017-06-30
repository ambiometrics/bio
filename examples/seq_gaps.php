<?php

require_once(__DIR__ . '/../include.php');

$seq = new \bio\Sequence('ACTAAACCCTCTGCTAGCTAGTGTACGTGTGTCAGTCGAT');
echo $seq, "\n";


echo $seq->codon(7), "\n";


echo $seq->translate(), "\n";


echo $seq->length(), "\n";


$seq2 = new \bio\Sequence("ACAKLMLKM----AKLNCNKAJNCJKA");

echo $seq2, "\n";

echo $seq2->ungap()->to_lower(), "\n";

echo $seq2->fasta(), "\n";

