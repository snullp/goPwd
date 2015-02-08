<?php
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
require_once 'config.php';
require_once 'Google/Client.php';

if (session_id() === '') session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// import all generator settings
function get_pwdgen_list() {
    return array_filter(scandir(dirname(__FILE__).'/Generators'),
        function ($var){
            $test = ".pwdGen.php";
            $strlen = strlen($var);
            $testlen = strlen($test);
            if ($testlen > $strlen) return false;
            return substr_compare($var,
                                  $test,
                                  $strlen - $testlen,
                                  $testlen)===0;
        });
}

function init_generators() {
    global $generators;
    $generator_php_files = get_pwdgen_list();
    
    foreach ($generator_php_files as $gen_file) {
        require_once "Generators/$gen_file";
    }
}

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

// extend or truncate a string to given length, used by openssl lib
function pad_or_truncate($str, $len) {
    if (strlen($str)< $len) {
        return str_pad($str,$len);
    } else {
        return substr($str,0,$len);
    }
}

function create_dir_if_nonexist($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

function get_config($name) {
    $name = urlencode($name);
    $folder = dirname(__FILE__).'/Configs/'.get_email();

    if (!file_exists($folder."/$name")) {
        return null;
    }

    return json_decode(
                openssl_decrypt(
                    file_get_contents($folder."/$name"),
                    'des-cfb',
                    get_key(),
                    0,
                    pad_or_truncate($name,8)),
                true);
}

function set_config($name, $config) {
    $name = urlencode($name);
    $folder = dirname(__FILE__).'/Configs/'.get_email();

    create_dir_if_nonexist($folder);

    file_put_contents(
        $folder."/$name",
        openssl_encrypt(
            json_encode($config),
            'des-cfb',
            get_key(),
            0,pad_or_truncate($name,8)
        ));
}

/*
 password generator interfaces
 */

function get_pwd($name, $user_argv=null) {
    if ($name === '') return '';

    global $generators;

    if ($user_argv == null) {

        $argv = get_config($name);
        if ($argv == null) {
            return null;
        }

        $generator = $argv['generator'];

        print "<p class='debug'>Debug: Using $generator with ";
        print_r($argv);
        print '</p>';

        return $generators[$generator]['function']($name, get_key(), $argv);

    } else {

        $generator = $user_argv['generator'];

        if (!isset($generators[$generator])) return null;

        set_config($name, $user_argv);

        print "<p class='debug'>Debug: Using $generator with ";
        print_r($user_argv);
        print '</p>';

        return $generators[$generator]['function']($name, get_key(), $user_argv);

    }
}
