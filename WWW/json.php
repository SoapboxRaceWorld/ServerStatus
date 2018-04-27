<?php
	$db = mysqli_connect("", "", "");
	mysqli_select_db($db, "");
	header("Content-type: application/json");

	$jsonarray = array();
	$shorter = array();

	$q = mysqli_query($db, "SELECT * FROM `servers` WHERE `fetchData` = 1 ORDER BY `sortID` ASC");

	while($x = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
		if($_GET['s'] == NULL) {
			$x['info'] = str_replace(array("<b>", "</b>"), "**", strip_tags($x['info'], "<b>"));
			$x['requireTicket'] = (bool)$x['requireTicket'];

			$x['fetchdata'] = (int)$x['fetchdata'];
			$x['numberOfRegistered'] = (int)$x['numberOfRegistered'];
			$x['onlineNumber'] = (int)$x['onlineNumber'];
			$x['status'] = (bool)$x['status'];
			$x['sort'] = (int)$x['sort'];
			$x['id'] = (int)$x['id'];
			$x['social'] = json_decode($x['social'], true);
		}

		$jsonarray[] = $x;
	}

	echo json_encode($jsonarray);
?>
