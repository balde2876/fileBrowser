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
if (in_array("READ",$permissions)){
	//echo $file;
	$logfile = fopen($_SESSION["logfile"], "a");
	fwrite($logfile, "   READ at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	fwrite($logfile, "      File : ".$file."\r\n");
	fclose($logfile);

	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		exit;
	}
} else {
	$logfile = fopen($_SESSION["logfile"], "a");
	fwrite($logfile, "   DENIED READ at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	fwrite($logfile, "      File : ".$file."\r\n");
	fclose($logfile);
	header('Location: insufficientPermissions.php');
	exit;
}
?>
