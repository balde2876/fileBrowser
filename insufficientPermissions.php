<?php
session_start();
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
#uploadButton {
  -webkit-border-radius: 64;
  -moz-border-radius: 64;
  display:inline-block;
  border-radius: 64px;
  box-shadow: 0px 2px 12px -3px #444444;
  -webkit-transition-duration: 0.2s;
  transition-duration: 0.2s;
  -webkit-transition-property: box-shadow, transform;
  transition-property: box-shadow, transform;
}
#uploadButton:hover {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
  box-shadow: 0px 5px 25px -5px #444444;
}
</style>

</head>
<body>

<div class="material" id="jss001" style="width:calc(100% - 68px);background-color: #ffffff; position:absolute; top: 10px; left:10px; overflow: hidden; z-index:1;
padding-left:24px;padding-top:86px;padding-bottom:52px;padding-right:24px;
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
<?php
if (isset($_SESSION['user'])){
} else {
header("Location: login.php");
die();
}

echo "<a style='position:relative;top:-10px;height:36px;display:inline;padding-right:5px;' href='driveselector.php'>
<h3 style='color:#ffffff;position:relative;top:17px;height:36px;display:inline;'>Cannot Download File</h3></a>";
echo '</div></div>';
echo "<h3>Insurficcient permissions as current user</h3>";
echo '<div style="width:100%;background-color: #eeeeee; position:absolute;
bottom: 0px; z-index:0; left: 0px; height: 32px;">
<p style="color:#444444;position:absolute;top:4px;left:64px;">Logged in as account '.$_SESSION['user'].' in group '.$_SESSION['group'].' </p><a style="color:#444444;position:absolute;right:50px;width:50px;top:4px;" href="logoutscript.php">Logout</a></div>';

?>

</div>
</body>
</html>
