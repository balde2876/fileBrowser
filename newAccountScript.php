<?php
session_start();

$f1 = fopen("users.json", "r") or die("No user file");
$json = fread($f1,filesize("users.json"));
fclose($f1);
$users = json_decode($json,true);

if ($_POST["password"] != $_POST["password2"]) {
	header("Location: newAccount.php?err=pnm");
	die();
}

$ipsw = hash('SHA512', $_POST["password"]);
$iusr = strtoupper($_POST["username"]);
// Remove anything which isn't a word, whitespace, number
// or any of the following caracters -_~,;[]().
// If you don't need to handle multi-byte characters
// you can use preg_replace rather than mb_ereg_replace
// Thanks @Łukasz Rysiak!
$iusr = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $iusr);
// Remove any runs of periods (thanks falstro!)
$iusr = mb_ereg_replace("([\.]{2,})", '', $iusr);
//echo ($iusr."//".$ipsw);

if (array_key_exists($iusr, $users)){
	header("Location: newAccount.php?err=aae");
} else {
	$_SESSION["user"] = $iusr;
	$_SESSION["group"] = "USERS";
	$cdate = date('d F Y H-i-s');
	$_SESSION["logfile"] = "logs/".$iusr."@".$cdate.".txt";
	$logfile = fopen($_SESSION["logfile"], "w");
	fclose($logfile);

	$logfile = fopen($_SESSION["logfile"], "a");
	fwrite($logfile, "Session Start\r\n");
	fwrite($logfile, "   ACCOUNT CREATE at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	fwrite($logfile, "      Account : ".$iusr."\r\n");
	fwrite($logfile, "      Group : ".$_SESSION["group"]."\r\n");
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		fwrite($logfile, "      HTTP Proxy IP : ".$_SERVER['HTTP_X_FORWARDED_FOR']."\r\n");
	}
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		fwrite($logfile, "      HTTP Client IP : ".$_SERVER['HTTP_CLIENT_IP']."\r\n");
	}
	if (!empty($_SERVER['REMOTE_ADDR'])) {
		fwrite($logfile, "      Client IP : ".$_SERVER['REMOTE_ADDR']."\r\n");
	}
	fwrite($logfile, "      Session ID : ".session_id()."\r\n");
	fclose($logfile);
	
	$users[$iusr] = [
		"password" => $ipsw,
		"group" => "USERS",
	];
	
	$fp = fopen("users.json", "w");
	fwrite($fp, json_encode($users, JSON_PRETTY_PRINT));
	fclose($fp);


	header("Location: driveselector.php");
}
?>
