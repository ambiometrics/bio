<?php

require_once(__DIR__ . '/../include.php');

use \bio\Sequence;

$seq = new Sequence("ACAKLMLKM----AKLNCNKAJNCJKA");

echo $seq, "\n";

echo $seq->ungap()->to_lower(), "\n";

echo $seq->fasta(), "\n";

