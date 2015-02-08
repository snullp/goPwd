<?php
/*
  This generator is equivelent to traditional generate-and-store solutions
 */

$generators['Random'] = array(
    'require' => array('seed' => rand()),
    'function' => function($name, $ukey, $options){
        $number = intval($options['seed']);

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

