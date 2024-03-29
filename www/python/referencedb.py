''' 
This libary loads the reference data from the databse tables

'''

import os, math, sys, time
import pymysql
from datetime import datetime


def loadRaces(cur,seriesid):
	# Processing Teams File
	Dict = {}
	query = "SELECT c.racenumber as racenum, a.*  FROM races a, seriesraces c WHERE c.seriesid='%s' AND a.id=c.raceid ORDER BY c.racenumber" % (seriesid)
	cur.execute(query)
	lastrace = 0

	for row in cur.fetchall():
        
		racenum = row['racenum']
		raceid = row['id']
		racename = row['name']
		racedate = row['racedate']
		lastrace = row['racenum']

		#racedate = time.strptime(row['racedate'].strip(), "%Y-%m-%d")
        
		Dict[racenum] = {"racename":racename, 'raceid':raceid, "date":racedate}

	return Dict,lastrace


def loadGtRaces(cur):
	# Processing Teams File
	Dict = {}
	query = "SELECT a.* FROM races a, seriesraces c WHERE c.seriesid=7 AND a.id=c.raceid"
	cur.execute(query)

	for row in cur.fetchall():
        
		racenum = row['racenumber']
		raceid = row['id']
		racename = row['name']
		racedate = row['racedate']
		#racedate = time.strptime(row['racedate'].strip(), "%Y-%m-%d")
        
		Dict[racenum] = {"racename":racename, 'raceid':raceid, "date":racedate}

	return Dict


def loadTeams(cur):
	Dict = {}
	query = "SELECT * FROM `team`"
	cur.execute(query)

	for row in cur.fetchall():

		teamid = row['teamid']
		#print(row['teamname'])
		teamname = row['teamname']
        
		Dict[teamid] = {"teamname":teamname, "teamid":teamid, "total":0, "1":"", "2":"" , "3":"" , "4":"" , "5":"" , "6":"", "7":"", "8":""}

	return Dict


def loadTeamMembers(cur):
	# Processing Teams_Members File
	Dict = {}
	query = "SELECT * FROM teammember"
	cur.execute(query)

	for row in cur.fetchall():

		riderid = row['riderid']
		adddate = row['adddate']
		teamid = row['teamid']
		teamname = row['teamname']
		#print(row['ridername'])
		ridername = row['ridername']


		dropdate = ""

		Dict[riderid] = {"teamid":teamid, "teamname":teamname, "ridername":ridername, "adddate":adddate, "dropdate":dropdate}

	return Dict

def loadRiders(cur):
	# Processing Riders File
	Dict = {}
	query = "SELECT riderid,name,dob,category,gender FROM rider"
	cur.execute(query)

	for row in cur.fetchall():

		riderid = row['riderid']
		ridername = row['name']	
		#print(riderid)
		#print(row['name'])
		#print(row['name'].encode('ascii'))
		dob = row['dob']
		raceage = time.localtime()[0] - dob.year

		category = row['category']
		gender = row['gender']

		Dict[riderid] = {"ridername":ridername, "category":category, "gender":gender, "raceage":raceage}

	return Dict

def loadPoints(cur):
	# Processing Points Allocation File
	Dict = {}
	query = "SELECT * FROM points"
	cur.execute(query)

	for row in cur.fetchall():

		place = row['place']
		points = row['points']

		Dict[place] = int(points)

	return Dict

def loadCategories(cur):
# Processing Categories File
	Dict = {}
	query = "SELECT * FROM category"
	cur.execute(query)

	for row in cur.fetchall():

		category = row['name']
		sortorder = int(row['sortorder'])
		cat = row['cat']
		gender = row['gender']
		minage = int(row['minage'])
		maxage = int(row['maxage'])
		dropraces = int(row['dropraces'])

		Dict[category] = {"sort":sortorder, "category":cat, "gender":gender, "minage":minage, "maxage":maxage, "dropraces":dropraces}

	return Dict


