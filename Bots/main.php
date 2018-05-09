#!/usr/bin/php
<?php
  set_time_limit(0);
  ini_set('default_socket_timeout', 10);
  error_reporting(E_ALL);

  $set_server_status = array();
  $last_server_status = array();

  $ctx = stream_context_create(array(
      'http' => array(
          'timeout' => 50
          )
      )
  );

  $retries = 1;
  $maxretries = 10;

  function createQuery($url, $x=0, $y=0) {
    global $retries, $maxretries, $ch, $timeout, $result;

    $ch=curl_init();
    $timeout=5;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $result=curl_exec($ch);

    while(curl_error($ch) && $retries < $maxretries){
      echo "[".date('Y-m-d H:i:s', time())."] ".curl_error($ch).". Retrying ".$retries."/".$maxretries.PHP_EOL;
      $retries++;
      sleep('1');
      $result = curl_exec($ch);
    }

    $retries = 1;
    curl_close($ch);
    return $result;
  }

  $a = array();

  while(1) {
    $database = mysqli_connect("", "", "");
    mysqli_select_db($database, "");

    $db = mysqli_query($database, "SELECT * FROM `servers` WHERE `fetchdata` = '1'");
    $time = time();

    while($row = mysqli_fetch_array($db, MYSQLI_ASSOC)) {
      $fetch = @createQuery($row['serverIP']."/GetServerInformation", 0, $ctx);

      if(json_decode($fetch, true) != NULL) {
        $onlineNumber = json_decode($fetch, true)['onlineNumber'];
        $regNumber = json_decode($fetch, true)['numberOfRegistered'];

        $country = json_decode($fetch, true)['country'];
        if(json_decode($fetch, true)['requireTicket'] == 1) {
          $requireTicket = 1;
        } else {
          $requireTicket = 0;
        }
        $adminList = json_decode($fetch, true)['adminList'];
        $status = 1;

        $social = array();
        $social['www'] = json_decode($fetch, true)['homePageUrl'];
        $social['fb'] = json_decode($fetch, true)['facebookUrl'];
        $social['discord'] = json_decode($fetch, true)['discordUrl'];

        $resocialize = json_encode($social);
      } else {
        $onlineNumber = 0;
        $regNumber = 0;

        $country = "Unknown";
        $requireTicket = 0;
        $adminList = "Unknown";
        $status = 0;
        $resocialize = NULL;
      }

      $set_server_status[$row['ID']] = $status;

      if(isset($last_server_status[$row['ID']])) {
        if($set_server_status[$row['ID']] != $last_server_status[$row['ID']]) {
          if($status == 1) {
            //system("./send.sh online \"".$row['serverName']."\"");

            //JSON
            $f = fopen("serverstatus.json", "w+");
            $a['status'] = "Online";
            $a['servername'] = $row['serverName'];
            fwrite($f, json_encode($a));
            fclose($f);
          } else {
            //system("./send.sh offline \"".$row['serverName']."\"");

            //JSON
            $f = fopen("serverstatus.json", "w+");
            $a['status'] = "Offline";
            $a['servername'] = $row['serverName'];
            fwrite($f, json_encode($a));
            fclose($f);
          }
        }
      }

      $last_server_status[$row['ID']] = $status;

      mysqli_query($database, "INSERT INTO `analytics` (`serverid`, `usersOnline`, `usersRegistered`, `timestamp`) VALUES ('".$row['ID']."', '".$onlineNumber."', '".$regNumber."', '".$time."')");
      mysqli_query($database, "UPDATE `servers` SET `onlineNumber` = '".$onlineNumber."', `registeredCount` = '".$regNumber."', `country` = '".$country."', `requireTicket` = '".$requireTicket."', `adminList` = '".$adminList."', `isOnline` = '".$status."' WHERE `ID` = '".$row['ID']."'");

      if($row['maxOnline'] < $onlineNumber) {
        mysqli_query($database, "UPDATE `servers` SET `maxOnline` = '".$onlineNumber."', `maxOnlineTimestamp` = ".$time." WHERE `ID` = '".$row['ID']."'");
      }

      if($status == 1) {
        mysqli_query($database, "UPDATE `servers` SET `social` = '".mysqli_real_escape_string($database, $resocialize)."' WHERE `ID` = '".$row['ID']."'");
      }


      //Masking test

      //calculate repeat for spacebar
      $repeattime = 52-(strlen("| Updated ".$row['serverName']))-1;

      echo "[".date('Y-m-d H:i:s', time())."]"; echo "┌".str_repeat("-", 50)."┐".PHP_EOL;
      echo "[".date('Y-m-d H:i:s', time())."]"; echo "| Updated ".$row['serverName'].str_repeat(" ", $repeattime)."|".PHP_EOL;
      echo "[".date('Y-m-d H:i:s', time())."]"; echo "├".str_repeat("-", 50)."┤".PHP_EOL;
      $mask = "| %-36.36s | %9.9s |".PHP_EOL;
      echo "[".date('Y-m-d H:i:s', time())."]"; printf($mask, 'OnlineUsers', $onlineNumber);
      echo "[".date('Y-m-d H:i:s', time())."]"; printf($mask, 'Registered Users', $regNumber);
      echo "[".date('Y-m-d H:i:s', time())."]"; echo "└".str_repeat("-", 50)."┘".PHP_EOL;
      /*echo "[".date('Y-m-d H:i:s', $time)."] Updated ".$row['serverName'].PHP_EOL;
      echo "[".date('Y-m-d H:i:s', $time)."] OnlineUsers: ".$onlineNumber.PHP_EOL;
      echo "[".date('Y-m-d H:i:s', $time)."] RegisteredUsers:".$regNumber.PHP_EOL;*/
    }

    $r = 52-(strlen("| ALL SERVERS HAS BEEN UPDATED."))-1;

    echo "[".date('Y-m-d H:i:s', time())."]"; echo "┌".str_repeat("-", 50)."┐".PHP_EOL;
    echo "[".date('Y-m-d H:i:s', time())."]"; echo "| ALL SERVERS HAS BEEN UPDATED.".str_repeat(" ", $r)."|".PHP_EOL;
    echo "[".date('Y-m-d H:i:s', time())."]"; echo "└".str_repeat("-", 50)."┘".PHP_EOL;

    mysqli_close($database);
    sleep(60);
  }
?>
