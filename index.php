<?php
session_start();
if($_SERVER['SERVER_PORT'] != '443') { header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); exit(); }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>File Browser</title>
<link rel="icon" type="image/png" href="icon.png">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
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
<body oncontextmenu="return false">

<div class="material" id="jss001" style="width:calc(100% - 68px);background-color: #ffffff; position:absolute; top: 10px; left:10px; overflow: hidden; z-index:1;
padding-left:24px;padding-top:122px;padding-bottom:52px;padding-right:24px;
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

//This function was taken from https://sourceforge.net/projects/soloadmin/
//User:                        https://sourceforge.net/u/ucl/profile/
function filesize64($file)
{
    static $iswin;
    if (!isset($iswin)) {
        $iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
    }

    static $exec_works;
    if (!isset($exec_works)) {
        $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
    }

    // try a shell command
    if ($exec_works) {
        $cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : "stat -c%s \"$file\"";
        @exec($cmd, $output);
        if (is_array($output) && ctype_digit($size = trim(implode("\n", $output)))) {
            return $size;
        }
    }

    // try the Windows COM interface
    if ($iswin && class_exists("COM")) {
        try {
            $fsobj = new COM('Scripting.FileSystemObject');
            $f = $fsobj->GetFile( realpath($file) );
            $size = $f->Size;
        } catch (Exception $e) {
            $size = null;
        }
        if (ctype_digit($size)) {
            return $size;
        }
    }

    // if all else fails
    return filesize($file);
}
//This function was taken from http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
//Author:                      Jeffrey Sambells
function human_filesize($bytes, $dec = 2)
{
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

$f1 = fopen("config.json", "r") or die("No config file");
$json = fread($f1,filesize("config.json"));
fclose($f1);
$settings = json_decode($json,true);
$f1 = fopen("groups.json", "r") or die("No groups file");
$json = fread($f1,filesize("groups.json"));
fclose($f1);
$groupSettings = json_decode($json,true)[$_SESSION["group"]];
$allPermissions = $groupSettings['permissions'];
$permissions = [];


//
$icons = $settings['icons'];
$fileExtensions = $settings['fileExtensions'];
$iconImageRoot = $settings['iconImageRoot'];
$defaultIcon = $settings['defaultIcon'];
$folderIcon = $settings['folderIcon'];
$dir = "";
if (isset($_GET['f'])){
$dir = str_replace(array("///fstp///","/"),array(".","\\"),$_GET['f']);
} else {
$dir = "";
}
$breadcrumbs = explode("\\", str_replace("/","\\",$dir));
$i = 1;
$temp1 = "";
echo "<a style='position:relative;top:-10px;height:36px;display:inline;padding-right:5px;' href='driveselector.php'><h3 style='color:#ffffff;position:relative;top:2px;height:36px;display:inline;'>Drives<img src='img/breadcrumb.png' style='position:relative;top:11px;left:5px;height:36px;display:inline;'></img></h3></a>";
foreach ($breadcrumbs as &$value) {
	$value1 = $value;
	$end = "";
	if ($i < count($breadcrumbs)){
		echo "<a style='position:relative;top:-10px;height:36px;display:inline;padding-right:5px;' href='index.php?f=".implode("\\", array_slice($breadcrumbs,0,$i)).$end."'><h3 style='color:#ffffff;position:relative;top:2px;height:36px;display:inline;'>".$value1."<img src='img/breadcrumb.png' style='position:relative;top:11px;left:5px;height:36px;display:inline;'></img>";
	} else {
		echo "<a style='position:relative;top:5px;height:36px;display:inline;padding-right:5px;' href='index.php?f=".implode("\\", array_slice($breadcrumbs,0,$i)).$end."'><h3 style='color:#ffffff;position:relative;top:2px;height:36px;display:inline;'>".$value1;
	}
	echo "</h3></a>";
	$temp1 = $temp1.$value1."/";
	if (array_key_exists($temp1, $allPermissions)) {
		$permissions = $allPermissions[$temp1];
	}
	$i = $i + 1;
}

echo "<p id='curDir' hidden>".$dir."</p>";
echo "<p id='groupPermissionsJson' hidden>".json_encode($permissions)."</p>";
//echo "<p id='groupPermissionsJson' hidden>".$permissions."</p>";
echo '</div></div><div style="width:100%;background-color: #eeeeee; position:absolute;
top: 67px; z-index:0; left: 0px; height: 32px;">
<p style="color:#444444;position:absolute;top:4px;left:64px;">Name</p><p style="color:#444444;position:absolute;right:50px;width:50px;top:4px;">Size</p></div>';
if (in_array("LIST",$permissions)){

  $logfile = fopen($_SESSION["logfile"], "a");
  fwrite($logfile, "   LIST at ".date('H:i:s')." on ".date('d F Y')."\r\n");
  fwrite($logfile, "      Directory : ".$dir."\r\n");
  fclose($logfile);

	$files1 = scandir($dir);
	$fid = 0;
	foreach ($files1 as &$value) {
		$filetype = "file";
		$filepathpieces = explode("\\", $value);
		$filepieces = explode(".", end($filepathpieces));
		$icon = $defaultIcon;
		$fileSize = "";
		if (array_key_exists(end($filepieces), $fileExtensions)) {
			$icon = $icons[$fileExtensions[end($filepieces)]];
		}
		if (is_dir($dir."\\".$value)){
			$icon = $folderIcon;
			$filetype = "folder";
		} else {
			$desthref = "getFile.php?path=".$dir."\\".$value;
			//$fileSizeRaw = filesize($driveAccessRoot.$_GET['filepos']."\\".$value);
			$fileSize = human_filesize(filesize64($dir."\\".$value));
		}
		$actFile = str_replace(array("'",'"',"=","?","&","."),array("///aphe///","///quot///","///equa///","///ques///","///amps///","///fstp///"),str_replace("\\","/",$dir."/".$value));
		if (($value == ".") or ($value == "..")){

		} else {
			echo "<div style='position:relative;top:0px;left:0px;width:100%;height:32px;'>";
			echo "<a id='link".$fid."' onclick='clickAction(".'"'.$actFile.'","'.$filetype.'"'.")' oncontextmenu='cMenu(".'"'.$actFile.'",'.$fid.',"'.$filetype.'","'.$value.'"'.");return false;' style='position:absolute;top:5px;left:40px;'>".$value."</a>";
			echo "<img style='position:absolute;top:0px;left:0px;width:32px;height:32px;' src='".$iconImageRoot.$icon."'></img>";
			echo "<p style='color:#444444;position:absolute;right:26px;width:50px;top:4px;'>".$fileSize."</p>";
			echo "</div>";
		}
		$fid = $fid + 1;
	}


} else {
  $logfile = fopen($_SESSION["logfile"], "a");
  fwrite($logfile, "   DENIED LIST at ".date('H:i:s')." on ".date('d F Y')."\r\n");
  fwrite($logfile, "      Directory : ".$dir."\r\n");
  fclose($logfile);

	echo "<h3>You do not have list permissions for this folder</h3>";
}
echo '<div style="width:100%;background-color: #eeeeee; position:absolute;
bottom: 0px; z-index:0; left: 0px; height: 32px;">
<p style="color:#444444;position:absolute;top:4px;left:64px;">Logged in as account '.$_SESSION['user'].' in group '.$_SESSION['group'].' </p><a style="color:#444444;position:absolute;right:50px;width:50px;top:4px;" href="logoutscript.php">Logout</a></div>';
?>
</div>

<div id="renameFileMenu" class="material" style="position:absolute;top:100px;left:20px;width:0px;height:0px;z-index:50;background-color:#ffffff;">
  <input style="position:absolute;margin-top:0px;top:5px;left:5px;width:150px;" id="renameFileMenuInput" value="meme" class="field"></input>
</div>

<div id="contextualMenu" class="material" style="position:absolute;top:100px;left:20px;width:0px;height:0px;z-index:50;background-color:#ffffff;">
	<a id="cMenu1" href=""><div id="dcMenu1" style="position:absolute;top:0px;left:0px;width:100%;height:48px;background-color:#ffffff;color:#000000;">
		<p style="position:absolute;top:12px;left:16px;">Download</p>
		<div style="position:absolute;top:47px;left:0px;width:100%;height:1px;background-color:#dddddd;"></div>
	</div></a>
	<a id="cMenu2"><div id="dcMenu2" style="position:absolute;top:48px;left:0px;width:100%;height:48px;background-color:#eeeeee;color:#888888;">
		<p style="position:absolute;top:12px;left:16px;">Rename</p>
		<div style="position:absolute;top:47px;left:0px;width:100%;height:1px;background-color:#dddddd;"></div>
	</div></a>
	<a id="cMenu3" href=""><div id="dcMenu3" style="position:absolute;top:96px;left:0px;width:100%;height:48px;background-color:#eeeeee;color:#888888;">
		<p style="position:absolute;top:12px;left:16px;">Copy</p>
		<div style="position:absolute;top:47px;left:0px;width:100%;height:1px;background-color:#dddddd;"></div>
	</div></a>
	<a id="cMenu4" href=""><div id="dcMenu4" style="position:absolute;top:144px;left:0px;width:100%;height:48px;background-color:#eeeeee;color:#888888;">
		<p style="position:absolute;top:12px;left:16px;">Paste</p>
		<div style="position:absolute;top:47px;left:0px;width:100%;height:1px;background-color:#dddddd;"></div>
	</div></a>
	<a id="cMenu5" href=""><div id="dcMenu5" style="position:absolute;top:192px;left:0px;width:100%;height:48px;background-color:#eeeeee;color:#888888;">
		<p style="position:absolute;top:12px;left:16px;">Delete</p>
		<div style="position:absolute;top:47px;left:0px;width:100%;height:1px;background-color:#dddddd;"></div>
	</div></a>
</div>

<style>
#filedrag
{
	display: none;
	font-weight: bold;
	text-align: center;
	padding: 1em 0;
	margin: 1em 0;
	color: #555;
	border: 2px dashed #555;
	border-radius: 7px;
	cursor: default;
}

#filedrag.hover
{
	color: #f00;
	border-color: #f00;
	border-style: solid;
	box-shadow: inset 0 3px 4px #888;
}
</style>

<div id="dropzone" class="materialInset" style="display: none;position:absolute;top:10px;left:10px;width:100px;height:100px;z-index:60;">
  <form id="upload_form" style="position:absolute;top:45px;left:45px;z-index:5;" enctype="multipart/form-data" method="post" style="height:90px;margin-bottom:40px;">
    <input style="width: 100px;height: 30px;opacity: 0;overflow: hidden;position: absolute;" type="file" name="file1" id="file1"></input>
  </form>
  <img src="img/upload.png" style="position:absolute;top:20px;left:20px;height:60px;width:60px;z-index:3;"></img>
  <div id="progressBarBitJs" style="position:absolute;top:0px;left:0px;width:100px;height:0px;background-color:#88ff00;z-index:1;"></div>
</div>

<script>
document.addEventListener("click", CloseCMenu);
px = 20;
py = 20;
$( document ).on( "mousemove", function( event ) {
	px = event.pageX;
	py = event.pageY;
});
permissions = JSON.parse($("#groupPermissionsJson").text())

for (i = 0; i < permissions.length; i++) {
    console.log(permissions[i]);
}

var dragTimer;
$("#dropzone").hide();
$(document).on('dragover', function(e) {
    var dt = e.originalEvent.dataTransfer;
    if(dt.types != null && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('application/x-moz-file'))) {
        $("#dropzone").show();
        window.clearTimeout(dragTimer);
    }
    e = e || window.event;
    var posX = e.originalEvent.pageX;
    var posY = e.originalEvent.pageY;
    $("#dropzone").css({top: posY - 50, left: posX - 50});
});
$(document).on('dragleave', function(e) {
    dragTimer = window.setTimeout(function() {
        $("#dropzone").hide();
    }, 25);
});

