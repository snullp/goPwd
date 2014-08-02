<?php
/*
 Warning: this file is not properly written as it contains several security
 vulnerabilities. DO NOT USE in real practice.

 */
?>
<!DOCTYPE html>
<html>
<head>
<title>A simple password manager</title>
</head>
<body>
<?php
require_once 'include/goPwd.php';

$email = get_email();
if ($email == ''){
    //not logged in
?>
    <a href="<?php echo get_auth_url();?>"> login </a>
<?php
} else {
    //logged in, check available filters
    echo "Logged in as $email";
    echo  "<br>";
    if (isset($_GET['name'])){
        //process the query
            echo $_GET['generator'].": Password for ".$_GET['name'].": ".get_pwd($_GET['generator'],$_GET['name']);

    }
?>
<form action="." method="get">
<input type="text" name="name"> 
<select name="generator">
<?php
    $gens = get_pwdgen_list();
    foreach ($gens as $gen){
        echo "<option>".$gen."</option>";
    }
?>
</select>
<input type="submit" value="submit">

</form>
<?php
}
?>
</body>
</html>
