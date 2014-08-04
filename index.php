<!DOCTYPE html>
<!-- Warning: this file is not properly written as it contains several security vulnerabilities. DO NOT USE in real practice. -->
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
        if (!isset($_GET['update'])) {
            if ($options = get_configs($_GET['name'])){
                echo "System found existing configurations: ";
                print_r($options);
            }
            echo "<br>";
            echo $_GET['generator'].": Password for ".$_GET['name'].": ".get_pwd($_GET['generator'],$_GET['name'],null);
        } else {
            echo "Configurations updated: ";
            //convert options
            $options = Array();
            if (isset($_GET['options'])) {
                foreach($_GET['options'] as $value){
                    $options[$value] = true;
                }
            }
            $options['generator'] = $_GET['generator'];
            print_r($options);
            echo "<br>";
            echo "Password for ".$_GET['name'].": ".get_pwd($_GET['generator'],$_GET['name'],$options);
        }

    }
?>
<hr>
<form action="." method="get">
<input type="text" name="name" value="<?php if(isset($_GET['name'])) echo $_GET['name']; ?>"> 
<select name="generator">
<?php
    $gens = get_pwdgen_list();
    foreach ($gens as $gen){
        echo "<option>".$gen."</option>";
    }
?>
</select>
<br>
<input id="update-check" type="checkbox" name="update" value=1>Update: 
<input type="checkbox" name="options[]" value="plainpwd" disabled>Plain Pwd
<input type="checkbox" name="options[]" value="nospecial" disabled>No Special
<input type="submit" value="submit">

</form>
<?php
}
?>
</body>
<script type="text/javascript">
(function(){
    var checkbox = document.getElementById('update-check');
    checkbox.addEventListener('click',function(){
        var list = document.getElementsByName('options[]');
        for (var i=0; i<list.length; i++){
            if (checkbox.checked){
                list[i].disabled = false;
            }else{
                list[i].disabled = true;
            }
        }
    });
})();
</script>
</html>
