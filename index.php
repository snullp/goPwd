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
if ($email === '') {
    //not logged in
?>
    <a href="<?php echo get_auth_url();?>"> login </a>
<?php
} else {
    init_generators();
    //logged in, check available filters
    echo "<p>Logged in as $email</p>\n";
    if (isset($_GET['name']) && isset($_GET['generator'])) {
        //process the query
        switch($_GET['generator']){
        case 'Saved':
            if ($pwd = get_pwd($_GET['name'])){
                echo "<p>Password for ".$_GET['name'].": $pwd</p>";
            } else {
                echo "<p>Error: No existing configs found.</p>";
            }
            break;
        default:
            $argv = array('generator' => $_GET['generator']);
            foreach ($_GET as $rkey => $value) {
                if (strncmp($rkey, 'conf_', 5) === 0) {
                    $key = substr($rkey, 5);
                    $argv[$key] = $value;
                }
            }

            if ($pwd = get_pwd($_GET['name'], $argv)){
                echo "<p>Password for ".$_GET['name'].": $pwd</p>";
            } else {
                echo "<p>Error.</p>";
            }
            break;
        }
    }
?>
<hr>
<form action="." method="get">
<p>
<input type="text" name="name" value="<?php if(isset($_GET['name'])) echo $_GET['name']; ?>"> 
<select id="gen-selector" name="generator">
    <option selected>Saved</option>
</select>
</p>
<div id="user-argv">
</div>
<p><input type="submit" value="submit"></p>
</form>

<script type="text/javascript">
(function(){
    var gendata = {<?php
foreach ($generators as $name => $value) {
    echo "'$name' : {";
    foreach ($generators[$name]['require'] as $key => $value) {
        echo "'$key' : '$value',";
    }
    echo "},";
}
?>};
    var sel = document.getElementById('gen-selector');
    for (var key in gendata) {
        var option = document.createElement('option');
        option.innerHTML = key;
        sel.appendChild(option);
    }
    sel.addEventListener('change', function() {
        var container = document.getElementById('user-argv');
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }
        if (sel.value === 'Saved') return;
        for (var key in gendata[sel.value]) {
            var newfield = document.createElement("p");
            container.appendChild(newfield);
            newfield.innerHTML = key + 
                                 ': <input type="text" name="conf_' + key + '" value="' +
                                 gendata[sel.value][key] + '">';
        }
    });
})();
</script>
<?php } ?>
</body>
</html>
