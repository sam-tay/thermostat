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
	function OffMode() {
			<?php 
				$mode_file = fopen("variables/curr_mode.sam",'w');
				$temp_file = fopen("variables/temp_setting.sam",'w');
				$status_file = fopen("variables/schedule_status.sam",'w');
				$new_temp = '--';
				$new_status = 'OFF';
				$oold = "off";
				fwrite( $mode_file , $oold );
				fclose($mode_file);
				fwrite( $temp_file , $new_temp );
				fclose( $temp_file );
				fwrite( $status_file , $new_status );
				fclose( $status_file );
			?>
			window.location = "index.php";
		};
			window.onload=OffMode;
</script>

</body>
</html> 