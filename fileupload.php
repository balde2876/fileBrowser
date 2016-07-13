<?php
session_start();

$file = str_replace("/","\\",str_replace(array("///aphe///","///quot///","///equa///","///ques///","///amps///","///fstp///"),array("'",'"',"=","?","&","."),$_POST['path']."/".$_FILES["file1"]["name"]));
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

$fileName = $_FILES["file1"]["name"]; // The file name
$fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["file1"]["type"]; // The type of file it is
$fileSize = $_FILES["file1"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
if (!$fileTmpLoc) { // if file not chosen
    echo "SCBF04";
    exit();
}
echo $fileTmpLoc;
if (in_array("WRITE",$permissions)){
  $logfile = fopen($_SESSION["logfile"], "a");
  fwrite($logfile, "   UPLOAD ( WRITE ) at ".date('H:i:s')." on ".date('d F Y')."\r\n");
  fwrite($logfile, "      File : ".$file."\r\n");
  fclose($logfile);
  rename($_FILES["file1"]["tmp_name"],$file);
} else {
  $logfile = fopen($_SESSION["logfile"], "a");
  fwrite($logfile, "   DENIED UPLOAD ( WRITE ) at ".date('H:i:s')." on ".date('d F Y')."\r\n");
  fwrite($logfile, "      File : ".$file."\r\n");
  fclose($logfile);
  unlink($_FILES["file1"]["tmp_name"]);
}
?>
