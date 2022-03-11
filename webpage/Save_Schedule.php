<?php 
	session_start();
?>
 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>

<!-- Define php scripts to run -->
<?php
$sched_type = $_POST['sched_type_post'];
$weeknum = $_POST['weeknumber_post'];
$endnum = $_POST['endnumber_post'];
switch ($sched_type) {
	case "cool":
		$current_file = "variables/cool_schedule.sam";
		break;
	default:
		//make adjusting the heat schedule the default
		$current_file = "variables/heat_schedule.sam";
};
$output = 'weeknum=' . $weeknum;

for ($x = 0; $x < $weeknum ; $x++) {
	$str1 = '$weekstarttime[' . $x . '] = $_POST["weekstarttime' . $x . '_post"];';
	$str2 = '$weekstoptime[' . $x . '] = $_POST["weekstoptime' . $x . '_post"];';
	$str3 = '$weektemp[' . $x . '] = $_POST["weektemp' . $x . '_post"];';
	eval($str1);
	eval($str2);
	eval($str3);
	$output .= '&weekstarttime' . $x . '=' . $weekstarttime[ $x ] . '&weekstoptime' . $x . '=' . $weekstoptime[ $x ] . '&weektemp' . $x . '=' . $weektemp[ $x ];
};
$output .= '&endnum=' . $endnum;
for ($x = 0; $x < $endnum ; $x++) {
	$str1 = '$endstarttime[' . $x . '] = $_POST["endstarttime' . $x . '_post"];';
	$str2 = '$endstoptime[' . $x . '] = $_POST["endstoptime' . $x . '_post"];';
	$str3 = '$endtemp[' . $x . '] = $_POST["endtemp' . $x . '_post"];';
	eval($str1);
	eval($str2);
	eval($str3);
	$output .= '&endstarttime' . $x . '=' . $endstarttime[ $x ] . '&endstoptime' . $x . '=' . $endstoptime[ $x ] . '&endtemp' . $x . '=' . $endtemp[ $x ];
};

// Logic to ensure new times are valid
$weekerr = '';
$enderr = '';
// Ensure 24 hr span
$weektot = time_sum( $weekstarttime , $weekstoptime );
$endtot = time_sum( $endstarttime , $endstoptime );
if ( $weektot != 24 ) {
	$weekerr .= 'Not all weekday times used. Must sum to 24 hrs per day.';
}
if ( $endtot != 24 ) {
	$enderr .= 'Not all weekend times used. Must sum to 24 hrs per day.';
}
if ( overlap_test( $weekstarttime , $weekstoptime ) ) {
	$weekerr .= 'Week time ranges overlap.';
}
if ( overlap_test( $endstarttime , $endstoptime ) ) {
	$enderr .= 'Weekend time ranges overlap.';
}

$err = $weekerr . $enderr;
if ( $err != '' ) {
	$err = 'Could not save new schedule. ' . $err;
	$ballz = 1;
} else {
	$err = 'Saved new <br> ' . $sched_type . ' schedule'; 
	file_put_contents($current_file, $output);
	$ballz = 2;
}

$uri = "/schedule.php";


function time_sum( $start_times , $end_times ) {
	$delt = 0;
	$check = 0;
	for ($i=0;$i<sizeof($start_times);$i++) {
		$check = (strtotime( $end_times[ $i ] ) - strtotime( $start_times[ $i ] ) )/3600;
		if ($check < 0) {
			$check = $check + 24;
		}
		$delt += $check;
	};
	return $delt;
};
function overlap_test( $start_times , $end_times ) {
	//simple check. ensure that the previous end time is equal to start time of next segment
	$overlap = false;
	if (sizeof($start_times) > 1 ) {
		for ($i=1;$i<sizeof($start_times);$i++) {
			if ( $end_times[$i - 1 ] != $start_times[ $i ]) {
				$overlap = true;
			}
		}
		if ( $end_times[ sizeof($start_times) - 1 ] != $start_times[ 0 ] ) {
			$overlap = true;
		}
	}
	return $overlap;
}
?>

<link rel="stylesheet" href="w3_styles.css"/>
<?php
	if ( $ballz == 2 ) {
		echo '<meta charset="utf-8" http-equiv="refresh" content="5;index.php" />';
	} else {
		echo '<meta charset="utf-8" />';
	}
?>

</head>

<meta name="viewport" content="width=device-width, initial-scale=1.0"/>


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
<div class="w3-row" style="height:70vh" >
		<div class="w3-row" style="height:35vh">
			<p style="font-size:17vh; line-height:17vh; padding:0px; vertical-align:top;" align="left"> <?php echo $err ?> </p>
			<?php
					if ( $ballz == 2 ) {
						echo '<p> Redirecting in 5 seconds... </p>';
					} else {
						echo '';
					}
			?>
		</div>
</div>

<!-- insert from scripts.js the if statement for image selection. will run through switch case with php variable -->

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

<!-- on load function to redraw everythin -->
</body>
</html> 
