<?php
require_once('includes/admin-functions.php');

if(isset($_GET['fileid'])) {
	$tmp = base64_decode($_GET['fileid']);
	$title = "";
	if (strpos($tmp, '||') === false)
		$file = $tmp;
	else
		list($file, $title, $pluginurl) = explode("||", $tmp);
	
	if(empty($file) || !$file)
		die ('Unknown file.');
}else
	die ();
	
$fileinfo = pathinfo($file);
if($fileinfo['dirname']==".") 
	die("Unknown file.");

$ext = substr(strrchr($file,'.'),1)
?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />

<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="skin/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){

	$("#jquery_jplayer_1").jPlayer({
		ready: function (event) {
			$(this).jPlayer("setMedia", {
				<?php echo $ext; ?>:"<?php echo $file?>"
			}).jPlayer("play");
		},
		swfPath: "js",
		supplied: "<?php echo $ext; ?>",
		wmode: "window"
	});
	$.jPlayer.timeFormat.showHour = true;
	$.jPlayer.timeFormat.padHour = true;
	
});
//]]>
</script>
</head>
<body>

		<div id="jquery_jplayer_1" class="jp-jplayer"></div>

		<div id="jp_container_1" class="jp-audio">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<ul class="jp-controls">
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
					</ul>
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-volume-bar">
						<div class="jp-volume-bar-value"></div>
					</div>
					<div class="jp-time-holder">
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>

						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
						</ul>
					</div>
				</div>
				<div class="jp-title">
					<ul>
						<li><?php echo $title; ?></li>
						<li><?php echo get_downloadhtml($file, "Download Audio", "", $pluginurl); ?></li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>
</body>

</html>