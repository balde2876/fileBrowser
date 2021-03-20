<?php
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>File Browser</title>
<link rel="icon" type="image/png" href="icon.png">
<script src="//code.jquery.com/jquery-1.12.0.js"></script>
<meta id="viewport" name="viewport" content="width=500">
<style>
@import url(styleSheet.css);
</style>

</head>
<body>

<div class="material" id="jss001" style="width:calc(100% - 108px);background-color: #ffffff; position:absolute; top: 10px; left:10px; overflow: hidden; z-index:1;
padding-left:64px;padding-top:122px;padding-bottom:28px;padding-right:24px;
margin-bottom:10px;
">
<div style="width:100%;background-color: #444444; position:absolute;
top: 0px; z-index:0; height:67px; left: 0px;
">
<div style="height:36px;position:absolute;
top: 0px; z-index:0; left: 0px;
padding-left:24px;padding-top:12px;padding-bottom:19px;padding-right:24px;
margin-bottom:0px;
">
<h3 style='color:#ffffff;position:relative;top:5px;height:36px;display:inline;padding-right:5px;'>New Account</h3>
</div></div>
<div style="width:100%;background-color: #eeeeee; position:absolute;top: 67px; z-index:0; left: 0px; height: 32px;">
</div>
<form action="newAccountScript.php" method="POST" style="max-width:400px;padding-right:38px;">
<input class="field" style="position:relative; width:100%;height: 25px;" type="text" name="username" id="username" placeholder="Username"></input>
<input class="field" style="position:relative; width:100%;height: 25px;" type="password" name="password" id="password" placeholder="Password"></input>
<input class="field" style="position:relative; width:100%;height: 25px;" type="password" name="password2" id="password2" placeholder="Password Again"></input>
<input id="button1" style="width: 0.1px;height: 0.1px;opacity: 0;overflow: hidden;position: absolute;z-index: -1;" type="submit"></input>
<label for="button1"><div class="btn" style="position:relative;width:100%; height: 25px;">Create account</div></label>
</form>
<img style='position:absolute;top:126px;left:24px;width:32px;height:32px;' src='img/id.png'></img>
</br>
<?php
if (isset($_GET['err'])){
	if ($_GET['err'] == "pnm"){
		echo "<h3>Passwords don't match</h3></br>";
	}
	if ($_GET['err'] == "aae"){
		echo "<h3>Account already exists</h3></br>";
	}
}
?>
<a href="login.php">Login to existing account</a>
</div>
</body>
</html>
