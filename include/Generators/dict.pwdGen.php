<?php
/*
This generator is designated to pick some words from a dictionary based on the input, will assume the hash of the input provides enough fairness in its distribution.
Will adjust the result (perhaps generate algorithm?) based on password requirement (No digit? No special character?)
 */

function hash_len($dict_size){
    $len = ceil(log($dict_size,16));
    if ($len < 4) $len = 4;
    return $len;
}

$generators['Dict'] = array(
    'require' => array('plainpwd' => 0, 'nospecial' => 0),
    'function' => function($name, $ukey, $argv) {
        $dict_file = fopen(dirname(__FILE__) . '/dict','r');
        $dict = Array();

        while ($line = fgets($dict_file)) {
            $dict[] = trim($line);
        }

        $dict_size = count($dict);
        $hash_len = hash_len($dict_size);

        $hash = md5($name.$ukey);

        $first = substr($hash,0,4);
        $rest = substr($hash,4);

        $words = Array();
        $rest_len = strlen($rest);

        while ($rest_len > $hash_len) {
            $index = substr($rest,0,$hash_len);
            $rest = substr($rest,$hash_len);
            $rest_len -= $hash_len;
            $words[] = $dict[hexdec($index) % $dict_size];
        }

        $words_count = count($words);
        if ($words_count < 3) return "Dictionary too small";

        $num = hexdec(substr($first,0,1));
        $special = hexdec(substr($first,1,1))%2==0?'!':'?';

        srand(hexdec(substr($first,2)));

        $numbers = range(0,$words_count-1);
        shuffle($numbers);

        $result = "";
        if (isset($argv['plainpwd']) && $argv['plainpwd'] !== '0'){
            $result .= $words[$numbers[0]];
            $result .= $words[$numbers[1]];
            $result .= $words[$numbers[2]];
        } else {
            $result .= ucfirst($words[$numbers[0]]);
            $result .= $num;
            $result .= ucfirst($words[$numbers[1]]);
            $result .= ucfirst($words[$numbers[2]]);
            if (isset($argv['nospecial']) && $argv['nospecial'] === '0')
                $result .= $special;
        }

        return $result;

    });

