<?php
/*
  This generator is equivelent to traditional generate-and-store solutions
 */

function generate($name, $ukey, $options=null){
    $config = get_configs($name);
    $number = rand();
    if (isset($config['number']))
        $number = $config['number'];
    else{
        if (is_array($config)) $config['number']=$number;
        else $config = Array('number' => $number);
        set_configs($name,$config);
    }
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
}
