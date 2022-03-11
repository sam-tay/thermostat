#!/usr/bin/python3

import datetime
import urllib.parse

def check_schedule():
	#must be placed within function to import and run as script
	var_dir = '/home/rugged/Desktop/python-internet-test/webpage/variables';
	#var_dir = '/home/pi/Desktop/Thermostat/Webpage/variables';
	statusname = var_dir + '/curr_mode.sam';

	var_file = open( statusname , 'r' );
	status = var_file.read();
	var_file.close();

	#check which status to open
	if (status == 'heat'):
		schedname = var_dir + '/heat_schedule.sam';
	elif (status == 'cool'):
		schedname = var_dir + '/cool_schedule.sam';
	else:
		schedname = '';

	#get variables for time
	if (schedname != ''):
		var_file = open( schedname , 'r' );
		sched_read = var_file.read();
		sched = urllib.parse.parse_qs(sched_read);
		var_file.close();
		#determine if currently a week day or weekend
		day_num = datetime.datetime.today().weekday(); #5 & 6 are weekends
		if (day_num < 5):
			#weekday
			begining = 'week';
		else:
			begining = 'end';
		daily_incs = int( sched[ begining + 'num' ][0] );
		#import all times
		starts = [];
		stops = [];
		temps = [];
		substring = ':';
		for x in range( 0 , daily_incs ):
			#determine if saved as H:M or H:M:S
			psuedo_start = sched[ begining + 'starttime' + str(x)][0];
			psuedo_stop = sched[ begining + 'stoptime' + str(x)][0];
			srt_count = psuedo_start.count(substring);
			stp_count = psuedo_stop.count(substring);
			if srt_count == 2:
				starts.append( datetime.datetime.strptime( psuedo_start , '%H:%M:%S').time() );
			else:
				starts.append( datetime.datetime.strptime( psuedo_start , '%H:%M').time() );
			if stp_count == 2:
				stops.append( datetime.datetime.strptime( psuedo_stop , '%H:%M:%S').time() );
			else:
				stops.append( datetime.datetime.strptime( psuedo_stop , '%H:%M').time() );
			temps.append( sched[ begining + 'temp' + str(x)][0] );

		current_time = datetime.datetime.now().time();
		#determine which time range to use
		t_index = -1;
		for x in range( 0 , daily_incs ):
			if ( starts[ x ] < stops[ x ] ):
				if ( current_time >= starts[ x ] ) and ( current_time <= stops[ x ] ):
					t_index = x;
			else:
				if ( current_time <= stops[ x ] ) or ( current_time >= starts[ x ] ):
					t_index = x;
		if( t_index > -1 ):
			sched_temp = int( temps[ t_index ] );
			msg = 'Running Schedule';
		else:
			print('Error in determining schedule temp. Current time is not within any time range. Setting to default of 68');
			msg = 'Error getting schedule temp';
			sched_temp = int( 68 );
	else:
		msg = 'HELLA FUCKING ERROR (or thing is turned off)';
		sched_temp = int(-10);

	temp_file = var_dir + '/temp_setting.sam';
	status_file = var_dir + '/schedule_status.sam';
	var_file = open( temp_file , 'w' );
	var_file.write( "%i" % ( sched_temp ) );
	var_file.close();
	var_file = open( status_file , 'w' );
	var_file.write( "%s" % ( msg ) );
	var_file.close();

#magic line that tells function if called directly from command line, run. also allows function to be called as thread when imported into Thermostat.py
if __name__ == "__main__":
	check_schedule();