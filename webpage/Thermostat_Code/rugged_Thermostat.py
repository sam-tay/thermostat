#!/usr/bin/env python3

# Must be run using 'python3 Thermostat.py'

#Shared Imports
import time
import threading
import datetime
import atexit
import sys

#imports for Temperature
import board
import busio
import adafruit_mcp9808

#imports for Relay
import RPi.GPIO as GP

#import functions from Website folder
sys.path.insert( 0 , '/home/pi/Desktop/Thermostat/Webpage/' );
from Webpage import GetWeather
from Webpage import ScheduleCheck

class CurrentState:
	#This will be the variable defining the current state of the system. 0 is off, 1 is on, last_call is the time the last change of state occured
	def __init__( self , heating = 0 , cooling = 0 , fanning = 0 , last_call = 0 , heat_error = 0 , cool_timeout = 0 ):
		self.heating = heating;
		self.cooling = cooling;
		self.fanning = fanning;
		self.heat_error = heat_error;
		self.last_call = datetime.datetime.now();
		self.cool_timeout = cool_timeout;
#Define global variables
#var_dir = '/home/rugged/Desktop/python-internet-test/webpage/variables';
var_dir = '/home/pi/Desktop/Thermostat/Webpage/variables';
curtempname = var_dir + '/curr_temp.sam';
errorname = var_dir + '/errors.sam';
CurState = CurrentState();
current_temp = 0;
hot_temp = 70 #default heat temperature
cold_temp = 72 #default cool temperature
outside_temp = 10;
outside_hum = 10;
outside_uv = 5;

#Define variables to define which setting thermostat is set to
F = 0 # 0 is auto , 1 is on
H = 1 # 0 is off , 1 is heat , 2 is air conditioner

#Variables for learning heat curves
heat_time = 0;
heat_delta = 0;
cool_time = 0;
cool_delta = 0;
hot_thres = 0;
cold_thres = 0;

def read_temp():
	global current_temp
	global curtempname;
	current_temp = mcp.temperature * ( 9 / 5 ) + 32;
	#write to file
	var_file = open( curtempname , 'w' );
	var_file.write( '%i' % ( current_temp ) );
	var_file.close();
	return;
def elapsed_time(current , previous):
	#Returns the elapsed time, in minutes
	if ( current.day == 1 ) and ( previous.day != 1 ):
		#assume 1 day?. should be ok :)
		elapsssed = 1440 + ( current.hour - previous.hour ) * 60 + current.minute - previous.minute + (current.second - previous.second ) / 60;
	else:
        elapsssed = ( current.day - previous.day ) * 1440 + ( current.hour - previous.hour ) * 60 + current.minute - previous.minute + (current.second - previous.second ) / 60;
    return elapsssed;
def exit_handler():
	#will set all relays to off after an exit
	GP.output( fan , GP.HIGH );
	GP.output( heat , GP.HIGH );
	GP.output( cool , GP.HIGH );
	print( "Exiting program meow" )
	return;
def heat_call():
	global heat_time;
	global heat_delta;
	global hot_temp;
	global hot_thres;
	global errorname;
	GP.output( heat , GP.LOW );
	setattr( CurState , 'heating' , 1 );
	setattr( CurState , 'last_call' , datetime.datetime.now());
	#read_temp();
	starting_temp = current_temp;
	actual_start_temp = current_temp;
	i = 0;
	while ( current_temp < ( hot_temp + hot_thres ) ) and ( H == 1 ):
		i += 1;
		time.sleep( 2 );
		#read_temp();
		print( current_temp )
		#Check that heat is actually heating. Reset "starting temp" so it is current. Probably want to do every ~10 minutes( 300i).
		if i > 300:
			#This checks to see that heat call is actually heating
			if current_temp <= starting_temp:
				setattr( CurState , 'heat_error' , 1 );
				GP.output( heat , GP.HIGH );
				var_file = open( errorname , 'r' );
				var_file.write( '%s' % ( 'Heat Hold!!' ) );
				var_file.close();
				time.sleep( 1200 ); #20 minutes (1200 )
				GP.output( heat , GP.LOW );
				#read_temp();
				starting_temp = current_temp;
				i = 0;
				#setattr( CurState , 'heat_error' , 0 );
			else:
				starting_temp = current_temp;
				i = 0;
	GP.output( heat , GP.HIGH );
	setattr( CurState , 'heating' , 0 );
	setattr( CurState , 'last_call' , datetime.datetime.now());
	if CurState.heat_error != 1:
		heat_time = elapsed_time( datetime.datetime.now() , CurState.last_call );
	else:
		heat_time = 0;
		setattr( CurState , 'heat_error' , 0 );
		var_file = open( errorname , 'r' );
		var_file.write('');
		var_file.close();
	heat_delta = current_temp - actual_start_temp;
	return;
