<?php
session_start();

$logfile = fopen($_SESSION["logfile"], "a");
fwrite($logfile, "   LOGOUT at ".date('H:i:s')." on ".date('d F Y')."\r\n");
fwrite($logfile, "      Account : ".$_SESSION["user"]."\r\n");
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
fwrite($logfile, "Session End\r\n");
fclose($logfile);

session_destroy();
header("Location: login.php?err=lgo");
?>
