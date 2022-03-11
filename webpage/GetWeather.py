from forecastiopy import *

def update_weather():
	#Must be placed in a function to call in both Thermostat and from command line...
	Co_Springs = [ 38.875899,-104.770032 ]
	API_key = ''
	print( "Removed my API key. Get your own fucking key :)" )

	fio = ForecastIO.ForecastIO(API_key, latitude=Co_Springs[0], longitude=Co_Springs[1])

	#current contains current information
	#daily contains information about forecast for that day
	current = FIOCurrently.FIOCurrently(fio)
	daily = FIODaily.FIODaily(fio)

	outside_temp = current.temperature
	outside_hum = current.humidity * 100
	uv_ind = current.uvIndex

	#want to display weather for next day/this day depending on time of day. This (day_1) gives for the current day. day_2 is tomorrow
	per_chance = daily.day_1_precipProbability * 100
	per_type = daily.day_1_precipType
	clouds = daily.day_1_cloudCover * 100

	# Determine forecast
	if ( per_chance > 40 ):
		forecast = per_type #options are rain , snow , sleet
	elif ( clouds > 40 ):
		forecast = 'cloud'
	else:
		forecast = 'sun'


	#Write the data out to forecast file
	var_dir = '/home/rugged/Desktop/python-internet-test/webpage/variables';
	forecastname = var_dir + '/forecast.sam';
	humname = var_dir + '/outside_hum.sam';
	tempname = var_dir + '/outside_temp.sam';
	uvname = var_dir + '/uv_ind.sam';
	var_file = open( forecastname , 'w' );
	var_file.write( "%s" % ( forecast ) )
	var_file.close();
	var_file = open( humname , 'w' );
	var_file.write( "%2.0f" % ( outside_hum ) )
	var_file.close();
	var_file = open( tempname , 'w' );
	var_file.write( "%5.2f" % ( outside_temp ) )
	var_file.close();
	var_file = open( uvname , 'w' );
	var_file.write( "%i" % ( uv_ind ) )
	var_file.close();

if __name__ == "__main__":
	update_weather();