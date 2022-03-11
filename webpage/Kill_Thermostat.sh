#!/usr/bin/env bash

Proc=$(pgrep python3)
if [ $? == 0 ]
then
	kill -9 $Proc
fi
