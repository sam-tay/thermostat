# thermostat
RaspberryPi Powered Smart Thermostat


# Contents:

This project contains two major pieces. The python multi-threaded function, which controls the thermostat and checks the temperatures, and a web-page, which is served by the raspbery pi and can be accesses by a tablet or any internet connected device.

1. Thermostat.py
	- The code for controlling the thermostat and starting the webserver. This should be a CRON-TAB started process, which gets checked and restarted periodically (programmed to use persistent files so shutdowns shouldnt cause issues when the thermostat restarts)
1. webpage (Directory)
	- Contains all of the code for the webpage, which is a server-based php implemeted webpage (if I were to redo this, I would definitely make a lot of changes here...)

# Instructions:

1. On a default raspberryPi, navigate to the Desktop and run 

		git clone git@github.com:sam-tay/thermostat.git

1. Set up 2 CRONTAB files. Point the first at webpage/Start_Thermostat.sh and the second at webpage/Start_Server.sh
 - You may need to change these files to be executable by running chmod +x FILENAME
1. Create template files for if you want to save heating/cooling data. Do this by running:

		cd InitializationFile
		python3 Create_data_files.py

1. Everything should be up and running. Check localhost:4000 to interact with the webpage and change the thermostat parameters

# Hardware:

This project expects the rasbperryPi to have a few devices connected to it. The first is an Adafruit Si7021 Humidity/temperature sensor attached to the primary I2C bus. The second is three 24VAC capable relays. These relays need to be connected to the house's heating system to the heat_Call, cool, and fan control lines, with the relay toggling between the 24V Hot line and the ground line (or open). The control for these relays should be to:
| GPIO | Relay |
----------------
| 23 | fan 
| 24 | Heat Call
| 25 | Cool Call

# Configuration:

Almost all configuration can be done directly from the webpage. Fixing the IP Address of the RaspberryPi is encouraged (or using a domain name) so navigation to the control page will be easy. The device can be scheduled to optimize the turn on/turn off times and the setpoints for the thermostat throughout the day. The location for the weather API should be modified to reflect the actual location (this can be done in the webpage/GetWeather.py function, and an API key will need to be inserted into that).

# Data Tracking:

You can log data in order to optimize the setpoint throughout the day (or if you just like data). Change the SaveData variable in Thermostat.py to any value > 0 to enable saving. The data will be saved to a heat_data.csv and a cool_data.csv in the working directory.

Good Luck and happy coding  
   |\__/,|   (`\  
   |o o  |__ _)  
 _.( T   )  `  /  
((_ `^--' /_<  \  
`` `-'(((/  (((/  
