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
	function RefreshWeather() {
			<?php
				$cmd = 'python3 GetWeather.py';
				shell_exec($cmd);
			?>
			window.location = "index.php";
		};
			window.onload=RefreshWeather;
</script>

</body>
</html> 