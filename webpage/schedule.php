<?php 
	session_start();
?>
 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>
<link rel="stylesheet" href="w3_styles.css"/>
<meta charset="utf-8" />
</head>

<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<!-- Define php scripts to run -->
<?php
//session_start();
//if( empty( $_COOKIE['weeknum'])) {
	//$weeknum = file_get_contents("variables/schedule.sam");
//	$weeknum = 5;
//	setcookie('weeknum', $weeknum, time()+3600);
	//$_SESSION['weeknum'] = $weeknum;
//};

//2 is cooling schedule, 1 is heating schedule, 0 is undefined and will load defaults. This is the tag @ end of url /schedule.php?heat=XXX
// add an isset()??
if (isset( $_GET["heat"] ) ) {
	//variable is already set by url, use 1 or 2 for loading
	if ($_GET["heat"]==1) {
		$current_file = "variables/heat_schedule.sam";
		$sched_type = "heat";
	} else {
		$current_file = "variables/cool_schedule.sam";
		$sched_type = "cool";
	}
} else {
	//use the current mode to determine which schedule to open
	$curr_mode = file_get_contents("variables/curr_mode.sam");
	switch ($curr_mode) {
		case "cool":
			$current_file = "variables/cool_schedule.sam";
			$sched_type = "cool";
			break;
		default:
			//make adjusting the heat schedule the default
			$current_file = "variables/heat_schedule.sam";
			$sched_type = "heat";
	};
}
$outside_temp = file_get_contents("variables/outside_temp.sam");
$outside_hum = file_get_contents("variables/outside_hum.sam");
$uv_ind = file_get_contents("variables/uv_ind.sam");
$forecast = file_get_contents("variables/forecast.sam");
$set_temp = file_get_contents("variables/temp_setting.sam");
$fan_mode = file_get_contents("variables/fan_mode.sam");
$curr_temp = file_get_contents("variables/curr_temp.sam");
$curr_hum = file_get_contents("variables/curr_hum.sam");

