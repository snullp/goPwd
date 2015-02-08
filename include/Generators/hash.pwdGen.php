<?php
/*
 This generator simply combines the entity name and user key, then return the hash of them. Could it offer enough security?
 */


$generators['Hash'] = array(
    'require' => array(),
    'function' => function($name, $ukey, $options) {
        return sha1($name.$ukey);
    });
