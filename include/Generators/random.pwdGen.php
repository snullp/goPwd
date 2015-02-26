<?php
/*
  This generator is equivelent to traditional generate-and-store solutions
 */

$generators['Random'] = array(
    'require' => array('seed' => rand()),
    'function' => function($name, $ukey, $argv){
        // seed is not specified, cannot generate a new one and save to conf, abort.
        if (!isset($argv['seed'])) return 0;
        $number = intval($argv['seed']);

        srand($number);
        $result = "";
        for ($i=0;$i<10;$i++){
            $tmp = rand() % (10+26+26);
            if ($tmp < 10) $result.=$tmp;
            else if ($tmp < 10+26) 
                $result.=chr(ord('a')+($tmp-10));
            else $result.=chr(ord('A')+($tmp-10-26));
        }

        return $result;
    });

