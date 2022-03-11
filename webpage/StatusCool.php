 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>
<link rel="stylesheet" href="w3_styles.css">
<meta charset="utf-8" />
</head>
<body>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<script type="text/javascript">
	function CoolMode() {
			<?php 
				$mode_file = fopen("variables/curr_mode.sam",'w');
				$oold = "cool";
				fwrite( $mode_file , $oold );
				fclose($mode_file);
				$cmd = 'python3 ScheduleCheck.py';
				shell_exec($cmd);
			?>
			window.location = "index.php";
		};
			window.onload=CoolMode;
</script>

</body>
</html> 