def cool_call():
	global cool_time;
	global cool_delta;
	global cold_temp;
	global cold_thres;
	global errorname;
	GP.output( cool , GP.LOW );
	GP.output( fan , GP.LOW );
	setattr( CurState , 'cooling' , 1);
	setattr( CurState , 'fanning' , 1);
	setattr( CurState , 'last_call' , datetime.datetime.now());
	#read_temp();
	starting_temp = current_temp;
	while ( current_temp > (cold_temp - cold_thres) ) and ( H == 2 ):
		time.sleep( 2 );
		#if fan is "on at start of ac, and then switched to auto, may cause issue. Catch here
		if CurState.fanning == 0:
			GP.output( fan , GP.LOW );
			setattr( CurState , 'fanning' , 1 );
		#read_temp();
	GP.output( cool , GP.HIGH );
	GP.output( fan , GP.HIGH );
	cool_delta = current_temp - starting_temp;
	cool_time = elapsed_time( datetime.datetime.now() , CurState.last_call );
	setattr( CurState , 'last_call' , datetime.datetime.now() );
	setattr( CurState , 'cooling' , 0 );
	setattr( CurState , 'fanning' , 0 );
	return;
def fan_call():
	global F;
	GP.output( fan , GP.LOW );
	setattr( CurState , 'fanning' , 1 );
	while F == 1:
		time.sleep( 5 );
	GP.output( fan , GP.LOW );
	setattr( CurState , 'fanning' , 0 );
	return;
def get_status():
	#Will get status of schedule and heating
	global var_dir;
	global H;
	global F;
	global hot_temp;
	global cold_temp;
	statusname = var_dir + '/curr_mode.sam';
	var_file = open( statusname , 'r' );
	status = var_file.read();
	var_file.close();

	#check which status to open
	if (status == 'heat'):
		H = 1;
	elif (status == 'cool'):
		H = 2;
	else:
		H = 0;
	#H is now set

	fanname = var_dir + '/fan_mode.sam';
	var_file = open( fanname , 'r' );
	fan_status = var_file.read();
	var_file.close();

	#check which status to open
	if (fan_status == 'on'):
		F = 1;
	else:
		F = 0;

	#Determine whether the schedule file should be read or the hold temp should be used
	schedstatusname = var_dir + '/schedule_status.sam';
	var_file = open( schedstatusname , 'r');
	sched_status = var_file.read();
	var_file.close();

	if ( sched_status == 'Running Schedule'):
		#check schedule before 
		ScheduleCheck.check_schedule();
	else:
		#This is hacky. See if it can be fixed. May need an additional variable parsed from php that somehow includes the entire format for a date time 
		instant_time = datetime.datetime.now();
		checker1 = int( sched_status[ -8:-6 ] );
		checker2 = sched_status[ -2: ];
		if checker1 == 12:
			checker1 = 0;
		if ( checker1 >= 9 ) and ( checker2 == 'pm' ) and ( instant_time.hour < 21 ):
			yesterday = instant_time - datetime.timedelta( days = 1 );
			hold_start = datetime.datetime.strptime( sched_status[-8:] + ' 00 ' + str( yesterday.day ) + ' ' + str( yesterday.month ) + ' ' + str( yesterday.year ), '%I:%M %p %S %d %m %Y' );
		else:
			hold_start = datetime.datetime.strptime( sched_status[-8:] + ' 00 ' + str( instant_time.day ) + ' ' + str( instant_time.month ) + ' ' + str( instant_time.year ), '%I:%M %p %S %d %m %Y' );
		hold_time = elapsed_time( instant_time , hold_start );
		#hold_time.day( datetime.datetime.now().day() );
		if hold_time > 180:
			var_file = open( schedstatusname , 'w' );
			var_file.write( '%s' % ( 'Running Schedule' ) );
			var_file.close();
			ScheduleCheck.check_schedule();

	#get the current set temperature
	tempname = var_dir + '/temp_setting.sam';
	var_file = open( tempname , 'r' );
	new_temp = var_file.read();
	var_file.close();
	if ( new_temp != '--' ):
		new_temp = 0;
	else:
		new_temp = int( new_temp );

	if ( H == 1 ):
		#Heat temp
		hot_temp = new_temp;
	elif ( H == 2 ):
		cold_temp = new_temp;
	else:
		#Do default temperatures cuz it will not matter
		hot_temp = 70 #default heat temperature
		cold_temp = 72 #default cool temperature
