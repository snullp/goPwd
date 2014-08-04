<?php
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
require_once 'config.php';
require_once 'Google/Client.php';

if (session_id() === '') session_start();

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
    if ($_client === null){
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
function pad_or_truncate($str, $len){
    if (strlen($str)< $len) return str_pad($str,$len);
    else return substr($str,0,$len);
}

function create_dir_if_nonexist($path){
    if (!file_exists($path)){
        mkdir($path, 0777, true);
    }
}

function get_configs($name){
    $name = urlencode($name);
    $folder = dirname(__FILE__).'/Configs/'.get_email();
    if (!file_exists($folder."/$name")) return null;
    return json_decode(openssl_decrypt(file_get_contents($folder."/$name"),'des-cfb',get_key(),0,pad_or_truncate($name,8)),true);
}

function set_configs($name, $config){
    $name = urlencode($name);
    $folder = dirname(__FILE__).'/Configs/'.get_email();
    create_dir_if_nonexist($folder);
    file_put_contents($folder."/$name",openssl_encrypt(json_encode($config),'des-cfb',get_key(),0,pad_or_truncate($name,8)));
}

/*
 password generator interfaces
 */

function get_pwdgen_list(){
    return array_filter(scandir(dirname(__FILE__).'/Generators'),function ($var){
        $test = ".pwdGen.php";
        $strlen = strlen($var);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($var, $test, $strlen - $testlen, $testlen)===0;
    });
}

function get_pwd($gen, $name, $configs=null){
    if ($name === "") return "";
    if ($configs!==null) set_configs($name,$configs);
    else $configs = get_configs($name);
    if (isset($configs['generator'])) $gen = $configs['generator'];
    if (!in_array($gen, get_pwdgen_list())) return "";
    require_once dirname(__FILE__)."/Generators/$gen";
    return generate($name, get_key(), $configs);
}