$("#file1").on('change',function(){
    uploadFile();
});

function _(el){
	return document.getElementById(el);
}
function uploadFile(){
	var file = _("file1").files[0];
	var formdata = new FormData();
	formdata.append("file1", file);
  formdata.append("path", $("#curDir").text());
	var ajax = new XMLHttpRequest();
	ajax.upload.addEventListener("progress", progressHandler, false);
	ajax.addEventListener("load", completeHandler, false);
	ajax.addEventListener("error", errorHandler, false);
	ajax.addEventListener("abort", abortHandler, false);
	ajax.open("POST", "fileupload.php");
	ajax.send(formdata);
}
function progressHandler(event){
	//_("loaded_n_total").innerHTML = "Uploaded "+event.loaded+" bytes of "+event.total;
  $("#dropzone").show();
  window.clearTimeout(dragTimer);
  var percent = (event.loaded / event.total) * 100;
  _("progressBarBitJs").style.top = (100 - ((event.loaded / event.total)*100)) + "px";
	_("progressBarBitJs").style.height = ((event.loaded / event.total)*100) + "px";
  _("progressBarBitJs").style.backgroundColor = "#88ff00";
	//console.log(Math.round(percent)+"% uploaded... please wait");
}
function completeHandler(event){
  $("#dropzone").hide();
	console.log(event.target.responseText);
	_("progressBarBitJs").style.height = "0px";
  location.reload();
}
function errorHandler(event){
	_("progressBarBitJs").style.height = "100px";
	_("progressBarBitJs").style.top = "0px";
  _("progressBarBitJs").style.backgroundColor = "#FF2E00";
  window.setTimeout(function() {
      $("#dropzone").hide();
  }, 2000);
}
function abortHandler(event){
	_("progressBarBitJs").style.height = "100px";
  _("progressBarBitJs").style.top = "0px";
  _("progressBarBitJs").style.backgroundColor = "#FF2E00";
  window.setTimeout(function() {
      $("#dropzone").hide();
  }, 2000);
}

