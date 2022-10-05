''' 
This libary loads the reference data from the databse tables

'''

import os, math, sys, time
import pymysql


def loadTimes(cur):
	# Processing Teams File
	Dict = {}
	query = "SELECT * FROM stamps WHERE id_event=1 order by stamp_readout_datetime"
	cur.execute(query)

	for row in cur.fetchall():
        
		sicard_id = row['stamp_card_id']
		control_code = row['stamp_control_code']
		control_mode = row['stamp_control_mode']
		stamp_type = row['stamp_type']
		read_datetime = row['stamp_readout_datetime']
		punch_time = row['stamp_punch_datetime']
		punch_ms = row['stamp_punch_ms']
        
		Dict[racenum] = {"racename":racename, 'raceid':raceid, "date":racedate}

	return Dict



def loadRiders(cur):
	# Processing Riders File
	Dict = {}
	query = "SELECT * FROM riders WHERE raceid=1"
	cur.execute(query)

	for row in cur.fetchall():

		riderid = row['riderid']
		sicard_id = row['sicard_id']
		ridername = row['name']
		category = row['category']
		plate = row['plate']

		Dict[sicard_id] = {"riderid":riderid, "ridername":ridername, "category":category, "riderid":riderid, "sicard_id":sicard_id, "plate":plate}

	return Dict




