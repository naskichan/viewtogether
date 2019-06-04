<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="resource/jquery-ui.css">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>

  <script src="resource/jquery-1.12.4.js"></script>
  <script src="resource/jquery-ui.js"></script>
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
                ?> <select id='pllist' onchange='plChanged(0)'> <?php
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
    <div id="search"><input type="text" id="searchbar" placeholder="Insert complete Youtube Link..."><button id="searchbtn" class="asbtn" onClick="play(0, 1)">Play</button><button id="addbtn" class="asbtn" onClick="add()">Add</button></div>
    </div>
    <div id="openHam" onclick="openNav()">☰</div>
    <div id="hambar" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav('hambar')">&times;</a>
        <a href="javascript:void(0)" onclick="newPlaylist()">Create New Playlist</a>
        <a href="#">Services</a>
        <a href="#">Clients</a>
        <a href="#">Contact</a>
</div>
              <div id="newPlaylistModal" class="modal" onclick="closeModal('newPlaylistModal')">
                  <div class="modal-content">
                  <div id="plclosebtn" class="closebtn" onclick="closeModal('newPlaylistModal')">&times;</div>
                  <input class="input" type="text" placeholder="Write Playlistname here">
                  <button class="btn" onclick="display('import')">Import</button>
                  <input class="input" type="text" placeholder="Insert Youtube Playlist Link here" style="display: none">
                  <div id="newPlaylistList">
                  <?php 
                  require_once("scripts/mysqliset.php");
                if(!$db) {
	               exit("Verbindungsfehler: ".mysqli_connect_error());
                }
                $stm = "SELECT ID, Playlistname FROM playlists";
                $query = mysqli_query($db, $stm);
                while($row = mysqli_fetch_object($query)) {
                    echo "<div class='plentry' value='$row->ID'>$row->Playlistname</div>";
                }
                  ?>
                      </div>
                      
                      <div id="createpl" class="btn" onclick="createPlaylist()">Create</div>
                  </div>
              </div>
          <div id="comFace">
            <div id="userList">
              </div>
              <div id="chat"></div>
          </div>
    <script>
        var PAUSE = 2;
        var PLAY = 1;
        var NOTSTARTED = -1;
        var ENDED = 0;
        var LOADING = 3;
        var sendState = 0;
        function getSystemTime() {
            var date = new Date();
            return date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
        }
        function openNav() {
            document.getElementById("hambar").style.width = "250px";
        }
        function closeNav(eid) {
         document.getElementById(eid).style.width = "0";   
        }
        function closeModal(eid) {
            document.getElementById(eid).style.display = "none";
        }
        function newPlaylist() {
            document.getElementById("newPlaylistModal").style.display = "block";
        }
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
          playerVars: { 'autoplay': 0, 'controls': 0, 'disablekb': 0},
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }
        function deci(deciMode) {
            pause(deciMode);
        }
        
        var holdsync = 0;
        function sendsync(sendSyncMode, sendSyncData) {
            console.log("["+getSystemTime()+"] Alert! sendsync started in mode "+sendSyncMode+", so");
            if(curplid == "") {
                curplid = player.getVideoUrl().substr(32);
            }
            if(sendSyncMode == 0) {
                 console.log("["+getSystemTime()+"] Im sending vidid: "+sendSyncData);
                $.get( "scripts/sendSync.php", { vidid: sendSyncData })
                .done(function( data ) {
                     console.log("Im done sending vidid");
                });
            }
            else if(sendSyncMode == 1) {
                console.log("["+getSystemTime()+"] Im sending selplid: "+sendSyncData);
                $.get( "scripts/sendSync.php", { selplid: sendSyncData })
                .done(function( data ) {
                    console.log("Im done sending selplid");
                    holdsync = 0;
                });
            }
            else if(sendSyncMode == 3) {
                console.log("["+getSystemTime()+"] Im sending Status: "+sendSyncData);
                $.get( "scripts/sendSync.php", { status: sendSyncData })
                .done(function( data ) {
                    console.log("Im done sending pstatus");
                    holdsync = 0;
                });
            }
            else if(sendSyncMode == 2) {
                console.log("["+getSystemTime()+"] Im sending time: "+sendSyncData);
                $.get( "scripts/sendSync.php", { time: sendSyncData })
                .done(function( data ) {
                    console.log("Im done sending time");
                    time = data;
                    holdsync = 0;
                });
            }
            else if(sendSyncMode == 4) {
                console.log("["+getSystemTime()+"] Im sending change");
                $.get( "scripts/sendSync.php", { change: sendSyncData })
                .done(function( data ) {
                    console.log("Im done sending change");
                    sendsync(2, player.getCurrentTime());
                });
            }
            
        }
        function pause(pauseMode, pauseState) {
            var d = new Date();
            
                if(pauseState == PLAY && pauseMode == 1) {
                    console.log("["+getSystemTime()+"] System interaction [mode 1] -> playing");
                    document.getElementById("pause").src = "http://icons.iconarchive.com/icons/icons8/windows-8/128/Media-Controls-Pause-icon.png";
                    holdsync = 1;
                    sendsync(3, PLAY);
                    
                }
                else if(pauseState == PAUSE && pauseMode == 1) {
                    console.log("["+getSystemTime()+"] System interaction [mode 1] -> pausing");
                    document.getElementById("pause").src = "img/play.png";
                    holdsync = 1;
                    sendsync(3, PAUSE);
                }
                else if(pauseMode == 0 && pstatus != PAUSE) {
                    console.log("["+getSystemTime()+"] User Interaction [mode 0] -> pausing");
                    document.getElementById("pause").src = "img/play.png";
                    sendState = 1;
                    holdsync = 1;
                    player.pauseVideo();
                    sendsync(3, PAUSE);
                }
                else if(pauseMode == 0 && pstatus != PLAY) {
                    console.log("["+getSystemTime()+"] User Interaction [mode 0] -> playing");
                    document.getElementById("pause").src = "http://icons.iconarchive.com/icons/icons8/windows-8/128/Media-Controls-Pause-icon.png";
                    sendState = 1;
                    holdsync = 1;
                    player.playVideo();
                    sendsync(3, PLAY);
                }
            //Watch out! Inconsistency
                else if(pauseState == PAUSE && pauseMode == 2 && pstatus != PAUSE) {
                    console.log("["+getSystemTime()+"] Server said I shall pause");
                    document.getElementById("pause").src = "img/play.png";
                    sendState = 1;
                    player.pauseVideo();
                }
                else if(pauseState == PLAY && pauseMode == 2 && pstatus != PLAY) {
                    console.log("["+getSystemTime()+"] Server said I shall play");
                    document.getElementById("pause").src = "http://icons.iconarchive.com/icons/icons8/windows-8/128/Media-Controls-Pause-icon.png";
                    sendState = 1;
                    console.log("player.playVideo");
                    player.playVideo();
                }
        }

      // 4. The API will call this function when the video player is ready.
        
        var tot = 0;
        function totalTime() {
            var dur = player.getDuration();
            var hours = dur/60/60;
            var minutes = (hours - Math.floor(hours)) * 60;
            var seconds = (minutes - Math.floor(minutes)) * 60;
            if(Math.floor(hours) < 10) {
                  hours = "0"+Math.floor(hours);
              } else {
                  hours = Math.floor(hours);
              }
              if(Math.floor(minutes < 10)) {
                  minutes = "0"+Math.floor(minutes);
              } else {
                  minutes = Math.floor(minutes);
              }
              if(Math.floor(seconds < 10)) {
                  seconds = "0"+Math.floor(seconds);
              } else {
                  seconds = Math.floor(seconds);
              }
            var strTime = ' ' + hours + ':' + minutes + ':' + seconds + ' ';  
            return strTime;
          }
          function playedTime(dur) {
                var hours = dur/60/60;
                var minutes = (hours - Math.floor(hours)) * 60;
                var seconds = (minutes - Math.floor(minutes)) * 60
                if(Math.floor(hours) < 10) {
                  hours = "0"+Math.floor(hours);
              } else {
                  hours = Math.floor(hours);
              }
              if(Math.floor(minutes < 10)) {
                  minutes = "0"+Math.floor(minutes);
              } else {
                  minutes = Math.floor(minutes);
              }
              if(Math.floor(seconds < 10)) {
                  seconds = "0"+Math.floor(seconds);
              } else {
                  seconds = Math.floor(seconds);
              }
                var strTime = ' ' + hours + ':' + minutes + ':' + seconds + '/';  
                return strTime;
            }
      function onPlayerReady(event) {
          console.log("Player is now ready!");
           document.getElementById("ttotal").innerHTML = totalTime();
          login("guest");
          var id = 0;
          var time = 0;
        $( function() {
            
            
            var cookievol = 0;
            
            if(document.cookie.split(";")[0].length < 8) {
                cookievol = parseInt(document.cookie.split(";")[0].substr(4));
                console.log("Cookie: "+document.cookie);
                console.log("cookievol: "+cookievol);
                player.setVolume(cookievol);
            }
            
            var select = $( "#player" );
            var slider = $( "<div id='slider'></div>" ).insertAfter( select ).slider({
              range: "min",
              min: 1,
              max: player.getDuration(),
              slide: function(event, ui) {
                  slidevalue = slider.slider( "option", "value" );
                  console.log(slidevalue);
                  document.getElementById("tplayed").innerHTML = playedTime(slidevalue);
                  status = 1;
                  
              }
            });
            var status = 0;
            var id = setInterval(frame, 100);
          function frame() {
              if(status == 0) {
                  slidevalue = player.getCurrentTime();
                  document.getElementById("tplayed").innerHTML = playedTime(slidevalue);
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
                    slidevalue = slider.slider( "option", "value" );
                    player.seekTo(ui.value, true);
                    sendsync(2, ui.value);
                    //Added slider max value change to player.getDuration();
                    console.log("slidestop finish");
                    status = 0;
                }
            });
          };
            var nbar = $( "<div id='noisebar'>" );
            var noise = $( "<div id='noise'></div>" ).insertAfter( select ).slider({
              range: "min",
              min: 0,
              max: 100,
              value: cookievol,
              slide: function(event, ui) {
                  player.setVolume(ui.value);
                  
              }
            })
            //Note: Is not syncing anymore
            var statusnoise = 0;
            console.log("About to start getsync");
            var count = setInterval(getsync, 300);
            var id = setInterval(framer, 100);
          function framer() {
              if(statusnoise == 0) {
                  noise.slider("value", player.getVolume());
                  document.cookie = "vol="+noise.slider("option", "value");
              }
            noise.on( "slidestart", function( event, ui ) {
                statusnoise = 1;
                
            }),
            noise.on( "slidestop", function( event, ui ) {
                statusnoise = 0;
                
            });
          };
            var firstTime = 0;
            
            // This function launches every second
            // When called for the first time, get Synced with everything
            // (Warning: Not implemented yet) When update is given, update^^
            function getsync() {
                //This stops the asynchronous process of syncipng until it shall be resumed (0 = go, 1 = stop)
                
                $.get( "scripts/getSynced.php")
            .done(function( data ) {
                    var arr = data.split(";");
                    if(firstTime == 0) {
                        console.log("["+getSystemTime()+"] Syncing for the first Time!");
                        console.log("curplid: "+arr[1]+", selplid: "+arr[2]+", time: "+arr[3]+", pstatus: "+arr[4]);
                        time = arr[3];
                        firstTime = 1;
                        play(arr[1], 0, time);
                        pause(2, arr[4]);
                        listUsers();
                        plChanged(1, arr[2]);
                    }
                    else {
                        if(arr[1] != curplid) {
                            console.log("["+getSystemTime()+"] Server said update video to id: "+arr[1]);
                            console.log("curplid is: "+curplid+" so Ill do it");
                            play(arr[1], 0);
                        }
                        else if(arr[2] != selplid) {
                            console.log("["+getSystemTime()+"] Server said change selected playlist to: "+arr[2]);
                            console.log("It is "+selplid+" now, so Ill do it");
                            plChanged(1, arr[2]);
                        }
                        else if(arr[3] != time && holdsync == 0 && player.getCurrentTime() > 0) {
                            console.log("["+getSystemTime()+"] Server said Im not in sync, syncing to: "+arr[3]+" from "+time);
                            player.seekTo(arr[3]);
                            time = arr[3];
                        }
                        else if(arr[4] != pstatus && (pstatus != 3 && pstatus != 0) && holdsync == 0) {
                            console.log("["+getSystemTime()+"] Server said Status changed, syncing to: "+arr[4]+" from "+pstatus);
                            pause(2, arr[4]);
                        }
                        else if(arr[5] != 0) {
                            console.log("Change detected");
                            $('#playlist-entries').load('scripts/listEntries.php?selplid='+selplid);
                            holdsync = 1;
                            sendsync(4, 0);
                            
                        }
                        else if(holdsync == 1) {
                            console.log("["+getSystemTime()+"] Server said update, but Im ignoring him ;)");
                        }
                        listUsers();
                        
                    }
            });
                
            }
        
      });
      }
      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      var done = false;
        var pstatus = 0;
      function onPlayerStateChange(event) {
          console.log("Event data"+event.data);
          
        if(event.data == 0) {
            // Ended
            // Set pstatus to event status (0 or -1)
            
            nextEntry(curplid);
            
        }
          else if(event.data == 3) {
              player.playVideo();
          }
          else if(event.data == -1) {
              
          }
          else if(event.data == 5) {
              console.log("Video cued, starting");
              pause(2, PLAY);
              // Setting pstatus to 4 will cause the server to update the status.
              // 4 is just a value that isn´t used
              pstatus = 4;
              
          } else if(sendState == 0 && (event.data == PLAY || event.data == PAUSE)) {
                console.log("sendState was 0, setting pstatus to: "+event.data+" and exec pause(1, "+event.data+")");
                pstatus = event.data;
                pause(1, event.data);
            }
            else if(sendState == 1) {
                console.log("sendState was 1. Command executed");
                pstatus = event.data;
                sendState = 0;
            }
            
      }
        var curplid = "";
        // Mode 0: Server
        // Mode 1: User
      function play(vidid, playMode, startTime) {
          console.log("["+getSystemTime()+"] Function play("+vidid+", "+playMode+", "+startTime+")");
          //Applied changes
          //Setting previous Playlist Object to deselected
          try {
                document.getElementById(curplid).style.backgroundColor = "#eee";
              
            } catch(err) {
                
            }
          //Is Vidid given?
          if(vidid == 0) {
              console.log("vidid was 0")
              var searchlink = document.getElementById("searchbar").value;
              var searcharr = searchlink.substring(32).split("&");
              
              vidid = searcharr[0];
              console.log("Now it´s "+vidid);
          }
          // Set current playid to new playid
          // Is the User giving this order?
          if(playMode == 1) {
              console.log("["+getSystemTime()+"] The User has given me the order to play, so Ill sync");
              //Send new played Video to Server
              sendsync(0, vidid);
              holdsync = 1;
              sendsync(2, 0);
          }
          sendState = 1;
          player.loadVideoById(vidid, startTime, "default");
          console.log("["+getSystemTime()+"] Loading video "+vidid+" at "+startTime);
          document.getElementById("ttotal").innerHTML = totalTime();
          console.log("["+getSystemTime()+"] curplid was "+curplid+". Setting to "+vidid);
          curplid = vidid;
          try {
              document.getElementById(curplid).style.backgroundColor = "darkgrey";
          } catch(err) {
              
          }
      }
        var selplid;
        function add() {
            var plelem = document.getElementById("pllist");
            var addlink = document.getElementById("searchbar").value;
            var vidid = addlink.substr(32);
            $.get( "scripts/addEntries.php", { vidid: vidid, selplid: selplid })
            .done(function( data ) {
                holdsync = 1;
                sendsync(4, 1);
            });
        }
        var UID ="guest";
        function login(username) {
            console.log("login("+username+") was called");
            $.get( "scripts/login.php", { username: username })
                .done(function( data ) {
                console.log("Im now logged in. data: "+data);
                UID = data;
                holdsync = 1;
                sendsync(4, 1);
                });
        }
        $(window).bind("beforeunload", function() {
        holdsync = 1;
        $.get( "scripts/logout.php", { username: UID, time: player.getCurrentTime()})
                .done(function( data ) {
                sendSync(4, 1);
                holdsync = 0;
                });
            
        });
        function listUsers() {
            $('#userList').load('scripts/listUsers.php');
        }
        function plChanged(plChangedMode, plChangedData) {
            console.log("plChanged("+plChangedMode+", "+plChangedData+") was called");
            if(plChangedMode == 0) {
                holdsync = 1;
                console.log("User called to Update Selected Playlist");
                selplid = document.getElementById("pllist").options[document.getElementById("pllist").selectedIndex].value;
                sendsync(1, selplid);
            } else if(plChangedMode != 0) {
                console.log("plelem.options["+plChangedData+"].value is "+document.getElementById("pllist").options[plChangedData-1].value);
                selplid = document.getElementById("pllist").options[plChangedData-1].value;
            }
                  //Selected Playlist ID selplid;
            $('#playlist-entries').load('scripts/listEntries.php?selplid='+selplid);
            
            }
        function delentry(id) {
            $.get( "scripts/delEntries.php", { id: id })
            .done(function( data ) {
                $('#playlist-entries').load('scripts/listEntries.php?selplid='+selplid);
            });
        }
        function nextEntry(curplid) {
            console.log("["+getSystemTime()+"] nextEntry("+curplid+")");
            // curplid: Id of currently played (selected) video
            $.get( "scripts/nextEntry.php", { selplid: selplid, curplid: curplid })
            .done(function( data ) {
                    console.log("nextEntry: "+data);
                    play(data, 1);
            });
        }
      </script>
    </body>
</html>
