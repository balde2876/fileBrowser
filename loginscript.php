<?php
session_start();

$f1 = fopen("users.json", "r") or die("No user file");
$json = fread($f1,filesize("users.json"));
fclose($f1);
$users = json_decode($json,true);

$ipsw = hash('SHA512', $_POST["password"]);
$iusr = strtoupper($_POST["username"]);
//echo ($iusr."//".$ipsw);

if (array_key_exists($iusr, $users)){
	if ($users[$iusr]["password"] == $ipsw) {
		$_SESSION["user"] = $iusr;
		$_SESSION["group"] = $users[$iusr]["group"];
		header("Location: index.php");
	} else {
		header("Location: login.php?err=pwi");
	}
} else {
	header("Location: login.php?err=ane");
}
?>