$schedule = file_get_contents($current_file);
//Schedule is in format: #weeknum(start;finish;temp;start;finish;temp)#endnum(start;finish;temp)
parse_str( $schedule , $output );
$weeknum = $output["weeknum"];
$endnum = $output['endnum'];
for ($x = 0; $x < $weeknum ; $x++) {
	//try assigning to an array instead of individual variables
	//$str1 = '$weekstarttime' . $x . ' = $output["weekstarttime' . $x . '"];';
	//$str2 = '$weekstoptime' . $x . ' = $output["weekstoptime' . $x . '"];';
	//$str3 = '$weektemp' . $x . ' = $output["weektemp' . $x . '"];';
	$str1 = '$weekstarttime[' . $x . '] = $output["weekstarttime' . $x . '"];';
	$str2 = '$weekstoptime[' . $x . '] = $output["weekstoptime' . $x . '"];';
	$str3 = '$weektemp[' . $x . '] = $output["weektemp' . $x . '"];';
	eval($str1);
	eval($str2);
	eval($str3);
};
for ($x = 0; $x < $endnum ; $x++) {
	$str1 = '$endstarttime[' . $x . '] = $output["endstarttime' . $x . '"];';
	$str2 = '$endstoptime[' . $x . '] = $output["endstoptime' . $x . '"];';
	$str3 = '$endtemp[' . $x . '] = $output["endtemp' . $x . '"];';
	eval($str1);
	eval($str2);
	eval($str3);
};
$errors = file_get_contents("variables/errors.sam");
$schedule_status = file_get_contents("variables/schedule_status.sam");
$uri = $_SERVER['REQUEST_URI']; // will be / or /index.php for all requests
$uri_parts = explode('?', $uri , 2);
$uri = $uri_parts[0];
$i = 0;
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
<form action="Save_Schedule.php" method="post">
<div class="w3-row" style="height:70vh" >
		<div class="w3-row" style="height:35vh">
			<div class="w3-col m6 w3-center">
				<p style="font-size:8vh; line-height:0vh; padding:0px; vertical-align:top;" align="left"> Weekdays </p>
				<p style="font-size:3vh; line-height:0vh; padding:0px; vertical-align:top;" align="left"> Number of daily divisions: </p>
				<div class="w3-row" style="height:13vh" >
					<div class="w3-col m4 w3-center" style="height:100%">
							<input name="weeknumber_post" type="text" id="weeknumber" style="font-size:9vh; padding:0px; margin-top:0px; max-width:100%; max-height:100%;" value="2">
					</div>
					<div class="w3-col m4 w3-center" style="height:100%">
						<button onclick="UpWeek();return false;" class="w3-button w3-teal w3-border w3-border-blue" style="width:100%; height:100%;">+</button>
					</div>
					<div class="w3-col m4 w3-center" style="height:100%">
						<button onclick="DownWeek();return false;" class="w3-button w3-teal w3-border w3-border-blue" style="width:100%; height:100%;">-</button>
					</div>
				</div>
			</div>
			<div class="w3-col m6 w3-center">
				<div class="w3-row" style="height:5vh">
					<div class="w3-col s4 w3-center" style="height:100%">
						<p style="font-size:3vh; padding:0px; margin-top:0px;">Start</p>
					</div>
					<div class="w3-col s4 w3-center" style="height:100%">
						<p style="font-size:3vh; padding:0px; margin-top:0px;">Stop</p>
					</div>
					<div class="w3-col s4 w3-center" style="height:100%">
						<p style="font-size:3vh; padding:0px; margin-top:0px;">Temp</p>
					</div>
				</div>
				<div class="w3-row"  id="weekdaysplits">
				</div>
			</div>
		</div>
		<div class="w3-row" style="height:35vh">
			<div class="w3-col m6 w3-center">
				<p style="font-size:8vh; line-height:0vh; padding:0px; vertical-align:top;" align="left"> WeekEnds </p>
				<p style="font-size:3vh; line-height:0vh; padding:0px; vertical-align:top;" align="left"> Number of daily divisions: </p>
				<div class="w3-row" style="height:13vh" >
					<div class="w3-col m4 w3-center" style="height:100%">
						<input name="endnumber_post" type="text" id="endnumber" style="font-size:9vh; padding:0px; margin-top:0px; max-width:100%; max-height:100%;" value="3">
					</div>
					<div class="w3-col m4 w3-center" style="height:100%">
						<button onclick="UpEnd();return false;" class="w3-button w3-teal w3-border w3-border-blue" style="width:100%; height:100%;">+</button>
					</div>
					<div class="w3-col m4 w3-center" style="height:100%">
						<button onclick="DownEnd();return false;" class="w3-button w3-teal w3-border w3-border-blue" style="width:100%; height:100%;">-</button>
					</div>
				</div>
			</div>
			<div class="w3-col m6 w3-center">
				<div class="w3-row" style="height:5vh">
					<div class="w3-col s4 w3-center" style="height:100%">
						<p style="font-size:3vh; padding:0px; margin-top:0px;">Start</p>
					</div>
					<div class="w3-col s4 w3-center" style="height:100%">
						<p style="font-size:3vh; padding:0px; margin-top:0px;">Stop</p>
					</div>
					<div class="w3-col s4 w3-center" style="height:100%">
						<p style="font-size:3vh; padding:0px; margin-top:0px;">Temp</p>
					</div>
				</div>
				<div class="w3-row"  id="weekendsplits">
				</div>
			</div>
		</div>
</div>

<!-- Define lower section -->
<div class="w3-row" style="height:15vh" >
	<div class="w3-col m4 w3-center">
		<a id="Heat_Schedule" href="/schedule.php?heat=1" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:10vh"> Heat Sched. </a>
	</div>
	<div class="w3-col m4 w3-center">
		<a id="Cool_Schedule" href="/schedule.php?heat=2" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:10vh"> Cool Sched. </a>
	</div>
	<div class="w3-col m4 w3-center">
		<input type="submit" class="w3-button w3-black w3-block w3-border w3-border-blue" style="font-size:10vh" value="Save">
		</input>
	</div>
