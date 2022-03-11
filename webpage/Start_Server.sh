#!/usr/bin/env bash

Proc=$(pgrep php)
if [ $? != 0 ]
then
	nohup php -S 0.0.0.0:4000 &
fi
