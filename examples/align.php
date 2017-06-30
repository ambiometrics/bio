<?php

require_once(__DIR__ . '/../include.php');


$s1 = 'AACCTG';
$s2 = 'AACTTTTTTTCTG';


$align = \bio\Sequence::align($s1, $s2);

var_dump($align);

echo \bio\Sequence::similarity($align[0], $align[1]), "\n";


