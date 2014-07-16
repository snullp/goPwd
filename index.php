<?php
require_once 'include/goPwd.php';

$email = get_email();
if ($email == ''){
?>

    <a href="<?php echo get_auth_url();?>"> login </a>
<?php
} else {
    echo "Logged as $email";
    echo  "<br>";
    echo "Key ".get_key();
}
