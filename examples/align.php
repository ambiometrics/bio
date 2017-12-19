<?php

require_once(__DIR__ . '/../vendor/autoload.php');


$s1 = 'AACCTG';
$s2 = 'AACTTTTTTTCTG';


$align = \edwrodrig\bio\Sequence::align($s1, $s2);

var_dump($align);

echo \edwrodrig\bio\Sequence::similarity($align[0], $align[1]), "\n";


