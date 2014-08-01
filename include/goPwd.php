<?php
require_once 'config.php';
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
require_once 'Google/Client.php';

if (session_id() == '') session_start();

/*
    If user is authenticated, return user's email address
 */
function get_email(){
    if (!isset($_SESSION['id'])) return '';
    return $_SESSION['id']['email'];
}

/*
    If user is authenticated, return user's primary key
 */
function get_key(){
    if (!isset($_SESSION['id'])) return '';
    return $_SESSION['id']['key'];
}

/*
    private function, return a configured GoogleClient

    @param state if given a state string, will send it also for anti-forgery purpose
 */
function get_gclient($state = null){
    global $config;
    global $_client;
    if ($_client == null){
        $client = new Google_Client();
        $client->setClientId($config['client_id']);
        $client->setClientSecret($config['client_secret']);
        $client->setRedirectUri($config['redirect_uri']);
        $client->setScopes('email');
        $_client = $client;
    }

    if (is_string($state)) 
        $_client->setState($state);
    else
        $_client->setState(null);

    return $_client;
}

/*
    get authentication request url
 */
function get_auth_url(){
    $state = md5(rand());
    $_SESSION['last_state']=$state;

    $state_str = json_encode(array(
        'state' => $state,
        'ret' => $_SERVER['REQUEST_URI']
    ));

    return get_gclient($state_str)->createAuthUrl();
}

/*
 per website configuration storage
 */
function get_configs($name){
}

function set_configs($name, $config){
}
