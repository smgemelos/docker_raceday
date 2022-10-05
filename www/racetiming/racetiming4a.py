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
    for i in s:
        if i != "":
            total = total + i
    return total

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
    millisec = str(ts.microsecond).zfill(6)[0:3]
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

        query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
        racedb_cur.execute(query)

        if (racedb_cur.rowcount > 0):

            time.sleep(0.6)

            query = "SELECT * FROM stamps WHERE id_event=1 AND stamp_readout_datetime>'%s' order by stamp_readout_datetime" % laststamp
            racedb_cur.execute(query)

            stamps = racedb_cur.fetchall()

            UpdateList = {}


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
                Look for sicard_id in the rider db table - if it's not in the rider table, it's not registered
                '''
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

                    print "Rider ",ridername
                    '''
                    Look for riderid in the raceresults table
                    - We are keying on RiderID in case there is a need to change the 
                    SAIC assigned to a rider during the race, we can update the Entry 
                    in the riders table and continue to update the race results.
                    '''
                    query = "SELECT * FROM `raceresults` WHERE `riderid`='%s'" % riderid
                    racedb_cur.execute(query)
                    count = racedb_cur.rowcount

                    if (count == 0):

                        print "No existing raceresults found, adding new entry to raceresults..."

                        '''
                        If the riderid does not currently exist in the RaceResult db - but 
                        the rider is in the Rider table - add a new row to the RaceResults table
                        '''
                        zero = 0.0

                        query = "INSERT INTO `raceresults` (`plate`,`sicard_id`,`name`,`riderid`,`raceid`,`category`,`ranktotal`,`t1`,`t11`,`t2`,`t22`,`t3`,`t33`,`t4`,`t44`,`t5`,`t55`,`t6`,`t66`,`t7`,`t77`,`t8`,`t88`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)" 
                        data = (plate,sicard_id,ridername,riderid,raceid,category,rankstart,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero,zero)
                        racedb_cur.execute(query,data)

                        Standing = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category, "plate":plate, "total":0, "rank":rankstart,
                                      "1":0.0, "11":0.0, "s1":"", 
                                      "2":0.0, "22":0.0, "s2":"",
                                      "3":0.0, "33":0.0, "s3":"",
                                      "4":0.0, "44":0.0, "s4":"",
                                      "5":0.0, "55":0.0, "s5":"",
                                      "6":0.0, "66":0.0, "s6":"",
                                      "7":0.0, "77":0.0, "s7":"",
                                      "8":0.0, "88":0.0, "s8":""
                                    }
                    else:
                        results = racedb_cur.fetchone()

                        if (sicard_id != results["sicard_id"]):
                            print "WARNING: SIcardID Mismatch for RiderID %s" % riderid
                            ERROR = 1


                        elif (category != results["category"]):
                            print "ERROR: Category Mismatch for SIcardID %s" % sicard_id
                            ERROR = 1

                        else:
                            Standing = {"sicard_id":sicard_id, "riderid":riderid, "ridername":ridername, "category":category, "plate":plate, "total":results['total'], "rank":results['ranktotal'],
                                      "1":float(results['t1']), "11":float(results['t11']), "s1":results['s1'], 
                                      "2":float(results['t2']), "22":float(results['t22']), "s2":results['s2'],
                                      "3":float(results['t3']), "33":float(results['t33']), "s3":results['s3'],
                                      "4":float(results['t4']), "44":float(results['t44']), "s4":results['s4'],
                                      "5":float(results['t5']), "55":float(results['t55']), "s5":results['s5'],
                                      "6":float(results['t6']), "66":float(results['t66']), "s6":results['s6'],
                                      "7":float(results['t7']), "77":float(results['t77']), "s7":results['s7'],
                                      "8":float(results['t8']), "88":float(results['t88']), "s8":results['s8']
                                    }


                    if not ERROR:

                        if control_code in Gates:

                            if ( control_mode in cModes ):

                                print "UPDATING: %s SIcardID %s" % (ridername,sicard_id)
                                print "Gate: %s  -- Time %s  msec %s" % (control_code,punch_time.strftime("%H:%M:%S"),punch_ms)
                                print ""

                                time1 = (punch_time - d.datetime(1970, 1,1)).total_seconds()
                                time2 = (time1*1000 + punch_ms)/1000
                                
                                cat = Standing['category']

                                Standing[str(control_code)] = time2
                                s1 = calcStageTime(Standing["1"],Standing["11"]) if (catStages[cat] >= 1) else "" 
                                s2 = calcStageTime(Standing["2"],Standing["22"]) if (catStages[cat] >= 2) else ""
                                s3 = calcStageTime(Standing["3"],Standing["33"]) if (catStages[cat] >= 3) else ""
                                s4 = calcStageTime(Standing["4"],Standing["44"]) if (catStages[cat] >= 4) else ""
                                s5 = calcStageTime(Standing["5"],Standing["55"]) if (catStages[cat] >= 5) else ""
                                s6 = calcStageTime(Standing["6"],Standing["66"]) if (catStages[cat] >= 6) else ""
                                s7 = calcStageTime(Standing["7"],Standing["77"]) if (catStages[cat] >= 7) else ""
                                s8 = calcStageTime(Standing["8"],Standing["88"]) if (catStages[cat] >= 8) else ""

                                total = calcTotalTime([s1,s2,s3,s4,s5,s6,s7,s8])
                                #print "Total: " + str(total)
                                rank  = round(calcRankTime([s1,s2,s3,s4,s5,s6,s7,s8]),3)
                                #print rank

                                Standing['s1'] = timeStr(s1) if (s1 != "") else "" 
                                Standing['s2'] = timeStr(s2) if (s2 != "") else "" 
                                Standing['s3'] = timeStr(s3) if (s3 != "") else "" 
                                Standing['s4'] = timeStr(s4) if (s4 != "") else "" 
                                Standing['s5'] = timeStr(s5) if (s5 != "") else "" 
                                Standing['s6'] = timeStr(s6) if (s6 != "") else "" 
                                Standing['s7'] = timeStr(s7) if (s7 != "") else "" 
                                Standing['s8'] = timeStr(s8) if (s8 != "") else "" 
                                Standing['total'] = timeStr(total)
                                Standing['rank']  = rank      


                                query = "UPDATE `raceresults` SET `sicard_id`=%s,`total`=%s,`ranktotal`=%s,`s1`=%s,`s2`=%s,`s3`=%s,`s4`=%s,`s5`=%s,`s6`=%s,`s7`=%s,`s8`=%s,`t1`=%s,`t11`=%s,`t2`=%s,`t22`=%s,`t3`=%s,`t33`=%s,`t4`=%s,`t44`=%s,`t5`=%s,`t55`=%s,`t6`=%s,`t66`=%s,`t7`=%s,`t77`=%s,`t8`=%s,`t88`=%s WHERE `riderid`=%s "
                                data = (sicard_id,Standing['total'],Standing['rank'],
                                        Standing['s1'],Standing['s2'],Standing['s3'],Standing['s4'],Standing['s5'],Standing['s6'],Standing['s7'],Standing['s8'],
                                        str(Standing['1']),str(Standing['11']),str(Standing['2']),str(Standing['22']),str(Standing['3']),str(Standing['33']),Standing['4'],Standing['44'],
                                        Standing['5'],Standing['55'],Standing['6'],Standing['66'],Standing['7'],Standing['77'],Standing['8'],Standing['88'],
                                        riderid)

                                racedb_cur.execute(query,data)  



        time.sleep(1)
        #print("Running Race Time Calculations")


   
if __name__ == "__main__":
    main();