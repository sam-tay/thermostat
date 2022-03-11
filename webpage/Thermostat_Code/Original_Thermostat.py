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
CurState = CurrentState();
current_temp = 0;
hot_temp = 70 #default heat temperature
cold_temp = 72 #default cool temperature

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
	current_temp = mcp.temperature * ( 9 / 5 ) + 32;
	return;
def elapsed_time(current , previous):
	#Returns the elapsed time, in minutes
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
	heat_delta = current_temp - actual_start_temp;
	return;
def cool_call():
	global cool_time;
	global cool_delta;
	global cold_temp;
	global cold_thres;
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
hc = threading.Thread( target=heat_call );
cc = threading.Thread( target=cool_call );
fc = threading.Thread( target=fan_call );
#read_temp();
#rt.start();

#heat_call()
hc = threading.Thread( target=heat_call )
hc.start()
#threading.run( heat_call );

print( "thread count:" , threading.activeCount() );

while True:
	#Threads must be initiated in the while loop. They can only be "started" once, but keeping them in the while loop does not lead to threads stacking on top of eachother. Only 2 threads should be running at all times :)
	time.sleep( 2 ); #poll for new information every 2 seconds
	print( "heating state is " , CurState.heating )
	print( "heat error is " , CurState.heat_error )
	print( "thread count:" , threading.activeCount() );
	rt = threading.Thread( target=read_temp );
	rt.start();
	
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
			#Check if cooling is already running
			if CurState.cooling == 0:
				#check elapsed time from last call (MUST BE MORE THAN 5 MINUTES)
				e_time = elapsed_time( datetime.datetime.now() , CurState.last_call );
				if e_time > 5:
					cc = threading.Thread( target=cool_call );
					cc.start();
					if CurState.cool_timeout == 1:
						setattr( CurState , 'cool_timeout' , 0 );
				else:
					if CurState.cool_timeout != 1:
						setattr( CurState , 'cool_timeout' , 1 );




print( CurState.heating )
