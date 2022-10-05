import os, math, sys, time
import datetime as d
#from dateutil import parser
import pymysql

Standing = {}
Rider = {}

Stages = ['s1','s2','s3','s4','s5','s6','s7','s8']

rankstart = 115200.0
Gates = [1,11,2,22,3,33,4,44,5,55,6,66,7,77,8,88]
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

            time.sleep(0.5)

            query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
            racedb_cur.execute(query)

            stamps = racedb_cur.fetchall()

            Standings = {}

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
                if sicard_id not in Standing.keys():

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
                        Standing[sicard_id] = {"sicard_id":sicard_id, "ERROR":ERROR }

                    elif (count > 1):
                        print "ERROR: Duplicate Entries for SIcardID %s in Rider Table" % sicard_id
                        ERROR = 1
                        Standing[sicard_id] = {"sicard_id":sicard_id, "ERROR":ERROR }

                    else:
                        riders = racedb_cur.fetchone()
                        riderid = riders['riderid']
                        ridername = riders['name']
                        raceid = riders['raceid']
                        category = riders['category']
                        plate = riders['plate']


                        '''
                        Look for sicard_id in the raceresults table
                        '''
                        query = "SELECT * FROM `raceresults` WHERE `sicard_id`=%s" % sicard_id
                        racedb_cur.execute(query)
                        count = racedb_cur.rowcount

                        if (count == 0):

                            print "No existing raceresults found, adding new entry to raceresults..."

                            zero = 0.0

                            query = "INSERT INTO `raceresults` (`plate`,`sicard_id`,`name`,`riderid`,`raceid`,`category`,`ranktotal`,`ttotal`,`tpenalty`,`t1`,`t11`,`t2`,`t22`,`t3`,`t33`,`t4`,`t44`,`t5`,`t55`,`t6`,`t66`,`t7`,`t77`,`t8`,`t88`,`dnf`,`dq`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
                            data = (plate,sicard_id,ridername,riderid,raceid,category,rankstart,zero,0,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,'N','N')
                            racedb_cur.execute(query,data)


                            '''
                            Create a new entry in the Standings table,  - and mark an ERROR
                            '''
                            Standing[sicard_id] = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category, "plate":plate, 
                                                    "1":0.0, "11":0.0, "s1":"", 
                                                    "2":0.0, "22":0.0, "s2":"",
                                                    "3":0.0, "33":0.0, "s3":"",
                                                    "4":0.0, "44":0.0, "s4":"",
                                                    "5":0.0, "55":0.0, "s5":"",
                                                    "6":0.0, "66":0.0, "s6":"",
                                                    "7":0.0, "77":0.0, "s7":"",
                                                    "8":0.0, "88":0.0, "s8":"",
                                                    "total":"", "ttotal":0.0, "tpenalty":0,  "stages":0, "rank":rankstart,
                                                    "ERROR":ERROR
                                                    }

                            
                        else:
                            results = racedb_cur.fetchone()

                            if (riderid != results["riderid"]):
                                print "ERROR: RiderID Mismatch for SIcardID %s" % sicard_id
                                ERROR = 1

                            elif (category != results["category"]):
                                print "ERROR: Category Mismatch for SIcardID %s" % sicard_id
                                ERROR = 1

                            else:
                                Standing[sicard_id] = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category, "plate":plate, 
                                                        "1":float(results['t1']), "11":float(results['t11']), "s1":results['s1'], 
                                                        "2":float(results['t2']), "22":float(results['t22']), "s2":results['s2'],
                                                        "3":float(results['t3']), "33":float(results['t33']), "s3":results['s3'],
                                                        "4":float(results['t4']), "44":float(results['t44']), "s4":results['s4'],
                                                        "5":float(results['t5']), "55":float(results['t55']), "s5":results['s5'],
                                                        "6":float(results['t6']), "66":float(results['t66']), "s6":results['s6'],
                                                        "7":float(results['t7']), "77":float(results['t77']), "s7":results['s7'],
                                                        "8":float(results['t8']), "88":float(results['t88']), "s8":results['s8'],
                                                        "total":results['total'], "ttotal":results['ttotal'], "tpenalty":results['tpenalty'], 
                                                        "rank":results['ranktotal'],"stages":results['stages'],
                                                        "ERROR":ERROR
                                                        }

                    Standing[sicard_id]["ERROR"] = ERROR


                
                
                if not Standing[sicard_id]["ERROR"]:

                    if control_code in Gates:

                        if ( control_mode in cModes ):

                            print "Timestamp: %s SIcardID %s" % (Standing[sicard_id]["ridername"],sicard_id)
                            print "Gate: %s  -- Time %s  msec %s" % (control_code,punch_time.strftime("%H:%M:%S"),punch_ms)
                            print ""

                            time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()
                            time2 = (time1*1000 + punch_ms)/1000
                            
                            cat = Standing[sicard_id]['category']

                            Standing[sicard_id][str(control_code)] = time2
                            s1 = calcStageTime(Standing[sicard_id]["1"],Standing[sicard_id]["11"]) if (catStages[cat] >= 1) else "" 
                            s2 = calcStageTime(Standing[sicard_id]["2"],Standing[sicard_id]["22"]) if (catStages[cat] >= 2) else ""
                            s3 = calcStageTime(Standing[sicard_id]["3"],Standing[sicard_id]["33"]) if (catStages[cat] >= 3) else ""
                            s4 = calcStageTime(Standing[sicard_id]["4"],Standing[sicard_id]["44"]) if (catStages[cat] >= 4) else ""
                            s5 = calcStageTime(Standing[sicard_id]["5"],Standing[sicard_id]["55"]) if (catStages[cat] >= 5) else ""
                            s6 = calcStageTime(Standing[sicard_id]["6"],Standing[sicard_id]["66"]) if (catStages[cat] >= 6) else ""
                            s7 = calcStageTime(Standing[sicard_id]["7"],Standing[sicard_id]["77"]) if (catStages[cat] >= 7) else ""
                            s8 = calcStageTime(Standing[sicard_id]["8"],Standing[sicard_id]["88"]) if (catStages[cat] >= 8) else ""

                            total,stages = calcTotalTime([s1,s2,s3,s4,s5,s6,s7,s8])
                            total = total + Standing[sicard_id]["tpenalty"]
                            #stages = stages if (catStages[cat] >= stages) else catStages[cat]
                            #print "Total: " + str(total)
                            rank  = round(calcRankTime([s1,s2,s3,s4,s5,s6,s7,s8]),3) + Standing[sicard_id]["tpenalty"]
                            #print rank

                            Standing[sicard_id]['s1'] = timeStr(s1) if (s1 != "") else "" 
                            Standing[sicard_id]['s2'] = timeStr(s2) if (s2 != "") else "" 
                            Standing[sicard_id]['s3'] = timeStr(s3) if (s3 != "") else "" 
                            Standing[sicard_id]['s4'] = timeStr(s4) if (s4 != "") else "" 
                            Standing[sicard_id]['s5'] = timeStr(s5) if (s5 != "") else "" 
                            Standing[sicard_id]['s6'] = timeStr(s6) if (s6 != "") else "" 
                            Standing[sicard_id]['s7'] = timeStr(s7) if (s7 != "") else "" 
                            Standing[sicard_id]['s8'] = timeStr(s8) if (s8 != "") else "" 
                            Standing[sicard_id]['total'] = timeStr(total)
                            Standing[sicard_id]['ttotal'] = total
                            Standing[sicard_id]['rank']  = rank  
                            Standing[sicard_id]['stages']  = stages 

            

            for siac in Standing.keys():

                #print "SIAC: %s  ERROR: %s " % (siac,Standing[siac]["ERROR"])

                if not Standing[siac]["ERROR"]:

                    print "Updating results for Rider ",Standing[siac]["ridername"]
                    
                    query = "UPDATE `raceresults` SET `total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`t1`=%s,`t11`=%s,`t2`=%s,`t22`=%s,`t3`=%s,`t33`=%s,`t4`=%s,`t44`=%s,`t5`=%s,`t55`=%s,`t6`=%s,`t66`=%s,`t7`=%s,`t77`=%s,`t8`=%s,`t88`=%s,`ttotal`=%s,`stages`=%s WHERE `sicard_id`=%s "
                    data = (Standing[siac]['total'],Standing[siac]['rank'],
                            Standing[siac]['s1'],Standing[siac]['s2'],Standing[siac]['s3'],Standing[siac]['s4'],Standing[siac]['s5'],Standing[siac]['s6'],Standing[siac]['s7'],Standing[siac]['s8'],
                            str(Standing[siac]['1']),str(Standing[siac]['11']),str(Standing[siac]['2']),str(Standing[siac]['22']),str(Standing[siac]['3']),str(Standing[siac]['33']),Standing[siac]['4'],Standing[siac]['44'],
                            Standing[siac]['5'],Standing[siac]['55'],Standing[siac]['6'],Standing[siac]['66'],Standing[siac]['7'],Standing[siac]['77'],Standing[siac]['8'],Standing[siac]['88'],Standing[siac]['ttotal'],Standing[siac]['stages'],
                            siac)

                    racedb_cur.execute(query,data)  

                Standing.pop(siac)

        time.sleep(1)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();