<?php
/*
 This generator simply combine the entity name and user key, then return the hash of them. Could it offer enough security?
 */

function generate($name, $ukey, $option=null){
    return sha1($name.$gkey);
}
