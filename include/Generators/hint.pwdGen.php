<?php
/*
 * Store anything you want
 */


$generators['Hint'] = array(
    'require' => array('hint' => '123456?'),
    'function' => function($name, $ukey, $argv) {
        return isset($argv['hint']) ? $argv['hint'] : 'Busted!';
    });