</div>
</form>
<!-- insert from scripts.js the if statement for image selection. will run through switch case with php variable -->
<script>
	function RedrawWeek() {
		var weeknum = parseInt(document.getElementById("weeknumber").value);
		if (weeknum <= 3 ) {
			var height = 7; //To ensure width of boxes isnt too small to display numbers
		} else {
			var height = 30 / ( weeknum * 1.25 );
		}
		var i;
		var weekdaysplits = document.getElementById("weekdaysplits");
		//Remove old stuff
		while (weekdaysplits.hasChildNodes()) {
			weekdaysplits.removeChild( weekdaysplits.lastChild );
		}
		for (i=0;i<weeknum;i++) {
			//weekdaysplits.appendChild( document.createTextNode("Split " + (i+1) ) );
			var div = document.createElement("div");
			var innerdiv = document.createElement("div");
			var innnerdiv = document.createElement("div");
			var timediv = document.createElement("div");
			var tstartinput = document.createElement("input");
			var tendinput = document.createElement("input");
			var tempinput = document.createElement("input");
			tstartinput.type = "time";
			tendinput.type = "time";
			tempinput.type = "number";
			tstartinput.id = "weekstarttime" + i;
			tstartinput.name = "weekstarttime" + i + "_post";
			tendinput.id = "weekstoptime" + i;
			tendinput.name = "weekstoptime" + i + "_post";
			tempinput.id = "weektemp" + i;
			tempinput.name = "weektemp" + i + "_post";
			tempinput.max = "90";
			tempinput.min = "50";

			tstartinput.style.padding = "0px";
			tstartinput.style.fontSize = (height / 2) + "vh";
			tstartinput.style.marginTop = "0px";
			tstartinput.style.maxWidth = "15vw";

			tendinput.style.padding = "0px";
			tendinput.style.fontSize = (height / 2) + "vh";
			tendinput.style.marginTop = "0px";
			tendinput.style.maxWidth = "15vw";

			tempinput.style.padding = "0px";
			tempinput.style.fontSize = (height / 2) + "vh";
			tempinput.style.marginTop = "0px";
			tempinput.style.maxWidth = "14vw";


			div.className = "w3-row";
			div.style.height = height + "vh";
			innerdiv.className = "w3-col s4 w3-center";
			innerdiv.style.height = "100%";
			innnerdiv.className = "w3-col s4 w3-center";
			innnerdiv.style.height = "100%";
			timediv.className = "w3-col s4 w3-center";
			timediv.style.height = "100%";
			timediv.style.maxWidth = "15vw";

			timediv.appendChild(tempinput);
			innerdiv.appendChild(tstartinput);
			innnerdiv.appendChild(tendinput);
			div.appendChild(innerdiv);
			div.appendChild(innnerdiv);
			div.appendChild(timediv);

			weekdaysplits.appendChild(div);
		};
	};
	function RedrawEnd() {
		var endnum = parseInt(document.getElementById("endnumber").value);
		if (endnum <=3 ) {
			var height = 7; //This max is in place to ensure width of boxes isnt too small to display time
		} else {
			var height = 30 / ( endnum * 1.25 );
		};
		var i;
		var weekendsplits = document.getElementById("weekendsplits");
		//Remove old stuff
		while (weekendsplits.hasChildNodes()) {
			weekendsplits.removeChild( weekendsplits.lastChild );
		}
		for (i=0;i<endnum;i++) {
			//weekdaysplits.appendChild( document.createTextNode("Split " + (i+1) ) );
			var div = document.createElement("div");
			var innerdiv = document.createElement("div");
			var innnerdiv = document.createElement("div");
			var timediv = document.createElement("div");
			var tstartinput = document.createElement("input");
			var tendinput = document.createElement("input");
			var tempinput = document.createElement("input");
			tstartinput.type = "time";
			tendinput.type = "time";
			tempinput.type = "number";
			tstartinput.id = "endstarttime" + i;
			tendinput.id = "endstoptime" + i;
			tempinput.id = "endtemp" + i;
			tstartinput.name = "endstarttime" + i + "_post";
			tendinput.name = "endstoptime" + i + "_post";
			tempinput.name = "endtemp" + i + "_post";
			tempinput.max = "90";
			tempinput.min = "50";

			tstartinput.style.padding = "0px";
			tstartinput.style.fontSize = (height / 2) + "vh";
			tstartinput.style.marginTop = "0px";
			//tstartinput.style.maxWidth = "12vw";

			tendinput.style.padding = "0px";
			tendinput.style.fontSize = (height / 2) + "vh";
			tendinput.style.marginTop = "0px";
			//tendinput.style.maxWidth = "12vw";

			tempinput.style.padding = "0px";
			tempinput.style.fontSize = (height / 2) + "vh";
			tempinput.style.marginTop = "0px";
			tempinput.style.maxWidth = "12vw";


			div.className = "w3-row";
			div.style.height = height + "vh";
			innerdiv.className = "w3-col s4 w3-center";
			innerdiv.style.height = "100%";
			innnerdiv.className = "w3-col s4 w3-center";
			innnerdiv.style.height = "100%";
			timediv.className = "w3-col s4 w3-center";
			timediv.style.height = "100%";
			timediv.style.maxWidth = "15vw";

			timediv.appendChild(tempinput);
			innerdiv.appendChild(tstartinput);
			innnerdiv.appendChild(tendinput);
			div.appendChild(innerdiv);
			div.appendChild(innnerdiv);
			div.appendChild(timediv);

			weekendsplits.appendChild(div);
		};
		//add hidden parameter that is which schedule is being modified
		var sched_input = document.createElement("input");
		sched_input.type = "hidden";
		sched_input.name = "sched_type_post";
		sched_input.value = "<?php echo $sched_type ?>";
		weekendsplits.appendChild(sched_input);
	};
	function UpWeek() {
		var weeknum = parseInt(document.getElementById("weeknumber").value);
		var k;
		var weekstarttime = [];
		var weekstoptime = [];
		var weektemp = [];
		for (k=0;k<weeknum;k++) {
			var string1 = 'weekstarttime[' + k.toString() + '] = document.getElementById("weekstarttime' + k.toString() + '").value;';
			var string2 = 'weekstoptime[' + k.toString() + '] = document.getElementById("weekstoptime' + k.toString() + '").value;';
			var string3 = 'weektemp[' + k.toString() + '] = document.getElementById("weektemp' + k.toString() + '").value;';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
		weeknum = weeknum + 1;
		document.getElementById("weeknumber").value = weeknum;
		
		<?php
			$weeknum = $weeknum + 1;
			//$_SESSION['weeknum'] = $weeknum;
			//setcookie('weeknum', $weeknum, time()+3600);
		?>
		RedrawWeek();
		for (k=0;k<(weeknum-1);k++) {
			var string1 = 'document.getElementById("weekstarttime' + k.toString() + '").value = "' + weekstarttime[k] + '";';
			var string2 = 'document.getElementById("weekstoptime' + k.toString() + '").value = "' + weekstoptime[k] + '";';
			var string3 = 'document.getElementById("weektemp' + k.toString() + '").value = "' + weektemp[k] + '";';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
	};
	function DownWeek() {
		var weeknum = parseInt(document.getElementById("weeknumber").value);
		var k;
		var weekstarttime = [];
		var weekstoptime = [];
		var weektemp = [];
		for (k=0;k<weeknum;k++) {
			var string1 = 'weekstarttime[' + k.toString() + '] = document.getElementById("weekstarttime' + k.toString() + '").value;';
			var string2 = 'weekstoptime[' + k.toString() + '] = document.getElementById("weekstoptime' + k.toString() + '").value;';
			var string3 = 'weektemp[' + k.toString() + '] = document.getElementById("weektemp' + k.toString() + '").value;';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
		weeknum = weeknum - 1;
		document.getElementById("weeknumber").value = weeknum;
		<?php
			$weeknum = $weeknum - 1;
			//$_SESSION['weeknum'] = $weeknum;
			//setcookie('weeknum', $weeknum, time()+3600);
		?>
		RedrawWeek();
		for (k=0;k<(weeknum);k++) {
			var string1 = 'document.getElementById("weekstarttime' + k.toString() + '").value = "' + weekstarttime[k] + '";';
			var string2 = 'document.getElementById("weekstoptime' + k.toString() + '").value = "' + weekstoptime[k] + '";';
			var string3 = 'document.getElementById("weektemp' + k.toString() + '").value = "' + weektemp[k] + '";';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
	};
	function UpEnd() {
		var endnum = parseInt(document.getElementById("endnumber").value);
		var k;
		var endstarttime = [];
		var endstoptime = [];
		var endtemp = [];
		for (k=0;k<endnum;k++) {
			var string1 = 'endstarttime[' + k.toString() + '] = document.getElementById("endstarttime' + k.toString() + '").value;';
			var string2 = 'endstoptime[' + k.toString() + '] = document.getElementById("endstoptime' + k.toString() + '").value;';
			var string3 = 'endtemp[' + k.toString() + '] = document.getElementById("endtemp' + k.toString() + '").value;';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
		endnum = endnum + 1;
		document.getElementById("endnumber").value = endnum;
		
		<?php
			$endnum = $endnum + 1;
			//$_SESSION['weeknum'] = $weeknum;
			//setcookie('endnum', $endnum, time()+3600);
		?>
		RedrawEnd();
		for (k=0;k<(endnum-1);k++) {
			var string1 = 'document.getElementById("endstarttime' + k.toString() + '").value = "' + endstarttime[k] + '";';
			var string2 = 'document.getElementById("endstoptime' + k.toString() + '").value = "' + endstoptime[k] + '";';
			var string3 = 'document.getElementById("endtemp' + k.toString() + '").value = "' + endtemp[k] + '";';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
	};
	function DownEnd() {
		var endnum = parseInt(document.getElementById("endnumber").value);
		var k;
		var endstarttime = [];
		var endstoptime = [];
		var endtemp = [];
		for (k=0;k<endnum;k++) {
			var string1 = 'endstarttime[' + k.toString() + '] = document.getElementById("endstarttime' + k.toString() + '").value;';
			var string2 = 'endstoptime[' + k.toString() + '] = document.getElementById("endstoptime' + k.toString() + '").value;';
			var string3 = 'endtemp[' + k.toString() + '] = document.getElementById("endtemp' + k.toString() + '").value;';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
		endnum = endnum - 1;
		document.getElementById("endnumber").value = endnum;
		
		<?php
			$endnum = $endnum - 1;
			//$_SESSION['weeknum'] = $weeknum;
			//setcookie('endnum', $endnum, time()+3600);
		?>
		RedrawEnd();
		for (k=0;k<endnum;k++) {
			var string1 = 'document.getElementById("endstarttime' + k.toString() + '").value = "' + endstarttime[k] + '";';
			var string2 = 'document.getElementById("endstoptime' + k.toString() + '").value = "' + endstoptime[k] + '";';
			var string3 = 'document.getElementById("endtemp' + k.toString() + '").value = "' + endtemp[k] + '";';
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
	};
</script>

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
	var sched_type = "<?php echo $sched_type ?>";
	if (sched_type == "heat") {
		document.getElementById('Heat_Schedule').className = "w3-button w3-green w3-block w3-border w3-border-blue";
	} else {
		document.getElementById('Cool_Schedule').className = "w3-button w3-green w3-block w3-border w3-border-blue";
	}

</script>

<!-- on load function to redraw everythin -->
<script type="text/javascript">
	function codeOnLoad() {
		var weeknum = parseInt(<?php echo $weeknum ?>);
		var endnum = parseInt(<?php echo $endnum ?>);
		var i;
		document.getElementById("weeknumber").value = weeknum;
		document.getElementById("endnumber").value = endnum;
		RedrawWeek();
		RedrawEnd();
		//use json encode to create an array object (above) from the read in data. This must be done before iterating through this loop because the way this loop works only iterates the php variables once
		//something like:
		var weekstarttime = <?php echo json_encode($weekstarttime) ?>;
		var weekstoptime = <?php echo json_encode($weekstoptime) ?>;
		var weektemp = <?php echo json_encode($weektemp) ?>;
		var endstarttime = <?php echo json_encode($endstarttime) ?>;
		var endstoptime = <?php echo json_encode($endstoptime) ?>;
		var endtemp = <?php echo json_encode($endtemp) ?>;
		for(i=0;i<weeknum;i++) {
			var x = i.toString();
			var string1 = 'document.getElementById("weekstarttime' + x + '").value = "' + weekstarttime[i] + '";';
			var string2 = 'document.getElementById("weekstoptime' + x + '").value = "' + weekstoptime[i] + '";';
			var string3 = 'document.getElementById("weektemp' + x + '").value = "' + weektemp[i] + '";';
			//console.log( string1 );
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
		for(i=0;i<endnum;i++) {
			var x = i.toString();
			var string1 = 'document.getElementById("endstarttime' + x + '").value = "' + endstarttime[i] + '";';
			var string2 = 'document.getElementById("endstoptime' + x + '").value = "' + endstoptime[i] + '";';
			var string3 = 'document.getElementById("endtemp' + x + '").value = "' + endtemp[i] + '";';
			//console.log( string1 );
			eval( string1 );
			eval( string2 );
			eval( string3 );
		};
	};
	window.onload = codeOnLoad;
</script>
</body>
</html> 
