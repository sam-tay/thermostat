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
	function FanOn() {
			<?php 
				$fan_file = fopen("variables/fan_mode.sam",'w');
				$oold = "on";
				fwrite( $fan_file , $oold );
				fclose($fan_file);
			?>
			window.location = "index.php";
		};
			window.onload=FanOn;
</script>

</body>
</html> 