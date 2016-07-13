<?php
session_start();

$f1 = fopen("users.json", "r") or die("No user file");
$json = fread($f1,filesize("users.json"));
fclose($f1);
$users = json_decode($json,true);

$ipsw = strtoupper(hash('SHA512', $_POST["password"]));
$iusr = strtoupper($_POST["username"]);
//echo ($iusr."//".$ipsw);

if (array_key_exists($iusr, $users)){
	if (strtoupper($users[$iusr]["password"]) == $ipsw) {
		$_SESSION["user"] = $iusr;
		$_SESSION["group"] = $users[$iusr]["group"];
		$cdate = date('d F Y H-i-s');
		$_SESSION["logfile"] = "logs/".$iusr."@".$cdate.".txt";
		$logfile = fopen($_SESSION["logfile"], "w");
		fclose($logfile);

		$logfile = fopen($_SESSION["logfile"], "a");
		fwrite($logfile, "Session Start\r\n");
		fwrite($logfile, "   LOGIN at ".date('H:i:s')." on ".date('d F Y')."\r\n");
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

		header("Location: driveselector.php");
	} else {
		header("Location: login.php?err=pwi");
	}
} else {
	header("Location: login.php?err=ane");
}
?>
