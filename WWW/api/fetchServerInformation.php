<?php
  $db = mysqli_connect("", "", "");
  mysqli_select_db($db, "");

  $q = mysqli_query($db, "SELECT * FROM `servers`");
  while($x = mysqli_fetch_array($q)) {
    $jsnames[] = $x['serverName'];
  }

  echo $_GET['callback']."([";
  if(in_array($_GET['name'], $jsnames)) {
    $query = mysqli_query($db, "SELECT * FROM `servers` WHERE `serverName` = '".mysqli_real_escape_string($db, $_GET['name'])."'");
    $id = mysqli_fetch_array($query)['ID'];
    $query = mysqli_query($db, "SELECT * FROM `analytics` WHERE `serverid` = '".$id."'");

    while($row = mysqli_fetch_array($query)) {
      echo "[".$row['timestamp']."000,".$row['usersOnline']."],";
    }
  } else {
    echo "Unknown server";
  }

  echo "])";
?>
