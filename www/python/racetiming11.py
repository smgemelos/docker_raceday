# RaceTiming v9
# October 22, 2021
#
# Added functionality to Zero Out Stages per Category - pulls mapping from DB
# Added functionality to assign beacon ID for each timing point - pulls mapping from DB.  
# Changed default Beacon IDs from 1, 11, 2, 22, etc -> 1, 101, 2, 102, 3, 103, etc - updated DB results to match.


import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

Standing = {}
Rider = {}

Stages = ['s1','s2','s3','s4','s5','s6','s7','s8','s9','s10','s11','s12']

rankstart = 200000.0
cModes = [2,3,4,18,19,20]

def loadRiders(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM riders WHERE raceid=1 AND sicard_id IS NOT NULL"
    cur.execute(query)

    for row in cur.fetchall():

        riderid = row['riderid']
        sicard_id = row['sicard_id']
        ridername = row['name']
        category = row['category']
        plate = row['plate']

        Dict[sicard_id] = {"riderid":riderid, "ridername":ridername, "category":category, "riderid":riderid, "sicard_id":sicard_id, "plate":plate}

    return Dict


def loadSiacRiderid(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM siacriderid "
    cur.execute(query)

    for row in cur.fetchall():

        riderid = row['riderid']
        sicard_id = row['sicard_id']

        Dict[sicard_id] = {"riderid":riderid}
        
        Dict[sicard_id]={"riderid":riderid, "ridername":ridername, "raceid":raceid,
                        "category":category, "plate":plate, "ERROR":ERROR}

    return Dict
    

def loadTimeAdjust(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM beacons"
    cur.execute(query)

    for row in cur.fetchall():

        timepoint = row['timepoint']
        seconds = row['seconds']

        Dict[timepoint] = seconds

    return Dict

def loadBeacons(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM beacons"
    cur.execute(query)

    for row in cur.fetchall():

        timepoint = row['timepoint']
        beaconid = row['beaconid']

        Dict[beaconid] = timepoint

    return Dict


def loadCatStages(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM categories"
    cur.execute(query)

    for row in cur.fetchall():

        name = row['name']
        stages = row['stages']

        Dict[name] = {}

        Dict[name]['stages'] = stages

        for  i in Stages:
            Dict[name][i] = row[i]

    return Dict


def PointsTotal(a,N):
    total = 0
    a.sort(reverse=True)
    for i in range(N):
        total = total + a[i]
    return total


def calcStageTime(start,finish):

    t1 = 0.0 if (start == "") else start
    t2 = 0.0 if (finish == "") else finish

    #if ( (t1 == 0.0) or (t2 == 0.0) ):
    if ( (t1 == 0.0) or (t2 == 0.0) or (float(t2) < float(t1)) ):
        stagetime = ''
        return stagetime
    else:
        stagetime = float(t2) - float(t1)
        return stagetime
    

def calcTotalTime(s):
    total = 0
    stages = 0
    for i in s:
        if i != "":
            total = total + i
            stages = stages + 1
    return total,stages

def calcRankTime(s):
    total = 0
    for i in s:
        if i != "":
            total = total + i
        else:
            total = total + 14400  #default stage time is 4 hrs
    return total


def formattime(timestr):

    timearr = timestr.split(':')
    if len(timearr) == 3:
        timestr0 = "" if timearr[0] == '00' else timearr[0]+':'
        timestr1 = "" if timearr[1] == '00' else timearr[1]+':'
        timestr2 = timearr[2]
    else:
        timestr0 = ""
        timestr1 = ""
        timestr2 = ""

    return timestr


def timeStr(s):
    sec = float(s)

    root = d.datetime(1970,1,1)
    ts = root + d.timedelta(seconds=(sec))
    tmstr = ts.strftime("%H:%M:%S")
    usec = ts.microsecond
    if (usec < 10000):
        usecstr = "00" + str(usec)
    elif (usec < 100000):
        usecstr = "0" + str(usec)
    else:
        usecstr = str(usec)

    millisec = usecstr[0:3]

    tstr = "%s.%s" % (tmstr,millisec)
    return tstr



def main():

    # Connect to the database
    racedb = pymysql.connect(host='localhost',
                             user='root',
                             password='root',
                             db='lcsportident_events',
                             charset='latin1',
                             cursorclass=pymysql.cursors.DictCursor)
    racedb.autocommit(True)
    racedb_cur = racedb.cursor()

    #cesdb = pymysql.connect(host='ces.cyjhywszjezw.us-east-1.rds.amazonaws.com',
    #                     user='cesuser',
    #                     password='wvG-Tkd-huo-72S',
    #                     db='ces',
    #                     charset='latin1',
    #                     cursorclass=pymysql.cursors.DictCursor)
    #cesdb.autocommit(True)
    #cesdb_cur = cesdb.cursor()


    
    catStages = loadCatStages(racedb_cur)
    beacons = loadBeacons(racedb_cur)

    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        #print "Hit RETURN to continue."
        #raw_input("?")

        print "Waiting...."
        timeAdjust = loadTimeAdjust(racedb_cur)

        query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
        racedb_cur.execute(query)
        count = racedb_cur.rowcount
        
        if (count > 0):

            #time.sleep(0.5)

            #query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
            #racedb_cur.execute(query)

            stamps = racedb_cur.fetchall()

            Standings = {}
            SiacRiderid = {}

            '''
            Run through each of the new timestamps in the stamps TABLE
            '''
            for stamp in stamps:

                ERROR = 0
                
                sicard_id = stamp['stamp_card_id']
                control_code = stamp['stamp_control_code']
                control_mode = stamp['stamp_control_mode']
                stamp_type = stamp['stamp_type']
                read_datetime = stamp['stamp_readout_datetime']
                punch_time = stamp['stamp_punch_datetime']
                punch_time = punch_time.replace(year=1970, month=01, day=01)
                punch_ms = stamp['stamp_punch_ms']

                print "Read stamp - saicID: ",sicard_id,"    code: ",control_code,"    mode: ",control_mode

                laststamp = read_datetime.strftime("%Y-%m-%d %H:%M:%S")

                '''
                Look for sicard_id in the Standings dictionary - if this is a new sicard_id, 
                we look up the sicard_id in the Rader table, and pull the rider info.

                If we don't find the sicard_id in the Rider table, we alert - and mark an ERROR
                '''
                if sicard_id not in SiacRiderid.keys():

                    riderid = " "
                    ridername = "Unknown Rider"
                    raceid = " "
                    category = "Unknown Rider"
                    plate = " "

                    query = "SELECT a.raceid, a.name, a.riderid, a.category, a.plate FROM riders a, siacriderid b WHERE b.sicard_id=%s AND b.riderid=a.riderid" % sicard_id
                    racedb_cur.execute(query)
                    count = racedb_cur.rowcount

                    if (count == 0):
                        print "ERROR: Unknown SIcardID %s - Check the Rider Table" % sicard_id
                        ERROR = 1

                    elif (count > 1):
                        print "ERROR: Duplicate Entries for SIcardID %s in Rider Table" % sicard_id
                        ERROR = 1

                    else:
                        riders = racedb_cur.fetchone()
                        riderid = riders['riderid']
                        ridername = riders['name']
                        raceid = riders['raceid']
                        category = riders['category']
                        plate = riders['plate']

                    SiacRiderid[sicard_id]={"riderid":riderid, "ridername":ridername, "raceid":raceid, 
                                            "category":category, "plate":plate, "ERROR":ERROR}

                
                if SiacRiderid[sicard_id]["ERROR"] == 0:

                    riderid = SiacRiderid[sicard_id]["riderid"]
                    ridername = SiacRiderid[sicard_id]['ridername']
                    raceid = SiacRiderid[sicard_id]['raceid']
                    category = SiacRiderid[sicard_id]['category']
                    plate = SiacRiderid[sicard_id]['plate']

                    if riderid not in Standing.keys():

                        '''
                        Look for sicard_id in the raceresults table
                        '''
                        query = "SELECT * FROM `raceresults` WHERE `riderid`='%s'" % riderid
                        racedb_cur.execute(query)
                        count = racedb_cur.rowcount

                        if (count == 0):

                            print "No existing raceresults found, adding new entry to raceresults..."

                            zero = 0.0

                            query = ("INSERT INTO `raceresults` "
                                     "(`plate`,`sicard_id`,`name`,`riderid`,`raceid`,`category`,`ranktotal`,`ttotal`,`tpenalty`,"
                                     " `t1`,`t101`,`t2`,`t102`,`t3`,`t103`,`t4`,`t104`,`t5`,`t105`,`t6`,`t106`,`t7`,`t107`,`t8`,`t108`,`t9`,`t109`,`t10`,`t110`,`t11`,`t111`,`t12`,`t112`,`dnf`,`dq`) "
                                     "VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" )
                            data = (plate,sicard_id,ridername,riderid,raceid,category,rankstart,zero,0,
                                    zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,'N','N')

                            racedb_cur.execute(query,data)


                            '''
                            Create a new entry in the Standings table,  - and mark an ERROR
                            '''
                            Standing[riderid] = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category,                             "plate":plate,
                                                    "1":0.0, "101":0.0, "s1":"", 
                                                    "2":0.0, "102":0.0, "s2":"",
                                                    "3":0.0, "103":0.0, "s3":"",
                                                    "4":0.0, "104":0.0, "s4":"",
                                                    "5":0.0, "105":0.0, "s5":"",
                                                    "6":0.0, "106":0.0, "s6":"",
                                                    "7":0.0, "107":0.0, "s7":"",
                                                    "8":0.0, "108":0.0, "s8":"",
                                                    "9":0.0, "109":0.0, "s9":"",
                                                    "10":0.0, "110":0.0, "s10":"",
                                                    "11":0.0, "111":0.0, "s11":"",
                                                    "12":0.0, "112":0.0, "s12":"",
                                                    "total":"", "ttotal":0.0, "tpenalty":0,  "stages":0, "rank":rankstart,
                                                    "ERROR":ERROR
                                                    }

                            
                        else:
                            results = racedb_cur.fetchone()

                            print results

                            
                            Standing[riderid] = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category,                             "plate":plate,
                                                    "1":float(results['t1']), "101":float(results['t101']), "s1":results['s1'], 
                                                    "2":float(results['t2']), "102":float(results['t102']), "s2":results['s2'],
                                                    "3":float(results['t3']), "103":float(results['t103']), "s3":results['s3'],
                                                    "4":float(results['t4']), "104":float(results['t104']), "s4":results['s4'],
                                                    "5":float(results['t5']), "105":float(results['t105']), "s5":results['s5'],
                                                    "6":float(results['t6']), "106":float(results['t106']), "s6":results['s6'],
                                                    "7":float(results['t7']), "107":float(results['t107']), "s7":results['s7'],
                                                    "8":float(results['t8']), "108":float(results['t108']), "s8":results['s8'],
                                                    "9":float(results['t9']), "109":float(results['t109']), "s9":results['s9'],
                                                    "10":float(results['t10']), "110":float(results['t110']), "s10":results['s10'],
                                                    "11":float(results['t11']), "111":float(results['t111']), "s11":results['s11'],
                                                    "12":float(results['t12']), "112":float(results['t112']), "s12":results['s12'],
                                                    "total":results['total'], "ttotal":results['ttotal'], "tpenalty":results['tpenalty'], 
                                                    "rank":results['ranktotal'],"stages":results['stages'],
                                                    }


                    if control_code in beacons.keys():

                        if ( control_mode in cModes ):

                            print "Timestamp: %s SIcardID %s" % (Standing[riderid]["ridername"],sicard_id)
                            print "Gate: %s  -- Time %s  msec %s" % (control_code,punch_time.strftime("%H:%M:%S"),punch_ms)
                            print "Time Adjust = %s seconds" % timeAdjust[beacons[control_code]]
                            print ""

                            time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()

                            time2 = (time1*1000 + punch_ms)/1000 + timeAdjust[beacons[control_code]]
                            
                            cat = Standing[riderid]['category']

                            Standing[riderid][str(beacons[control_code])] = time2 if (Standing[riderid][str(beacons[control_code])] == 0.0) else Standing[riderid][str(beacons[control_code])]

                            s1  = 0.0 if (catStages[cat]['s1']==0) else (calcStageTime(Standing[riderid]["1"],Standing[riderid]["101"]) if (catStages[cat]['stages'] >= 1) else "")
                            s2  = 0.0 if (catStages[cat]['s2']==0) else (calcStageTime(Standing[riderid]["2"],Standing[riderid]["102"]) if (catStages[cat]['stages'] >= 2) else "")
                            s3  = 0.0 if (catStages[cat]['s3']==0) else (calcStageTime(Standing[riderid]["3"],Standing[riderid]["103"]) if (catStages[cat]['stages'] >= 3) else "")
                            s4  = 0.0 if (catStages[cat]['s4']==0) else (calcStageTime(Standing[riderid]["4"],Standing[riderid]["104"]) if (catStages[cat]['stages'] >= 4) else "")
                            s5  = 0.0 if (catStages[cat]['s5']==0) else (calcStageTime(Standing[riderid]["5"],Standing[riderid]["105"]) if (catStages[cat]['stages'] >= 5) else "")
                            s6  = 0.0 if (catStages[cat]['s6']==0) else (calcStageTime(Standing[riderid]["6"],Standing[riderid]["106"]) if (catStages[cat]['stages'] >= 6) else "")
                            s7  = 0.0 if (catStages[cat]['s7']==0) else (calcStageTime(Standing[riderid]["7"],Standing[riderid]["107"]) if (catStages[cat]['stages'] >= 7) else "")
                            s8  = 0.0 if (catStages[cat]['s8']==0) else (calcStageTime(Standing[riderid]["8"],Standing[riderid]["108"]) if (catStages[cat]['stages'] >= 8) else "")
                            s9  = 0.0 if (catStages[cat]['s9']==0) else (calcStageTime(Standing[riderid]["9"],Standing[riderid]["109"]) if (catStages[cat]['stages'] >= 9) else "")
                            s10 = 0.0 if (catStages[cat]['s10']==0) else (calcStageTime(Standing[riderid]["10"],Standing[riderid]["110"]) if (catStages[cat]['stages'] >= 10) else "")
                            s11 = 0.0 if (catStages[cat]['s11']==0) else (calcStageTime(Standing[riderid]["11"],Standing[riderid]["111"]) if (catStages[cat]['stages'] >= 11) else "")
                            s12 = 0.0 if (catStages[cat]['s12']==0) else (calcStageTime(Standing[riderid]["12"],Standing[riderid]["112"]) if (catStages[cat]['stages'] >= 12) else "")

                            total,stages = calcTotalTime([s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12])
                            total = total + Standing[riderid]["tpenalty"]
                            #stages = stages if (catStages[cat] >= stages) else catStages[cat]
                            #print "Total: " + str(total)
                            rank  = round(calcRankTime([s1,s2,s3,s4,s5,s6,s7,s8,s9,s10,s11,s12]),3) + Standing[riderid]["tpenalty"]
                            #print rank

                            Standing[riderid]['s1']  = timeStr(s1) if (s1 != "") else "" 
                            Standing[riderid]['s2']  = timeStr(s2) if (s2 != "") else "" 
                            Standing[riderid]['s3']  = timeStr(s3) if (s3 != "") else "" 
                            Standing[riderid]['s4']  = timeStr(s4) if (s4 != "") else "" 
                            Standing[riderid]['s5']  = timeStr(s5) if (s5 != "") else "" 
                            Standing[riderid]['s6']  = timeStr(s6) if (s6 != "") else "" 
                            Standing[riderid]['s7']  = timeStr(s7) if (s7 != "") else "" 
                            Standing[riderid]['s8']  = timeStr(s8) if (s8 != "") else ""
                            Standing[riderid]['s9']  = timeStr(s9) if (s9 != "") else "" 
                            Standing[riderid]['s10'] = timeStr(s10) if (s10 != "") else "" 
                            Standing[riderid]['s11'] = timeStr(s11) if (s11 != "") else "" 
                            Standing[riderid]['s12'] = timeStr(s12) if (s12 != "") else "" 
                            Standing[riderid]['total'] = timeStr(total)
                            Standing[riderid]['ttotal'] = total
                            Standing[riderid]['rank']  = rank  
                            Standing[riderid]['stages']  = stages 

                            if (stages == catStages[cat]['stages']):
                                query = "UPDATE siacriderid SET saic_returned='Yes' WHERE sicard_id='%s'" %  sicard_id
                                racedb_cur.execute(query)


            for rider in Standing.keys():

                #print "SIAC: %s  ERROR: %s " % (rider,Standing[rider]["ERROR"])

                print rider



                print "Updating results for Rider ",Standing[rider]["ridername"]
                
                query = ("UPDATE `raceresults` "
                         "SET `total`=%s,`ranktotal`=%s,`ttotal`=%s,`stages`=%s,`category`=%s,`sicard_id`=%s,"
                         "`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`s9`=%s,`s10`=%s,`s11`=%s,`s12`=%s,"
                         "`t1`=%s,`t101`=%s,`t2`=%s,`t102`=%s,`t3`=%s,`t103`=%s,`t4`=%s,`t104`=%s,`t5`=%s,`t105`=%s,`t6`=%s,`t106`=%s,"
                         "`t7`=%s,`t107`=%s,`t8`=%s,`t108`=%s,`t9`=%s,`t109`=%s,`t10`=%s,`t110`=%s,`t11`=%s,`t111`=%s,`t12`=%s,`t112`=%s "
                         "WHERE `riderid`=%s ")
                data = (Standing[rider]['total'],Standing[rider]['rank'],Standing[rider]['ttotal'],Standing[rider]['stages'],Standing[rider]['category'],Standing[rider]['sicard_id'],
                        formattime(Standing[rider]['s1']),formattime(Standing[rider]['s2']),formattime(Standing[rider]['s3']),formattime(Standing[rider]['s4']),
                        formattime(Standing[rider]['s5']),formattime(Standing[rider]['s6']),formattime(Standing[rider]['s7']),formattime(Standing[rider]['s8']),
                        formattime(Standing[rider]['s9']),formattime(Standing[rider]['s10']),formattime(Standing[rider]['s11']),formattime(Standing[rider]['s12']),
                        Standing[rider]['1'],Standing[rider]['101'],Standing[rider]['2'],Standing[rider]['102'],Standing[rider]['3'],Standing[rider]['103'],
                        Standing[rider]['4'],Standing[rider]['104'],Standing[rider]['5'],Standing[rider]['105'],Standing[rider]['6'],Standing[rider]['106'],
                        Standing[rider]['7'],Standing[rider]['107'],Standing[rider]['8'],Standing[rider]['108'],Standing[rider]['9'],Standing[rider]['109'],
                        Standing[rider]['10'],Standing[rider]['110'],Standing[rider]['11'],Standing[rider]['111'],Standing[rider]['12'],Standing[rider]['112'],
                        rider)

                #print query % data

                racedb_cur.execute(query,data)  

                SiacRiderid.pop(Standing[rider]['sicard_id'])
                Standing.pop(rider)


        time.sleep(1)
        #print("Running Race Time Calculations")

   
if __name__ == "__main__":
    main();
