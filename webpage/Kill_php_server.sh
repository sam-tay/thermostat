#!/usr/bin/env bash

Proc=$(pgrep php)
if [ $? == 0 ]
then
	kill -9 $Proc
fi
