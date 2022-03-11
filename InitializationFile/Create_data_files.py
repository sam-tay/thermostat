#!/usr/bin/env python3

var_file = open( 'heat_data.csv' , 'w+' );
var_file.write( '%s,%s,%s,%s,%s,%s,%s,%s' % ( 'Elapsed_time (m)' , 'Temp. Change (F)' , 'Set Temp (F)' , 'Threshold (F)' , 'Indoor Hum. (%)' , 'Outside Temp (F)' , 'Outside Hum. (%)' , 'UV Index' ) );
var_file.close();
var_file = open( 'cool_data.csv' , 'w+' );
var_file.write( '%s,%s,%s,%s,%s,%s,%s,%s' % ( 'Elapsed_time (m)' , 'Temp. Change (F)' , 'Set Temp (F)' , 'Threshold (F)' , 'Indoor Hum. (%)' , 'Outside Temp (F)' , 'Outside Hum. (%)' , 'UV Index' ) );
var_file.close();