<?php
require_once '../include/goPwd.php';

function redirectTo($uri = '/'){
    $url = 'https://'.$_SERVER['SERVER_NAME'].$uri;
    header("Location: $url");
    die();
}

$state_array = json_decode($_GET['state'],true);

//cannot decode state query as json
if ($state_array==null) redirectTo('/');

$ret_addr = $state_array['ret'];

// Verify state
if (!isset($_SESSION['last_state']) || $_SESSION['last_state'] != $state_array['state']) redirectTo($ret_addr);

//state check passed
unset($_SESSION['last_state']);

//get id_token
$id_token = null;
try{
    $client = get_gclient();
    $client->authenticate($_GET['code']);
    $id_token = $client->verifyIdToken()->getAttributes();
} catch (Exception $e){
    echo $e->getMessage();
    die();
}

//check if we got real id_token
if ($id_token == null || !isset($id_token['payload']) || !is_array($id_token['payload'])) redirectTo($ret_addr);

session_regenerate_id(true);

$_SESSION['id']=array(
    'email' => $id_token['payload']['email'],
//calculate user key
    'key' => sha1($config['client_secret'].$id_token['payload']['sub'])
);
redirectTo($ret_addr);

