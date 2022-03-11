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
	function RestartPi() {
			<?php 
				//look in install folder for info about how to execute without sudo
				$cmd = 'sudo reboot now';
				$out = shell_exec($cmd);
			?>
			window.location = "index.php";
		};
			window.onload=RestartPi;
</script>

</body>
</html> 