def outdoors():
	global var_dir;
	global outside_temp;
	global outside_hum;
	global outside_uv;
	GetWeather.update_weather();
	otempname = var_dir + '/outside_temp.sam';
	ohumname = var_dir + '/outside_hum.sam';
	ouvname = var_dir + '/uv_ind.sam';
	var_file = open( otempname , 'r' );
	outside_temp = float( var_file.read() );
	var_file.close();
	var_file = open( ohumname , 'r' );
	outside_hum = int( var_file.read() );
	var_file.close();
	var_file = open( ouvname , 'r' );
	outside_uv = int( var_file.read() );
	var_file.close();


#relay setup
fan = 24 #23, changed to 24 for testing
heat = 23 #24, changed to 23 for testing
cool = 25

atexit.register( exit_handler );

GP.setmode(GP.BCM)
GP.setwarnings(False)
GP.setup( fan , GP.OUT)
GP.setup( heat , GP.OUT)
GP.setup( cool , GP.OUT)
	#initialize to off
GP.output( fan , GP.HIGH )
GP.output( heat , GP.HIGH )
GP.output( cool , GP.HIGH )

#Temperature Setup
i2c_bus = busio.I2C(board.SCL, board.SDA)
mcp = adafruit_mcp9808.MCP9808(i2c_bus)
	#Initialize temperatures

#set up threads for calls
#rt = threading.Thread( target=read_temp );
#hc = threading.Thread( target=heat_call );
#cc = threading.Thread( target=cool_call );
#fc = threading.Thread( target=fan_call );
#read_temp();
#rt.start();

#heat_call()
#hc = threading.Thread( target=heat_call )
#hc.start()
#threading.run( heat_call );

print( "thread count:" , threading.activeCount() );

i = 1298;
outside_temp_err = 0;

while True:
	i += 1;
	if ( i % 5 == 0 ):
		gs = threading.Thread( target=get_status );
		gs.start();
	if ( i > 1300 ):
		#Every hour will read outdoor temp
		oc = threading.Thread( target=outdoors );
		oc.start();
	#Threads must be initiated in the while loop. They can only be "started" once, but keeping them in the while loop does not lead to threads stacking on top of eachother. Only 2 threads should be running at all times :)
	time.sleep( 2 ); #poll for new information every 2 seconds
	print( "heating state is " , CurState.heating )
	print( "heat error is " , CurState.heat_error )
	print( "thread count:" , threading.activeCount() );
	rt = threading.Thread( target=read_temp );
	rt.start();


	#Get the current state of the device
	
	#initiate fan control here:
	# for F, 0 means auto, 1 means on
	if F == 1:
		#check if fan is running
		if CurState.fanning != 1:
			fc.fanning = threading.Thread( target=fan_call );
			fc.start();
	
	#initiate thermostat control here:
	#H 0 is off , 1 is heat , 2 is air conditioner
	if H == 1:
		#heating call
		if current_temp < ( hot_temp ):
			#Check if heating is already running
			if CurState.heating == 0:
				#check for elapsed time from last call
				#This is not needed for heat. Only needed for cooling
				#e_time = elapsed_time( datetime.datetime.now() , CurState.last_call );
				#if e_time > 5:
					#hc = threading.Thread( target=heat_call );
					#hc.start();
				hc = threading.Thread( target=heat_call );
				hc.start();
	elif H == 2:
		#cooling call
		if current_temp > cold_temp:
			#Check if outside temp is too cold
			if outside_temp < 50:
				if outside_temp_err == 0:
					outside_temp_err = 1;
					var_file = open( errorname , 'r' );
					var_file.write( 'Out. temp too cold' );
					var_file.close();
			else:
				if outside_temp_err == 1:
					outside_temp_err = 0;
					var_file = open( errorname , 'r' );
					var_file.write('');
					var_file.close();
				#Check if cooling is already running
				if CurState.cooling == 0:
					#check elapsed time from last call (MUST BE MORE THAN 5 MINUTES)
					e_time = elapsed_time( datetime.datetime.now() , CurState.last_call );
					if e_time > 5:
						cc = threading.Thread( target=cool_call );
						cc.start();
						if CurState.cool_timeout == 1:
							setattr( CurState , 'cool_timeout' , 0 );
							var_file = open( errorname , 'r' );
							var_file.write('');
							var_file.close();
					else:
						if CurState.cool_timeout != 1:
							setattr( CurState , 'cool_timeout' , 1 );
							var_file = open( errorname , 'r' );
							var_file.write( 'Cool timeout' );
							var_file.close();




print( CurState.heating )
