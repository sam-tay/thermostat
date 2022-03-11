 <!DOCTYPE html>
<html>
<head>
<title>Fuckin pigs</title>
<link rel="stylesheet" href="w3_styles.css">
<meta charset="utf-8" />
</head>
<body>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
	$set_temp = file_get_contents("variables/temp_setting.sam");
	$max_temp = 90;
	$min_temp = 50;
?>

<script type="text/javascript">
	function RaiseTemp() {
			<?php 
				$temp_file = fopen("variables/temp_setting.sam",'w');
				$status_file = fopen("variables/schedule_status.sam",'w');
				$new = 'Temperature hold <br> Started: ' . date("h:i a");
				$oold = (int)$set_temp + 1;
				if ( $oold < $min_temp ) {
					$oold = $min_temp;
				} elseif ( $oold > $max_temp ) {
					$oold = $max_temp;
				}
				fwrite( $temp_file , $oold );
				fclose($temp_file);
				fwrite( $status_file , $new );
				fclose( $status_file );
			?>
			window.location = "index.php";
		};
			window.onload=RaiseTemp;
</script>

</body>
</html> 