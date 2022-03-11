 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>
<link rel="stylesheet" href="w3_styles.css">
<meta charset="utf-8" />
</head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Define php scripts to run -->
<?php
$uri = $_SERVER['REQUEST_URI']; // will be / or /index.php for all requests
?>


<body style="background-color:Black">
<!-- Define top row -->
<!-- for adjusting heights of things, use style="height:%%vh" but will not resize text. Can also use style="font-size:%%vh" for shit :) -->
<div class="w3-row m4" style="height:15vh">
	<div class="w3-col m4 w3-center">
		<a id="HOME" href="index.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:10vh"> Home </a>
	</div>
	<div class="w3-col m4 w3-center">
		<a id="SCHEDULE" href="schedule.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:10vh"> Schedule </a>
	</div>
	<div class="w3-col m4 w3-center">
		<a id="MENU" href="menu.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:10vh"> Menu </a>
	</div>
</div>

<!-- Define middle section -->
<div class="w3-row" style="height:85vh" >
	<div class="w3-row">
		<a id="Restart_server" href="RestartServer.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:9vh"> Restart Internet Server </a>
	</div>
	<div class="w3-row">
		<a id="Refresh_weather" href="RefreshWeather.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:9vh"> Refresh Weather Data </a>
	</div>
	<div class="w3-row">
		<a id="Restart_Thermostat" href="RestartThermostat.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:9vh"> Restart Thermostat Program </a>
	</div>
	<div class="w3-row">
		<a id="Restart_pi" href="RestartPi.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:9vh"> Restart Pi </a>
	</div>
	<div class="w3-row">
		<a id="Internet_connect" href="ConnectionSettings.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:9vh"> Change Network Settings </a>
	</div>
</div>
	

<script type="text/javascript">
	var page = "<?php echo $uri ?>";
	switch( page ) {
			case "/schedule.php":
				document.getElementById('SCHEDULE').className = "w3-button w3-green w3-block w3-border w3-border-blue";
				break;
			case "/menu.php":
				document.getElementById('MENU').className = "w3-button w3-green w3-block w3-border w3-border-blue";
				break;
			default:
				document.getElementById('HOME').className = "w3-button w3-green w3-block w3-border w3-border-blue";
	};
</script>
</body>
</html> 
