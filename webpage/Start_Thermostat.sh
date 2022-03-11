#!/usr/bin/env bash

Proc=$(pgrep python3)
if [ $? != 0 ]
then
	nohup python3 Thermostat.py &
fi
