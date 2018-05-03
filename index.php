<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<title>RBGE Digital Stories</title>
		<link rel="icon" href="favicon.png">
		<link rel="apple-touch-icon" href="favicon.png">
		<style type="text/css" media="screen">
			body{
				margin: 0px;
				font-family: Arial;
			}
			ul{
				border-top: 1px solid gray;
				padding-left: 0px;
				margin-top: 0px;
				list-style-type: none;
			}
			li{
				overflow: hidden;
				border-bottom: 1px solid gray;
			}
			li img{ 
				height: 100px;
				width: 100px;
				float:left;
				margin-right: 1em;
				
			}
			a {
				text-decoration: none;
				color: gray;
			}
		</style>
		
		<script type="text/javascript" src="jquery-3.3.1.min.js"></script>

		<script type="text/javascript">
			
			$(function() {
			
				$('#my-video-player').hide();
				
				
				$('.video-link').on('click', function(){
					
					var file = $(this).data('video-name');
					var player = $('#my-video-player').get(0);
					
					// stop it
					player.pause();
					
					// remove the source
					$('#my-video-player').empty();
					
					// add a new source
					var new_source = $('<source></source>');
					new_source.attr('type', 'video/mp4');
					new_source.attr('src', file);
					$('#my-video-player').append(new_source);
					
					// start it up
					player.load();
					$('#my-video-player').slideDown('slow');
					player.play();
					
				});
				
				$('#my-video-player').get(0).onended = function() {
				    $('#my-video-player').slideUp('slow');
				};

			});
			
		</script>

	</head>
	<body>


<video id="my-video-player" width="100%" controls >
  <!-- <source id="my-video-source" type="video/mp4"> -->
</video>

<ul>
<?php 
$videos = glob("videos/*.mp4");

foreach($videos as $video){
    echo "<li><a href=\"#\" class=\"video-link\" data-video-name=\"$video\">";
	echo "<img src=\"$video.jpg\" />";
	
	$meta = file("$video.txt");
	echo "<h3>".$meta[0]."</h3>";
	echo "<p>".$meta[1]."</p>"; 
}
?>
</ul>


	</body>
	
</html>
