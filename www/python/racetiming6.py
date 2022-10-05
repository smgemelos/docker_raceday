import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

Standing = {}
Rider = {}

Stages = ['s1','s2','s3','s4','s5','s6','s7','s8','s9','s10']

rankstart = 115200.0
Gates = [1,11,2,22,3,33,4,44,5,55,6,66,7,77,8,88,9,99,10,100]
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


def loadCatStages(cur):
    # Processing Riders File
    Dict = {}
    query = "SELECT * FROM categories"
    cur.execute(query)

    for row in cur.fetchall():

        name = row['name']
        stages = row['stages']

        Dict[name] = stages

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

    if ( (t1 == 0.0) or (t2 == 0.0) ):
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

    # Clear the results DB
    #query = "DELETE FROM `raceresults`"
    #racedb_cur.execute(query)
    #query = "ALTER TABLE `raceresults` AUTO_INCREMENT = 1"
    #racedb_cur.execute(query)

    # Clear the stamps DB
    #query = "DELETE FROM `stamps`"
    #racedb_cur.execute(query)
    #query = "ALTER TABLE `stamps` AUTO_INCREMENT = 1"
    #racedb_cur.execute(query)
    
    catStages = loadCatStages(racedb_cur)

    laststamp = d.datetime(1970, 1,1).strftime("%Y-%m-%d %H:%M:%S")

    while True:

        #print "Hit RETURN to continue."
        #raw_input("?")

        print "Waiting...."

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

                    query = "SELECT * FROM `riders` WHERE `sicard_id`=%s" % sicard_id
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
                                     " `t1`,`t11`,`t2`,`t22`,`t3`,`t33`,`t4`,`t44`,`t5`,`t55`,`t6`,`t66`,`t7`,`t77`,`t8`,`t88`,`t9`,`t99`,`t10`,`t100`,`dnf`,`dq`) "
                                     "VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" )
                            data = (plate,sicard_id,ridername,riderid,raceid,category,rankstart,zero,0,
                                    zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,'N','N')

                            racedb_cur.execute(query,data)


                            '''
                            Create a new entry in the Standings table,  - and mark an ERROR
                            '''
                            Standing[riderid] = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category, "plate":plate, 
                                                    "1":0.0, "11":0.0, "s1":"", 
                                                    "2":0.0, "22":0.0, "s2":"",
                                                    "3":0.0, "33":0.0, "s3":"",
                                                    "4":0.0, "44":0.0, "s4":"",
                                                    "5":0.0, "55":0.0, "s5":"",
                                                    "6":0.0, "66":0.0, "s6":"",
                                                    "7":0.0, "77":0.0, "s7":"",
                                                    "8":0.0, "88":0.0, "s8":"",
                                                    "9":0.0, "99":0.0, "s9":"",
                                                    "10":0.0, "100":0.0, "s10":"",
                                                    "total":"", "ttotal":0.0, "tpenalty":0,  "stages":0, "rank":rankstart,
                                                    "ERROR":ERROR
                                                    }

                            
                        else:
                            results = racedb_cur.fetchone()

                            print results

                            
                            Standing[riderid] = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category, "plate":plate, 
                                                    "1":float(results['t1']), "11":float(results['t11']), "s1":results['s1'], 
                                                    "2":float(results['t2']), "22":float(results['t22']), "s2":results['s2'],
                                                    "3":float(results['t3']), "33":float(results['t33']), "s3":results['s3'],
                                                    "4":float(results['t4']), "44":float(results['t44']), "s4":results['s4'],
                                                    "5":float(results['t5']), "55":float(results['t55']), "s5":results['s5'],
                                                    "6":float(results['t6']), "66":float(results['t66']), "s6":results['s6'],
                                                    "7":float(results['t7']), "77":float(results['t77']), "s7":results['s7'],
                                                    "8":float(results['t8']), "88":float(results['t88']), "s8":results['s8'],
                                                    "9":float(results['t9']), "99":float(results['t99']), "s9":results['s9'],
                                                    "10":float(results['t10']), "100":float(results['t100']), "s10":results['s10'],
                                                    "total":results['total'], "ttotal":results['ttotal'], "tpenalty":results['tpenalty'], 
                                                    "rank":results['ranktotal'],"stages":results['stages'],
                                                    }


                    if control_code in Gates:

                        if ( control_mode in cModes ):

                            print "Timestamp: %s SIcardID %s" % (Standing[riderid]["ridername"],sicard_id)
                            print "Gate: %s  -- Time %s  msec %s" % (control_code,punch_time.strftime("%H:%M:%S"),punch_ms)
                            print ""

                            time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()
                            time2 = (time1*1000 + punch_ms)/1000
                            
                            cat = Standing[riderid]['category']

                            Standing[riderid][str(control_code)] = time2
                            s1 = calcStageTime(Standing[riderid]["1"],Standing[riderid]["11"]) if (catStages[cat] >= 1) else "" 
                            s2 = calcStageTime(Standing[riderid]["2"],Standing[riderid]["22"]) if (catStages[cat] >= 2) else ""
                            s3 = calcStageTime(Standing[riderid]["3"],Standing[riderid]["33"]) if (catStages[cat] >= 3) else ""
                            s4 = calcStageTime(Standing[riderid]["4"],Standing[riderid]["44"]) if (catStages[cat] >= 4) else ""
                            s5 = calcStageTime(Standing[riderid]["5"],Standing[riderid]["55"]) if (catStages[cat] >= 5) else ""
                            s6 = calcStageTime(Standing[riderid]["6"],Standing[riderid]["66"]) if (catStages[cat] >= 6) else ""
                            s7 = calcStageTime(Standing[riderid]["7"],Standing[riderid]["77"]) if (catStages[cat] >= 7) else ""
                            s8 = calcStageTime(Standing[riderid]["8"],Standing[riderid]["88"]) if (catStages[cat] >= 8) else ""
                            s9 = calcStageTime(Standing[riderid]["9"],Standing[riderid]["99"]) if (catStages[cat] >= 9) else ""
                            s10 = calcStageTime(Standing[riderid]["10"],Standing[riderid]["100"]) if (catStages[cat] >= 10) else ""


                            total,stages = calcTotalTime([s1,s2,s3,s4,s5,s6,s7,s8,s9,s10])
                            total = total + Standing[riderid]["tpenalty"]
                            #stages = stages if (catStages[cat] >= stages) else catStages[cat]
                            #print "Total: " + str(total)
                            rank  = round(calcRankTime([s1,s2,s3,s4,s5,s6,s7,s8,s9,s10]),3) + Standing[riderid]["tpenalty"]
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
                            Standing[riderid]['total'] = timeStr(total)
                            Standing[riderid]['ttotal'] = total
                            Standing[riderid]['rank']  = rank  
                            Standing[riderid]['stages']  = stages 

            


            for rider in Standing.keys():

                #print "SIAC: %s  ERROR: %s " % (siac,Standing[siac]["ERROR"])



                print "Updating results for Rider ",Standing[rider]["ridername"]
                
                query = ("UPDATE `raceresults` "
                         "SET `total`=%s,`ranktotal`=%s,`ttotal`=%s,`stages`=%s,`category`=%s,`sicard_id`=%s,"
                         "`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`s9`=%s,`s10`=%s,"
                         "`t1`=%s,`t11`=%s,`t2`=%s,`t22`=%s,`t3`=%s,`t33`=%s,`t4`=%s,`t44`=%s,`t5`=%s,`t55`=%s,"
                         "`t6`=%s,`t66`=%s,`t7`=%s,`t77`=%s,`t8`=%s,`t88`=%s,`t9`=%s,`t99`=%s,`t10`=%s,`t100`=%s "
                         "WHERE `riderid`=%s ")
                data = (Standing[rider]['total'],Standing[rider]['rank'],Standing[rider]['ttotal'],Standing[rider]['stages'],Standing[rider]['category'],Standing[rider]['sicard_id'],
                        Standing[rider]['s1'],Standing[rider]['s2'],Standing[rider]['s3'],Standing[rider]['s4'],Standing[rider]['s5'],Standing[rider]['s6'],Standing[rider]['s7'],Standing[rider]['s8'],Standing[rider]['s9'],Standing[rider]['s10'],
                        Standing[rider]['1'],Standing[rider]['11'],Standing[rider]['2'],Standing[rider]['22'],Standing[rider]['3'],Standing[rider]['33'],Standing[rider]['4'],Standing[rider]['44'],Standing[rider]['5'],Standing[rider]['55'],
                        Standing[rider]['6'],Standing[rider]['66'],Standing[rider]['7'],Standing[rider]['77'],Standing[rider]['8'],Standing[rider]['88'],Standing[rider]['9'],Standing[rider]['99'],Standing[rider]['10'],Standing[rider]['100'],
                        rider)

                #print query % data

                racedb_cur.execute(query,data)  

                SiacRiderid.pop(Standing[rider]['sicard_id'])
                Standing.pop(rider)


        time.sleep(1)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();