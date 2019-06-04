<!DOCTYPE html>
<html>
    <head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
    </head>
  <body>
    <!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
      
      <div id="playlist-elem">
          <div id="playlist-selector">
              <?php 
                require_once("scripts/mysqliset.php");
                if(!$db) {
	               exit("Verbindungsfehler: ".mysqli_connect_error());
                }
                $stm = "SELECT ID, Playlistname FROM playlists";
                $query = mysqli_query($db, $stm);
                echo "<select id='pllist' onchange='plChanged()'>";
                while($row = mysqli_fetch_object($query)) {
                    echo "<option value='$row->ID'>$row->Playlistname</option>";
                }
                echo "</select>";
              ?>
              </div>
      <div id="playlist-entries">
      </div>
          </div>
      <div id="midframe">
      <div id="player"></div><div id="myProgress">
    <div id="stbut" class="bar">
    <img onclick="deci(0)" id="pause" src="img/play.png" style= "height: 90%; width: 90%;"/>
    </div>
    <p id="tplayed" class="bar time"></p> <p id="ttotal" class="bar time"></p>
    </div>    
    <div id="search"><input type="text" id="searchbar" placeholder="Insert complete Youtube Link..."><button id="searchbtn" onClick="play(0)">Play</button><button id="addbtn" onClick="add()">Add</button></div>
    </div>
    <script>
        
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '450.9375',
          width: '740',
          playerVars: { 'autoplay': 1, 'controls': 0, 'disablekb': 1},
          videoId: 'M7lc1UVf-VE',
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }
        var stop = false;
        function deci(mode) {
            stop = false;
            console.log("deci("+mode+") -> pause("+mode+") and setting stop to false");
            pause(mode);
        }
        function pause(mode) {
                console.log("pause("+mode+")");
                
                console.log("What is pstatus ["+pstatus+"] and mode ["+mode+"]?");
                if(pstatus == 1 && mode == 1) {
                    console.log("pstatus == 1 && mode == 1 -> changing to Pause Icon and player.playVideo() and pstatus = 1 and stop = true");
                    document.getElementById("pause").src = "http://icons.iconarchive.com/icons/icons8/windows-8/128/Media-Controls-Pause-icon.png";
                    player.playVideo();
                    pstatus = 1;
                    stop = true;
                    
                }
                else if(mode == 1) {
                    console.log("pstatus was not 1 and mode == 1 -> changing to Play Icon and player.pauseVideo() and pstatus = 2 and stop = true");
                    document.getElementById("pause").src = "img/play.png";
                    player.pauseVideo();
                    pstatus = 2;
                    stop = true;
                }
                else if(pstatus == 2 && mode == 0) {
                    console.log("pstatus == 2 && mode == 0 -> changing to Pause Icon and player.playVideo() and pstatus = 1 and stop 0 true");
                    document.getElementById("pause").src = "http://icons.iconarchive.com/icons/icons8/windows-8/128/Media-Controls-Pause-icon.png";
                    player.playVideo();
                    pstatus = 1;
                    stop = true;
                }
                else if(mode == 0) {
                    console.log("Statements not valid but mode == 0 -> changing to Play Icon and player.pauseVideo() and pstatus = 2 and stop = true");
                    document.getElementById("pause").src = "img/play.png";
                    player.pauseVideo();
                    pstatus = 2;
                    stop = true;
                }
            }

      // 4. The API will call this function when the video player is ready.
        
        var tot = 0;
      function onPlayerReady(event) {
          console.log("player is now Ready");
          plChanged();
          function totalTime() {
            var dur = player.getDuration();
            var hours = dur/60/60;
            var minutes = (hours - Math.floor(hours)) * 60;
            var seconds = (minutes - Math.floor(minutes)) * 60
            var strTime = ' ' + Math.floor(hours) + ':' + Math.floor(minutes) + ':' + Math.floor(seconds) + ' ';  
            return strTime;
          }
          document.getElementById("ttotal").innerHTML = totalTime();
          var id = 0;
        $( function() {
            function playedTime() {
                var dur = player.getCurrentTime();
                var hours = dur/60/60;
                var minutes = (hours - Math.floor(hours)) * 60;
                var seconds = (minutes - Math.floor(minutes)) * 60
                var strTime = ' ' + Math.floor(hours) + ':' + Math.floor(minutes) + ':' + Math.floor(seconds) + '/';  
                return strTime;
            }
            var select = $( "#player" );
            var slider = $( "<div id='slider'></div>" ).insertAfter( select ).slider({
              range: "min",
              min: 1,
              max: player.getDuration(),
            });
            var status = 0;
            var id = setInterval(frame, 100);
          function frame() {
              if(status == 0) {
                  document.getElementById("tplayed").innerHTML = playedTime();
                  document.getElementById("ttotal").innerHTML = totalTime();
                  slider.slider( "option", "max", player.getDuration() );
                  slider.slider("option", "value", player.getCurrentTime());
              }
            slider.on( "slidestart", function( event, ui ) {
                if(status == 0) {
                    status = 1;
                    console.log("testst");
                }
            }),
            slider.on( "slidestop", function( event, ui ) {
                //Question mark should be 1 or 0?
                if(status == 1) {
                    console.log("slidestop start");
                    slider.slider( "option", "value", ui.value );
                    var value = slider.slider( "option", "value" );
                    player.seekTo(ui.value, true);
                    //Added slider max value change to player.getDuration();
                    console.log("slidestop finish");
                    status = 0;
                }
            });
          };
            var nbar = $( "<div id='noisebar'>" );
            var noise = $( "<div id='noise'></div>" ).insertAfter( select ).slider({
              range: "min",
              min: 1,
              max: 100,
              value: player.getVolume(),
              slide: function(event, ui) {
                  player.setVolume(ui.value);
              }
            })
            var statusnoise = 0;
            var id = setInterval(frame, 100);
          function framer() {
              if(statusnoise == 0) {
            noise.slider("value", player.getVolume());
              }
            noise.on( "slidestart", function( event, ui ) {
                statusnoise = 1;
                console.log("testst");
                
            }),
            noise.on( "slidestop", function( event, ui ) {
                statusnoise = 0;
                console.log("teststop");
                
            });
          };
        console.log("just before .playVideo()");
        
      });
      }
      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
      function onPlayerStateChange(event) {
          console.log("Player State changed");
        console.log("Is event.data = PLAYING && done=false? Hint: event.data: "+event.data+", PLAYING: "+YT.PlayerState.PLAYING);
        if (event.data == YT.PlayerState.PLAYING && !done) {
            console.log("It is! -> setTimeout(stopVideo, 900) & done = true");
          setTimeout(stopVideo, 900);
          done = true;
        }
          console.log("What is event.data´s value? Hint: Value is: "+event.data);
        if(event.data == 0 || event.data == -1) {
            console.log("It was 0 or -1! -> pstatus = 1 and deci(1)");
            pstatus = 1;
            deci(1);
        }
        else if(event.data != pstatus && event.data != 5 && event.data != 3) {
            console.log("It was != pstatus [Hint: "+pstatus+"] and it was != 5 and != 3 -> pstatus = event.data and deci(1)");
            pstatus = event.data;
            deci(1);
        }
          else if(event.data == 5 || event.data == 3) {
              console.log("It was 5 or 3 -> do nothing");
          }
      }
      function stopVideo() {
          console.log("stopVideo() -> player.stopVideo() and pstatus = 2");
        player.stopVideo();
          pstatus = 2;
      }
      function play(vidid) {
          console.log("play");
          //Applied changes
          if(vidid == 0) {
              var searchlink = document.getElementById("searchbar").value;
              vidid = searchlink.substr(32);
          }
          player.loadVideoById(vidid, 0, "default");
          document.getElementById("ttotal").innerHTML = totalTime();
          
      }
        function add() {
            var plelem = document.getElementById("pllist");
            var addlink = document.getElementById("searchbar").value;
            var vidid = addlink.substr(32);
            var selplid = plelem.options[plelem.selectedIndex].value;
            $.get( "scripts/addEntries.php", { vidid: vidid, selplid: selplid })
            .done(function( data ) {
                alert(data);
                $('#playlist-entries').load('scripts/listEntries.php?selplid='+selplid);
            });
        }
        function plChanged() {
            var plelem = document.getElementById("pllist");
            var passVar = plelem.options[plelem.selectedIndex].value;
                  //Selected Playlist ID selplid;
            $('#playlist-entries').load('scripts/listEntries.php?selplid='+passVar);
            
            }
      </script>
    </body>
</html>
