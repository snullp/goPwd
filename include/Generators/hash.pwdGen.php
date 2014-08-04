<?php
/*
 This generator simply combines the entity name and user key, then return the hash of them. Could it offer enough security?
 */

function generate($name, $ukey, $options=null){
    return sha1($name.$ukey);
}
