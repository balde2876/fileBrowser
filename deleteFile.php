<?php
session_start();
$file = str_replace("/","\\",str_replace(array("///aphe///","///quot///","///equa///","///ques///","///amps///","///fstp///"),array("'",'"',"=","?","&","."),$_GET['path']));
$f1 = fopen("groups.json", "r") or die("No groups file");
$json = fread($f1,filesize("groups.json"));
fclose($f1);
$groupSettings = json_decode($json,true)[$_SESSION["group"]];
$allPermissions = $groupSettings['permissions'];
$permissions = [];

$breadcrumbs = explode("\\", $file);
$i = 1;
$temp1 = "";
foreach ($breadcrumbs as &$value) {
	$temp1 = $temp1.$value."/";
	if (array_key_exists($temp1, $allPermissions)) {
		$permissions = $allPermissions[$temp1];
	}
	$i = $i + 1;
}
//echo json_encode($permissions);
if (in_array("DELETE",$permissions)){
	$logfile = fopen($_SESSION["logfile"], "a");
	fwrite($logfile, "   DELETE at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	fwrite($logfile, "      File : ".$file."\r\n");
	fclose($logfile);
	//echo $file;
	if (file_exists($file)) {
		unlink($file);
		if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
		exit;
	}
} else {
	$logfile = fopen($_SESSION["logfile"], "a");
	fwrite($logfile, "   DENIED DELETE at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	fwrite($logfile, "      File : ".$file."\r\n");
	fclose($logfile);
	header('Location: insufficientPermissions.php');
	exit;
}
?>