function isInArray(value, array) {
  return array.indexOf(value) > -1;
}

function clickAction(path,filetype){
	if (filetype == "folder"){
		window.location = "index.php?f=" + path.replace("/", "\\");
	} else {
		window.location = "getFile.php?path=" + path.replace("/", "\\");
	}
}

$("#renameFileMenuInput").keyup(function(event){
    if(event.keyCode == 13){
        CloseRFMenu();
    }
});

rfpath = "";

function renameFile(path,file){
  $("#renameFileMenuInput").val(file);
  rfpath = path;
  //alert(rfpath);
  $("#renameFileMenu").css('height',"0px");
  $("#renameFileMenu").css('width',"0px");
  $("#renameFileMenu").css('top', py + "px");
  $("#renameFileMenu").css('left', px + "px");
  if ((py+48)>($(window).height() + $(document).scrollTop())){
		$("#renameFileMenu").animate({height: '48px',width: '200px',top: ((py-48) +'px')},300);
	} else {
		$("#renameFileMenu").animate({height: '48px',width: '200px'},300);
	}
}

function cMenu(path,fid,filetype,file){
	//$("#contextualMenu").animate({top: '240px'});
	$("#contextualMenu").css('height',"0px");
	$("#contextualMenu").css('width',"0px");
	$("#contextualMenu").css('top', py + "px");
	$("#contextualMenu").css('left', px + "px");
  $("#dcMenu1").css('background-color',"#eeeeee");
  $("#dcMenu1").css('color',"#888888");
	$("#dcMenu2").css('background-color',"#eeeeee");
	$("#dcMenu2").css('color',"#888888");
	$("#dcMenu3").css('background-color',"#eeeeee");
	$("#dcMenu3").css('color',"#888888");
	$("#dcMenu4").css('background-color',"#eeeeee");
	$("#dcMenu4").css('color',"#888888");
	$("#dcMenu5").css('background-color',"#eeeeee");
	$("#dcMenu5").css('color',"#888888");
  $("#cMenu1").attr("href", "");
  $("#cMenu2").attr("onclick", "");
  $("#cMenu5").attr("href", "");
	//alert(filetype);
	if (filetype == "folder"){
		if (isInArray("DELETEFOLDER", permissions)) {
			$("#dcMenu5").css('background-color',"#ffffff");
			$("#dcMenu5").css('color',"#000000");
      $("#cMenu5").attr("href", "deleteFolder.php?path=" + path.replace("/", "\\"));
		}
    if (isInArray("RENAME", permissions)) {
			$("#dcMenu2").css('background-color',"#ffffff");
			$("#dcMenu2").css('color',"#000000");
      $("#cMenu2").attr("onclick", "renameFile('" + path.replace("\\", "/") + "','" + file.replace("/", "\\") + "')");
		}
	} else {
    if (isInArray("RENAME", permissions)) {
      $("#dcMenu2").css('background-color',"#ffffff");
      $("#dcMenu2").css('color',"#000000");
      $("#cMenu2").attr("onclick", "renameFile('" + path.replace("\\", "/") + "','" + file.replace("/", "\\") + "')");
    }
    if (isInArray("READ", permissions)) {
      $("#dcMenu1").css('background-color',"#ffffff");
  		$("#dcMenu1").css('color',"#000000");
      $("#cMenu1").attr("href", "getFile.php?path=" + path.replace("/", "\\"));
    }
		if (isInArray("DELETE", permissions)) {
			$("#dcMenu5").css('background-color',"#ffffff");
			$("#dcMenu5").css('color',"#000000");
      $("#cMenu5").attr("href", "deleteFile.php?path=" + path.replace("/", "\\"));
		}
	}
	if ((py+240)>($(window).height() + $(document).scrollTop())){
		$("#contextualMenu").animate({height: '240px',width: '200px',top: ((py-240) +'px')},300);
	} else {
		$("#contextualMenu").animate({height: '240px',width: '200px'},300);
	}
	//"getFile.php?path="
}
function CloseCMenu(){
	$("#contextualMenu").animate({height: '0px',width: '0px'},300);
}
function CloseRFMenu(){
	$("#renameFileMenu").animate({height: '0px',width: '0px'},300);
  packedinput = $("#renameFileMenuInput").val().replace(".", "///fstp///").replace("&", "///amps///").replace("'", "///aphe///").replace('"', "///quot///");
  packedpath = rfpath;
  window.location="renameFile.php?rename=" + packedinput + "&path=" + packedpath;
}
</script>

</body>
</html>
