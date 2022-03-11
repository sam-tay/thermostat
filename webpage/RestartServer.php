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
	function RestartServer() {
			<?php
				//This will just kill the program. Also set up cron to check if program is running and restart if not
				//Set up cron to run start_server
				$fuck = system('nohup ./Kill_php_server.sh > /dev/null 2>&1 &',$retval);
			?>
			window.location = "index.php";
		};
			window.onload=RestartServer;
</script>

</body>
</html> 

