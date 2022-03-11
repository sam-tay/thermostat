 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>
<link rel="stylesheet" href="w3_styles.css">
<meta charset="utf-8" http-equiv="refresh" content="5;index.php" name="mobile-web-app-capable" content="yes"/> <!-- 5 is the refresh rate -->
</head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Define php scripts to run -->
<?php
$outside_temp = file_get_contents("variables/outside_temp.sam");
$outside_hum = file_get_contents("variables/outside_hum.sam");
$uv_ind = file_get_contents("variables/uv_ind.sam");
$forecast = file_get_contents("variables/forecast.sam");
$set_temp = file_get_contents("variables/temp_setting.sam");
$fan_mode = file_get_contents("variables/fan_mode.sam");
$curr_temp = file_get_contents("variables/curr_temp.sam");
$curr_hum = file_get_contents("variables/curr_hum.sam");
$curr_mode = file_get_contents("variables/curr_mode.sam");
$errors = file_get_contents("variables/errors.sam");
$schedule_status = file_get_contents("variables/schedule_status.sam");
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
<div class="w3-row" style="height:57vh" >
        <div class="w3-col m4 w3-center" style="max-height:100%">
		<p id="ddate" style="font-size:4vh; line-height:0vh;"><?php echo date("D M d, Y") ?></p>
		<p id="ttime" style="font-size:8vh; line-height:0vh;"><?php echo date("h:i a") ?></p>
		<img id="Wimg" src="test_cock.jpg" alt="Weather" style="max-width:100%; height:22vh;" align="top">
		<p id="OutTemp" style="font-size:6vh; line-height:0vh;"><?php echo $outside_temp  ?>&deg | <?php echo $outside_hum ?> % RoH</p>
		<p id="UVind" style="font-size:4vh; line-height:0vh;"><?php echo $uv_ind ?> UV Index</p>
        </div>
        <div class="w3-col m4 w3-center" style="height:100%">
		<p style="font-size:4vh; line-height:0vh;" align="left"> Indoor Temp: </p>
		<p id="currentTemp" style="font-size:18vh; line-height:0vh;"><b> <?php echo $curr_temp ?>&deg </b></p>
		<p style="font-size:4vh; line-height:0vh;" align="left"> Indoor Hum: </p>
		<p id="currentHum" style="font-size:8vh; line-height:0vh;"> <?php echo $curr_hum ?>% </p>
		
        </div>
        <div class="w3-col m4 w3-center" style="height:100%">
		<div class="w3-row" style="height:20vh">
			<a href="RaiseTemp.php">
				<img src="up-arrow.png" alt="up temp" style="height:20vh; width:20vw;" align="top" class="w3-button">
			</a>
		</div>
		<div class="w3-row" style="height:17vh">
			<p style="font-size:3vh; line-height:3vh; margin:0em"> Set to: </p>
			<p id="setTemp" style="font-size:15vh; line-height:15vh; margin:0em"><?php echo $set_temp ?>&deg </p>
		</div>
		<div class="w3-row" style="height:20vh">
			<a href="LowerTemp.php">
				<img src="down-arrow.png" alt="down temp" style="height:20vh; width:20vw;" align="top" class="w3-button">
			</a>
		</div>
        </div>
</div>

<!-- Define lower section -->
<div class="w3-row" style="height:25vh">
        <div class="w3-col m4 w3-center w3-border w3-border-red" style="height:100%">
        	<div class="w3-row w3-cell-top w3-left" style="height:5vh">
			<p style="font-size:4vh; line-height:4vh; padding:0px; vertical-align:top; margin-top:0em;"> System </p>
		</div>
		<div class="w3-row w3-center" style="height:20vh">
			<div class="w3-col m4 w3-center">
                		<a id="HEAT" href="StatusHeat.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="height:20vh; font-size:3vw; line-height:20vh"> Heat </a>
			</div>
                        <div class="w3-col m4 w3-center">
                		<a id="COOL" href="StatusCool.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="height:20vh; font-size:3vw; line-height:20vh"> Cool </a>
                        </div>
                        <div class="w3-col m4 w3-center">
                		<a id="OFF" href="StatusOff.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="height:20vh; font-size:3vw; line-height:20vh"> Off </a>
                        </div>
		</div>
	</div>
        <div class="w3-col m4 w3-center w3-border w3-border-red" style="height:100%">
        	<div class="w3-row w3-cell-top w3-left" style="height:5vh">
			<p style="font-size:4vh; line-height:4vh; padding:0px; vertical-align:top; margin-top:0em;"> Fan </p>
                </div>
		<div class="w3-row w3-center" style="height:20vh">
                        <div class="w3-col m6 w3-center">
                		<a id="AUTO" href="StatusAuto.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="height:20vh; font-size:4vw; line-height:20vh"> Auto </a>
                        </div>
                        <div class="w3-col m6 w3-center">
                			<a id="ON" href="StatusOn.php" class="w3-button w3-black w3-block w3-border w3-border-blue" style="height:20vh; font-size:4vw; line-height:20vh"> On </a>
                        </div>
                </div>
        </div>
        <div class="w3-col m4 w3-center w3-border w3-border-red" style="height:100%">
        	<div class="w3-row w3-cell-top w3-left" style="height:5vh">
				<p style="font-size:4vh; line-height:4vh; padding:0px; vertical-align:top; margin-top:0em;"> Status </p>
            </div>
                <div class="w3-row" style="height:20vh">
                	<p style="font-size:2vw; line-height:3vh; vertical-aligh:top;"><?php echo $schedule_status ?></p>
                	<p style="font-size:2vw; line-height:3vh; vertical-aligh:top; color:red"><?php echo $errors ?></p>
				</div>
        </div>
</div>

<!-- insert from scripts.js the if statement for image selection. will run through switch case with php variable -->
<script type="text/javascript">
	var forecast = "<?php echo $forecast ?>";
	var f_img = "";
	switch ( forecast ) {
		case "sun":
			f_img = "sunny.png";
			break;
		case "cloud":
			f_img = "cloudy.png";
			break;
		case "rain":
			f_img = "rainy.png";
			break;
		case "snow":
			f_img = "snowy.png";
			break;
		case "sleet":
			f_img = "haily.png";
			break;
		default:
			f_img = "test_cock.jpg";
		};
	document.getElementById("Wimg").src = f_img;

	//Define if statements for changing classes of variables
	var status = "<?php echo $curr_mode ?>";
	switch( status ) {
			case "heat":
				//code to change color of button
				document.getElementById('HEAT').className = "w3-button w3-green w3-block w3-border w3-border-blue";
				break;
			case "cool":
				document.getElementById('COOL').className = "w3-button w3-green w3-block w3-border w3-border-blue";
				break;
			default:
				document.getElementById('OFF').className = "w3-button w3-green w3-block w3-border w3-border-blue";	
		};
		var fan_status = "<?php echo $fan_mode ?>";
		switch( fan_status ) {
			case "on":
				//code to change color of button
				document.getElementById('ON').className = "w3-button w3-green w3-block w3-border w3-border-blue";
				break;
			default:
				document.getElementById('AUTO').className = "w3-button w3-green w3-block w3-border w3-border-blue";	
		};
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

<!-- This script will redirect phones to mobile site. It is hacky and will not allow access to desktop site (even if "request desktop site it enabled") -->
<script type="text/javascript">

if (screen.width <= 699) {
document.location = "mobile.php";
}

</script>

</body>
</html> 
