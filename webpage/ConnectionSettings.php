<?php 
	session_start();
?>
 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>
<link rel="stylesheet" href="w3_styles.css">
<meta charset="utf-8" />
</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body style="background-color:Black">

<form action="ChangeWifi.php" method="post" >
	<p style="font-size:8vh; line-height:0vh; padding:0px; vertical-align:top;" align="left"> Wifi Name: </p>
	<input name="SSID" type="text" id="wifi_name" style="font-size:8vh; padding:0px; margin-top:0px; max-width:70%; max-height:100%;" value=""> 
	<p style="font-size:8vh; line-height:0vh; padding:0px; vertical-align:top;" align="left"> Wifi Password: </p>
	<input name="Pass" type="password" id="wifi_pass" style="font-size:8vh; padding:0px; margin-top:0px; max-width:100%; max-height:100%;" value=""> <br>
	<input type="submit" value="Connect" style="font-size:8vh">
</form>
<p style="font-size:3vh"> Note: This will change the wifi of thermostat (not the tablet). After submitting, you will need to change the tablet wifi to the new wifi and will need to determine the new IP address of the thermostat to access it. </p>

</body>
</html> 