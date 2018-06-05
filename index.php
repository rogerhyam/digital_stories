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
			.running-video{
				background-color: gray !important;
			}
			.running-video a{
				color: white;
			}
			
			#my-video-player{
				width: 100%;	
				margin-top: auto;
			}
			
		</style>
		
		<script type="text/javascript" src="jquery-3.3.1.min.js"></script>

		<script type="text/javascript">
			
			$(document).ready(function(){
				
				// bit of a namespace object
		        var digitalStories = {};
				
				$('#my-video-player').hide();
				digitalStories.currentVideoLink = null;
				
				$('.video-link').on('click', function(){
					var videoLink = $(this);
					digitalStories.selectVideo(videoLink);
					digitalStories.startVideo();
				});
				
				$('#my-video-player').get(0).onended = function() {
				    $('#my-video-player').slideUp('slow');
					$('#menu-list').show();
				};
				
				$('#my-video-player').get(0).onpause = function() {
				    $('#my-video-player').slideUp('slow');
					$('#menu-list').show();
				};
				
				digitalStories.toggleVideo = function(){
					
					// find the one which is highlighted and start it running or stop it
					var player = $('#my-video-player').get(0);
					
					// stop it
					if(player.paused){
						console.log('starting');
						digitalStories.startVideo();
						setTimeout(digitalStories.gameLoop, 1000);
					}else{
						console.log('stopping');
						player.pause();
						$('#my-video-player').slideUp('slow');
						setTimeout(digitalStories.gameLoop, 1000);
					}
					
				}
				
				digitalStories.nextVideo = function(){
					
					var player = $('#my-video-player').get(0);
					player.pause();
					$('#my-video-player').slideUp('slow');
					
					// find the one that is highlighted.
					if($('li.running-video').length > 0){
						
						// get the next one if there is one.
						var next = $('li.running-video').next();
						if(next.length > 0){
							digitalStories.selectVideo(next.find('.video-link'));
						}else{
							// nothing if we don't have another
							// just listen for a button press
							digitalStories.gameLoop();
						}
						
					}else{
						digitalStories.selectVideo($('.video-link').first());
					}
					
				}
				
				digitalStories.previousVideo = function(){
					
					var player = $('#my-video-player').get(0);
					player.pause();
					$('#my-video-player').slideUp('slow');
					
					// find the one that is highlighted.
					if($('li.running-video').length > 0){
						
						// get the next one if there is one.
						var prev = $('li.running-video').prev();
						if(prev.length > 0){
							console.log('got a previous');
							digitalStories.selectVideo(prev.find('.video-link'));
						}else{
							// nothing if we don't have another
							// just listen for a button press
							digitalStories.gameLoop();
						}
						
					}else{
						console.log('going to last');
						digitalStories.selectVideo($('.video-link').last());
					}
				}
				
				digitalStories.selectVideo = function(videoLink){
					
					digitalStories.currentVideoLink = videoLink;
					
					// remove highlights
					$('.video-link').parent().removeClass('running-video');
					videoLink.parent().addClass('running-video');
					
					var file = videoLink.data('video-name');
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
					
					// listen for button presses
					setTimeout(digitalStories.gameLoop, 500);
					
				}
				
				digitalStories.startVideo = function(){
					$('#my-video-player').slideDown('slow');
					$('#menu-list').hide();
					var player = $('#my-video-player').get(0);
					player.play();
				}
				
				
				// game looper to control from joystick
		        digitalStories.gameLoop = function(){
					
					// this must run as a singleton of we get double button presses            
		            var gp = navigator.getGamepads()[0];
            
		            // sometimes it isn't there so we wait an poll again
		            if (!gp){
		                setTimeout(digitalStories.gameLoop, 1000);
		                return;
		            }
            
		            if(gp.buttons[2].value == 1){
		                console.log('button 2 pressed');
						digitalStories.previousVideo();
		            }else if(gp.buttons[1].value == 1){
						console.log('button 1 pressed');
						digitalStories.toggleVideo();
		            }else if(gp.buttons[0].value == 1){
						console.log('button 0 pressed');
		                digitalStories.nextVideo();
		            }else{
		                setTimeout(digitalStories.gameLoop, 100);
		            }
		        }
				
				// kick off the game loop
				setTimeout(digitalStories.nextVideo , 1000);

			});
			
		</script>

	</head>
	<body>


<video id="my-video-player" controls >
  <!-- <source id="my-video-source" type="video/mp4"> -->
</video>
<ul id="menu-list">
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
