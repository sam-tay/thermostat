<?php 
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>

<?php
	$name = $_POST('SSID');
	$pass = $_POST('Pass');
	$wifi_card = 'wlan0'; //This will need to be changed to reflect the pi
	$cmd = 'wpa_cli -i wlan0 reconfigure';
	$wpa1 = 'ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n';
	$wpa2 = 'update_config=1\n';
	$wpa3 = 'country=US\n';
	$wpa4 = '\n';
	$wpa5 = 'network={\n';
	$wpa6 = '	ssid="' . $name . '"\n';
	$wpa7 = '	psk="' . $pass . '"\n';
	$wpa8 = '	key_mgmt=WPA-PSK\n';
	$wpa9 = '}';
	$wpa_file = fopen('/etc/wpa_supplicant/wpa_supplicant.conf','w');
	$wpa = $wpa1 . $wpa2 . $wpa3 . $wpa4 . $wpa5 . $wpa6 . $wpa7 . $wpa8 . $wpa9;
	fwrite( $wpa_file , $wpa );
	fclose( $wpa_file );

	//Check functionalititty of this
	$out = shell_exec($cmd); //These dont appear to work. Test on pi

	//Do actually by modifying the wpa_supplicant file: https://weworkweplay.com/play/automatically-connect-a-raspberry-pi-to-a-wifi-network/
?>

<link rel="stylesheet" href="w3_styles.css">
<header name = "Access-Control-Allow-Origin" value = "*" />
<meta charset="utf-8" />
</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body style="background-color:Black">
</body>
</html> 